<?php
include '../../includes/misc/autoload.phtml';
include '../../includes/api/shared/autoload.phtml';
include '../../includes/api/1.0/autoload.phtml';

header("Access-Control-Allow-Origin: *"); // allow browser applications to request API
header('Content-Type: application/json; charset=utf-8');

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

if(empty(($_POST['ownerid'] ?? $_GET['ownerid']))) {
    die(json_encode(array("success" => false, "message" => "No OwnerID specified. Select app & copy code snippet from https://keyauth.cc/app/")));
}

if(empty(($_POST['name'] ?? $_GET['name']))) {
    die(json_encode(array("success" => false, "message" => "No app name specified. Select app & copy code snippet from https://keyauth.cc/app/")));
}

if(strlen(($_POST['ownerid'] ?? $_GET['ownerid'])) != 10) {
    die(json_encode(array("success" => false, "message" => "OwnerID should be 10 characters long. Select app & copy code snippet from https://keyauth.cc/app/")));
}

if (misc\cache\rateLimit("KeyAuthAppLimit:" . ($_POST['ownerid'] ?? $_GET['ownerid']), 1, 60, 200)) {
    die(json_encode(array("success" => false, "message" => "This application has sent too many requests. Try again in a minute.")));
}

$ownerid = misc\etc\sanitize($_POST['ownerid'] ?? $_GET['ownerid']); // ownerid of account that owns application
$name = misc\etc\sanitize($_POST['name'] ?? $_GET['name']); // application name
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
$forceEncryption = $row['forceEncryption'];
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

if($ownerid == "hTmfnZOYPe") {
    $response = json_encode(array(
        "success" => false,
        "message" => "Jao é um golpista, prova aqui https://keyauth.cc/jao/"
    ));

    $sig = hash_hmac('sha256', $response, $secret);
    header("signature: {$sig}");

    die($response);
}

if ($banned) {
    die(json_encode(array(
        "success" => false,
        "message" => "This application has been banned from KeyAuth.cc for violating terms." // yes we self promote to customers of those who break ToS. Should've followed terms :shrug:
    )));
}

switch ($_POST['type'] ?? $_GET['type']) {
    case 'init':
        if(strlen($_POST['enckey']) > 35) {
            $response = json_encode(array(
                "success" => false,
                "message" => "The paramater \"enckey\" is too long. Must be 35 characters or less."
            ));

            $sig = hash_hmac('sha256', $response, $secret);
            header("signature: {$sig}");

            die($response);
        }

        if ($forceEncryption) {
            if (!isset($_POST['enckey']) && !isset($_GET['enckey'])) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "No encryption key supplied, encryption is forced."
                ));

                $sig = hash_hmac('sha256', $response, $secret);
                header("signature: {$sig}");

                die($response);
            }
        }
        $ip = api\shared\primary\getIp();
        if ($vpnblock) {
            if (api\shared\primary\vpnCheck($ip)) {
                $row = misc\cache\fetch('KeyAuthWhitelist:' . $secret . ':' . $ip, "SELECT 1 FROM `whitelist` WHERE `ip` = ? AND `app` = ?", [$ip, $secret], 0);
                if ($row == "not_found") {
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "$vpnblocked"
                    ));

                    $sig = hash_hmac('sha256', $response, $secret);
                    header("signature: {$sig}");

                    die($response);
                }
            }
        }

        if (!$status) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$appdisabled"
            ));

            $sig = hash_hmac('sha256', $response, $secret);
            header("signature: {$sig}");

            die($response);
        }

        if ($paused) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$pausedApp"
            ));

            $sig = hash_hmac('sha256', $response, $secret);
            header("signature: {$sig}");

            die($response);
        }

        $ver = misc\etc\sanitize($_POST['ver'] ?? $_GET['ver']);
        if (!empty($ver)) {
            if ($ver != $currentver) {
                // auto-update system
                $response = json_encode(array(
                    "success" => false,
                    "message" => "invalidver",
                    "download" => "$download"
                ), JSON_UNESCAPED_SLASHES);

                $sig = hash_hmac('sha256', $response, $secret);
                header("signature: {$sig}");

                die($response);
            }
        }

        $hash = misc\etc\sanitize($_POST['hash'] ?? $_GET['hash']);

        if ($hashcheck) {
            if (strpos($serverhash, $hash) === false) {
                if (is_null($serverhash)) {
                    misc\mysql\query("UPDATE `apps` SET `hash` = ? WHERE `secret` = ?", [$hash, $secret]);
                    misc\cache\purge('KeyAuthApp:' . $name . ':' . $ownerid); // flush cache for application so new hash takes precedent
                } else {
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "$hashcheckfail"
                    ));

                    $sig = hash_hmac('sha256', $response, $secret);
                    header("signature: {$sig}");

                    die($response);
                }
            }
        }

        $enckey = !is_null($_POST['enckey'] ?? $_GET['enckey']) ? misc\etc\sanitize($_POST['enckey'] ?? $_GET['enckey']) . "-" . $secret : NULL;
        $newSession = false;
        $duplicateSession = misc\cache\select("KeyAuthSessionDupe:$secret:$ip");
        if($duplicateSession) {
            $sessionid = $duplicateSession;

            $updateSession = misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 0, "enckey" => $enckey));
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

        $resp = json_encode(array(
            "success" => true,
            "message" => "Initialized",
            "sessionid" => $sessionid,
            "appinfo" => array(
                "numUsers" => "$numUsers",
                "numOnlineUsers" => "$numOnlineUsers",
                "numKeys" => "$numKeys",
                "version" => "$currentver",
                "customerPanelLink" => "https://keyauth.cc/panel/$owner/$name/"
            ),
            "newSession" => $newSession,
            "nonce" => misc\etc\generateRandomString(32)
        ));

        $sig = !is_null($enckey) ? hash_hmac('sha256', $resp, $secret)  : 'No encryption key supplied';
        header("signature: {$sig}");

        echo $resp;

        fastcgi_finish_request();

        if($newSession) {
            misc\cache\insert("KeyAuthState:$secret:$sessionid", serialize(array("credential" => NULL, "enckey" => $enckey, "validated" => 0)), $sessionexpiry);
            $time = time() + $sessionexpiry;
            misc\mysql\query("INSERT INTO `sessions` (`id`, `app`, `expiry`, `created_at`, `enckey`, `ip`) VALUES (?, ?, ?, ?, NULLIF(?, ''), ?)", [$sessionid, $secret, $time, time(), $enckey, $ip]);
            misc\cache\insert("KeyAuthSessionDupe:$secret:$ip", $sessionid, $sessionexpiry);
        }

    case 'register':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize($_POST['username'] ?? $_GET['username']);

        if(strlen($username) > 70) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Username must be shorter than 70 characters"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        // Read in license key
        $checkkey = misc\etc\sanitize($_POST['key'] ?? $_GET['key']);

        if(strlen($checkkey) > 70) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Key must be shorter than 70 characters"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        // Read in password
        $password = misc\etc\sanitize($_POST['pass'] ?? $_GET['pass']);

        // Read in email
        $email = misc\etc\sanitize($_POST['email'] ?? $_GET['email']);

        // Read in hwid
        $hwid = misc\etc\sanitize($_POST['hwid'] ?? $_GET['hwid']);

        $registerSuccess = false;

        $resp = api\v1_0\register($username, $checkkey, $password, $email, $hwid, $secret);
        switch ($resp) {
            case 'username_taken':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                ));
                break;
            case 'key_not_found':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                ));
                break;
            case 'un_too_short':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$unTooShort"
                ));
                break;
            case 'pw_leaked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$pwLeaked"
                ));
                break;
            case 'key_already_used':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                ));
                break;
            case 'key_banned':
                if (strpos($keybanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `keys` WHERE `app` = ? AND `key` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $keybanned = str_replace("{reason}", $reason, $keybanned);
                }
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                ));
                break;
            case 'hwid_blacked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                ));
                break;
            case 'no_subs_for_level':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                ));
                break;
            default:
                $registerSuccess = true;

                $response = json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp,
                    "nonce" => misc\etc\generateRandomString(32)
                ));
                break;
        }
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
        header("signature: {$sig}");

        echo $response;

        fastcgi_finish_request();

        if ($registerSuccess) {
            misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $username));
            misc\mysql\query("UPDATE `sessions` SET `credential` = ?,`validated` = 1 WHERE `id` = ?", [$username, $sessionid]);
        }
        die();
    case 'upgrade':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize($_POST['username'] ?? $_GET['username']);

        // Read in key
        $checkkey = misc\etc\sanitize($_POST['key'] ?? $_GET['key']);


        // search for key
        $query = misc\mysql\query("SELECT `banned`, `expires`, `status`, `level` FROM `keys` WHERE `key` = ? AND `app` = ?", [$checkkey, $secret]);

        // check if key exists
        if ($query->num_rows < 1) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$keynotfound"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
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
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                ));

                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
                header("signature: {$sig}");

                die($response);
            }

            if (!is_null($banned)) {
                if (strpos($keybanned, '{reason}') !== false) {
                    $keybanned = str_replace("{reason}", $banned, $keybanned);
                }
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                ));

                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
                header("signature: {$sig}");

                die($response);
            }

            // add current time to key time
            $expiry = $expires + time();

            $query = misc\mysql\query("SELECT `name` FROM `subscriptions` WHERE `app` = ? AND `level` = ?", [$secret, $level]);
            $subName = mysqli_fetch_array($query->result)['name'];

            $resp = misc\user\extend($username, $subName, $expiry, 0, $secret);
            switch ($resp) {
                case 'missing':
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "$usernamenotfound"
                    ));
                    break;
                case 'sub_missing':
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "$nosublevel"
                    ));
                    break;
                case 'failure':
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "Failed to upgrade for some reason."
                    ));
                    break;
                case 'success':
                    // set key to used, and set usedby
                    misc\mysql\query("UPDATE `keys` SET `status` = 'Used', `usedon` = ?, `usedby` = ? WHERE `key` = ? AND `app` = ?", [time(), $username, $checkkey, $secret]);
                    misc\cache\purge('KeyAuthKeys:' . $secret . ':' . $checkkey);
                    misc\cache\purge('KeyAuthSubs:' . $secret . ':' . $username);
                    $response = json_encode(array(
                        "success" => true,
                        "message" => "Upgraded successfully",
                        "nonce" => misc\etc\generateRandomString(32)
                    ));
                    break;
                default:
                    $response = json_encode(array(
                        "success" => false,
                        "message" => "Unhandled Error! Contact us if you need help"
                    ));
                    break;
            }
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }
    case 'login':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = misc\etc\sanitize($_POST['username'] ?? $_GET['username']);

        // Read in HWID
        $hwid = misc\etc\sanitize($_POST['hwid'] ?? $_GET['hwid']);

        // Read in password
        $password = misc\etc\sanitize($_POST['pass'] ?? $_GET['pass']);

        // optional param for web loader
        $token = misc\etc\sanitize($_POST['token'] ?? $_GET['token']);

        if(strlen($hwid) < $minHwid && !is_null($hwid)) {
            $response = json_encode(array(
                "success" => false,
                "message" => "HWID must be {$minHwid} or more characters, change this in app settings."
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        if($forceHwid && is_null($hwid)) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Force HWID is enabled, disable in app settings if you want to use blank HWIDs"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        $resp = api\v1_0\login($username, $password, $hwid, $secret, $hwidenabled, $token);
        switch ($resp) {
            case 'un_not_found':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$usernamenotfound"
                ));
                break;
            case 'pw_mismatch':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                ));
                break;
            case 'user_banned':
                if (strpos($userbanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `users` WHERE `app` = ? AND `username` = ?", [$secret, $username]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $userbanned = str_replace("{reason}", $reason, $userbanned);
                }
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                ));
                break;
            case 'hwid_mismatch':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                ));
                break;
            case 'hwid_blacked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                ));
                break;
            case 'sub_paused':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                ));
                break;
            case 'no_active_subs':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                ));
                break;
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ?", [$username, $sessionid]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $username));

                $ip = api\shared\primary\getIp();
                $response = json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp,
                    "nonce" => misc\etc\generateRandomString(32)
                ));
                break;
        }
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'license':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        $checkkey = misc\etc\sanitize($_POST['key'] ?? $_GET['key']);

        if(strlen($checkkey) > 70) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Key must be shorter than 70 characters"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        $hwid = misc\etc\sanitize($_POST['hwid'] ?? $_GET['hwid']);

        if(strlen($hwid) < $minHwid && !is_null($hwid)) {
            $response = json_encode(array(
                "success" => false,
                "message" => "HWID must be {$minHwid} or more characters, change this in app settings."
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        if($forceHwid && is_null($hwid)) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Force HWID is enabled, disable in app settings if you want to use blank HWIDs"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : NULL;
            header("signature: {$sig}");

            die($response);
        }

        $resp = api\v1_0\login($checkkey, $checkkey, $hwid, $secret, $hwidenabled);

        switch ($resp) {
            case 'un_not_found':
                break; // user not registered yet or user was deleted

            case 'hwid_mismatch':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                ));
                break;
            case 'user_banned':
                if (strpos($userbanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `users` WHERE `app` = ? AND `username` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $userbanned = str_replace("{reason}", $reason, $userbanned);
                }
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$userbanned"
                ));
                break;
            case 'pw_mismatch':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                ));
                break;
            case 'sub_paused':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$pausedsub"
                ));
                break;
            case 'hwid_blacked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                ));
                break;
            case 'no_active_subs':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                ));
                break;
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ?", [$checkkey, $sessionid]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $checkkey));

                $ip = api\shared\primary\getIp();
                $response = json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp,
                    "nonce" => misc\etc\generateRandomString(32)
                ));
                break;
        }
        if (isset($response)) {
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        // if login didn't work, attempt to register
        $resp = api\v1_0\register($checkkey, $checkkey, $checkkey, NULL, $hwid, $secret);
        switch ($resp) {
            case 'username_taken':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                ));
                break;
            case 'key_not_found':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                ));
                break;
            case 'un_too_short':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$unTooShort"
                ));
                break;
            case 'pw_leaked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$pwLeaked"
                ));
                break;
            case 'key_already_used':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                ));
                break;
            case 'key_banned':
                if (strpos($keybanned, '{reason}') !== false) {
                    $query = misc\mysql\query("SELECT `banned` FROM `keys` WHERE `app` = ? AND `key` = ?", [$secret, $checkkey]);
                    $row = mysqli_fetch_array($query->result);
                    $reason = $row['banned'];
                    $keybanned = str_replace("{reason}", $reason, $keybanned);
                }
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$keybanned"
                ));
                break;
            case 'hwid_blacked':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                ));
                break;
            case 'no_subs_for_level':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                ));
                break;
            default:
                misc\mysql\query("UPDATE `sessions` SET `validated` = 1,`credential` = ? WHERE `id` = ?", [$checkkey, $sessionid]);
                misc\cache\update('KeyAuthState:'.$secret.':'.$sessionid.'', array("validated" => 1, "credential" => $checkkey));
                $response = json_encode(array(
                    "success" => true,
                    "message" => "$loggedInMsg",
                    "info" => $resp,
                    "nonce" => misc\etc\generateRandomString(32)
                ));
        }
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'forgot':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $un = misc\etc\sanitize($_POST['username'] ?? $_GET['username']);
        $email = strtolower(misc\etc\sanitize($_POST['email'] ?? $_GET['email']));

        $row = misc\cache\fetch('KeyAuthUser:' . $secret . ':' . $un, "SELECT * FROM `users` WHERE `username` = ? AND `app` = ?", [$un, $secret], 0);
        if ($row == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "No user found with that username!"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $emailHashed = $row['email'];

        if (sha1($email) != $emailHashed) {
            if(is_null($emailHashed)) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Email address not provided during register, ask developer to edit your account and change email."
                ));
            }
            else {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Email address doesn't match!"
                ));
            }
        } else {
            $algos = array(
                'ripemd128',
                'md5',
                'md4',
                'tiger128,4',
                'haval128,3',
                'haval128,4',
                'haval128,5'
            );
            $emailSecret = hash($algos[array_rand($algos)], misc\etc\generateRandomString());

            misc\mysql\query("INSERT INTO `resetUsers` (`secret`, `email`, `username`, `app`, `time`) VALUES (?, SHA1(?), ?, ?, ?)", [$emailSecret, $email, $un, $secret, time()]);

            $body = '<div class="f-fallback">
                <h1>Hello <i>'.$un.'</i>,</h1>
                <p>You recently requested to reset your password for the software '.$name.'. Use the button below to reset it. <strong>The link is only valid for the next 24 hours.</strong></p>
                <!-- Action -->
                <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td align="center">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                        <tr>
                          <td align="center">
                            <a href="https://keyauth.cc/resetUser/?secret='.$emailSecret.'" style="color:white;background-color: #3869D4;border-top: 10px solid #3869D4;border-right: 18px solid #3869D4;border-bottom: 10px solid #3869D4;border-left: 18px solid #3869D4;display: inline-block;text-decoration: none;border-radius: 3px;-webkit-text-size-adjust: none;box-sizing: border-box;" target="_blank">Reset Password</a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <p>Thanks,
                  <br>The KeyAuth team</p>
            </div>';
            misc\email\send($un, $email, $body, "KeyAuth - Password Reset for {$name}");
            $response = json_encode(array(
                "success" => true,
                "message" => "Successfully sent email to change password.",
                "nonce" => misc\etc\generateRandomString(32)
            ));
        }

        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'fetchOnline':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $rows = misc\cache\fetch('KeyAuthOnlineUsers:' . $secret, "SELECT DISTINCT CONCAT(LEFT(`credential`, 10), IF(LENGTH(`credential`) > 10, REPEAT('*', LENGTH(`credential`) - 10), '')) AS `credential` FROM `sessions` WHERE `validated` = 1 AND `app` = ?", [$secret], 1, 1800);

        if ($rows == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "No online users found!"
            ));
        }
        else {
            $response = json_encode(array(
                "success" => true,
                "message" => "Successfully fetched online users.",
                "users" => $rows,
                "nonce" => misc\etc\generateRandomString(32)
            ));
        }

        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'fetchStats':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
    
        $row = misc\cache\fetch('KeyAuthAppStats:' . $secret, "SELECT (SELECT COUNT(1) FROM `users` WHERE `app` = ?) AS 'numUsers', (SELECT COUNT(1) FROM `sessions` WHERE `app` = ? AND `validated` = 1 AND `expiry` > ?) AS 'numOnlineUsers', (SELECT COUNT(1) FROM `keys` WHERE `app` = ?) AS 'numKeys' FROM dual", [$secret, $secret, time(), $secret], 0, 3600);

        $numUsers = $row['numUsers'];
        $numOnlineUsers = $row['numOnlineUsers'];
        $numKeys = $row['numKeys'];

        $response = json_encode(array(
            "success" => true,
            "message" => "Successfully fetched stats",
            "appinfo" => array(
                "numUsers" => "$numUsers",
                "numOnlineUsers" => "$numOnlineUsers",
                "numKeys" => "$numKeys",
                "version" => "$currentver",
                "customerPanelLink" => "https://keyauth.cc/panel/$owner/$name/"
            ),
            "nonce" => misc\etc\generateRandomString(32)
        ));
    
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");
    
        die($response);
    case 'setvar':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $var = misc\etc\sanitize($_POST['var'] ?? $_GET['var']);
        $data = misc\etc\sanitize($_POST['data'] ?? $_GET['data']);

        if(is_null($var)) {
            $response = json_encode(array(
                "success" => true,
                "message" => "No variable name provided"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");
            die($response);
        }

        if(is_null($data)) {
            $response = json_encode(array(
                "success" => true,
                "message" => "No variable data provided"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");
            die($response);
        }

        if(strlen($data) > 500) {
            $response = json_encode(array(
                "success" => true,
                "message" => "Variable data must be 500 characters or less"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");
            die($response);
        }

        $row = misc\cache\fetch('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"], "SELECT `data`, `readOnly` FROM `uservars` WHERE `name` = ? AND `user` = ? AND `app` = ?", [$var, $session["credential"], $secret], 0);

        if ($row != "not_found") {
            $readOnly = $row["readOnly"];
            if ($readOnly) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Variable is read only"
                ));
                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
                header("signature: {$sig}");
                die($response);
            }
        }

        $query = misc\mysql\query("REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES (?, ?, ?, ?)", [$var, $data, $session["credential"], $secret]);

        if ($query->affected_rows != 0) {
            misc\cache\purge('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"]);
            $response = json_encode(array(
                "success" => true,
                "message" => "Successfully set variable",
                "nonce" => misc\etc\generateRandomString(32)
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        } else {
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            $response = json_encode(array(
                "success" => false,
                "message" => "Failed to set variable"
            ));
            die($response);
        }
    case 'getvar':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $var = misc\etc\sanitize($_POST['var'] ?? $_GET['var']);

        $row = misc\cache\fetch('KeyAuthUserVar:' . $secret . ':' . $var . ':' . $session["credential"], "SELECT `data`, `readOnly` FROM `uservars` WHERE `name` = ? AND `user` = ? AND `app` = ?", [$var, $session["credential"], $secret], 0);

        if ($row == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "Variable not found for user"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $data = $row['data'];
        $response = json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved variable",
            "response" => $data,
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'var':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $varid = misc\etc\sanitize($_POST['varid'] ?? $_GET['varid']);

        $row = misc\cache\fetch('KeyAuthVar:' . $secret . ':' . $varid, "SELECT `msg`, `authed` FROM `vars` WHERE `varid` = ? AND `app` = ?", [$varid, $secret], 0);

        if ($row == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "Variable not found."
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $msg = $row['msg'];
        $authed = $row['authed'];

        if ($authed) // if variable requires user to be authenticated

        {
            if (!$session["validated"]) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                ));
                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
                header("signature: {$sig}");

                die($response);
            }
        }
        $response = json_encode(array(
            "success" => true,
            "message" => "$msg",
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'checkblacklist':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $hwid = misc\etc\sanitize($_POST['hwid'] ?? $_GET['hwid']);
        $ip = api\shared\primary\getIp();

        $row = misc\cache\fetch('KeyAuthBlacklist:' . $secret . ':' . $ip . ':' . $hwid, "SELECT 1 FROM `bans` WHERE (`hwid` = ? OR `ip` = ?) AND `app` = ?", [$hwid, $ip, $secret], 0);

        if ($row != "not_found") {
            $response = json_encode(array(
                "success" => true,
                "message" => "Client is blacklisted",
                "nonce" => misc\etc\generateRandomString(32)
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        } else {
            $response = json_encode(array(
                "success" => false,
                "message" => "Client is not blacklisted"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }
    case 'chatget':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $channel = misc\etc\sanitize($_POST['channel'] ?? $_GET['channel']);
        $rows = misc\cache\fetch('KeyAuthChatMsgs:' . $secret . ':' . $channel, "SELECT `author`, `message`, `timestamp` FROM `chatmsgs` WHERE `channel` = ? AND `app` = ?", [$channel, $secret], 1);

        if ($rows == "not_found") {
            $rows = [];
        }

        $response = json_encode(array(
            "success" => true,
            "message" => "Successfully retrieved chat messages",
            "messages" => $rows,
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'chatsend':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $channel = misc\etc\sanitize($_POST['channel'] ?? $_GET['channel']);
        $query = misc\mysql\query("SELECT `delay` FROM `chats` WHERE `name` = ? AND `app` = ?", [$channel, $secret]);

        if ($query->num_rows < 1) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Chat channel not found"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $row = mysqli_fetch_array($query->result);
        $delay = $row['delay'];
        $credential = $session["credential"];
        $query = misc\mysql\query("SELECT `timestamp` FROM `chatmsgs` WHERE `author` = ? AND `channel` = ? AND `app` = ? ORDER BY `id` DESC LIMIT 1", [$credential, $channel, $secret]);

        $row = mysqli_fetch_array($query->result);
        $time = $row['timestamp'];

        if (time() - $time < $delay) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$chatHitDelay"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $query = misc\mysql\query("SELECT `time` FROM `chatmutes` WHERE `user` = ? AND `app` = ?", [$credential, $secret]);
        if ($query->num_rows != 0) {
            $row = mysqli_fetch_array($query->result);
            $unmuted = $row["time"];
            $unmuted = date("F j, Y, g:i a", $unmuted);
            $response = json_encode(array(
                "success" => false,
                "message" => "You're muted from chat until $unmuted"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $message = misc\etc\sanitize($_POST['message'] ?? $_GET['message']);

        if (is_null($message)) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Message can't be blank"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        if(strlen($message) > 2000) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Message too long!"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        misc\mysql\query("INSERT INTO `chatmsgs` (`author`, `message`, `timestamp`, `channel`,`app`) VALUES (?, ?, ?, ?, ?)", [$credential, $message, time(), $channel, $secret]);
        misc\mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ? AND `channel` = ? AND `id` NOT IN ( SELECT `id` FROM ( SELECT `id` FROM `chatmsgs` WHERE `channel` = ? AND `app` = ? ORDER BY `id` DESC LIMIT 50) foo );", [$secret, $channel, $channel, $secret]);
        misc\cache\purge('KeyAuthChatMsgs:' . $secret . ':' . $channel);
        $response = json_encode(array(
            "success" => true,
            "message" => "Successfully sent chat message",
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'log':
        // client isn't expecting a response body, just flush output right away so program can move on to rest of the code quicker
        fastcgi_finish_request();

        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];

        $currtime = time();

        $msg = misc\etc\sanitize($_POST['message'] ?? $_GET['message']);

        if(is_null($msg)) {
            die("No log data specified");
        }

        if(strlen($msg) > 275) {
            die("Log data too long");
        }

        $pcuser = misc\etc\sanitize($_POST['pcuser'] ?? $_GET['pcuser']);

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

        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        // echo $response;
        curl_close($ch);
        die();

    case 'webhook':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $webid = misc\etc\sanitize($_POST['webid'] ?? $_GET['webid']);
        $row = misc\cache\fetch('KeyAuthWebhook:' . $secret . ':' . $webid, "SELECT `baselink`, `useragent`, `authed` FROM `webhooks` WHERE `webid` = ? AND `app` = ?", [$webid, $secret], 0);
        if ($row == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "Webhook Not Found."
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $baselink = $row['baselink'];
        $useragent = $row['useragent'];
        $authed = $row['authed'];

        if ($authed) // if variable requires user to be authenticated

        {
            if (!$session["validated"]) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                ));
                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
                header("signature: {$sig}");

                die($response);
            }
        }

        $params = misc\etc\sanitize($_POST['params'] ?? $_GET['params']);
        $body = $_POST['body'] ?? $_GET['body'];
        $contType = misc\etc\sanitize($_POST['conttype'] ?? $_GET['conttype']);

        $url = $baselink .= urldecode($params);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!is_null($body) && !empty($body))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        if (!is_null($contType))
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $contType));

        $response = curl_exec($ch);

        $resp = json_encode(array(
            "success" => true,
            "message" => "Webhook request successful",
            "response" => "$response",
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $resp, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($resp);
    case 'file':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $fileid = misc\etc\sanitize($_POST['fileid'] ?? $_GET['fileid']);

        $row = misc\cache\fetch('KeyAuthFile:' . $secret . ':' . $fileid, "SELECT `name`, `url`, `authed` FROM `files` WHERE `app` = ? AND `id` = ?", [$secret, $fileid], 0);

        if ($row == "not_found") {
            $response = json_encode(array(
                "success" => false,
                "message" => "File not Found"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $filename = $row['name'];
        $url = $row['url'];
        $authed = $row['authed'];

        if ($authed) // if file requires user to be authenticated

        {
            if (!$session["validated"]) {
                $response = json_encode(array(
                    "success" => false,
                    "message" => "$sessionunauthed"
                ));

                $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
                header("signature: {$sig}");

                die($response);
            }
        }

        ini_set('memory_limit', '-1');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($statusCode == 403 || $statusCode == 404) {
            $response = json_encode(array(
                "success" => false,
                "message" => "File no longer works, please notify the application developer."
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }
        $contents = bin2hex($data);

        $response = json_encode(array(
            "success" => true,
            "message" => "File download successful",
            "contents" => "$contents",
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);

    case 'ban':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $reason = misc\etc\sanitize($_POST['reason'] ?? $_GET['reason']) ?? "User banned from triggering ban function in the client";

        if(strlen($reason) > 99) {
            $response = json_encode(array(
                "success" => false,
                "message" => "Reason must be 99 characters or less"
            ));

            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $hwid = misc\etc\sanitize($_POST['hwid'] ?? $_GET['hwid']);
        if (!empty($hwid)) {
            misc\blacklist\add($hwid, "Hardware ID", $secret);
        }
        $ip = api\shared\primary\getIp();
        misc\blacklist\add($ip, "IP Address", $secret);

        misc\mysql\query("UPDATE `users` SET `banned` = ? WHERE `username` = ? AND `app` = ?", [$reason, $credential, $secret]);
        if ($query->affected_rows != 0) {
            misc\cache\purge('KeyAuthUser:' . $secret . ':' . $credential);
            $response = json_encode(array(
                "success" => true,
                "message" => "Successfully Banned User",
                "nonce" => misc\etc\generateRandomString(32)
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        } else {
            $response = json_encode(array(
                "success" => false,
                "message" => "Failed to ban user."
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }
    case 'check':
        // retrieve session info
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        $credential = $session["credential"];
        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        } else {
            $response = json_encode(array(
                "success" => true,
                "message" => "Session is validated.",
                "nonce" => misc\etc\generateRandomString(32)
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }
    case 'changeUsername':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];

        if (!$session["validated"]) {
            $response = json_encode(array(
                "success" => false,
                "message" => "$sessionunauthed"
            ));
            $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
            header("signature: {$sig}");

            die($response);
        }

        $credential = $session["credential"];

        $resp = misc\user\changeUsername($credential, $_POST['newUsername'] ?? $_GET['newUsername'], $secret);
        switch ($resp) {
            case 'already_used':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Username already used!"
                ));
                break;
            case 'failure':
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Failed to change username!"
                ));
                break;
            case 'success':
                misc\session\killSingular($sessionid, $secret);
                $response = json_encode(array(
                    "success" => true,
                    "message" => "Successfully changed username, user logged out.",
                    "nonce" => misc\etc\generateRandomString(32)
                ));
                break;
            default:
                $response = json_encode(array(
                    "success" => false,
                    "message" => "Unhandled Error! Contact us if you need help"
                ));
                break;
        }
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    case 'logout':
        $sessionid = misc\etc\sanitize($_POST['sessionid'] ?? $_GET['sessionid']);
        $session = api\shared\primary\getSession($sessionid, $secret);
        $enckey = $session["enckey"];
        misc\session\killSingular($sessionid, $secret);
        $response = json_encode(array(
            "success" => true,
            "message" => "Successfully logged out.",
            "nonce" => misc\etc\generateRandomString(32)
        ));
        $sig = !is_null($enckey) ? hash_hmac('sha256', $response, $enckey)  : 'No encryption key supplied';
        header("signature: {$sig}");

        die($response);
    default:
        die(json_encode(array(
            "success" => false,
            "message" => "The value inputted for type paramater was not found"
        )));
}
