<?php

include '../../includes/connection.php';
include '../../includes/functions.php';



if (!isset($_SERVER["HTTP_X_SELLIX_SIGNATURE"]) && !isset($_SERVER["HTTP_X_SHOPPY_SIGNATURE"]))
{
	die("Request isn't coming from Sellix or Shoppy.");
}

if (isset($_SERVER["HTTP_X_SELLIX_SIGNATURE"]))
{
    $app = strip_tags(trim(mysqli_real_escape_string($link, $_GET['app'])));
    $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '$app'");
	
    if (mysqli_num_rows($result) == 0)
    { // if no application was found with the supplied secret as the 'app' paramater
        die("Failure: application not found");
    }
    $row = mysqli_fetch_array($result);
    $name = $row["name"];
    $secret = $row["sellixsecret"];
	$dayproduct = $row["sellixdayproduct"];
	$weekproduct = $row["sellixweekproduct"];
	$monthproduct = $row["sellixmonthproduct"];
	$lifetimeproduct = $row["sellixlifetimeproduct"];

    $payload = file_get_contents('php://input');
    $header_signature = sanitize($_SERVER["HTTP_X_SELLIX_SIGNATURE"]);
    $signature = hash_hmac('sha512', $payload, $secret);
    if (!hash_equals($signature, $header_signature))
    { // if the sellix webhook secret the request was sent from didn't match the one set in the database
        die("Failure: authentication with sellix secret failed");
    }

    $json = json_decode($payload);
    $data = $json->data;
    $custom = $data->custom_fields; // getting custom fields, the hidden fields on KeyAuth sellix embed which provide sellix the KeyAuth username
    $result = mysqli_query($link, "SELECT `balance` FROM `accounts` WHERE `username` = '" . sanitize($custom->username) . "' AND `app` = '$name'");

    if (mysqli_num_rows($result) == 0)
    { // if reseller not found
        die("Failure: No account with the supplied username under this application found");
    }
    // getting the balance of each key length for the specified reseller account
    $row = mysqli_fetch_array($result);
    $balance = $row["balance"];
    $balance = explode("|", $balance);
    $day = $balance[0];
    $week = $balance[1];
    $month = $balance[2];
    $threemonth = $balance[3];
    $sixmonth = $balance[4];
    $lifetime = $balance[5];

    $amount = sanitize($data->quantity); // find quantity of keys purchased
    // then given the duration of keys they purchased, add to their balance
    switch (sanitize($data->product_id))
    {
		case $dayproduct:
			$day = $day + $amount;
			break;
		case $weekproduct:
			$week = $week + $amount;
			break;
		case $monthproduct:
			$month = $month + $amount;
			break;
		case $lifetimeproduct:
			$lifetime = $lifetime + $amount;
			break;
		default:
			die("You didn't set product id in app settings.");
    }

    $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
    // set balance
    mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '" . sanitize($custom->username) . "'");
    die("Success: Reseller Balance Increased");
}

// else shoppy

$app = strip_tags(trim(mysqli_real_escape_string($link, $_GET['app'])));
$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '$app'");

if (mysqli_num_rows($result) == 0)
{ // if no application was found with the supplied secret as the 'app' paramater
    die("Failure: application not found");
}

$row = mysqli_fetch_array($result);
$name = $row["name"];
$secret = $row["shoppysecret"];
$dayproduct = $row["shoppydayproduct"];
$weekproduct = $row["shoppyweekproduct"];
$monthproduct = $row["shoppymonthproduct"];
$lifetimeproduct = $row["shoppylifetimeproduct"];

$payload = file_get_contents('php://input');
$header_signature = sanitize($_SERVER["HTTP_X_SHOPPY_SIGNATURE"]);
$signature = hash_hmac('sha512', $payload, $secret);
if (!hash_equals($signature, $header_signature))
{ 
	// if the shoppy webhook secret the request was sent from didn't match the one set in the database
    die("Failure: authentication with shoppy secret failed");
}
$json = json_decode($payload);
$un = sanitize($json->data->order->custom_fields[0]->value);

$productid = sanitize($json->data->order->product_id);

$result = mysqli_query($link, "SELECT `balance` FROM `accounts` WHERE `username` = '$un' AND `app` = '$name'");

if (mysqli_num_rows($result) == 0)
{ 
	// if reseller not found
    die("Failure: No account with the supplied username under this application found");
}
// getting the balance of each key length for the specified reseller account
$row = mysqli_fetch_array($result);
$balance = $row["balance"];
$balance = explode("|", $balance);
$day = $balance[0];
$week = $balance[1];
$month = $balance[2];
$threemonth = $balance[3];
$sixmonth = $balance[4];
$lifetime = $balance[5];

$amount = sanitize($json->data->order->quantity); // find quantity of keys purchased
// then given the duration of keys they purchased, add to their balance
switch ($productid)
{
    case $dayproduct:
        $day = $day + $amount;
		break;
    case $weekproduct:
        $week = $week + $amount;
		break;
    case $monthproduct:
        $month = $month + $amount;
		break;
    case $lifetimeproduct:
        $lifetime = $lifetime + $amount;
		break;
	default:
		die("You didn't set product id in app settings.");
}

$balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
// set balance
mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '$un'");
die("Success: Reseller Balance Increased");

?>