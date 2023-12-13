<?php

namespace App\Core\Lava;

use Exception;
use SmartyException;

abstract class LavaAction
{
	public $app;
	public $params;

	private $tpl;

	public static $cache = false;

	public function __construct(Lava $app, $actionParams = [])
	{
		$this->app = $app;
//		$this->tpl = $tpl;
		$this->params = $actionParams;
	}

	/**
	 * @return mixed
	 */
	abstract public function run(
//		Lava $app
	);
//	{
////		$this->app = $app;
//		// ..override?
//	}

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

	abstract public function render($template, $params = []);
//	{
////		if (!is_array($params)) {
////			$params = [$params];
////		}
////		$this->tpl->assign($params);
////
////		try {
////			$this->tpl->display($template);
////			return true;
////		} catch (SmartyException|Exception $_) {
////			return false;
////		}
//	}
}