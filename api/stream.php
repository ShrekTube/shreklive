<?php

/**
 * Class stream
 */
class stream extends master
{
	public $rtmp;
	public $rtmpinfo;

	/**
	 * stream constructor.
	 * @param $key
	 * @param $params
	 */
	function __construct($key, $params)
	{
		parent::__construct($key, $params);

		// Check stream information
		$GLOBALS['furl'] = $this->furl;
		$this->rtmp = new rtmp();
		$this->rtmpinfo = $this->rtmp->checkStreams();
	}

	/**
	 * @return array
	 */
	public function info()
	{
		return $this->rtmpinfo;
	}

	/**
	 * @return array
	 */
	public function ping()
	{
		if (!empty($this->params[0])) {
			$channel = $this->params[0];
			if (!empty($this->rtmpinfo['rtmp']['channels'][$channel])) {
				$json = [
					'active' => true,
					'recording' => $this->rtmpinfo['rtmp']['channels'][$channel]['recording'],
					'stream' => $this->rtmpinfo['rtmp']['channels'][$channel]['stream'],
					'URL' => $this->rtmpinfo['rtmp']['channels'][$channel]['URL']
				];
			} else {
				$json = [
					'active' => false,
					'recording' => false,
				];
			}
		} else {
			$json = [];
			foreach ($this->rtmpinfo['rtmp']['channels'] as $channel) {
				$json[$channel['name']] = [
					'active' => true,
					'recording' => $channel['recording'],
					'stream' => $this->rtmpinfo['rtmp']['channels'][$channel["name"]]['stream'],
					'URL' => $this->rtmpinfo['rtmp']['channels'][$channel["name"]]['URL']
				];
			}
		}
		return $json;
	}

	/**
	 * @return mixed
	 */
	public function record_start()
	{
		$channel = $this->params[0];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "$this->furl/control/record/start?app=live&name=" . $channel . "&rec=rec");
		$json = curl_exec($ch);
		curl_close($ch);
		return $json;
	}

	/**
	 * @return mixed
	 */
	public function record_stop()
	{
		$channel = $this->params[0];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "$this->furl/control/record/stop?app=live&name=" . $channel . "&rec=rec");
		$json = curl_exec($ch);
		curl_close($ch);
		return $json;
	}
}
