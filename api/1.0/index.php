<?php
include '../../includes/misc/autoload.phtml';
include '../../includes/api/shared/autoload.phtml';
include '../../includes/api/1.0/autoload.phtml';

header("Access-Control-Allow-Origin: *"); // allow browser applications to request API

set_exception_handler(function ($exception) {
    error_log("\n--------------------------------------------------------------\n");
    error_log($exception);
    error_log("\nRequest data:");
    error_log(print_r($_POST, true));
    error_log("\n--------------------------------------------------------------");
    http_response_code(500);
    $errorMsg = str_replace($databaseUsername, "REDACTED", $exception->getMessage());
    die(json_encode(array("success" => false, "message" => "Error: " . $errorMsg)));
});

if(empty($_POST['ownerid'])) {
    die(json_encode(array("success" => false, "message" => "No OwnerID specified. Select app & copy code snippet from https://keyauth.cc/app/")));
}

if(empty($_POST['name'])) {
    die(json_encode(array("success" => false, "message" => "No app name specified. Select app & copy code snippet from https://keyauth.cc/app/")));
}

if(strlen(hex2bin($_POST['ownerid'])) != 10) {
    die(json_encode(array("success" => false, "message" => "OwnerID should be 10 characters long. Select app & copy code snippet from https://keyauth.cc/app/")));
}

$ownerid = misc\etc\sanitize(hex2bin($_POST['ownerid'])); // ownerid of account that owns application
$name = misc\etc\sanitize(hex2bin($_POST['name'])); // application name
$row = misc\cache\fetch('KeyAuthApp:' . $name . ':' . $ownerid, "SELECT * FROM `apps` WHERE `ownerid` = ? AND `name` = ?", [$ownerid, $name], 0);

if ($row == "not_found") {
    die("KeyAuth_Invalid");
}

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
$sessionexpiry = $row['session'];
$forceHwid = $row['forceHwid'];

$banned = $row['banned'];
$owner = $row['owner'];
$name = $row['name'];

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
$vpnblocked = $row['vpnblocked'];
$keybanned = $row['keybanned'];
$userbanned = $row['userbanned'];
$sessionunauthed = $row['sessionunauthed'];
$hashcheckfail = $row['hashcheckfail'];

// why using null coalescing operators? because if I add a field and it's not in redis cache, it'll be NULL
$loggedInMsg = $row['loggedInMsg'] ?? "Logged in!";
$pausedApp = $row['pausedApp'] ?? "Application is currently paused, please wait for the developer to say otherwise.";
$unTooShort = $row['unTooShort'] ?? "Username too short, try longer one.";
$pwLeaked = $row['pwLeaked'] ?? "This password has been leaked in a data breach (not from us), please use a different one.";
$chatHitDelay = $row['chatHitDelay'] ?? "Chat slower, you've hit the delay limit";
$minHwid = $row['minHwid'] ?? 20;

if ($banned) {
    die(api\v1_0\Encrypt(json_encode(array(
        "success" => false,
        "message" => "This application has been banned from KeyAuth.cc for violating terms." // yes we self promote to customers of those who break ToS. Should've followed terms :shrug:
    )), $secret));
}

if ($_GET['host'] == "keyauth.business") {
    die(api\v1_0\Encrypt(json_encode(array(
        "success" => false,
        "message" => "Please tell the developer of this program to use latest API domain. This domain is old, it will expire in a month."
    )), $secret));
}

switch (hex2bin($_POST['type'])) {
    case 'init':
        $ip = api\shared\primary\getIp();
        if ($vpnblock) {
            if (api\shared\primary\vpnCheck($ip)) {
                $row = misc\cache\fetch('KeyAuthWhitelist:' . $secret . ':' . $ip, "SELECT 1 FROM `whitelist` WHERE `ip` = ? AND `app` = ?", [$ip, $secret], 0);
                if ($row == "not_found") {
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "$vpnblocked"
                    )), $secret));
                }
            }
        }

        if (!$status) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$appdisabled"
            )), $secret));
        }

        if ($paused) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$pausedApp"
            )), $secret));
        }

        $ver = misc\etc\sanitize(api\v1_0\Decrypt($_POST['ver'], $secret));

        if ($ver != $currentver) {
            // auto-update system
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "invalidver",
                "download" => "$download"
            ), JSON_UNESCAPED_SLASHES), $secret));
        }

        $hash = misc\etc\sanitize($_POST['hash']);

        if ($hashcheck) {
            if (strpos($serverhash, $hash) === false) {
                if (is_null($serverhash)) {
                    misc\mysql\query("UPDATE `apps` SET `hash` = ? WHERE `secret` = ?", [$hash, $secret]);
                    misc\cache\purge('KeyAuthApp:' . $name . ':' . $ownerid); // flush cache for application so new hash takes precedent
                } else {
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "$hashcheckfail"
                    )), $secret));
                }
            }
        }

        $enckey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['enckey'], $secret));

        $newSession = false;
        $duplicateSession = misc\cache\select("KeyAuthSessionDupe:$secret:$ip");
        if($duplicateSession) {
            $sessionid = $duplicateSession;
            $updateSession = misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("enckey" => $enckey));
            if(!$updateSession) {
                $sessionid = misc\etc\generateRandomString();
                $newSession = true;
            }
        }
        else {
            $sessionid = misc\etc\generateRandomString();
            $newSession = true;
        }

        // $row = misc\cache\fetch('KeyAuthAppStats:' . $secret, "SELECT (SELECT COUNT(1) FROM `users` WHERE `app` = ?) AS 'numUsers', (SELECT COUNT(1) FROM `sessions` WHERE `app` = ? AND `validated` = 1 AND `expiry` > ?) AS 'numOnlineUsers', (SELECT COUNT(1) FROM `keys` WHERE `app` = ?) AS 'numKeys' FROM dual", [$secret, $secret, time(), $secret], 0, 3600);

        $numUsers = "N/A - Use fetchStats() function in latest example";
        $numOnlineUsers = "N/A - Use fetchStats() function in latest example";
        $numKeys = "N/A - Use fetchStats() function in latest example";

        echo api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Initialized",
            "sessionid" => $sessionid,
            "appinfo" => array(
                "numUsers" => "$numUsers",
                "numOnlineUsers" => "$numOnlineUsers",
                "numKeys" => "$numKeys",
                "version" => "$currentver",
                "customerPanelLink" => "https://keyauth.cc/panel/$owner/$name/"
            )
        )), $secret);

        fastcgi_finish_request();

        if($newSession) {
            misc\cache\insert("KeyAuthState:$secret:$sessionid", serialize(array("credential" => NULL, "enckey" => $enckey, "validated" => 0)), $sessionexpiry);
            $time = time() + $sessionexpiry;
            misc\mysql\query("INSERT INTO `sessions` (`id`, `app`, `expiry`, `created_at`, `enckey`,`ip`) VALUES (?, ?, ?, ?, ?, ?)", [$sessionid, $secret, $time, time(), $enckey, $ip]);
            $session = api\shared\primary\getSession($sessionid, $secret);
            $enckey = $session["enckey"];
            misc\cache\insert("KeyAuthSessionDupe:$secret:$ip", $sessionid, $sessionexpiry);
        }

    case 'register':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize(api\v1_0\Decrypt($_POST['username'], $enckey));

        if(strlen($username) > 70) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Username must be shorter than 70 characters"
            )), $enckey));
        }

        // Read in license key
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));

        if(strlen($checkkey) > 70) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Key must be shorter than 70 characters"
            )), $enckey));
        }

        // Read in password
        $password = misc\etc\sanitize(api\v1_0\Decrypt($_POST['pass'], $enckey));

        // Read in email
        $email = misc\etc\sanitize(api\v1_0\Decrypt($_POST['email'], $enckey));

        // Read in hwid
        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));

        $resp = api\v1_0\register($username, $checkkey, $password, $email, $hwid, $secret);
        switch ($resp) {
            case 'username_taken':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )), $enckey));
            case 'key_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )), $enckey));
            case 'un_too_short':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$unTooShort"
                )), $enckey));
            case 'pw_leaked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pwLeaked"
                )), $enckey));
            case 'key_already_used':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )), $enckey));
            case 'key_banned':
                if (strpos($keybanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `keys` WHERE `app` = ? AND `key` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $keybanned = str_replace("{reason}", $reason, $keybanned);
                }
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                )), $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )), $enckey));
            case 'no_subs_for_level':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )), $enckey));
            default:
                misc\mysql\query("UPDATE `sessions` SET `credential` = ?,`validated` = 1 WHERE `id` = ? AND `app` = ?", [$username, $sessionid, $secret]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $username));
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp
                )), $enckey));
        }
    case 'upgrade':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize(api\v1_0\Decrypt($_POST['username'], $enckey));

        // Read in key
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));


        // search for key
        $query = misc\mysql\query("SELECT `banned`, `expires`, `status`, `level` FROM `keys` WHERE `key` = ? AND `app` = ?", [$checkkey, $secret]);

        // check if key exists
        if ($query->num_rows < 1) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$keynotfound"
            )), $enckey));
        }
        // if key does exist
        elseif ($query->num_rows > 0) {
            // get key info
            while ($row = mysqli_fetch_array($query->result)) {
                $expires = $row['expires'];
                $status = $row['status'];
                $level = $row['level'];
                $banned = $row['banned'];
            }

            // check if used
            if ($status == "Used") {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )), $enckey));
            }

            if (!is_null($banned)) {
                if (strpos($keybanned, '{reason}') !== false) {
                    $keybanned = str_replace("{reason}", $banned, $keybanned);
                }
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                )), $enckey));
            }

            // add current time to key time
            $expiry = $expires + time();

            $query = misc\mysql\query("SELECT `name` FROM `subscriptions` WHERE `app` = ? AND `level` = ?", [$secret, $level]);
            $subName = mysqli_fetch_array($query->result)['name'];

            $resp = misc\user\extend($username, $subName, $expiry, 0, $secret);
            switch ($resp) {
                case 'missing':
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "$usernamenotfound"
                    )), $enckey));
                case 'sub_missing':
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "$nosublevel"
                    )), $enckey));
                case 'failure':
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "Failed to upgrade for some reason."
                    )), $enckey));
                case 'success':
                    // set key to used, and set usedby
                    misc\mysql\query("UPDATE `keys` SET `status` = 'Used', `usedon` = ?, `usedby` = ? WHERE `key` = ? AND `app` = ?", [time(), $username, $checkkey, $secret]);
                    misc\cache\purge('KeyAuthKeys:' . $secret . ':' . $checkkey);
                    misc\cache\purge('KeyAuthSubs:' . $secret . ':' . $username);
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => true,
                        "message" => "Upgraded successfully"
                    )), $enckey));
                default:
                    die(api\v1_0\Encrypt(json_encode(array(
                        "success" => false,
                        "message" => "Unhandled Error! Contact us if you need help"
                    )), $enckey));
            }
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

        if(strlen($hwid) < $minHwid && !is_null($hwid)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "HWID must be {$minHwid} or more characters, change this in app settings."
            )), $enckey));
        }

        if($forceHwid && is_null($hwid)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Force HWID is enabled, disable in app settings if you want to use blank HWIDs"
            )), $enckey));
        }

        $resp = api\v1_0\login($username, $password, $hwid, $secret, $hwidenabled);
        switch ($resp) {
            case 'un_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernamenotfound"
                )), $enckey));
            case 'pw_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )), $enckey));
            case 'user_banned':
                if (strpos($userbanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `users` WHERE `app` = ? AND `username` = ?", [$secret, $username]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $userbanned = str_replace("{reason}", $reason, $userbanned);
                }
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                )), $enckey));
            case 'hwid_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )), $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )), $enckey));
            case 'sub_paused':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                )), $enckey));
            case 'no_active_subs':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )), $enckey));
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ? AND `app` = ?", [$username, $sessionid, $secret]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $username));
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp
                )), $enckey));
        }

    case 'license':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        $checkkey = misc\etc\sanitize(api\v1_0\Decrypt($_POST['key'], $enckey));

        if(strlen($checkkey) > 70) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Key must be shorter than 70 characters"
            )), $enckey));
        }

        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));

        if(strlen($hwid) < $minHwid && !is_null($hwid)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "HWID must be {$minHwid} or more characters, change this in app settings."
            )), $enckey));
        }

        if($forceHwid && is_null($hwid)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Force HWID is enabled, disable in app settings if you want to use blank HWIDs"
            )), $enckey));
        }

        $resp = api\v1_0\login($checkkey, $checkkey, $hwid, $secret, $hwidenabled);
        switch ($resp) {
            case 'un_not_found':
                break; // user not registered yet or user was deleted
            case 'hwid_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )), $enckey));
            case 'user_banned':
                if (strpos($userbanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `users` WHERE `app` = ? AND `username` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $userbanned = str_replace("{reason}", $reason, $userbanned);
                }
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                )), $enckey));
            case 'pw_mismatch':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )), $enckey));
            case 'sub_paused':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                )), $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )), $enckey));
            case 'no_active_subs':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )), $enckey));
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ?", [$checkkey, $sessionid]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $checkkey));
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp
                )), $enckey));
        }

        // if login didn't work, attempt to register
        $resp = api\v1_0\register($checkkey, $checkkey, $checkkey, NULL, $hwid, $secret);
        switch ($resp) {
            case 'username_taken':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )), $enckey));
            case 'key_not_found':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )), $enckey));
            case 'un_too_short':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Username too short, try longer one."
                )), $enckey));
            case 'pw_leaked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$pwLeaked"
                )), $enckey));
            case 'key_already_used':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )), $enckey));
            case 'key_banned':
                if (strpos($keybanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `keys` WHERE `app` = ? AND `key` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $keybanned = str_replace("{reason}", $reason, $keybanned);
                }
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                )), $enckey));
            case 'hwid_blacked':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )), $enckey));
            case 'no_subs_for_level':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )), $enckey));
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ?", [$checkkey, $sessionid]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $checkkey));
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp
                )), $enckey));
        }
    case 'fetchOnline':
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $rows = misc\cache\fetch('KeyAuthOnlineUsers:' . $secret, "SELECT DISTINCT CONCAT(LEFT(`credential`, 10), IF(LENGTH(`credential`) > 10, REPEAT('*', LENGTH(`credential`) - 10), '')) AS `credential` FROM `sessions` WHERE `validated` = 1 AND `app` = ?", [$secret], 1, 1800);

        if ($rows == "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "No online users found!"
            )), $enckey));
        }

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully fetched online users.",
            "users" => $rows
        )), $enckey));
    case 'setvar':
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $var = misc\etc\sanitize(api\v1_0\Decrypt($_POST['var'], $enckey));
        $data = misc\etc\sanitize(api\v1_0\Decrypt($_POST['data'], $enckey));

        if(is_null($var)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "No variable name provided"
            )), $enckey));
        }

        if(is_null($data)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "No variable data provided"
            )), $enckey));
        }

        if(strlen($data) > 500) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Variable data must be 500 characters or less"
            )), $enckey));
        }

        $row = misc\cache\fetch('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"], "SELECT `data`, `readOnly` FROM `uservars` WHERE `name` = ? AND `user` = ? AND `app` = ?", [$var, $session["credential"], $secret], 0);

        if ($row != "not_found") {
            $readOnly = $row["readOnly"];
            if ($readOnly) {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Variable is read only"
                )), $enckey));
            }
        }

        $query = misc\mysql\query("REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES (?, ?, ?, ?)", [$var, $data, $session["credential"], $secret]);

        if ($query->affected_rows != 0) {
            misc\cache\purge('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"]);
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Successfully set variable"
            )), $enckey));
        } else {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Failed to set variable"
            )), $enckey));
        }
    case 'getvar':
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $var = misc\etc\sanitize(api\v1_0\Decrypt($_POST['var'], $enckey));

        $row = misc\cache\fetch('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"], "SELECT `data`, `readOnly` FROM `uservars` WHERE `name` = ? AND `user` = ? AND `app` = ?", [$var, $session["credential"], $secret], 0);

        if ($row == "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Variable not found for user"
            )), $enckey));
        }

        $data = $row['data'];
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved variable",
            "response" => $data
        )), $enckey));
    case 'var':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $varid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['varid'], $enckey));
        $row = misc\cache\fetch('KeyAuthVar:' . $secret . ':' . $varid, "SELECT `msg`, `authed` FROM `vars` WHERE `varid` = ? AND `app` = ?", [$varid, $secret], 0);
        if ($row == "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Variable not found."
            )), $enckey));
        }

        $msg = $row['msg'];
        $authed = $row['authed'];

        if ($authed) // if variable requires user to be authenticated

        {
            if (!$session["validated"]) {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                )), $enckey));
            }
        }
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "$msg"
        )), $enckey));
    case 'checkblacklist':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));
        $ip = api\shared\primary\getIp();
        $row = misc\cache\fetch('KeyAuthBlacklist:' . $secret . ':' . $ip . ':' . $hwid, "SELECT 1 FROM `bans` WHERE (`hwid` = ? OR `ip` = ?) AND `app` = ?", [$hwid, $ip, $secret], 0);

        if ($row != "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Client is blacklisted"
            )), $enckey));
        } else {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Client is not blacklisted"
            )), $enckey));
        }
    case 'chatget':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $channel = misc\etc\sanitize(api\v1_0\Decrypt($_POST['channel'], $enckey));
        $rows = misc\cache\fetch('KeyAuthChatMsgs:' . $secret . ':' . $channel, "SELECT `author`, `message`, `timestamp` FROM `chatmsgs` WHERE `channel` = ? AND `app` = ?", [$channel, $secret], 1);

        if ($rows == "not_found") {
            $rows = [];
        }

        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved chat messages",
            "messages" => $rows
        )), $enckey));
    case 'chatsend':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $channel = misc\etc\sanitize(api\v1_0\Decrypt($_POST['channel'], $enckey));
        $query = misc\mysql\query("SELECT `delay` FROM `chats` WHERE `name` = ? AND `app` = ?", [$channel, $secret]);

        if ($query->num_rows < 1) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Chat channel not found"
            )), $enckey));
        }

        $row = mysqli_fetch_array($query->result);
        $delay = $row['delay'];
        $credential = $session["credential"];
        $query = misc\mysql\query("SELECT `timestamp` FROM `chatmsgs` WHERE `author` = ? AND `channel` = ? AND `app` = ? ORDER BY `id` DESC LIMIT 1", [$credential, $channel, $secret]);

        $row = mysqli_fetch_array($query->result);
        $time = $row['timestamp'];

        if (time() - $time < $delay) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$chatHitDelay"
            )), $enckey));
        }

        $query = misc\mysql\query("SELECT `time` FROM `chatmutes` WHERE `user` = ? AND `app` = ?", [$credential, $secret]);
        if ($query->num_rows != 0) {
            $row = mysqli_fetch_array($query->result);
            $unmuted = $row["time"];
            $unmuted = date("F j, Y, g:i a", $unmuted);
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "You're muted from chat until $unmuted"
            )), $enckey));
        }

        $message = misc\etc\sanitize(api\v1_0\Decrypt($_POST['message'], $enckey));

        if (is_null($message)) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Message can't be blank"
            )), $enckey));
        }

        if(strlen($message) > 2000) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Message too long!"
            )), $enckey));
        }

        misc\mysql\query("INSERT INTO `chatmsgs` (`author`, `message`, `timestamp`, `channel`,`app`) VALUES (?, ?, ?, ?, ?)", [$credential, $message, time(), $channel, $secret]);
        misc\mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ? AND `channel` = ? AND `id` NOT IN ( SELECT `id` FROM ( SELECT `id` FROM `chatmsgs` WHERE `channel` = ? AND `app` = ? ORDER BY `id` DESC LIMIT 50) foo );", [$secret, $channel, $channel, $secret]);
        misc\cache\purge('KeyAuthChatMsgs:' . $secret . ':' . $channel);
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully sent chat message"
        )), $enckey));
    case 'log':
        // client isn't expecting a response body, just flush output right away so program can move on to rest of the code quicker
        fastcgi_finish_request();

        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];

        $currtime = time();

        $msg = misc\etc\sanitize(api\v1_0\Decrypt($_POST['message'], $enckey));

        if(is_null($msg)) {
            die();
        }

        if(strlen($msg) > 275) {
            die("Log data too long");
        }

        $pcuser = misc\etc\sanitize(api\v1_0\Decrypt($_POST['pcuser'], $enckey));

        if (is_null($webhook)) {
            $roleCheck = misc\cache\fetch('KeyAuthSellerCheck:' . $owner, "SELECT `role`,`expires` FROM `accounts` WHERE `username` = ?", [$owner], 0);
            if($roleCheck['role'] == "tester") {
                $query = misc\mysql\query("SELECT count(*) AS 'numLogs' FROM `logs` WHERE `logapp` = ?",[$secret]);
                $row = mysqli_fetch_array($query->result);
                $numLogs = $row["numLogs"];
                if($numLogs >= 20) {
                    die();
                }
            }

            misc\mysql\query("INSERT INTO `logs` (`logdate`, `logdata`, `credential`, `pcuser`,`logapp`) VALUES (?, ?, NULLIF(?, ''), NULLIF(?, ''), ?)", [$currtime, $msg, $credential, $pcuser, $secret]);
            die();
        }

        $credential = $session["credential"] ?? "N/A";

        $msg = "📜 Log: " . $msg;

        $ip = api\shared\primary\getIp();

        $json_data = json_encode([
            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => $msg,

                    // Embed left border color in HEX
                    "color" => hexdec("00ffe1"),

                    // Additional Fields array
                    "fields" => [["name" => "🔐 Credential:", "value" => "```" . $credential . "```"], ["name" => "💻 PC Name:", "value" => "```" . $pcuser . "```", "inline" => true], ["name" => "🌎 Client IP:", "value" => "```" . $ip . "```", "inline" => true]]
                ]
            ]

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($webhook);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json'
        ));

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);
        die();

    case 'webhook':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $webid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['webid'], $enckey));

        $row = misc\cache\fetch('KeyAuthWebhook:' . $secret . ':' . $webid, "SELECT `baselink`, `useragent`, `authed` FROM `webhooks` WHERE `webid` = ? AND `app` = ?", [$webid, $secret], 0);
        if ($row == "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Webhook Not Found."
            )), $enckey));
        }

        $baselink = $row['baselink'];

        $useragent = $row['useragent'];

        $authed = $row['authed'];

        if ($authed) // if variable requires user to be authenticated

        {
            if (!$session["validated"]) {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                )), $enckey));
            }
        }

        $params = misc\etc\sanitize(api\v1_0\Decrypt($_POST['params'], $enckey));
        $body = api\v1_0\Decrypt($_POST['body'], $enckey);
        $contType = misc\etc\sanitize(api\v1_0\Decrypt($_POST['conttype'], $enckey));

        $url = $baselink .= urldecode($params);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!is_null($body)) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        if (!is_null($contType)) curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: ' . $contType
        ));

        $response = curl_exec($ch);
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "Webhook request successful",
            "response" => "$response"
        )), $enckey));
    case 'file':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $fileid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['fileid'], $enckey));

        $row = misc\cache\fetch('KeyAuthFile:' . $secret . ':' . $fileid, "SELECT `name`, `url`, `authed` FROM `files` WHERE `app` = ? AND `id` = ?", [$secret, $fileid], 0);

        if ($row == "not_found") {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "File not Found"
            )), $enckey));
        }

        $filename = $row['name'];
        $url = $row['url'];
        $authed = $row['authed'];

        if ($authed) // if file requires user to be authenticated

        {
            if (!$session["validated"]) {
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                )), $enckey));
            }
        }

        ini_set('memory_limit', '-1');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($statusCode == 403 || $statusCode == 404) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "File no longer works, please notify the application developer."
            )), $enckey));
        }
        $contents = bin2hex($data);
        die(api\v1_0\Encrypt(json_encode(array(
            "success" => true,
            "message" => "File download successful",
            "contents" => "$contents"
        )), $enckey));

    case 'ban':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $reason = misc\etc\sanitize($_POST['reason']) ?? "User banned from triggering ban function in the client";

        if(strlen($reason) > 99) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Reason must be 99 characters or less"
            )), $enckey));
        }

        $hwid = misc\etc\sanitize(api\v1_0\Decrypt($_POST['hwid'], $enckey));
        if (!empty($hwid)) {
            misc\blacklist\add($hwid, "Hardware ID", $secret);
        }
        $ip = api\shared\primary\getIp();
        misc\blacklist\add($ip, "IP Address", $secret);

        misc\mysql\query("UPDATE `users` SET `banned` = ? WHERE `username` = ? AND `app` = ?", [$reason, $credential, $secret]);

        if ($query->affected_rows != 0) {
            misc\cache\purge('KeyAuthUser:' . $secret . ':' . $credential);
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Successfully Banned User"
            )), $enckey));
        } else {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "Failed to ban user."
            )), $enckey));
        }
    case 'check':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];
        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        } else {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => true,
                "message" => "Session is validated."
            )), $enckey));
        }
    case 'changeUsername':
        // retrieve session info
        $sessionid = misc\etc\sanitize(hex2bin($_POST['sessionid']));
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        if (!$session["validated"]) {
            die(api\v1_0\Encrypt(json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            )), $enckey));
        }

        $credential = $session["credential"];

        $resp = misc\user\changeUsername($credential, $_POST['newUsername'], $secret);
        switch ($resp) {
            case 'already_used':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Username already used!"
                )), $enckey));
            case 'failure':
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Failed to change username!"
                )), $enckey));
            case 'success':
                misc\session\killSingular($sessionid, $secret);
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Successfully changed username, user logged out."
                )), $enckey));
            default:
                die(api\v1_0\Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Unhandled Error! Contact us if you need help"
                )), $enckey));
        }
    default:
        die(json_encode(array(
            "success" => false,
            "message" => "Unhandled Type"
        )));
}
