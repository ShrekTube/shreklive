<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// start the session, in case we're already logged in (currently broke, future enhancements)
session_start();

//includes
/**
 * @param $class
 */
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$submitted = filter_input(INPUT_POST, 'Submitted', FILTER_SANITIZE_STRING);

if (empty($_SESSION['authenticated']) && !empty($_COOKIE['rememberMe']) && !empty($_COOKIE['email'])) {
	$_SESSION['authenticated'] = $_COOKIE['email'];
	header('Location: /channels');
}

if ($submitted === 'Reset') {
	$user = new user();
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$authcode = filter_input(INPUT_POST, 'authCode', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	if (empty($email) || empty($authcode) || empty($password)) {
		$status = 'Please enter all the required information.';
	} else {
		$reset = $user->passwordReset($email, $authcode, $password);
		if ($reset === true) {
			$status = 'Password reset successfully! You may now <a href="/login">login</a>.';
		} else {
			$status = $reset;
		}
	}
}

if ($submitted === 'getCode') {
	$user = new user();
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$getcode = $user->resetCode($email, $furl);
	if ($getcode === true) {
		$status = 'Code sent! Check your email.';
	} else {
		$status = $getcode;
	}
}
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $sitetitle ?> Password Reset</title>
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
				<header><?= $sitetitle ?></header>
				<nav class="mdl-navigation">
					<a class="mdl-navigation__link mdl-navigation__link--current" href="/">
						<i class="material-icons" role="presentation">arrow_back</i>
						Back to Login
					</a>
				</nav>
			</div>
			<main class="mdl-layout__content mdl-color--grey-100">
				<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
					<div class="mdl-card mdl-shadow--2dp employer-form" action="">

						<div class="mdl-card__title-login">
							<div class="mdl-tabs__tab-bar-login">
								<a href="#code" class="mdl-tabs__tab mdl-tabs__tab-half-width is-active">Get Code</a>
								<a href="#reset" class="mdl-tabs__tab mdl-tabs__tab-half-width">Reset Password</a>
							</div>
						</div>

						<div class="mdl-tabs__panel" id="reset">
							<div class="mdl-card__supporting-text">
								<span>Enter your email, authentication code, and new password below. If you need a code, click on <b>Get Code</b> above</span>
								<form action="" method="POST" class="form" id="resetForm">
									<div class="form__article">
										<div class="mdl-grid">
											<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="email" name="email" id="emailAddress" required/>
												<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
											</div>
										</div>
										<div class="mdl-grid">
											<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="text" name="authCode" id="authCode" required/>
												<label class="mdl-textfield__label" for="authCode">Password Reset
													Code</label>
											</div>
										</div>
										<div class="mdl-grid">
											<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="password" name="password" id="Password" required/>
												<label class="mdl-textfield__label" for="Password">New Password</label>
											</div>
										</div>
										<div class="form__action">
											<button type="submit" name="Submitted" value="Reset" form="resetForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
												Reset
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
							</div>
						</div>

						<div class="mdl-tabs__panel is-active" id="code">
							<div class="mdl-card__supporting-text">
								<span>Enter your email address and click Get Code to be sent a password reset code. If you already have a code, click on <b>Reset Password</b> above.</span>
								<form action="" method="POST" class="form" id="codeForm">
									<div class="form__article">
										<div class="mdl-grid">
											<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="email" name="email" id="emailAddress" required/>
												<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
											</div>
										</div>
										<div class="form__action">
											<button type="submit" name="Submitted" value="getCode" form="codeForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
												Get Code
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
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>

		<!-- inject:js -->
		<script src="/js/getmdl-select.min.js"></script>
		<script src="/js/material.js"></script>
		<!-- endinject -->

	</body>
</html>
