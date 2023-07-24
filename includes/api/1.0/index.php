<?php
namespace api\v1_0;

use api\shared\primary;
use misc\etc;
use misc\cache;
use misc\mysql;

#region enc region
function Encrypt($string, $enckey)
{
    return bin2hex(openssl_encrypt($string, 'aes-256-cbc', substr(hash('sha256', $enckey), 0, 32), OPENSSL_RAW_DATA, substr(hash('sha256', $_POST['init_iv']), 0, 16)));
}
function Decrypt($string, $enckey)
{
    return openssl_decrypt(hex2bin($string), 'aes-256-cbc', substr(hash('sha256', $enckey), 0, 32), OPENSSL_RAW_DATA, substr(hash('sha256', $_POST['init_iv']), 0, 16));
}
#endregion
#region rgstr region
function register($un, $key, $pw, $email, $hwid, $secret)
{
    $query = mysql\query("SELECT `minUsernameLength`, `blockLeakedPasswords` FROM `apps` WHERE `secret` = ?",[$secret]);
    $row = mysqli_fetch_array($query->result);
    $minUsernameLength = $row['minUsernameLength'];
    $blockLeakedPasswords = $row['blockLeakedPasswords'];

    if (strlen($un) < $minUsernameLength) {
        return 'un_too_short';
    }

    if ($blockLeakedPasswords && etc\isBreached($pw)) {
        return 'pw_leaked';
    }

    // search username
    $query = mysql\query("SELECT 1 FROM `users` WHERE `username` = ? AND `app` = ?",[$un, $secret]);
    // if username already in existence
    if ($query->num_rows >= 1) {
        return 'username_taken';
    }
    // search for key
    $query = mysql\query("SELECT `expires`, `status`, `level`, `genby` FROM `keys` WHERE `key` = ? AND `app` = ?",[$key, $secret]);
    // check if key exists
    if ($query->num_rows < 1) {
        return 'key_not_found';
    }
    // if key does exist
    elseif ($query->num_rows > 0) {
        // gather key info
        while ($row = mysqli_fetch_array($query->result)) {
            $expires = $row['expires'];
            $status = $row['status'];
            $level = $row['level'];
            $genby = $row['genby'];
        }
        // check license status
        switch ($status) {
            case 'Used':
                return 'key_already_used';
            case 'Banned':
                return 'key_banned';
        }
        $ip = primary\getIp();
        $hwidBlackCheck = mysql\query("SELECT 1 FROM `bans` WHERE (`hwid` = ? OR `ip` = ?) AND `app` = ?",[$hwid, $ip, $secret]);
        if ($hwidBlackCheck->num_rows > 0) {
            $query = mysql\query("UPDATE `keys` SET `status` = 'Banned',`banned` = 'This key has been banned as the client was blacklisted.' WHERE `key` = ? AND `app` = ?",[$un, $secret]);
            cache\purge('KeyAuthKeys:' . $secret);
            return 'hwid_blacked';
        }
        // add current time to key time
        $expiry = $expires + time();
        $query = mysql\query("SELECT `name` FROM `subscriptions` WHERE `app` = ? AND `level` = ?", [$secret, $level]);
        if ($query->num_rows == 0) {
            return 'no_subs_for_level';
        }
        // update key to used
        mysql\query("UPDATE `keys` SET `status` = 'Used',`usedon` = ?,`usedby` = ? WHERE `key` = ? AND `app` = ?",[time(), $un, $key, $secret]);

        cache\purge('KeyAuthKeys:' . $secret);
        while ($row = mysqli_fetch_array($query->result)) {
            // add each subscription that user's key applies to
            $subname = $row['name'];
            mysql\query("INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`, `key`) VALUES (?, ?, ?, ?, ?)",[$un, $subname, $expiry, $secret, $key]);

        }
        $password = password_hash($pw, PASSWORD_BCRYPT);
        // create user
        mysql\query("INSERT INTO `users` (`username`, `email`, `password`, `hwid`, `app`,`owner`,`createdate`, `lastlogin`, `ip`) VALUES (?, SHA(LOWER(NULLIF(?, ''))), ?, NULLIF(?, ''), ?, ?, ?, ?, ?)",[$un, $email, $password, $hwid, $secret, $genby, time(), time(), $ip]);
        $query = mysql\query("SELECT `subscription`, `key`, `expiry` FROM `subs` WHERE `user` = ? AND `app` = ? AND `expiry` > " . time() . "",[$un, $secret]);
        $rows = array();
        if($query->num_rows > 0) {
            while ($r = mysqli_fetch_assoc($query->result)) {
                $timeleft = $expiry - time();
                $r += ["timeleft" => $timeleft];
                $rows[] = $r;
            }
        }
        else {
            $timeleft = $expiry - time();
            $rows = array("subscription" => "$subname", "key" => "$key", "expiry" => "$expiry", "timeleft" => "$timeleft");
        }
        
        cache\purge('KeyAuthUser:' . $secret . ':' . $un);
        cache\purge('KeyAuthSubs:' . $secret . ':' . $un);
        // success
        return array(
            "username" => "$un",
            "subscriptions" => $rows,
            "ip" => $ip,
            "hwid" => $hwid,
            "createdate" => "". time() ."",
            "lastlogin" => "" . time() . ""
        );
    }
}
#endregion
#region login region
function login($un, $pw, $hwid, $secret, $hwidenabled, $token = null)
{
    $row = cache\fetch('KeyAuthUser:' . $secret . ':' . $un, "SELECT * FROM `users` WHERE `username` = ? AND `app` = ?", [$un, $secret], 0);

    if ($row == "not_found") {
        return 'un_not_found';
    }

    $pass = $row['password'];
    $serverHwid = $row['hwid'];
    $banned = $row['banned'];
    $createdate = $row['createdate'];
    if ($banned != NULL) {
        return 'user_banned';
    }
    $ip = primary\getIp();

    if (!is_null($token)) {
        $validToken = md5(substr($pass, -5));
        if ($validToken != $token) {
            return 'pw_mismatch';
        }
    } else if (!is_null($pass)) {
        // check if pass matches
        if (!password_verify($pw, $pass)) {
            return 'pw_mismatch';
        }
    } else {
        $pass_encrypted = password_hash($pw, PASSWORD_BCRYPT);
        $query = mysql\query("UPDATE `users` SET `password` = ? WHERE `username` = ? AND `app` = ?",[$pass_encrypted, $un, $secret]);

        cache\purge('KeyAuthUser:' . $secret . ':' . $un);
    }
    // check if hwid enabled for application
    if ($hwidenabled == "1") {
        // check if hwid in db contains hwid recieved
        if (!is_null($hwid) && !str_contains($serverHwid, $hwid) && !is_null($serverHwid)) {
            return 'hwid_mismatch';
        } else if (is_null($serverHwid) && !is_null($hwid)) {
            $query = mysql\query("UPDATE `users` SET `hwid` = NULLIF(?, '') WHERE `username` = ? AND `app` = ?",[$hwid, $un, $secret]);

            cache\purge('KeyAuthUser:' . $secret . ':' . $un);
        }
    }
    $rows = cache\fetch('KeyAuthSubs:' . $secret . ':' . $un, "SELECT `subscription`, `key`, `expiry` FROM `subs` WHERE `user` = ? AND `app` = ? AND `expiry` > ?", [$un, $secret, time()], 1, null, "ssi");
    if($_SERVER['HTTP_USER_AGENT'] == "PostmanRuntime/7.31.3") {
        var_dump($rows);
    }
    
    if ($rows == "not_found") {
        $query = mysql\query("SELECT `paused` FROM `subs` WHERE `user` = ? AND `app` = ? AND `paused` = 1",[$un, $secret]);
        if ($query->num_rows >= 1) {
            return 'sub_paused';
        }
        return 'no_active_subs';
    }

    $rowsFinal = array(); 
    foreach ($rows as $row) {
        $timeleft = $row["expiry"] - time();

        $levelquery = mysql\query("SELECT `level` FROM `subscriptions` WHERE `name` = ? AND `app` = ?", [$row["subscription"], $secret]);
        $level = mysqli_fetch_array($levelquery->result);


        $row += ["timeleft" => $timeleft, "level" => $level["level"]];
        $rowsFinal[] = $row;
    }

    $ip = primary\getIp();

    $query = mysql\query("UPDATE `users` SET `ip` = NULLIF(?, ''),`lastlogin` = " . time() . " WHERE `username` = ? AND `app` = ?",[$ip, $un, $secret]);

    return array(
        "username" => "$un",
        "subscriptions" => $rowsFinal,
        "ip" => $ip,
        "hwid" => $serverHwid,
        "createdate" => "$createdate",
        "lastlogin" => "" . time() . ""
    );
}
#endregion
