<?php
namespace Fw;

/**
 * Fw00t Asset manager
 *
 * @package Fw
 *
 */

class Bundler
{
	static $bundles;
	static $stack = array();

	/**
	 * Searches for asset based on the asset resources defined in \Fw\Find
	 *
	 * @param $file
	 * @return string
	 * @throws \Exception
	 */

	static function find($file, $prefix_cdn_config = "cdn")
	{
		if (!$real_path = \Fw\Find::in("assets", $file)) throw new \Exception("$file not found");
		$so = strlen(\Fw\Config::get("docroot")) - 1;
		return \Fw\Config::get($prefix_cdn_config) . substr($real_path, $so);
	}

	/**
	 * Add bundle resource defined in config/bundler.php
	 *
	 * @param $bundle
	 * @throws \Exception
	 */

	static function add($bundle)
	{
		// load bundle config file
		if (!self::$bundles) self::$bundles = Config::load("bundler.php");

		// check if bundle exists
		if (!isset(self::$bundles[$bundle])) {
			throw new \Exception("Bundle ($bundle) not found in the config file");
		}

		foreach (self::$bundles[$bundle] as $category => $filenames) {
			$filenames = explode(",", $filenames);
			foreach ($filenames as $filename) {
				//echo "$category -> $filename <br>";
				self::$stack[$category][] = self::find($filename);
			}
		}

	}

	/**
	 *
	 *
	 * @param $bundle
	 * @param $category
	 * @return array
	 * @throws \Exception
	 */
	static function get_bundle_by_category($bundle, $category)
	{
		// load bundle config file
		if (!self::$bundles) self::$bundles = Config::load("bundler.php");

		// check if bundle exists
		if (!isset(self::$bundles[$bundle])) {
			throw new \Exception("Bundle ($bundle) not found in the config file");
		}

		$ret = array();

		foreach (self::$bundles[$bundle] as $_cat => $filenames) {
			$filenames = explode(",", $filenames);
			foreach ($filenames as $filename) {
				if ($_cat == $category) {
					// checks if there's a "cdn" key in this partic. bundle declaration
					// If there is, then prefix it with whatever the config is.
					if (isset(self::$bundles[$bundle]["cdn"])) {
						$ret[] = self::find($filename, self::$bundles[$bundle]["cdn"]);
					} else $ret[] = self::find($filename);
				}
			}
		}
		return $ret;
	}

	/**
	 * @param $category
	 * @return mixed
	 * @throws \Exception
	 */
	static function get($category)
	{
		//check if category exists
		if (!isset(self::$stack[$category])) {
			throw new \Exception("Category ($category) not found.");
		}
		return self::$stack[$category];
	}

}
