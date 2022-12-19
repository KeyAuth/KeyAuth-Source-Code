<?php

namespace misc\app;

use misc\etc;
use misc\cache;

function pause($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = `expiry`-" . time() . " WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `expiry` > '" . time() . "'");
	mysqli_query($link, "UPDATE `apps` SET `paused` = 1 WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	
	$result = mysqli_query($link, "SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
	cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
function unpause($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	mysqli_query($link, "UPDATE `subs` SET `paused` = 0, `expiry` = `expiry`+" . time() . " WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `paused` = 1");
	mysqli_query($link, "UPDATE `apps` SET `paused` = 0 WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");

	$result = mysqli_query($link, "SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
	cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
function addHash($hash, $secret = null)
{
	$hash = etc\sanitize($hash);
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$result = mysqli_query($link, "SELECT `hash`, `name`, `ownerid` FROM `apps` WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	$row = mysqli_fetch_array($result);
	$oldHash = $row["hash"];
	$name = $row["hash"];
	$ownerid = $row["ownerid"];

	$newHash = $oldHash .= $hash;

	mysqli_query($link, "UPDATE `apps` SET `hash` = '$newHash' WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");

	if (mysqli_affected_rows($link) > 0) {
		cache\purge("KeyAuthApp:{$name}:{$ownerid}");
		return 'success';
	}
	return 'failure';
}
function resetHash($secret = null, $name = null, $ownerid = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	$name = $name ?? $_SESSION['name'];
	$ownerid = $ownerid ?? $_SESSION['ownerid'];
	
	mysqli_query($link, "UPDATE `apps` SET `hash` = NULL WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");

	if (mysqli_affected_rows($link) > 0) {
		cache\purge("KeyAuthApp:{$name}:{$ownerid}");
		return 'success';
	}
	return 'failure';
}