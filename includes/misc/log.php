<?php
namespace misc\logging;

use misc\etc;

function deleteAll($secret = null)
{
	global $link;
	
	mysqli_query($link, "DELETE FROM `logs` WHERE `logapp` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
?>