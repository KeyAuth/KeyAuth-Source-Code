<?php
include '../../../includes/connection.php';
include '../../../includes/misc/autoload.phtml';
include '../../../includes/dashboard/autoload.phtml';
dashboard\primary\head();
?>
            <!-- ============================================================== -->

            <div class="container-fluid" id="content" style="display:none;">

                <!-- ============================================================== -->

                <!-- Start Page Content -->

                <!-- ============================================================== -->

                <!-- File export -->

                <div class="row">

                    <div class="col-12">

					<?php dashboard\primary\heador(); ?>

					<?php if ($_SESSION['timeleft'])
{ ?>

					<div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>

					<?php
} ?>

					<form method="POST">

					<button data-toggle="modal" type="button" id="modal" data-target="#create-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create User</button>  <button data-toggle="modal" type="button" id="modal" data-target="#set-user-var" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Set Variable</button>  <button data-toggle="modal" type="button" data-target="#import-users" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Import users</button>  <button data-toggle="modal" type="button" data-target="#extend-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Extend User(s)</button>  <button name="delusers" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete all users?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Users</button>  <button name="delexpusers" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete expired users?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete Expired Users</button>  <button name="resetall" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to reset HWID for all users?')"><i class="fas fa-redo-alt fa-sm text-white-50"></i> HWID Reset All Users</button>

                            </form>

							<br>

							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=oLj04x0k1RI" target="tutorial">https://youtube.com/watch?v=oLj04x0k1RI</a> You may also join Discord and ask for help!

                                        </div>

<div id="create-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Add User</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

                                                    <div class="form-group">

                                                        <label for="recipient-name" class="control-label">Username:</label>

                                                        <input type="text" class="form-control" name="username" placeholder="Username for user" required>

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Password:</label>

                                                        <input type="password" class="form-control" name="password" placeholder="leave blank if you want it to set to first password used to login">

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Subscription: </label>

                                                        <select name="sub" class="form-control">

														<?php
($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`name`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option>" . $row["name"] . "</option>";
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

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="adduser">Add</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

									<div id="set-user-var" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Set Variable</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

                                                    <div class="form-group">

                                                        <label for="recipient-name" class="control-label">User:</label>

                                                        <select name="user" class="form-control"><option value="all">All</option>

														<?php
($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`username`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "<option value=\"" . $row["username"] . "\">" . $row["username"] . "</option>";
    }
}
?>

														</select>

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Variable:</label>

														<input type="text" class="form-control" name="var" placeholder="Variable name (enter one if creating new one)" list="vars" required>

                                                        <datalist id="vars">

														<?php
($result = mysqli_query($link, "SELECT * FROM `uservars` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option>" . $row["name"] . "</option>";
    }
}
?>

														</datalist>

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Variable Data: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Assigns variable to selected user(s) which you can get and set from loader"></i></label>

                                                        <input type="text" class="form-control" name="data" placeholder="User variable data" required>

                                                    </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="setvar">Add</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

									<div id="import-users" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Import Users</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

                                                    <div class="form-group">

                                                        <label for="recipient-name" class="control-label">Users: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="No password is imported since passwords could be hashed in different formats or inaccessible to you when trying to export your users from another service. KeyAuth will use the password the user first signs in with."></i></label>

                                                        <input class="form-control" name="users" placeholder="Format: username,hwid,days|username,hwid,days">

                                                    </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="importusers">Add</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

									<div id="extend-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Extend User(s)</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

                                                    <div class="form-group">

                                                        <label for="recipient-name" class="control-label">User:</label>

                                                        <select name="user" class="form-control"><option value="all">All</option>

														<?php
($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`username`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "<option value=\"" . $row["username"] . "\">" . $row["username"] . "</option>";
    }
}
?>

														</select>

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Subscription:</label>

                                                        <select name="sub" class="form-control">

														<?php
($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY CHAR_LENGTH(`name`) DESC")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option>" . $row["name"] . "</option>";
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

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="extenduser">Add</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

										<div id="rename-app" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Rename Application</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

                                                    <div class="form-group">

                                                        <label for="recipient-name" class="control-label">Name:</label>

                                                        <input type="text" class="form-control" name="name" placeholder="New Application Name">

                                                    </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="renameapp">Add</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>



<script type="text/javascript">



var myLink = document.getElementById('mylink');



myLink.onclick = function(){





$(document).ready(function(){

        $("#content").fadeOut(100);

        $("#changeapp").fadeIn(1900);

        }); 



}





</script>

<div id="ban-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Ban User</h4>

                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

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

                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="banuser">Ban</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

                        <div class="card">

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table id="file_export" class="table table-striped table-bordered display">

                                        <thead>

                                            <tr>

<th>Username</th>

<th>HWID</th>

<th>IP</th>

<th>Creation Date</th>

<th>Last Login Date</th>

<th>Banned</th>

<th>Action</th>

                                            </tr>

                                        </thead>

                                        <tbody>

<?php
if ($_SESSION['app'])
{
    ($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
    $rows = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $rows[] = $r;
    }
    foreach ($rows as $row)
    {
        $user = $row['username'];
?>



                                                    <tr>



                                                    <td><?php echo $row["username"]; ?></td>



                                                    <td><?php echo $row["hwid"] ?? "N/A"; ?></td>

													

                                                    <td><?php echo $row["ip"] ?? "N/A"; ?></td>

													

                                                    <td><script>document.write(convertTimestamp(<?php echo $row["createdate"]; ?>));</script></td>

													

                                                    <td><script>document.write(convertTimestamp(<?php echo $row["lastlogin"]; ?>));</script></td>

													

                                                    <td><?php echo $row["banned"] ?? "N/A"; ?></td>



                                                    <td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                                Manage

                                            </button>

                                            <div class="dropdown-menu">

                                                <form method="post"><button class="dropdown-item" name="deleteuser" value="<?php echo $user; ?>">Delete</button>

												<button class="dropdown-item" name="resetuser" value="<?php echo $user; ?>">Reset HWID</button>

                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-user" onclick="banuser('<?php echo $user; ?>')">Ban</a>

                                                <button class="dropdown-item" name="unbanuser" value="<?php echo $user; ?>">Unban</button>

                                                <div class="dropdown-divider"></div>

												<button class="dropdown-item" name="edituser" value="<?php echo $user; ?>">Edit</button></div></td></tr></form>

<?php
    }
}
?>

                                        </tbody>

                                        <tfoot>

                                            <tr>

<th>Username</th>

<th>HWID</th>

<th>IP</th>

<th>Creation Date</th>

<th>Last Login Date</th>

<th>Banned</th>

<th>Action</th>

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Show / hide columns dynamically -->

                

                <!-- Column rendering -->

                

                <!-- Row grouping -->

                

                <!-- Multiple table control element -->

                

                <!-- DOM / jQuery events -->

                

                <!-- Complex headers with column visibility -->

                

                <!-- language file -->

                

                <!-- Setting defaults -->

                

                <!-- Footer callback -->

                

                <?php
if (isset($_POST['deleteuser']))
{
    $resp = misc\user\deleteSingular($_POST['deleteuser']);
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
	$resp = misc\user\resetSingular($_POST['resetuser']);
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
	$resp = misc\user\setVariable($_POST['user'], $_POST['var'], $_POST['data']);
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
	$resp = misc\user\ban($_POST['un'], $_POST['reason']);
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
    $resp = misc\user\unban($_POST['unbanuser']);
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
if (isset($_POST['edituser']))
{
    $un = misc\etc\sanitize($_POST['edituser']);
    $result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
    if (mysqli_num_rows($result) == 0)
    {
        mysqli_close($link);
        dashboard\primary\error("User not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($result);
?>

					<div id="edit-user" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true"o ydo >

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

												<h4 class="modal-title">Edit User</h4>

                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                                            </div>

                                            <div class="modal-body">

                                                <form method="post">

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Username:</label>

                                                        <input class="form-control" name="username" placeholder="Set new username">

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Password:</label>

                                                        <input type="password" class="form-control" name="pass" placeholder="Set new password, we cannot read old password because it's hashed with BCrypt">

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">Active Subscriptions: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="List of non-expired, non-paused subscriptions. Change selection if you want to delete one of them."></i></label>

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
        $value = "[" . $subrow['subscription'] . "]" . " - Expires: " . date('jS F Y h:i:s A (T)', $subrow["expiry"]);
?>

														<option><?php echo $value; ?></option>

														<?php
    }
?>

														</select>

                                                    </div>

													<div class="form-group">

                                                        <label for="recipient-name" class="control-label">User Variables: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="List of variables assigned to this user. Change selection if you want to delete one of them."></i></label>

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

                                                        <input type="text" class="form-control" name="hwid" placeholder="Enter HWID if you want this key to support multiple computers">

                                                    </div>

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

                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                                <button class="btn btn-warning waves-effect waves-light" value="<?php echo $un; ?>" name="deletesub">Delete Subscription</button>

                                                <button class="btn btn-primary waves-effect waves-light" value="<?php echo $un; ?>" name="deletevar">Delete Variable</button>

                                                <button class="btn btn-danger waves-effect waves-light" value="<?php echo $un; ?>" name="saveuser">Save</button>

												</form>

                                            </div>

                                        </div>

                                    </div>

									</div>

									<?php
}
if (isset($_POST['saveuser']))
{
    $un = misc\etc\sanitize($_POST['saveuser']);
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
    }
    if (isset($username) && trim($username) != '')
    {
        mysqli_query($link, "UPDATE `users` SET `username` = '$username' WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
        mysqli_query($link, "UPDATE `subs` SET `user` = '$username' WHERE `user` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
    }
    if (isset($pass) && trim($pass) != '')
    {
        mysqli_query($link, "UPDATE `users` SET `password` = '" . password_hash($pass, PASSWORD_BCRYPT) . "' WHERE `username` = '$un' AND `app` = '" . $_SESSION['app'] . "'");
    }
    dashboard\primary\success("Successfully Updated User");
    echo "<meta http-equiv='Refresh' Content='2'>";
}
if (isset($_POST['deletevar']))
{
	$resp = misc\user\deleteVar($_POST['deletevar'], $_POST['var']);
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
    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    $sub = get_string_between($sub, '[', ']');
	
	$resp = misc\user\deleteSub($_POST['deletesub'], $sub);
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
        $expiry = $third * 86400;
        mysqli_query($link, "INSERT INTO `users` (`username`, `hwid`, `app`,`owner`, `createdate`) VALUES ('$first','$second','" . $_SESSION['app'] . "','" . $_SESSION['username'] . "','" . time() . "')");
		mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$first','default','$expiry','" . $_SESSION['app'] . "')");
    }
    dashboard\primary\success("Successfully imported users!");
    echo "<meta http-equiv='Refresh' Content='3'>";
}
if (isset($_POST['extenduser']))
{	
	$resp = misc\user\extend($_POST['user'], $_POST['sub'], strtotime($_POST['expiry']));
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
if (isset($_POST['adduser']))
{
	$resp = misc\user\add($_POST['username'], $_POST['sub'], strtotime($_POST['expiry']), NULL, $_POST['password']);
    switch ($resp)
    {
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
?>

                

                <!-- ============================================================== -->

                <!-- End PAge Content -->

                <!-- ============================================================== -->

                <!-- ============================================================== -->

                <!-- Right sidebar -->

                <!-- ============================================================== -->

                <!-- .right-sidebar -->

                <!-- ============================================================== -->

                <!-- End Right sidebar -->

                <!-- ============================================================== -->

            </div>

            <!-- ============================================================== -->

            <!-- End Container fluid  -->

            <!-- ============================================================== -->

            <!-- ============================================================== -->

            <!-- footer -->

            <!-- ============================================================== -->

            <footer class="footer text-center">

       Copyright &copy; <script>document.write(new Date().getFullYear())</script> KeyAuth

</footer>

            <!-- ============================================================== -->

            <!-- End footer -->

            <!-- ============================================================== -->

        </div>

        <!-- ============================================================== -->

        <!-- End Page wrapper  -->

        <!-- ============================================================== -->

    </div>

    <!-- ============================================================== -->

    <!-- End Wrapper -->

    <!-- ============================================================== -->

    <!-- ============================================================== -->

    

   

    <!-- ============================================================== -->

    <!-- All Jquery -->

    <!-- ============================================================== -->

    

    <!-- Bootstrap tether Core JavaScript -->

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/popper-js/dist/umd/popper.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- apps -->

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.init.dark.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app-style-switcher.js"></script>

    <!-- slimscrollbar scrollbar JavaScript -->

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/sparkline/sparkline.js"></script>

    <!--Wave Effects -->

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/waves.js"></script>

    <!--Menu sidebar -->

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/sidebarmenu.js"></script>

    <!--Custom JavaScript -->

   <script src="https://cdn.keyauth.uk/dashboard/dist/js/feather.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/custom.min.js"></script>

    <!--This page JavaScript -->

    <!--chartis chart-->

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>

    <!--c3 charts -->

    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/d3.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.js"></script>

    <!--chartjs -->

    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chart-js/dist/chart.min.js"></script>

    <script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/dashboards/dashboard1.js"></script>

		<script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>

	    <!-- start - This is for export functionality only -->

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

  

					



<script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/datatable/datatable-advanced.init.js"></script>



<script>

                        

		function banuser(username) {

		 var banuser = $('.banuser');

		 banuser.attr('value', username);

      }

                    </script>

</body>

</html>
