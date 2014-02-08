<?php
namespace Fw\Model;

/**
 * Fw00t Model Collection Class. Methods to return an array of entities.
 *
 * @package Fw00t
 */
abstract class Collection
{
	var $_table = "";
	var $_entity = "";
	static $_cache = array();
	static $_instance;

	/**
	 * Contructor
	 */
	public function __construct()
	{
		\Fw\Db::init();
	}

	/**
	 * @return mixed
	 */
	public static function instance()
	{
		$class = get_called_class();
		return new $class();
	}

	/**
	 * Get by ID Helper. Benefits from caching
	 *
	 * @param $id
	 * @param string $ammend_query
	 * @return mixed
	 */
	function get_by_id($id, $ammend_query = '')
	{
		if (isset(self::$_cache[$this->_table][$id])) {
			//var_dump(self::$_cache[$this->_table][$id]);
			return self::$_cache[$this->_table][$id];
		}

		$q = \Fw\Db::prepare("select * from " . $this->_table . " where id=? " . $ammend_query);
		$q->execute(array($id));
		$res = $q->fetchObject($this->_entity);
		self::$_cache[$this->_table][$id] = $res;

		return $res;
	}


	/**
	 * Get by Arbitary Field
	 *
	 * @param $field
	 * @param $value
	 * @return mixed
	 */
	function get_by($field, $value)
	{
		$q = \Fw\Db::prepare("select * from " . $this->_table . " where " . $field . "=?");
		$q->execute(array($value));
		return $q->fetchAll(\PDO::FETCH_CLASS, $this->_entity);
	}

	/**
	 *
	 * @param $query
	 * @param array $arr
	 * @return mixed
	 */

	function fetchAll($query, $arr = array())
	{
		$q = \Fw\Db::prepare($query);
		$q->execute($arr);
		return $q->fetchAll(\PDO::FETCH_CLASS, $this->_entity);

	}

	/**
	 * @param $query
	 * @param array $arr
	 * @return mixed
	 */

	function fetchOne($query, $arr = array())
	{
		$q = \Fw\Db::prepare($query);
		$q->execute($arr);
		return $q->fetchObject($this->_entity);

	}

	/**
	 *
	 */

	function clear_cache()
	{
		unset(self::$_cache[$this->_table]);
	}


	/**
	 * @param $arr
	 *
	 */

	function dump($arr)
	{
		echo "<table border='1' style='font-family:arial;font-size:10px'>";
		foreach ($arr as $row) {
			echo "<tr>";
			foreach ($row as $field => $val) {

				if (strlen($val) > 50) {
					$val = substr($val, 0, 50) . "...";
				}

				echo "<td style='vertical-align: top'><b>$field</b><br/> $val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}

}