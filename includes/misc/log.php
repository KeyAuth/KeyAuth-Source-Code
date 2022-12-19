<?php

namespace misc\logging;

use misc\etc;

function deleteAll($secret = null)
{
  global $link;
  include_once (($_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/panel" || $_SERVER['DOCUMENT_ROOT'] == "/usr/share/nginx/html/api") ? "/usr/share/nginx/html" : $_SERVER['DOCUMENT_ROOT']) . '/includes/connection.php'; // create connection with MySQL
  mysqli_query($link, "DELETE FROM `logs` WHERE `logapp` = '" . ($secret ?? $_SESSION['app']) . "'");
  if (mysqli_affected_rows($link) > 0) {
    return 'success';
  } else {
    return 'failure';
  }
}