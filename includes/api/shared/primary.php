<?php
namespace api\shared\primary;

use misc\etc;
use misc\cache;

function vpnCheck($ipaddr)
{
	$url = "http://ip-api.com/json/{$ipaddr}?fields=16908288"; // returns fields: proxy,hosting
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	$resp = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$json = json_decode($resp);
	
	if($httpcode == 429) {
		return false;
	}
	
	if($json->proxy || $json->hosting) {
		return true;
	}
	return false;
}
function getIp()
{
    return etc\sanitize($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
}
function getSession($sessionid, $secret)
{
    // had to name it 'state' instead of 'session' because Redis wouldn't save key with 'session' in it
    $row = cache\fetch('KeyAuthState:' . $secret . ':' . $sessionid, "SELECT * FROM `sessions` WHERE `id` = '$sessionid' AND `app` = '$secret' AND `expiry` > " . time() . "", 0);
    if ($row == "not_found") {
        die(json_encode(array(
            "success" => false,
            "message" => "Invalid SessionID. Your program either failed to initialize, or never attempted to."
        )));
    }
    return array(
        "credential" => $row["credential"],
        "enckey" => $row["enckey"],
        "validated" => $row["validated"]
    );
}