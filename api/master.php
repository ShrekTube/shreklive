<?php

/**
 * Class master
 */
class master
{
    public $params;
    public $key;
    public $surl;
    public $furl;

    /**
     * master constructor.
     * @param $key
     * @param $params
     */
    public function __construct($key, $params)
    {
        $protocol = $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $this->surl = $_SERVER['HTTP_HOST'];
        $this->furl = $protocol . $this->surl;
        $this->params = $params;
        $this->key = $key;
    }

    /**
     * @param $string
     * @return bool
     */
    public function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        $first = substr($string, 0, 1);
        $last = substr($string, -1);
        if (($first === '{' && $last === '}') || ($first === '[' && $last === ']')) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        return false;
    }
}