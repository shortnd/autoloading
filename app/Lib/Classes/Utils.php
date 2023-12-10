<?php

namespace App\Lib\Classes;

final class Utils
{
	public static function handleToCamelCase($string = "", $lowerFirst = false)
	{
		$parts = array_map(static function ($part) {
			return ucwords($part);
		}, explode("-", (string)$string));
		if ($lowerFirst) {
			$parts[0] = strtolower($parts[0]);
		}
		return implode("", $parts);
	}
}