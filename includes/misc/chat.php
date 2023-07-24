<?php

namespace misc\chat;

use misc\etc;
use misc\cache;
use misc\mysql;

function deleteMessage($id, $secret = null)
{
        $id = etc\sanitize($id);

        $query = mysql\query("SELECT `channel` FROM `chatmsgs` WHERE `app` = ? AND `id` = ?",[$secret ?? $_SESSION['app'], $id]);
        $row = mysqli_fetch_array($query->result);
        cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $row['channel']);

        $query = mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ? AND `id` = ?",[$secret ?? $_SESSION['app'], $id]);
        if ($query->affected_rows > 0) {
                return 'success';
        } else {
                return 'failure';
        }
}
function muteUser($user, $time, $secret = null)
{
        $user = etc\sanitize($user);
        $time = etc\sanitize($time);

        $query = mysql\query("SELECT 1 FROM `users` WHERE `app` = ? AND `username` = ?",[$secret ?? $_SESSION['app'], $user]);
        if ($query->num_rows == 0) {
                return 'missing';
        }
        $query = mysql\query("INSERT INTO `chatmutes` (`user`, `time`, `app`) VALUES (?, ?, ?)",[$user, $time, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
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
        $user = etc\sanitize($user);

        $query = mysql\query("DELETE FROM `chatmutes` WHERE `app` = ? AND `user` = ?",[$secret ?? $_SESSION['app'], $user]);

        if ($query->affected_rows > 0) {
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
        $channel = etc\sanitize($channel);

        $query = mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ? AND `channel` = ?",[$secret ?? $_SESSION['app'], $channel]);

        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $channel);
                return 'success';
        } else {
                return 'failure';
        }
}
function createChannel($name, $delay, $secret = null)
{
        $name = etc\sanitize($name);
        $delay = etc\sanitize($delay);

        $query = mysql\query("INSERT INTO `chats` (`name`, `delay`, `app`) VALUES (?, ?, ?)",[$name, $delay, $secret ?? $_SESSION['app']]);

        if ($query->affected_rows > 0) {
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
        $name = etc\sanitize($name);

        $query = mysql\query("DELETE FROM `chats` WHERE `app` = ? AND `name` = ?",[$secret ?? $_SESSION['app'], $name]);

        if ($query->affected_rows > 0) {
                $query = mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ? AND `channel` = ?",[$secret ?? $_SESSION['app'], $name]);
                cache\purge('KeyAuthChatMsgs:' . ($secret ?? $_SESSION['app']) . ':' . $name);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthChats:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
