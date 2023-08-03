<?php

namespace misc\variable;

use misc\etc;
use misc\cache;
use misc\mysql;

function add($name, $data, $authed, $secret = null)
{
        $name = etc\sanitize($name);
        $data = etc\sanitize($data);
        $authed = etc\sanitize($authed);

        if(strlen($data) > 1000) {
                return 'too_long';
        }

        $name_check = mysql\query("SELECT 1 FROM `vars` WHERE `varid` = ? AND `app` = ?",[$name, $secret ?? $_SESSION['app']]);
        if ($name_check->num_rows > 0) {
                return 'exists';
        }
        $query = mysql\query("INSERT INTO `vars`(`varid`, `msg`, `app`, `authed`) VALUES (?, ?, ?, ?)",[$name, $data,$secret ?? $_SESSION['app'], $authed]);
        if ($query->affected_rows > 0) {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteAll($secret = null)
{
        $query = mysql\query("DELETE FROM `vars` WHERE `app` = ?",[$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                cache\purgePattern('KeyAuthVar:' . ($secret ?? $_SESSION['app']));
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function deleteSingular($var, $secret = null)
{
        $var = etc\sanitize($var);

        $query = mysql\query("DELETE FROM `vars` WHERE `app` = ? AND `varid` = ?",[$secret ?? $_SESSION['app'], $var]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthVar:' . ($secret ?? $_SESSION['app']) . ':' . $var);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
function edit($name, $data, $authed, $secret = null)
{
        $name = etc\sanitize($name);
        $data = etc\sanitize($data);
        $authed = etc\sanitize($authed);

        if(strlen($data) > 1000) {
                return 'too_long';
        }

        $query = mysql\query("UPDATE `vars` SET `msg` = ?, `authed` = ? WHERE `varid` = ? AND `app` = ?",[$data, $authed, $name, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) {
                cache\purge('KeyAuthVar:' . ($secret ?? $_SESSION['app']) . ':' . $name);
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthVars:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } else {
                return 'failure';
        }
}
