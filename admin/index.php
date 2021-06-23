<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$admins = array("mak", "zegevlier");
if(!in_array($_SESSION['username'],$admins)) // if user is not an admin, send them to homepage
{
	header("Location: ../");
	die();
}



include '../includes/connection.php';

function wh_log($log_msg) // logging account upgrades to ./log/
{
    $log_filename = "logs";
    if (!file_exists($log_filename)) 
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
} 


if(isset($_POST['checkexist']))

{

	$username = strip_tags(mysqli_real_escape_string($link, $_POST['username']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
	
    if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist");
    }
	else
	{
		echo "Does exist";
	}

}



if(isset($_POST['checkemail']))

{

	$username = strip_tags(mysqli_real_escape_string($link, $_POST['username']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist, can't complete email check.");
    }
	
	echo mysqli_fetch_array($result)["email"];

}



if(isset($_POST['checkrole']))

{

	$username = strip_tags(mysqli_real_escape_string($link, $_POST['username']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)
    {
	die("Doesn't exist, can't complete role check.");
    }
	
	echo mysqli_fetch_array($result)["role"];

}
if(isset($_POST['checkorder']))

{

$orderid = strip_tags(mysqli_real_escape_string($link, $_POST['orderid']));

$url = "https://shoppy.gg/api/v1/orders/{$orderid}";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "User-Agent: KeyAuth",
   "Authorization: shoppyapikey",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);

}



if(isset($_POST['devupgrade']))

{

	$username = strip_tags(mysqli_real_escape_string($link, $_POST['username']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist, can't upgrade account.");
    }
	
	mysqli_query($link, "UPDATE `accounts` SET `role` = 'developer' WHERE `username` = '$username'");
	wh_log("".$_SESSION['username']." has upgraded {$username}");
	echo "upgraded to developer";

}



if(isset($_POST['sellerupgrade']))

{

	$username = strip_tags(mysqli_real_escape_string($link, $_POST['username']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist, can't upgrade account.");
    }
	
	mysqli_query($link, "UPDATE `accounts` SET `role` = 'seller' WHERE `username` = '$username'");
	wh_log("".$_SESSION['username']." has upgraded {$username}");
	echo "upgraded to seller";

}

if(isset($_POST['usercheckwithemail']))
{
	$email = strip_tags(mysqli_real_escape_string($link, $_POST['email']));

	($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist, can't complete user check with email.");
    }
	
	echo mysqli_fetch_array($result)["username"];
}

if(isset($_POST['appinfo']))
{
	$name = strip_tags(mysqli_real_escape_string($link, $_POST['appname']));

	($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$name'")) or die(mysqli_error($link));
	
	if (mysqli_num_rows($result) === 0)

    {
	die("Doesn't exist, can't complete app info check.");
    }
	
	$row = mysqli_fetch_array($result);
	
	echo "Owner: ";
	echo $row['owner'];
	echo nl2br("\nSecret: ");
	echo $row['secret'];
	echo nl2br("\nOwnerID: ");
	echo $row['ownerid'];
}

?>

<title>KeyAuth Admin</title>

<form method="post">

<input name="username" placeholder="username"></input>  <input name="orderid" placeholder="Order ID"></input>  <input name="email" placeholder="Email"></input>  <input name="appname" placeholder="App name"></input><br><br><button name="checkexist">Check Existance</button><br><br><button name="checkemail">Check Email</button><br><br><button name="checkrole">Check Role</button><br><br><button name="checkorder">Check Order</button><br><br><button name="devupgrade">Upgrade Developer</button>  <button name="sellerupgrade">Upgrade Seller</button><br><br><button name="usercheckwithemail">Check Username With Email</button><br><br><button name="appinfo">Check Application Info</button><br><br><i>Activity logged to <a href="./logs/" target="logs">./logs/</a></i>

</form>