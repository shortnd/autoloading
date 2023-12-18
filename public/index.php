<?php

use App\Lib\Classes\PublicSite;
use App\Psr4AutoloaderClass;

define("BASE_PATH", dirname(__DIR__) . '/');

//use App\Lib\Classes\PublicSite;

$_ENV['DEV'] = true;

require BASE_PATH . 'functions.php';
require basePath('app/Psr4AutoloaderClass.php');
//require basePath('deps/smarty3/Autoloader.php');
require basePath('deps/smarty3/bootstrap.php');

$loader = new Psr4AutoloaderClass();
$loader->register();
// PSR-0 Autoloading for smarty
//$loader->addNamespace('Smarty', basePath('deps/smarty3'));
$loader->addNamespace('App', basePath('app'));

$app = new PublicSite();
//$app->setRoutes(basePath('conf/public.routes.php'));
//$app->addRoute([
//	'about' => 'AboutController'
//]);
$app->run();
