<?php

namespace App\Dao;

use App\Core\Lava\LavaPdo;
use PDO;

class BaseDao extends LavaPdo
{

	/**
	 * @inheritDoc
	 */
	protected function connection()
	{
		return new PDO("sqlite:" . basePath("/storage/db.sqlite"));
	}

	/**
	 * @inheritDoc
	 */
	public function select($params = [], $sort = null, $direction = null, $sideloadResources = null)
	{
		// TODO: Implement select() method.
	}

	/**
	 * @inheritDoc
	 */
	public function selectOne($params = [], $sort = null, $direction = null, $sideloadResources = null)
	{
		// TODO: Implement selectOne() method.
	}

	/**
	 * @inheritDoc
	 */
	public function deleteAt($row)
	{
		// TODO: Implement deleteAt() method.
	}

	protected function buildSchema()
	{
		// TODO: Implement buildSchema() method.
	}

	public function orderBy($how = null, $direction = null)
	{
		// TODO: Implement orderBy() method.
	}
}