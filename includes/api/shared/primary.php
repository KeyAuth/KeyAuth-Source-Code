<?php

namespace api\shared\primary;

function vpnCheck($ipaddr)
{
	global $proxycheckapikey;
    $url = "https://proxycheck.io/v2/{$ipaddr}?key={$proxycheckapikey}?vpn=1";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result);
    if ($json
        ->$ipaddr->proxy == "yes")
    {
        return true;
    }
    return false;
}
function getIp()
{
    return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}
function getSession($sessionid, $secret)
{
    global $link; // needed to refrence active MySQL connection
    mysqli_query($link, "DELETE FROM `sessions` WHERE `expiry` < " . time() . "") or die(mysqli_error($link));
    // clean out expired sessions
    $result = mysqli_query($link, "SELECT * FROM `sessions` WHERE `id` = '$sessionid' AND `app` = '$secret'");
    $num = mysqli_num_rows($result);
    if ($num === 0)
    {
        die(json_encode(array(
                    "success" => false,
                    "message" => "Invalid SessionID. Your program either failed to initialize, or never attempted to."
                )) );
    }
    $row = mysqli_fetch_array($result);
    return array(
        "credential" => $row["credential"],
        "enckey" => $row["enckey"],
        "validated" => $row["validated"]
    );
}
