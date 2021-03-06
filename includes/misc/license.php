<?php
namespace misc\license;

use misc\etc;
function license_masking($mask)
{
    $mask_arr = str_split($mask);
    $size_of_mask = count($mask_arr);
    for ($i = 0;$i < $size_of_mask;$i++)
    {
        if ($mask_arr[$i] === 'X')
        {
            $mask_arr[$i] = etc\random_string_upper(1);
        }
        else if ($mask_arr[$i] === 'x')
        {
            $mask_arr[$i] = etc\random_string_lower(1);
        }
    }
    return implode('', $mask_arr);
}
function createLicense($amount, $mask, $duration, $level, $note, $expiry = null, $secret = null)
{
	global $link;
	$amount = etc\sanitize($amount);
	$mask = etc\sanitize($mask);
	$duration = etc\sanitize($duration);
	$level = etc\sanitize($level);
	$note = etc\sanitize($note);
	$expiry = etc\sanitize($expiry);
	$secret = etc\sanitize($secret);
	
	if ($amount > 100)
    {
        return 'max_keys';
    }
	if (!isset($amount))
    {
        $amount = 1;
    }
	if (!is_numeric($level))
    {
        $level = 1;
    }
	if ($_SESSION['role'] == "tester")
    {
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `genby` = '" . $_SESSION['username'] . "'");
        $currkeys = mysqli_num_rows($result);
        if ($currkeys + $amount > 50)
        {
            return 'tester_limit';
        }
    }
	if(is_null($expiry))
	{
		$expiry = 86400; // set unit to day(s) if license expiry unit isn't specified (as with SellerAPI)
	}
	$duration = $duration * $expiry;
	if ($amount > 1 && strpos($mask, 'X') === false && strpos($mask, 'x') === false)
	{
		return 'dupe_custom_key';
	}
	$licenses = array();

    for ($i = 0;$i < $amount;$i++)
    {

        $license = license_masking($mask);
        mysqli_query($link, "INSERT INTO `keys` (`key`, `note`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$license',NULLIF('$note', ''), '$duration','Not Used','$level','" . ($_SESSION['username'] ?? 'SellerAPI') . "', '" . time() . "', '" . ($secret ?? $_SESSION['app']) . "')");
        $licenses[] = $license;
    }

    return $licenses;
}
function addTime($time, $expiry, $secret = null)
{
	global $link;
	$time = etc\sanitize($time);
    $expiry = etc\sanitize($expiry);
	
    $time = $time * $expiry;
    mysqli_query($link, "UPDATE `keys` SET `expires` = `expires`+$time WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Not Used'");
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
	
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
        return 'failure';
    }
}
function deleteAllUnused($secret = null)
{
	global $link;
	
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Not Used'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
        return 'failure';
    }
}
function deleteAllUsed($secret = null)
{
	global $link;
	
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `status` = 'Used'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
        return 'failure';
    }
}
function deleteSingular($key, $secret = null)
{
	global $link;
	$key = etc\sanitize($key);
	mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'"); // delete any subscriptions created with key
	mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
        return 'failure';
    }
}
function ban($key, $reason, $secret = null)
{
	global $link;
	$key = etc\sanitize($key);
	$reason = etc\sanitize($reason);
	
	mysqli_query($link, "UPDATE `keys` SET `banned` = '$reason', `status` = 'Banned' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
    if (mysqli_affected_rows($link) > 0)
    {
		return 'success';
    }
    else
    {
        return 'failure';
    }
}
function unban($key, $secret = null)
{
	global $link;
	$key = etc\sanitize($key);
	
	$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'");
    if (mysqli_num_rows($result) == 0) // check if key exists
    {
        return 'missing';
    }
    $row = mysqli_fetch_array($result);
    $usedby = $row["usedby"];
    $status = "Used";
    if (is_null($usedby))
    {
        $status = "Not Used";
    }
    mysqli_query($link, "UPDATE `keys` SET `banned` = NULL, `status` = '$status' WHERE `app` = '" . ($secret ?? $_SESSION['app']) . "' AND `key` = '$key'"); // update key from banned to its old status
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
