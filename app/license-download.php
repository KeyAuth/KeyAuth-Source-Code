<?php

include '../includes/connection.php';


session_start();

if ($_SESSION['role'] == "Reseller") {
    die("Resellers can't access this.");
}

if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}

$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '" . $_SESSION['app'] . "'");



while ($row = mysqli_fetch_array($result))



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