<?php

namespace Fw;

class Json
{
	var $stack = array();
	var $count = 0;

	function add($data, $val = '')
	{
		if ($val) {
			$this->stack[$val] = $data;
		} else {
			$this->stack[$this->count] = $data;
			$this->count++;
		}
		return $this;
	}

	function render()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-Type: application/json');
		echo json_encode($this->stack);
		die();
	}


}