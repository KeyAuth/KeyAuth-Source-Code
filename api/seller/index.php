<?php
ini_set('display_errors', 'Off');
error_reporting(0);
include '../../includes/connection.php';
include '../../includes/functions.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$key = strip_tags(trim(mysqli_real_escape_string($link, $_GET['key'])));
$user = strip_tags(trim(mysqli_real_escape_string($link, $_GET['user'])));
$sellerkey = strip_tags(trim(mysqli_real_escape_string($link, $_GET['sellerkey'])));
$format = strip_tags(trim(mysqli_real_escape_string($link, $_GET['format'])));

function license_masking($mask) // the license masking function, should be in one file used by both dashboard and API, but here we are

{
    $mask_arr = str_split($mask);
    $size_of_mask = count($mask_arr);
    for ($i = 0;$i < $size_of_mask;$i++)
    {
        if ($mask_arr[$i] === 'X')
        {
            $mask_arr[$i] = random_string_upper(1);
        }
        else if ($mask_arr[$i] === 'x')
        {
            $mask_arr[$i] = random_string_lower(1);
        }
    }
    return implode('', $mask_arr);
}

function license($amount, $mask, $expiry, $level, $link, $secret)
{

    $licenses = array();

    for ($i = 0;$i < $amount;$i++)
    {

        $license = license_masking($mask);
        mysqli_query($link, "INSERT INTO `keys` (`key`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$license','$expiry','Not Used','$level','SellerAPI', '" . time() . "', '$secret')");
        // echo $key;
        $licenses[] = $license;
    }

    return $licenses;
}

if (empty($sellerkey))
{
    mysqli_close($link);
    if ($format == "text")
    {
        die("Seller Key Not Set");
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "Seller Key Not Set"
        )));
    }
}

$type = strip_tags(trim(mysqli_real_escape_string($link, $_GET['type'])));
if (!$type)
{
    mysqli_close($link);
    if ($format == "text")
    {
        die("Type not defined");
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "Type not defined"
        )));
    }
}


$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `sellerkey` = '$sellerkey'");

$num = mysqli_num_rows($result);

if ($num == 0)
{
    http_response_code(404);
    mysqli_close($link);
    if ($format == "text")
    {
        die("Seller Key Not Found");
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "Seller Key Not Found"
        )));
    }
}

	$row = mysqli_fetch_array($result);
	
    $secret = $row['secret'];
    $owner = $row['owner'];
    $banned = $row['banned'];

if($banned)
{
	http_response_code(403);
    mysqli_close($link);
    if ($format == "text")
    {
        die("This application has been banned from KeyAuth.com for violating terms.");
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "This application has been banned from KeyAuth.com for violating terms."
        )));
    }
}

$seller_check = mysqli_query($link, "SELECT `role` FROM `accounts` WHERE `username` = '$owner'");
$sellrow = mysqli_fetch_array($seller_check);

$role = $sellrow["role"];

if ($role !== "seller")
{
    http_response_code(403);
    mysqli_close($link);
    if ($format == "text")
    {
        die("Not authorized to use SellerAPI, please upgrade.");
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "Not authorized to use SellerAPI, please upgrade."
        )));
    }
}

switch ($type)
{
    case 'add':
        $expiry = strip_tags(trim(mysqli_real_escape_string($link, $_GET['expiry'])));
        $level = strip_tags(trim(mysqli_real_escape_string($link, $_GET['level'])));
        $amount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['amount'])));

        if ($expiry == NULL)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("Expiry Not Set");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Expiry Not Set"
                )));
            }
        }

        if (!isset($amount))
        {
            $amount = "1";
        }
        if (!is_numeric($amount))
        {
            $amount = "1";
        }
        if ($amount > 50)
        {
            http_response_code(403);
            mysqli_close($link);
            if ($format == "text")
            {
                die("You can't generate more than 50 keys");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "You can't generate more than 50 keys"
                )));
            }
        }

        if (!isset($level))
        {
            $level = "1";
        }
        if (!is_numeric($level))
        {
            $level = "1";
        }

        $time = time();

        $mask = strip_tags(trim(mysqli_real_escape_string($link, $_GET['mask'])));
        if (empty($mask))
        {
            $mask = "XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX";
        }
        if (!is_numeric($level))
        {
            $mask = "XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX";
        }
        $expiry = $expiry * 86400;
        $key = license($amount, $mask, $expiry, $level, $link, $secret);
        // license shit here
        if ($amount > 1)
        {
            if ($format == "text")
            {
                $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `gendate` >= '$time' AND `app` = '$secret'");
                while ($row = mysqli_fetch_array($result)) echo $row['key'] . "\n";
				break;
            }
            else
            {
                http_response_code(302);
                //$response = array("success" => false,"message" => "Keys Successfully Generated", "keys" => $key);
                mysqli_close($link);
                die(json_encode(array(
                    "success" => true,
                    "message" => "Keys Successfully Generated",
                    "keys" => $key
                )));
                // die($key);
                //echo '<pre>'; print_r($key); echo '</pre>';
                
            }
        }
        else
        {
            if ($format == "text")
            {
                die(array_values($key) [0]);
            }
            else
            {
                mysqli_close($link);
                die(json_encode(array(
                    "success" => true,
                    "message" => "Key Successfully Generated",
                    "key" => array_values($key) [0]
                )));
            }
        }
	case 'setvar':
		$var = sanitize($_GET['var']);
		$data = sanitize($_GET['data']);
		
		if($user == "all")
		{
			$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret'");
			if(mysqli_num_rows($result) == 0)
			{
				http_response_code(406);
				mysqli_close($link);
				if ($format == "text")
				{
					die("You have no users");
				}
				else
				{
					die(json_encode(array(
						"success" => false,
						"message" => "You have no users"
					)));
				}
			}
			
			$rows = array();
			while ($r = mysqli_fetch_assoc($result))
			{
				$rows[] = $r;
			}
		
			foreach ($rows as $row)
			{
				mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '".$row['username']."', '$secret')");
			}
		}
		else
		{
			mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '$user', '$secret')");
		}
		
		if(mysqli_affected_rows($link) != 0)
		{
			mysqli_close($link);
			if ($format == "text")
			{
				die("Set variable for selected user(s)");
			}
			else
			{
				die(json_encode(array(
					"success" => true,
					"message" => "Set variable for selected user(s)"
				)));
			}
		}
		else
		{
			mysqli_close($link);
			if ($format == "text")
			{
				die("Failed To set variable for selected user(s)");
			}
			else
			{
				die(json_encode(array(
					"success" => false,
					"message" => "Failed To set variable for selected user(s)"
				)));
			}
		}
	case 'getvar':
		$var = sanitize($_GET['var']);
		
		$result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `name` = '$var' AND `user` = '$user' AND `app` = '$secret'");
		
		if(mysqli_num_rows($result) == 0)
		{
			http_response_code(404);
			mysqli_close($link);
			if ($format == "text")
			{
				die("Variable not found for user");
			}
			else
			{
				die(json_encode(array(
					"success" => false,
					"message" => "Variable not found for user"
				)));
			}
		}
		
        $row = mysqli_fetch_array($result);
		$data = $row['data'];
		
		mysqli_close($link);
		if ($format == "text")
		{
			die($data);
		}
		else
		{
			die(json_encode(array(
				"success" => true,
				"message" => "Successfully retrieved variable",
				"response" => $data
			)));
		}
	case 'fetchallblacks':
		$result = mysqli_query($link, "SELECT `hwid`, `ip`, `type` FROM `bans` WHERE `app` = '$secret'");
	
		if (mysqli_num_rows($result) == 0)
		{
			http_response_code(406);
			mysqli_close($link);
			Die(json_encode(array(
				"success" => false,
				"message" => "No blacklists found"
			)));
		}
	
		$rows = array();
		while ($r = mysqli_fetch_assoc($result))
		{
			$rows[] = $r;
		}
		mysqli_close($link);
		die(json_encode(array(
			"success" => true,
			"message" => "Successfully retrieved blacklists",
			"subs" => $rows
		)));
	case 'fetchallsubs':
		$result = mysqli_query($link, "SELECT `name`, `level` FROM `subscriptions` WHERE `app` = '$secret'");
	
		if (mysqli_num_rows($result) == 0)
		{
			http_response_code(406);
			mysqli_close($link);
			Die(json_encode(array(
				"success" => false,
				"message" => "No subscriptions found"
			)));
		}
	
		$rows = array();
		while ($r = mysqli_fetch_assoc($result))
		{
			$rows[] = $r;
		}
		mysqli_close($link);
		die(json_encode(array(
			"success" => true,
			"message" => "Successfully retrieved subscriptions",
			"subs" => $rows
		)));
	case 'fetchalluservars':
		$result = mysqli_query($link, "SELECT `name`, `data`, `user` FROM `uservars` WHERE `app` = '$secret'");
	
		if (mysqli_num_rows($result) == 0)
		{
			http_response_code(406);
			mysqli_close($link);
			Die(json_encode(array(
				"success" => false,
				"message" => "No user variables Found"
			)));
		}
	
		$rows = array();
		while ($r = mysqli_fetch_assoc($result))
		{
			$rows[] = $r;
		}
		mysqli_close($link);
		die(json_encode(array(
			"success" => true,
			"message" => "Successfully retrieved user variables",
			"vars" => $rows
		)));
	case 'fetchallfiles':
        $result = mysqli_query($link, "SELECT `id`, `url` FROM `files` WHERE `app` = '$secret'");

        if (mysqli_num_rows($result) == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            Die(json_encode(array(
                "success" => false,
                "message" => "No files Found"
            )));
        }

        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }
        mysqli_close($link);
        die(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved files",
            "files" => $rows
        )));
	case 'fetchallvars':
        $result = mysqli_query($link, "SELECT `varid`, `msg` FROM `vars` WHERE `app` = '$secret'");

        if (mysqli_num_rows($result) == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            Die(json_encode(array(
                "success" => false,
                "message" => "No variables Found"
            )));
        }

        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }
        mysqli_close($link);
        die(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved variables",
            "vars" => $rows
        )));
    case 'addvar':
        $name = strip_tags(trim(mysqli_real_escape_string($link, $_GET['name'])));
        $data = strip_tags(trim(mysqli_real_escape_string($link, $_GET['data'])));

        if (empty($name) || empty($data))
        {
            mysqli_close($link);
            http_response_code(406);
            if ($format == "text")
            {
                die("Name or Data Fields Not Defined");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Name or Data Fields Not Defined"
                )));
            }
        }
        mysqli_query($link, "INSERT INTO `vars`(`varid`, `msg`, `app`) VALUES ('$name','$data','$secret')");

        mysqli_close($link);
        if ($format == "text")
        {
            die("Variable Addition Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Variable Addition Successful"
            )));
        }
    case 'addsub':
        $name = strip_tags(trim(mysqli_real_escape_string($link, $_GET['name'])));
        $level = strip_tags(trim(mysqli_real_escape_string($link, $_GET['level'])));

        if (empty($name) || empty($level))
        {
            mysqli_close($link);
            http_response_code(406);
            if ($format == "text")
            {
                die("Level or Subscription Name Not Defined");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Level or Subscription Name Not Defined"
                )));
            }
        }
        mysqli_query($link, "INSERT INTO `subscriptions`(`name`, `level`, `app`) VALUES ('$name','$level','$secret')");

        mysqli_close($link);
        if ($format == "text")
        {
            die("Subscription Addition Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Subscription Addition Successful"
            )));
        }
	case 'black':
		$ipaddr = $_GET['ip'] ?? $ip;
		$ipaddr = sanitize($ipaddr);
		
		$hwid = sanitize($_GET['hwid']);
        if (!empty($hwid))
        {
            mysqli_query($link, "INSERT INTO `bans` (`hwid`, `type`, `app`) VALUES ('$hwid','hwid', '$secret')");
        }

        mysqli_query($link, "INSERT INTO `bans` (`ip`, `type`, `app`) VALUES ('$ipaddr','ip', '$secret')");
		
        mysqli_close($link);
        if ($format == "text")
        {
            die("Blacklist Addition Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Blacklist Addition Successful"
            )));
        }
	case 'activate':
		
		include '../../includes/api/1.0/index.php'; // v1.0 api funcs

		$pass = strip_tags(trim(mysqli_real_escape_string($link, $_GET['pass'])));
		$hwid = strip_tags(trim(mysqli_real_escape_string($link, $_GET['hwid'])));

		$resp = register($user, $key, $pass, $hwid, $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(json_encode(array(
                    "success" => false,
                    "message" => "Username Already Exists."
                )));
            case 'key_not_found':
                die(json_encode(array(
                    "success" => false,
                    "message" => "Key Not Found."
                )));
            case 'key_already_used':
                die(json_encode(array(
                    "success" => false,
                    "message" => "Key Already Used."
                )));
            case 'key_banned':
                global $banned;
                die(json_encode(array(
                    "success" => false,
                    "message" => "Your license is banned."
                )));
            case 'hwid_blacked':
                die(json_encode(array(
                    "success" => false,
                    "message" => "HWID is blacklisted"
                )));
            case 'no_subs_for_level':
                die(json_encode(array(
                    "success" => false,
                    "message" => "No active subscriptions found."
                )));
            default:
                die(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$user",
                        "subscriptions" => $resp,
                        "ip" => $_SERVER["HTTP_X_FORWARDED_FOR"]
                    )
                )));
        }
	case 'resetpw':
        mysqli_query($link, "UPDATE `users` SET `password` = NULL WHERE `username` = '$user' AND `app` = '$secret'");

	if (mysqli_affected_rows($link) != 0)
	{
		mysqli_close($link);
        if ($format == "text")
		{
			die("Password reset successful");
		}
		else
		{
			die(json_encode(array(
				"success" => true,
				"message" => "Password reset successful"
			)));
		}
	}
	else
	{
		http_response_code(500);
		mysqli_close($link);
        if ($format == "text")
        {
            die("Failed To reset password");
        }
        else
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Failed To reset password"
            )));
        }
	}	
    case 'editvar':
        $varid = strip_tags(trim(mysqli_real_escape_string($link, $_GET['varid'])));
        $data = strip_tags(trim(mysqli_real_escape_string($link, $_GET['data'])));
        mysqli_query($link, "UPDATE `vars` SET `msg` = '$data' WHERE `varid` = '$varid' AND `app` = '$secret'");

        mysqli_close($link);
        if ($format == "text")
        {
            die("Variable Edit Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Variable Edit Successful"
            )));
        }
    case 'stats':
        $unusedquery = mysqli_query($link, "SELECT count(1) FROM `keys` WHERE `app` = '$secret' AND `status` = 'Not Used'");
        $row = mysqli_fetch_array($unusedquery);
        $unused = $row[0];

        $usedquery = mysqli_query($link, "SELECT count(1) FROM `keys` WHERE `app` = '$secret' AND `status` = 'Used'");
        $row = mysqli_fetch_array($usedquery);
        $used = $row[0];

        $pausedquery = mysqli_query($link, "SELECT count(1) FROM `keys` WHERE `app` = '$secret' AND `status` = 'Paused'");
        $row = mysqli_fetch_array($pausedquery);
        $paused = $row[0];

        $bannedquery = mysqli_query($link, "SELECT count(1) FROM `keys` WHERE `app` = '$secret' AND `status` = 'Banned'");
        $row = mysqli_fetch_array($bannedquery);
        $banned = $row[0];

        $totalkeys = $unused + $used + $paused + $banned;

        $webhooksquery = mysqli_query($link, "SELECT count(1) FROM `webhooks` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($webhooksquery);
        $webhooks = $row[0];

        $filesquery = mysqli_query($link, "SELECT count(1) FROM `files` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($filesquery);
        $files = $row[0];

        $varsquery = mysqli_query($link, "SELECT count(1) FROM `vars` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($varsquery);
        $vars = $row[0];

        $resellersquery = mysqli_query($link, "SELECT count(1) FROM `accounts` WHERE `app` = '$secret' AND `role` = 'Reseller'");
        $row = mysqli_fetch_array($resellersquery);
        $resellers = $row[0];

        $managersquery = mysqli_query($link, "SELECT count(1) FROM `accounts` WHERE `app` = '$secret' AND `role` = 'Manager'");
        $row = mysqli_fetch_array($managersquery);
        $managers = $row[0];

        $totalaccs = $resellers + $managers;

        Die(json_encode(array(
            "success" => true,
            "unused" => "$unused",
            "used" => "$used",
            "paused" => "$paused",
            "banned" => "$banned",
            "totalkeys" => "$totalkeys",
            "webhooks" => "$webhooks",
            "files" => "$files",
            "vars" => "$vars",
            "resellers" => "$resellers",
            "managers" => "$managers",
            "totalaccs" => "$totalaccs"
        )));
    case 'addhwid':
        die("Endpoint Deprecated, you can no longer use keys directly. A user is created from the key, and that user has a HWID and IP associated with it.");
    case 'addhwiduser':
        $hwid = strip_tags(trim(mysqli_real_escape_string($link, $_GET['hwid'])));
        $result = mysqli_query($link, "SELECT `hwid` FROM `users` WHERE `username` = '$user' AND `app` = '$secret'");
        $row = mysqli_fetch_array($result);
        $hwidd = $row["hwid"];

        $hwidd = $hwidd .= $hwid;

        mysqli_query($link, "UPDATE `users` SET `hwid` = '$hwidd' WHERE `username` = '$user' AND `app` = '$secret'");

        die("Added HWID");
    case 'extend':
		$name = strip_tags(trim(mysqli_real_escape_string($link, $_GET['name'])));
        $subquery = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '$secret' AND `name` = '$name'");

        $subcount = mysqli_num_rows($subquery);

        if ($subcount == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("No Subscriptions With That Name");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "No Subscriptions With That Name"
                )));
            }
        }
        
		$expiry = strip_tags(trim(mysqli_real_escape_string($link, $_GET['expiry'])));
		$expiry = ($expiry * 86400) + time();
		
		mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$user','$name', '$expiry', '$secret')");
		if (mysqli_affected_rows($link) != 0)
		{
			mysqli_close($link);
            if ($format == "text")
            {
                die("Successfully Extended User");
            }
            else
            {
                die(json_encode(array(
                    "success" => true,
                    "message" => "Successfully Extended User"
                )));
            }
		}
		else
		{
			http_response_code(500);
			mysqli_close($link);
            if ($format == "text")
            {
                die("Failed To Extend User");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Failed To Extend User"
                )));
            }
		}
    case 'extendall':
		$name = strip_tags(trim(mysqli_real_escape_string($link, $_GET['name'])));
        $subquery = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '$secret' AND `name` = '$name'");

        $subcount = mysqli_num_rows($subquery);

        if ($subcount == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("No Subscriptions With That Name");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "No Subscriptions With That Name"
                )));
            }
        }
        
		$expiry = strip_tags(trim(mysqli_real_escape_string($link, $_GET['expiry'])));
		$expiry = ($expiry * 86400) + time();
		
		$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret'");
		if(mysqli_num_rows($result) == 0)
		{
			http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("No users found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "No users found"
                )));
            }
		}
		
		$rows = array();
		while ($r = mysqli_fetch_assoc($result))
		{
			$rows[] = $r;
		}
		
		foreach ($rows as $row)
		{
			mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('".$row['username']."','$name', '$expiry', '$secret')");
		}
		
		if (mysqli_affected_rows($link) != 0)
		{
			mysqli_close($link);
            if ($format == "text")
            {
                die("Successfully Extended All Users");
            }
            else
            {
                die(json_encode(array(
                    "success" => true,
                    "message" => "Successfully Extended All Users"
                )));
            }
		}
		else
		{
			http_response_code(500);
			mysqli_close($link);
            if ($format == "text")
            {
                die("Failed To Extend All Users");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Failed To Extend All Users"
                )));
            }
		}
    case 'verify':
        $keyquery = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '$secret' AND `key` = '$key'");

        $keycount = mysqli_num_rows($keyquery);

        if ($keycount == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("Key Not Found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Key Not Found"
                )));
            }
        }
        else
        {
            if ($format == "text")
            {
                die("Key Successfully Verified");
            }
            else
            {
                die(json_encode(array(
                    "success" => true,
                    "message" => "Key Successfully Verified"
                )));
            }
        }
    case 'verifyuser':
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret' AND `username` = '$user'");

        if (mysqli_num_rows($result) == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("User Not Found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "User Not Found"
                )));
            }
        }
        else
        {
            if ($format == "text")
            {
                die("User Successfully Verified");
            }
            else
            {
                die(json_encode(array(
                    "success" => true,
                    "message" => "User Successfully Verified"
                )));
            }
        }
    case 'del':
        $keyquery = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '$secret' AND `key` = '$key'");

        $keycount = mysqli_num_rows($keyquery);

        if ($keycount == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("Key Not Found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Key Not Found"
                )));
            }
        }

        $result = mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$secret' AND `key` = '$key'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted License");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted License"
            )));
        }
    case 'delunused':
        $result = mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$secret' AND `status` = 'Not Used'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted Unused Licenses");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted Unused Licenses"
            )));
        }
    case 'delexp':
        $ye = time();
        $result = mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$secret' AND `status` != 'Not Used' AND `expires` < " . $ye . "");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted Expired Licenses");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted Expired Licenses"
            )));
        }
    case 'delexpusers':
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret'");
		if(mysqli_num_rows($result) == 0)
		{
			http_response_code(406);
			mysqli_close($link);
			if ($format == "text")
            {
                die("You have no users");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "You have no users"
                )));
            }
		}
		
		$rows = array();
		while ($r = mysqli_fetch_assoc($result))
		{
			$rows[] = $r;
		}
		
		$success = 0;
		foreach ($rows as $row)
		{
			$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `user` = '".$row['username']."' AND `app` = '$secret' AND `expiry` > '".time()."'");
			if(mysqli_num_rows($result) == 0)
			{
				$success = 1;
				mysqli_query($link, "DELETE FROM `users` WHERE `app` = '$secret' AND `username` = '".$row['username']."'");
			}
		}
		if($success)
		{
			if ($format == "text")
			{
				die("Expired Users Successfully Deleted");
			}
			else
			{
				die(json_encode(array(
					"success" => true,
					"message" => "Expired Users Successfully Deleted"
				)));
			}
		}
		else
		{
			if ($format == "text")
			{
				die("No users are expired");
			}
			else
			{
				die(json_encode(array(
					"success" => true,
					"message" => "No users are expired"
				)));
			}
		}
	case 'deluser':
        $usrquery = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret' AND `username` = '$user'");

        $usrcount = mysqli_num_rows($usrquery);

        if ($usrcount == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            if ($format == "text")
            {
                die("User Not Found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "User Not Found"
                )));
            }
        }

        $result = mysqli_query($link, "DELETE FROM `users` WHERE `app` = '$secret' AND `username` = '$user'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted User");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted User"
            )));
        }
    case 'delalllicenses':
        $ye = time();
        $result = mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$secret'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted All Licenses");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted All Licenses"
            )));
        }
    case 'delallvars':
        $ye = time();
        $result = mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '$secret'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Deleted All Variables");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Deleted All Variables"
            )));
        }
    case 'reset':
		die("Endpoint Deprecated, you can no longer use keys directly. A user is created from the key, and that user has a HWID and IP associated with it.");
    case 'resetuser':
        $userquery = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret' AND `username` = '$user'");

        $usercount = mysqli_num_rows($userquery);

        if ($usercount == 0)
        {
            mysqli_close($link);
            if ($format == "text")
            {
                die("User Not Found");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "User Not Found"
                )));
            }
        }

        mysqli_query($link, "UPDATE `users` SET `hwid` = '' WHERE `app` = '$secret' AND `username` = '$user'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Reset User");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Reset User"
            )));
        }
    case 'upload':
        $url = strip_tags(trim(mysqli_real_escape_string($link, $_GET['url'])));

        if (!filter_var($url, FILTER_VALIDATE_URL)) { 
            mysqli_close($link);
            if ($format == "text")
            {
                die("URL is invalid");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "URL is invalid"
                )));
            }
		}
		
        $file = file_get_contents($url);

		$filesize = strlen($file);
		
		if($filesize > 50000000)
		{
			error("File size limit is 50 MB.");
			return;
		}
		
		$id = generateRandomNum();
		$fn = basename($url);
		$fs = formatBytes($filesize);
                
        mysqli_query($link, "INSERT INTO `files` (name, id, url, size, uploaddate, app) VALUES ('$fn', '$id', '$url', '$fs', '".time()."', '$secret')");

        mysqli_close($link);
        if ($format == "text")
        {
            die("File ID " . $id . " Uploaded Successfully");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "File ID $id Uploaded Successfully"
            )));
        }

    case 'ban':
        $reason = strip_tags(trim(mysqli_real_escape_string($link, $_GET['reason'])));

        mysqli_query($link, "UPDATE `keys` SET `banned` = '$reason', `status` = 'Banned' WHERE `app` = '$secret' AND `key` = '$key'");

        mysqli_close($link);
        if ($format == "text")
        {
            die("Successfully Banned License");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Banned License"
            )));
        }
    case 'fetchallkeys':
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '$secret'");

        $num = mysqli_num_rows($result);

        if ($num == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            Die(json_encode(array(
                "success" => false,
                "message" => "No License Keys Found"
            )));
        }

        if ($format == "text")
        {
            $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '$secret'");

            while ($row = mysqli_fetch_array($result))
            {
                $stringData .= "" . $row['key'] . "\n";
            }
            $remove = substr($stringData, 0, -2);
            echo $remove;
			break;
        }
        else
        {
            $rows = array();
            while ($r = mysqli_fetch_assoc($result))
            {
                $rows[] = $r;
            }
            mysqli_close($link);
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Retrieved Licenses",
                "keys" => $rows
            )));
        }
    case 'fetchallusers':
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret'");

        $num = mysqli_num_rows($result);

        if ($num == 0)
        {
            http_response_code(406);
            mysqli_close($link);
            Die(json_encode(array(
                "success" => false,
                "message" => "No Users Found"
            )));
        }

        if ($format == "text")
        {
            $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '$secret'");

            while ($row = mysqli_fetch_array($result))
            {
                $stringData .= "" . $row['username'] . "\n";
            }
            $remove = substr($stringData, 0, -2);
            echo $remove;
			break;
        }
        else
        {
            $rows = array();
            while ($r = mysqli_fetch_assoc($result))
            {
                $rows[] = $r;
            }
            mysqli_close($link);
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Retrieved Users",
                "users" => $rows
            )));
        }
    case 'setseller':
        mysqli_close($link);
        if ($format == "text")
        {
            die("Seller Key Successfully Found");
        }
        else
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Seller Key Successfully Found"
            )));
        }
    case 'balance':
        $username = strip_tags(trim(mysqli_real_escape_string($link, $_GET['username'])));
        if (empty($username))
        {
            mysqli_close($link);
            if ($format == "text")
            {
                die("Username Not Set");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Username Not Set"
                )));
            }
        }

        $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `app` = '$name' AND `username` = '$username'");
        if ($result->num_rows == 0)
        {
            mysqli_close($link);
            if ($format == "text")
            {
                die("You don't own account you were attemping to modify balance for");
            }
            else
            {
                die(json_encode(array(
                    "success" => false,
                    "message" => "You don't own account you were attemping to modify balance for"
                )));
            }
        }

        $dayamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['day'])));
        $weekamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['week'])));
        $monthamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['month'])));
        $threemonthamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['threemonth'])));
        $sixmonthamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['sixmonth'])));
        $lifetimeamount = strip_tags(trim(mysqli_real_escape_string($link, $_GET['lifetime'])));

        if (!isset($dayamount))
        {
            $dayamount = "0";
        }
        if (!isset($weekamount))
        {
            $weekamount = "0";
        }
        if (!isset($monthamount))
        {
            $monthamount = "0";
        }
        if (!isset($threemonthamount))
        {
            $threemonthamount = "0";
        }
        if (!isset($sixmonthamount))
        {
            $sixmonthamount = "0";
        }
        if (!isset($lifetimeamount))
        {
            $lifetimeamount = "0";
        }

        $result = mysqli_query($link, "SELECT `balance` FROM `accounts` WHERE `username` = '$username'");

        $row = mysqli_fetch_array($result);

        $balance = $row["balance"];

        $balance = explode("|", $balance);

        $day = $balance[0];
        $week = $balance[1];
        $month = $balance[2];
        $threemonth = $balance[3];
        $sixmonth = $balance[4];
        $lifetime = $balance[5];

        $day = $day + $dayamount;

        $week = $week + $weekamount;

        $month = $month + $monthamount;

        $threemonth = $threemonth + $threemonthamount;

        $sixmonth = $sixmonth + $sixmonthamount;

        $lifetime = $lifetime + $lifetimeamount;

        $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;

        mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '$username'");

        mysqli_close($link);
        if ($format == "text")
        {
            die("Balance Successfully Added");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Balance Successfully Added"
            )));
        }
		case 'usersub':
		$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '$secret' AND `user` = '$user'");
	
		$num = mysqli_num_rows($result);
	
		if ($num == 0)
		{
			http_response_code(406);
			mysqli_close($link);
			Die(json_encode(array(
				"success" => false,
				"message" => "No Subscriptions Found" // in theory this should not happen
			)));
		}
	
		if ($format == "text")
		{
		// $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '$secret' AND `user` = '$user'"); // this is not needed as results already has a value so we dont need to get it again
	
			while ($row = mysqli_fetch_array($result))
			{
				$stringData .= "" . $row['user'] . " " . $row['subscription'] . " " . $row['expiry'] . " " . $row['key'] . "\n"; 
			}
			$remove = substr($stringData, 0, -2);
			echo $remove;
			break;
		}
		else
		{
			$rows = array();
			while ($r = mysqli_fetch_assoc($result))
			{
				$rows[] = $r;
			}
			mysqli_close($link);
			die(json_encode(array(
				"success" => true,
				"message" => "Successfully Retrieved User Subscription",
				"subs" => $rows
			)));
		}
    case 'getsettings':
        if ($row["enabled"] == 0)
        {
            $enabled = false;
        }
        else
        {
            $enabled = true;
        }

        if ($row["hwidcheck"] == 0)
        {
            $hwidcheck = false;
        }
        else
        {
            $hwidcheck = true;
        }
        $ver = $row["ver"];
        $download = $row["download"];
        $webhook = $row["webhook"];
        $resellerstore = $row["resellerstore"];
        $appdisabled = $row["appdisabled"];
        $usernametaken = $row["usernametaken"];
        $keynotfound = $row["keynotfound"];
        $keyused = $row["keyused"];
        $nosublevel = $row["nosublevel"];
        $usernamenotfound = $row["usernamenotfound"];
        $passmismatch = $row["passmismatch"];
        $hwidmismatch = $row["hwidmismatch"];
        $noactivesubs = $row["noactivesubs"];
        $hwidblacked = $row["hwidblacked"];
        $keypaused = $row["keypaused"];
        $keyexpired = $row["keyexpired"];
        $sellixsecret = $row["sellixsecret"];
        $dayproduct = $row["dayproduct"];
        $weekproduct = $row["weekproduct"];
        $monthproduct = $row["monthproduct"];
        $lifetimeproduct = $row["lifetimeproduct"];

        mysqli_close($link);
        if ($format == "text")
        {
            echo $enabled ? 'true' : 'false';
            echo "\n";
            echo $hwidcheck ? 'true' : 'false' . "\n";
            echo $ver . "\n";
            echo $download . "\n";
            echo $webhook . "\n";
            echo $resellerstore . "\n";
            echo $appdisabled . "\n";
            echo $usernametaken . "\n";
            echo $keynotfound . "\n";
            echo $keyused . "\n";
            echo $nosublevel . "\n";
            echo $usernamenotfound . "\n";
            echo $passmismatch . "\n";
            echo $hwidmismatch . "\n";
            echo $noactivesubs . "\n";
            echo $hwidblacked . "\n";
            echo $keypaused . "\n";
            echo $keyexpired . "\n";
            echo $sellixsecret . "\n";
            echo $dayproduct . "\n";
            echo $weekproduct . "\n";
            echo $monthproduct . "\n";
            echo $lifetimeproduct;
			break;
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Retrieved Settings Successfully",
                "enabled" => $enabled,
                "hwid-lock" => $hwidcheck,
                "version" => "$ver",
                "download" => "$download",
                "webhook" => "$webhook",
                "resellerstore" => "$resellerstore",
                "disabledmsg" => "$appdisabled",
                "usernametakenmsg" => "$usernametaken",
                "licenseinvalidmsg" => "$keynotfound",
                "keytakenmsg" => "$keyused",
                "nosubmsg" => "$nosublevel",
                "userinvalidmsg" => "$usernamenotfound",
                "passinvalidmsg" => "$passmismatch",
                "hwidmismatchmsg" => "$hwidmismatch",
                "noactivesubmsg" => "$noactivesubs",
                "blackedmsg" => "$hwidblacked",
                "pausedmsg" => "$keypaused",
                "expiredmsg" => "$keyexpired",
                "sellixsecret" => "$sellixsecret",
                "dayresellerproductid" => "$dayproduct",
                "weekresellerproductid" => "$weekproduct",
                "monthresellerproductid" => "$monthproduct",
                "liferesellerproductid" => "$lifetimeproduct"
            )));
        }
    case 'info':
        $resultt = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '$secret' AND `key` = '$key'");
        $numm = mysqli_num_rows($resultt);

        if ($numm == 0)
        {
            mysqli_close($link);
            Die(json_encode(array(
                "success" => false,
                "message" => "Key Not Found"
            )));
        }

        while ($roww = mysqli_fetch_array($resultt))
        {

            if ($roww["status"] == "Not Used")
            {
                mysqli_close($link);
                Die(json_encode(array(
                    "success" => false,
                    "message" => "Key Not Used"
                )));
            }
            $expiry = date('jS F Y h:i:s A (T)', $roww["expires"]);
            $lastlogin = date('jS F Y h:i:s A (T)', $roww["lastlogin"]);
            $hwid = $roww["hwid"];
            $status = $roww["status"];
            $level = $roww["level"];
            $genby = $roww["genby"];
            $usedby = $roww["usedby"];
            $gendate = date('jS F Y h:i:s A (T)', $roww["gendate"]);
            $ip = $roww["ip"];
            Die(json_encode(array(
                "success" => true,
                "expiry" => "$expiry",
                "lastlogin" => "$lastlogin",
                "hwid" => "$hwid",
                "status" => "$status",
                "level" => "$level",
                "createdby" => "$genby",
                "usedby" => "$usedby",
                "creationdate" => "$gendate",
                "ip" => "$ip"
            )));
        }

    case 'updatesettings':
        $enabled = strip_tags(trim(mysqli_real_escape_string($link, $_GET['enabled'])));
        $hwidcheck = strip_tags(trim(mysqli_real_escape_string($link, $_GET['hwidcheck'])));
        $ver = strip_tags(trim(mysqli_real_escape_string($link, $_GET['ver'])));
        $download = strip_tags(trim(mysqli_real_escape_string($link, $_GET['download'])));
        $webhook = strip_tags(trim(mysqli_real_escape_string($link, $_GET['webhook'])));
        $resellerstore = strip_tags(trim(mysqli_real_escape_string($link, $_GET['resellerstore'])));
        $appdisabled = strip_tags(trim(mysqli_real_escape_string($link, $_GET['appdisabled'])));
        $usernametaken = strip_tags(trim(mysqli_real_escape_string($link, $_GET['usernametaken'])));
        $keynotfound = strip_tags(trim(mysqli_real_escape_string($link, $_GET['keynotfound'])));
        $keyused = strip_tags(trim(mysqli_real_escape_string($link, $_GET['keyused'])));
        $nosublevel = strip_tags(trim(mysqli_real_escape_string($link, $_GET['nosublevel'])));
        $usernamenotfound = strip_tags(trim(mysqli_real_escape_string($link, $_GET['usernamenotfound'])));
        $passmismatch = strip_tags(trim(mysqli_real_escape_string($link, $_GET['passmismatch'])));
        $hwidmismatch = strip_tags(trim(mysqli_real_escape_string($link, $_GET['hwidmismatch'])));
        $noactivesubs = strip_tags(trim(mysqli_real_escape_string($link, $_GET['noactivesubs'])));
        $hwidblacked = strip_tags(trim(mysqli_real_escape_string($link, $_GET['hwidblacked'])));
        $keypaused = strip_tags(trim(mysqli_real_escape_string($link, $_GET['keypaused'])));
        $keyexpired = strip_tags(trim(mysqli_real_escape_string($link, $_GET['keyexpired'])));
        $sellixsecret = strip_tags(trim(mysqli_real_escape_string($link, $_GET['sellixsecret'])));
        $dayproduct = strip_tags(trim(mysqli_real_escape_string($link, $_GET['dayproduct'])));
        $weekproduct = strip_tags(trim(mysqli_real_escape_string($link, $_GET['weekproduct'])));
        $monthproduct = strip_tags(trim(mysqli_real_escape_string($link, $_GET['monthproduct'])));
        $lifetimeproduct = strip_tags(trim(mysqli_real_escape_string($link, $_GET['lifetimeproduct'])));

        if (!empty($enabled))
        {
            if ($enabled == "true")
            {
                $enabled = 1;
            }
            else if ($enabled == "false")
            {
                $enabled = 0;
            }
            mysqli_query($link, "UPDATE `apps` SET `enabled` = '$enabled' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($hwidcheck))
        {
            if ($hwidcheck == "true")
            {
                $hwidcheck = 1;
            }
            else if ($hwidcheck == "false")
            {
                $hwidcheck = 0;
            }
            mysqli_query($link, "UPDATE `apps` SET `hwidcheck` = '$hwidcheck' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($ver))
        {
            mysqli_query($link, "UPDATE `apps` SET `ver` = '$ver' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($download))
        {
            mysqli_query($link, "UPDATE `apps` SET `ver` = '$ver' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($webhook))
        {
            mysqli_query($link, "UPDATE `apps` SET `webhook` = '$webhook' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($resellerstore))
        {
            mysqli_query($link, "UPDATE `apps` SET `resellerstore` = '$resellerstore' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($appdisabled))
        {
            mysqli_query($link, "UPDATE `apps` SET `appdisabled` = '$appdisabled' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($usernametaken))
        {
            mysqli_query($link, "UPDATE `apps` SET `usernametaken` = '$usernametaken' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($keynotfound))
        {
            mysqli_query($link, "UPDATE `apps` SET `keynotfound` = '$keynotfound' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($keyused))
        {
            mysqli_query($link, "UPDATE `apps` SET `keyused` = '$keyused' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($nosublevel))
        {
            mysqli_query($link, "UPDATE `apps` SET `nosublevel` = '$nosublevel' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($usernamenotfound))
        {
            mysqli_query($link, "UPDATE `apps` SET `usernamenotfound` = '$usernamenotfound' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($passmismatch))
        {
            mysqli_query($link, "UPDATE `apps` SET `passmismatch` = '$passmismatch' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($hwidmismatch))
        {
            mysqli_query($link, "UPDATE `apps` SET `hwidmismatch` = '$hwidmismatch' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($noactivesubs))
        {
            mysqli_query($link, "UPDATE `apps` SET `noactivesubs` = '$noactivesubs' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($hwidblacked))
        {
            mysqli_query($link, "UPDATE `apps` SET `hwidblacked` = '$hwidblacked' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($keypaused))
        {
            mysqli_query($link, "UPDATE `apps` SET `keypaused` = '$keypaused' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($keyexpired))
        {
            mysqli_query($link, "UPDATE `apps` SET `keyexpired` = '$keyexpired' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($sellixsecret))
        {
            mysqli_query($link, "UPDATE `apps` SET `sellixsecret` = '$sellixsecret' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($dayproduct))
        {
            mysqli_query($link, "UPDATE `apps` SET `dayproduct` = '$dayproduct' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($weekproduct))
        {
            mysqli_query($link, "UPDATE `apps` SET `weekproduct` = '$weekproduct' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($monthproduct))
        {
            mysqli_query($link, "UPDATE `apps` SET `monthproduct` = '$monthproduct' WHERE `sellerkey` = '$sellerkey'");
        }
        if (!empty($lifetimeproduct))
        {
            mysqli_query($link, "UPDATE `apps` SET `lifetimeproduct` = '$lifetimeproduct' WHERE `sellerkey` = '$sellerkey'");
        }

        // mysqli_query($link, "UPDATE `keys` SET `expires` = '$expiry' WHERE `app` = '$secret' AND `key` = '$key'");
        mysqli_close($link);
        if ($format == "text")
        {
            die("Settings Update Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Settings Update Successful"
            )));
        }
    case 'edit':
        $expiry = strip_tags(trim(mysqli_real_escape_string($link, $_GET['expiry'])));
        mysqli_query($link, "UPDATE `keys` SET `expires` = '$expiry' WHERE `app` = '$secret' AND `key` = '$key'");

        mysqli_close($link);
        if ($format == "text")
        {
            die("License Edit Successful");
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "License Edit Successful"
            )));
        }
    case 'check':
        $username = strip_tags(trim(mysqli_real_escape_string($link, $_GET['username'])));
        $result = mysqli_query($link, "SELECT `role` FROM `accounts` WHERE `username` = '$username'");

        $row = mysqli_fetch_array($result);
        die($row["role"]);
    default:
        mysqli_close($link);
        if ($format == "text")
        {
            die("Type doesn't exist");
        }
        else
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Type doesn't exist"
            )));
        }
    }
?>
