<?php
namespace misc\blacklist;

use misc\etc;

function add($data, $type, $secret = null)
{
	global $link;
	$data = etc\sanitize($data);
	$type = etc\sanitize($type);
	
	switch($type)
	{
		case 'IP Address':
			mysqli_query($link, "INSERT INTO `bans`(`ip`, `type`, `app`) VALUES ('$data','ip','" . ($secret ?? $_SESSION['app']) . "')");
			break;
		case 'Hardware ID':
			mysqli_query($link, "INSERT INTO `bans`(`hwid`, `type`, `app`) VALUES ('$data','hwid','" . ($secret ?? $_SESSION['app']) . "')");
			break;
		default:
			return 'invalid';
	}
	if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteAll($secret = null)
{
	global $link;
	
	mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteSingular($blacklist, $type, $secret = null)
{
	global $link;
	$blacklist = etc\sanitize($blacklist);
	$type = etc\sanitize($type);

	switch($type)
	{
		case 'ip':
			mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `ip` = '$blacklist'");
			break;
		case 'hwid':
			mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `hwid` = '$blacklist'");
			break;
		default:
			return 'invalid';
	}
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