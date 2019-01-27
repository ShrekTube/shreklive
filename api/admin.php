<?php

/**
 * Class subscription
 */
class admin extends master
{

	public $db;

	/**
	 * subscription constructor.
	 * @param $key
	 * @param $params
	 */
	function __construct($key, $params)
	{
		parent::__construct($key, $params);
		$this->db = new database();
	}

	/**
	 * @return bool
	 */
	public function update()
	{
		$user = filter_var($this->params['email'], FILTER_SANITIZE_STRING);
		$column = filter_var($this->params['column'], FILTER_SANITIZE_STRING);
		$value = filter_var($this->params['value'], FILTER_SANITIZE_STRING);
		$key = $this->key;
		$ac_params = [$key];
		$ac_sql = "SELECT is_admin FROM " . $this->db->user_table . " WHERE api_key = $1";
		$ac_result = pg_fetch_object(pg_query_params($this->db->link, $ac_sql, $ac_params));
		if ($ac_result->is_admin === 't') {
			$sql = "UPDATE " . $this->db->user_table . " SET $column = $2 WHERE email = $1";
			$params = [$user, $value];
			return @!pg_query_params($this->db->link, $sql, $params) ? true : false;
		} else {
			return "You ain't no admin, son!";
		}
	}

}
