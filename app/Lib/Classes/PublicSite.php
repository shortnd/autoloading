<?php

namespace App\Lib\Classes;

use App\Core\Lava\Lava;
use App\Core\Lava\LavaAction;

class PublicSite extends Lava
{
	/** @var array */
	public $routes;

	public $extension;

	private $livePersonSections;

	public function __construct()
	{
		parent::__construct($this);
		require basePath("conf/public.conf.php");
		require basePath("conf/public.routes.php");
	}

	public function init()
	{
		if (!$this->isAjax()) {
			require_once(basePath('conf/public.redirects.php'));
		}
	}

	public function handleNoActionPath()
	{
		$this->runAction("Home", null, true);
	}

	public function sessionTimeout()
	{
		return true;
	}

	public function checkAuthentication()
	{
		return true;
	}

	public function parsePath()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$uri = preg_replace("/\?.*/", "", $uri);

		if (
			preg_match('@' . preg_quote($this->config['app']['controller']['uri'], '@') . '/?(.*)@', $uri, $matches)
		) {
			$this->redirectAction($matches[1], $_GET);
		}
		if (preg_match("/\/{2,}/", $uri)) {
			$uri = preg_replace("/\/{2,}/", "/", $uri);
			$this->redirectAction($uri, $_GET);
		}
		if (preg_match("/\.(\w+)$/", $uri, $matches)) {
			$this->extension = $matches[1];

			$uri = substr($uri, 0, -(strlen($this->extension) + 1));
			header("Content-Type: " . $this->getContentType());
		}

		$info = [];
		$info['actionPath'] = explode('/', preg_replace("/(^\/|\/$)/", "", $uri));

		return $info;
	}

	public function runAction($actionPath, $params = [], $checkCache = true)
	{
		if (!is_array($actionPath)) {
			$actionPath = [$actionPath];
		}
		if (count($actionPath) <= 0 || !$actionPath[0]) {
			$this->handleNoActionPath();
			return;
		}
		$pathParts = $actionPath;
		$actionProps = [];
		$_actionPath = null;
		foreach ($this->routes as $routePath => $routeAction) {
			$routePathParts = array_reverse(explode("/", $routePath), 1);

			$_actionProps = [];
			foreach ($routePathParts as $key => $routePathPart) {
				if ($pathParts[$key] && preg_match("/^\$/", $routePathPart)) {
					$_actionProps[substr($routePathPart, 1)] = $pathParts[$key];
					continue;
				}

				if ($routePathPart !== $pathParts[$key]) {
					continue(2);
				}
			}

			$_actionPath = $routeAction;
			$this->pathInfo['route'] = $routePath;
			$actionProps = $_actionProps;
			break;
		}

		if (!is_null($_actionPath)) {
			$actionPath = $_actionPath;
			unset($_actionPath);
		} else {
			$actionClass = Utils::handleToCamelCase(array_pop($actionPath));
			$actionPath[] = $actionClass;
			$actionPath = implode("/", $actionPath);
		}

		$actionClass = $this->getPathClass($actionPath);
		if (!class_exists($actionClass) || !is_subclass_of($actionClass, LavaAction::class)) {
			die("Look up via page table");
		}

		$this->log("Running action: $actionClass");

		$this->action = new $actionClass($this, $actionProps);
		if (!is_subclass_of($this->action, LavaAction::class)) {
			$this->lavaExit("Action $actionClass must extend LavaAction.");
		}

		if (is_array($params) && count($params)) {
			foreach ($params as $key => $value) {
				$this->action->setParam($key, $value);
			}
		}

		$response = null;
		if ($checkCache && $this->getAttribute('cache-responses')) {
			// $response = $this->cache();
		}

		if (!$response) {
			ob_start();
			$forward = $this->action->run($this);
			$response = ob_get_contents();
			ob_end_clean();

			if (is_object($forward) && strtolower(get_class($forward)) === "actionforward") {
				$this->runAction($forward->path, $forward->params, false);
				return;
			}
			if ($this->getAttribute('cache-responses') && $actionClass::$cache === true) {
				$this->saveCachedResponse($response);
			}

			print $response;
		}
	}

	public function getPathClass($actionPath)
	{
		return "\App\Actions\Public\\" . $actionPath;
	}

	public function import($file)
	{
//		include(basePath("app/Actions/Public/" . $file . ".php"));
//		return require basePath("app/Actions/Public/" . $file . ".php");
	}
}