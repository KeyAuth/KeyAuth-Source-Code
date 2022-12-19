<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
	die();
}
if($role == "Manager" && !($permissions & 128)) {
	die('You weren\'t granted permissions to view this page.');
}
if(!isset($_SESSION['app'])) {
	die("Application not selected.");
}

if (isset($_POST['genvar'])) {
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\variable\add($_POST['varname'], $_POST['vardata'], $authed);
    switch ($resp) {
        case 'exists':
            dashboard\primary\error("Variable name already exists!");
            break;
        case 'failure':
            dashboard\primary\error("Failed to create variable!");
            break;
        case 'success':
            dashboard\primary\success("Successfully created variable!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['delvars'])) {
    $resp = misc\variable\deleteAll();
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete all variables!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted all variables!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['deletevar'])) {
    $resp = misc\variable\deleteSingular($_POST['deletevar']);
    switch ($resp) {
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

if (isset($_POST['editvar'])) // edit modal

{
    $variable = misc\etc\sanitize($_POST['editvar']);

    $result = mysqli_query($link, "SELECT * FROM `vars` WHERE `varid` = '$variable' AND `app` = '" . $_SESSION['app'] . "'");
    if (mysqli_num_rows($result) < 1) {
        mysqli_close($link);
        dashboard\primary\error("Variable not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($result);

    $data = $row["msg"];

    echo '<div id="edit-webhook" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit Variable</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Variable Data:</label>
                                                        <input type="text" class="form-control" name="msg" value="' . $data . '" required>
														<input type="hidden" name="variable" value="' . $variable . '">
                                                    </div>
													<div class="form-check">
													<input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
													<label class="form-check-label" for="flexCheckChecked">
														Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
													</label>
													</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="savevar">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>';
}

if (isset($_POST['savevar'])) {
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\variable\edit($_POST['variable'], $_POST['msg'], $authed);
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to edit variable!");
            break;
        case 'success':
			misc\cache\purge('KeyAuthVar:' . $_SESSION['app'] . ':' . $_POST['variable']);
            dashboard\primary\success("Successfully edited variable!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}
?>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">

    <form method="post">
        <button data-bs-toggle="modal" type="button" data-bs-target="#create-variable" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Variable</button>
        <button name="delvars" data-bs-toggle="modal" type="button" data-bs-target="#deleteallvars" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Variables</button>
    </form>
    <br>
    <div id="create-variable" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title">Add Variables</h4>
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Variable Name:</label>
                            <input type="text" class="form-control" name="varname" placeholder="Name To Refrence Variable By" required>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Variable Data: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Get string from KeyAuth server, where it's more secure"></i></label>
                            <input type="text" class="form-control" placeholder="Value of Variable" name="vardata" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                            <label class="form-check-label" for="flexCheckChecked">
                                Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
                            </label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" name="genvar">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="deleteallvars">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Variables</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to delete all variables? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="delvars" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table id="kt_datatable_vars" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Variable Name</th>
                <th>Variable Data</th>
                <th>Authenticated</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if ($_SESSION['app']) {
                ($result = mysqli_query($link, "SELECT * FROM `vars` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {

                        echo "<tr>";

                        echo "  <td>" . $row["varid"] . "</td>";

                        echo "  <td>" . $row["msg"] . "</td>";

                        echo "  <td>" . (($row["authed"] ? 1 : 0) ? 'True' : 'False') . "</td>";

                        echo '<form method="POST">
			<td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions 
			<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
			<span class="svg-icon svg-icon-5 m-0">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
				</svg>
			</span>
			<!--end::Svg Icon--></a>
			<!--begin::Menu-->
			<div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4">
				<!--begin::Menu item-->
				<div class="menu-item px-3">
					<button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletevar" value="' . $row["varid"] . '">Delete</button>
				</div>
				<!--end::Menu item-->
				<!--begin::Menu item-->
				<div class="menu-item px-3">
					<button class="btn menu-link px-3" style="font-size:0.95rem;" name="editvar" value="' . $row["varid"] . '">Edit</button>
				</div>
				<!--end::Menu item-->
			</div></td></tr></form>';
                    }
                }
            }

            ?>
        </tbody>

    </table>

</div>
<!--end::Container-->
