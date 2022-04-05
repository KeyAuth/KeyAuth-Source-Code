<?php



ini_set('display_errors', 'Off');

error_reporting(0);



include '../../../includes/connection.php';

session_start();

// $myFile = "KeyAuthBulk.txt";// 
// $fo = fopen($myFile, 'w') or die("can't open file");
$abc = time() - 10;
$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `gendate` >= '$abc' AND `app` = '".$_SESSION['app']."' AND `genby` = '".$_SESSION['username']."'");
while ($row = mysqli_fetch_array($result))
$stringData.="".$row['key']."\n";
// fwrite($fo, $stringData);// 
// fclose($fo);

$stringData = preg_replace(
        '~[\r\n]+~',
        "\r\n",
        trim($stringData)
    );

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="KeyAuthBulk.txt"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($stringData));
die($stringData);

?>