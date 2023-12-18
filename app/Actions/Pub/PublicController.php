<?php

namespace App\Actions\Pub;

use App\Core\Lava\Lava;
use App\Core\Lava\LavaAction;
use App\Lib\Classes\PublicParser;
use App\Lib\Classes\Utils;
use RuntimeException;
use SmartyException;

class PublicController extends LavaAction
{
	public function render($template, $params = []): bool
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
			if ($_ENV['DEV']) {
				throw new RuntimeException("Unable to to load template: $template");
			}
			$this->app->log("Unable to load template: $template", PEAR_LOG_ERR);
		}
		return false;
	}
}