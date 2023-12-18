<?php

use App\Actions\Pub\AboutController;
use App\Actions\Pub\Home;
use App\Actions\Pub\TodoController;

$this->routes = [
	'about' => AboutController::class,
	'todos' => TodoController::class
];