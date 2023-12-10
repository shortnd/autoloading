<?php

define("BASE_PATH", dirname(__DIR__) . '/');

use App\Lib\Classes\PublicSite;
use Monolog\Logger;

require BASE_PATH . 'functions.php';
require BASE_PATH . '/vendor/autoload.php';

$app = new PublicSite();
$app->run();
