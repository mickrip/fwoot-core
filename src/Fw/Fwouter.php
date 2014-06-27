<?php

namespace Fw;


class Fwouter
{

	static $error = "";
	var $routes = array();

	function __construct($request_method, $params = array())
	{

		//$params = array_merge($params, array("" => "index"));
		$this->match($request_method, $params);
	}

	static function init()
	{

		// Set up defaults
		$default_class = "Index";
		$base_namespace = "\\Router\\";

		// get request method
		$request_method = $_SERVER['REQUEST_METHOD'];

		// set request Url if it isn't passed as parameter
		$request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/' . $default_class;

		if ($request_url == "/") $request_url = "/" . $default_class;
		// filter out query strings
		if (($strpos = strpos($request_url, '?')) !== false) {
			$request_url = substr($request_url, 0, $strpos);
		}

		// split up into array
		$params = array_filter(explode("/", $request_url));

		// turn all elements into ucfirst
		array_walk(
			$params,
			function (&$value, $index) {
				$value = ucfirst($value);
			}
		);

		// Locate Router Class
		$start = sizeof($params);
		$debug = "";


		do {
			$class_name = $base_namespace . implode("\\", array_slice($params, 0, $start));
			$debug = $debug . $class_name . ", \n";
			$start--;
		} while (!class_exists($class_name) && $start != 0);

		// Check if method in the default class (ie. /about/)
		if (!class_exists($class_name)) {
			// then look for the index route, and throw it into that.
			if (class_exists($base_namespace . $default_class)) {
				$class_name = $base_namespace . $default_class;
				$rest_of_url = strtolower(implode("/", $params));
				return new $class_name($request_method, $rest_of_url);
			} else {
				self::$error = "Unable to find router handler class. Looked for: $debug\n";
				return false;
			}
		} else {
			$rest_of_url = strtolower(implode("/", array_slice($params, $start + 1)));
			return new $class_name($request_method, $rest_of_url);

		}


	}

	function match($request_method, $params)
	{
		//echo "matching ($params)";

		$tokens = array(
			':string' => '([a-zA-Z]+)',
			':number' => '([0-9]+)',
			':alpha' => '([a-zA-Z0-9-_]+)'
		);

		foreach ((array)$this->routes as $route => $target) {
			$route = trim($route, "/");
			//echo "<div > $route - > $target </div > ";
			$pattern = strtr($route, $tokens);
			//echo 'LOOKING FOR: #^/?' . $pattern . '/?$# .. ' . "in $params";
			if (preg_match('#^/?' . $pattern . '/?$#', $params, $matches)) {
				//echo " <h2>found $target </h2 > ";
				$matches = array_slice($matches, 1);
				$method_name = strtolower($request_method) . "_" . $target;
				if (method_exists($this, $method_name)) {
					call_user_func_array(array($this, strtolower($request_method) . "_" . $target), $matches);
					return true;
				} else {
					self::$error = "Constructor route found, but method '$method_name' doesn't exist in '" . get_class($this) . "'";
					return false;
				}
				break;
			}

		}

		self::$error = "Could not match constructor route in '" . get_class($this) . "'";
		return false;


	}

	function request($key)
	{
		if (isset($_POST[$key])) return $_POST[$key];
		if (isset($_GET[$key])) return $_GET[$key];
		return false;
	}

	function json($arr)
	{
		header('Content-Type: application/json');
		echo json_encode($arr);
	}


}
