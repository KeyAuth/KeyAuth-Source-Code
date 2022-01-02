<?php
namespace misc\sub;

use misc\etc;

function deleteSingular($subscription, $secret = null)
{
	global $link;
	$subscription = etc\sanitize($subscription);
	
    mysqli_query($link, "DELETE FROM `subscriptions` WHERE `app` = '".($secret ?? $_SESSION['app'])."' AND `name` = '$subscription'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function add($name, $level, $secret = null)
{
	global $link;
	$name = etc\sanitize($name);
	$level = etc\sanitize($level);
	
    mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('$name','$level', '".($secret ?? $_SESSION['app'])."')");
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