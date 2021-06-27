<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
    include '../includes/connection.php';
    include '../includes/functions.php';
    session_start();

    if (isset($_SESSION['username'])) {
        header("Location: ../dashboard/");
        exit();
    }
    
    function xss_clean($data)
    {
        return strip_tags($data);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>KeyAuth - Forgot</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
    <link rel="shortcut icon" href="https://keyauth.com/assets/img/favicon.png" type="image/x-icon">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						Forgot
					</span>
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Email is required">
						<input class="input100" type="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
					</div>

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
    $register_status = 'success';
    if (isset($_POST['reset']))
    {
            $email = xss_clean(mysqli_real_escape_string($link, $_POST['email']));
            $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
            if (mysqli_num_rows($result) == 1)
            {
                
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
                        <p>Your new password is: <b>'.$newPass.'</b></p>
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
                    
                                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .success({
                    message: \'Please check your email, I sent password. (Check Spam Too!)\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
                    
                }
                else 
                {
                    
                                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'Failed to reset password. Please contact support!\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';     
                    
                }
            }
            else 
                {
                    
                                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'Failed to find account with that email\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';     
                    
                }
            
        }

?>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>