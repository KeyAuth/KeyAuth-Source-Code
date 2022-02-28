<?php

if(strlen($_GET['sellerkey']) != 32)
{
	http_response_code(404);
    error("Invalid seller key length. Seller key is located in seller settings of dashboard.");
}

include '../../includes/connection.php';
include '../../includes/misc/autoload.phtml';
include '../../includes/api/1.0/autoload.phtml';
include '../../includes/api/shared/autoload.phtml';

$key = misc\etc\sanitize($_GET['key']);
$user = misc\etc\sanitize($_GET['user']);
$sellerkey = misc\etc\sanitize($_GET['sellerkey']);
$format = misc\etc\sanitize($_GET['format']);

function success($message)
{
    global $link;
    mysqli_close($link);
    if ($format == "text")
    {
        die($message);
    }
    else
    {
        die(json_encode(array(
            "success" => true,
            "message" => "$message"
        )));
    }
}
function error($message)
{
    global $link;
    mysqli_close($link);
    if ($format == "text")
    {
        die($message);
    }
    else
    {
        die(json_encode(array(
            "success" => false,
            "message" => "$message"
        )));
    }
}

if (empty($sellerkey))
{
    error("No seller key specified");
}

$type = misc\etc\sanitize($_GET['type']);
if (!$type)
{
    error("Type not specified");
}

$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `sellerkey` = '$sellerkey'");

$num = mysqli_num_rows($result);

if ($num == 0)
{
    http_response_code(404);
    error("No application with specified seller key found");
}

$row = mysqli_fetch_array($result);

$secret = $row['secret'];
$owner = $row['owner'];
$banned = $row['banned'];

if ($banned)
{
    http_response_code(403);
    error("This application has been banned from KeyAuth.com for violating terms");
}

$seller_check = mysqli_query($link, "SELECT `role` FROM `accounts` WHERE `username` = '$owner'");
$sellrow = mysqli_fetch_array($seller_check);

$role = $sellrow["role"];

if ($role !== "seller")
{
    http_response_code(403);
    error("Not authorized to use SellerAPI, please upgrade");
}

switch ($type)
{
    case 'add':
        $expiry = misc\etc\sanitize($_GET['expiry']);
        $level = misc\etc\sanitize($_GET['level']);
		
		$payload = file_get_contents('php://input');
		$json = json_decode($payload);
		$data = $json->data;
        $amount = misc\etc\sanitize($data->quantity) ?? misc\etc\sanitize($json->data->order->quantity) ?? misc\etc\sanitize($_GET['amount']);

        if (is_null($expiry))
        {
            http_response_code(406);
            error("Expiry not set");
        }

	    if (!isset($amount))
        {
            $amount = "1";
        }
        if (!is_numeric($amount))
        {
            $amount = "1";
        }

        if (!isset($level))
        {
            $level = "1";
        }
        if (!is_numeric($level))
        {
            $level = "1";
        }

        $mask = misc\etc\sanitize($_GET['mask']);
        if (empty($mask))
        {
            $mask = "XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX";
        }
        if (!is_numeric($level))
        {
            $mask = "XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX";
        }

        $key = misc\license\createLicense($amount, $mask, $expiry, $level, NULL, 86400, $secret);
        switch ($key)
        {
            case 'max_keys':
                error("You can only generate 100 licenses at a time");
            break;
            case 'dupe_custom_key':
                error("Can't do custom key with amount greater than one");
            break;
            default:
                if ($amount > 1)
                {
                    if ($format == "text")
                    {
                        $keys = NULL;
                        for ($i = 0;$i < count($key);$i++)
                        {
                            $keys .= "" . $key[$i] . "\n";
                        }
                        $keys = preg_replace(

                        '~[\r\n]+~',

                        "\r\n",

                        trim($keys)
);
                        die($keys);
                    }
                    else
                    {
                        http_response_code(302);
                        mysqli_close($link);
                        die(json_encode(array(
                            "success" => true,
                            "message" => "Licenses successfully generated",
                            "keys" => $key
                        )));
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
                            "message" => "License Successfully Generated",
                            "key" => array_values($key) [0]
                        )));
                    }
                }
            break;
        }
    case 'addtime':
        $resp = misc\license\addTime($_GET['time'], 86400, $secret);
        switch ($resp)
        {
            case 'failure':
                http_response_code(500);
                error("Failed to add time!");
            break;
            case 'success':
                success("Added time to unused licenses!");
            break;
            default:
                http_response_code(400);
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'setvar':
        $resp = misc\user\setVariable($user, $_GET['var'], $_GET['data'], $secret);
        switch ($resp)
        {
            case 'missing':
                error("No users found!");
            break;
            case 'failure':
                error("Failed to set variable!");
            break;
            case 'success':
                success("Successfully set variable!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'getvar':
        $var = misc\etc\sanitize($_GET['var']);

        $result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `name` = '$var' AND `user` = '$user' AND `app` = '$secret'");

        if (mysqli_num_rows($result) == 0)
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
        if (!is_numeric($_GET['authed'])) error("Authed paramater must be 1 if you want to require login first, or 0 if you don't want to.");

        $resp = misc\variable\add($_GET['name'], $_GET['data'], $_GET['authed'], $secret);
        switch ($resp)
        {
            case 'exists':
                error("Variable name already exists!");
            break;
            case 'failure':
                error("Failed to create variable!");
            break;
            case 'success':
                success("Successfully created variable!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'addsub':
        $resp = misc\sub\add($_GET['name'], $_GET['level'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to create subscription!");
            break;
            case 'success':
                success("Successfully created subscription!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delappsub':
        $resp = misc\sub\deleteSingular($_GET['name'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete subscription!");
            break;
            case 'success':
                success("Successfully deleted subscription!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'addchannel':
        $resp = misc\chat\createChannel($_GET['name'], $_GET['delay'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to create channel!");
            break;
            case 'success':
                success("Successfully created channel!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delchannel':
        $resp = misc\chat\deleteChannel($_GET['name'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete channel!");
            break;
            case 'success':
                success("Successfully deleted channel!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'clearchannel':
        $resp = misc\chat\clearChannel($_GET['name'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to clear channel!");
            break;
            case 'success':
                success("Successfully cleared channel!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'muteuser':
        if (!is_numeric($_GET['time'])) error("Invalid time paramater, must be number");

        $timeout = $_GET['time'] + time();
        $resp = misc\chat\muteUser($_GET['user'], $timeout, $secret);
        switch ($resp)
        {
            case 'missing':
                error("User doesn't exist!");
            break;
            case 'failure':
                error("Failed to mute user!");
            break;
            case 'success':
                success("Successfully muted user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'unmuteuser':
        $resp = misc\chat\unMuteUser($_GET['user'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to unmute user!");
            break;
            case 'success':
                success("Successfully unmuted user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'kill':
        $resp = misc\session\killSingular($_GET['sessid'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to kill session!");
            break;
            case 'success':
                success("Successfully killed session!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'killall':
        $resp = misc\session\killAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to kill all sessions!");
            break;
            case 'success':
                success("Successfully killed all sessions!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'addwebhook':
        if (!is_numeric($_GET['authed'])) error("Authed paramater must be 1 if you want to require login first, or 0 if you don't want to.");

        $resp = misc\webhook\add($_GET['baseurl'], $_GET['ua'], $_GET['authed'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to add webhook!");
            break;
            case 'success':
                success("Successfully added webhook!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'black':
        $ipaddr = misc\etc\sanitize($_GET['ip']);

        $hwid = misc\etc\sanitize($_GET['hwid']);
        if (!empty($hwid))
        {
            mysqli_query($link, "INSERT INTO `bans` (`hwid`, `type`, `app`) VALUES ('$hwid','hwid', '$secret')");
        }

        if (!empty($ipaddr))
        {
            mysqli_query($link, "INSERT INTO `bans` (`ip`, `type`, `app`) VALUES ('$ipaddr','ip', '$secret')");
        }

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
    case 'delblack':
        $resp = misc\blacklist\deleteSingular($_GET['data'], $_GET['blacktype'], $secret);
        switch ($resp)
        {
            case 'invalid':
                error("Invalid blacklist type!");
            break;
            case 'failure':
                error("Failed to delete blacklist!");
            break;
            case 'success':
                success("Successfully deleted blacklist!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delblacks':
        $resp = misc\blacklist\deleteAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete all blacklists!");
            break;
            case 'success':
                success("Successfully deleted all blacklists!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'activate':
        $pass = misc\etc\sanitize($_GET['pass']);
        $hwid = misc\etc\sanitize($_GET['hwid']);

        $resp = api\v1_0\register($user, $key, $pass, $hwid, $secret);
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
        $passwd = misc\etc\sanitize($_GET['passwd']);
		if(!is_null($passwd))
			$passwd = password_hash($passwd, PASSWORD_BCRYPT);
        mysqli_query($link, "UPDATE `users` SET `password` = NULLIF('$passwd','') WHERE `username` = '$user' AND `app` = '$secret'");

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
    case 'editsub':
        $sub = misc\etc\sanitize($_GET['sub']);
        $level = misc\etc\sanitize($_GET['level']);
        mysqli_query($link, "UPDATE `subscriptions` SET `level` = '$level' WHERE `name` = '$sub' AND `app` = '$secret'");

        if (mysqli_affected_rows($link) > 0)
        {
            success("Subscription successfully edited");
        }
        else
        {
            error("Failed to edit subscription");
        }
    case 'editvar':
        $varid = misc\etc\sanitize($_GET['varid']);
        $data = misc\etc\sanitize($_GET['data']);
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
    case 'retrvvar':
        $name = misc\etc\sanitize($_GET['name']);

        $result = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$name' AND `app` = '$secret'");
        // if not found
        if (mysqli_num_rows($result) === 0)
        {
            error("Variable not found");
        }

        while ($row = mysqli_fetch_array($result))
        {
            $data = $row["msg"];
        }

        die(json_encode(array(
            "success" => true,
            "message" => "$data"
        )));
    case 'delvar':
        $resp = misc\variable\deleteSingular($_GET['name'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete variable!");
            break;
            case 'success':
                success("Successfully deleted variable!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
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
        $hwid = misc\etc\sanitize($_GET['hwid']);
        $result = mysqli_query($link, "SELECT `hwid` FROM `users` WHERE `username` = '$user' AND `app` = '$secret'");
        $row = mysqli_fetch_array($result);
        $hwidd = $row["hwid"];

        $hwidd = $hwidd .= $hwid;

        mysqli_query($link, "UPDATE `users` SET `hwid` = '$hwidd' WHERE `username` = '$user' AND `app` = '$secret'");

        success("Added HWID");
    case 'addhash':
        $hash = misc\etc\sanitize($_GET['hash']);
        $result = mysqli_query($link, "SELECT `hash` FROM `apps` WHERE `secret` = '$secret'");
        $row = mysqli_fetch_array($result);
        $oldHash = $row["hash"];

        $newHash = $oldHash .= $hash;

        mysqli_query($link, "UPDATE `apps` SET `hash` = '$newHash' WHERE `secret` = '$secret'");

        success("Added hash successfully");
    case 'getkey':
        $result = mysqli_query($link, "SELECT `key` FROM `keys` WHERE `usedby` = '$user' AND `app` = '$secret'");
        if (mysqli_num_rows($result) === 0)
        {
            error("License not found");
        }
        $row = mysqli_fetch_array($result);
        $key = $row["key"];
        die(json_encode(array(
            "success" => true,
            "key" => $key
        )));
    case 'userdata':
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$user' AND `app` = '$secret'");
        // if not found
        if (mysqli_num_rows($result) === 0)
        {
            error("User not found");
        }

        while ($row = mysqli_fetch_array($result))
        {
            $hwid = $row['hwid'];
            $ip = $row['ip'];
            $createdate = $row['createdate'];
            $lastlogin = $row['lastlogin'];
        }

        $result = mysqli_query($link, "SELECT `subscription`, `expiry` FROM `subs` WHERE `user` = '$user' AND `app` = '$secret' AND `expiry` > " . time() . "");
        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $timeleft = $r["expiry"] - time();
            $r += ["timeleft" => $timeleft];
            $rows[] = $r;
        }
        // success
        die(json_encode(array(
            "success" => true,
            "username" => $user,
            "subscriptions" => $rows,
            "ip" => $ip,
            "hwid" => $hwid,
            "createdate" => $createdate,
            "lastlogin" => $lastlogin
        )));
    case 'keydata':
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$key' AND `app` = '$secret'");
        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $r = array_reverse($r);
            $r['success'] = true;
            $r = array_reverse($r);
            $rows = $r;
        }
        die(json_encode($rows));
    case 'extend':
        if (!is_numeric($_GET['expiry'])) error("Expiry not set correctly, must be number of days");

        $expiry = $_GET['expiry'] * 86400 + time(); // 86400 is the number of seconds in a day since we're using unix time
        $resp = misc\user\extend($user, $_GET['sub'], $expiry, $secret);
        switch ($resp)
        {
            case 'missing':
                error("User(s) not found!");
            break;
            case 'sub_missing':
                error("Subscription not found!");
            break;
            case 'date_past':
                error("Subscription expiry must be set in the future!");
            break;
            case 'failure':
                error("Failed to extend user(s)!");
            break;
            case 'success':
                success("Successfully extended user(s)!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
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
        $resp = misc\license\deleteSingular($key, $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete license!");
            break;
            case 'success':
                success("Successfully deleted license!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'ban':
        $resp = misc\license\ban($key, $_GET['reason'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to ban license!");
            break;
            case 'success':
                success("Successfully banned license!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'unban':
        $resp = misc\license\unban($key, $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to unban license!");
            break;
            case 'success':
                success("Successfully unbanned license!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'banuser':
        $resp = misc\user\ban($user, $_GET['reason'], $secret);
        switch ($resp)
        {
            case 'missing':
                error("User not found!");
            break;
            case 'failure':
                error("Failed to ban user!");
            break;
            case 'success':
                success("Successfully banned user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'unbanuser':
        $resp = misc\user\unban($user, $secret);
        switch ($resp)
        {
            case 'missing':
                error("User not found!");
            break;
            case 'failure':
                error("Failed to unban user!");
            break;
            case 'success':
                success("Successfully unbanned user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'deluservar':
        $resp = misc\user\deleteVar($user, $_GET['var'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete variable!");
            break;
            case 'success':
                success("Successfully deleted variable!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delsub':
        $resp = misc\user\deleteSub($user, $_GET['sub'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete subscription!");
            break;
            case 'success':
                success("Successfully deleted subscription!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delunused':
        $resp = misc\license\deleteAllUnused($secret);
        switch ($resp)
        {
            case 'failure':
                error("Didn't find any unused keys!");
            break;
            case 'success':
                success("Deleted All Unused Keys!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delused':
        $resp = misc\license\deleteAllUsed($secret);
        switch ($resp)
        {
            case 'failure':
                error("Didn't find any used keys!");
            break;
            case 'success':
                success("Deleted All Used Keys!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'adduser':
        if (!is_numeric($_GET['expiry'])) error("Expiry not set correctly, must be number of days");

        $expiry = $_GET['expiry'] * 86400 + time(); // 86400 is the number of seconds in a day since we're using unix time
        $resp = misc\user\add($user, $_GET['sub'], $expiry, $secret, $_GET['pass']);
        switch ($resp)
        {
            case 'sub_missing':
                error("Subscription not found!");
            break;
            case 'date_past':
                error("Subscription expiry must be set in the future!");
            break;
            case 'failure':
                error("Failed to create user!");
            break;
            case 'success':
                success("Successfully created user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delexpusers':
        $resp = misc\user\deleteExpiredUsers($secret);
        switch ($resp)
        {
            case 'missing':
                error("You have no users!");
            break;
            case 'failure':
                error("No users are expired!");
            break;
            case 'success':
                success("Successfully deleted expired users!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delallusers':
        $resp = misc\user\deleteAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete all users!");
            break;
            case 'success':
                success("Successfully deleted all users!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'deluser':
        $resp = misc\user\deleteSingular($user, $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete user!");
            break;
            case 'success':
                success("Successfully deleted user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delalllicenses':
        $resp = misc\license\deleteAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Didn't find any keys!");
            break;
            case 'success':
                success("Deleted All Keys!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delallvars':
        $resp = misc\variable\deleteAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete all variables!");
            break;
            case 'success':
                success("Successfully deleted all variables!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'reset':
        die("Endpoint Deprecated, you can no longer use keys directly. A user is created from the key, and that user has a HWID and IP associated with it.");
    case 'resethash':
        mysqli_query($link, "UPDATE `apps` SET `hash` = NULL WHERE `secret` = '$secret'");

        if (mysqli_affected_rows($link) > 0) success("Reset hash successfully!");

        error("Failed to reset hash!");
    case 'resetuser':
        $resp = misc\user\resetSingular($user, $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to reset user!");
            break;
            case 'success':
                success("Successfully reset user!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'resetalluser':
        $resp = misc\user\resetAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to reset all users!");
            break;
            case 'success':
                success("Successfully reset all users!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'upload':
        $url = misc\etc\sanitize($_GET['url']);

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
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

        if ($filesize > 50000000)
        {
            error("File size limit is 50 MB.");
            return;
        }

        $id = misc\etc\generateRandomNum();
        $fn = basename($url);
        $fs = misc\etc\formatBytes($filesize);

        mysqli_query($link, "INSERT INTO `files` (name, id, url, size, uploaddate, app) VALUES ('$fn', '$id', '$url', '$fs', '" . time() . "', '$secret')");

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
    case 'delfile':
        $resp = misc\upload\deleteSingular($_GET['fileid'], $secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete all files!");
            break;
            case 'success':
                success("Successfully deleted all files!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
        }
    case 'delallfiles':
        $resp = misc\upload\deleteAll($secret);
        switch ($resp)
        {
            case 'failure':
                error("Failed to delete all files!");
            break;
            case 'success':
                success("Successfully deleted all files!");
            break;
            default:
                error("Unhandled Error! Contact us if you need help");
            break;
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
                "success" => true,
                "message" => "Seller Key Successfully Found"
            )));
        }
    case 'balance':
        $username = misc\etc\sanitize($_GET['username']);
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

        $dayamount = misc\etc\sanitize($_GET['day']);
        $weekamount = misc\etc\sanitize($_GET['week']);
        $monthamount = misc\etc\sanitize($_GET['month']);
        $threemonthamount = misc\etc\sanitize($_GET['threemonth']);
        $sixmonthamount = misc\etc\sanitize($_GET['sixmonth']);
        $lifetimeamount = misc\etc\sanitize($_GET['lifetime']);

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
                "message" => "No Subscriptions Found"
                // in theory this should not happen
                
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
        $enabled = misc\etc\sanitize($_GET['enabled']);
        $hwidcheck = misc\etc\sanitize($_GET['hwidcheck']);
        $ver = misc\etc\sanitize($_GET['ver']);
        $download = misc\etc\sanitize($_GET['download']);
        $webhook = misc\etc\sanitize($_GET['webhook']);
        $resellerstore = misc\etc\sanitize($_GET['resellerstore']);
        $appdisabled = misc\etc\sanitize($_GET['appdisabled']);
        $usernametaken = misc\etc\sanitize($_GET['usernametaken']);
        $keynotfound = misc\etc\sanitize($_GET['keynotfound']);
        $keyused = misc\etc\sanitize($_GET['keyused']);
        $nosublevel = misc\etc\sanitize($_GET['nosublevel']);
        $usernamenotfound = misc\etc\sanitize($_GET['usernamenotfound']);
        $passmismatch = misc\etc\sanitize($_GET['passmismatch']);
        $hwidmismatch = misc\etc\sanitize($_GET['hwidmismatch']);
        $noactivesubs = misc\etc\sanitize($_GET['noactivesubs']);
        $hwidblacked = misc\etc\sanitize($_GET['hwidblacked']);
        $keypaused = misc\etc\sanitize($_GET['keypaused']);
        $keyexpired = misc\etc\sanitize($_GET['keyexpired']);
        $sellixsecret = misc\etc\sanitize($_GET['sellixsecret']);
        $dayproduct = misc\etc\sanitize($_GET['dayproduct']);
        $weekproduct = misc\etc\sanitize($_GET['weekproduct']);
        $monthproduct = misc\etc\sanitize($_GET['monthproduct']);
        $lifetimeproduct = misc\etc\sanitize($_GET['lifetimeproduct']);

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
        $expiry = misc\etc\sanitize($_GET['expiry']);
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
        $username = misc\etc\sanitize($_GET['username']);
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
