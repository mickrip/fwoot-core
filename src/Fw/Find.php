<?php
namespace Fw;

/**
 * Fw00t Finder
 * @package Fw
 */

class Find
{

	static $path = array();
	static $class_dirs = array();
	static $filters = array();

	static function file($filename, Array $locations, $debug = false)
	{
		foreach ($locations as $val) {

			$_fullpath = $val . $filename;

			if ($debug) {
				echo "considering: $_fullpath <br/>";
			}
			if (file_exists($_fullpath)) {
				if ($debug) echo "FOUND: $_fullpath <br/>";
				return $_fullpath;
			}
		}
	}


	static function dir($dirname, Array $locations)
	{
		foreach ($locations as $val) {
			if (is_dir($val . "/" . $dirname)) {
				return $val . "/" . $dirname;
			}
		}
		return false;
	}

	static function add_path_to($cat, $path)
	{
		//echo "<br/>Adding ($path) to $cat<br/>";
		self::$path[$cat][] = $path;
	}

	static function dir_in($cat, $dir)
	{
		if (!isset(self::$path[$cat])) throw new \Exception("Category $cat not found");
		$path_list = self::$path[$cat];

		foreach ($path_list as $path) {
			if (is_dir($path . DS . $dir)) {
				return $path . DS . $dir . DS;
			} else {
				//echo "<div>NOT: " . $path . DS . $dir . "</div>";
			}
		}
		return false;

	}


	static function in($cat, $file, $debug = false)
	{
		// Looking for a potential error.
		Debug::clear();

		if (!$file) throw new \Exception("Looking for Empty File? (category = $cat)");
		if (!isset(self::$path[$cat])) throw new \Exception("Category $cat not found");
		$path_list = self::$path[$cat];
		foreach ($path_list as $path) {
			$db = "Looking for: " . $path . $file;
			Debug::add($path . DS . $file);
			if ($debug) echo "Looking for: " . $path . $file . "<br/>";
			if (file_exists($path . DS . $file)) {
				if ($debug) echo "FOUND: " . $path . DS . $file . "<br/>";
				return $path . DS . $file;
			}
		}

		return false;

	}

	static function get_paths($cat)
	{
		return self::$path[$cat];
	}


}