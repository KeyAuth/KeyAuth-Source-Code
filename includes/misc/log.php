<?php

namespace misc\logging;

use misc\etc;
use misc\mysql;

function deleteAll($secret = null)
{
  $query = mysql\query("DELETE FROM `logs` WHERE `logapp` = ?",[$secret ?? $_SESSION['app']]);
  if ($query->affected_rows > 0) {
    return 'success';
  } else {
    return 'failure';
  }
}
