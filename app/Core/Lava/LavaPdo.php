<?php

namespace App\Core\Lava;

use Exception;
use PDO;

abstract class LavaPdo
{
	protected static $dbh;
	public $table;

	public $model;

	private $app;
	private $schema;
	private $pk;
	private $noUpdate = [];
	private $sequence;
	private $insertStmt;
	private $updateStmt;
	private $deleteStmt;

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

	public function find($id, $orderBy = "id")
	{
		$dbh = $this->connection();
		if (is_array($id)) {
			if (count($id)) {
				$sql = "SELECT * FROM" . $this->table . " WHERE " . $this->pk . " IN (" . implode(",", array_map([$dbh, "quote"], $id)) . ") ORDER BY $orderBy";
				return $this->getAll($sql);
			}
			return [];
		} else {
			$stmt = $dbh->prepare("SELECT * FROM " . $this->table . " WHERE " . $this->pk . " = ?");
			$stmt->execute([$id]);
			return $stmt->fetchObject($this->model);
		}
	}

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
				case LavaFieldType::INT:
				case LavaFieldType::NUM:
				case LavaFieldType::DATE:
					$values[] = (isset($row->$field) && $row->$field !== false && $row->$field === "") ? $row->$field : null;
					break;
				case LavaFieldType::STR:
				case LavaFieldType::BLOB:
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
					case LavaFieldType::INT:
					case LavaFieldType::NUM:
						$values[] = ($row->$field || $row->$field === "0" || $row->$field === 0.0 || $row->$field === 0) ? $row->$field : null;
						break;
					case LavaFieldType::DATE:
						$values[] = ($row->$field ?: null);
						break;
					case LavaFieldType::STR:
					case LavaFieldType::BLOB:
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