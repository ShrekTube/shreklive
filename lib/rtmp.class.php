<?php

/*
 * ---------------------------------------------------------------
 * RTMP functions
 * ---------------------------------------------------------------
 *
 * This page contains the functions for showing, updating, checking,
 * changing, etc. the RTMP streams themselves. Currently many of
 * these functions live in the MediaInfo.class.php and RTMP.class.php,
 * but they will eventually all be rewritten and moved here.
 * 
 * TODO:
 *
 *     -Fix stream functions, they suck
 *     -General code clean up
 *
 */

/**
 * Class rtmp
 */
class rtmp extends database {

	public $rtmpinfo = [];

	/* 	Streamkey Auth Functions	 */

	// Check if the stream key is valid
	/**
	 * @param $key
	 * @param $name
	 * @param $file
	 * @return bool
	 */
	public function stream_check($key, $name, $file) {
		$params = [$name, $key];
		$sql = "SELECT * FROM $this->user_table WHERE display_name = $1 AND stream_key = $2 AND stream_key IS NOT null";
		$query = pg_query_params($this->link, $sql, $params);
		$row_cnt = pg_num_rows($query);
		if ($row_cnt >= 1) {
			$current = "Result: Valid stream key!\r\n";
			file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
			return true;
		} else {
			$current = "Result: Invalid stream key!\r\n";
			file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
			return false;
		}
	}

	/**
	 * @param $key
	 * @param $name
	 * @param $stream_title
	 * @param $furl
	 * @param $logfile
	 * @param $from_email
	 * @param $reply_email
	 */
	public function onLive($key, $name, $stream_title, $furl, $logfile, $from_email, $reply_email) {
		$params = [$key];
		$sql = "SELECT subscriber FROM $this->sub_table WHERE host_account = (SELECT email FROM $this->user_table WHERE stream_key = $1 LIMIT 1)";
		$result = pg_query_params($this->link, $sql, $params);
		$timestamp = date("F j, g:i a");
		$timestamp = $name . " went live on $timestamp\r\n";
		$write = $timestamp . 'Notified: ';
		while ($row = pg_fetch_assoc($result)) {
			$subject = $GLOBALS['sitetitle'] . ' - ' . $name . ' went live!';
            $message = $timestamp . "<br /><br />Stream Title: $stream_title<br /><br />Watch here: <a href='$furl/watch/$name'>$furl/watch/$name</a>";
			$headers = [];
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers[] = "From: $from_email";
			$headers[] = "Reply-To: $reply_email";
			$headers[] = 'X-Mailer: PHP/' . phpversion();
			mail($row['subscriber'], $subject, $message, implode("\r\n", $headers));
			$write .= $row['subscriber'] . ',';
		}
		$write .= "\r\n";
		file_put_contents($logfile . "on-live_notice.log", $write, FILE_APPEND | LOCK_EX);
	}

	/* Stream stuff */

	// Not happy with this, but it works.
	/**
	 * @param bool $forceCheck
	 * @return array
	 */
	public function checkStreams($forceCheck = true) {
		if (!isset($this->rtmpinfo["rtmp"])) {
			$this->rtmpinfo["rtmp"] = [
				"lastUpdate" => 0,
				"channels" => []
			];
		}

		if ($forceCheck || time() - $this->rtmpinfo["rtmp"]["lastUpdate"] > 5) {
			$this->fetchChannels();
		}
		return $this->rtmpinfo;
	}

	/**
	 * @return array
	 */
	private function fetchChannels() {
		$this->rtmpinfo["rtmp"]["lastUpdate"] = time();
		$this->rtmpinfo["rtmp"]["channels"] = [];
		$surl = $_SERVER['HTTP_HOST'];
		$rtmp = json_decode(json_encode((array) simplexml_load_file($GLOBALS['furl'] . '/stat.xml')), TRUE);
		$live = null;
		foreach ($rtmp["server"]["application"] as $i => $application) {
			if (is_numeric($i) && $application["name"] === "live") {
				$live = $application;
			} else if (!is_numeric($i)) {
				$live = $rtmp["server"]["application"];
			}
		}
		//echo '<pre>';print_r($live);echo '</pre>';
		if (!empty($live["live"]["stream"])) {
			if (array_key_exists("name", $live["live"]["stream"])) {
				$channel = $live["live"]["stream"];

				if (empty($channel["name"])) {
					$channel["name"] = "default";
				}
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]] = $channel;
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["stream"] = 'rtmp://' . $surl . '/live/' . $channel["name"];
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["URL"] = $GLOBALS["furl"] . '/watch/' . $channel["name"];
			} else {
				foreach ($live["live"]["stream"] as $key => $channel) {
					if (empty($channel["name"])) {
						$channel["name"] = "default";
					}
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]] = $channel;
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["stream"] = 'rtmp://' . $surl . '/live/' . $channel["name"];
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["URL"] = $GLOBALS["furl"] . '/watch/' . $channel["name"];
				}
			}
		}
		return $this->rtmpinfo;
	}

	/**
	 * @param $channelName
	 * @return bool
	 */
	private static function isRecordingChannel($channelName) {
		return (count(glob("/var/rachni/rec/" . $channelName . "-*.flv")) > 0);
	}

}
