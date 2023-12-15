<?php

namespace App\Dao;

use App\Core\Lava\LavaPdoType;
use App\Model\Product;

class ProductDao extends BaseDao
{
	protected $table = "products";
	protected $model = Product::class;
	protected $sequence = "products_id_seq";

	protected function buildSchema()
	{
		$this->addField('id', LavaPdoType::INT, ['primary-key']);
		$this->addField('created_at', LavaPdoType::DATE, ['no-update']);
		$this->addField('name', LavaPdoType::STR);
	}
}