<?php
require '../../includes/connection.php';
require '../../includes/misc/autoload.phtml';
require '../../includes/dashboard/autoload.phtml';
require '../../includes/api/1.0/autoload.phtml';
require '../../includes/api/shared/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
	
if(!isset($_SESSION['panelapp']))
{
	die("You must go to your panel login first then visit this page");
}
?>	
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Register</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="https://cdn.keyauth.uk/assets/img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.uk/auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.uk/auth/css/main.css">
	<meta name="robots" content="nosnippet, nofollow, noindex"/>
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						Register
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
					</div>
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "License is required">
						<input class="input100" type="text" name="license" placeholder="License">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn m-t-17">
						<button name="register" class="login100-form-btn">
							Register
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <?php
if (isset($_POST['register']))
{	
	$username = misc\etc\sanitize($_POST['username']);
	$password = misc\etc\sanitize($_POST['password']);
	$license = misc\etc\sanitize($_POST['license']);
    
	$resp = api\v1_0\register($username, $license, $password, NULL, $_SESSION['panelapp']);
	switch($resp)
	{
		case 'username_taken':
			dashboard\primary\error("Username taken!");
			break;
		case 'key_not_found':
			dashboard\primary\error("License doesn\'t exist!");
			break;
		case 'key_already_used':
			dashboard\primary\error("License already used!");
			break;
		case 'key_banned':
			dashboard\primary\error("License is banned!");
			break;
		default:
			$_SESSION['un'] = $username;
			header("location: ../dashboard/");
			break;
	}
}
?>
</body>
</html>