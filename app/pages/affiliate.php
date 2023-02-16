<?php
if (($_SESSION['role'] != "developer" && $_SESSION['role'] != "seller") || ($_SESSION['username'] == "demoseller" || $_SESSION['username'] == "demodeveloper")) {
    die('Only paid users can access affiliate dashboard');
}
if (isset($_POST['setcode'])) {
    $code = misc\etc\sanitize($_POST['code']);
	if(strtolower($code) == "keyauth") {
		dashboard\primary\error("Affiliate code can\'t be the name of this service, KeyAuth");
		echo "<meta http-equiv='Refresh' Content='2'>";
		return;
	}
	if(strlen($code) < 5) {
		dashboard\primary\error("Affiliate code must be 5 or more characters long");
		echo "<meta http-equiv='Refresh' Content='2'>";
		return;
	}
    mysqli_query($link, "UPDATE `accounts` SET `afCode` = '$code' WHERE `username` = '" . $_SESSION['username'] . "'");
	if(mysqli_affected_rows($link) > 0) {
		$_SESSION['afCode'] = $code;
		dashboard\primary\success("Successfully set affiliate code!");
	}
	else {
		dashboard\primary\error("Failed to set affiliate code! May already be taken");
	}
}
?>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
	<script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>
    <?php
	$codeSet = 0;
    ($result = mysqli_query($link, "SELECT `afCode` FROM `accounts` WHERE `username` = '" . $_SESSION['username'] . "'")) or die(mysqli_error($link));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        
		$code = $row['afCode'];
		if(!is_null($code)) {
			$codeSet = 1;
		}
    }
    ?>
    <div class="card">
        <div class="card-body">
            <form class="form" method="post">
				Recommend KeyAuth to someone. If they sign up with your affiliate code, they get 2 month free trial & then if they buy KeyAuth after you get 2 months added to your subscription.
				<br>
				<br>
				<b>Note:</b>
				<ul>
					<li>You can only set affiliate code <u style="color:red;">once</u></li>
					<li>You are <u style="color:red;">NOT</u> not allowed to share your affiliate link in KeyAuth Discord channel or KeyAuth YouTube channel</li>
				</ul> 
				<?php
				if($codeSet) {
				?>
				<div class="form-group row">
                    <label for="example-tel-input" class="col-2 col-form-label">Affiliate Link</label>
                    <div class="col-10">
                        <label class="form-control" style="height:auto;"><?php
                                                                            echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/register/?af='.$code.'" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/register/?af='.$code.'</a>';

                                                                            ?></label>
                    </div>
                </div>
				<?php 
				}
				else {
				?>
				<br>
                <div class="form-group row">
                    <label for="example-tel-input" class="col-2 col-form-label">Affiliate Code</label>
                    <div class="col-10">
                        <input class="form-control" id="af_code" name="code" placeholder="Affiliate code, must be at least 5 characters" minlength="5" maxlength="50" type="text" required>
                    </div>
                </div>
                <br>
                <a type="button" class="btn btn-info" onclick="genRandCode()">
                    <i class="fas fa-random"></i> Generate random code</a>
                <button name="setcode" class="btn btn-warning">
                    <i class="fas fa-save"></i> Set code</button>
				<?php
				}
				?>
            </form>
        </div>
    </div>
	<br>
	<table id="kt_datatable_af_logs" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Date</th>
                <th>Referrering website</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<!--end::Container-->
<script>
function genRandCode() {
	// credits https://stackoverflow.com/a/8084248
	let r = (Math.random() + 1).toString(36).substring(7).toUpperCase();
	document.getElementById('af_code').value = r;
	document.getElementById('af_code').select();
}
</script>