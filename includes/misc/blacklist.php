<?php

namespace misc\blacklist;

use misc\etc;
use misc\cache;

function add($data, $type, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$data = etc\sanitize($data);
	$type = etc\sanitize($type);

	switch ($type) {
		case 'IP Address':
			mysqli_query($link, "INSERT INTO `bans`(`ip`, `type`, `app`) VALUES ('$data','ip','" . ($secret ?? $_SESSION['app']) . "')");
			cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':' . $data);
			break;
		case 'Hardware ID':
			mysqli_query($link, "INSERT INTO `bans`(`hwid`, `type`, `app`) VALUES ('$data','hwid','" . ($secret ?? $_SESSION['app']) . "')");
			cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':*:' . $data);
			break;
		default:
			return 'invalid';
	}
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthBlacks:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteAll($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']));
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthBlacks:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteSingular($blacklist, $type, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$blacklist = etc\sanitize($blacklist);
	$type = etc\sanitize($type);

	switch ($type) {
		case 'ip':
			mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `ip` = '$blacklist'");
			cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':' . $blacklist);
			break;
		case 'hwid':
			mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `hwid` = '$blacklist'");
			cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':*:' . $blacklist);
			break;
		default:
			return 'invalid';
	}
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthBlacks:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function addWhite($ip, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$ip = etc\sanitize($ip);

	mysqli_query($link, "INSERT INTO `whitelist`(`ip`, `app`) VALUES ('$ip','" . ($secret ?? $_SESSION['app']) . "')");
	cache\purge('KeyAuthWhitelist:' . ($secret ?? $_SESSION['app']) . ':' . $ip);
			
	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteWhite($ip, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$ip = etc\sanitize($ip);

	mysqli_query($link, "DELETE FROM `whitelist` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `ip` = '$ip'");
	cache\purge('KeyAuthWhitelist:' . ($secret ?? $_SESSION['app']) . ':' . $ip);
			
	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	} else {
		return 'failure';
	}
}