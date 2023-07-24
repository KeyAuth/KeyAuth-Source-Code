<?php

namespace misc\auditLog;
use misc\mysql;
use dashboard\primary;

function send($event)
{

        $query = mysql\query("SELECT `auditLogWebhook` FROM `apps` WHERE `secret` = ?",[$_SESSION['app']]);

        $row = mysqli_fetch_array($query->result);
        if(!is_null($row['auditLogWebhook'])) {
                primary\wh_log($row['auditLogWebhook'], "**User:** " . $_SESSION['username'] . "**Event:** $event", "");
        }
        else {
                $query = mysql\query("INSERT INTO `auditLog` (`user`, `event`, `time`, `app`) VALUES (?, ?, ?, ?)",[$_SESSION['username'], $event, time(), $_SESSION['app']]);
        }
}
