<?php
if ($_SESSION['role'] == "Reseller")
{
    header("location: ./?page=reseller-licenses");
	die();
}
if($role == "Manager" && !($permissions & 2)) {
	die('You weren\'t granted permissions to view this page.');
}
if(!isset($_SESSION['app'])) {
	die("Application not selected.");
}
if (isset($_POST['saveuser']))
{
    $un = misc\etc\sanitize(urldecode($_POST['saveuser']));
    $username = misc\etc\sanitize($_POST['username']);
    $hwid = misc\etc\sanitize($_POST['hwid']);
    $pass = misc\etc\sanitize($_POST['pass']);
    if (isset($hwid) && trim($hwid) != '')
    {
        $result = mysqli_query($link, "SELECT `hwid` FROM `users` WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
        $row = mysqli_fetch_array($result);
        $hwidd = $row["hwid"];
        $hwidd = $hwidd .= $hwid;
        mysqli_query($link, "UPDATE `users` SET `hwid` = '$hwidd' WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
		if(mysqli_affected_rows($link)) {
			dashboard\primary\success("Successfully updated user!");
			misc\cache\purge('KeyAuthUser:'.$_SESSION['app'].':'.$un);
		}
		else {
			dashboard\primary\error("Failed to update user!");
		}
    }
    if (isset($username) && trim($username) != '')
    {
        $resp = misc\user\changeUsername($un, $username, $_SESSION['app']);
		switch($resp) {
			case 'already_used':
				dashboard\primary\error("Username already used!");
				break;
			case 'failure':
				dashboard\primary\error("Failed to change username!");
				break;
			case 'success':
				dashboard\primary\success("Successfully changed username!");
				break;
			default:
				dashboard\primary\error("Unhandled Error! Contact us if you need help");
				break;
		}
    }
    if (isset($pass) && trim($pass) != '')
    {
        $resp = misc\user\changePassword($un, $pass, $_SESSION['app']);
		switch($resp) {
			case 'failure':
				dashboard\primary\error("Failed to change password!");
				break;
			case 'success':
				dashboard\primary\success("Successfully changed password!");
				break;
			default:
				dashboard\primary\error("Unhandled Error! Contact us if you need help");
				break;
		}
    }
}
if (isset($_POST['deletevar']))
{
	$resp = misc\user\deleteVar(urldecode($_POST['deletevar']), $_POST['var']);
    switch ($resp)
    {
        case 'failure':
			dashboard\primary\error("Failed to delete variable!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted variable!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['deletesub']))
{
    $sub = misc\etc\sanitize($_POST['sub']);
	
	$resp = misc\user\deleteSub(urldecode($_POST['deletesub']), $sub);
    switch ($resp)
    {
        case 'failure':
			dashboard\primary\error("Failed to delete subscription!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted subscription!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['importusers']))
{
	if(!empty($_POST['authgg'])) {
		$json = $_POST['authgg'];
		$data = json_decode($json);

		foreach($data as $key => $row) {
			$email = misc\etc\sanitize($row->email);
			if(strpos($email, '@') !== false) { // ensure the email field is an actual email address
				$email = sha1(strtolower($email));
			}
			else {
				$email = NULL;
			}
			
			$lastlogin = strtotime(misc\etc\sanitize($row->lastlogin));
			if($lastlogin < 0) { // check if user has ever logged in, if not set value to NULL
				$lastlogin = NULL;
			}
			$username = misc\etc\sanitize($row->username);
			$hwid = misc\etc\sanitize($row->hwid);
			$lastip = misc\etc\sanitize($row->lastip);
			
			if($hwid == "NO HWID, SIGNED UP WITH PHP") { // set hwid to NULL if there's no HWID set
				$hwid = NULL; 
			}
			
			mysqli_query($link, "INSERT INTO `users`(`username`, `email`, `hwid`, `app`, `createdate`, `lastlogin`, `ip`) VALUES ('$username',NULLIF('$email', ''), NULLIF('$hwid', ''),'" . $_SESSION['app'] . "', UNIX_TIMESTAMP(),NULLIF('$lastlogin', ''), NULLIF('$lastip', ''))");
			
			$expiry = strtotime(misc\etc\sanitize($row->expiry_date));
			if($expiry > time()) { // check if user's subscription is still active or expired.
				$rank = misc\etc\sanitize($row->rank) + 1;
				mysqli_query($link, "INSERT INTO `subs`(`user`, `subscription`, `expiry`, `app`, `key`) VALUES ('$username','rank ". $rank ."', '$expiry','" . $_SESSION['app'] . "', '$username')");
			}
			
			if(!empty($row->variable)) { // check if user has a variable assigned to it. KeyAuth is superior and allows multiple user variables.
				$variable = misc\etc\sanitize($row->variable);
				mysqli_query($link, "INSERT INTO `uservars`(`name`, `data`, `user`, `app`) VALUES ('main','$variable', '$username', '" . $_SESSION['app'] . "')");
			}
		}
	}
	else {
		$users = misc\etc\sanitize($_POST['users']);
		$text = explode("|", $users);
		str_replace('"', "", $text);
		str_replace("'", "", $text);
		foreach ($text as $line)
		{
			$array = explode(',', $line);
			$first = $array[0];
			if (!isset($first) || $first == '')
			{
				dashboard\primary\error("Invalid Format!");
				echo "<meta http-equiv='Refresh' Content='2;'>";
				return;
			}
			$second = $array[1];
			if (!isset($second) || $second == '')
			{
				dashboard\primary\error("Invalid Format!");
				echo "<meta http-equiv='Refresh' Content='2;'>";
				return;
			}
			$third = $array[2];
			if (!isset($third) || $third == '')
			{
				dashboard\primary\error("Invalid Format!");
				echo "<meta http-equiv='Refresh' Content='2;'>";
				return;
			}
			$expiry = ($third * 86400) + time();
			mysqli_query($link, "INSERT INTO `users` (`username`, `hwid`, `app`,`owner`, `createdate`) VALUES ('$first','$second','" . $_SESSION['app'] . "','" . $_SESSION['username'] . "','" . time() . "')");
			mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$first','default','$expiry','" . $_SESSION['app'] . "')");
		}
	}
    dashboard\primary\success("Successfully imported users!");
}
if (isset($_POST['extenduser']))
{	
	$activeOnly = ($_POST['activeOnly'] == "on") ? 1 : 0;
	$expiry = time() + $_POST['time'] * $_POST['expiry'];
	$resp = misc\user\extend(urldecode($_POST['user']), $_POST['sub'], $expiry, $activeOnly);
    switch ($resp)
    {
		case 'missing':
			dashboard\primary\error("User(s) not found!");
			break;
		case 'sub_missing':
			dashboard\primary\error("Subscription not found!");
			break;
		case 'date_past':
			dashboard\primary\error("Subscription expiry must be set in the future!");
			break;
		case 'failure':
			dashboard\primary\error("Failed to extend user(s)!");
			break;
		case 'success':
			dashboard\primary\success("Successfully extended user(s)!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['subtractuser']))
{	
	$expiry = $_POST['time'] * $_POST['expiry'];
	$resp = misc\user\subtract(urldecode($_POST['user']), $_POST['sub'], $expiry, $secret);
	switch ($resp) {
		case 'invalid_seconds':
			dashboard\primary\error("Seconds specified must be greater than zero.");
			break;
		case 'failure':
			dashboard\primary\error("Failed to substract from subscription!");
			break;
		case 'success':
			dashboard\primary\success("Successfully subtracted time from subscription.");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['adduser']))
{
	$resp = misc\user\add(urldecode($_POST['username']), $_POST['sub'], strtotime($_POST['expiry']), NULL, $_POST['password']);
    switch ($resp)
    {
		case 'already_exist':
			dashboard\primary\error("Username already exists!");
			break;
		case 'sub_missing':
			dashboard\primary\error("Subscription not found!");
			break;
		case 'date_past':
			dashboard\primary\error("Subscription expiry must be set in the future!");
			break;
        case 'failure':
			dashboard\primary\error("Failed to create user!");
			break;
		case 'success':
			dashboard\primary\success("Successfully created user!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['delexpusers']))
{
	$resp = misc\user\deleteExpiredUsers();
    switch ($resp)
    {
		case 'missing':
			dashboard\primary\error("You have no users!");
			break;
		case 'failure':
			dashboard\primary\error("No users are expired!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted expired users!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['delusers']))
{
	$resp = misc\user\deleteAll();
    switch ($resp)
    {
		case 'failure':
			dashboard\primary\error("Failed to delete all users!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted all users!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['resetall']))
{
	$resp = misc\user\resetAll();
    switch ($resp)
    {
		case 'failure':
			dashboard\primary\error("Failed to reset all users!");
			break;
		case 'success':
			dashboard\primary\success("Successfully reset all users!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}

if (isset($_POST['deleteuser']))
{
    $resp = misc\user\deleteSingular(urldecode($_POST['deleteuser']));
    switch ($resp)
    {
        case 'failure':
			dashboard\primary\error("Failed to delete user!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted user!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['resetuser']))
{
	$resp = misc\user\resetSingular(urldecode($_POST['resetuser']));
    switch ($resp)
    {
        case 'failure':
			dashboard\primary\error("Failed to reset user!");
			break;
		case 'success':
			dashboard\primary\success("Successfully reset user!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['setvar']))
{
	$resp = misc\user\setVariable(urldecode($_POST['user']), $_POST['var'], $_POST['data']);
    switch ($resp)
    {
		case 'missing':
			dashboard\primary\error("No users found!");
			break;
        case 'failure':
			dashboard\primary\error("Failed to set variable!");
			break;
		case 'success':
			dashboard\primary\success("Successfully set variable!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['banuser']))
{
	$resp = misc\user\ban(urldecode($_POST['un']), $_POST['reason']);
    switch ($resp)
    {
		case 'missing':
			dashboard\primary\error("User not found!");
			break;
        case 'failure':
			dashboard\primary\error("Failed to ban user!");
			break;
		case 'success':
			dashboard\primary\success("Successfully banned user!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['unbanuser']))
{
    $resp = misc\user\unban(urldecode($_POST['unbanuser']));
    switch ($resp)
    {
		case 'missing':
			dashboard\primary\error("User not found!");
			break;
        case 'failure':
			dashboard\primary\error("Failed to unban user!");
			break;
		case 'success':
			dashboard\primary\success("Successfully unbanned user!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['pauseuser']))
{
	$user = misc\etc\sanitize(urldecode($_POST['pauseuser']));
	$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `expiry` > '" . time() . "' AND `user` = '$user'");
    while ($row = mysqli_fetch_array($result)) {
        $expires = $row['expiry'];
        $exp = $expires - time();
        mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");
    }
    if (mysqli_affected_rows($link) > 0) {
		misc\cache\purge('KeyAuthSubs:'.$_SESSION['app'].':'.$user);
        dashboard\primary\success("Successfully paused user", $format);
    } else {
        dashboard\primary\error("Failed to pause user", $format);
    }
}
if (isset($_POST['unpauseuser']))
{
	$user = misc\etc\sanitize(urldecode($_POST['unpauseuser']));
	$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `user` = '$user' AND `paused` = 1");
    while ($row = mysqli_fetch_array($result)) {
        $expires = $row['expiry'];
        $exp = $expires + time();
        mysqli_query($link, "UPDATE `subs` SET `paused` = 0, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");
    }
    if (mysqli_affected_rows($link) > 0) {
		misc\cache\purge('KeyAuthSubs:'.$_SESSION['app'].':'.$user);
        dashboard\primary\success("Successfully unpaused user", $format);
    } else {
        dashboard\primary\error("Failed to unpause user", $format);
    }
}
?>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>
    <form method="POST">
        <button data-bs-toggle="modal" type="button" id="modal" data-bs-target="#create-user"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Create User</button>
        <button data-bs-toggle="modal" type="button" id="modal" data-bs-target="#set-user-var"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Set Variable</button>
        <button data-bs-toggle="modal" type="button" data-bs-target="#import-users"
            class="dt-button buttons-print btn btn-primary mr-1"><i
                class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Import users</button><br><br>
        <button data-bs-toggle="modal" type="button" data-bs-target="#extend-user"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Extend
            User(s)</button>
		<button data-bs-toggle="modal" type="button" data-bs-target="#subtract-user"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Subtract
            User(s)</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#delete-allusers"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i>
            Delete All Users</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#delete-allexpired"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i>
            Delete Expired Users</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#reset-allusers"
            class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-redo-alt fa-sm text-white-50"></i>
            HWID Reset All Users</button>
    </form>

    <div id="create-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Add User</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Username:</label>

                            <input type="text" class="form-control" name="username" placeholder="Username for user"
                                required>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Password:</label>

                            <input type="password" class="form-control" name="password"
                                placeholder="leave blank if you want it to set to first password used to login">

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Subscription: </label>

                            <select name="sub" class="form-control">

                                <?php
($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY `level` ASC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
		echo "  <option value=\"".$row["name"]."\">" . $row["name"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Subscription Expiry:</label>

                            <?php
echo '<input class="form-control" type="datetime-local" name="expiry" value="' . date("Y-m-d\TH:i", time()) . '" required>';
?>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="adduser">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="set-user-var" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Set Variable</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">User:</label>

                            <select name="user" class="form-control">
                                <option value="all">All</option>

                                <?php
($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`username`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "<option value=\"" . urlencode($row["username"]) . "\">" . $row["username"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Variable:</label>

                            <input type="text" class="form-control" name="var"
                                placeholder="Variable name (enter one if creating new one)" list="vars" required>

                            <datalist id="vars">

                                <?php
($result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option value=\"".$row["name"]."\">" . $row["name"] . "</option>";
    }
}
?>

                            </datalist>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Variable Data: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip"
                                    data-placement="top"
                                    title="Assigns variable to selected user(s) which you can get and set from loader"></i></label>

                            <input type="text" class="form-control" name="data" placeholder="User variable data"
                                required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="setvar">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="import-users" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Import Users</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Users: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip"
                                    data-placement="top"
                                    title="No password is imported since passwords could be hashed in different formats or inaccessible to you when trying to export your users from another service. KeyAuth will use the password the user first signs in with."></i></label>

                            <input class="form-control" name="users"
                                placeholder="Format: username,hwid,days|username,hwid,days">

                        </div>
						
						<div class="form-group">

                            <label for="recipient-name" class="control-label">Import from auth.gg:</label>

                            <input class="form-control" name="authgg"
                                placeholder="Paste in JSON from developers.auth.gg">

                        </div>
						
						

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger waves-effect waves-light" name="importusers">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="extend-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Extend User(s)</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">User:</label>

                            <select name="user" class="form-control">
                                <option value="all">All</option>

                                <?php
($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`username`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "<option value=\"" . urlencode($row["username"]) . "\">" . $row["username"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Subscription:</label>

                            <select name="sub" class="form-control">

                                <?php
($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY `level` ASC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option value=\"".$row["name"]."\">" . $row["name"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Unit Of Time To Add:</label>
                            <select name="expiry" class="form-control">
                                <option value="86400">Days</option>
                                <option value="60">Minutes</option>
                                <option value="3600">Hours</option>
                                <option value="1">Seconds</option>
                                <option value="604800">Weeks</option>
                                <option value="2629743">Months</option>
                                <option value="31556926">Years</option>
                                <option value="315569260">Lifetime</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Time To Add:</label>
                            <input class="form-control" name="time" placeholder="Multiplied by selected unit of time">
                        </div>
						<br>
						<input class="form-check-input" style="color:white;border-color:white;" name="activeOnly"
                            type="checkbox" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                            Active users only <i class="fas fa-question-circle fa-lg text-white-50"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Extend only users who have an active subscription of the exact subscription you're extending"></i>
                        </label>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger waves-effect waves-light" name="extenduser">Extend</button>

                    </form>

                </div>

            </div>

        </div>

    </div>
	
	<div id="subtract-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Subtract User(s)</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">User:</label>

                            <select name="user" class="form-control">
								<option value="all">All</option>
							
                                <?php
($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`username`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "<option value=\"" . urlencode($row["username"]) . "\">" . $row["username"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Subscription:</label>

                            <select name="sub" class="form-control">

                                <?php
($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY `level` ASC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option value=\"".$row["name"]."\">" . $row["name"] . "</option>";
    }
}
?>

                            </select>

                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Unit Of Time To Subtract:</label>
                            <select name="expiry" class="form-control">
                                <option value="86400">Days</option>
                                <option value="60">Minutes</option>
                                <option value="3600">Hours</option>
                                <option value="1">Seconds</option>
                                <option value="604800">Weeks</option>
                                <option value="2629743">Months</option>
                                <option value="31556926">Years</option>
                                <option value="315569260">Lifetime</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Time To Subtract:</label>
                            <input class="form-control" name="time" placeholder="Multiplied by selected unit of time">
                        </div>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger waves-effect waves-light" name="subtractuser">Subtract</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="rename-app" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Rename Application</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Name:</label>

                            <input type="text" class="form-control" name="name" placeholder="New Application Name">

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger waves-effect waves-light" name="renameapp">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="ban-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Ban User</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Ban reason:</label>

                            <input type="text" class="form-control" name="reason" placeholder="Reason for ban" required>

                            <input type="hidden" class="banuser" name="un">

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="banuser">Ban</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <!--begin::Modal - Delete all users-->
    <div class="modal fade" tabindex="-1" id="delete-allusers">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Users</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to delete all users? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="delusers" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Delete all users-->
    <!--begin::Modal - Delete all expired users-->
    <div class="modal fade" tabindex="-1" id="delete-allexpired">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Expired Users</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to delete all expired users? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="delexpusers" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Delete all expired users-->

    <!--begin::Modal - HWID reset all users-->
    <div class="modal fade" tabindex="-1" id="reset-allusers">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">HWID Reset All Users</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to hwid reset all users? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="resetall" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - HWID reset all users-->

    <table id="kt_datatable_users" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Username</th>
                <th>HWID</th>
                <th>IP</th>
                <th>Creation Date</th>
                <th>Last Login Date</th>
                <th>Banned</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>





    <?php

if (isset($_POST['edituser']))
{
$un = misc\etc\sanitize(urldecode($_POST['edituser']));
$result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
if (mysqli_num_rows($result) == 0)
{
mysqli_close($link);
error("User not Found!");
echo "<meta http-equiv='Refresh' Content='2'>";
return;
}
$row = mysqli_fetch_array($result);
?>

    <div id="edit-user" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        style="display: block;" aria-modal="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Edit User</h4>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary"
                        onClick="window.location.href=window.location.href">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->

                </div>

                <div class="modal-body">

                    <form method="post">

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Username:</label>

                            <input class="form-control" name="username" placeholder="Set new username">

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Password:</label>

                            <input type="password" class="form-control" name="pass"
                                placeholder="Set new password, we cannot read old password because it's hashed with BCrypt">

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Active Subscriptions: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="List of non-expired, non-paused subscriptions. Change selection if you want to delete one of them."></i></label>

                            <select class="form-control" name="sub">

                                <?php
$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `user` = '$un' AND `app` = '" . $_SESSION['app'] . "' AND `expiry` > '" . time() . "'");
$rows = array();
while ($r = mysqli_fetch_assoc($result))
{
$rows[] = $r;
}
foreach ($rows as $subrow)
{
$sub = $subrow['subscription'];
$value = "[" . $subrow['subscription'] . "] - Expires: <script>document.write(convertTimestamp(" . $subrow["expiry"] . "));</script>";
?>

                                <option value="<?php echo $sub; ?>"><?php echo $value; ?></option>

                                <?php
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">User Variables: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="List of variables assigned to this user. Change selection if you want to delete one of them."></i></label>

                            <select class="form-control" name="var">

                                <?php
$result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `user` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
$rows = array();
while ($r = mysqli_fetch_assoc($result))
{
$rows[] = $r;
}
foreach ($rows as $varrow)
{
$value = $varrow['name'] . " : " . $varrow["data"];
?>

                                <option value="<?php echo $varrow['name']; ?>"><?php echo $value; ?></option>

                                <?php
}
?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Additional HWID:</label>

                            <input type="text" class="form-control" name="hwid"
                                placeholder="Enter HWID if you want this key to support multiple computers">

                        </div>
                        <br>
                        <div class="form-group">

                            <label for="recipient-name" class="control-label">HWID:</label>

                            <p><?php echo $row['hwid'] ?? "N/A"; ?></p>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">IP:</label>

                            <p><?php echo $row['ip'] ?? "N/A"; ?></p>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" onClick="window.location.href=window.location.href" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>

                    <button class="btn btn-warning waves-effect waves-light" value="<?php echo urlencode($un); ?>"
                        name="deletesub">Delete Subscription</button>

                    <button class="btn btn-primary waves-effect waves-light" value="<?php echo urlencode($un); ?>"
                        name="deletevar">Delete Variable</button>

                    <button class="btn btn-danger waves-effect waves-light" value="<?php echo urlencode($un); ?>"
                        name="saveuser">Save</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <?php
}
?>
    <script>
    function banuser(username) {

        var banuser = $('.banuser');

        banuser.attr('value', username);

    }
    </script>
</div>
<!--end::Container-->