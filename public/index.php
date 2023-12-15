<?php

use App\Lib\Classes\PublicSite;
use App\Psr4AutoloaderClass;

define("BASE_PATH", dirname(__DIR__) . '/');

//use App\Lib\Classes\PublicSite;

require BASE_PATH . 'functions.php';
require basePath('app/Psr4AutoloaderClass.php');

$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('App', basePath('app'));

$app = new PublicSite();
$app->addRoute([
	'about' => 'AboutController'
]);
$app->run();
