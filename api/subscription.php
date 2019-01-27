<?php

/**
 * Class subscription
 */
class subscription extends master
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
    public function add()
    {
        $host = filter_var($this->params[0], FILTER_SANITIZE_STRING);
        $key = $this->key;
        $params = [$host, $key];
        $sql = "INSERT INTO " . $this->db->sub_table . " (host_account, subscriber) VALUES ((SELECT email FROM " . $this->db->user_table . " WHERE display_name = $1 LIMIT 1), (SELECT email FROM " . $this->db->user_table . " WHERE api_key = $2 LIMIT 1))";
		return @!pg_query_params($this->db->link, $sql, $params) ? false : true;
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $host = filter_var($this->params[0], FILTER_SANITIZE_STRING);
        $key = $this->key;
        $params = [$host, $key];
        $sql = "DELETE FROM " . $this->db->sub_table . " WHERE host_account = (SELECT email FROM " . $this->db->user_table . " WHERE display_name = $1 LIMIT 1) AND subscriber = (SELECT email FROM " . $this->db->user_table . " WHERE api_key = $2 LIMIT 1)";
		return @!pg_query_params($this->db->link, $sql, $params) ? false : true;
    }

    /**
     * @return string
     */
    public function _list()
    {
        // verify we're checking an actual channel
        // ** TODO - Make this more robust for a sub management page
        $params = [$this->key];
        $sql = "SELECT " . $this->db->user_table . ".email, " . $this->db->sub_table . ".host_account, " . $this->db->sub_table . ".subscriber FROM " . $this->db->user_table . " JOIN " . $this->db->sub_table . " ON " . $this->db->sub_table . ".host_account = " . $this->db->user_table . ".email OR " . $this->db->sub_table . ".subscriber = " . $this->db->user_table . ".email WHERE api_key = $1";
        $result = pg_query_params($this->db->link, $sql, $params);
        $subscribed = [];
        $subscribers = [];
        while ($row = pg_fetch_assoc($result)) {
            if ($row['host_account'] === $row['email']) {
                $subscribers[] = $row['subscriber'];
            }
            if ($row['subscriber'] === $row['email']) {
                $subscribed[] = $row['host_account'];
            }
        }
        $json = [
            'subscribed' => $subscribed,
            'subscribers' => $subscribers
        ];
        return json_encode($json);
    }

}
