<?php

namespace misc\license;

use misc\etc;
use misc\cache;
use misc\user;

function license_masking($mask) // subsitute random characters for upper-case and lower-case random character variables, X or x
{
	$mask_arr = str_split($mask);
	$size_of_mask = count($mask_arr);
	for ($i = 0; $i < $size_of_mask; $i++) {
		if ($mask_arr[$i] === 'X') {
			$mask_arr[$i] = etc\random_string_upper(1);
		} else if ($mask_arr[$i] === 'x') {
			$mask_arr[$i] = etc\random_string_lower(1);
		}
	}
	return implode('', $mask_arr);
}
function createLicense($amount, $mask, $duration, $level, $note, $expiry = null, $secret = null, $owner = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$amount = etc\sanitize($amount);
	$mask = etc\sanitize($mask);
	$duration = etc\sanitize($duration);
	$level = etc\sanitize($level);
	$note = etc\sanitize($note);
	$expiry = etc\sanitize($expiry);
	$secret = etc\sanitize($secret);

	if ($amount > 100) {
		return 'max_keys';
	}
	if (!isset($amount)) {
		$amount = 1;
	}
	if (!is_numeric($level)) {
		$level = 1;
	}
	if (is_null($expiry)) {
		$expiry = 86400; // set unit to day(s) if license expiry unit isn't specified (as it isn't with SellerAPI)
	}
	$duration = $duration * $expiry;
	if ($amount > 1 && strpos($mask, 'X') === false && strpos($mask, 'x') === false) {
		return 'dupe_custom_key';
	}

	switch ($_SESSION['role']) {
		case 'tester':
			$result = mysqli_query($link, "SELECT 1 FROM `keys` WHERE `genby` = '" . $_SESSION['username'] . "'");
			$currkeys = mysqli_num_rows($result);
			if ($currkeys + $amount > 50) {
				return 'tester_limit';
			}
			break;
		case 'Reseller':
			if ($amount < 0) {
				return 'no_negative';
			}
			$result = mysqli_query($link, "SELECT `keylevels`, `balance` FROM `accounts` WHERE `username` = 	'" . $_SESSION['username'] . "'");
			$row = mysqli_fetch_array($result);
			$keylevels = explode("|", $row['keylevels']);
			$balance = explode("|", $row['balance']);
			if ($row['keylevels'] != "N/A" && !in_array($level, $keylevels)) {
				return 'unauthed_level';
			}
			$day = $balance[0];
			$week = $balance[1];
			$month = $balance[2];
			$threemonth = $balance[3];
			$sixmonth = $balance[4];
			$lifetime = $balance[5];
			switch ($expiry) {
				case '1 Day':
					$duration = 86400;
					$day = $day - $amount;
					break;
				case '1 Week':
					$duration = 604800;
					$week = $week - $amount;
					break;
				case '1 Month':
					$duration = 2.592e+6;
					$month = $month - $amount;
					break;
				case '3 Month':
					$duration = 7.862e+6;
					$threemonth = $threemonth - $amount;
					break;
				case '6 Month':
					$duration = 1.572e+7;
					$sixmonth = $sixmonth - $amount;
					break;
				case '1 Lifetime':
					$duration = 8.6391e+8;
					$lifetime = $lifetime - $amount;
					break;
				default:
					return 'invalid_exp';
					break;
			}
			if ($day < 0 || $month < 0 || $week < 0 || $threemonth < 0 || $sixmonth < 0 || $lifetime < 0) {
				return 'insufficient_balance';
			}
			$balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
			mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '" . $_SESSION['username'] . "'");
			break;
		case 'seller':
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
			break;
	}
	
	if(!is_null($secret)) {
		cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
	}

	$licenses = array();

	for ($i = 0; $i < $amount; $i++) {

		$license = license_masking($mask);
		mysqli_query($link, "INSERT INTO `keys` (`key`, `note`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$license',NULLIF('$note', ''), '$duration','Not Used','$level','" . ($owner ?? $_SESSION['username']) . "', '" . time() . "', '" . ($secret ?? $_SESSION['app']) . "')");
		$licenses[] = $license;
	}

	return $licenses;
}
function addTime($time, $expiry, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$time = etc\sanitize($time);
	$expiry = etc\sanitize($expiry);

	$time = $time * $expiry;
	mysqli_query($link, "UPDATE `keys` SET `expires` = `expires`+$time WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Not Used'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
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
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteAllUnused($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Not Used'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteAllUsed($secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Used'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteSingular($key, $userToo, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$key = etc\sanitize($key);
	$userToo = etc\sanitize($userToo);

	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key' AND `genby` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) < 1) {
			return 'nope';
		}
	}

	if ($userToo) {
		$result = mysqli_query($link, "SELECT `usedby` FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
		$row = mysqli_fetch_array($result);
		$usedby = $row['usedby'];
		
		user\deleteSingular($usedby, $secret);
	}
	mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'"); // delete any subscriptions created with key
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
			cache\purge('KeyAuthKey:' . ($secret ?? $_SESSION['app']) . ':' . $key);
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function ban($key, $reason, $userToo, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$key = etc\sanitize($key);
	$reason = etc\sanitize($reason);
	$userToo = etc\sanitize($userToo);

	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key' AND `genby` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) === 0) {
			return 'nope';
		}
	}

	if ($userToo) {
		$result = mysqli_query($link, "SELECT `usedby` FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
		$row = mysqli_fetch_array($result);
		$usedby = $row['usedby'];
		
		user\ban($usedby, $reason, $secret);
	}

	mysqli_query($link, "UPDATE `keys` SET `banned` = '$reason', `status` = 'Banned' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function unban($key, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$key = etc\sanitize($key);

	if ($_SESSION['role'] == "Reseller") {
		$result = mysqli_query($link, "SELECT 1 FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key' AND `genby` = '" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) === 0) {
			return 'nope';
		}
	}

	$status = "Not Used";
	$result = mysqli_query($link, "SELECT `usedby` FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);
		$usedby = $row['usedby'];
		$status = "Used";
		
		user\unban($usedby, $secret);
	}


	mysqli_query($link, "UPDATE `keys` SET `banned` = NULL, `status` = '$status' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'"); // update key from banned to its old status
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}