<?php
namespace misc\user;

use misc\etc;

function deleteSingular($username, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	
    mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username'");
    mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
	
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function resetSingular($username, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	
    mysqli_query($link, "UPDATE `users` SET `hwid` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function setVariable($user, $var, $data, $secret = null)
{
	global $link;
	$user = etc\sanitize($user);
	$var = etc\sanitize($var);
	$data = etc\sanitize($data);
	
	if ($user == "all")
    {
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
        if (mysqli_num_rows($result) == 0)
        {
            return 'missing';
        }
        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }
        foreach ($rows as $row)
        {
            mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '" . $row['username'] . "', '" . ($secret ?? $_SESSION['app']) . "')");
        }
    }
    else
    {
        mysqli_query($link, "REPLACE INTO `uservars` (`name`, `data`, `user`, `app`) VALUES ('$var', '$data', '$user', '" . ($secret ?? $_SESSION['app']) . "')");
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
function ban($username, $reason, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	$reason = etc\sanitize($reason);
	
	$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
    if (mysqli_num_rows($result) == 0)
    {
        return 'missing';
    }
    $row = mysqli_fetch_array($result);
    $hwid = $row["hwid"];
    $ip = $row["ip"];
    mysqli_query($link, "UPDATE `users` SET `banned` = '$reason' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
    if ($hwid != NULL)
    {
        mysqli_query($link, "INSERT INTO `bans`(`hwid`,`type`, `app`) VALUES ('$hwid','hwid','" . ($secret ?? $_SESSION['app']) . "')");
    }
    if ($ip != NULL)
    {
        mysqli_query($link, "INSERT INTO `bans`(`ip`,`type`, `app`) VALUES ('$ip','ip','" . ($secret ?? $_SESSION['app']) . "')");
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
function unban($username, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	
    $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
    if (mysqli_num_rows($result) == 0)
    {
        return 'missing';
    }
    $row = mysqli_fetch_array($result);
    $hwid = $row["hwid"];
    $ip = $row["ip"];
	mysqli_query($link, "DELETE FROM `bans` WHERE `hwid` = '$hwid' OR `ip` = '$ip' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    mysqli_query($link, "UPDATE `users` SET `banned` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '$username'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteVar($username, $var, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	$var = etc\sanitize($var);
	
    mysqli_query($link, "DELETE FROM `uservars` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username' AND `name` = '$var'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteSub($username, $sub, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	$sub = etc\sanitize($sub);
	
    mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `user` = '$username' AND `subscription` = '$sub'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function extend($username, $sub, $expiry, $secret = null)
{
	global $link;
	$username = etc\sanitize($username);
	$sub = etc\sanitize($sub);
	$expiry = etc\sanitize($expiry);
	
    $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_num_rows($result) == 0)
    {
        return 'sub_missing';
    }
    else if ($expiry < time())
    {
        return 'date_past';
    }
    if ($username == "all")
    {
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
        if (mysqli_num_rows($result) == 0)
        {
            return 'missing';
        }
        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }
        foreach ($rows as $row)
        {
            mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('" . $row['username'] . "','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
        }
    }
    else
    {
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$username' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
        if (mysqli_num_rows($result) == 0)
        {
            return 'missing';
        }
        mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
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
function add($username, $sub, $expiry, $secret = null, $password = null)
{
	global $link;
	$username = etc\sanitize($username);
	if(!empty($password))
	$password = password_hash(etc\sanitize($password) , PASSWORD_BCRYPT);
	$sub = etc\sanitize($sub);
	$expiry = etc\sanitize($expiry);
	
    $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_num_rows($result) == 0)
    {
        return 'sub_missing';
    }
    else if ($expiry < time())
    {
        return 'date_past';
    }
	
	mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$username','$sub', '$expiry', '" . ($secret ?? $_SESSION['app']) . "')");
    mysqli_query($link, "INSERT INTO `users` (`username`, `password`, `hwid`, `app`,`owner`,`createdate`) VALUES ('$username',NULLIF('$password', ''), NULL, '" . ($secret ?? $_SESSION['app']) . "','" . ($_SESSION['username'] ?? 'SellerAPI') . "','" . time() . "')");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function deleteExpiredUsers($secret = null)
{
	global $link;

    $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_num_rows($result) == 0)
    {
        return 'missing';
    }
    $rows = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $rows[] = $r;
    }
    $success = 0;
    foreach ($rows as $row)
    {
        $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `user` = '" . $row['username'] . "' AND `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `expiry` > '" . time() . "'");
        if (mysqli_num_rows($result) == 0)
        {
            $success = 1;
            mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `username` = '" . $row['username'] . "'");
        }
    }
    if ($success)
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

    mysqli_query($link, "DELETE FROM `users` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
		return 'failure';
    }
}
function resetAll($secret = null)
{
	global $link;

    mysqli_query($link, "UPDATE `users` SET `hwid` = NULL WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
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