<?php
/*#====================
astronauth/ - landing page. if signed in: redirect to account; if not: redirect to signin
astronauth/signin/
astronauth/signout/
astronauth/signup/
astronauth/forgotPassword/ - …
astronauth/account/ - own account management
*/#====================

session_start();

set_exception_handler(function($e){
	return_500($e);
});

require __DIR__ . '/../libs/Astronauth/autoloader.php';

$astronauth = new \Astronauth\Main();
$astronauth->authenticate();

$path = trim($_SERVER['REQUEST_URI'], '/');

$server = (object) [
	'url' => \Astronauth\Config\Config::SERVER_URL
];

$authstate_redirect = false;

if($path == 'astronauth'){
	require __DIR__ . '/templates/landing.php';

} else if($path == 'astronauth/signin'){
	if($astronauth->is_authenticated()){
		$authstate_redirect = true;
		header('Location: ' . $server->url . '/account');
	}

	$signin_success = null;

	if(!empty($_POST)){
		if($signin_success = $astronauth->signin($_POST['identifier'], $_POST['password'], (bool) $_POST['remember'])){
			header('Location: ' . $server->url . '/account');
		}
	}

	require __DIR__ . '/templates/signin.php';

} else if($path == 'astronauth/signout'){
	if(!$astronauth->is_authenticated()){
		$authstate_redirect = true;
		header('Location: ' . $server->url . '/signin');
	}

	$signout_success = null;

	if(!empty($_POST)){
		$astronauth->signout();
		$signout_success = true;
		header('Location: ' . $server->url . '/signin');
	}

	require __DIR__ . '/templates/signout.php';

} else if($path == 'astronauth/signup'){
	$signup_return = null;

	if(!empty($_POST)){
		if($signup_return = $astronauth->create_account($_POST['name'], $_POST['email'], $_POST['password'])){
			header('Location: ' . $server->url . '/account');
		}
	}

	require __DIR__ . '/templates/signup.php';

} else if($path == 'astronauth/forgotPassword'){
	require __DIR__ . '/templates/forgotPassword.php';

} else if($path == 'astronauth/account'){
	require __DIR__ . '/templates/account.php';

} else {
	// 404
	require __DIR__ . '/templates/404.php';

}

function return_500($exception) {
	require __DIR__ . '/templates/500.php';
}
?>
