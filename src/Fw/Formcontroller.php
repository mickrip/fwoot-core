<?php
namespace Fw;

use HybridLogic\Validation\Validator;
use HybridLogic\Validation\Rule;

/**
 * Controller class with Ajax request helpers.
 *
 * @package Fw
 */

abstract class Formcontroller extends Controller
{

	var $validator;

	function __construct($params = array())
	{
		parent::__construct($params);
		$this->validator = new Validator();
		$this->form_rules();
	}

	protected function success()
	{
		$arr = array("success" => 1);
		$this->response->setContent(json_encode($arr));
		$this->response->headers->set('Content-Type', 'application/json');
		die();
	}

	protected function fail($err, $return_as_array = false)
	{
		if (is_array($err)) {
			$err = array_values($err);
		} else {
			if ($return_as_array) $err = array($err);
		}


		$arr = array("message" => $err);
		$this->response->setContent(json_encode($arr));
		$this->response->headers->set('Content-Type', 'application/json');
		die();
	}

	protected function is_valid()
	{
		if ($this->validator->is_valid($this->request->request->all())) {
			return $this->validator->get_data();
		} else {
			return false;
		}
	}

}