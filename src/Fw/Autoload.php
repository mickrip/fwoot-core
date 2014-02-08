<?php
namespace Fw;

/**
 * Simple autoloader functionality
 *
 * @package Fw
 */

class Autoload
{

	static $debug = false;
	static $consider_count = 0;

	/**
	 * Adds path to the autoload register
	 *
	 * Example:
	 * \Fw\Autoload::add_path("/var/www/website/classes");
	 *
	 * @param $prefix
	 */
	static function add_path($prefix)
	{
		spl_autoload_register(

			function ($cn) use ($prefix) {
				$className = ltrim($cn, '\\');
				$fileName = '';
				if ($lastNsPos = strrpos($className, '\\')) {
					$namespace = substr($className, 0, $lastNsPos);
					$className = substr($className, $lastNsPos + 1);
					$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
				}
				$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
				$fileName = $prefix . DIRECTORY_SEPARATOR . $fileName;
				if (self::$debug) echo "<div>AUTOLOAD : Considering ($fileName)</div>";
				self::$consider_count++;
				if (self::$consider_count > 50) die("Consider Count Exceeded");
				if (file_exists($fileName)) {
					if (self::$debug) echo "<div>AUTOLOAD: Returning $fileName </div>";
					require $fileName;
					return;
				}
			}
		);
	}
}