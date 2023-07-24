<?php

namespace misc\app;

use misc\etc;
use misc\cache;
use misc\mysql;

function pause($secret = null)
{
        
        mysql\query("UPDATE `subs` SET `paused` = 1, `expiry` = `expiry`-? WHERE `app` = ? AND `expiry` > ?",[time(), $secret ?? $_SESSION['app'], time()], "isi");
        mysql\query("UPDATE `apps` SET `paused` = 1 WHERE `secret` = ?",[$secret ?? $_SESSION['app']]);
        $query = mysql\query("SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = ?", [$secret ?? $_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
        cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
function unpause($secret = null)
{
        mysql\query("UPDATE `subs` SET `paused` = 0, `expiry` = `expiry`+? WHERE `app` = ? AND `paused` = 1", [time(), $secret ?? $_SESSION['app']], "is");
        mysql\query("UPDATE `apps` SET `paused` = 0 WHERE `secret` = ?",[$secret ?? $_SESSION['app']]);
        $query = mysql\query("SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = ?",[$secret ?? $_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
        cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
function addHash($hash, $secret = null)
{
        $hash = etc\sanitize($hash);
        $query = mysql\query("SELECT `hash`, `name`, `ownerid` FROM `apps` WHERE `secret` = ?",[$secret ?? $_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        $oldHash = $row["hash"];
        $name = $row["hash"];
        $ownerid = $row["ownerid"];

        $newHash = $oldHash .= $hash;

        $query = mysql\query("UPDATE `apps` SET `hash` = ? WHERE `secret` = ?",[$newHash, $secret ?? $_SESSION['app']]);

        if ($query->affected_rows > 0) {
                cache\purge("KeyAuthApp:{$name}:{$ownerid}");
                return 'success';
        }
        return 'failure';
}
function resetHash($secret = null, $name = null, $ownerid = null)
{
        
        $name = $name ?? $_SESSION['name'];
        $ownerid = $ownerid ?? $_SESSION['ownerid'];
        
        $query = mysql\query("UPDATE `apps` SET `hash` = NULL WHERE `secret` = ?",[$secret ?? $_SESSION['app']]);

        if ($query->affected_rows > 0) {
                cache\purge("KeyAuthApp:{$name}:{$ownerid}");
                return 'success';
        }
        return 'failure';
}
