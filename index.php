<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// class includes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists('api/' . strtolower($class) . '.php')) {
			include 'api/' . strtolower($class) . '.php';
		} elseif (file_exists('lib/' . strtolower($class) . '.class.php')) {
			include 'lib/' . strtolower($class) . '.class.php';
		}
	}
});

// verify we're logged in
require 'inc/auth.php';

// Load RTMP channels informations
$rtmpclass = new rtmp();
$rtmpinfo = $rtmpclass->checkStreams();

// grab account info
$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);
$accountinfo = $user->info($email);

// functions to convert raw bytes/bits to something more readable for stream info
/**
 * @param $bites
 * @param int $decimals
 * @return string
 */
function bitsConvert($bites, $decimals = 2)
{
	$sz = 'BKMGTP';
	$factor = floor((strlen($bites) - 1) / 3);
	return sprintf("%.{$decimals}f", $bites / pow(1000, $factor)) . @$sz[$factor];
}

/**
 * @param $bytes
 * @param int $decimals
 * @return string
 */
function bytesConvert($bytes, $decimals = 2)
{
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// Check if we're trying to watch a video or stream
// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);

// Set current page and then
// check if we're trying to 
// watch a video or stream
$page = $uriVars[0];
if ($page === 'watch') {
	$streamkey = $uriVars[1];
	$subemail = $user->updateStreamkey($streamkey, 'email');
	// Set up data for checking subscription status
	$sub = new subscription($accountinfo['api_key'], $streamkey);
	$list = $sub->_list();
	$subarray = json_decode($list);
	if (in_array($subemail, $subarray->subscribed)) {
		$substatus = 'Unsubscribe';
		$subcolor = 'style="background-color: rgb(0, 188, 212)"';
	} else {
		$substatus = 'Subscribe';
		$subcolor = '';
	}
}
if ($page === 'video') {
	$video = $uriVars[1];
}
if ($page === 'download') {
	$download = filter_var($uriVars[1], FILTER_SANITIZE_STRING);
	$file = "/var/tmp/rec/" . $download;
	if (file_exists($file)) {
		$size = filesize("./" . basename($file));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $size);
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		readfile($file);
		exit();
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $sitetitle ?> Site<?php if (!empty($streamkey)) {
			echo ' - Watching ' . $user->updateStreamkey($streamkey, 'channel');
		} ?>
	</title>
	<link href="/img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,300,100,700,900' rel='stylesheet'
		  type='text/css'>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/js/vjs/6.2.6/video-js.min.css">
	<link rel="stylesheet" type="text/css" href="/js/vjs/video-js-skin.css">
	<link rel="stylesheet" href="/css/application.css">
	<link rel="stylesheet" href="/css/site.css">
	<link rel="stylesheet" href="/js/jqui/jquery-ui.min.css">
	<link rel="stylesheet" href="/js/sb/jquery.mCustomScrollbar.css">
	<script src="/js/jquery.min.js"></script>
	<script src="/js/jqui/jquery-ui.min.js"></script>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header"><!-- START LAYOUT WRAP -->
	<!-- START NAV HEADER -->
	<header class="mdl-layout__header">
		<div class="mdl-layout__header-row">
			<div class="mdl-layout-spacer"></div>

			<?php if (!empty($streamkey)) { ?>
				<a class="mdl-navigation__link force_link" onclick="popoutPlayer()">
					<i class="material-icons" role="presentation">video_label</i>
					Switch to Popout Player
				</a>
				<a class="mdl-navigation__link" id="toggleChat" href="#">
					<i class="material-icons" role="presentation">chat</i>
					Show/Hide Chat
				</a>
				<a class="mdl-navigation__link force_link" onclick="resetPlayer()">
					<i class="material-icons" role="presentation">video_label</i>
					Reset Player
				</a>
			<?php } ?>

			<div class="avatar-dropdown" id="icon">
				<span><?= $accountinfo['display_name'] ?></span>
				<img src="<?= $accountinfo['profile_img'] ?>">
			</div>

			<ul class="mdl-menu mdl-list mdl-menu--bottom-right mdl-js-menu mdl-shadow--2dp account-dropdown"
				for="icon">
				<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<img class="material-icons mdl-list__item-avatar"
									 src="<?= $accountinfo['profile_img'] ?>">
								<span><?= $accountinfo['display_name'] ?></span>
								<span class="mdl-list__item-sub-title"><?= $accountinfo['email'] ?></span>
							</span>
				</li>

				<li class="list__item--border-top"></li>

				<a href="#" class="mdl-menu__item mdl-list__item copyButton" id="copyButton"
				   title="Copy to clipboard"
				   data-clipboard-text="<?= $accountinfo['display_name'] ?>?key=<?= $accountinfo['stream_key']; ?>">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">vpn_key</i>
								<span>Stream Key</span>
								<i class="material-icons md-16 copyIcon">content_copy</i>
				</a>

				<li class="list__item--border-top"></li>

				<a href="/settings" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">settings</i>
								Settings
							</span>
				</a>
				<a href="?action=logout" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon text-color--secondary">exit_to_app</i>
								Log out
							</span>
				</a>
			</ul>
		</div>
	</header>
	<!-- END NAV HEADER -->

	<!-- START NAV DRAWER -->
	<div class="mdl-layout__drawer">
		<header class="dm-logo-header"><?= $sitetitle ?></header>
		<nav class="mdl-navigation">
			<a class="mdl-navigation__link<?php
			if ($page === 'channels' || (empty($page) && empty($streamkey))) {
				echo ' mdl-navigation__link--current';
			}
			?>" href="/channels">
				<i class="material-icons" role="presentation">videogame_asset</i>
				Live channels
			</a>
			<a class="mdl-navigation__link<?php
			if ($page === 'videos') {
				echo ' mdl-navigation__link--current';
			}
			?>" href="/videos">
				<i class="material-icons" role="presentation">video_library</i>
				Videos
			</a>
			<a class="mdl-navigation__link<?php
			if ($page === 'chat') {
				echo ' mdl-navigation__link--current';
			}
			?>" href="/chat">
				<i class="material-icons" role="presentation">chat</i>
				Global Chat
			</a>
			<?php if (!empty($streamkey)) { ?>
				<a class="mdl-navigation__link mdl-navigation__link--current mdl-typography--text-nowrap-ellipsis"
				   href="/watch/<?= $streamkey; ?>">
					<i class="material-icons" role="presentation">visibility</i>
					Watching: <?php echo $user->updateStreamkey($streamkey, 'channel'); ?>
				</a>

				<button id="subButton" class="mdl-button mdl-js-button mdl-button--raised" channel="<?= $streamkey; ?>"
						type="button" <?= $subcolor ?>><?= $substatus ?></button>
				<div id="subToast" class="mdl-js-snackbar mdl-snackbar">
					<div class="mdl-snackbar__text"></div>
					<button class="mdl-snackbar__action" type="button"></button>
				</div>
			<?php } else { ?>
				<a class="mdl-navigation__link" href="/">
					<i class="material-icons" role="presentation">visibility</i>
					Watching: N/A
				</a>
			<?php } ?>
			<div class="mdl-layout-spacer"></div>
			<a class="mdl-navigation__link" target="_blank" href="https://github.com/Fenrirthviti/">
				<i class="material-icons" role="presentation">link</i>
				GitHub
			</a>
		</nav>
	</div>
	<!-- END NAV DRAWER-->

	<!-- START CONTENT PAGE -->
	<main class="mdl-layout__content" id="mainContent">

		<?php
		// Load the appropriate page, but only if it exists
		// and checking first if trying to watch a stream or play a video
		if (!empty($streamkey)) {
			include 'inc/index/streamplayer.php';
		} elseif (!empty($video)) {
			include 'inc/index/videoplayer.php';
		} elseif (file_exists('inc/index/' . $page . '.php') === true) {
			include 'inc/index/' . $page . '.php';
		} elseif (empty($page) || ($page === 'index.php')) {
			include 'inc/index/channels.php';
		} else {
			include 'inc/404.php';
		}
		?>
		<div id="keyCopy" class="mdl-js-snackbar mdl-snackbar keyCopy-toast">
			<div class="mdl-snackbar__text keyCopy-text"></div>
			<button class="mdl-snackbar__action keyCopy-action" type="button"></button>
		</div>
	</main>
	<!-- END CONTENT PAGE-->

</div> <!-- END LAYOUT WRAP -->

<!-- START FOOTER -->
<script src="/js/date.format.min.js"></script>
<script src="/js/material.js"></script>
<script src="/js/getmdl-select.min.js"></script>
<script src="/js/sb/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="/js/vjs/6.2.6/video.min.js"></script>
<script src="/js/vjs/6.2.6/videojs-flash.min.js"></script>
<script src="/js/vjs/videojs-persistvolume.js"></script>
<script src="/js/vjs/videojs-contrib-hls.min.js"></script>
<script src="/js/clipboard.min.js"></script>
<script src="/js/rachni.js"></script>

<script type='text/javascript'>
	new Clipboard('.copyButton');
	var api_key = "<?= $accountinfo['api_key'] ?>";
	var display_name = "<?= $accountinfo['display_name'] ?>";
	var jp_status = "<?= $accountinfo['chat_jp_setting'] ?>";
	<?php if (!empty($streamkey)) { ?>
	var live_status = true;
	var stream_key = "<?= $streamkey; ?>";
	var current_channel = '<?= $streamkey; ?>';
	var videoposter = '<?php echo $user->updateStreamkey($streamkey, 'offline_image') ?>';
	var streamPlayer = videojs('streamPlayer', {
		techOrder: ['flash'],
		sources: [{
			src: 'rtmp://<?= $surl ?>/live&<?= $streamkey ?>',
			type: 'rtmp/flv',
			label: 'Flash'
		}],
	});
	streamPlayer.persistvolume({namespace: "Rachni-Volume-Control-" + stream_key});
	streamPlayer.on('fullscreenchange', function () {
		setTimeout(function () {
			$('#output').mCustomScrollbar('scrollTo', 'bottom');
		}, 200);
	});

	this.popoutPlayer = function () {
		streamPlayer.pause();
		window.open("<?= $furl ?>/popout/<?= $streamkey ?>", "_blank", "menubar=0,scrollbars=0,status=0,titlebar=0,toolbar=0,top=200,left=200,resizable=yes,width=1280,height=784");
	};
	this.resetPlayer = function () {
		streamPlayer.reset();
		streamPlayer.src({type: 'rtmp/flv', src: 'rtmp://<?= $surl ?>/live&<?= $streamkey ?>'});
		streamPlayer.persistvolume({namespace: "Rachni-Volume-Control-" + stream_key});
		$('.vjs-poster').hide();
		live_status = true;
	};

	<?php } elseif (!empty($video)) { ?>
	var videoPlayer = videojs('videoPlayer', {
		techOrder: ['html5'],
		sources: [{
			src: '//<?= $surl ?>/rec/<?= $video ?>',
			type: 'video/mp4',
		}],
	});
	videoPlayer.persistvolume({namespace: "Rachni-Volume-Control-<?= $video ?>"});

	<?php } else { ?>
	var current_channel = 'GlobalChatChannel';
	<?php } ?>
</script>
</body>
</html>
<!-- END FOOTER -->
