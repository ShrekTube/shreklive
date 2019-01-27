<?php

/* * *****************   *
 *    /lib/database.class.php    *
 *    ******************   */


/* NOTE: This site uses PostgreSQL. If you want to use MySQL you will need to change all the DB queries. */

class database {

	// Connection params
	private $host = 'localhost';
	private $port = '5432';
	private $user = '';
	private $password = '';
	private $dbname = '';
	
	// This can be accessed by the database class and any class that extends database by using $this->link
	public $link;
	
	// set up our table names
	public $user_table = 'users';
	public $sub_table = 'subscribers';
	public $chat_table = 'chat';

	public function __construct() {

	// Open database connection
		$this->link = pg_connect("host=$this->host port=$this->port dbname=$this->dbname user=$this->user password=$this->password");
		if (!$this->link) {
			$message = "A connection error occured. ";
			$message .= pg_connection_status($this->link);
			throw new Exception($message);
		}
	}
}
