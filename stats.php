<?php

include 'includes/connection.php';

// database stats
$result = mysqli_query($link,"select count(1) FROM `accounts`");
$row = mysqli_fetch_array($result);

$accs = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `apps`");
$row = mysqli_fetch_array($result);

$apps = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `keys`");
$row = mysqli_fetch_array($result);

$keys = number_format($row[0]);

mysqli_close($link);

// availability stats
$url = "https://stats.uptimerobot.com/api/getMonitorList/2DrzGFk4PY"; // uptimerobot status page https://stats.uptimerobot.com/2DrzGFk4PY

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
$json = json_decode($resp);

$discord = $json->psp->monitors[0]->{'30dRatio'}->ratio;
$website = $json->psp->monitors[1]->{'30dRatio'}->ratio;
$api = $json->psp->monitors[2]->{'30dRatio'}->ratio;

$avgUptime = number_format(array_sum(array($discord, $website, $api)) / 3,2, '.', '');

// output JSON
die(json_encode(array(
    "accounts" => $accs,
    "applications" => $apps,
    "licenses" => $keys,
    "uptime" => $avgUptime
)));
		
?>