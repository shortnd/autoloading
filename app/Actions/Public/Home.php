<?php

namespace App\Actions\Public;

use App\Core\Lava\LavaAction;

class Home extends LavaAction
{
	public function run()
	{
		$this->app->log("DUDE", PEAR_LOG_INFO);
		echo "CHECK A FILE?";
	}

	public function render($template, $params = [])
	{
		// TODO: Implement render() method.
	}
}