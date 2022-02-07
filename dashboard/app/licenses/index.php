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
					<button data-toggle="modal" type="button" data-target="#create-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create keys</button>  <button data-toggle="modal" type="button" data-target="#import-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Import keys</button>  <button data-toggle="modal" type="button" data-target="#comp-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Add Time</button><br><br><button name="dlkeys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-download fa-sm text-white-50"></i> Download All keys</button>  <button name="delkeys" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete all keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All keys</button>  <button name="deleteallunused" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete all unused keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Unused Keys</button>  <button name="deleteallused" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete all used keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Used Keys</button>
                            </form>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=oLj04x0k1RI" target="tutorial">https://youtube.com/watch?v=oLj04x0k1RI</a> You may also join Discord and ask for help!</div>
							<?php
if (isset($_SESSION['keys_array']))
{
    $list = $_SESSION['keys_array'];
    $keys = NULL;
    for ($i = 0;$i < count($list);$i++)
    {
        $keys .= "" . $list[$i] . "<br>";
    }
    echo "<div class=\"card\"> <div class=\"card-body\"> $keys </div> </div>";
    unset($_SESSION['keys_array']);
}
?>
<div id="create-keys" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Licenses</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
											<?php
$list = $_SESSION['licensePresave'];
$format = $list[0];
$amt = $list[1];
$lvl = $list[2];
$note = $list[3];
$dur = $list[4];
?>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Amount:</label>
                                                        <input type="number" class="form-control" name="amount" placeholder="Default 1" value="<?php if (!is_null($amt))
{
    echo $amt;
} ?>">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Key Mask: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Format keys are in. You can do custom by putting whatever, or do capital X or lowercase X for random character"></i></label>
                                                        <input type="text" class="form-control" value="<?php if (!is_null($format))
{
    echo $format;
}
else
{
    echo "XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX";
} ?>" placeholder="Key Format. X is capital random char, x is lowercase" name="mask" required maxlength="49">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Level: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="This needs to coordinate to the level of subscription you want to give to user when they redeem license. If it's blank, go to subscriptions tab and create subscription"></i></label>
                                                        <select name="level" class="form-control">
														<?php
($result = mysqli_query($link, "SELECT DISTINCT `level` FROM `subscriptions` WHERE `app` = '" . $_SESSION['app'] . "' ORDER BY `level` ASC"));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
?>
																	<option <?=$lvl == $row["level"] ? ' selected="selected"' : ''; ?>><?php echo $row["level"]; ?></option>
																	<?php
    }
}
?>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Note:</label>
                                                        <input type="text" class="form-control" name="note" placeholder="Optional, e.g. this license was for Joe" value="<?php if (!is_null($note))
{
    echo $note;
} ?>">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Expiry Unit:</label>
                                                        <select name="expiry" class="form-control"><option value="86400">Days</option><option value="60">Minutes</option><option value="3600">Hours</option><option value="1">Seconds</option><option value="604800">Weeks</option><option value="2629743">Months</option><option value="31556926">Years</option><option value="315569260">Lifetime</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Duration: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="When the key is redeemed, a subscription with the duration of the key will be added to the user who redeemed the key."></i></label>
                                                        <input name="duration" type="number" class="form-control" placeholder="Multiplied by selected Expiry unit" value="<?php if (!is_null($dur))
{
    echo $dur;
} ?>" required>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="genkeys">Add</button>
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
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
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
                                                <button class="btn btn-danger waves-effect waves-light" name="renameapp">Rename</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
					
<div id="import-keys" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Import Licenses</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Keys: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Make sure you have a subscription created that matches each level of the keys you're importing."></i></label>
                                                        <input class="form-control" name="keys" placeholder="Format: KEYHERE,LVLHERE,DAYSHERE|KEYHERE,LVLHERE,DAYSHERE">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="importkeys">Import</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									
<div id="comp-keys" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Time</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Unit Of Time To Add:</label>
                                                        <select name="expiry" class="form-control"><option value="86400">Days</option><option value="60">Minutes</option><option value="3600">Hours</option><option value="1">Seconds</option><option value="604800">Weeks</option><option value="2629743">Months</option><option value="31556926">Years</option><option value="315569260">Lifetime</option></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Time To Add: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If the key is used, this will do nothing. Used keys are turned into users so if you want to add time to a user, go to users tab and click extend user(s)"></i></label>
                                                        <input class="form-control" name="time" placeholder="Multiplied by selected unit of time">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="addtime">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                    <?php
if (isset($_POST['genkeys']))
{
    $key = misc\license\createLicense($_POST['amount'], $_POST['mask'], $_POST['duration'], $_POST['level'], $_POST['note'], $_POST['expiry']);
    switch ($key)
    {
        case 'max_keys':
            dashboard\primary\error("You can only generate 100 licenses at a time");
        break;
        case 'tester_limit':
            dashboard\primary\error("Tester plan only allows for 50 licenses, please upgrade!");
        break;
        case 'dupe_custom_key':
            dashboard\primary\error("Can\'t do custom key with amount greater than one");
        break;
        default:
            mysqli_query($link, "UPDATE `accounts` SET `format` = '" . misc\etc\sanitize($_POST['mask']) . "',`amount` = '" . misc\etc\sanitize($_POST['amount']) . "',`lvl` = '" . misc\etc\sanitize($_POST['level']) . "',`note` = '" . misc\etc\sanitize($_POST['note']) . "',`duration` = '" . misc\etc\sanitize($_POST['duration']) . "' WHERE `username` = '" . $_SESSION['username'] . "'");
            if (misc\etc\sanitize($_POST['amount']) > 1)
            {
                $_SESSION['keys_array'] = $key;
                echo "<meta http-equiv='Refresh' Content='0;'>";
            }
            else
            {
                echo "<script>
navigator.clipboard.writeText('" . array_values($key) [0] . "');
</script>";
                dashboard\primary\success("License Created And Copied To Clipboard!");
            }
        break;
    }
}
if (isset($_POST['importkeys']))
{
    $keys = misc\etc\sanitize($_POST['keys']);
    $text = explode("|", $keys);
    str_replace('"', "", $text);
    str_replace("'", "", $text);
    foreach ($text as $line)
    {
        $array = explode(',', $line);
        $first = $array[0];
        if (!isset($first) || $first == '')
        {
            dashboard\primary\error("Invalid Format, please watch tutorial video!");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        $second = $array[1];
        if (!isset($second) || $second == '')
        {
            dashboard\primary\error("Invalid Format, please watch tutorial video!");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        $third = $array[2];
        if (!isset($third) || $third == '')
        {
            dashboard\primary\error("Invalid Format, please watch tutorial video!");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        $expiry = $third * 86400;
        mysqli_query($link, "INSERT INTO `keys` (`key`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$first','$expiry','Not Used','$second','" . $_SESSION['username'] . "','" . time() . "','" . $_SESSION['app'] . "')");
    }
    dashboard\primary\success("Successfully imported licenses!");
    echo "<meta http-equiv='Refresh' Content='3'>";
}
if (isset($_POST['addtime']))
{
	$resp = misc\license\addTime($_POST['time'], $_POST['expiry']);
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Failed to add time!");
			break;
		case 'success':
			dashboard\primary\success("Added time to unused licenses!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['dlkeys']))
{
    echo "<meta http-equiv='Refresh' Content='0; url=download.php'>";
    // get all rows, put in text file, download text file, delete text file.
    
}
if (isset($_POST['delkeys']))
{
	$resp = misc\license\deleteAll();
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Didn\'t find any keys!");
			break;
		case 'success':
			dashboard\primary\success("Deleted All Keys!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['deleteallunused']))
{
	$resp = misc\license\deleteAllUnused();
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Didn\'t find any unused keys!");
			break;
		case 'success':
			dashboard\primary\success("Deleted All Unused Keys!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['deleteallused']))
{
	$resp = misc\license\deleteAllUsed();
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Didn\'t find any used keys!");
			break;
		case 'success':
			dashboard\primary\success("Deleted All Used Keys!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
?>

<script type="text/javascript">

var myLink = document.getElementById('mylink');

myLink.onclick = function(){


$(document).ready(function(){
        $("#content").fadeOut(100);
        $("#changeapp").fadeIn(1900);
        }); 

}


</script>
<div id="ban-key" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Ban License</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Ban reason:</label>
                                                        <input type="text" class="form-control" name="reason" placeholder="Reason for ban" required>
														<input type="hidden" class="bankey" name="key">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="bankey">Ban</button>
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
<th>Key</th>
<th>Creation Date</th>
<th>Generated By</th>
<th>Duration</th>
<th>Note</th>
<th>Used On</th>
<th>Used By</th>
<th>Status</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

<?php
if ($_SESSION['app'])
{
    $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '" . $_SESSION['app'] . "'");
    $rows = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $rows[] = $r;
    }
    foreach ($rows as $row)
    {
        $key = $row['key'];
        $badge = $row['status'] == "Not Used" ? 'badge badge-success' : 'badge badge-danger';
?>

													<tr>

                                                    <td><?php echo $key; ?></td>
													
													<td><script>document.write(convertTimestamp(<?php echo $row["gendate"]; ?>));</script></td>

                                                    <td><?php echo $row["genby"]; ?></td>
                                                    
                                                    <td><?php echo $row["expires"] / 86400 ?> Day(s)</td>
                                                    <td><?php echo $row["note"] ?? "N/A"; ?></td>
													
													<td><script>document.write(convertTimestamp(<?php echo $row["usedon"]; ?>));</script></td>
													<td><?php echo $row["usedby"] ?? "N/A"; ?></td>
                                                    <td><label class="<?php echo $badge; ?>"><?php echo $row['status']; ?></label></td>

                                            <form method="POST"><td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" name="deletekey" value="<?php echo $key; ?>">Delete</button>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-key" onclick="bankey('<?php echo $key; ?>')">Ban</a>
                                                <button class="dropdown-item" name="unbankey" value="<?php echo $key; ?>">Unban</button>
                                                <div class="dropdown-divider"></div>
												<button class="dropdown-item" name="editkey" value="<?php echo $key; ?>">Edit</button></div></td></tr></form>
<?php
    }
}
?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Key</th>
<th>Creation Date</th>
<th>Generated By</th>
<th>Duration</th>
<th>Note</th>
<th>Used On</th>
<th>Used By</th>
<th>Status</th>
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
if (isset($_POST['deletekey']))
{
	$resp = misc\license\deleteSingular($_POST['deletekey']);
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Failed to delete license!");
			break;
		case 'success':
			dashboard\primary\success("Successfully deleted license!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['bankey']))
{
    $resp = misc\license\ban($_POST['key'], $_POST['reason']);
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Failed to ban license!");
			break;
		case 'success':
			dashboard\primary\success("Successfully banned license!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['unbankey']))
{
	$resp = misc\license\unban($_POST['unbankey']);
	switch($resp)
	{
		case 'failure':
			dashboard\primary\error("Failed to unban license!");
			break;
		case 'success':
			dashboard\primary\success("Successfully unbanned license!");
			break;
		default:
			dashboard\primary\error("Unhandled Error! Contact us if you need help");
			break;
	}
}
if (isset($_POST['editkey']))
{
    $key = misc\etc\sanitize($_POST['editkey']);
    $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$key' AND `app` = '" . $_SESSION['app'] . "'");
    if (mysqli_num_rows($result) == 0)
    {
        mysqli_close($link);
        dashboard\primary\error("Key not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($result);
?>
        <div id="edit-key" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit License</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Key Level:</label>
                                                        <input type="text" class="form-control" name="level" value="<?php echo $row['level']; ?>" required>
														<input type="hidden" name="key" value="<?php echo $key; ?>">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Duration Unit:</label>
                                                        <select name="expiry" class="form-control"><option value="86400">Days</option><option value="60">Minutes</option><option value="3600">Hours</option><option value="1">Seconds</option><option value="604800">Weeks</option><option value="2629743">Months</option><option value="31556926">Years</option><option value="315569260">Lifetime</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Duration: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Editing license duration after the license has been used will do nothing. Used licenses become users so you need to go to users tab and click extend user(s) instead"></i></label>
                                                        <input name="duration" type="number" class="form-control" placeholder="Multiplied by selected Expiry unit">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="savekey">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									<?php
}
if (isset($_POST['savekey']))
{
    $key = misc\etc\sanitize($_POST['key']);
    $level = misc\etc\sanitize($_POST['level']);
    $duration = misc\etc\sanitize($_POST['duration']);
    if (!empty($duration))
    {
        $expiry = misc\etc\sanitize($_POST['expiry']);
        $duration = $duration * $expiry;
        mysqli_query($link, "UPDATE `keys` SET `expires` = '$duration' WHERE `key` = '$key' AND `app` = '" . $_SESSION['app'] . "'");
    }
    mysqli_query($link, "UPDATE `keys` SET `level` = '$level' WHERE `key` = '$key' AND `app` = '" . $_SESSION['app'] . "'");
    dashboard\primary\success("Successfully Updated Settings!");
    echo "<meta http-equiv='Refresh' Content='2'>";
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
                        
		function bankey(key) {
		 var bankey = $('.bankey');
		 bankey.attr('value', key);
      }
                    </script>
</body>
</html>
