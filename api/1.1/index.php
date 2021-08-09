<?php

/*
KeyAuth 1.1 API Endpoint
This endpoint utilizes the same functions as 1.0, thought it is not encrypted.
Use this for server-sided enviorments where client-side encryption is not needed (i.e. PHP, Node.js)
*/

include '../../includes/connection.php'; // mysql conn
include '../../includes/functions.php'; // general funcs
include '../../includes/api/1.0/index.php'; // v1.0 api funcs
$ownerid = sanitize($_POST['ownerid']); // ownerid of account that owns application
$name = sanitize($_POST['name']); // application name
$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `ownerid` = '$ownerid' AND `name` = '$name'");

if (mysqli_num_rows($result) === 0)

{
    Die("KeyAuth_Invalid");
}

while ($row = mysqli_fetch_array($result))
{
    // app settings
    $secret = $row['secret'];
	$hwidenabled = $row['hwidcheck'];
    $status = $row['enabled'];
    $currentver = $row['ver'];
    $download = $row['download'];
    $webhook = $row['webhook'];
    $appdisabled = $row['appdisabled'];

    // custom error messages
    $usernametaken = $row['usernametaken'];
    $keynotfound = $row['keynotfound'];
    $keyused = $row['keyused'];
    $nosublevel = $row['nosublevel'];
    $usernamenotfound = $row['usernamenotfound'];
    $passmismatch = $row['passmismatch'];
    $noactivesubs = $row['noactivesubs'];
    $keypaused = $row['keypaused'];
    $keyexpired = $row['keyexpired'];
}

switch ($_POST['type'])
{
    case 'init':

        if ($status == "0")

        {

            die(json_encode(array(
                "success" => false,
                "message" => "$appdisabled"
            )));

        }

        $enckey = NULL; // no encryption, so encryption key will be null.
        $sessionid = generateRandomString();
        // session init
        $time = time() + 3600;
        mysqli_query($link, "INSERT INTO `sessions` (`id`, `app`, `expiry`, `enckey`) VALUES ('$sessionid','$secret', '$time', '$enckey')");

        die(json_encode(array(
            "success" => true,
            "message" => "Initialized",
            "sessionid" => $sessionid
        )));

    case 'register':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = sanitize($_POST['username']);

        // Read in license key
        $checkkey = sanitize($_POST['key']);

        // Read in password
        $password = sanitize($_POST['pass']);

        $resp = register($username, $checkkey, $password, "server", $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )));
            case 'key_not_found':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )));
            case 'key_already_used':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )));
            case 'key_paused':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keypaused"
                )));
            case 'key_banned':
                global $banned;
                die(json_encode(array(
                    "success" => false,
                    "message" => "Your license is banned."
                )));
            case 'no_subs_for_level':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `credential` = '$username',`validated` = 'true' WHERE `id` = '$sessionid'");
                die(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$username",
                        "subscriptions" => $resp,
                        "ip" => $_SERVER["HTTP_X_FORWARDED_FOR"]
                    )
                )));
        }
    case 'upgrade':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = sanitize($_POST['username']);

        // search username
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$username' AND `app` = '$secret'");

        // check if username already exists
        if (mysqli_num_rows($result) == 0)

        {

            die(json_encode(array(
                "success" => false,
                "message" => "$usernamenotfound"
            )));

        }

        // Read in key
        $checkkey = sanitize($_POST['key']);

        // search for key
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$checkkey' AND `app` = '$secret'");

        // check if key exists
        if (mysqli_num_rows($result) < 1)

        {

            die(json_encode(array(
                "success" => false,
                "message" => "$keynotfound"
            )));

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

                die(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )));

            }

            // set key to used
            mysqli_query($link, "UPDATE `keys` SET `status` = 'Used' WHERE `key` = '$checkkey'");

            // add current time to key time
            $expiry = $expires + time();

            $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '$secret' AND `level` = '$level'");

            $num = mysqli_num_rows($result);

            if ($num == 0)

            {

                mysqli_close($link);
                die(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )));

            }

            while ($row = mysqli_fetch_array($result))
            {

                $subname = $row['name'];

                mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$subname', '$expiry', '$secret')");

            }

            // success
            die(json_encode(array(
                "success" => true,
                "message" => "Upgraded successfully"
            )));

        }

    case 'login':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        // Read in username
        $username = sanitize($_POST['username']);

        // Read in password
        $password = sanitize($_POST['pass']);

        $resp = login($username, $password, "server", $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$usernamenotfound"
                )));
            case 'pw_mismatch':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )));
            case 'user_banned':
                die(json_encode(array(
                    "success" => false,
                    "message" => "The user is banned"
                )));
            case 'no_active_subs':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$username' WHERE `id` = '$sessionid'");
                die(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$username",
                        "subscriptions" => $resp,
                        "ip" => $_SERVER["HTTP_X_FORWARDED_FOR"]
                    )
                )));
        }

    case 'license':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $checkkey = sanitize($_POST['key']);

        $resp = login($checkkey, $checkkey, "server", $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
            break; // user not registered yet or user was deleted
                
            case 'user_banned':
                die(json_encode(array(
                    "success" => false,
                    "message" => "The user is banned"
                )));
            case 'pw_mismatch':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )));
            case 'no_active_subs':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$checkkey",
                        "subscriptions" => $resp,
                        "ip" => $_SERVER["HTTP_X_FORWARDED_FOR"]
                    )
                )));
        }

        // if login didn't work, attempt to register
        $resp = register($checkkey, $checkkey, $checkkey, "server", $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )));
            case 'key_not_found':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )));
            case 'key_already_used':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )));
            case 'key_paused':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$keypaused"
                )));
            case 'key_banned':
                die(json_encode(array(
                    "success" => false,
                    "message" => "Your license is banned."
                )));
            case 'no_subs_for_level':
                die(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$checkkey",
                        "subscriptions" => $resp,
                        "ip" => $_SERVER["HTTP_X_FORWARDED_FOR"]
                    )
                )));
        }

    case 'var':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }

        $varid = sanitize($_POST['varid']);
        $varquery = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$varid' AND `app` = '$secret'");
        $msg = mysqli_fetch_array($varquery) ['msg'];

        die(json_encode(array(
            "success" => true,
            "message" => "$msg"
        )));

    case 'log':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $credential = $session["credential"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }

        $currtime = time();

        $msg = sanitize($_POST['message']);

        mysqli_query($link, "INSERT INTO `logs` (`logdate`, `logdata`, `logkey`, `logapp`) VALUES ('$currtime','$msg','$credential','$secret')");

        $msg = "📜 Log: " . $msg;

        $pcuser = sanitize($_POST['pcuser']);

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
        "fields" => [["name" => "🔐 Credential:", "value" => "```" . $credential . "```"], ["name" => "💻 PC Name:", "value" => "```" . $pcuser . "```", "inline" => true], ["name" => "🌎 Client IP:", "value" => "```" . $_SERVER["HTTP_X_FORWARDED_FOR"] . "```", "inline" => true], ["name" => "📈 Level:", "value" => "```1```", "inline" => true]]

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

    case 'webhook':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $credential = $session["credential"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }

        $webid = sanitize($_POST['webid']);

        $webquery = mysqli_query($link, "SELECT * FROM `webhooks` WHERE `webid` = '$webid' AND `app` = '$secret'");

        if (mysqli_num_rows($webquery) < 1)

        {

            die(json_encode(array(
                "success" => false,
                "message" => "webhook Not Found."
            )));

        }

        elseif (mysqli_num_rows($webquery) > 0)

        {

            while ($rowww = mysqli_fetch_array($webquery))
            {

                $baselink = $rowww['baselink'];

                $useragent = $rowww['useragent'];

            }

            $params = sanitize($_POST['params']);

            $url = $baselink .= $params;

            $ch = curl_init($url);

            // https://keyauth.com/api/seller/?sellerkey=sellerkeyhere&type=add&expiry=0.00694444444
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            // curl_close($ch);
            die(json_encode(array(
                "success" => true,
                "message" => "webhook request successful"
            )));

        }

    case 'file':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $credential = $session["credential"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }

        $fileid = sanitize($_POST['fileid']);

        $result = mysqli_query($link, "SELECT * FROM `files` WHERE `app` = '$secret' AND `id` = '$fileid'");

        if (mysqli_num_rows($result) < 1)

        {

            die(json_encode(array(
                "success" => false,
                "message" => "File not Found"
            )));

        }

        $filename = mysqli_fetch_array($result) ["name"];
        $file_destination = '../../api/libs/' . $fileid . '/' . $filename;

        $contents = bin2hex(file_decrypt(file_get_contents($file_destination) , "salksalasklsakslakaslkasl"));
        die(json_encode(array(
            "success" => true,
            "message" => "File download successful",
            "contents" => "$contents"
        )));

    case 'ban':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $credential = $session["credential"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }
        mysqli_query($link, "UPDATE `users` SET `banned` = 'User banned from triggering ban function in the client' WHERE `username` = '$credential'");
        if (mysqli_affected_rows($link) != 0)
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Successfully Banned User"
            )));
        }
        else
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Failed to ban user."
            )));
        }
    case 'check':
        // retrieve session info
        $sessionid = sanitize($_POST['sessionid']);
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $credential = $session["credential"];
        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )));
        }
        else
        {
            die(json_encode(array(
                "success" => true,
                "message" => "Session is validated."
            )));
        }
    default:
        die(json_encode(array(
            "success" => false,
            "message" => "Unhandled Type"
        )));
}
?>