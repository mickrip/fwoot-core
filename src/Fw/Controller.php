<?php
namespace Fw;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session as Sesh;

/**
 * Controller class. Serves a dual purpose.
 *
 * 1. You can load a controller from the bootstrap file via the static load method
 * 2. Provides controller utility methods. Request object, etc.
 *
 * @package Fw
 */
class Controller
{

	static $current;
	var $params = array();

	function __construct($params = array())
	{

		$this->request = Request::createFromGlobals();
		$this->post = & $this->request->request; // stupid name
		$this->response = new Response();
		self::$current = $this;

		// artificially inject $request into the request object and params
		foreach ($params as $key => $val) {
			$this->request->request->set($key, $val);
			$this->set_param($key, $val);
		}
	}


	/**
	 *
	 * Called if the Controller fails to load
	 *
	 * @param $msg
	 * @return bool
	 */

	private static function er($msg)
	{
		_404($msg);
		die();
	}

	/**
	 * Loads Controller
	 *
	 * Example:
	 * Controller::load($controller, $action));
	 *
	 * self::er($msg) is called if there is a problem
	 *
	 * @param $controller
	 * @param string $action
	 * @param array $params
	 * @return bool
	 */

	static function load($controller, $action = '', $params = array())
	{

		$class_name = ucfirst($controller);
		$method = $action;
		$tc = "Controller\\" . $class_name;
		if (!class_exists($tc)) return self::er("Class $tc does not exist.");
		$this_object = new $tc($params);


		if (!method_exists($this_object, $method)) {
			return self::er("Method $method in $tc does not exist.");
		}


		if (!is_callable(array($this_object, $method))) return self::er("Method $method in $tc is not callable.");
		if (is_callable(array($this_object, "__before"))) {
			call_user_func(array($this_object, "__before"));
		}


		call_user_func(array($this_object, $method));

		if (is_callable(array($this_object, "__after"))) {
			call_user_func(array($this_object, "__after"));
		}
		return $this_object;
	}

	/**
	 * Twig Helper. This maybe should be somewhere else ;)
	 *
	 * @param $template_file
	 * @param string $inject
	 */
	function twig($template_file, $inject = '')
	{
		$this->output(
		     \Fw\Twig::factory($template_file, $inject)
		             ->render()
		);
	}


	/**
	 * Outputs to screen. If param is an array, then json headers + data are generated.
	 *
	 * @param $o
	 */
	function output($o)
	{

		if (is_array($o)) {
			$this->response->setContent(json_encode($o));
			$this->response->headers->set('Content-Type', 'application/json');
			die();

		} else {
			$this->response->setContent($o);
		}
	}

	/**
	 * Called after controller has run.
	 */

	function __after() { }


	/**
	 * Magic destructor. Sends anything that needs to be sent.
	 */
	function __destruct()
	{
		$this->response->send();
	}

	/**
	 * Redirect Utility
	 *
	 * @param $url
	 */

	function redirect($url)
	{
		$response = new RedirectResponse($url);
		$response->send();
		die();
	}

	function set_param($key, $val)
	{
		$this->params[$key] = $val;
	}

	function get_param($key)
	{
		if (isset($this->params[$key])) return $this->params[$key];
	}

}