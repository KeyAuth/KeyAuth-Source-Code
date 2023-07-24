<?php

namespace misc\sub;

use misc\etc;
use misc\cache;
use misc\mysql;

function deleteSingular($subscription, $secret = null)
{
        $subscription = etc\sanitize($subscription);

        $query = mysql\query("DELETE FROM `subscriptions` WHERE `app` = ? AND `name` = ?",[$secret ?? $_SESSION['app'], $subscription]);
        if ($query->affected_rows > 0) {
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
        $name = etc\sanitize($name);
        $level = etc\sanitize($level);

        $query = mysql\query("INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES (?, ?, ?)",[$name , $level,$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthSubscriptions:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
