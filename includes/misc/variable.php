<?php

namespace misc\variable;

use misc\etc;
use misc\cache;

function add($name, $data, $authed, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$name = etc\sanitize($name);
	$data = etc\sanitize($data);
	$authed = etc\sanitize($authed);

	$var_check = mysqli_query($link, "SELECT 1 FROM `vars` WHERE `varid` = '$name' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_num_rows($var_check) > 0) {
		return 'exists';
	}
	mysqli_query($link, "INSERT INTO `vars`(`varid`, `msg`, `app`, `authed`) VALUES ('$name','$data','" . ($secret ?? $_SESSION['app']) . "', '$authed')");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
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
	mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purgePattern('KeyAuthVar:' . ($secret ?? $_SESSION['app']));
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteSingular($var, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$var = etc\sanitize($var);

	mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `varid` = '$var'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthVar:' . ($secret ?? $_SESSION['app']) . ':' . $var);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function edit($name, $data, $authed, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$name = etc\sanitize($name);
	$data = etc\sanitize($data);
	$authed = etc\sanitize($authed);

	mysqli_query($link, "UPDATE `vars` SET `msg` = '$data', `authed` = '$authed' WHERE `varid` = '$name' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthVar:' . ($secret ?? $_SESSION['app']) . ':' . $var);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
