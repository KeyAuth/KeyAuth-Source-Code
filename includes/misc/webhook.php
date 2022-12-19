<?php

namespace misc\webhook;

use misc\etc;
use misc\cache;

function add($baseLink, $userAgent, $authed, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$baseLink = etc\sanitize($baseLink);
	$userAgent = etc\sanitize($userAgent);
	$authed = etc\sanitize($authed);

	$webid = etc\generateRandomString();
	if (is_null($userAgent))
		$userAgent = "KeyAuth";
	mysqli_query($link, "INSERT INTO `webhooks` (`webid`, `baselink`, `useragent`, `app`, `authed`) VALUES ('$webid','$baseLink', '$userAgent', '" . ($secret ?? $_SESSION['app']) . "', '$authed')");
	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteSingular($webhook, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$webhook = etc\sanitize($webhook);

	mysqli_query($link, "DELETE FROM `webhooks` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `webid` = '$webhook'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthWebhook:' . ($secret ?? $_SESSION['app']) . ':' . $webhook);
		return 'success';
	} else {
		return 'failure';
	}
}