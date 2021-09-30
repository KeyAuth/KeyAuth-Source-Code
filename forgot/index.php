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
	<title>KeyAuth - Forgot</title>
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
						KeyAuth - Forgot
					</span>
					
					<div class="wrap-input100 validate-input m-b-16">
						<input class="input100" type="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
					</div>
					
					<input type="hidden" name="recaptcha_response" id="recaptchaResponse">

					<div class="container-login100-form-btn m-t-17">
						<button name="reset" class="login100-form-btn">
							Reset Password
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

        <?php
if (isset($_POST['reset']))
{

    $recaptcha_response = sanitize($_POST['recaptcha_response']);
    $recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=6LdW_eAbAAAAALg8QLx524hDcnYOkZYIaCSmqH_x&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score < 0.5)
    {
        error("Human Check Failed!");
        return;
    }

    $email = sanitize($_POST['email']);
    $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
    if (mysqli_num_rows($result) == 0)
    {
        error("No account with this email!");
        return;
    }

    $un = mysqli_fetch_array($result) ['username'];

    $newPass = generateRandomString();
    $newPassHashed = password_hash($newPass, PASSWORD_BCRYPT);
    $fromName = 'KeyAuth';
    $htmlContent = ' 
                    <html> 
                    <head> 
                        <title>KeyAuth - You Requested A Password Reset</title> 
                    </head> 
                    <body> 
                        <h1>We have reset your password</h1> 
                        <p>Your new password is: <b>' . $newPass . '</b></p>
						<p>Also, in case you forgot, your username is: <b>' . $un . '</b></p>
                        <p>Login to your account and change your password for the best privacy.</p>
                        <p style="margin-top: 20px;">Thanks,<br><b>KeyAuth.</b></p>
                    </body> 
                    </html>';
    // Set content-type header for sending HTML email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";

    $subject = 'KeyAuth - Password Reset';
    $from = "noreply@keyauth.com";

    $headers .= "From:" . $from;

    if (mail($email, $subject, $htmlContent, $headers))
    {
        $update = mysqli_query($link, "UPDATE `accounts` SET `password` = '$newPassHashed' WHERE `email` = '$email'") or die(mysqli_error($link));
		success("Please check your email, I sent password. (Check Spam Too!)");
	}
    else
    {
        error("Failed to reset password. Please contact support!");
    }

}

?>
</body>
</html>