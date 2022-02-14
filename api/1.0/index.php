<?php
include '../../includes/connection.php'; // mysql conn
include '../../includes/misc/autoload.phtml';
include '../../includes/api/shared/autoload.phtml';
include '../../includes/api/1.0/autoload.phtml';

$ownerid = misc\etc\sanitize(hex2bin($_POST['ownerid'])); // ownerid of account that owns application
$name = misc\etc\sanitize(hex2bin($_POST['name'])); // application name
$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `ownerid` = '$ownerid' AND `name` = '$name'");

if (mysqli_num_rows($result) === 0)
{
    die("KeyAuth_Invalid");
}
while ($row = mysqli_fetch_array($result))
{
    // app settings
    $secret = $row['secret'];
    $hwidenabled = $row['hwidcheck'];
    $vpnblock = $row['vpnblock'];
    $status = $row['enabled'];
    $paused = $row['paused'];
    $currentver = $row['ver'];
    $download = $row['download'];
    $webhook = $row['webhook'];
    $appdisabled = $row['appdisabled'];
    $hashcheck = $row['hashcheck'];
    $serverhash = $row['hash'];

    $banned = $row['banned'];
    $owner = $row['owner'];

    // custom error messages
    $usernametaken = $row['usernametaken'];
    $keynotfound = $row['keynotfound'];
    $keyused = $row['keyused'];
    $nosublevel = $row['nosublevel'];
    $usernamenotfound = $row['usernamenotfound'];
    $passmismatch = $row['passmismatch'];
    $hwidmismatch = $row['hwidmismatch'];
    $noactivesubs = $row['noactivesubs'];
    $hwidblacked = $row['hwidblacked'];
    $pausedsub = $row['pausedsub'];
    $keyexpired = $row['keyexpired'];
    $vpnblocked = $row['vpnblocked'];
    $keybanned = $row['keybanned'];
    $userbanned = $row['userbanned'];
    $sessionunauthed = $row['sessionunauthed'];
    $hashcheckfail = $row['hashcheckfail'];

    $sessionexpiry = $row['session'];
}

if ($banned)
{
    die(api\v1_0\Encrypt(json_encode(array(
        "success" => false,
        "message" => "This application has been banned from KeyAuth.com for violating terms."
    )) , $secret));
}

switch (hex2bin($_POST['type']))
{
    case 'init':

        if ($vpnblock)
        {
			$ip = api\shared\primary\getIp();
            if (api\shared\primary\vpnCheck($ip))
            {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$vpnblocked"
                )) , $secret));
            }
        }

        if (!$status)

        {

            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$appdisabled"
            )) , $secret));

        }
		
	if($paused)
		{
			die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Application is currently paused, please wait for the developer to say otherwise."
            )) , $secret));
	}

        $ver = misc\etc\sanitize(api\v1_0\Decrypt($_POST['ver'], $secret));

        if ($ver != $currentver)

        {
            // auto-update system
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "invalidver",
                "download" => "$download"
            ) , JSON_UNESCAPED_SLASHES) , $secret));
        }

        $hash = misc\etc\sanitize($_POST['hash']);

        if ($hashcheck)
        {
			if (strpos($serverhash, $hash) === false)
            {
                if (is_null($serverhash))
                {
                    mysqli_query($link, "UPDATE `apps` SET `hash` = '$hash' WHERE `secret` = '$secret'");
                }
                else
                {
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "$hashcheckfail"
                    )) , $secret));
                }
            }
        }

        $enckey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['enckey'], $secret));
        $sessionid = misc\etc\generateRandomString();
        // session init
        $time = time() + $sessionexpiry;
        mysqli_query($link, "INSERT INTO `sessions` (`id`, `app`, `expiry`, `enckey`) VALUES ('$sessionid','$secret', '$time', '$enckey')");

        $result = mysqli_query($link, "select count(1) FROM `users` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($result);
        $numUsers = number_format($row[0]);

        $result = mysqli_query($link, "select count(1) FROM `sessions` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($result);
        $numOnlineUsers = number_format($row[0]);

        $result = mysqli_query($link, "select count(1) FROM `keys` WHERE `app` = '$secret'");
        $row = mysqli_fetch_array($result);
        $numKeys = number_format($row[0]);

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Initialized",
            "sessionid" => $sessionid,
            "appinfo" => array(
                "numUsers" => $numUsers,
                "numOnlineUsers" => $numOnlineUsers,
                "numKeys" => $numKeys,
                "version" => $ver,
                "customerPanelLink" => "https://".($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'])."/panel/$owner/$name/"
            )
        )) , $secret));

    case 'register':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize(api\v1_0\Decrypt($_POST['username'], $enckey));

        // Read in license key
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));

        // Read in password
        $password = misc\etc\sanitize(api\v1_0\Decrypt($_POST['pass'], $enckey));

        // Read in hwid
        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));

        $resp = api\v1_0\register($username, $checkkey, $password, $hwid, $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )) , $enckey));
            case 'key_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )) , $enckey));
            case 'key_already_used':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));
            case 'key_banned':
                global $banned;
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                )) , $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_subs_for_level':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `credential` = '$username',`validated` = 1 WHERE `id` = '$sessionid'");
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => $resp
                )) , $enckey));
        }
    case 'upgrade':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = api\v1_0\Decrypt($_POST['username'], $enckey);

        $username = misc\etc\sanitize($username);

        // search username
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$username' AND `app` = '$secret'");

        // check if username already exists
        if (mysqli_num_rows($result) == 0)

        {

            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$usernamenotfound"
            )) , $enckey));

        }

        // Read in key
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));

        // search for key
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$checkkey' AND `app` = '$secret'");

        // check if key exists
        if (mysqli_num_rows($result) == 0)

        {

            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$keynotfound"
            )) , $enckey));

        }

        // if key does exist
        elseif (mysqli_num_rows($result) > 0)

        {

            $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$checkkey' AND `app` = '$secret'");

            // get key info
            while ($row = mysqli_fetch_array($result))
            {

                $expires = $row['expires'];

                $status = $row['status'];

                $level = $row['level'];

            }

            // check if used
            if ($status == "Used")

            {

                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));

            }
		
	    // set key to used, and set usedby
            mysqli_query($link, "UPDATE `keys` SET `status` = 'Used', `usedby` = '$username' WHERE `key` = '$checkkey'");

            // add current time to key time
            $expiry = $expires + time();

            $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '$secret' AND `level` = '$level'");

            $num = mysqli_num_rows($result);

            if ($num == 0)

            {

                mysqli_close($link);
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));

            }

            $subname = mysqli_fetch_array($result) ['name'];

            mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$subname', '$expiry', '$secret')");

            // success
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Upgraded successfully"
            )) , $enckey));

        }

    case 'login':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize(api\v1_0\Decrypt($_POST['username'], $enckey));

        // Read in HWID
        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));

        // Read in password
        $password = misc\etc\sanitize(api\v1_0\Decrypt($_POST['pass'], $enckey));

        $resp = api\v1_0\login($username, $password, $hwid, $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernamenotfound"
                )) , $enckey));
            case 'pw_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )) , $enckey));
            case 'user_banned':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                )) , $enckey));
            case 'hwid_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )) , $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'sub_paused':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                )) , $enckey));
            case 'no_active_subs':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 1,`credential` = '$username' WHERE `id` = '$sessionid'");
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => $resp
                )) , $enckey));
        }

    case 'license':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));

        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));

        $resp = api\v1_0\login($checkkey, $checkkey, $hwid, $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
            break; // user not registered yet or user was deleted
                
            case 'hwid_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )) , $enckey));
            case 'user_banned':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                )) , $enckey));
            case 'pw_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )) , $enckey));
            case 'sub_paused':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                )) , $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_active_subs':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 1,`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => $resp
                )) , $enckey));
        }

        // if login didn't work, attempt to register
        $resp = api\v1_0\register($checkkey, $checkkey, $checkkey, $hwid, $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )) , $enckey));
            case 'key_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )) , $enckey));
            case 'key_already_used':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));
            case 'key_banned':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                )) , $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_subs_for_level':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 1,`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => $resp
                )) , $enckey));
        }
    case 'setvar':
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"])
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )) , $enckey));
        }

        $var = misc\etc\sanitize(api\v1_0\Decrypt($_POST['var'], $enckey));
        $data = misc\etc\sanitize(api\v1_0\Decrypt($_POST['data'], $enckey));

        mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '" . $session["credential"] . "', '$secret')");

        if (mysqli_affected_rows($link) != 0)
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Successfully set variable"
            )) , $enckey));
        }
        else
        {
            mysqli_close($link);
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Failed to set variable"
            )) , $enckey));
        }
    case 'getvar':
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"])
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )) , $enckey));
        }

        $var = misc\etc\sanitize(api\v1_0\Decrypt($_POST['var'], $enckey));

        $result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `name` = '$var' AND `user` = '" . $session["credential"] . "' AND `app` = '$secret'");

        if (mysqli_num_rows($result) == 0)
        {
            mysqli_close($link);
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Variable not found for user"
            )) , $enckey));
        }

        $row = mysqli_fetch_array($result);
        $data = $row['data'];

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved variable",
            "response" => $data
        )) , $enckey));
    case 'var':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $varid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['varid'], $enckey));
        $varquery = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$varid' AND `app` = '$secret'");
		
		if (mysqli_num_rows($varquery) == 0)
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Variable not found."
            )) , $enckey));
        }
		
        $row = mysqli_fetch_array($varquery);
        $msg = $row['msg'];
        $authed = $row['authed'];

        if ($authed) // if variable requires user to be authenticated
        
        {
            if (!$session["validated"])
            {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                )) , $enckey));
            }
        }

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "$msg"
        )) , $enckey));
    case 'checkblacklist':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));
		$ip = api\shared\primary\getIp();
        $result = mysqli_query($link, "SELECT * FROM `bans` WHERE (`hwid` = '$hwid' OR `ip` = '$ip') AND `app` = '$secret'");

        if (mysqli_num_rows($result) != 0)
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Client is blacklisted"
            )) , $enckey));
        }
        else
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Client is not blacklisted"
            )) , $enckey));
        }
    case 'chatget':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"])
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )) , $enckey));
        }

        $channel = misc\etc\sanitize(api\v1_0\Decrypt($_POST['channel'], $enckey));
        $result = mysqli_query($link, "SELECT `author`, `message`, `timestamp` FROM `chatmsgs` WHERE `channel` = '$channel' AND `app` = '$secret'");

        $rows = array();

        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved chat messages",
            "messages" => $rows
        )) , $enckey));
    case 'chatsend':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"])
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )) , $enckey));
        }

        $channel = misc\etc\sanitize(api\v1_0\Decrypt($_POST['channel'], $enckey));
        $result = mysqli_query($link, "SELECT * FROM `chats` WHERE `name` = '$channel' AND `app` = '$secret'");

        if (mysqli_num_rows($result) == 0)
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Chat channel not found"
            )) , $enckey));
        }

        $row = mysqli_fetch_array($result);
        $delay = $row['delay'];
        $credential = $session["credential"];
        $result = mysqli_query($link, "SELECT * FROM `chatmsgs` WHERE `author` = '$credential' AND `channel` = '$channel' AND `app` = '$secret' ORDER BY `id` DESC LIMIT 1");

        $row = mysqli_fetch_array($result);
        $time = $row['timestamp'];

        if (time() - $time < $delay)
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Chat slower, you've hit the delay limit"
            )) , $enckey));
        }

        $result = mysqli_query($link, "SELECT * FROM `chatmutes` WHERE `user` = '$credential' AND `app` = '$secret'");
        if (mysqli_num_rows($result) != 0)
        {
            $row = mysqli_fetch_array($result);
            $unmuted = $row["time"];
            $unmuted = date("F j, Y, g:i a", $unmuted);

            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "You're muted from chat until $unmuted"
            )) , $enckey));
        }

        $message = misc\etc\sanitize(api\v1_0\Decrypt($_POST['message'], $enckey));
        mysqli_query($link, "INSERT INTO `chatmsgs` (`author`, `message`, `timestamp`, `channel`,`app`) VALUES ('$credential','$message','" . time() . "','$channel','$secret')");
        mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '$secret' AND `id` NOT IN ( SELECT `id` FROM ( SELECT `id` FROM `chatmsgs` WHERE `channel` = '$channel' AND `app` = '$secret' ORDER BY `id` DESC LIMIT 20) foo );");
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully sent chat message"
        )) , $enckey));
    case 'log':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];

        $currtime = time();

        $msg = misc\etc\sanitize(api\v1_0\Decrypt($_POST['message'], $enckey));

        $pcuser = misc\etc\sanitize(api\v1_0\Decrypt($_POST['pcuser'], $enckey));

        mysqli_query($link, "INSERT INTO `logs` (`logdate`, `logdata`, `credential`, `pcuser`,`logapp`) VALUES ('$currtime','$msg',NULLIF('$credential', ''),NULLIF('$pcuser', ''),'$secret')");

        $credential = $session["credential"] ?? "N/A";

        $msg = "📜 Log: " . $msg;
		
		$ip = api\shared\primary\getIp();
        $url = $webhook;

        $timestamp = date("c", strtotime("now"));

        $json_data = json_encode([

        // Message
        //"content" => "Hello World! This is message line ;) And here is the mention, use userID <@12341234123412341>",
        

        // Username
        "username" => "KeyAuth",

        // Avatar URL.
        // Uncoment to replace image set in webhook
        "avatar_url" => "https://keyauth.com/assets/img/favicon.png",

        // Text-to-speech
        "tts" => false,

        // File upload
        // "file" => "",
        

        // Embeds Array
        "embeds" => [

        [

        // Embed Title
        "title" => $msg,

        // Embed Type
        "type" => "rich",

        // Embed Description
        //"description" => "Description will be here, someday, you can mention users here also by calling userID <@12341234123412341>",
        

        // URL of title link
        // "url" => "https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c",
        

        // Timestamp of embed must be formatted as ISO8601
        "timestamp" => $timestamp,

        // Embed left border color in HEX
        "color" => hexdec("00ffe1") ,

        // Footer
        "footer" => [

        "text" => $name

        ],

        // Additional Fields array
        "fields" => [["name" => "🔐 Credential:", "value" => "```" . $credential . "```"], ["name" => "💻 PC Name:", "value" => "```" . $pcuser . "```", "inline" => true], ["name" => "🌎 Client IP:", "value" => "```" . $ip . "```", "inline" => true], ["name" => "📈 Level:", "value" => "```1```", "inline" => true]]

        ]

        ]

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json'
        ));

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        // echo $response;
        curl_close($ch);
        mysqli_close($link);
        die();

    case 'webhook' :
    // retrieve session info
    $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
    $session = api\shared\primary\getSession($sessionid, $secret);
    $enckey = $session["enckey"];

    $webid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['webid'], $enckey));
    $webquery = mysqli_query($link, "SELECT * FROM `webhooks` WHERE `webid` = '$webid' AND `app` = '$secret'");
    if (mysqli_num_rows($webquery) == 0)
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => false,
            "message" => "Webhook Not Found."
        )) , $enckey));
    }

    while ($row = mysqli_fetch_array($webquery))
    {

        $baselink = $row['baselink'];

        $useragent = $row['useragent'];

        $authed = $row['authed'];
    }

    if ($authed) // if variable requires user to be authenticated
    
    {
        if (!$session["validated"])
        {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )) , $enckey));
        }
    }

    $params = misc\etc\sanitize(api\v1_0\Decrypt($_POST['params'], $enckey));
    $body = api\v1_0\Decrypt($_POST['body'], $enckey);
    $contType = misc\etc\sanitize(api\v1_0\Decrypt($_POST['conttype'], $enckey));

    $url = $baselink .= $params;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	if(!is_null($body))
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	
	if(!is_null($contType))
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $contType));


    $response = curl_exec($ch);
    die(api\v1_0\Encrypt(json_encode(array(
        "success" => true,
        "message" => "Webhook request successful",
        "response" => "$response"
    )) , $enckey));
case 'file' :
// retrieve session info
$sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
$session = api\shared\primary\getSession($sessionid, $secret);
$enckey = $session["enckey"];

$fileid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['fileid'], $enckey));

$result = mysqli_query($link, "SELECT * FROM `files` WHERE `app` = '$secret' AND `id` = '$fileid'");

if (mysqli_num_rows($result) == 0)

{

    die(api\v1_0\Encrypt(json_encode(array(
        "success" => false,
        "message" => "File not Found"
    )) , $enckey));

}

while ($row = mysqli_fetch_array($result))
{
    $filename = $row['name'];
    $url = $row['url'];
    $authed = $row['authed'];
}

if ($authed) // if file requires user to be authenticated

{
    if (!$session["validated"])
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => false,
            "message" => "$sessionunauthed"
        )) , $enckey));
    }
}

$contents = bin2hex(file_get_contents($url));

die(api\v1_0\Encrypt(json_encode(array(
    "success" => true,
    "message" => "File download successful",
    "contents" => "$contents"
)) , $enckey));

case 'ban':
    // retrieve session info
    $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
    $session = api\shared\primary\getSession($sessionid, $secret);
    $enckey = $session["enckey"];

    $credential = $session["credential"];
    if (!$session["validated"])
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => false,
            "message" => "$sessionunauthed"
        )) , $enckey));
    }

    $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));
    if (!empty($hwid))
    {
        mysqli_query($link, "INSERT INTO `bans` (`hwid`, `type`, `app`) VALUES ('$hwid','hwid', '$secret')");
    }
	$ip = api\shared\primary\getIp();
    mysqli_query($link, "INSERT INTO `bans` (`ip`, `type`, `app`) VALUES ('$ip','ip', '$secret')");

    mysqli_query($link, "UPDATE `users` SET `banned` = 'User banned from triggering ban function in the client' WHERE `username` = '$credential'");
    if (mysqli_affected_rows($link) != 0)
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully Banned User"
        )) , $enckey));
    }
    else
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => false,
            "message" => "Failed to ban user."
        )) , $enckey));
    }
case 'check':
    // retrieve session info
    $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
    $session = api\shared\primary\getSession($sessionid, $secret);
    $enckey = $session["enckey"];

    $credential = $session["credential"];
    if (!$session["validated"])
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => false,
            "message" => "$sessionunauthed"
        )) , $enckey));
    }
    else
    {
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Session is validated."
        )) , $enckey));
    }
default:
    die(json_encode(array(
        "success" => false,
        "message" => "Unhandled Type"
    )));
}
?>
