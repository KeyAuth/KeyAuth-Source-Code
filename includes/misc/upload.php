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

  if(str_contains($url, "localhost") || str_contains($url, "127.0.0.1") || str_contains($url, "file:/"))
    return 'no_local';

  $file = file_get_contents($url);
  $filesize = strlen($file);
  if ($filesize > 10000000 && $_SESSION['role'] == "tester") {
    return 'tester_file_exceed';
  } else if ($filesize > 50000000 && ($_SESSION['role'] == "developer" || $_SESSION['role'] == "Manager")) {
    return 'dev_file_exceed';
  } else if ($filesize > 75000000) {
    return 'seller_file_exceed';
  }
  $id = etc\generateRandomNum();
  $fn = basename($url);
  $fs = etc\formatBytes($filesize);

  if (strlen($fn) > 49) {
    return 'name_too_large';
  }

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
