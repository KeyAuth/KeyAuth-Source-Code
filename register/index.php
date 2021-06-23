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
	<title>KeyAuth - Register</title>
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
						Register
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Email is required">
						<input class="input100" type="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
					</div>
					<div class="flex-sb-m w-full p-t-3 p-b-24">
						<div class="contact100-form-checkbox">
							<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
							<label class="label-checkbox100" for="ckb1">
								Remember me
							</label>
						</div>

						<div>
							<a href="../login/" class="txt1">
								Already Registered?
							</a>
						</div>
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

        $register_status = "success";

        if (isset($_POST['register']))
        {   
                    

                $username = xss_clean(mysqli_real_escape_string($link, $_POST['username']));
                
                $racist = array("nigger","kike","chink","coon"); 
  
                if (in_array($username, $racist)) 
                { 
                                    echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'KeyAuth Doesnt support racism. Pick another username!\',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';
                

                    return;
                } 
                
                
                $password = xss_clean(mysqli_real_escape_string($link, $_POST['password']));
                
                $email = xss_clean(mysqli_real_escape_string($link, $_POST['email']));

                $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));
            
                if (strlen($password) <= 5)
                {
                    echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'Use a longer password!\',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';
                    


                        return;
                }
                else if (mysqli_num_rows($result) >= 1)
                {   
                    
                    echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'Username already taken!\',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';
                    

                        return;
                }
                else if (strlen($email) > 40)
                {
                    echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'Email too long!, maximum lenght is 40 characters.\',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';
                    


                        return;
                }
                else if (strlen($password) > 30)
                {
                    echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'Password too long!, maximum lenght is 30 characters.\',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';

                     return;
                }
                else
                {
                    $user_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));
                    $do_user_check = mysqli_num_rows($user_check);
    
                    if ($do_user_check > 0)
                    {
                        $register_status = "user_taken";
                    }
    
                    $email_check = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
                    $do_email_check = mysqli_num_rows($email_check);
					$row = mysqli_fetch_array($email_check);
					
                    if ($do_email_check > 0)
                    {
					echo '
                    <script type=\'text/javascript\'>
                    
                    const notyf = new Notyf();
                    notyf
                      .error({
                        message: \'Email already used by username: '.$row['username'].' \',
                        duration: 3500,
                        dismissible: true
                      });                
                    
                    </script>
                    ';

                     return;
                    }

                    if ($register_status == "success")
                    {
                        $pass_encrypted = password_hash($password, PASSWORD_BCRYPT);
                        
                        $ownerid = generateRandomString();

                        $expires = time() + 3.154e+7;
                        
                        mysqli_query($link, "INSERT INTO `accounts` (`username`, `email`, `password`, `ownerid`, `role`, `app`, `owner`, `isbanned`, `img`, `pp`, `dayrate`, `weekrate`, `monthrate`,`balance`, `expires`, `registrationip`) VALUES ('$username', '$email', '$pass_encrypted', '$ownerid','tester','','',0,'https://i.imgur.com/TrwYFBa.png', '','','','','1', '$expires', '".$_SERVER["HTTP_CF_CONNECTING_IP"]."')") or die(mysqli_error($link));
                        $register_status = "success";
                    }

                    if ($register_status == true)
                    {
                        echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'Successfully registered!, Redirecting(...)\',
                            duration: 3500,
                            dismissible: true
                          });                
                        
                        </script>
                        ';
                        
                        $_SESSION['username'] = $username;
                            $_SESSION['email'] = $email;
                            $_SESSION['ownerid'] = $ownerid;
                            $_SESSION['role'] = 'tester';
                            
                            $_SESSION['img'] = 'https://i.imgur.com/TrwYFBa.png';
                            
                          echo '<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=../dashboard/">';
                          exit();
                    
                }
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
