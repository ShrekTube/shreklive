<?php
// start the session, in case we're already logged in
session_start();

// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//includes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists('lib/' . strtolower($class) . '.class.php')) {
			include 'lib/' . strtolower($class) . '.class.php';
		}
	}
});

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$submitted = filter_input(INPUT_POST, 'Submitted', FILTER_SANITIZE_STRING);

if (empty($_SESSION['authenticated']) && !empty($_COOKIE['rememberMe']) && !empty($_COOKIE['email'])) {
	$_SESSION['authenticated'] = $_COOKIE['email'];
	if (!empty($_SESSION['dest_url'])) {
		header('Location: ' . $_SESSION['dest_url']);
	} else {
		header('Location: /channels');
	}
}

if ($submitted === 'Login') {
	$user = new user();
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$status = $user->login($email, $password);
	if ($status === true) {
		if (!empty($_SESSION['dest_url'])) {
			header('Location: ' . $_SESSION['dest_url']);
		} else {
			header('Location: /channels');
		}
	}
}

if ($submitted === 'Register') {
	if ($reg_open === true) {
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$displayname = filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_STRING);

		// verify all required info is present before executing register
		if (empty($email) || empty($password) || empty($displayname)) {
			$status = 'Please enter a valid email address, display name, and a password.';
		} else {
			$user = new user();
			$status = $user->register($email, $password, $displayname);
			if ($status === true) {
				$status = '<br />Account created.<br />Please check your email for verification.<br /><br />Note: Email may be in spam.';
			}
		}
	} else {
		$status = '<br />Account creation currently disabled.<br /><br />Please contact issues@rirnef.net for details.';
	}
}

// check if we're trying to verify an account
// and if the URL has both required pieces of information
// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);

if (!empty($uriVars[1])) {
	$vcheck = $uriVars[1];
	if ($vcheck === 'verify') {
		$vemail = $uriVars[2];
		$vcode = $uriVars[3];
		if (!empty($vemail) && !empty($vcode)) {
			$user = new user();
			$vstatus = $user->verify($vemail, $vcode);

			// let user know how it went
			if ($vstatus === 'true') {
				$status = 'Account verification successful! You may now log in.';
			} else {
				$status = $vstatus;
			}
		}
	}
}
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $sitetitle?> Login</title>
	<link href="/img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,300,100,700,900' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="/css/application.css">
	<link rel="stylesheet" href="/css/site.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
	<header class="mdl-layout__header">
		<div class="mdl-layout__header-row">
			<div class="mdl-layout-spacer"></div>

			<div class="avatar-dropdown" id="icon">
				<span>Not Logged In</span>
			</div>

			<ul class="mdl-menu mdl-list mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect mdl-shadow--2dp account-dropdown"
				for="icon">
				<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<span>Not Logged In</span>
								<span class="mdl-list__item-sub-title">not@logged.in.yet.com</span>
							</span>
				</li>
				<li class="list__item--border-top"></li>

				<a href="#register" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">account_circle</i>
								<span>Log In or Register!</span>
							</span>
				</a>
			</ul>
		</div>
	</header>

	<div class="mdl-layout__drawer">
		<header class="dm-logo-header"><?= $sitetitle ?></header>
		<nav class="mdl-navigation">
			<a class="mdl-navigation__link mdl-navigation__link--current" href="/">
				<i class="material-icons" role="presentation">person</i>
				Login
			</a>
		</nav>
	</div>

	<main class="mdl-layout__content mdl-color--grey-100">
		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
			<div class="mdl-card mdl-shadow--2dp login-form" action="">
				<div class="mdl-card__title-login">

					<div class="mdl-tabs__tab-bar-login">
						<a href="#login" class="mdl-tabs__tab mdl-tabs__tab-half-width is-active">Log In</a>
						<a href="#register" class="mdl-tabs__tab mdl-tabs__tab-half-width">Register</a>
					</div>

				</div>



				<div class="mdl-tabs__panel is-active" id="login">
					<div class="mdl-card__supporting-text">
						<form action="" method="POST" class="form" id="loginForm">
							<div class="form__article">
								<div class="mdl-grid">
									<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="email" name="email" id="emailAddress"/>
										<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
									</div>
								</div>

								<div class="mdl-grid">
									<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="password" name="password" id="Password"/>
										<label class="mdl-textfield__label" for="Password">Password</label>
									</div>
								</div>




								<div class="form__action">
									<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="rememberLogin">
										<input type="checkbox" name="rememberMe" value="true" id="rememberLogin" class="mdl-checkbox__input">
										<span class="mdl-checkbox__label">Remember Me</span>
									</label>
									<button type="submit" name="Submitted" value="Login" form="loginForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
										Login
									</button>
								</div>
								<div class="mdl-grid">
									<div class="mdl-cell mdl-cell--12-col mdl-typography--text-center">
										<?php
										if (!empty($status)) {
											echo '<br />' . $status;
										}
										?>
									</div>
									<div class="mdl-cell mdl-cell--12-col forgot-pass">
										<span class="mdl-typography--text-left">Forgot your password? <a href="/lostpass">Click here</a></span>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="mdl-tabs__panel" id="register">
					<div class="mdl-card__supporting-text">
						<?php if ($reg_open === true) { ?>
							<form action="" method="POST" class="form" id="registerForm">
								<div class="form__article">

									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
											<input class="mdl-textfield__input" type="email" name="email" id="emailAddress"/>
											<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
										</div>
									</div>

									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
											<input class="mdl-textfield__input" type="text" name="displayname" id="displayName"/>
											<label class="mdl-textfield__label" for="displayName">Display Name</label>
										</div>
									</div>

									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
											<input class="mdl-textfield__input" type="password" name="password" id="Password"/>
											<label class="mdl-textfield__label" for="Password">Password</label>
										</div>
									</div>

									<div class="form__action-register">
										<button type="submit" name="Submitted" value="Register" form="registerForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
											Register
										</button>
									</div>

									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col mdl-typography--text-center">
											<?php
											if (!empty($status)) {
												echo '<br />' . $status;
											}
											?>
										</div>
									</div>
								</div>
							</form>
						<?php } else { ?>

							<div class="mdl-grid">
								<span>Registration currently closed. <br /><br />Please contact <a href="mailto:<?= $reply_email ?>"><?= $reply_email ?></a> for more info.</span>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>

<script src="/js/material.js"></script>
<script src="/js/getmdl-select.min.js"></script>

</body>
</html>
