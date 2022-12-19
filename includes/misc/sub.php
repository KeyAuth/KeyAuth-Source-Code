<?php

namespace misc\sub;

use misc\etc;
use misc\cache;

function deleteSingular($subscription, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$subscription = etc\sanitize($subscription);

	mysqli_query($link, "DELETE FROM `subscriptions` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `name` = '$subscription'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthSubscriptions:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function add($name, $level, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$name = etc\sanitize($name);
	$level = etc\sanitize($level);

	mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('$name','$level', '" . ($secret ?? $_SESSION['app']) . "')");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthSubscriptions:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}