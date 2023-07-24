<?php

namespace misc\webhook;

use misc\etc;
use misc\cache;
use misc\mysql;

function add($webhookName, $baseLink, $userAgent, $authed, $secret = null)
{
        $baseLink = etc\sanitize($baseLink);
        $userAgent = etc\sanitize($userAgent);
        $authed = intval($authed);

        if (!filter_var($baseLink, FILTER_VALIDATE_URL))
                return 'invalid_url';

        if(str_contains($baseLink, "localhost") || str_contains($baseLink, "127.0.0.1"))
                return 'no_local';

            $webid = etc\sanitize($webhookName) ?? etc\generateRandomString();
        if (is_null($userAgent))
                $userAgent = "KeyAuth";
        $query = mysql\query("INSERT INTO `webhooks` (`webid`, `baselink`, `useragent`, `app`, `authed`) VALUES (?, ?, ?, ?, ?)",[$webid, $baseLink, $userAgent, $secret ?? $_SESSION['app'], $authed]);
        if ($query->affected_rows > 0) {
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteSingular($webhook, $secret = null){
        $webhook = etc\sanitize($webhook);

        $query = mysql\query("DELETE FROM `webhooks` WHERE `app` = ? AND `webid` = ?",[$secret ?? $_SESSION['app'], $webhook]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthWebhook:' . ($secret ?? $_SESSION['app']) . ':' . $webhook);
                return 'success';
        } else {
                return 'failure';
        }
}

function deleteAll($secret = null){
    $query = mysql\query("DELETE FROM `webhooks` WHERE `app` = ?", [$secret ?? $_SESSION['app']]);
    if ($query->affected_rows > 0){
        cache\purge('KeyAuthWebhook:' . ($secret ?? $_SESSION['app']));
        return 'success';
    } else {
        return 'failure';
    }
}
