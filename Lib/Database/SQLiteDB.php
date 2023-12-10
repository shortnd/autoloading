<?php

namespace Lib\Database;

use PDO;
use PDOException;

class SQLiteDB
{
	private PDO $db;

	public function __construct(string $dbFile)
	{
		try {
			$this->db = new PDO("sqlite:$dbFile");
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $exception) {
			die("Database Connection Error: " . $exception->getMessage());
		}
	}

	public function query($sql, $params = [])
	{
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}

	public function fetchAll($sql, $params = [])
	{
		return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetch($sql, $params = [])
	{
		return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
	}

	public function lastInsertId()
	{
		return $this->db->lastInsertId();
	}
}