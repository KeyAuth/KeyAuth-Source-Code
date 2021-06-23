<?php

 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

    include '../includes/connection.php';
    session_start();

    if (isset($_SESSION['key'])) {
        header("Location: ../dashboard/");
        exit();
    }
    
    function xss_clean($data)
    {
        return strip_tags($data);
    }

    $link = $_SERVER['REQUEST_URI'];
    /*	if(substr_count($link, '/') == 4)
    {
        die("invalid link");
    }	*/
    
    $uri = trim($_SERVER['REQUEST_URI'], '/');
    $pieces = explode('/', $uri);
    $owner = $pieces[1];    $username = $pieces[2];
    
    $username = xss_clean(mysqli_real_escape_string($link, $username));
    if(!$username)
    {
        Die("Invalid Link, link should look something like https://keyauth.com/panel/mak/CSGI, where mak is the owner of the app, and CSGI is the app name.");
    }
    
    $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$username' AND `owner` = '$owner'");

    if (mysqli_num_rows($result) < 1)
    {
        Die("Panel does not exist");
    }		while ($row = mysqli_fetch_array($result))    {		$secret = $row['secret'];	}	

?>
<!DOCTYPE html>
<html lang="en">
<head>
		<?php
	echo'
	<title>KeyAuth - Login to '.$username.' Panel</title>
	<meta name="og:image" content="https://keyauth.com/assets/img/favicon.png">
    <meta name="description" content="Login to Reset your key or download '.$username.'">
    ';
    ?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
    <link rel="shortcut icon" href="https://keyauth.com/assets/img/favicon.png" type="image/x-icon">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="../vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../css/util.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						<?php echo'Login To '.$username.' Panel'; ?>
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="keyauthusername" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password" name="keyauthpassword" placeholder="Password">
						<span class="focus-input100"></span>
					</div>
					
					<div class="flex-sb-m w-full p-t-3 p-b-24">

						<div>
							<a href="../register/" class="txt1">
								Register
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


            

            
            $un = xss_clean(mysqli_real_escape_string($link, $_POST['keyauthusername']));
            $password = xss_clean(mysqli_real_escape_string($link, $_POST['keyauthpassword']));
            $resp['submitted_data'] = $_POST;
            $login_status = "invalid";

            ($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$un'")) or die(mysqli_error($link));

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
                    $pp = $row['pp'];
                    
                    $owner = $row['owner'];
                    $dayrate = $row['dayrate'];
                    $weekrate = $row['weekrate'];
                    $monthrate = $row['monthrate'];
                    $threemonthrate = $row['threemonthrate'];
                    $sixmonthrate = $row['sixmonthrate'];
                    $liferate = $row['liferate'];
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
                }								if ($role != "panel")                {                    $login_status = "banned";                    echo '                            <script type=\'text/javascript\'>                                                        const notyf = new Notyf();                            notyf                              .error({                                message: \'This account is not a panel account\',                                duration: 3500,                                dismissible: true                              });                                                                        </script>                            ';							return;                }
                

                if ($login_status !== "banned" || $login_status !== "invalid")
                {
                    if (strtolower($un) == strtolower($user) && password_verify($password, $pass) && $isbanned == "0")
                    {
                        $login_status = "success";
                    }

                    $resp['login_status'] = $login_status;

                    if ($login_status == "success")
                    {
                        
                            $_SESSION['un'] = $un;
                            $_SESSION['key'] = $email;
                            $_SESSION['img'] = $img;
                            $_SESSION['panelapp'] = $secret;
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
	<script src="../vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="../vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="../vendor/bootstrap/js/popper.js"></script>
	<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="../vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="../vendor/daterangepicker/moment.min.js"></script>
	<script src="../vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="../vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="../js/main.js"></script>

</body>
</html>