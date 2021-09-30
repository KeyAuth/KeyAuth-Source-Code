<?php
include '../../includes/connection.php'; // mysql conn
include '../../includes/functions.php'; // general funcs
include '../../includes/api/1.0/index.php'; // v1.0 api funcs
$ownerid = sanitize(hex2bin($_POST['ownerid'])); // ownerid of account that owns application
$name = sanitize(hex2bin($_POST['name'])); // application name
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
    $vpnblock = $row['vpnblock'];
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
    $hwidmismatch = $row['hwidmismatch'];
    $noactivesubs = $row['noactivesubs'];
    $hwidblacked = $row['hwidblacked'];
    $keypaused = $row['keypaused'];
    $keyexpired = $row['keyexpired'];
}

switch (hex2bin($_POST['type']))
{
    case 'init':

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $secret));
            }
        }

        if (!$status)

        {

            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "$appdisabled"
            )) , $secret));

        }

        $ver = sanitize(Decrypt($_POST['ver'], $secret));

        if ($ver != $currentver)

        {
            // auto-update system
            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "invalidver",
                "download" => "$download"
            ) , JSON_UNESCAPED_SLASHES) , $secret));

        }

        $enckey = sanitize(Decrypt($_POST['enckey'], $secret));
        $sessionid = generateRandomString();
        // session init
        $time = time() + 21600;
        mysqli_query($link, "INSERT INTO `sessions` (`id`, `app`, `expiry`, `enckey`) VALUES ('$sessionid','$secret', '$time', '$enckey')");

        die(Encrypt(json_encode(array(
            "success" => true,
            "message" => "Initialized",
            "sessionid" => $sessionid
        )) , $secret));

    case 'register':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        // Read in username
        $username = sanitize(Decrypt($_POST['username'], $enckey));

        // Read in license key
        $checkkey = sanitize(Decrypt($_POST['key'], $enckey));

        // Read in password
        $password = sanitize(Decrypt($_POST['pass'], $enckey));

        // Read in hwid
        $hwid = sanitize(Decrypt($_POST['hwid'], $enckey));

        $resp = register($username, $checkkey, $password, $hwid, $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )) , $enckey));
            case 'key_not_found':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )) , $enckey));
            case 'key_already_used':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));
            case 'key_paused':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keypaused"
                )) , $enckey));
            case 'key_banned':
                global $banned;
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Your license is banned."
                )) , $enckey));
            case 'hwid_blacked':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_subs_for_level':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `credential` = '$username',`validated` = 'true' WHERE `id` = '$sessionid'");
                die(Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$username",
                        "subscriptions" => $resp,
                        "ip" => $ip
                    )
                )) , $enckey));
        }
    case 'upgrade':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        // Read in username
        $username = Decrypt($_POST['username'], $enckey);

        $username = sanitize($username);

        // search username
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$username' AND `app` = '$secret'");

        // check if username already exists
        if (mysqli_num_rows($result) == 0)

        {

            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "$usernamenotfound"
            )) , $enckey));

        }

        // Read in key
        $checkkey = sanitize(Decrypt($_POST['key'], $enckey));

        // search for key
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$checkkey' AND `app` = '$secret'");

        // check if key exists
        if (mysqli_num_rows($result) < 1)

        {

            die(Encrypt(json_encode(array(
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

                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));

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
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));

            }

            $subname = mysqli_fetch_array($result) ['name'];

            mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$subname', '$expiry', '$secret')");

            // success
            die(Encrypt(json_encode(array(
                "success" => true,
                "message" => "Upgraded successfully"
            )) , $enckey));

        }

    case 'login':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        // Read in username
        $username = sanitize(Decrypt($_POST['username'], $enckey));

        // Read in HWID
        $hwid = sanitize(Decrypt($_POST['hwid'], $enckey));

        // Read in password
        $password = sanitize(Decrypt($_POST['pass'], $enckey));

        $resp = login($username, $password, $hwid, $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernamenotfound"
                )) , $enckey));
            case 'pw_mismatch':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )) , $enckey));
            case 'user_banned':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "The user is banned"
                )) , $enckey));
            case 'hwid_mismatch':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )) , $enckey));
            case 'hwid_blacked':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_active_subs':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$username' WHERE `id` = '$sessionid'");
                die(Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$username",
                        "subscriptions" => $resp,
                        "ip" => $ip
                    )
                )) , $enckey));
        }

    case 'license':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];
        $checkkey = sanitize(Decrypt($_POST['key'], $enckey));

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        $hwid = sanitize(Decrypt($_POST['hwid'], $enckey));

        $resp = login($checkkey, $checkkey, $hwid, $secret, $hwidenabled);
        switch ($resp)
        {
            case 'un_not_found':
            break; // user not registered yet or user was deleted
                
            case 'hwid_mismatch':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidmismatch"
                )) , $enckey));
            case 'user_banned':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "The user is banned"
                )) , $enckey));
            case 'pw_mismatch':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$passmismatch"
                )) , $enckey));
            case 'hwid_blacked':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_active_subs':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$noactivesubs"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$checkkey",
                        "subscriptions" => $resp,
                        "ip" => $ip
                    )
                )) , $enckey));
        }

        // if login didn't work, attempt to register
        $resp = register($checkkey, $checkkey, $checkkey, $hwid, $secret);
        switch ($resp)
        {
            case 'username_taken':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$usernametaken"
                )) , $enckey));
            case 'key_not_found':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keynotfound"
                )) , $enckey));
            case 'key_already_used':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keyused"
                )) , $enckey));
            case 'key_paused':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$keypaused"
                )) , $enckey));
            case 'key_banned':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "Your license is banned."
                )) , $enckey));
            case 'hwid_blacked':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$hwidblacked"
                )) , $enckey));
            case 'no_subs_for_level':
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "$nosublevel"
                )) , $enckey));
            default:
                mysqli_query($link, "UPDATE `sessions` SET `validated` = 'true',`credential` = '$checkkey' WHERE `id` = '$sessionid'");
                die(Encrypt(json_encode(array(
                    "success" => true,
                    "message" => "Logged in!",
                    "info" => array(
                        "username" => "$checkkey",
                        "subscriptions" => $resp,
                        "ip" => $ip
                    )
                )) , $enckey));
        }

    case 'var':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
        // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
        if (!$validated)
        {
            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "Session is not validated."
            )) , $enckey));
        }

        $varid = sanitize(Decrypt($_POST['varid'], $enckey));
        $varquery = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$varid' AND `app` = '$secret'");
        $msg = mysqli_fetch_array($varquery) ['msg'];

        die(Encrypt(json_encode(array(
            "success" => true,
            "message" => "$msg"
        )) , $enckey));

    case 'log':
        // retrieve session info
        $sessionid = sanitize(hex2bin($_POST['sessionid']));
        $session = getsession($sessionid, $secret);
        $enckey = $session["enckey"];

        if ($vpnblock)
        {
            if (vpn_check($ip))
            {
                die(Encrypt(json_encode(array(
                    "success" => false,
                    "message" => "VPNs are disallowed on this application"
                )) , $enckey));
            }
        }

        $credential = $session["credential"];

        $currtime = time();

        $msg = sanitize(Decrypt($_POST['message'], $enckey));

        $pcuser = sanitize(Decrypt($_POST['pcuser'], $enckey));

        mysqli_query($link, "INSERT INTO `logs` (`logdate`, `logdata`, `credential`, `pcuser`,`logapp`) VALUES ('$currtime','$msg',NULLIF('$credential', ''),NULLIF('$pcuser', ''),'$secret')");

        $credential = $session["credential"] ?? "N/A";

        $msg = "📜 Log: " . $msg;

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
    $sessionid = sanitize(hex2bin($_POST['sessionid']));
    $session = getsession($sessionid, $secret);
    $enckey = $session["enckey"];

    if ($vpnblock)
    {
        if (vpn_check($ip))
        {
            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "VPNs are disallowed on this application"
            )) , $enckey));
        }
    }

    $credential = $session["credential"];
    $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
    // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
    if (!$validated)
    {
        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "Session is not validated."
        )) , $enckey));
    }

    $webid = sanitize(Decrypt($_POST['webid'], $enckey));

    $webquery = mysqli_query($link, "SELECT * FROM `webhooks` WHERE `webid` = '$webid' AND `app` = '$secret'");

    if (mysqli_num_rows($webquery) < 1)

    {

        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "webhook Not Found."
        )) , $enckey));

    }

    elseif (mysqli_num_rows($webquery) > 0)

    {

        while ($rowww = mysqli_fetch_array($webquery))
        {

            $baselink = $rowww['baselink'];

            $useragent = $rowww['useragent'];

        }

        $params = sanitize(Decrypt($_POST['params'], $enckey));

        $url = $baselink .= $params;

        $ch = curl_init($url);

        // https://keyauth.com/api/seller/?sellerkey=sellerkeyhere&type=add&expiry=0.00694444444
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        // curl_close($ch);
        die(Encrypt(json_encode(array(
            "success" => true,
            "message" => "webhook request successful",
            "resp" => "$response"
        )) , $enckey));

    }

case 'file' :
// retrieve session info
$sessionid = sanitize(hex2bin($_POST['sessionid']));
$session = getsession($sessionid, $secret);
$enckey = $session["enckey"];

if ($vpnblock)
{
    if (vpn_check($ip))
    {
        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "VPNs are disallowed on this application"
        )) , $enckey));
    }
}

$credential = $session["credential"];
$validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
// ensure session is validated before returning authenticated var --> todo: unauthenticated vars
if (!$validated)
{
    die(Encrypt(json_encode(array(
        "success" => false,
        "message" => "Session is not validated."
    )) , $enckey));
}

$fileid = sanitize(Decrypt($_POST['fileid'], $enckey));

$result = mysqli_query($link, "SELECT * FROM `files` WHERE `app` = '$secret' AND `id` = '$fileid'");

if (mysqli_num_rows($result) < 1)

{

    die(Encrypt(json_encode(array(
        "success" => false,
        "message" => "File not Found"
    )) , $enckey));

}

while ($row = mysqli_fetch_array($result))
{
    $filename = $row['name'];
    $url = $row['url'];
}

if(!is_null($url))
{
	$contents = bin2hex(file_get_contents($url));
}
else
{
$file_destination = '../../api/libs/' . $fileid . '/' . $filename;

$contents = bin2hex(file_decrypt(file_get_contents($file_destination) , "salksalasklsakslakaslkasl"));
}

die(Encrypt(json_encode(array(
    "success" => true,
    "message" => "File download successful",
    "contents" => "$contents"
)) , $enckey));

case 'ban':
    // retrieve session info
    $sessionid = sanitize(hex2bin($_POST['sessionid']));
    $session = getsession($sessionid, $secret);
    $enckey = $session["enckey"];

    if ($vpnblock)
    {
        if (vpn_check($ip))
        {
            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "VPNs are disallowed on this application"
            )) , $enckey));
        }
    }

    $credential = $session["credential"];
    $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
    // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
    if (!$validated)
    {
        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "Session is not validated."
        )) , $enckey));
    }

    $hwid = sanitize(Decrypt($_POST['hwid'], $enckey));
    if (!empty($hwid))
    {
        mysqli_query($link, "INSERT INTO `bans` (`hwid`, `type`, `app`) VALUES ('$hwid','hwid', '$secret')");
    }

    mysqli_query($link, "INSERT INTO `bans` (`ip`, `type`, `app`) VALUES ('$ip','ip', '$secret')");

    mysqli_query($link, "UPDATE `users` SET `banned` = 'User banned from triggering ban function in the client' WHERE `username` = '$credential'");
    if (mysqli_affected_rows($link) != 0)
    {
        die(Encrypt(json_encode(array(
            "success" => true,
            "message" => "Successfully Banned User"
        )) , $enckey));
    }
    else
    {
        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "Failed to ban user."
        )) , $enckey));
    }
case 'check':
    // retrieve session info
    $sessionid = sanitize(hex2bin($_POST['sessionid']));
    $session = getsession($sessionid, $secret);
    $enckey = $session["enckey"];

    if ($vpnblock)
    {
        if (vpn_check($ip))
        {
            die(Encrypt(json_encode(array(
                "success" => false,
                "message" => "VPNs are disallowed on this application"
            )) , $enckey));
        }
    }

    $credential = $session["credential"];
    $validated = filter_var($session["validated"], FILTER_VALIDATE_BOOLEAN);
    // ensure session is validated before returning authenticated var --> todo: unauthenticated vars
    if (!$validated)
    {
        die(Encrypt(json_encode(array(
            "success" => false,
            "message" => "Session is not validated."
        )) , $enckey));
    }
    else
    {
        die(Encrypt(json_encode(array(
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