<?php
namespace misc\variable;

use misc\etc;

function add($name, $data, $authed, $secret = null)
{
	global $link;
	$name = etc\sanitize($name);
	$data = etc\sanitize($data);
	$authed = etc\sanitize($authed);
	
	$var_check = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$name' AND `app` = '".($secret ?? $_SESSION['app'])."'");
	if (mysqli_num_rows($var_check) > 0)
	{
		return 'exists';
	}
	mysqli_query($link, "INSERT INTO `vars`(`varid`, `msg`, `app`, `authed`) VALUES ('$name','$data','".($secret ?? $_SESSION['app'])."', '$authed')");
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
	
	mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteSingular($var, $secret = null)
{
	global $link;
	$var = etc\sanitize($var);
	
    mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `varid` = '$var'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function edit($name, $data, $authed, $secret = null)
{
	global $link;
	$name = etc\sanitize($name);
	$data = etc\sanitize($data);
	$authed = etc\sanitize($authed);
	
	mysqli_query($link, "UPDATE `vars` SET `msg` = '$data', `authed` = '$authed' WHERE `varid` = '$name' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
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