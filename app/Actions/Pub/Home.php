<?php

namespace App\Actions\Pub;

use App\Core\Lava\Lava;
use App\Core\Lava\LavaAction;
use App\Lib\Classes\PublicParser;
use SmartyException;

class Home extends LavaAction
{
	public function view()
	{
		$this->render('home.tpl', []);
	}

	public function render(string $template, $params = []): bool
	{
		$publicParser = new PublicParser($this->app);

		if (!is_array($params)) {
			$params = [$params];
		}

		$publicParser->assign($params);
		try {
			$publicParser->display($template);
			return true;
		} catch (SmartyException $e) {
			$this->app->log("Unable to load template: $template", PEAR_LOG_ERR);
		}
		return false;
	}
}