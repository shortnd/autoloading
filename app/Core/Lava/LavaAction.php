<?php

namespace App\Core\Lava;

use Exception;
use SmartyException;

abstract class LavaAction
{
	public $app;
	public $params;
	public $route;
	public $method;

	public static $cache = false;

	public function __construct(Lava $app, $actionParams = [])
	{
		$this->app = $app;

		$this->params = $actionParams;
		$this->route = array_key_exists('route', $app->pathInfo) ? $app->pathInfo['route'] : null;
		$this->method = $this->defineMethod();
	}

	/**
	 * @return void
	 */
	public function run()
	{
		call_user_func_array([$this, $this->method], $this->params);
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getParam($name)
	{
		return $this->params[$name];
	}

	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	public function defineMethod(): string
	{
		// The method of this controller we'll be calling in run()
		$method = $this->getRequestVerb();

		// The "action" being called via this controller
		$action = array_shift($this->params);

		if (!is_null($action) && $action !== '') {
			$method .= $this->handleToCamelCase($action);
		}

		if (!method_exists($this,$method)) {
			$this->app->redirectAction($this->route,$_GET);
		}

		return $method;
	}

	public function getRequestVerb()
	{
		$requestMethod = 'get';
		if (array_key_exists('override_request_method', $_POST)) {
			$requestMethod = strtolower($_POST['override_request_method']);
		} else if ($_SERVER['REQUEST_METHOD']) {
			$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
		}

		switch ($requestMethod) {
			case "head":
				exit();
			case "put":
				return "update";
			case "delete":
				return "delete";
			case "post":
				return "add";
			case "get":
				return "view";
			default:
				return $requestMethod;
		}
	}

	abstract public function render(string $template, $params = []): bool;


	private function handleToCamelCase(string $string, bool $lowerFirst = false): string
	{
		$parts = array_map(static function ($part) {
			return ucwords($part);
		}, explode("-", $string));
		if ($lowerFirst) {
			$parts[0] = strtolower($parts[0]);
		}
		return implode("", $parts);
	}
}