<?php

namespace Fw;

/**
 * Array Helper Class
 *
 * @package Fw
 */

class Arr
{

	/**
	 * Retrieve key from an array. Default value will be returned if
	 * key does not exist.
	 *
	 * @param $array
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}


	/**
	 * @param $array
	 * @param $column_key
	 *
	 * Stolen from a comment on php.net from vovan-ve at yandex dot ru
	 * and modified to deal with objects as well as arrays
	 */

	public static function column(Array $input, $columnKey, $indexKey = null)
	{
		$result = array();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values($input);
			} else {
				foreach ($input as $row) {
					switch (gettype($row)) {
						case "array":
							$result[] = $row[$columnKey];
							break;
						case "object":
							$result[] = $row->$columnKey;
							break;
					}
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			} else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}

		return $result;
	}
}