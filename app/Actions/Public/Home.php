<?php

namespace App\Actions\Public;

use App\Core\Lava\LavaAction;

class Home extends LavaAction
{
	public function run()
	{
		$this->app->log("DUDE", PEAR_LOG_INFO);
		echo "CHECK A FILE?";
//		return $this->render('home.tpl');
//		return $this->render('');
//		echo <<<HTML
//<h1>It works ish</h1>
//<p>Now we just need to work on templating...</p>
//<p>Logging</p>
//<p>LavaPDO</p>
//<p></p>
//HTML;

	}
}