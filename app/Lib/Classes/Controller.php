<?php

namespace App\Lib\Classes;

use App\Core\Lava\Lava;
use App\Core\Lava\LavaAction;

class Controller extends LavaAction
{
//	protected $app;

	public function render(string $template, $params = []): bool
	{
		var_dump([$template, $params]);
		return false;
	}
}