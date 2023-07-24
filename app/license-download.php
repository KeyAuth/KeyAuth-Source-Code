<?php

include '../includes/misc/autoload.phtml';

session_start();

if ($_SESSION['role'] == "Reseller") {
    die("Resellers can't access this.");
}

if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}

switch ($_POST['type'] ?? $_GET['type']) {
    case 'users':
        $jsonarray = json_encode(
            array(
                "users" => array(),
                "subscription" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userquery = misc\mysql\query("SELECT * FROM `users` WHERE `app` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userquery->result)) {
        
            $userjson = array(
                "username" => $row["username"],
                "email" => $row["email"],
                "password" => $row["password"],
                "hwid" => $row["hwid"],
                "banned" => $row["banned"],
                "ip" => $row["ip"]
            );
        
            array_push($jsondata->users, $userjson);
        }
        
        $subscriptionquery = misc\mysql\query("SELECT * FROM `subs` WHERE `app` = ? ", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($subscriptionquery->result)) {
        
            $subjson = array(
                "user" => $row["user"],
                "subscription" => "default",
                "expiry" => $row["expiry"]
            );
        
            array_push($jsondata->subscription, $subjson);
        }
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthUsers.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        
        die($newjson);
    
    case 'licenses':
        $query = misc\mysql\query("SELECT * FROM `keys` WHERE `app` = ?",[$_SESSION['app']]);



        while ($row = mysqli_fetch_array($query->result))



            $stringData .= "" . $row['key'] . "\n";



        $stringData = preg_replace(

            '~[\r\n]+~',

            "\r\n",

            trim($stringData)

        );



        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthKeys.txt"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($stringData));


        die($stringData);

    default:
        echo 'Invalid Type or Type does not Exist';
}