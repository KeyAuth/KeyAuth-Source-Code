<?php

/*	
This is the reseller system. 
I used to use PayPal built in, though that wasn't as nice design wise, didn't handle payments that weren't instant, and several customers wanted Sellix instead.
It currently only works with Sellix, as that was what was demanded.	
It handles the custom fields from sellix webhook and gives the reseller account an increase in balance
*/

include '../../includes/connection.php';

$app = strip_tags(trim(mysqli_real_escape_string($link, $_GET['app'])));
$result = mysqli_query($link, "SELECT `name`,`sellixsecret` FROM `apps` WHERE `secret` = '$app'");

if (mysqli_num_rows($result) == 0) { // if no application was found with the supplied secret as the 'app' paramater
    die("Failure: no applications found");
}
$row = mysqli_fetch_array($result);
$name = $row["name"];
$secret = $row["sellixsecret"];

$payload = file_get_contents('php://input');
$header_signature = $_SERVER["HTTP_X_SELLIX_SIGNATURE"];
$signature = hash_hmac('sha512', $payload, $secret);
if (!hash_equals($signature, $header_signature)) { // if the sellix webhook secret the request was sent from didn't match the one set in the database
  die("Failure: authentication with sellix secret failed");
}

$json = json_decode($payload);
$data = $json->data;
$custom = $data->custom_fields; // getting custom fields, the hidden fields on KeyAuth sellix embed which provide sellix the KeyAuth username
    
    $result = mysqli_query($link, "SELECT `balance` FROM `accounts` WHERE `username` = '".$custom->username."' AND `app` = '$name'");
	
if (mysqli_num_rows($result) == 0) { // if reseller not found
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
                            
            $amount = $data->quantity; // find quantity of keys purchased
						// then given the duration of keys they purchased, add to their balance
			switch ($data->product_title) {
				case 'Day Reseller Keys':
					$day = $day + $amount;
					break;
				case 'Week Reseller Keys':
					$week = $week + $amount;
					break;
				case 'Month Reseller Keys':
					$month = $month + $amount;
					break;
				case 'Lifetime Reseller Keys':
					$lifetime = $lifetime + $amount;
					break;
			}
            
           
            $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
            // set balance
            mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '".$custom->username."'");
			die("Success: Reseller Balance Increased");

?>