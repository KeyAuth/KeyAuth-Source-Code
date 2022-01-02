<?php
error_reporting(0); // disable PHP errors, you can comment out or turn on logs if you need to fix issue
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "root", "", "keyauth");
// Check connection status
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$logwebhook = ""; // discord webhook which receives login logs and keys created
$adminwebhook = ""; // discord webhook which receives admin actions
$webhookun = "KeyAuth Logs"; // webhook username
$adminwebhookun = "KeyAuth Admin Logs"; // admin webhook's username

$adminapikey = ""; // api key for api/admin (an api only my staff can use)
$shoppyAPIkey = ""; // shoppy.gg API key for my staff to look up orders
$proxycheckapikey = ""; // proxycheck.io API key to check if IP is considered a VPN
?>