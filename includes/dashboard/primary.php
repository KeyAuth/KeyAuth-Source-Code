<?php

namespace dashboard\primary;

use misc\mysql;

function time2str($date)
{
    $now = time();
    $diff = $now - $date;
    if ($diff < 60) {
        return sprintf($diff > 1 ? '%s seconds' : 'second', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 60) {
        return sprintf($diff > 1 ? '%s minutes' : 'minute', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 24) {
        return sprintf($diff > 1 ? '%s hours' : 'hour', $diff);
    }
    $diff = floor($diff / 24);
    if ($diff < 7) {
        return sprintf($diff > 1 ? '%s days' : 'day', $diff);
    }
    if ($diff < 30) {
        $diff = floor($diff / 7);
        return sprintf($diff > 1 ? '%s weeks' : 'week', $diff);
    }
    $diff = floor($diff / 30);
    if ($diff < 12) {
        return sprintf($diff > 1 ? '%s months' : 'month', $diff);
    }
    $diff = date('Y', $now) - date('Y', $date);
    return sprintf($diff > 1 ? '%s years' : 'year', $diff);
}
function expireCheck($username, $expires)
{
    if ($expires < time()) {
        $_SESSION['role'] = "tester";
        $query = mysql\query("UPDATE `accounts` SET `role` = 'tester' WHERE `username` = ?",[$username]);
    }
    if ($expires - time() < 2629743) // check if account expires in month or less
    {
        return true;
    } else {
        return false;
    }
}
function wh_log($webhook_url, $msg, $un)
{
    $json_data = json_encode([
        // Message
        "content" => $msg,
        // Username
        "username" => "$un",
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
}
function error($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .error({

                                message: \'' . addslashes($msg) . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}
function success($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .success({

                                message: \'' . addslashes($msg) . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}
