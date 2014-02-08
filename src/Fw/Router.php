<?php
namespace Fw;

class Router
{
	static $alto;
	static $default_parms = array();

	static function check_alto()
	{
		if (!self::$alto) {
			self::$alto = new \AltoRouter();
		}
	}

	static function run()
	{
		if (!$match = self::$alto->match()) return false;

		foreach (self::$default_parms as $key => $val) {
			if (!isset($match["params"][$key])) {
				$match["params"][$key] = $val;
			}
		}

		call_user_func($match["target"], (object)$match["params"]);
		return true;
	}


	static function map($pattern, $target, $name = '', $method = 'GET|POST')
	{
		self::check_alto();
		$route_pattern = str_replace("//", "/", \Fw\Config::get("base") . $pattern);
		self::$alto->map($method, $route_pattern, $target, $name);
	}

	static function set_default_param($key, $val)
	{
		self::$default_parms[$key] = $val;
	}

}