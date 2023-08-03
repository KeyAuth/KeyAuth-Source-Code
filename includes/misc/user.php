<?php

namespace misc\user;

use misc\etc;
use misc\cache;
use misc\blacklist;
use misc\mysql;

function deleteSingular($username, $secret = null)
{
        $username = etc\sanitize($username);

        if ($_SESSION['role'] == "Reseller") {
                $query = mysql\query("SELECT 1 FROM `users` WHERE `app` = ? AND `username` = ? AND `owner` = ?", [$secret ?? $_SESSION['app'], $username, $_SESSION['username']]);
                if ($query->num_rows < 1) {
                        return 'nope';
                }
        }

        $query = mysql\query("DELETE FROM `subs` WHERE `app` = ? AND `user` = ?", [$secret ?? $_SESSION['app'], $username]);
        $query = mysql\query("DELETE FROM `uservars` WHERE `app` = ? AND `user` = ?", [$secret ?? $_SESSION['app'], $username]);
        $query = mysql\query("DELETE FROM `users` WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $username]);

        if ($query->affected_rows > 0) {
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
        $username = etc\sanitize($username);

        if ($_SESSION['role'] == "Reseller") {
                $query = mysql\query("SELECT 1 FROM `users` WHERE `app` = ? AND `username` = ? AND `owner` = ?", [$secret ?? $_SESSION['app'], $username, $_SESSION['username']]);
                if ($query->num_rows < 1) {
                        return 'nope';
                }
        }

        $query = mysql\query("UPDATE `users` SET `hwid` = NULL WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $username]);
        if ($query->affected_rows > 0) {
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
function setVariable($user, $var, $data, $secret = null, $readOnly = 0)
{
        $user = etc\sanitize($user);
        $var = etc\sanitize($var);
        $data = etc\sanitize($data);
        $readOnly = intval($readOnly);

        if ($user == "all") {
                $query = mysql\query("SELECT `username` FROM `users` WHERE `app` = ?", [$secret ?? $_SESSION['app']]);
                if ($query->num_rows < 1) {
                        return 'missing';
                }
                $rows = array();
                while ($r = mysqli_fetch_assoc($query->result)) {
                        $rows[] = $r;
                }
                foreach ($rows as $row) {
                        $query = mysql\query("REPLACE INTO `uservars` (`name`, `data`, `user`, `app`, `readOnly`) VALUES (?, ?, ?, ?, ?)", [$var, $data, $row['username'], $secret ?? $_SESSION['app'], $readOnly]);
                }
                cache\purgePattern('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']));
        } else {
                $query = mysql\query("REPLACE INTO `uservars` (`name`, `data`, `user`, `app`, `readOnly`) VALUES (?, ?, ?, ?, ?)", [$var, $data, $user, $secret ?? $_SESSION['app'], $readOnly]);
                cache\purge('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']) . ':' . $var . ':' . $user);
        }
        if ($query->affected_rows > 0) {
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
        $username = etc\sanitize($username);
        $reason = etc\sanitize($reason);

        if ($_SESSION['role'] == "Reseller") {
                $query = mysql\query("SELECT 1 FROM `users` WHERE `app` = ? AND `username` = ? AND `owner` = ?", [$secret ?? $_SESSION['app'], $username, $_SESSION['username']]);
                if ($query->num_rows < 1) {
                        return 'nope';
                }
        }

        $query = mysql\query("SELECT * FROM `users` WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $username]);
        if ($query->num_rows < 1) {
                return 'missing';
        }
        $row = mysqli_fetch_array($query->result);
        $hwid = $row["hwid"];
        $ip = $row["ip"];
        $query = mysql\query("UPDATE `users` SET `banned` = ? WHERE `app` = ? AND `username` = ?", [$reason, $secret ?? $_SESSION['app'], $username]);
        if (!is_null($hwid)) {
                blacklist\add($hwid, "Hardware ID", ($secret ?? $_SESSION['app']));
        }
        if (!is_null($ip)) {
                blacklist\add($ip, "IP Address", ($secret ?? $_SESSION['app']));
        }
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUserData:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function unban($username, $secret = null)
{
        $username = etc\sanitize($username);

        if ($_SESSION['role'] == "Reseller") {
                $query = mysql\query("SELECT 1 FROM `users` WHERE `app` = ? AND `username` = ? AND `owner` = ?", [$secret ?? $_SESSION['app'], $username, $_SESSION['username']]);
                if ($query->num_rows < 1) {
                        return 'nope';
                }
        }

        $query = mysql\query("SELECT `hwid`, `ip` FROM `users` WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $username]);
        if ($query->num_rows < 1) {
                return 'missing';
        }
        $row = mysqli_fetch_array($query->result);
        $hwid = $row["hwid"];
        $ip = $row["ip"];
        cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':' . $ip);
        if (!is_null($hwid)) {
                cache\purgePattern('KeyAuthBlacklist:' . ($secret ?? $_SESSION['app']) . ':*:' . $hwid);
        }

        $query = mysql\query("DELETE FROM `bans` WHERE `hwid` = ? OR `ip` = ? AND `app` = ?", [$hwid, $ip, $secret ?? $_SESSION['app']]);
        $query = mysql\query("UPDATE `users` SET `banned` = NULL WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $username]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUserData:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteVar($username, $var, $secret = null)
{
        $username = etc\sanitize($username);
        $var = etc\sanitize($var);

        $query = mysql\query("DELETE FROM `uservars` WHERE `app` = ? AND `user` = ? AND `name` = ?", [$secret ?? $_SESSION['app'], $username, $var]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthUserVar:' . ($secret ?? $_SESSION['app']) . ':' . $var . ':' . $username);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUserVars:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUserStoredVars:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteSub($username, $sub, $secret = null)
{
        $username = etc\sanitize($username);
        $sub = etc\sanitize($sub);

        $query = mysql\query("DELETE FROM `subs` WHERE `app` = ? AND `user` = ? AND `subscription` = ?", [$secret ?? $_SESSION['app'], $username, $sub]);

        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                return 'success';
        } else {
                return 'failure';
        }
}
function extend($username, $sub, $expiry, $activeOnly = 0, $secret = null)
{
        $username = etc\sanitize($username);
        $sub = etc\sanitize($sub);
        $expiry = etc\sanitize($expiry);

        $query = mysql\query("SELECT 1 FROM `subscriptions` WHERE `name` = ? AND `app` = ?", [$sub, $secret ?? $_SESSION['app']]);
        if ($query->num_rows < 1) {
                return 'sub_missing';
        } else if ($expiry < time()) {
                return 'date_past';
        }
        if ($username == "all") {
                if (!$activeOnly) {
                        $query = mysql\query("SELECT `username` FROM `users` WHERE `username` NOT IN (SELECT `user` FROM `subs` WHERE `subscription` = ? AND `expiry` > ? AND `app` = ?) AND `app` = ?", [$sub, time(), $secret ?? $_SESSION['app'], $secret ?? $_SESSION['app']]);

                        $rows = array();
                        while ($r = mysqli_fetch_assoc($query->result)) {
                                $rows[] = $r;
                        }
                        foreach ($rows as $row) {
                                $query = mysql\query("INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES (?, ?, ?, ?)", [$row['username'], $sub, $expiry, $secret ?? $_SESSION['app']]);
                        }
                }
                $appendExpiry = $expiry - time();
                $query = mysql\query("UPDATE `subs` SET `expiry` = `expiry`+? WHERE `subscription` = ? AND `expiry` > " . time() . " AND `app` = ?", [$appendExpiry, $sub, $secret ?? $_SESSION['app']]);

                cache\purgePattern('KeyAuthSubs:' . ($secret ?? $_SESSION['app']));
        } else {
                $query = mysql\query("SELECT `username` FROM `users` WHERE `username` = ? AND `app` = ?", [$username, $secret ?? $_SESSION['app']]);

                if ($query->num_rows < 1) {
                        return 'missing';
                }
                $query = mysql\query("SELECT `id` FROM `subs` WHERE `user` = ? AND `subscription` = ? AND `expiry` > " . time() . " AND `app` = ?", [$username, $sub, $secret ?? $_SESSION['app']]);

                if ($query->num_rows > 0) {
                        $appendExpiry = $expiry - time();
                        $query = mysql\query("UPDATE `subs` SET `expiry` = `expiry`+? WHERE `user` = ? AND `subscription` = ? AND `app` = ?", [$appendExpiry, $username, $sub, $secret ?? $_SESSION['app']]);
                } else {
                        $query = mysql\query("INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES (?, ?, ?, ?)", [$username, $sub, $expiry, $secret ?? $_SESSION['app']]);
                }
                cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
        }
        if ($query->affected_rows > 0) {
                return 'success';
        } else {
                return 'failure';
        }
}
function subtract($username, $sub, $seconds, $secret = null)
{
        $username = etc\sanitize($username);
        $sub = etc\sanitize($sub);
        $seconds = etc\sanitize($seconds);

        if ($seconds <= 0) {
                return 'invalid_seconds';
        }

        if ($username == "all") {
                $query = mysql\query("UPDATE `subs` SET `expiry` = `expiry`-? WHERE `subscription` = ? AND `app` = ?", [$seconds, $sub, $secret ?? $_SESSION['app']]);
        } else {
                $query = mysql\query("UPDATE `subs` SET `expiry` = `expiry`-? WHERE `user` = ? AND `subscription` = ? AND `app` = ?", [$seconds, $username, $sub, $secret ?? $_SESSION['app']]);
        }

        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                return 'success';
        } else {
                return 'failure';
        }
}
function add($username, $sub, $expiry, $secret = null, $password = null)
{
        $username = etc\sanitize($username);
        $query = mysql\query("SELECT 1 FROM `users` WHERE `username` = ? AND `app` = ?", [$username, $secret ?? $_SESSION['app']]);

        if ($query->num_rows > 0) {
                return 'already_exist';
        }

        if (strtolower($username) === "all" || strtoupper($username) === "ALL"){
                return 'username_not_allowed';
       }

        if (!empty($password))
                $password = password_hash(etc\sanitize($password), PASSWORD_BCRYPT);

        $sub = etc\sanitize($sub);
        $expiry = etc\sanitize($expiry);

        $query = mysql\query("SELECT 1 FROM `subscriptions` WHERE `name` = ? AND `app` = ?", [$sub, $secret ?? $_SESSION['app']]);

        if ($query->num_rows < 1) {
                return 'sub_missing';
        } else if ($expiry < time()) {
                return 'date_past';
        }

        $query = mysql\query("INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES (?, ?, ?, ?)", [$username, $sub, $expiry, $secret ?? $_SESSION['app']]);
        $query = mysql\query("INSERT INTO `users` (`username`, `password`, `hwid`, `app`,`owner`,`createdate`) VALUES (?, NULLIF(?, ''), NULL, ?, ?, ?);", [$username, $password, $secret ?? $_SESSION['app'], $_SESSION['username'] ?? 'SellerAPI', time()]);
        if ($query->affected_rows > 0) {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteExpiredUsers($secret = null)
{
        $query = mysql\query("SELECT `username` FROM `users` WHERE `app` = ?", [$secret ?? $_SESSION['app']]);

        if ($query->num_rows < 1) {
                return 'missing';
        }
        $rows = array();
        while ($r = mysqli_fetch_assoc($query->result)) {
                $rows[] = $r;
        }
        $success = 0;
        foreach ($rows as $row) {
                $query = mysql\query("SELECT 1 FROM `subs` WHERE `user` = ? AND `app` = ? AND `expiry` > ?", [$row['username'], $secret ?? $_SESSION['app'], time()]);

                if ($query->num_rows < 1) {
                        $success = 1;
                        $query = mysql\query("DELETE FROM `users` WHERE `app` = ? AND `username` = ?", [$secret ?? $_SESSION['app'], $row['username']]);
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
        $query = mysql\query("DELETE FROM `users` WHERE `app` = ?", [$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
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
        $query = mysql\query("UPDATE `users` SET `hwid` = NULL WHERE `app` = ?", [$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
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
function unbanAll($secret = null)
{
        $query = mysql\query("UPDATE `users` SET `banned` = NULL WHERE `app` = ?", [$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
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
function changeUsername($oldUsername, $newUsername, $secret = null)
{
        $oldUsername = etc\sanitize($oldUsername);
        $newUsername = etc\sanitize($newUsername);

        $query = mysql\query("SELECT 1 FROM `users` WHERE `username` = ? AND `app` = ?", [$newUsername, $secret ?? $_SESSION['app']]);
        if ($query->num_rows > 0) {
                return 'already_used';
        }
        $query = mysql\query("UPDATE `users` SET `username` = ? WHERE `username` = ? AND `app` = ?", [$newUsername, $oldUsername, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                mysql\query("UPDATE `subs` SET `user` = ? WHERE `user` = ? AND `app` = ?", [$newUsername, $oldUsername, $secret ?? $_SESSION['app']]);
                mysql\query("UPDATE `uservars` SET `user` = ? WHERE `user` = ? AND `app` = ?", [$newUsername, $oldUsername, $secret ?? $_SESSION['app']]);
                mysql\query("UPDATE `chatmsgs` SET `author` = ? WHERE `author` = ? AND `app` = ?", [$newUsername, $oldUsername, $secret ?? $_SESSION['app']]);
                mysql\query("UPDATE `keys` SET `usedby` = ? WHERE `usedby` = ? AND `app` = ?", [$newUsername, $oldUsername, $secret ?? $_SESSION['app']]);
                cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $oldUsername);
                cache\purge('KeyAuthSubs:' . ($secret ?? $_SESSION['app']) . ':' . $oldUsername);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsernames:' . ($secret ?? $_SESSION['app']));
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function changePassword($username, $password, $secret = null)
{
        $username = etc\sanitize($username);
        $password = etc\sanitize($password);

        $query = mysql\query("UPDATE `users` SET `password` = ? WHERE `username` = ? AND `app` = ?", [password_hash($password, PASSWORD_BCRYPT), $username, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function changeEmail($username, $email, $secret = null)
{
        $username = etc\sanitize($username);
        $email = etc\sanitize($email);

        $query = mysql\query("UPDATE `users` SET `email` = SHA(?) WHERE `username` = ? AND `app` = ?", [$email, $username, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthUser:' . ($secret ?? $_SESSION['app']) . ':' . $username);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthUsers:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
