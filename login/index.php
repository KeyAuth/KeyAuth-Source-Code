<?php


    include '../includes/connection.php';
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
	<title>KeyAuth - Login</title>
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
						Login
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="keyauthusername" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password" name="keyauthpassword" placeholder="Password">
						<span class="focus-input100"></span>
					</div>

                    <div class="wrap-input100 validate-input m-b-16">
						<input class="input100" name="keyauthtwofactor" placeholder="Two Factor Code (if applicable)">
						<span class="focus-input100"></span>
					</div>
					
					<div class="flex-sb-m w-full p-t-3 p-b-24">
						<div>
							<a href="../register/" class="txt1">
								Register
							</a>
						</div>

						<div>
							<a href="../forgot/" class="txt1">
								Forgot?
							</a>
						</div>
					</div>

					<div class="container-login100-form-btn m-t-17">
						<button name="login" class="login100-form-btn">
							Login
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <?php
        if (isset($_POST['login']))
        {
            if (empty($_POST['keyauthusername']) || empty($_POST['keyauthpassword']))
            {
                
                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'You must fill in all the fields!\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
                

                

                  return;
            }


            $status_login = "";


            

            
            $username = xss_clean(mysqli_real_escape_string($link, $_POST['keyauthusername']));
            $password = xss_clean(mysqli_real_escape_string($link, $_POST['keyauthpassword']));
            $resp['submitted_data'] = $_POST;
            $login_status = "invalid";

            ($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));

            if (mysqli_num_rows($result) < 1)
            {
                $login_status = "invalid";
                
                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'Your login details are incorrect!\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';                

                

                  return;
            }
            else if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $user = $row['username'];
                    $pass = $row['password'];
                    $id = $row['ownerid'];
                    $email = $row['email'];
                    $role = $row['role'];
                    $app = $row['app'];
                    $isbanned = $row['isbanned'];
                    $img = $row['img'];
                    
                    $owner = $row['owner'];
                    $twofactor_optional = $row['twofactor'];
                }

                if ($isbanned == "1")
                {
                    $login_status = "banned";
                    echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Your account has been banned!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
							
                }
                

                if ($login_status !== "banned" || $login_status !== "invalid")
                {
                    if (strtolower($username) == strtolower($user) && password_verify($password, $pass) && $isbanned == "0")
                    {
                        $login_status = "success";
                    }

                    $resp['login_status'] = $login_status;

                    if ($login_status == "success")
                    {
                            
                        if ($twofactor_optional == "1")
                        {
                           // keyauthtwofactor
                           $twofactor = xss_clean(mysqli_real_escape_string($link, $_POST['keyauthtwofactor']));
                           if (empty($twofactor))
                           {
                                           echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'Two facor field needed for this acccount!\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
                

                

                  return;
                           }

                require_once 'GoogleAuthenticator.php';
    $gauth = new GoogleAuthenticator();

            $user_result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));
            
            while ($row = mysqli_fetch_array($user_result))
            {
                $google_Code = $row['googleAuthCode'];
            }
            
            $checkResult = $gauth->verifyCode($google_Code, $twofactor, 2);
            
            if ($checkResult)
            {
                            $_SESSION['username'] = $_POST['keyauthusername'];
                            $_SESSION['email'] = $email;
                            $_SESSION['ownerid'] = $id;
                            $_SESSION['owner'] = $owner;
                            $_SESSION['role'] = $role;
                            
                            if($role == "Reseller" || $role == "Manager")
                            {
                                $_SESSION['app'] = $app;
                            }
                            
                            $_SESSION['img'] = $img;
                
                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .success({
                    message: \'You have successfully logged in!\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';

                  echo "<meta http-equiv='Refresh' Content='2; url=../dashboard'>"; 
            }
            else
            {
                echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'The code entered is incorrect\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
                
            return;
            }
                        }

                        
                            $_SESSION['username'] = $_POST['keyauthusername'];
                            $_SESSION['email'] = $email;
                            $_SESSION['ownerid'] = $id;
                            $_SESSION['owner'] = $owner;
                            $_SESSION['role'] = $role;
                            
                            if($role == "Reseller" || $role == "Manager")
                            {
								($result = mysqli_query($link, "SELECT `secret` FROM `apps` WHERE `name` = '$app' AND `owner` = '$owner'")) or die(mysqli_error($link));
								if (mysqli_num_rows($result) < 1)
								{
									$login_status = "invalid";
									
									echo '
									<script type=\'text/javascript\'>
									
									const notyf = new Notyf();
									notyf
									.error({
										message: \'Application you\'re assigned to no longer exists!\',
										duration: 3500,
										dismissible: true
									});                
									
									</script>
									';                
								
									
								
									return;
								}
								while ($row = mysqli_fetch_array($result))
								{
								$app = $row["secret"];
								}
                                $_SESSION['app'] = $app;
                            }
                            
                            $_SESSION['img'] = $img;
							
							
							mysqli_query($link, "INSERT INTO `acclogs`(`username`, `date`, `ip`, `useragent`) VALUES ('".$_POST['keyauthusername']."','".time()."','".$_SERVER["HTTP_CF_CONNECTING_IP"]."','".$_SERVER['HTTP_USER_AGENT']."')");
							
							// webhook start
								$timestamp = date("c", strtotime("now"));

								$json_data = json_encode([
									// Message
									"content" => "".$_SESSION['username']." has logged into KeyAuth with IP {$_SERVER['HTTP_CF_CONNECTING_IP']}",
									
									// Username
									"username" => "KeyAuth Logs",
								
								], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
								
								
								$ch = curl_init("webhook_link_here");
								curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/json'));
								curl_setopt($ch,CURLOPT_POST,1);
								curl_setopt($ch,CURLOPT_POSTFIELDS,$json_data);
								curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
								curl_setopt($ch,CURLOPT_HEADER,0);
								
								curl_exec($ch);
								curl_close($ch);
								// webhook end
							
                            echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .success({
                                message: \'You have successfully logged in!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';                                            
                            
                      
                            
             
                            echo "<meta http-equiv='Refresh' Content='2; url=../dashboard/'>";                             
                        


                    }
                    else
                    {
                            echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Your login details are incorrect!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';                
        
                          return;
}}}}
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