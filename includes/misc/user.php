<?php

namespace misc\user;

use misc\etc;
use misc\cache;
use misc\blacklist;

function deleteSingular($username, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);

	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username' AND `owner` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'nope';
		}
	}
	
	mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username'");
	mysqli_query($link, "DELETE FROM `uservars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username'");
	mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");

	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function resetSingular($username, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	
	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username' AND `owner` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'nope';
		}
	}

	mysqli_query($link, "UPDATE `users` SET `hwid` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function setVariable($user, $var, $data, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$user = etc\sanitize($user);
	$var = etc\sanitize($var);
	$data = etc\sanitize($data);

	if ($user == "all") {
		$result = mysqli_query($link, "SELECT `username` FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'missing';
		}
		$rows = array();
		while ($r = mysqli_fetch_assoc($result)) {
			$rows[] = $r;
		}
		foreach ($rows as $row) {
			mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '" . $row['username'] . "', '" . ($secret ?? $_SESSION['app']) . "')");
		}
		cache\purgePattern('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']));
	} else {
		mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '$user', '" . ($secret ?? $_SESSION['app']) . "')");
		cache\purge('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']) . ':' . $var . ':' . $user);
	}
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUserVars:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function ban($username, $reason, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$reason = etc\sanitize($reason);
	
	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username' AND `owner` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'nope';
		}
	}

	$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	if (mysqli_num_rows($result) < 1) {
		return 'missing';
	}
	$row = mysqli_fetch_array($result);
	$hwid = $row["hwid"];
	$ip = $row["ip"];
	mysqli_query($link, "UPDATE `users` SET `banned` = '$reason' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	if (!is_null($hwid)) {
		blacklist\add($hwid, "Hardware ID", ($secret ?? $_SESSION['app']));
	}
	if (!is_null($ip)) {
		blacklist\add($ip, "IP Address", ($secret ?? $_SESSION['app']));
	}
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function unban($username, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	
	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username' AND `owner` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'nope';
		}
	}

	$result = mysqli_query($link, "SELECT `hwid`, `ip` FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	if (mysqli_num_rows($result) < 1) {
		return 'missing';
	}
	$row = mysqli_fetch_array($result);
	$hwid = $row["hwid"];
	$ip = $row["ip"];
	cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':' . $ip);
	if(!is_null($hwid)) {
		cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':*:' . $hwid);
	}
	mysqli_query($link, "DELETE FROM `bans` WHERE `hwid` = '$hwid' OR `ip` = '$ip' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	mysqli_query($link, "UPDATE `users` SET `banned` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteVar($username, $var, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$var = etc\sanitize($var);

	mysqli_query($link, "DELETE FROM `uservars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username' AND `name` = '$var'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']) . ':' . $var . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUserVars:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteSub($username, $sub, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$sub = etc\sanitize($sub);

	mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username' AND `subscription` = '$sub'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		return 'success';
	} else {
		return 'failure';
	}
}
function extend($username, $sub, $expiry, $activeOnly = 0, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$sub = etc\sanitize($sub);
	$expiry = etc\sanitize($expiry);

	$result = mysqli_query($link, "SELECT 1 FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_num_rows($result) < 1) {
		return 'sub_missing';
	} else if ($expiry < time()) {
		return 'date_past';
	}
	if ($username == "all") {
		if(!$activeOnly) {
			$result = mysqli_query($link, "SELECT GROUP_CONCAT(`user`) AS `existingUsers` FROM `subs` WHERE `subscription` = 'default' AND `expiry` > " . time() . " AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
			$row = mysqli_fetch_array($result);
			$existingUsers = $row['existingUsers'];
			
			$result = mysqli_query($link, "SELECT `username` FROM `users` WHERE `username` NOT IN('$existingUsers') AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
			$rows = array();
			while ($r = mysqli_fetch_assoc($result)) {
				$rows[] = $r;
			}
			foreach ($rows as $row) {
				mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('" . $row['username'] . "','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
			}
		}
		$appendExpiry = $expiry - time();
		mysqli_query($link, "UPDATE `subs` SET `expiry` = `expiry`+$appendExpiry WHERE `subscription` = '$sub' AND `expiry` > " . time() . " AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		cache\purgePattern('KeyAuthSubs:' . ($secret ?? $_SESSION['app']));
	} else {
		$result = mysqli_query($link, "SELECT `username` FROM `users` WHERE `username` = '$username' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'missing';
		}
		$result = mysqli_query($link, "SELECT `id` FROM `subs` WHERE `user` = '$username' AND `subscription` = '$sub' AND `expiry` > " . time() . " AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		if (mysqli_num_rows($result) > 0) {
			$appendExpiry = $expiry - time();
			mysqli_query($link, "UPDATE `subs` SET `expiry` = `expiry`+$appendExpiry WHERE `user` = '$username' AND `subscription` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		} else {
			mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
		}
		cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
	}
	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	} else {
		return 'failure';
	}
}
function subtract($username, $sub, $seconds, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$sub = etc\sanitize($sub);
	$seconds = etc\sanitize($seconds);

	if ($seconds <= 0) {
		return 'invalid_seconds';
	}
	
	if($username == "all") {
		mysqli_query($link, "UPDATE `subs` SET `expiry` = `expiry`-$seconds WHERE `subscription` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	}
	else {
		mysqli_query($link, "UPDATE `subs` SET `expiry` = `expiry`-$seconds WHERE `user` = '$username' AND `subscription` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	}
	
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		return 'success';
	} else {
		return 'failure';
	}
}
function add($username, $sub, $expiry, $secret = null, $password = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$username = etc\sanitize($username);
	$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `username` = '$username' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_num_rows($result) > 0) {
		return 'already_exist';
	}
	
	if (!empty($password))
		$password = password_hash(etc\sanitize($password), PASSWORD_BCRYPT);
	$sub = etc\sanitize($sub);
	$expiry = etc\sanitize($expiry);

	$result = mysqli_query($link, "SELECT 1 FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_num_rows($result) < 1) {
		return 'sub_missing';
	} else if ($expiry < time()) {
		return 'date_past';
	}

	mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
	mysqli_query($link, "INSERT INTO `users` (`username`, `password`, `hwid`, `app`,`owner`,`createdate`) VALUES ('$username',NULLIF('$password', ''), NULL, '" . ($secret ?? $_SESSION['app']) . "','" . ($_SESSION['username'] ?? 'SellerAPI') . "','" . time() . "')");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteExpiredUsers($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$result = mysqli_query($link, "SELECT `username` FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_num_rows($result) < 1) {
		return 'missing';
	}
	$rows = array();
	while ($r = mysqli_fetch_assoc($result)) {
		$rows[] = $r;
	}
	$success = 0;
	foreach ($rows as $row) {
		$result = mysqli_query($link, "SELECT 1 FROM `subs` WHERE `user` = '" . $row['username'] . "' AND `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `expiry` > '" . time() . "'");
		if (mysqli_num_rows($result) < 1) {
			$success = 1;
			mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '" . $row['username'] . "'");
			cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $row['username']);
		}
	}
	if ($success) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
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
	mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purgePattern('KeyAuthUser:' . ($secret ?? $_SESSION['app']));
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function resetAll($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "UPDATE `users` SET `hwid` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purgePattern('KeyAuthUser:' . ($secret ?? $_SESSION['app']));
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function changeUsername($oldUsername, $newUsername, $secret = null) {
	$oldUsername = etc\sanitize($oldUsername);
	$newUsername = etc\sanitize($newUsername);
	
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `username` = '$newUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if(mysqli_num_rows($result) > 0) {
		return 'already_used';
	}
	mysqli_query($link, "UPDATE `users` SET `username` = '$newUsername' WHERE `username` = '$oldUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		mysqli_query($link, "UPDATE `subs` SET `user` = '$newUsername' WHERE `user` = '$oldUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		mysqli_query($link, "UPDATE `uservars` SET `user` = '$newUsername' WHERE `user` = '$oldUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		mysqli_query($link, "UPDATE `chatmsgs` SET `user` = '$newUsername' WHERE `author` = '$oldUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		mysqli_query($link, "UPDATE `keys` SET `usedby` = '$newUsername' WHERE `usedby` = '$oldUsername' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $oldUsername);
		cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $oldUsername);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	}
	else {
		return 'failure';
	}
}
function changePassword($username, $password, $secret = null) {
	$username = etc\sanitize($username);
	$password = etc\sanitize($password);
	
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "UPDATE `users` SET `password` = '" . password_hash($password, PASSWORD_BCRYPT) . "' WHERE `username` = '$username' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	}
	else {
		return 'failure';
	}
}