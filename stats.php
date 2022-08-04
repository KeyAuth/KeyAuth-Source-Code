<?php

include 'includes/misc/autoload.phtml';

$row = misc\cache\fetch('KeyAuthStats', "SELECT(select count(1) FROM `accounts`) AS 'numAccs',(select count(1) FROM `sessions` WHERE `validated` = 1 AND `expiry` > " . time() . ") AS 'numOnlineUsers',(select count(1) FROM `keys`) AS 'numKeys',(select count(1) FROM `apps`) AS 'numApps';", 0, 1800);

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
