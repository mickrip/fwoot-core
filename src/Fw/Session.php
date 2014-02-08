<?php
namespace Fw;

class Session
{
	static $session;
	static $userobj = false;

	static function init()
	{
		if (!self::$session) {
			self::$session = new \Symfony\Component\HttpFoundation\Session\Session();
			self::$session->start();
		}
	}

	static function set($var, $val)
	{
		self::init();
		self::$session->set($var, $val);
	}

	static function get($var)
	{
		self::init();
		return self::$session->get($var);
	}

	static function end()
	{
		self::init();
		self::$session->clear();
	}




}