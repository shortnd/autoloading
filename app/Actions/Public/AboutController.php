<?php

namespace App\Actions\Public;

use App\Dao\ProductDao;
use App\Lib\Classes\Controller;

class AboutController extends Controller
{
	public function view()
	{
		$productDao = new ProductDao($this->app);
		dd($productDao->getAll("SELECT * FROM products"));
	}
}