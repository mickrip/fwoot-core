<?php

namespace Fw;

class Debug
{
	static $stack = array();
	static $count = 0;

	static function add($data)
	{
		self::$stack[self::$count] = $data;
		self::$count++;
	}

	static function get()
	{
		return implode(", ", self::$stack);

	}

	static function clear()
	{
		self::$stack = array();
		self::$count = 0;
	}
}