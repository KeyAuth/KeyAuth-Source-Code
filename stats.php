<?php
header("Access-Control-Allow-Origin: *"); // allow any website to request API
header('Content-Type: application/json; charset=utf-8');

include 'includes/misc/autoload.phtml';

$row = misc\cache\fetch('KeyAuthStats', "SELECT FORMAT((select count(1) FROM `accounts`), N'N0') AS 'numAccs',FORMAT((select count(DISTINCT `ip`,`app`) FROM `sessions` WHERE `validated` = 1 AND `expiry` > " . time() . "), N'N0') AS 'numOnlineUsers',FORMAT((select count(1) FROM `keys`), N'N0') AS 'numKeys',FORMAT((select count(1) FROM `apps`), N'N0') AS 'numApps';", 0, 1800);

$numAccs = $row['numAccs'];
$numOnlineUsers = $row['numOnlineUsers'];
$numApps = $row['numApps'];
$numKeys = $row['numKeys'];

die(json_encode(array(
    "accounts" => $numAccs,
    "applications" => $numApps,
    "licenses" => $numKeys,
    "activeUsers" => $numOnlineUsers
)));