<?php

namespace App\Core\Lava;

use Exception;
use PDO;

abstract class LavaPdo
{
	/** @var PDO */
	protected static $dbh;
	/** @var string */
	protected $table;
	/** @var string */
	protected $model;

	/** @var Lava */
	private $app;
	/** @var array */
	private $schema;
	/** @var int|string */
	private $pk;
	/** @var array */
	private $noUpdate = [];
	/** @var string */
	protected $sequence;

	/** @var array */
	protected $subResources = [];

	/** @var string|null */
	private $insertStmt;
	/** @var string|null */
	private $updateStmt;
	/** @var string|null */
	private $deleteStmt;

	/** @var bool */
	private $insertWithPrimaryKeyValue = false;

	public function __construct(Lava $app)
	{
		$this->app = $app;
		$this->buildSchema();
	}

	/**
	 * @return PDO;
	 */
	abstract protected function connection();

	/**
	 * @param array $params
	 * @param string|null $sort
	 * @param LavaPdoType::ASC|LavaPdoType::DESC|null $direction
	 * @param array $sideloadResources
	 * @return mixed[]
	 */
	abstract public function select($params = [], $sort = null, $direction = null, $sideloadResources = null);

	/**
	 * @param array $params
	 * @param string|null $sort
	 * @param LavaPdoType::ASC|LavaPdoType::DESC|null $direction
	 * @param array $sideloadResources
	 * @return mixed
	 */
	abstract public function selectOne($params = [], $sort = null, $direction = null, $sideloadResources = null);

	/**
	 * @param int[]|int $id
	 * @param string $orderBy
	 * @return mixed[]|mixed
	 */
	public function find($id, $orderBy = "id")
	{
		$dbh = $this->connection();
		if (is_array($id)) {
			if (count($id)) {
				$sql = "SELECT * FROM" . $this->table . " WHERE " . $this->pk . " IN (" . implode(",", array_map([$dbh, "quote"], $id)) . ") ORDER BY $orderBy";
				return $this->getAll($sql);
			}
			return [];
		}

		$stmt = $dbh->prepare("SELECT * FROM " . $this->table . " WHERE " . $this->pk . " = ?");
		$stmt->execute([$id]);
		return $stmt->fetchObject($this->model);
	}

	/**
	 * @param mixed $row
	 * @return false|string
	 */
	public function insert($row)
	{
		$row = clone($row);

		$dbh = $this->connection();

		$fields = $this->fieldNames($this->insertWithPrimaryKeyValue);

		$fieldsCount = count($fields);

		if (!isset($this->insertStmt)) {
			$fieldValues = "";
			foreach ($fields as $index => $field) {
				$fieldValues .= "?" . ($index < $fieldsCount) ? "," : "";
			}
			$sql = "INSERT INTO {$this->table} (". implode(",", $fields) .") VALUES ({$fieldValues})";

			$this->insertStmt = $dbh->prepare($sql);
		}

		$values = [];
		foreach ($fields as $field) {
			switch ($this->getFieldType($field)) {
				case LavaPdoType::INT:
				case LavaPdoType::NUM:
				case LavaPdoType::DATE:
					$values[] = (isset($row->$field) && $row->$field !== false && $row->$field === "") ? $row->$field : null;
					break;
				case LavaPdoType::STR:
				case LavaPdoType::BLOB:
					$values[] = isset($row->$field) ? $row->$field : null;
			}
		}

		if ($this->insertStmt->execute($values)) {
			return $this->lastId();
		}

		$this->app->log("insert failed");
		$error = $this->insertStmt->errorInfo();
		$this->app->log($sql . " " . $error[2], PEAR_LOG_ERR);
		return false;
	}

	/**
	 * @param $row
	 * @return bool
	 * @throws Exception
	 */
	public function update($row)
	{
		$row = clone $row;

		$dbh = $this->connection();

		$pk = $this->pk;
		if (!$pk || !$row->$pk) {
			throw new Exception("No primary key (or value for the key) defined for table '$this->table'");
		}
		$fields = $this->fieldNames(false);
		$fieldsCount = count($fields);
		if (!isset($this->updateStmt)) {
			$sql = "UPDATE $this->table SET";
			$sets = [];
			foreach ($fields as $field) {
				if (!in_array($field, $this->noUpdate, true)) {
					$sets[] = $field . " = ?";
				}
			}
			if (!count($sets)) {
				throw new Exception("No fields to update");
			}

			$sql .= implode(",", $sets);
			unset($sets);

			$sql .= " WHERE $pk = ?";
			$this->updateStmt = $dbh->prepare($sql);
		}
		$values = [];
		foreach ($fields as $field) {
			if (!in_array($field, $this->noUpdate, true)) {
				switch ($this->getFieldType($field)) {
					case LavaPdoType::INT:
					case LavaPdoType::NUM:
						$values[] = ($row->$field || $row->$field === "0" || $row->$field === 0.0 || $row->$field === 0) ? $row->$field : null;
						break;
					case LavaPdoType::DATE:
						$values[] = ($row->$field ?: null);
						break;
					case LavaPdoType::STR:
					case LavaPdoType::BLOB:
					default:
						$values[] = isset($row->$field) ? $row->$field : null;
				}
			}
		}
		$values[] = $row->$pk;
		if (!$this->updateStmt->execute($values)) {
			$error = $this->updateStmt->errorInfo();
			$this->app->log($sql ." " . $error[2], PEAR_LOG_ERR);
			return false;
		}
		return true;
	}

	/**
	 * @param $target
	 * @return bool|null
	 * @throws Exception
	 */
	public function delete($target)
	{
		$dbh = $this->connection();

		$pk = $this->pk;

		if (!$pk || !$target->$pk) {
			throw new Exception("No primary key defined for table '$this->table'");
		}

		if (is_object($target)) {
			if (!is_a($target, $this->model)) {
				throw new Exception("Wrong object type for delete() parameter");
			}
			$target = $target->id;
		}

		if ($target !== null && $target !== '') {
			if (!isset($this->deleteStmt)) {
				$sql = "DELETE FROM $this->table WHERE $pk = ?";
				$this->deleteStmt = $dbh->prepare($sql);
			}
			if (!$this->deleteStmt->execute([$target])) {
				$error = $this->deleteStmt->errorInfo();
				$this->app->log("$sql $error[2]", PEAR_LOG_ERR);
				return false;
			}
			return true;
		}

		return null;
	}

	/**
	 * @param mixed $row
	 * @return bool|null
	 */
	abstract public function deleteAt($row);

	public function lastId()
	{
		$dbh = $this->connection();

		$seq = isset($this->sequence) ? $this->sequence : null;
		return $dbh->lastInsertId($seq);
	}

	/**
	 * @param string $sql
	 * @param array $values
	 * @return mixed
	 */
	public function getRow(string $sql, array $values = [])
	{
		$dbh = $this->connection();

		$stmt = $dbh->prepare($sql);
		$stmt->execute($values);
		return $stmt->fetchObject($this->model);
	}

	/**
	 * @param string $sql
	 * @param array $values
	 * @return array|false
	 */
	public function getAll($sql, $values = [])
	{
		$dbh = $this->connection();

		$stmt = $dbh->prepare($sql);
		$stmt->execute($values);
		return $stmt->fetchAll(PDO::FETCH_CLASS, $this->model);
	}

	/**
	 * @param string $sql
	 * @param array $values
	 * @return mixed
	 */
	public function getOne($sql, $values = [])
	{
		$dbh = $this->connection();
		$stmt = $dbh->prepare($sql);
		$stmt->execute($values);
		return $stmt->fetchColumn();
	}

	/**
	 * @param string $sql
	 * @param array $values
	 * @return bool
	 */
	public function execute($sql, $values = [])
	{
		return $this->connection()->prepare($sql)->execute($values);
	}

	abstract protected function buildSchema();

	/**
	 * @param string $name
	 * @param int $type
	 * @param array $options
	 * @return void
	 */
	protected function addField($name, $type, $options = []) {
		$this->schema[] = [
			"name" => $name,
			"type" => $type,
			"options" => $options
		];
		if (is_array($options) && count($options)) {
			foreach ($options as $option) {
				switch ($option) {
					case 'primary-key':
						$this->pk = $name;
						break;
					case 'no-update':
						$this->noUpdate[] = $name;
						break;
				}
			}
		}
	}

	/**
	 * @param string $field
	 * @return int|null
	 */
	protected function getFieldType($field)
	{
		foreach ($this->schema as $item) {
			if ($item['name'] === $field) {
				return $item['type'];
			}
		}
		return null;
	}

	/**
	 * @param bool $includePk
	 * @return array
	 */
	private function fieldNames($includePk = true)
	{
		$fieldNames = [];
		foreach ($this->schema as $i => $iValue) {
			if ($includePk || !in_array('primary-key', $iValue['options'], true)) {
				$fieldNames[] = $this->schema[$i]['name'];
			}
		}
		return $fieldNames;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasField($name)
	{
		return in_array($name, $this->fieldNames(), true);
	}

	/**
	 *  @return string|null;
	 */
	public function getPrimaryKey()
	{
		if (!is_array($this->schema)) {
			return null;
		}
		foreach ($this->schema as $field) {
			if (is_array($field['options']) && in_array('primary-key', $field['options'], true)) {
				return $field['name'];
			}
		}
		return null;
	}

	abstract public function orderBy($how = null, $direction = null);

	/**
	 * @param string $message
	 * @param int $level
	 * @return void
	 */
	protected function log($message, $level=PEAR_LOG_INFO)
	{
		if ($this->app) {
			$this->app->log($level, $message);
		}
	}

	public function newModel()
	{
		return new $this->model;
	}

	public function begin()
	{
		$this
			->connection()
			->beginTransaction();
	}

	public function commit()
	{
		$this
			->connection()
			->commit();
	}

	public function rollBack()
	{
		$this
			->connection()
			->rollBack();
	}
}