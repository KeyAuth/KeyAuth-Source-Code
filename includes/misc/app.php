<?php

namespace misc\app;

use misc\etc;
use misc\cache;

function pause($secret = null)
{
	global $link;
	mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = `expiry`-" . time() . " WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `expiry` > '" . time() . "'");
	mysqli_query($link, "UPDATE `apps` SET `paused` = 1 WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	
	$result = mysqli_query($link, "SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
	cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
function unpause($secret = null)
{
	global $link;
	mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = `expiry`+" . time() . " WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `paused` = 1");
	mysqli_query($link, "UPDATE `apps` SET `paused` = 0 WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");

	$result = mysqli_query($link, "SELECT `ownerid`,`name`,`customDomainAPI` FROM `apps` WHERE `secret` = '" . ($secret ?? $_SESSION['app']) . "'");
	$row = mysqli_fetch_array($result);
	cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
	cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
}
