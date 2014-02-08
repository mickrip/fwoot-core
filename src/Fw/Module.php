<?php

namespace Fw;

class Module
{

	var $name;
	static $_modulestack = array();

	static function load($module_name)
	{

		if (!$path = Find::dir_in("modules", $module_name)) throw new \Exception("Module '$module_name' not found");

		if (file_exists($path . "init.php")) {
			include($path . "init.php");
		}

		// add to autoloader
		Autoload::add_path($path . "classes");

		// add module instance to stack
		$class_name = "\\Fw\\Module\\" . ucfirst($module_name);

		if (class_exists($class_name)) {
			self::register(new $class_name());
		} else {
			throw new \Exception("Module Class '$class_name' not found");
		}
	}

	static function register(\Fw\Module $classObj)
	{
		self::$_modulestack[] = $classObj;
	}


	// Hooks
	static function run_begin()
	{
		foreach (self::$_modulestack as $class) {
			$class->begin();
		}
	}

	static function run_end()
	{
		foreach (self::$_modulestack as $class) {
			$class->end();
		}
	}

	static function run_routes()
	{
		foreach (self::$_modulestack as $class) {
			$class->routes();
		}
	}

	static function run_paths()
	{
		foreach (self::$_modulestack as $class) {
			$class->paths();
		}
	}


	// Interfaces (for simplicity)
	function begin() { }

	function end() { }

	function routes() { }

	function paths() { }

}