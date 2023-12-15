<?php

namespace App\Lib\Classes;

use App\Core\Lava\Lava;
use App\Core\Lava\LavaAction;

class Controller extends LavaAction
{
//	protected $app;
	protected $method;
	protected $route;

	public function __construct(Lava $app, $actionParams = [])
	{
		parent::__construct($app, $actionParams);

		$this->route = array_key_exists('route', $this->app->pathInfo) ? $this->app->pathInfo['route'] : '/';

		$this->params = array_slice($this->app->pathInfo['actionPath'], count(explode("/", $this->route)));

		$this->method = $this->defineMethod();
	}

	public function run()
	{
		call_user_func_array([$this, $this->method], $this->params);
	}

	public function defineMethod()
	{
		$method = $this->getRequestVerb();
		$action = array_shift($this->params);

		if ($action !== '') {
			$method .= Utils::handleToCamelCase($action);
		}
		if (!method_exists($this, $method)) {
			$this->app->redirectAction($this->route, $_GET);
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

	public function render($template, $params = [])
	{
		var_dump([$template, $params]);
		return "";
	}
}