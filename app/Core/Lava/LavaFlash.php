<?php

namespace App\Core\Lava;

class LavaFlash
{
	private static string $message;
	private static string $error;
	private static string $notice;
	private static array $values = [];

	/**
	 * @param string|null $value
	 * @return mixed|string
	 */
	public static function message(string $value = null)
	{
		if ($value !== null) {
			self::$message = $value;
			$_SESSION['_LAVA_FLASH_LAST_message'] = $value;
		}
		return self::$message ?? $_REQUEST['_LAVA_FLASH_LAST_message'];
	}

	/**
	 * @param $value
	 * @return mixed|string
	 */
	public static function error($value = null)
	{
		if ($value !== null) {
			self::$error = $value;
			$_SESSION['_LAVA_FLASH_LAST_error'] = $value;
		}
		return self::$error ?? $_REQUEST['_LAVA_FLASH_LAST_error'];
	}

	/**
	 * @param $value
	 * @return mixed|string
	 */
	public static function notice($value = null)
	{
		if ($value !== null) {
			self::$notice = $value;
			$_SESSION['_LAVA_FLASH_LAST_notice'] = $value;
		}
		return self::$notice ?? $_REQUEST['_LAVA_FLASH_LAST_notice'];
	}

	/**
	 * @param $name
	 * @param $value
	 * @return void
	 */
	public static function put($name, $value)
	{
		if ($name && isset($value)) {
			self::$values[$name] = $value;
			$_SESSION["_LAVA_FLASH_LAST_$name"] = $value;
		}
	}

	/**
	 * @param $name
	 * @return mixed|null
	 */
	public static function get($name)
	{
		if ($name) {
			return self::$values[$name] ?? $_REQUEST["_LAVA_FLASH_LAST_$name"];
		}
		return null;
	}

	/**
	 * @return void
	 */
	public static function cleanMessage()
	{
		$_SESSION['_LAVA_FLASH_LAST_error'] = null;
		$_SESSION['_LAVA_FLASH_LAST_message'] = null;
		$_SESSION['_LAVA_FLASH_LAST_notice'] = null;
		self::$error = null;
		self::$message = null;
		self::$notice = null;

		$last = [];
		foreach ($_SESSION as $key => $value) {
			if (preg_match("/_LAVA_FLASH_LAST_(\w+)$/", $key, $matches)) {
				$last[$key] = $value;
			}
		}
		foreach ($last as $key => $value) {
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
		}
	}
}