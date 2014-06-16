<?php
namespace Fw\Twig;

/*
This object is available to all twig templates (as fw.)
The methods within this object are specific to the fwoot framework.
ie. Finding Paths, Config, etc.
*/

class Extend
{

	// Force CSS cache to get a new version
	var $asset_version = "";

	function config($get)
	{
		return \Fw\Config::get($get);
	}

	// Force CSS cache to get a new version
	function set_asset_version($version_number)
	{
		$this->asset_version = "?" . $version_number;
	}


	function find($in, $file)
	{
		$real_path = \Fw\Find::in($in, $file);
		$so = strlen(\Fw\Config::get("docroot")) - 1;
		return substr($real_path, $so);
	}

	function asset($file, $from_prefix_config = "cdn")
	{
		if ($this->asset_version) {
			return \Fw\Bundler::find($file, $from_prefix_config) . $this->asset_version;
		} else {
			return \Fw\Bundler::find($file, $from_prefix_config);
		}
	}

	function bundle($modules)
	{
		foreach (explode(",", $modules) as $module) {
			\Fw\Bundler::add($module);
		}
	}

	function inc($bundles)
	{

		$ret = array();
		$html = "";
		foreach (explode(",", $bundles) as $bundle) {

			$temp = explode(".", $bundle);
			$bundle = trim($temp[0]);
			$category = trim($temp[1]);

			$ret = array_merge($ret, \Fw\Bundler::get_bundle_by_category($bundle, $category));
		}

		// Really only should be a single category
		switch ($category) {
			case "js":
				foreach ($ret as $asset) {
					$html .= '<script src="' . $asset . $this->asset_version . '"></script>';
				}
				break;
			case "css":
				foreach ($ret as $asset) {
					$html .= '<link href="' . $asset . $this->asset_version . '" rel="stylesheet" media="screen">';
				}
				break;
		}
		return $html;
	}

	function getbundle($category)
	{
		//ladybug_dump(\Fw\Bundler::$stack);
		if (!isset(\Fw\Bundler::$stack[$category])) {
			return array();
		} else {
			return \Fw\Bundler::$stack[$category];
		}
	}

	function debug()
	{
		ladybug_dump(\Fw\Bundler::$stack);
	}
}
