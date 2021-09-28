<?php
include '../includes/connection.php';
include '../includes/functions.php';
session_start();

if (isset($_SESSION['un']))
{
    header("Location: ../dashboard/");
    exit();
}

function htmlEncode($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Administrator/Not Discord says don't use 'link' var it's already been defined in connection.php, use something like 'requrl'
$requrl = $_SERVER['REQUEST_URI'];

$uri = trim($_SERVER['REQUEST_URI'], '/');
$pieces = explode('/', $uri);
$owner = sanitize($pieces[1]);
$username = sanitize($pieces[2]);

if (!strip_tags(htmlEncode($requrl)) || substr_count($requrl, '/') != 3)
{
    Die("Invalid Link, link should look something like https://keyauth.com/panel/mak/CSGI, where mak is the owner of the app, and CSGI is the app name.");
}

$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$username' AND `owner` = '$owner'");

if (mysqli_num_rows($result) < 1)
{
    die("Panel does not exist.");
}

while ($row = mysqli_fetch_array($result))
{
    $secret = $row['secret'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
echo '
	    <title>KeyAuth - Login to ' . $username . ' Panel</title>
	    <meta name="og:image" content="https://keyauth.com/assets/img/favicon.png">
        <meta name="description" content="Login to Reset your key or download ' . $username . '">
        ';
?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../../../assets/img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
	<link rel="stylesheet" type="text/css" href="../../../auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="../../../auth/css/main.css">
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						<?php echo 'Login To ' . $username . ' Panel'; ?>
					</span>

					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Username is required">
						<input class="input100" type="text" name="keyauthusername" placeholder="Username">
						<span class="focus-input100"></span>
					</div>
					
					
					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password" name="keyauthpassword" placeholder="Password">
						<span class="focus-input100"></span>
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

        error("You must fill in all the fields!");
        return;
    }

    $un = sanitize($_POST['keyauthusername']);
    $password = sanitize($_POST['keyauthpassword']);

    $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un'");

    if (mysqli_num_rows($result) < 1)
    {
        error("User not found!");
        return;
    }
    else if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $pass = $row['password'];
            $banned = $row['banned'];
        }

        if (!is_null($banned))
        {
            error("Banned: Reason: " . sanitize($banned));
            return;
        }

        if (!password_verify($password, $pass))
        {
            error("Password is invalid!");
            return;
        }

        $_SESSION['un'] = $un;
        $_SESSION['panelapp'] = $secret;
        header("location: ../dashboard/");
    }
}
?>
</body>
</html>
