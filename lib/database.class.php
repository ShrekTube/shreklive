<?php

/* * *****************   *
 *    /lib/database.class.php    *
 *    ******************   */


/* NOTE: This site uses PostgreSQL. If you want to use MySQL you will need to change all the DB queries. */

class database {

	// Connection params
	private $host = 'ec2-107-22-238-186.compute-1.amazonaws.com';
	private $port = '5432';
	private $user = 'tsddbxspfpwajb';
	private $password = 'dc150d7d826eb0a3675e7346b986b5ab10408e5e4dbfc41f45d6efd1bb207140';
	private $dbname = 'd2h9ru23534k8p';
	
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
