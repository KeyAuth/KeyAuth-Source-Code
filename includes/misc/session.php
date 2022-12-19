<?php

namespace misc\session;

use misc\etc;
use misc\cache;

function killAll($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "DELETE FROM `sessions` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purgePattern('KeyAuthState:' . ($secret ?? $_SESSION['app']));
		cache\purgePattern('KeyAuthStateDuplicates:' . ($secret ?? $_SESSION['app']));
		return 'success';
	} else {
		return 'failure';
	}
}
function killSingular($id, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$id = etc\sanitize($id);

	$result = mysqli_query($link, "SELECT `ip` FROM `sessions` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `id` = '$id'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthStateDuplicates:' . ($secret ?? $_SESSION['app']) . ':' . $row['ip']);

	mysqli_query($link, "DELETE FROM `sessions` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `id` = '$id'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthState:' . ($secret ?? $_SESSION['app']) . ':' . $id);
		return 'success';
	} else {
		return 'failure';
	}
}