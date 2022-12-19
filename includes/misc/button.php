<?php

namespace misc\button;

use misc\etc;
use misc\cache;

function addButton($text, $value, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	$text = etc\sanitize($text);
    $value = etc\sanitize($value);
    mysqli_query($link, "INSERT INTO `buttons` (`text`, `value`, `app`) VALUES ('$text','$value', '" . ($secret ?? $_SESSION['app']) . "')");
    if (mysqli_affected_rows($link) > 0) {
        if ($_SESSION['role'] == "seller" || !is_null($secret)) {
            cache\purge('KeyAuthButtons:' . ($secret ?? $_SESSION['app']));
        }
        return 'success';
    } else {
        return 'failure';
    }
}

function deleteButton($value, $secret = null)
{
	global $link;
	include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
	
	$value = etc\sanitize($value);
    mysqli_query($link, "DELETE FROM `buttons` WHERE `value` = '$value' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0) {
        if ($_SESSION['role'] == "seller" || !is_null($secret)) {
            cache\purge('KeyAuthButtons:' . ($secret ?? $_SESSION['app']));
        }
        return 'success';
    } else {
        return 'failure';
    }
}

?>