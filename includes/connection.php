<?php

//error_reporting(0); // disable useless warnings, should turn this on if you need to debug a problem

/* Attempt MySQL server connection. Assuming you are running MySQL

server with default setting (user 'root' with no password) */

$link = mysqli_connect("localhost", "root", "", "main");

// Check connection status

if ($link === false) {
    http_response_code(503);
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$logwebhook = ""; // discord webhook which receives login logs and keys created

$adminwebhook = ""; // discord webhook which receives admin actions

$webhookun = "KeyAuth Logs"; // webhook username

$adminwebhookun = "KeyAuth Admin Logs"; // admin webhook's username


$adminapikey = ""; // api key for api/admin (an api only my staff can use)

$proxycheckapikey = ""; // proxycheck.io API key to check if IP is considered a VPN

$bunnyNetKey = ""; // bunny.net CDN used for custom domains
