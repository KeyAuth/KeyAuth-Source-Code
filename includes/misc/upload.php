<?php

namespace misc\upload;

use misc\etc;
use misc\cache;
use misc\mysql;

function add($url, $authed, $secret = null)
{
  $url = etc\sanitize($url);
  $authed = etc\sanitize($authed);

  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    return 'invalid';
  }
  $file = file_get_contents($url);
  $filesize = strlen($file);
  if ($filesize > 10000000 && $role == "tester") {
    error("Users with tester plan may only upload files up to 10MB. Paid plans may upload up to 50MB.");
    return;
  } else if ($filesize > 50000000 && ($role == "developer" || $role == "Manager")) {
    error("File size limit is 50 MB.");
    return;
  } else if ($filesize > 75000000) {
    error("File size limit is 75 MB.");
    return;
  }
  $id = etc\generateRandomNum();
  $fn = basename($url);
  $fs = etc\formatBytes($filesize);
  $query = mysql\query("INSERT INTO `files` (name, id, url, size, uploaddate, app, authed) VALUES (?, ?, ?, ?, ?, ?, ?)", [$fn, $id, $url, $fs, time(), $secret ?? $_SESSION['app'], $authed]);
  if ($query->affected_rows > 0) {
    if ($_SESSION['role'] == "seller" || !is_null($secret)) {
      cache\purge('KeyAuthFiles:' . ($secret ?? $_SESSION['app']));
    }
    return 'success';
  } else {
    return 'failure';
  }
}
function deleteAll($secret = null)
{
  $query = mysql\query("DELETE FROM `files` WHERE `app` = ?", [$secret ?? $_SESSION['app']]);

  if ($query->affected_rows > 0) {
    cache\purgePattern('KeyAuthFile:' . ($secret ?? $_SESSION['app']));
    if ($_SESSION['role'] == "seller" || !is_null($secret)) {
      cache\purge('KeyAuthFiles:' . ($secret ?? $_SESSION['app']));
    }
    return 'success';
  } else {
    return 'failure';
  }
}
function deleteSingular($file, $secret = null)
{
  $file = etc\sanitize($file);

  $query = mysql\query("DELETE FROM `files` WHERE `app` = ? AND `id` = ?", [$secret ?? $_SESSION['app'], $file]);

  if ($query->affected_rows > 0) {
    cache\purge('KeyAuthFile:' . ($secret ?? $_SESSION['app']) . ':' . $file);
    if ($_SESSION['role'] == "seller" || !is_null($secret)) {
      cache\purge('KeyAuthFiles:' . ($secret ?? $_SESSION['app']));
    }
    return 'success';
  } else {
    return 'failure';
  }
}
