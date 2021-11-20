<?php
ini_set('display_errors', 'Off');
error_reporting(0);
include '../../includes/connection.php';
include '../../includes/functions.php';

$apikey = sanitize($_GET['apikey']);
if($apikey != $adminapikey)
{
	die(json_encode(array(
            "success" => false,
            "message" => "invalid admin API key"
        )));
}

$type = sanitize($_GET['type']);
switch ($type)
{
    case 'checkorder':
        $orderid = sanitize($_GET['orderid']);
		$url = "https://shoppy.gg/api/v1/orders/{$orderid}";
	
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$headers = array(
		"User-Agent: KeyAuth", // must set a useragent for Shoppy API, anything.
		"Authorization: {$shoppyAPIkey}", // shoppy API key, variable found in includes/connection.php
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$resp = curl_exec($curl);
		curl_close($curl);
		
		$json = json_decode($resp);
		
		if($json->message == "Requested resource not found")
		{
			die(json_encode(array(
						"success" => false,
						"message" => "Order not found"
			)));
		}
		else
		{
			// success("Order from " . $json->email . " for $" . $json->price . " was found");
			die(json_encode(array(
						"success" => true,
						"message" => "Order from " . $json->email . " for $" . $json->price . " was found"
			)));
		}
	case 'checkemail':
		$email = sanitize($_GET['email']);
		$result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `email` = '$email'");

		if(mysqli_num_rows($result) == 0)
		{
			die(json_encode(array(
						"success" => false,
						"message" => "No acccount found with email"
			)));
		}
	
        $row = mysqli_fetch_array($result);

        $un = $row['username'];
		$ban = $row['banned'] == NULL ? 'false' : 'true';
		$totp = (($row['twofactor'] ? 1 : 0) ? 'enabled' : 'disabled');
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account successfully found",
						"username" => "$un",
						"banned" => "$ban",
						"totp" => "$totp"
		)));
	case 'checkun':
		$username = sanitize($_GET['username']);
		$result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'");

		if(mysqli_num_rows($result) == 0)
		{
			die(json_encode(array(
						"success" => false,
						"message" => "No acccount found with username"
			)));
		}
	
        $row = mysqli_fetch_array($result);

        $email = $row['email'];
		$ban = $row['banned'] == NULL ? 'false' : 'true';
		$totp = (($row['twofactor'] ? 1 : 0) ? 'enabled' : 'disabled');
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account successfully found",
						"email" => "$email",
						"banned" => "$ban",
						"totp" => "$totp"
		)));
	case 'banacc':
		$un = sanitize($_GET['username']);
        $reason = sanitize($_GET['reason']);

        mysqli_query($link, "UPDATE `accounts` SET `banned` = '$reason' WHERE `username` = '$un'"); // set account to banned
        mysqli_query($link, "UPDATE `apps` SET `banned` = '1' WHERE `owner` = '$un'"); // ban all apps owned by account
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account successfully banned"
		)));
	case 'unbanacc':
        $un = sanitize($_GET['username']);
        $reason = sanitize($_GET['reason']);

        mysqli_query($link, "UPDATE `accounts` SET `banned` = NULL WHERE `username` = '$un'"); // set account to not banned
        mysqli_query($link, "UPDATE `apps` SET `banned` = '0' WHERE `owner` = '$un'"); // unban all apps owned by account
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account successfully unbanned"
		)));
	case 'saveemail':
        $un = sanitize($_GET['username']);
        $email = sanitize($_GET['email']);

        mysqli_query($link, "UPDATE `accounts` SET `email` = '$email' WHERE `username` = '$un'");
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account email updated"
		)));
    case 'upgrade':
        $un = sanitize($_GET['username']);
        $role = sanitize($_GET['role']);
		
		switch($role)
		{
			case 'tester':
				break;
			case 'developer':
				break;
			case 'seller':
				break;
			default:
				die(json_encode(array(
						"success" => false,
						"message" => "Role doesn't exist"
				)));
		}

        mysqli_query($link, "UPDATE `accounts` SET `role` = '$role' WHERE `username` = '$un'");
		
		die(json_encode(array(
						"success" => true,
						"message" => "Account successfully upgraded"
		)));
    case 'enabletotp':
        $un = sanitize($_GET['username']);

        mysqli_query($link, "UPDATE `accounts` SET `twofactor` = 1 WHERE `username` = '$un'");
		
		die(json_encode(array(
						"success" => true,
						"message" => "Two-factor enabled"
		)));
	case 'disabletotp':
		$un = sanitize($_GET['username']);

        mysqli_query($link, "UPDATE `accounts` SET `twofactor` = 0 WHERE `username` = '$un'");
		
		die(json_encode(array(
						"success" => true,
						"message" => "Two-factor enabled"
		)));
	case 'applookup':
		$name = sanitize($_GET['name']);
		$owner = sanitize($_GET['owner']);

		$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$name' AND `owner` = '$owner'");
		
		if (mysqli_num_rows($result) === 0)
		{
			die(json_encode(array(
						"success" => false,
						"message" => "No application found"
			)));
		}
		
		$row = mysqli_fetch_array($result);
		die(json_encode(array(
					"success" => false,
					"message" => "Application successfully retrieved",
					"secret" => "".$row["secret"]."",
					"ownerid" => "".$row["ownerid"].""
		)));
    }
?>