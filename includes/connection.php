<?php

// set headers for increased security. I would set via htaccess though FluxCDN doesn't have mod_security apache module enabled
header("x-xss-protection: 1; mode=block");
header("strict-transport-security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: interest-cohort=()");
header("x-content-type-options: nosniff");
header("x-frame-options: DENY");
header("Referrer-Policy: no-referrer");

// recommended to minimize the attackers knowledge of potential vulreabilities in PHP version
header_remove("X-Powered-By");

// disable PHP errors
error_reporting(0);

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "keyauth_root", "Zsc3L4tUvJVdv4", "keyauth_main");
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 
// Print host information
// echo "Connect Successfully. Host info: " . mysqli_get_host_info($link);
?>