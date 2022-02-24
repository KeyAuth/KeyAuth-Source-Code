<?php

include 'includes/connection.php';

$result = mysqli_query($link,"select count(1) FROM `accounts`");
$row = mysqli_fetch_array($result);

$accs = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `apps`");
$row = mysqli_fetch_array($result);

$apps = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `keys`");
$row = mysqli_fetch_array($result);

$keys = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `sessions` WHERE `validated` = 1");
$row = mysqli_fetch_array($result);

$activeUsers = number_format($row[0]);

mysqli_close($link);

// output JSON
die(json_encode(array(
    "accounts" => $accs,
    "applications" => $apps,
    "licenses" => $keys,
    "activeUsers" => $activeUsers
)));
		
?>