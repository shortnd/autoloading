<?php

namespace App\Core\Lava;

class ActionForward
{
	public string $path;
	public array $params;

	public function __construct(string $path, array $params = [])
	{
		$this->path = $path;
		$this->params = $params;
	}
}