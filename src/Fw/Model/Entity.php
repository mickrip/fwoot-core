<?php
namespace Fw\Model;

use \Fw\Db as Db;

abstract class Entity
{
	var $_id = "id";
	var $_pk = "";
	var $_field_list = array();
	static $_table_cache = array();

	function get_fields()
	{

		if (sizeof($this->_field_list)) {
			return;
		}

		if (isset(self::$_table_cache[$this->_table]["field_list"])) {
			$this->_pk = self::$_table_cache[$this->_table]["pk"];
			$this->_field_list = self::$_table_cache[$this->_table]["field_list"];
			return;
		}

		$q = Db::prepare("SHOW COLUMNS FROM " . $this->_table);
		$q->execute();
		$res = $q->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($res as $row) {
			$this->_field_list[] = $row["Field"];
			if ($row["Key"] == "PRI") {
				$this->_pk = $row["Field"];
			}
		}


		if (!$this->_pk) {
			throw new \Fw\Exception("Trying to access a table with a primary key? Table = " . $this->_table);
		}

		// Write Cache
		self::$_table_cache[$this->_table]["field_list"] = $this->_field_list;
		self::$_table_cache[$this->_table]["pk"] = $this->_pk;

		/*
		echo "<pre>";
		var_dump($res);
		echo "</pre>";

		echo "<pre>";
		var_dump($this->_field_list);
		echo "</pre>";
		die();
		*/
	}

	// Alias
	function select_fields()
	{
		return $this->to_array();
	}

	function to_array()
	{
		$collect = array();
		$args = func_get_args();
		foreach ($args as $arg) {
			if (isset($this->$arg)) {
				$collect[$arg] = $this->$arg;
			}
			if (method_exists($this, $arg)) {
				$collect[$arg] = call_user_func(array($this, $arg));
			}
		}

		return $collect;

	}


	function fetchAll($query, $arr = array())
	{
		$q = \Fw\Db::prepare($query);
		$q->execute($arr);
		return $q->fetchAll(\PDO::FETCH_CLASS, $this->_entity);

	}

	function fetchOne($query, $arr = array())
	{
		$q = \Fw\Db::prepare($query);
		$q->execute($arr);
		return $q->fetchObject($this->_entity);

	}

	function q($query, $arr = array())
	{
		$q = Db::prepare($query);
		$q->execute($arr);
	}


	function create($debug = '')
	{
		$this->get_fields();
		$arr = array();
		$query2 = "";
		$query = "insert into " . $this->_table . " (";
		foreach ($this->_field_list as $v) {
			if (isset($this->{$v})) {
				$query .= "$v,";
				$query2 .= "?,";
				$arr[] = $this->{$v};
			}
		}
		$query2 = substr($query2, 0, -1);
		$query = substr($query, 0, -1) . ") values ($query2)";
		if ($debug) die("Q: " . $query);
		$p = Db::prepare($query);

		$p->execute($arr);
		return Db::lastInsertId();
	}

	function retrieve($id)
	{
	}

	function update()
	{

		$this->get_fields();
		$arr = array();
		$query = "update " . $this->_table . " set ";
		foreach ($this->_field_list as $v) {
			$query .= "$v = ?,";
			$arr[] = $this->{$v};
		}
		$query = substr($query, 0, -1);
		$query = $query . " where " . $this->_pk . " = " . $this->{$this->_pk};
		$p = Db::prepare($query);
		$p->execute($arr);


	}

	function delete()
	{
	}

}