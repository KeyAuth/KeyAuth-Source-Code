<?php
require '../../includes/connection.php';
require '../../includes/misc/autoload.phtml';
require '../../includes/dashboard/autoload.phtml';

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if (!isset($_SESSION['panelapp']))
{
    die("You must go to your panel login first then visit this page");
}
?>	
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Upgrade</title>
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
						Upgrade
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "License is required">
						<input class="input100" type="text" name="license" placeholder="License">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn m-t-17">
						<button name="upgrade" class="login100-form-btn">
							Upgrade
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <?php
if (isset($_POST['upgrade']))
{
    $username = misc\etc\sanitize($_POST['username']);
    $license = misc\etc\sanitize($_POST['license']);

    // search username
    $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$username' AND `app` = '".$_SESSION['panelapp']."'");

    // check if username already exists
    if (mysqli_num_rows($result) == 0)
    {
        dashboard\primary\error("Username doesn\'t exist!");
        return;
    }

    // search for key
    $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$license' AND `app` = '".$_SESSION['panelapp']."'");

    // check if key exists
    if (mysqli_num_rows($result) == 0)
    {
		dashboard\primary\error("License doesn\'t exist!");
        return;
    }

    // get key info
    while ($row = mysqli_fetch_array($result))
    {

        $expires = $row['expires'];

        $status = $row['status'];

        $level = $row['level'];

    }

    // check if used
    if ($status == "Used")
    {
		dashboard\primary\error("License already used!");
        return;
    }

    // set key to used
    mysqli_query($link, "UPDATE `keys` SET `status` = 'Used' WHERE `key` = '$license'");

    // add current time to key time
    $expiry = $expires + time();

    $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '".$_SESSION['panelapp']."' AND `level` = '$level'");

    $num = mysqli_num_rows($result);

    if ($num == 0)
    {
		dashboard\primary\error("License level doesn\'t correspond to a subscription level!");
        return;
    }

    $subname = mysqli_fetch_array($result) ['name'];

    mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$subname', '$expiry', '".$_SESSION['panelapp']."')");
	dashboard\primary\success("Successfully Upgraded!");
}
?>
</body>
</html>
