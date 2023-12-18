<?php

namespace App\Dao;

use App\Core\Lava\LavaPdoType;
use App\Model\Todo;

class TodoDao extends BaseDao
{
	protected $table = "todos";
	protected $model = Todo::class;
	protected $sequence = "todos_id_seq";

	protected function buildSchema()
	{
		$this->addField('id', LavaPdoType::INT, ['primary-key']);
		$this->addField('text', LavaPdoType::STR);
		$this->addField('done', LavaPdoType::INT);
	}
}