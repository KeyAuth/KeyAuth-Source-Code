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

        // Create an array to hold the keys
        $keysArray = array();
        
        while ($row = mysqli_fetch_array($query->result)) {
            // Add each key to the array
            $keysArray[] = array(
                "key" => $row['key'],
                "level" => $row['level'],
                "expiry" => $row['expires'] / 86400
            );
        }
        
        // Convert the array to a JSON string
        $jsonData = json_encode($keysArray);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/json'); // Set the content type to JSON
        header('Content-Disposition: attachment; filename="KeyAuthKeys.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($jsonData));
        
        echo $jsonData;
        
        die($stringData);
    case 'logs':
        $jsonarray = json_encode(
            array(
                "logs" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userlogquery = misc\mysql\query("SELECT * FROM `logs` WHERE `logapp` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userlogquery->result)) {
        
            $userlogjson = array(
                "logdate" => $row["logdate"],
                "logdata" => $row["logdata"],
                "credential" => $row["credential"],
                "pcuser" => $row["pcuser"]
            );
        
            array_push($jsondata->logs, $userlogjson);
        }
        
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthUserLogs.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        
        die($newjson);     
    case 'auditLog':
        $jsonarray = json_encode(
            array(
                "auditLog" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userquery = misc\mysql\query("SELECT * FROM `auditLog` WHERE `app` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userquery->result)) {
        
            $userjson = array(
                "id" => $row["id"],
                "user" => $row["user"],
                "event" => $row["event"],
                "time" => $row["time"],
            );
        
            array_push($jsondata->auditLog, $userjson);
        }
        
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthAuditLogs.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        die($newjson);     
    default:
        echo 'Invalid Type or Type does not Exist';
}
