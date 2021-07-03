<?php
include 'connection.php'; // start MySQL connection
session_start();

$role = $_SESSION['role']; // user role
function sanitize($input)
{
    global $link; // needed to refrence active MySQL connection
    return mysqli_real_escape_string($link, strip_tags(trim($input))); // return string with quotes escaped to prevent SQL injection, script tags stripped to prevent XSS attach, and trimmed to remove whitespace
    
}

function heador()
{
    global $link; // needed to refrence active MySQL connection
    global $role; // needed to refrence user role
    if ($role != "Manager")
    {
        echo "
                                <form class=\"text-left\" method=\"POST\">
                    <p class=\"mb-4\">Name: <br>" . $_SESSION['name'] . "<br /><div class=\"mb-4\">Secret: <div class=\"secret\">" . $_SESSION['secret'] . "</div></div><a style=\"color:#4e73df;cursor: pointer;\" id=\"mylink\">Change</a><button style=\"border: none;padding:0;background:0;color:#FF0000;padding-left:5px;\" name=\"deleteapp\" onclick=\"return confirm('Are you sure you want to delete application?')\">Delete</button>";
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
        $row = mysqli_fetch_array($result);
        if ($row['paused'] == "0")
        {
            echo "<button style=\"border: none;padding:0;background:0;color:#ffcc00;padding-left:5px;\" name=\"pausekeys\" onclick=\"return confirm('Are you sure you want to pause all keys?')\">Pause</button><button style=\"border: none;padding:0;background:0;color:#52ef52;padding-left:5px;\" name=\"refreshapp\" onclick=\"return confirm('Are you sure you want to reset application secret?')\">Refresh</button><button style=\"border: none;padding:0;background:0;color:#a28a5e;padding-left:5px;\" data-toggle=\"modal\" type=\"button\" data-target=\"#rename-app\" >Rename</button></p></form>";
        }
        else
        {
            echo "<button style=\"border: none;padding:0;background:0;color:#ffcc00;padding-left:5px;\" name=\"unpausekeys\" onclick=\"return confirm('Are you sure you want to unpause all keys?')\">Unpause</button><button style=\"border: none;padding:0;background:0;color:#52ef52;padding-left:5px;\" name=\"refreshapp\" onclick=\"return confirm('Are you sure you want to reset application secret?')\">Refresh</button><button style=\"border: none;padding:0;background:0;color:#a28a5e;padding-left:5px;\" data-toggle=\"modal\" type=\"button\" data-target=\"#rename-app\" >Rename</button></p></form>";
        }

        if (isset($_POST['deleteapp']))
        {

            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Delete Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            $app = $_SESSION['app'];
            $owner = $_SESSION['username'];

            mysqli_query($link, "DELETE FROM `files` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete files
            mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete keys
            mysqli_query($link, "DELETE FROM `logs` WHERE `logapp` = '$app'") or die(mysqli_error($link)); // delete logs
            mysqli_query($link, "DELETE FROM `apps` WHERE `secret` = '$app'") or die(mysqli_error($link));

            if (mysqli_affected_rows($link) != 0)
            {
                $_SESSION['app'] = NULL;
                success("Successfully deleted App!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
            else
            {
                error("Application Deletion Failed!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
        }

        if (isset($_POST['pausekeys']))
        {

            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Reset Keys");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            ($result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '" . $_SESSION['app'] . "' AND `status` = 'Used'")) or die(mysqli_error($link));
            if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $expires = $row['expires'];
                    $exp = $expires - time();
                    mysqli_query($link, "UPDATE `keys` SET `status` = 'Paused', `expires` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `key` = '" . $row['key'] . "'");
                }
                mysqli_query($link, "UPDATE `apps` SET `paused` = 1 WHERE `secret` = '" . $_SESSION['app'] . "'");
                success("Paused All Keys!");
                echo "<meta http-equiv='Refresh' Content='2'>";
            }
            else
            {
                mysqli_close($link);
                error("Found no Used Keys!");
                echo "<meta http-equiv='Refresh' Content='2'>";
                return;
            }
        }

        if (isset($_POST['unpausekeys']))
        {
            ($result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '" . $_SESSION['app'] . "' AND `status` = 'Paused'")) or die(mysqli_error($link));
            if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $expires = $row['expires'];
                    $exp = $expires + time();
                    mysqli_query($link, "UPDATE `keys` SET `status` = 'Used', `expires` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `key` = '" . $row['key'] . "'");

                }
                mysqli_query($link, "UPDATE `apps` SET `paused` = 0 WHERE `secret` = '" . $_SESSION['app'] . "'");
                success("Unpaused All Keys!");
                echo "<meta http-equiv='Refresh' Content='2'>";
            }
            else
            {
                mysqli_close($link);
                error("Found no Paused Keys!");
                echo "<meta http-equiv='Refresh' Content='2'>";
                return;
            }
        }

        if (isset($_POST['refreshapp']))
        {
            $gen = generateRandomString();
            $new_secret = hash('sha256', $gen);

            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Refresh Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            mysqli_query($link, "UPDATE `bans` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `files` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `keys` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `logs` SET `logapp` = '$new_secret' WHERE `logapp` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `subs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `subscriptions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `users` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `vars` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `webhooks` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `apps` SET `secret` = '$new_secret' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");

            $_SESSION['app'] = $new_secret;
            $_SESSION['secret'] = $new_secret;

            if (mysqli_affected_rows($link) != 0)
            {
                success("Successfully Refreshed App!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
            else
            {
                error("Application Refresh Failed!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
        }

        if (isset($_POST['renameapp']))
        {
            $name = sanitize($_POST['name']);

            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Rename Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '$name'");
            $num = mysqli_num_rows($result);
            if ($num > 0)
            {
                error("You already have an application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            mysqli_query($link, "UPDATE `apps` SET `name` = '$name' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");

            $_SESSION['name'] = $name;

            if (mysqli_affected_rows($link) != 0)
            {
                success("Successfully Renamed App!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
            else
            {
                error("Application Renamed Failed!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
        }
		
		if (isset($_POST['appname']))
        {
            $appname = sanitize($_POST['appname']);
            $result = mysqli_query($link, "SELECT * FROM apps WHERE name='$appname' AND owner='".$_SESSION['username']."'");
            if (mysqli_num_rows($result) > 0)
            {
				mysqli_close($link);
				error("You already own application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            
            $owner = $_SESSION['username'];
			
			if($role == "tester")
            {
			$result = mysqli_query($link, "SELECT * FROM apps WHERE owner='$owner'");

            if (mysqli_num_rows($result) > 0)
            {
				mysqli_close($link);
				error("Tester plan only supports one application!");
                echo "<meta http-equiv='Refresh' Content='2;'>";

                return;
            }
			
			}
			
			if($role == "Manager")
            {
				mysqli_close($link);
				error("Manager Accounts Are Not Allowed To Create Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
			}
			
            $ownerid = $_SESSION['ownerid'];
            $clientsecret = hash('sha256', generateRandomString());
			$algos = array(
				'ripemd128',
				'md5',
				'md4',
				'tiger128,4',
				'haval128,3',
				'haval128,4',
				'haval128,5'
			);
			$sellerkey = hash($algos[array_rand($algos) ], generateRandomString());
            $result = mysqli_query($link, "INSERT INTO `apps`(`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES ('".$owner."','".$appname."','".$clientsecret."','$ownerid', '1','1','$sellerkey')");
            mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('default', '1', '$clientsecret')");
			if (mysqli_affected_rows($link) != 0)
            {
                $_SESSION['secret'] = $clientsecret;
				success("Successfully Created App!");
                $_SESSION['app'] = $clientsecret;
                $_SESSION['name'] = $appname;
                $_SESSION['sellerkey'] = $sellerkey;
                echo "<meta http-equiv='Refresh' Content='2;'>";     
            }
            else
            {
                error("Failed to create application!");
            }
        }

    }

}
function sidebar($role)
{
    echo '	<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Application</span></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/licenses/" aria-expanded="false"><i data-feather="key"></i><span class="hide-menu">Licenses</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/users/" aria-expanded="false"><i data-feather="users"></i><span class="hide-menu">Users</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/subscriptions/" aria-expanded="false"><i data-feather="bar-chart"></i><span class="hide-menu">Subscriptions</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/webhooks/" aria-expanded="false"><i data-feather="server"></i><span class="hide-menu">Webhooks</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/files/" aria-expanded="false"><i data-feather="paperclip"></i><span class="hide-menu">Files</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/variables/" aria-expanded="false"><i data-feather="file-text"></i><span class="hide-menu">Variables</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/logs/" aria-expanded="false"><i data-feather="database"></i><span class="hide-menu">Logs</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/blacklists/" aria-expanded="false"><i data-feather="user-x"></i><span class="hide-menu">Blacklists</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Account</span></li>';
    if ($role == "developer" || $role == "seller")
    {
        echo '                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/manage/" aria-expanded="false"><i data-feather="sliders"></i><span class="hide-menu">Manage</span></a></li>';
    }
    echo '                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/upgrade/" aria-expanded="false"><i data-feather="activity"></i><span class="hide-menu">Upgrade</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>';
    if ($role == "seller")
    {
        echo '                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Seller</span></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../seller/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>						';
    }
}
function error($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>
                
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'' . $msg . '\',
                                duration: 3500,
                                dismissible: true
                              });               
                
                </script>';
}

function success($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>
                
                            const notyf = new Notyf();
                            notyf
                              .success({
                                message: \'' . $msg . '\',
                                duration: 3500,
                                dismissible: true
                              });               
                
                </script>';
}

function random_string_upper($length = 10, $keyspace = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'):
    string
    {
        $out = '';

        for ($i = 0;$i < $length;$i++)
        {
            $rand_index = random_int(0, strlen($keyspace) - 1);

            $out .= $keyspace[$rand_index];
        }

        return $out;
    }

    function random_string_lower($length = 10, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyz'):
        string
        {
            $out = '';

            for ($i = 0;$i < $length;$i++)
            {
                $rand_index = random_int(0, strlen($keyspace) - 1);

                $out .= $keyspace[$rand_index];
            }

            return $out;
        }

        function formatBytes($bytes, $precision = 2)
        {
            $units = array(
                'B',
                'KB',
                'MB',
                'GB',
                'TB'
            );

            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);

            // Uncomment one of the following alternatives
            // $bytes /= pow(1024, $pow);
            $bytes /= (1 << (10 * $pow));

            return round($bytes, $precision) . ' ' . $units[$pow];
        }

        function generateRandomString($length = 10)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0;$i < $length;$i++)
            {
                $randomString .= $characters[rand(0, $charactersLength - 1) ];
            }
            return $randomString;
        }

        function generateRandomNum($length = 6)
        {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0;$i < $length;$i++)
            {
                $randomString .= $characters[rand(0, $charactersLength - 1) ];
            }
            return $randomString;
        }

        function file_encrypt($text, $key = 'KeyAuth'):
            string
            {
                $iv = "salksalasklsakslakaslkasl";
                return base64_encode(openssl_encrypt($text, 'aes-256-cbc', md5($key) , true, $iv) . '{keyauth}' . $iv);
            }

            function file_decrypt($text, $key = 'KeyAuth'):
                string
                {
                    $data = explode('{keyauth}', base64_decode($text));
                    return openssl_decrypt($data[0], 'aes-256-cbc', md5($key) , true, $data[1]);
                }

                function getsession($sessionid, $secret)
                {
                    global $link; // needed to refrence active MySQL connection
                    mysqli_query($link, "DELETE FROM `sessions` WHERE `expiry` < " . time() . "") or die(mysqli_error($link));
                    // clean out expired sessions
                    $result = mysqli_query($link, "SELECT * FROM `sessions` WHERE `id` = '$sessionid' AND `app` = '$secret'");
                    $num = mysqli_num_rows($result);
                    if ($num === 0)
                    {
                        die("no active session");
                    }
                    $row = mysqli_fetch_array($result);
                    return array(
                        "credential" => $row["credential"],
                        "enckey" => $row["enckey"],
                        "validated" => $row["validated"]
                    );
                }
?>