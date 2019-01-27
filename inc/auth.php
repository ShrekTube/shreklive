<?php

/*
 * ---------------------------------------------------------------
 * Authentication check script
 * ---------------------------------------------------------------
 *
 * This page is used for verifying that a user is actually logged in.
 * It will check for cookie, set that as active session if session isn't
 * already present. It will also verify a cookie is valid against the
 * user database, to prevent a deleted user from connecting with a 
 * previously valid cookie.
 * 
 * TODO:
 *
 *     -Make this whole page not suck
 *
 * NOTE: This file sets up $user for use later
 * 		 If this file is included, do not set again on the page.
 */

// prevent opening duplicate sessions
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

$user = new user();
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_COOKIE, 'email', FILTER_SANITIZE_STRING);
$refer = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);

if (!empty($email) && $user->session_check($email) === false) {
	session_destroy();
	setcookie('rememberMe', null, -1, '/');
	setcookie('email', null, -1, '/');
	if ($refer != '/img/favicon.ico') {
		$_SESSION['dest_url'] = $refer;
	}
	header("Location: /login");
}

if (!empty($_SESSION['authenticated']) && $user->session_authenticate($action) === false) {
	if ($refer != '/img/favicon.ico') {
		$_SESSION['dest_url'] = $refer;
	}
	header('Location: /login');
}

if (empty($_SESSION['authenticated'])) {
	if ($refer != '/img/favicon.ico') {
		$_SESSION['dest_url'] = $refer;
	}
	header('Location: /login');
}

