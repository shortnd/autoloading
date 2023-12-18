<?php

namespace App\Actions\Pub;

use App\Dao\ProductDao;
use App\Lib\Classes\Controller;

class AboutController extends PublicController
{
	public function view()
	{
		$this->render('about.tpl');
	}
}