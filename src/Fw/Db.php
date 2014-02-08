<?php

namespace Fw;

/**
 * Database PDO Utility Class.
 *
 * Class Db
 * @package Fw
 */

class Db
{
	private static $link = null;
	public static $pdo;

	private static function getLink()
	{
		if (self :: $link) {
			return self :: $link;
		}

		$creds = Config::load("db.php");

		$DB_HOST = $creds["host"];
		$DB_PASS = $creds["pass"];
		$DB_USER = $creds["user"];
		$DB_DATABASE = $creds["database"];

		self::$link = new \PDO("mysql:host=$DB_HOST;dbname=$DB_DATABASE;charset=utf8", $DB_USER, $DB_PASS);

		self::$link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		self::$link->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

		self::$pdo = self::$link;

		return self::$link;
	}

	public static function __callStatic($name, $args)
	{
		$callback = array(self :: getLink(), $name);
		return call_user_func_array($callback, $args);
	}

	public static function init()
	{
		return self::getLink();
	}
}


