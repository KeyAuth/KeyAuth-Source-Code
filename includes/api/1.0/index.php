<?php
include '../../connection.php';
include '../../functions.php';

#region enc region
function Encrypt($string, $enckey)

{
    return bin2hex(openssl_encrypt($string, 'aes-256-cbc', substr(hash('sha256', $enckey) , 0, 32) , OPENSSL_RAW_DATA, substr(hash('sha256', $_POST['init_iv']) , 0, 16)));
}

function Decrypt($string, $enckey)

{
    return openssl_decrypt(hex2bin($string) , 'aes-256-cbc', substr(hash('sha256', $enckey) , 0, 32) , OPENSSL_RAW_DATA, substr(hash('sha256', $_POST['init_iv']) , 0, 16));
}
#endregion

#region rgstr region
function register($un,$key,$pw,$hwid,$secret)
{
		global $link; // needed to refrence active MySQL connection
		global $ip; // needed to refrence client IP
		
        // search username
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '$secret'");

		// if username already in existence
        if (mysqli_num_rows($result) >= 1)

        {
			return 'username_taken';
        }

        // search for key
        $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$key' AND `app` = '$secret'");

        // check if key exists
        if (mysqli_num_rows($result) < 1)

        {

            return 'key_not_found';

        }

        // if key does exist
        elseif (mysqli_num_rows($result) > 0)

        {

            $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$key' AND `app` = '$secret'");

            // gather key info
            while ($row = mysqli_fetch_array($result))
            {

                $expires = $row['expires'];

                $status = $row['status'];

                $level = $row['level'];
				
                $genby = $row['genby'];

            }
            // check license status
			switch($status)
			{
				case 'Used':
					return 'key_already_used';
				case 'Banned':
					return 'key_banned';
					
			}
			
			$hwidcheck = mysqli_query($link, "SELECT * FROM `bans` WHERE (`hwid` = '$hwid' OR `ip` = '$ip') AND `app` = '$secret'");
            if (mysqli_num_rows($hwidcheck) > 0)

            {
                mysqli_query($link, "UPDATE `keys` SET `status` = 'Banned',`banned` = 'This key has been banned as the client was blacklisted.' WHERE `key` = '$un' AND `app` = '$secret'");
				return 'hwid_blacked';
            }
			

            // add current time to key time
            $expiry = $expires + time();

            $result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '$secret' AND `level` = '$level'");

            $num = mysqli_num_rows($result);

            if ($num == 0)

            {

                return 'no_subs_for_level';

            }
			
			// update key to used
            mysqli_query($link, "UPDATE `keys` SET `status` = 'Used',`usedon` = '".time()."',`usedby` = '$un' WHERE `key` = '$key'");

            while ($row = mysqli_fetch_array($result))
            {
				// add each subscription that user's key applies to
                $subname = $row['name'];
                mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`, `key`) VALUES ('$un','$subname', '$expiry', '$secret','$key')");

            }
			$password = password_hash($pw, PASSWORD_BCRYPT);
			
			$createdate = time();
			
            // create user
            mysqli_query($link, "INSERT INTO `users` (`username`, `password`, `hwid`, `app`,`owner`,`createdate`) VALUES ('$un','$password', NULLIF('$hwid', ''), '$secret', '$genby', '$createdate')");

			
            $result = mysqli_query($link, "SELECT `subscription`, `expiry` FROM `subs` WHERE `user` = '$un' AND `app` = '$secret' AND `expiry` > " . time() . "");

            $rows = array();

            while ($r = mysqli_fetch_assoc($result))
            {
				
				$timeleft = $r["expiry"] - time();
				$r += [ "timeleft" => $timeleft ];
                $rows[] = $r;

            }
            

            // success
            return array(
                "username" => "$un",
                "subscriptions" => $rows,
                "ip" => $ip,
				"hwid" => $hwid,
				"createdate" => "$createdate",
				"lastlogin" => "".time().""
            );
        }
}
#endregion

#region login region
function login($un,$pw,$hwid,$secret,$hwidenabled)
{
		global $link; // needed to refrence active MySQL connection
		global $ip; // needed to refrence client IP
		
        // Find username
        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '$secret'");

        // if not found
        if (mysqli_num_rows($result) === 0)

        {

            return 'un_not_found';

        }

        // if found
        elseif (mysqli_num_rows($result) > 0)

        {

            // get all rows from username query
            while ($row = mysqli_fetch_array($result))

            {

                $pass = $row['password'];
                //$expires = $row['expires'];
                $hwidd = $row['hwid'];
                $banned = $row['banned'];
                $createdate = $row['createdate'];

            }
			
			if($banned != NULL)
			{
				return 'user_banned';
			}
			
			$hwidcheck = mysqli_query($link, "SELECT * FROM `bans` WHERE (`hwid` = '$hwid' OR `ip` = '$ip') AND `app` = '$secret'");
			if (mysqli_num_rows($hwidcheck) > 0)
	
			{
				mysqli_query($link, "UPDATE `users` SET `banned` = 'User is blacklisted' WHERE `username` = '$un' AND `app` = '$secret'");
				return 'hwid_blacked';
			}

			if(!is_null($pass))
			{
				// check if pass matches
				if (!password_verify($pw, $pass))
				{
	
					return 'pw_mismatch';
	
				}
			}
			else
			{
				$pass_encrypted = password_hash($pw, PASSWORD_BCRYPT);
				mysqli_query($link, "UPDATE `users` SET `password` = '$pass_encrypted' WHERE `username` = '$un' AND `app` = '$secret'");
			}

            // check if hwid enabled for application
            if ($hwidenabled == "1")

            {

                // check if hwid in db contains hwid recieved
                if (strpos($hwidd, $hwid) === false && $hwidd != NULL)

                {

                    return 'hwid_mismatch';

                }
				else if($hwidd == NULL)
				{
					mysqli_query($link, "UPDATE `users` SET `hwid` = NULLIF('$hwid', '') WHERE `username` = '$un' AND `app` = '$secret'");
				}

            }
			
            $result = mysqli_query($link, "SELECT `subscription`, `key`, `expiry` FROM `subs` WHERE `user` = '$un' AND `app` = '$secret' AND `expiry` > " . time() . "");

            $num = mysqli_num_rows($result);

            if ($num == 0)

            {
				$result = mysqli_query($link, "SELECT `paused` FROM `subs` WHERE `user` = '$un' AND `app` = '$secret' AND `paused` = 1");
				if(mysqli_num_rows($result) >= 1)
				{
					mysqli_close($link);
					return 'sub_paused';
				}

                mysqli_close($link);
                return 'no_active_subs';

            }
			$lastlogin = time();
			mysqli_query($link, "UPDATE `users` SET `ip` = NULLIF('$ip', ''),`lastlogin` = '$lastlogin' WHERE `username` = '$un'");

            $rows = array();

            while ($r = mysqli_fetch_assoc($result))
            {
				$timeleft = $r["expiry"] - time();
				$r += [ "timeleft" => $timeleft ];
                $rows[] = $r;

            }
			
            // success			
			return array(
                "username" => "$un",
                "subscriptions" => $rows,
                "ip" => $ip,
				"hwid" => $hwid,
				"createdate" => $createdate,
				"lastlogin" => "$lastlogin"
            );

        }
}
#endregion
?>