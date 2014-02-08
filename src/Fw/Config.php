<?php
namespace Fw;

/**
 * Configuration class. Stores static configuration vars for internal framework with getter and setter.
 * Also has a load utility function for config files.
 *
 * Class Config
 * @package Fw
 */
class Config
{

	static $stack = array();

	// stolen from Kohana <3
	const PRODUCTION = 10;
	const STAGING = 20;
	const TESTING = 30;
	const DEVELOPMENT = 40;

	/**
	 * Loads a php file which returns an array of config data
	 *
	 * @param $filename
	 * @return mixed
	 */

	static function load($filename)
	{
		if (isset(self::$stack[$filename])) return self::$stack[$filename];
		$path = self::get("apppath") . "config" . DS . $filename;
		if (!file_exists($path)) throw new \Exception("Config file '$filename' not found'");
		$config_data = include($path);
		self::set($filename, $config_data);
		return $config_data;
	}


	/**
	 * Config Setter
	 *
	 * @param $var
	 * @param $val
	 */
	static function set($var, $val)
	{
		self::$stack[$var] = $val;
	}


	/**
	 * Config Getter
	 *
	 * @param $var
	 * @return bool
	 * @throws \Exception
	 */
	static function get($var, $filename = '')
	{

		if ($filename) {
			$config_data = self::load($filename);
			if (!isset($config_data[$var])) throw new \Exception("Config value \"$var\" not found in file '$filename'.");
			return $config_data[$var];
		}

		if (!isset(self::$stack[$var])) throw new \Exception("Config value \"$var\" not found. (Looking in Global Stack)");
		return (isset(self::$stack[$var])) ? self::$stack[$var] : false;
	}


	static function exists($var)
	{
		return (isset(self::$stack[$var])) ? self::$stack[$var] : false;
	}


	/**
	 * Debug Shit
	 */

	static function dump()
	{
		foreach (self::$stack as $key => $val) {
			if (is_string($val) || is_numeric($val)) {
				echo "<div>$key | $val </div>\n";
			} else {
				echo "<div>$key | ";
				var_dump($val);
				echo "</div>";
			}
		}
	}

}
