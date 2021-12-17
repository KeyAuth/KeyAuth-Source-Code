<?php
include '../includes/connection.php';
include '../includes/functions.php';
session_start();

if (isset($_SESSION['username']))
{
    header("Location: ../dashboard/");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>KeyAuth - Register</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="https://cdn.keyauth.com/assets/img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.com/auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.com/auth/css/main.css">
	<script src="https://www.google.com/recaptcha/api.js?render=6LdW_eAbAAAAACfb-xQmGOsinqox3Up0R4cFbSRj"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6LdW_eAbAAAAACfb-xQmGOsinqox3Up0R4cFbSRj', { action: 'contact' }).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						Register
					</span>

					
					<div class="wrap-input100 validate-input m-b-16">
						<input class="input100" type="text" name="username" placeholder="Username" minlength="2" required>
						<span class="focus-input100"></span>
					</div>
					
					<div class="wrap-input100 validate-input m-b-16">
						<input class="input100" type="email" name="email" placeholder="Email" required>
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16">
						<input class="input100" type="password" name="password" pattern='^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&?,.-_~` "]).*$' title="Please increase your password strength." placeholder="Password" required>
						<span class="focus-input100"></span>
					</div>
					
					<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
					
					<div class="flex-sb-m w-full p-t-3 p-b-24">

						<div>
							<a href="../login/" class="txt1">
								Already Registered?
							</a>
						</div>
					</div>
					
					<h>All registered users are bound by the <a href="../terms" class="txt1" target="_blank">Terms of Service and Privacy Policy</a></h>

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
	// you can leave captcha, I've allowed any hostname
    $recaptcha_response = sanitize($_POST['recaptcha_response']);
    $recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=6LdW_eAbAAAAALg8QLx524hDcnYOkZYIaCSmqH_x&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);
    
    // Take action based on the score returned:
    if ($recaptcha->score < 0.5)
    {
        error("Human Check Failed!");
        return;
    }

    $username = sanitize($_POST['username']);

    $password = sanitize($_POST['password']);

    $email = sanitize($_POST['email']);

    $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));

    if (mysqli_num_rows($result) == 1)
    {
        error("Username already taken!");
        return;
    }

    $email_check = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
    $do_email_check = mysqli_num_rows($email_check);
    if ($do_email_check > 0)
    {
        error('Email already used by username: ' . mysqli_fetch_array($email_check) ['username'] . '');
        return;
    }
	
    $pass_encrypted = password_hash($password, PASSWORD_BCRYPT);

    $ownerid = generateRandomString();

    mysqli_query($link, "INSERT INTO `accounts` (`username`, `email`, `password`, `ownerid`, `role`, `app`, `owner`, `img`,`balance`, `expires`, `registrationip`) VALUES ('$username', '$email', '$pass_encrypted', '$ownerid','tester','','','https://i.imgur.com/TrwYFBa.png','1', NULL, '$ip')") or die(mysqli_error($link));

	$_SESSION['logindate'] = time();
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['ownerid'] = $ownerid;
    $_SESSION['role'] = 'tester';
    $_SESSION['img'] = 'https://cdn.keyauth.com/front/assets/img/favicon.png';
    mysqli_close($link);
    header("location: ../dashboard/");
}

?> 
</body>
</html>