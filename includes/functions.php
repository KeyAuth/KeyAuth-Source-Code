<?php
include 'connection.php'; // start MySQL connection

$role = $_SESSION['role']; // user role
$ip = fetchip(); // ip address
function vpn_check($ipaddr)
{
    $url = "https://proxycheck.io/v2/{$ipaddr}?key={$proxycheckapikey}?vpn=1";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result);
	if($json->$ipaddr->proxy == "yes")
	{
		return true;
	}

    return false;
}

function expire_check($username, $expires)
{
	global $link;
	
	if($expires < time())
	{
		$_SESSION['role'] = "tester";
		mysqli_query($link,"UPDATE `accounts` SET `role` = 'tester' WHERE `username` = '$username'");
	}

	if($expires - time() < 2629743) // account expires in month
	{
		return true;
	}
	else
	{
		return false;
	}
}

function wh_log($webhook_url, $msg, $un)
{
    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([
    // Message
    "content" => $msg,

    // Username
    "username" => "$un",

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_exec($ch);
    curl_close($ch);
}

function sanitize($input)
{
	if(empty($input) & !is_numeric($input))
	{
		return NULL;
	}
	
    global $link; // needed to refrence active MySQL connection
    return mysqli_real_escape_string($link, strip_tags(trim($input))); // return string with quotes escaped to prevent SQL injection, script tags stripped to prevent XSS attach, and trimmed to remove whitespace
    
}

function fetchip()
{
    return str_replace(",62.210.119.214", "",$_SERVER['HTTP_X_FORWARDED_FOR']) ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}

function heador()
{
	global $link; // needed to refrence active MySQL connection
    global $role; // needed to refrence user role
    if ($role != "Manager")
    {
        echo "<form class=\"text-left\" method=\"POST\">
        <div class=\"form-group row\">
                                        <label for=\"example-tel-input\" class=\"col-2 col-form-label\">Application selected: </label>
                                        <div class=\"col-10\">
                                            <label class=\"form-control\" style=\"height:auto;\">
                                            ".$_SESSION['name']."
                                        </label>
                                      </div>
                                    </div>

                                    <div class=\"form-group row\">
                                        <label for=\"example-tel-input\" class=\"col-2 col-form-label\">Application secret: </label>
                                        <div class=\"col-10\">
                                            <label class=\"form-control\" style=\"height:auto;\">
                                            <div class=\"secret\">".$_SESSION['secret']."</div>
                                        </label>
                                    </div>
                                    </div>
        
        <button data-toggle=\"modal\" type=\"button\" id=\"mylink\" class=\"dt-button buttons-print btn btn-primary mr-1\"> <i class=\"fas fa-plus-circle fa-sm text-white-50\"></i> Change / Create Application</button>
        <button data-toggle=\"modal\" type=\"button\" data-target=\"#rename-app\" class=\"dt-button buttons-print btn btn-info mr-1\"> <i class=\"fas fa-edit fa-sm text-white-50\"></i> Rename Application</button>";           
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
        $row = mysqli_fetch_array($result);
        if (!$row['paused'])
        {
            echo "<button name=\"pauseapp\" class=\"dt-button buttons-print btn btn-warning mr-1\" onclick=\"return confirm('Are you sure you want to pause app & all users?')\"> <i class=\"fas fa-clock fa-sm text-white-50\"></i> Pause App & Users</button>";
        }
        else
        {
            echo "<button name=\"unpauseapp\" class=\"dt-button buttons-print btn btn-warning mr-1\" onclick=\"return confirm('Are you sure you want to unpause app & all users?')\"> <i class=\"fas fa-clock fa-sm text-white-50\"></i> Unpause App & Users</button>";
        }
        echo "
        <button name=\"refreshapp\" class=\"dt-button buttons-print btn btn-success mr-1\" onclick=\"return confirm('Are you sure you want to reset application secret?')\"> <i class=\"fas fa-sync-alt fa-sm text-white-50\"></i> Refresh Application Secret</button>
        <button name=\"deleteapp\" class=\"dt-button buttons-print btn btn-danger mr-1\" onclick=\"return confirm('Are you sure you want to delete application?')\"> <i class=\"fas fa-trash-alt fa-sm text-white-50\"></i> Delete Application</button>
        </form>";

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


        if (isset($_POST['pauseapp']))
        {

            if ($role == "Manager")
            {
                error("Manager accounts aren\'t allowed To pause applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `expiry` > '".time()."'");
            while ($row = mysqli_fetch_array($result))
            {
                $expires = $row['expiry'];
                $exp = $expires - time();
                mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");
            }
			
            mysqli_query($link, "UPDATE `apps` SET `paused` = 1 WHERE `secret` = '" . $_SESSION['app'] . "'");
            success("Paused application and any active subscriptions!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }

        if (isset($_POST['unpauseapp']))
        {
			if ($role == "Manager")
            {
                error("Manager accounts aren\'t allowed To unpause applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
			
            $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `paused` = 1");
            while ($row = mysqli_fetch_array($result))
            {
                $expires = $row['expiry'];
                $exp = $expires + time();
                mysqli_query($link, "UPDATE `subs` SET `paused` = 0, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");

            }
            mysqli_query($link, "UPDATE `apps` SET `paused` = 0 WHERE `secret` = '" . $_SESSION['app'] . "'");
            success("Unpaused application and any paused subscriptions!");
            echo "<meta http-equiv='Refresh' Content='2'>";
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
            mysqli_query($link, "UPDATE `chatmsgs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `chats` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `sessions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `uservars` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `chatmutes` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
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
            $result = mysqli_query($link, "SELECT * FROM apps WHERE name='$appname' AND owner='" . $_SESSION['username'] . "'");
            if (mysqli_num_rows($result) > 0)
            {
                mysqli_close($link);
                error("You already own application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }

            $owner = $_SESSION['username'];

            if ($role == "tester")
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

            if ($role == "Manager")
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
			mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('default', '1', '$clientsecret')");
            mysqli_query($link, "INSERT INTO `apps`(`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES ('" . $owner . "','" . $appname . "','" . $clientsecret . "','$ownerid', '1','1','$sellerkey')");
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
	echo '<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Application</span></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/licenses/" aria-expanded="false"><i data-feather="key"></i><span class="hide-menu">Licenses</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/users/" aria-expanded="false"><i data-feather="users"></i><span class="hide-menu">Users</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/subscriptions/" aria-expanded="false"><i data-feather="bar-chart"></i><span class="hide-menu">Subscriptions</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/chats/" aria-expanded="false"><i data-feather="message-square"></i><span class="hide-menu">Chats</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/sessions/" aria-expanded="false"><i data-feather="clock"></i><span class="hide-menu">Sessions</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/webhooks/" aria-expanded="false"><i data-feather="server"></i><span class="hide-menu">Webhooks</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/files/" aria-expanded="false"><i data-feather="paperclip"></i><span class="hide-menu">Files</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/variables/" aria-expanded="false"><i data-feather="file-text"></i><span class="hide-menu">Variables</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/logs/" aria-expanded="false"><i data-feather="database"></i><span class="hide-menu">Logs</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/blacklists/" aria-expanded="false"><i data-feather="user-x"></i><span class="hide-menu">Blacklists</span></a></li>
	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>
	<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Account</span></li>';
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