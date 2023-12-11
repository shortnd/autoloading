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

	public const INT = 1;
	public const STR = 2;
	public const DATE = 3;
	public const NUM = 4;
	public const BLOB = 5;

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
			$fieldName = $fields;
			switch ($this->getFieldType($fieldName)) {
				case self::INT:
				case self::NUM:
				case self::DATE:
					$values[] = (isset($row->$fieldName) && $row->$fieldName !== false && $row->$fieldName === "") ? $row->$fieldName : null;
					break;
				case self::STR:
				case self::BLOB:
					$values[] = isset($row->$fieldName) ? $row->$fieldName : null;
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
					case self::INT:
					case self::NUM:
						$values[] = ($row->$field || $row->$field === "0" || $row->$field === 0.0 || $row->$field === 0) ? $row->$field : null;
						break;
					case self::DATE:
						$values[] = ($row->$field ?: null);
						break;
					case self::STR:
					case self::BLOB:
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

	public function getAll($sql, $values = [])
	{
		$dbh = $this->connection();

		$stmt = $dbh->prepare($sql);
		$stmt->execute($values);
		return $stmt->fetchAll(PDO::FETCH_CLASS, $this->model);
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

	private function getFieldType($fieldName)
	{
		foreach ($this->schema as $value) {
			if ($value['name'] === $fieldName) {
				return $value['type'];
			}
		}
		return null;
	}
}