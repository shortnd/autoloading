<?php

namespace App\Actions\Pub;

use App\Dao\TodoDao;

class TodoController extends PublicController
{
	public function view(): void
	{
		$todoDao = new TodoDao($this->app);
		$todos = $todoDao->getAll("SELECT * FROM todos");
		$this->render('todos/index.tpl', [
			'todos' => []
		]);
	}
}