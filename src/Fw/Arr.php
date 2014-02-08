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
}