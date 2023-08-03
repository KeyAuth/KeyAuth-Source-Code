<?php

namespace misc\license;

use misc\etc;
use misc\cache;
use misc\user;
use misc\mysql;

function license_masking($mask, $int = null) // substitute random characters for upper-case and lower-case random character variables, X or x
{
        $mask_arr = str_split($mask);
        $size_of_mask = count($mask_arr);
        for ($i = 0; $i < $size_of_mask; $i++) 
        {
                if ($mask_arr[$i] === '*') {
                        if (isset($_POST['lowercaseLetters']) && $_POST['lowercaseLetters'] == 'on' && isset($_POST['capitalLetters']) && $_POST['capitalLetters'] == 'on')
                        {
                                $mask_arr[$i] = etc\random_string_gen(1);
                        }
                        elseif (isset($_POST['lowercaseLetters']) && $_POST['lowercaseLetters'] == 'on') 
                        {
                                $mask_arr[$i] = etc\random_string_lower(1);
                        }
                        elseif (isset($_POST['capitalLetters']) && $_POST['capitalLetters'] == 'on') 
                        {
                                $mask_arr[$i] = etc\random_string_upper(1);
                        }

                else
                {
                        if (isset($int)) {
                                if ($int === "1") 
                                {
                                        $mask_arr[$i] = etc\random_string_gen(1);
                                }
                                if ($int === "2") 
                                {
                                        $mask_arr[$i] = etc\random_string_upper(1);
                                }
                                if ($int === "3") 
                                {
                                        $mask_arr[$i] = etc\random_string_lower(1);
                                }
                        }
                        else {
                                $mask_arr[$i] = etc\random_string_gen(1);
                        }
               }
        }
        }
        return implode('', $mask_arr);
}
function createLicense($amount, $mask, $duration, $level, $note, $expiry = null, $secret = null, $owner = null, $character = null)
{
        $amount = etc\sanitize($amount);
        $mask = etc\sanitize($mask);
        $duration = etc\sanitize($duration);
        $level = etc\sanitize($level);
        $note = etc\sanitize($note);
        $expiry = etc\sanitize($expiry);
        $secret = etc\sanitize($secret);
        $letters = etc\sanitize($character);

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
        if (is_null($expiry)) 
        {
                $expiry = 86400; // set unit to day(s) if license expiry unit isn't specified (as it isn't with SellerAPI)
        }
        $duration = $duration * $expiry;
        if ($amount > 1 && strpos($mask, '*') === false) 
        {
                return 'dupe_custom_key';
        }

        switch ($_SESSION['role']) 
        {
                case 'tester':
                        $query = mysql\query("SELECT 1 FROM `keys` WHERE `genby` = ? AND `app` = ?",[$_SESSION['username'], $_SESSION['app']]);
                        $currkeys = $query->num_rows;
                        if ($currkeys + $amount > 10) 
                        {
                                return 'tester_limit';
                        }

                        $mask = "KEYAUTH-" . $mask;
                        break;
                case 'Reseller':
                        if ($amount < 0) 
                        {
                                return 'no_negative';
                        }
                        $query = mysql\query("SELECT `keylevels`, `balance` FROM `accounts` WHERE `username` = ?",[$_SESSION['username']]);
                        $row = mysqli_fetch_array($query->result);
                        $keylevels = explode("|", $row['keylevels']);
                        $balance = explode("|", $row['balance']);
                        if ($row['keylevels'] != "N/A" && !in_array($level, $keylevels)) 
                        {
                                return 'unauthed_level';
                        }
                        $day = $balance[0];
                        $week = $balance[1];
                        $month = $balance[2];
                        $threemonth = $balance[3];
                        $sixmonth = $balance[4];
                        $lifetime = $balance[5];
                        switch ($expiry) 
                        {
                                case '1 Day':
                                        $duration = 86400;
                                        $day = $day - $amount;
                                        break;
                                case '1 Week':
                                        $duration = 604800;
                                        $week = $week - $amount;
                                        break;
                                case '1 Month':
                                        $duration = 2.592e+6;
                                        $month = $month - $amount;
                                        break;
                                case '3 Month':
                                        $duration = 7.862e+6;
                                        $threemonth = $threemonth - $amount;
                                        break;
                                case '6 Month':
                                        $duration = 1.572e+7;
                                        $sixmonth = $sixmonth - $amount;
                                        break;
                                case '1 Lifetime':
                                        $duration = 8.6391e+8;
                                        $lifetime = $lifetime - $amount;
                                        break;
                                default:
                                        return 'invalid_exp';
                                        break;
                        }
                        if ($day < 0 || $month < 0 || $week < 0 || $threemonth < 0 || $sixmonth < 0 || $lifetime < 0) 
                        {
                                return 'insufficient_balance';
                        }
                        $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
                        $query = mysql\query("UPDATE `accounts` SET `balance` = ? WHERE `username` = ?",[$balance, $_SESSION['username']]);
                        break;
                case 'seller':
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                        break;
        }
        
        if(!is_null($secret)) {
                cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
        }

        $licenses = array();

        for ($i = 0; $i < $amount; $i++) 
        {
                $license = license_masking($mask, $letters);
                $query = mysql\query("INSERT INTO `keys` (`key`, `note`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES (?, NULLIF(?, ''), ?, 'Not Used', ?, ?, ?, ?)",[$license, $note, $duration, $level, $owner ?? $_SESSION['username'], time(), $secret ?? $_SESSION['app']]);
                $licenses[] = $license;
        }

        return $licenses;
}

function addTime($time, $expiry, $secret = null)
{
        $time = etc\sanitize($time);
        $expiry = etc\sanitize($expiry);

        $time = $time * $expiry;
        $query = mysql\query("UPDATE `keys` SET `expires` = `expires`+? WHERE `app` = ? AND `status` = 'Not Used'",[$time, $secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) 
                {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
function deleteAll($secret = null)
{
        $query = mysql\query("DELETE FROM `keys` WHERE `app` = ?",[$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
function deleteAllUnused($secret = null)
{
        $query = mysql\query("DELETE FROM `keys` WHERE `app` = ? AND `status` = 'Not Used'",[$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) 
                {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
function deleteAllUsed($secret = null)
{
        $query = mysql\query("DELETE FROM `keys` WHERE `app` = ? AND `status` = 'Used'",[$secret ?? $_SESSION['app']]);
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) 
                {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
function deleteSingular($key, $userToo, $secret = null)
{
    $key = etc\sanitize($key);
    $userToo = etc\sanitize($userToo);

    if ($_SESSION['role'] == "Reseller") 
        {
        $query = mysql\query("SELECT 1 FROM `keys` WHERE `app` = ? AND `key` = ? AND `genby` = ?",[$secret ?? $_SESSION['app'], $key, $_SESSION['username']]);
        if ($query->num_rows < 1) 
                {
            return 'nope';
        }
    }

    if ($userToo) {
        $query = mysql\query("SELECT `usedby` FROM `keys` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);
        $row = mysqli_fetch_array($query->result);
        $usedby = $row['usedby'];

        user\deleteSingular($usedby, $secret);
    }

    $query = mysql\query("DELETE FROM `subs` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);// delete any subscriptions created with key
    $query = mysql\query("DELETE FROM `keys` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);
    if ($query->affected_rows > 0) 
        {
        if ($_SESSION['role'] == "seller" || !is_null($secret)) {
            cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
            cache\purge('KeyAuthKey:' . ($secret ?? $_SESSION['app']) . ':' . $key);
        }
        return 'success';
    } 
        else 
        {
        return 'failure';
    }
}

function deleteMultiple($keys, $userToo, $secret = null) {
    $keys = explode(', ', $keys);
    $userToo = etc\sanitize($userToo);

    foreach ($keys as $key) {
        $key = etc\sanitize(trim($key));

        if ($_SESSION['role'] == "Reseller") 
                {
            $query = mysql\query("SELECT 1 FROM `keys` WHERE `app` = ? AND `key` = ? AND `genby` = ?",[$secret ?? $_SESSION['app'], $key, $_SESSION['username']]);
            if ($query->num_rows < 1) 
                        {
                return 'nope';
            }
        }

        if ($userToo) 
                {
            $query = mysql\query("SELECT `usedby` FROM `keys` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);
            $row = mysqli_fetch_array($query->result);
            $usedby = $row['usedby'];

            user\deleteSingular($usedby, $secret);
        }

        $query = mysql\query("DELETE FROM `subs` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);// delete any subscriptions created with key
        $query = mysql\query("DELETE FROM `keys` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);
        if ($query->affected_rows > 0) 
                {
            if ($_SESSION['role'] == "seller" || !is_null($secret)) 
                        {
                cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                cache\purge('KeyAuthKey:' . ($secret ?? $_SESSION['app']) . ':' . $key);
            }
        } 
                else 
                {
            return 'failure';
        }
    }
    return 'success';
}

function ban($key, $reason, $userToo, $secret = null)
{
        $key = etc\sanitize($key);
        $reason = etc\sanitize($reason);
        $userToo = etc\sanitize($userToo);

        if ($_SESSION['role'] == "Reseller") 
        {
                $query = mysql\query("SELECT 1 FROM `keys` WHERE `app` = ? AND `key` = ? AND `genby` = ?", [$secret ?? $_SESSION['app'], $key, $_SESSION['username']]);
                if ($query->num_rows === 0) 
                {
                        return 'nope';
                }
        }

        if ($userToo) {
                $query = mysql\query("SELECT `usedby` FROM `keys` WHERE `app` = ? AND `key` = ?", [$secret ?? $_SESSION['app'], $key]);
                $row = mysqli_fetch_array($query->result);
                $usedby = $row['usedby'];
                
                user\ban($usedby, $reason, $secret);
        }

        $query = mysql\query("UPDATE `keys` SET `banned` = ?, `status` = 'Banned' WHERE `app` = ? AND `key` = ?",[$reason, $secret ?? $_SESSION['app'], $key]);
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
function unban($key, $secret = null)
{
        $key = etc\sanitize($key);

        if ($_SESSION['role'] == "Reseller") 
        {
                $query = mysql\query("SELECT 1 FROM `keys` WHERE `app` = ? AND `key` = ? AND `genby` = ?",[$secret ?? $_SESSION['app'], $key, $_SESSION['username']]);
                if ($query->num_rows === 0) 
                {
                        return 'nope';
                }
        }

        $status = "Not Used";
        $query = mysql\query("SELECT `usedby` FROM `keys` WHERE `app` = ? AND `key` = ?",[$secret ?? $_SESSION['app'], $key]);
        $row = mysqli_fetch_array($query->result);
        if (!is_null($row['usedby'])) 
        {
                $status = "Used";       
                user\unban($row['usedby'], $secret);
        }

        $query = mysql\query("UPDATE `keys` SET `banned` = NULL, `status` = ? WHERE `app` = ? AND `key` = ?",[$status, $secret ?? $_SESSION['app'], $key]);// update key from banned to its old status
        if ($query->affected_rows > 0) 
        {
                if ($_SESSION['role'] == "seller" || !is_null($secret)) 
                {
                        cache\purge('KeyAuthKeys:' . ($secret ?? $_SESSION['app']));
                }
                return 'success';
        } 
        else 
        {
                return 'failure';
        }
}
