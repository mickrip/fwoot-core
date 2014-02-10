<?php
namespace Fw;

class Framework
{
	static function init()
	{

		define("DS", DIRECTORY_SEPARATOR);

		// Constants
		define('DOCROOT', dirname($_SERVER["SCRIPT_FILENAME"]) . DS);

		// Derive Some Config variables from URL
		$_base = function () {
			$_b = explode(DS, $_SERVER["SCRIPT_NAME"]);
			return substr($_SERVER["SCRIPT_NAME"], 0, strlen($_b[sizeof(explode(DS, $_SERVER["SCRIPT_NAME"])) - 1]) * -1);
		};
		\Fw\Config::set("base", $_base());
		$_SERVER["HTTPS"] = (isset($_SERVER["HTTPS"])) ? $_SERVER["HTTPS"] : "off";

		if (isset($_SERVER["SERVER_NAME"])) {
			\Fw\Config::set("domain", (@$_SERVER["HTTPS"] == "on") ? "https://" . $_SERVER["SERVER_NAME"] : "http://" . $_SERVER["SERVER_NAME"]);
		}else{
			\Fw\Config::set("domain","");
		}

		// Set Application Directory to server path
		if (!Config::exists("apppath")) {
			$app_path = DOCROOT;
		} else {
			$app_path = Config::get("apppath");
		}

		// Autoload Application Class
		\Fw\Autoload::add_path($app_path . "classes");

		// PHP Composer Autoloader
		if (file_exists($app_path . "/vendor/autoload.php")) {
			include($app_path . "/vendor/autoload.php");
		}

		// Please comment this properly because it could be obsolete
		//\Fw\Find::add_path_to("classes", $app_path . "classes" . DS);

		// Configuration
		\Fw\Config::set("docroot", DOCROOT); // doc root
		\Fw\Config::set("apppath", $app_path); // application path

		// Finder Paths
		Find::add_path_to("controllers", \Fw\Config::get("apppath") . "controllers");
		Find::add_path_to("views", Config::get("apppath") . "views");
		Find::add_path_to("assets", Config::get("apppath") . "assets");
		Find::add_path_to("assets", Config::get("apppath") . "bower_components");
		Find::add_path_to("modules", Config::get("apppath") . "modules");


	}

}