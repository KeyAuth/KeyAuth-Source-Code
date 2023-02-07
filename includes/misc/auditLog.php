<?php

namespace misc\auditLog;

function send($event)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	$result = mysqli_query($link, "SELECT `auditLogWebhook` FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'");
	$row = mysqli_fetch_array($result);
	if(!is_null($row['auditLogWebhook'])) {
		dashboard\primary\wh_log($row['auditLogWebhook'], "**User:** " . $_SESSION['username'] . "**Event:** $event");
	}
	else {
		mysqli_query($link, "INSERT INTO `auditLog` (`user`, `event`, `time`, `app`) VALUES ('" . $_SESSION['username'] . "','$event','".time()."', '" . $_SESSION['app'] . "')");
	}
}