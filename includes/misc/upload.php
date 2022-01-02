<?php
namespace misc\upload;

use misc\etc;

function add($url, $authed, $secret = null)
{
	global $link;
	$url = etc\sanitize($url);
	$authed = etc\sanitize($authed);
	
	if (!filter_var($url, FILTER_VALIDATE_URL))
    {
        return 'invalid';
    }
    $file = file_get_contents($url);
    $filesize = strlen($file);
    if ($filesize > 10000000 && $role == "tester")
    {
        error("Users with tester plan may only upload files up to 10MB. Paid plans may upload up to 50MB.");
        return;
    }
    else if ($filesize > 50000000 && $role == "developer")
    {
        error("File size limit is 50 MB.");
        return;
    }
    else if ($filesize > 75000000)
    {
        error("File size limit is 75 MB.");
        return;
    }
	$id = etc\generateRandomNum();
	$fn = basename($url);
    $fs = etc\formatBytes($filesize);
	mysqli_query($link, "INSERT INTO `files` (name, id, url, size, uploaddate, app, authed) VALUES ('$fn', '$id', '$url', '$fs', '" . time() . "', '" . ($secret ?? $_SESSION['app']) . "', '$authed')");
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
	
	mysqli_query($link, "DELETE FROM `files` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteSingular($file, $secret = null)
{
	global $link;
	$file = etc\sanitize($file);
	
    mysqli_query($link, "DELETE FROM `files` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `id` = '$file'");
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