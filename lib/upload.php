<?php
// includes site vars
include '../inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// includes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists('../api/' . strtolower($class) . '.php')) {
			include '../api/' . strtolower($class) . '.php';
		} elseif (file_exists('../lib/' . strtolower($class) . '.class.php')) {
			include '../lib/' . strtolower($class) . '.class.php';
		}
	}
});

// verify we're logged in
require '../inc/auth.php';

$user = new user();

// grab account info
$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);
$accountinfo = $user->info($email);

// avatar update functions -TODO: move all this to a class
if (isset($_FILES['avatar']['error'])) {
	var_dump($_FILES);
}

try {

	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (!isset($_FILES['avatar']['error']) || is_array($_FILES['avatar']['error'])) {
		throw new RuntimeException('Invalid parameters/No file uploaded.');
	}

	// Check $_FILES['avatar']['error'] value.
	switch ($_FILES['avatar']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Exceeded filesize limit.');
		default:
			throw new RuntimeException('Unknown errors.');
	}

	// You should also check filesize here.
	if ($_FILES['avatar']['size'] > 1000000) {
		throw new RuntimeException('Exceeded filesize limit.');
	}

	// DO NOT TRUST $_FILES['avatar']['mime'] VALUE !!
	// Check MIME Type by yourself.
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	if (false === $ext = array_search(
			$finfo->file($_FILES['avatar']['tmp_name']),
			[
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			],
			true
		)
	) {
		throw new RuntimeException('Invalid file format.');
	}

	$uploads_dir = "../profiles/$email"; //TODO -- Fix relative file path issues
	$tmp_name = $_FILES["avatar"]["tmp_name"];
	$name = basename($_FILES["avatar"]["name"]);
	$ext = pathinfo($name, PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	if (!is_dir($uploads_dir)) {
		$mkdir = mkdir($uploads_dir, 0775);
		echo 'Made directory? ' . $mkdir;
	}
	$moved = move_uploaded_file($tmp_name, "$uploads_dir/avatar.$ext");
	if ($moved === true) {
		$avatarPath = "/$uploads_dir/avatar.$ext";
		$update = $user->imageUpdate($email, $avatarPath, 'avatar');
		$accountinfo = $user->info($email); //update after changing
		echo "File upload moved to /$uploads_dir/avatar.$ext";
		header("Location: /settings");
	} else {
		echo "File upload failed!";
	}

} catch (RuntimeException $e) {

	echo $e->getMessage();

}

try {

	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (!isset($_FILES['offline']['error']) || is_array($_FILES['offline']['error'])) {
		throw new RuntimeException('Invalid parameters/No file uploaded.');
	}

	// Check $_FILES['offline']['error'] value.
	switch ($_FILES['offline']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Exceeded filesize limit.');
		default:
			throw new RuntimeException('Unknown errors.');
	}

	// You should also check filesize here.
	if ($_FILES['offline']['size'] > 1000000) {
		throw new RuntimeException('Exceeded filesize limit.');
	}

	// DO NOT TRUST $_FILES['offline']['mime'] VALUE !!
	// Check MIME Type by yourself.
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	if (false === $ext = array_search(
			$finfo->file($_FILES['offline']['tmp_name']),
			[
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			],
			true
		)
	) {
		throw new RuntimeException('Invalid file format.');
	}

	$uploads_dir = "../profiles/$email"; //TODO -- Fix relative file path issues
	$tmp_name = $_FILES["offline"]["tmp_name"];
	$name = basename($_FILES["offline"]["name"]);
	$ext = pathinfo($name, PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	if (!is_dir($uploads_dir)) {
		$mkdir = mkdir($uploads_dir, 0775);
		echo 'Made directory? ' . $mkdir;
	}
	$moved = move_uploaded_file($tmp_name, "$uploads_dir/offline.$ext");
	if ($moved === true) {
		$offlinePath = "/$uploads_dir/offline.$ext";
		$update = $user->imageUpdate($email, $offlinePath, 'offline');
		$accountinfo = $user->info($email); //update after changing
		echo "File upload moved to /$uploads_dir/offline.$ext";
		header("Location: /settings");
	} else {
		echo "File upload failed!";
	}

} catch (RuntimeException $e) {

	echo $e->getMessage();

}