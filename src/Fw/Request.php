<?php
namespace Fw;

use \Symfony\Component\HttpFoundation;

class Request
{
	private static $link = null;

	private static function init()
	{
		self::$link = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
	}

	static function headers()
	{
		self::init();
		return self::$link->headers;
	}

	static function useragent()
	{
		self::init();
		return self::$link->headers->get("user-agent");
	}

}


