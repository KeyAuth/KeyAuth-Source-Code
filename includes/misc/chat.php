<?php

namespace misc\chat;

use misc\etc;
use misc\cache;

function deleteMessage($id, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$id = etc\sanitize($id);

	$result = mysqli_query($link, "SELECT `channel` FROM `chatmsgs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `id` = '$id'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $row['channel']);

	mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `id` = '$id'");
	if (mysqli_affected_rows($link) > 0) {
		return 'success';
	} else {
		return 'failure';
	}
}
function muteUser($user, $time, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$user = etc\sanitize($user);
	$time = etc\sanitize($time);

	$result = mysqli_query($link, "SELECT 1 FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$user'");
	if (mysqli_num_rows($result) == 0) {
		return 'missing';
	}
	mysqli_query($link, "INSERT INTO `chatmutes` (`user`, `time`, `app`) VALUES ('$user','$time','" . ($secret ?? $_SESSION['app']) . "')");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthMutes:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function unMuteUser($user, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$user = etc\sanitize($user);

	mysqli_query($link, "DELETE FROM `chatmutes` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$user'");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthMutes:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function clearChannel($channel, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$channel = etc\sanitize($channel);

	mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `channel` = '$channel'");
	if (mysqli_affected_rows($link) > 0) {
		cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $channel);
		return 'success';
	} else {
		return 'failure';
	}
}
function createChannel($name, $delay, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$name = etc\sanitize($name);
	$delay = etc\sanitize($delay);

	mysqli_query($link, "INSERT INTO `chats` (`name`, `delay`, `app`) VALUES ('$name','$delay','" . ($secret ?? $_SESSION['app']) . "')");
	if (mysqli_affected_rows($link) > 0) {
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthChats:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}
function deleteChannel($name, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	$name = etc\sanitize($name);

	mysqli_query($link, "DELETE FROM `chats` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `name` = '$name'");
	if (mysqli_affected_rows($link) > 0) {
		mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `channel` = '$name'");
		cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $name);
		if ($_SESSION['role'] == "seller" || !is_null($secret)) {
			cache\purge('KeyAuthChats:' . ($secret ?? $_SESSION['app']));
		}
		return 'success';
	} else {
		return 'failure';
	}
}