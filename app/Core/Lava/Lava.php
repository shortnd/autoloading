<?php

namespace App\Core\Lava;

define('PEAR_LOG_EMERG',    LavaLogLevel::EMERGENCY);     /* System is unusable */
define('PEAR_LOG_ALERT',    LavaLogLevel::ALERT);     /* Immediate action required */
define('PEAR_LOG_CRIT',     LavaLogLevel::CRITICAL);     /* Critical conditions */
define('PEAR_LOG_ERR',      LavaLogLevel::ERROR);     /* Error conditions */
define('PEAR_LOG_WARNING',  LavaLogLevel::WARNING);     /* Warning conditions */
define('PEAR_LOG_NOTICE',   LavaLogLevel::NOTICE);     /* Normal but significant */
define('PEAR_LOG_INFO',     LavaLogLevel::INFO);     /* Informational */
define('PEAR_LOG_DEBUG',    LavaLogLevel::DEBUG);     /* Debug-level messages */

abstract class Lava
{
	public $routes = [];
	public $config = [];
	public $pathInfo;
	public $extension;
	public  $action;
	public $debug = [];
	public $attributes = [];
	private $app;

	public function __construct(Lava $app)
	{
		$this->app = $app;
	}

	public function app()
	{
		return $this->app;
	}

	public function configure($config)
	{
		include_once($config);
	}

	public function run()
	{
		$this->log("___________".date("m-d-Y H:i:s"), Logger::DEBUG);
		$this->log("REMOTE_ADDR:     ".$_SERVER['REMOTE_ADDR'], PEAR_LOG_DEBUG);
		$this->log("HTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'], PEAR_LOG_DEBUG);
		$this->log("REQUEST_METHOD:  ".$_SERVER['REQUEST_METHOD'], PEAR_LOG_DEBUG);
		$this->log("QUERY_STRING:    ".$_SERVER['QUERY_STRING'], PEAR_LOG_DEBUG);
		$this->log("REQUEST_URI:     ".$_SERVER['REQUEST_URI'], PEAR_LOG_DEBUG);
		$this->log("PHP_SELF:        ".$_SERVER['PHP_SELF'], PEAR_LOG_DEBUG);

		if (!$this->config) {
			$this->log("No configuration file", PEAR_LOG_ERR);
			$this->lavaExit("No Configuration File");
		}

		$this->init();
		$this->pathInfo = $this->parsePath();
		$this->startSession();
		$this->manageFlash();
		$this->checkAuthentication();
		$this->runAction($this->pathInfo['actionPath'], null, true);
		$this->finish();
	}

	public function addImportPath($path)
	{
		$this->config['app']['controller']['includePaths'][] = $path;
	}

	abstract public function init();

	public function startSession($sessionStartHandler = null)
	{
		// only manage session info if the conf file defined session params
		if ($this->config['app']['session']) {

			$name = $this->config['app']['session']['name'];
			session_name($name ?: null);

			if (!$this->config['app']['session']['path']) {
				$this->config['app']['session']['path'] = "/"; // must at least be a slash
			}

			$lifetime = $this->config['app']['session']['expire'];
			$path = $this->config['app']['session']['path'];
			$domain = $this->config['app']['session']['domain'];
			$secure = $this->config['app']['session']['secure'];
			session_set_cookie_params($lifetime, $path, $domain, $secure);
			// . $_REQUEST[$name]
			$this->log("Starting session ($name, (strlen: " . strlen(array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : "") . ")), $lifetime, $path, $domain, $secure)", PEAR_LOG_DEBUG);

			if (isset($_COOKIE[$name]) && !$_COOKIE[$name]) {
				$this->log("Cookie isset, but evals to false... unsetting.");
				unset($_COOKIE[$name]);
			}

			if ($sessionStartHandler) {
				if (!class_exists($sessionStartHandler)) {
					if (!$this->import($sessionStartHandler . ".php")) {
						throw new Exception("Cannot find session start handler: $sessionStartHandler");
					}
				}
				$handler = new $sessionStartHandler;
				$handler->start();

			} else {
				session_start();
			}

			$lastAccess = array_key_exists('__LAVA_LAST_ACCESS', $_SESSION) ? $_SESSION['__LAVA_LAST_ACCESS'] : null;
			if (!$lastAccess) {
				$this->log("First page access in session");
				$this->refreshTimeout();
			} else {
				$timeout = $this->config['app']['session']['timeout'];
				$inactive = time() - $lastAccess;

				$this->log("Session inactive for " . (time() - $lastAccess) . " seconds (max is $timeout)");

				if ($inactive > $timeout) {
					$this->log("Session Timeout");
					$this->sessionTimeout();
				} else {
					$this->refreshTimeout();
				}
			}
		} else {
			$this->log("Skipping session management: none defined in conf.");
		}
	}

	public function sessionTimeout()
	{
		$this->lavaExit("Session Timeout");
	}

	public function refreshTimeout()
	{
		$this->log("Refreshing session timeout");
		$_SERVER['__LAVA_LAST_ACCESS'] = time();
	}

	public function manageFlash()
	{
		$last = array();
		if ($_SESSION && !$this->isAjax()) {
			foreach ($_SESSION as $k => $v) {
				if (preg_match("/_LAVA_FLASH_LAST_(\w+)$/", $k, $matches)) {
					$this->log("found $k in Flash");
					$last[$k] = $v;
				}
			}
			foreach ($last as $k => $v) {
				$_REQUEST[$k] = $v;
				$_SESSION[$k] = null;
				unset($_SESSION[$k]);
			}
		}
	}

	abstract public function checkAuthentication();

	public function parsePath()
	{
		$uri = $_SERVER['PHP_SELF'];
		$uri = preg_replace("/\?.*/", "", $uri);

		preg_match('@' . preg_quote($this->config['app']['controller']['uri'], '@') . '/(.*)@', $uri, $matches);
		$actionPath = $matches[1];
		$actionClass = $this->getPathClass($actionPath);

		$info = array();
		$info['actionClass'] = $actionClass;
		$info['actionPath'] = $actionPath;

		return $info;
	}

	public function getPathClass($actionPath)
	{
		if (str_contains($actionPath, "/")) {
			$actionClass = preg_replace("/.*\//", "", $actionPath);
		} else {
			$actionClass = $actionPath;
		}
		return $actionClass;
	}

	public function getActionPath()
	{
		return $this->pathInfo['actionPath'];
	}

	public function getActionClass()
	{
		return $this->pathInfo['actionClass'];
	}

	public function cache()
	{
		// implement in subclass
	}

	public function runAction($actionPath, $params = [], $checkCache = false)
	{
		if (!$actionPath) {
			$this->handleNoActionPath();
		} else {

			$actionClass = $this->getPathClass($actionPath);
			if (!class_exists($actionClass) && !$this->import($actionPath . ".php")) {
				$this->handleActionNotFound($actionPath);
			}

			$this->log("Running action: $actionClass", PEAR_LOG_DEBUG);
			$this->action = new $actionClass;
			if (!is_subclass_of($this->action, 'LavaAction')) {
				$this->lavaExit("Action $actionClass must extend LavaAction.");
			}
			if (is_array($params) && count($params)) {
				foreach ($params as $k => $v) {
					$this->action->setParam($k, $v);
				}
			}

			// check the cache. the action class has been included
			// at this point, so the cache() function has access
			// to the class vars
			if ($checkCache) {
				$this->cache();
			}

			$forward = $this->action->run($this);

			if (strtolower(get_class($forward)) === "actionforward") {
				// run the return action but DO NOT check cache again
				$this->runAction($forward->path, $forward->params, false);
			}
		}
	}

	public function handleActionNotFound($path)
	{
		$this->log("Action not found for path: $path", PEAR_LOG_INFO);
		if (!headers_sent()) {
			$this->httpHeader(404);
		}
		$this->errorExit("Action not found for path: $path");
	}

	public function handleNoActionPath()
	{
		$this->log("No action specified in URL", PEAR_LOG_ERR);
		if (!headers_sent()) {
			$this->httpHeader(404);
		}
		$this->errorExit("No action specified in URL");
	}

	public function finish()
	{
		// clean up
	}

	public function lavaUrl($action, $base = "")
	{
		$controller = $this->config['app']['controller']['uri'];

		// remove trailing slash on base URL
		while (substr($base, -1) == "/") {
			$base = substr($base, 0, -1);
		}

		// add slash to beginning of controller
		if (substr($controller, 0, 1) != "/") {
			$controller = "/$controller";
		}

		// add slash to end of controller
		if (substr($controller, 0, -1) != "/") {
			$controller = "$controller/";
		}

		// strip slash from beginning of action
		while (substr($action, 0, 1) == "/") {
			$action = substr($action, 1);
		}

		// strip slash from end of action
		while (substr($action, -1) == "/") {
			$action = substr($action, 0, -1);
		}

		return "$base$controller$action";
	}

	public function import($file)
	{
		return require basePath($file . ".php");
	}

	public function getImportPath($file, $name = "")
	{
		// autoloading
	}

	public function log($message, $level = PEAR_LOG_DEBUG, $indent = null)
	{
		$this->debug($message);
		$logFile = $this->config['app']['controller']['logFile'];

		if ($this->config && is_writable($logFile)) {
			$logger = new LavaLogger($logFile);
			$logger->log($level, $message, !is_null($indent) ? $indent : []);
		}
	}

	public function debug($message)
	{
		$this->debug[] = $message;
	}

	public function dump()
	{
		print "\n\n<!-- DEBUG INFO:\n\n" . implode("\n", str_replace("--", "- -", $this->debug)) . "\n\n-->\n\n";
	}

	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	public function getAttribute($name)
	{
		return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
	}

	public function httpHeader($code)
	{
		$msg = "Bad request";
		switch ($code) {
			case 301:
				$msg = "Moved Permanently";
				break;
			case 400:
				$msg = "Bad Request";
				break;
			case 401:
				$msg = "Unauthorized";
				break;
			case 404:
				$msg = "Not Found";
				break;
			case 500:
				$msg = "Internal Server Error";
				break;
		}
		header("HTTP/1.1 $code $msg");
	}

	public function isAjax()
	{
		$headers = apache_request_headers();
		foreach ($headers as $value) {
			if (strtolower(trim($value)) === strtolower("XMLHttpRequest")) {
				return true;
			}
		}
		return false;
	}

	public function lavaExit($error = "")
	{
		if (!headers_sent()) {
			$this->httpHeader(500);
		}
		return $this->errorExit($error);
	}

	public function errorExit($error = "")
	{
		print "<h1>Lava Exit</h1><p>$error</p>";
		exit();
	}

	public function redirectAction($action, $params = [])
	{
		if (is_array($params) && count($params)) {
			$vars = [];
			foreach ($params as $key => $value) {
				$vars[] = "$key=" . urlencode($value);
			}
			header("Location: " . $this->lavaUrl($action) . "?" . http_build_query($vars));
		} else {
			header("Location: " . $this->lavaUrl($action) . ($params ? "?$params" : ''));
		}
		exit();
	}

	public function getContentType()
	{
		$contentType = null;

		switch ($this->extension) {
			case 'json':
				$contentType = 'application/json';
				break;

			case 'gif':
				$contentType = 'image/gif';
				break;

			case 'jpg':
			case 'jpeg':
				$contentType = 'image/jpeg';
				break;

			case 'png':
				$contentType = 'image/png';
				break;

			case 'pdf':
				$contentType = 'application/pdf';
				break;

			case 'js':
				$contentType = 'text/javascript';
				break;
		}

		return $contentType ?: 'text/html';
	}

	abstract public function saveCachedResponse($response);

	public function setRoutes($routes = [])
	{
		$this->routes = $routes;
	}

	public function addRoute($route = [])
	{
		$this->routes[] = $route;
	}

	public function setConfig($config = [])
	{
		$this->config = $config;
	}

	public function addConfig($option = [])
	{
		$this->config[] = $option;
	}
}