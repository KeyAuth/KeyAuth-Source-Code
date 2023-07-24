<?php

namespace misc\button;

use misc\etc;
use misc\cache;
use misc\mysql;

function addButton($text, $value, $secret = null)
{
    
    $text = etc\sanitize($text);
    $value = etc\sanitize($value);
    $query = mysql\query("INSERT INTO `buttons` (`text`, `value`, `app`) VALUES (?, ?, ?)",[$text, $value, $secret ?? $_SESSION['app']]);

    if ($query->affected_rows > 0) {
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
    
    $value = etc\sanitize($value);
    $query = mysql\query("DELETE FROM `buttons` WHERE `value` = ? AND `app` = ?",[$value, $secret ?? $_SESSION['app']]);

    if ($query->affected_rows > 0) {
        if ($_SESSION['role'] == "seller" || !is_null($secret)) {
            cache\purge('KeyAuthButtons:' . ($secret ?? $_SESSION['app']));
        }
        return 'success';
    } else {
        return 'failure';
    }
}

?>
