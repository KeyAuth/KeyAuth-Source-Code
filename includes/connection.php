<?php

// added headers for security
header("x-xss-protection: 1; mode=block");
header("strict-transport-security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: interest-cohort=()");
header("x-content-type-options: nosniff");
header("x-frame-options: DENY");
header("Referrer-Policy: no-referrer");

// disable PHP errors
error_reporting(0);

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "root", "", "keyauth");
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$logwebhook = "";
$adminwebhook = "";
$webhookun = "KeyAuth Logs";
$adminwebhookun = "KeyAuth Admin Logs";

$adminapikey = "";
$shoppyAPIkey = "";
$proxycheckapikey = "";
?>