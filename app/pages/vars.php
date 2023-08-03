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
    if ($_SESSION['role'] == "tester") {
        if(strlen($_POST['vardata']) > 100) {
            dashboard\primary\error("Must upgrade to developer or seller to create variables longer than 100 characters!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }

        $query = misc\mysql\query("SELECT count(*) AS 'numVars' FROM `vars` WHERE `app` = ?",[$_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        $numVars = $row["numVars"];
        if($numVars >= 5) {
            dashboard\primary\error("Must upgrade to developer or seller to create more than 5 variables!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }
    }
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\variable\add($_POST['varname'], $_POST['vardata'], $authed);
    switch ($resp) {
        case 'exists':
            dashboard\primary\error("Variable name already exists!");
            break;
        case 'too_long':
            dashboard\primary\error("Variable too long! Must be 1000 characters or less");
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

// edit modal
if (isset($_POST['editvar'])) {
    $variable = misc\etc\sanitize($_POST['editvar']);

    $query = misc\mysql\query("SELECT * FROM `vars` WHERE `varid` = ? AND `app` = ?",[$variable, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Variable not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($query->result);

    $data = $row["msg"];

    echo '<div id="edit-webhook" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title">Edit Variable</h4>
                                                <!--begin::Close-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary" onClick="window.location.href=window.location.href">
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
                                                        <label for="recipient-name" class="control-label">Variable Data:</label>
                                                        <textarea maxlength="1000" class="form-control" name="msg" required rows="3">'.$data.'</textarea>
                                                        <input type="hidden" name="variable" value="' . $variable . '">
                                                    </div>
                                                    <br>
                                                    <div class="form-check">
                                                    <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        case 'too_long':
            dashboard\primary\error("Variable too long! Must be 1000 characters or less");
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
    <!-- Include the jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
    $('div.modal-content').css('border', '2px solid #1b8adb');
    });
    </script>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
    <div class="alert alert-warning" role="alert">
        You must use the <b>var()</b> function for these. <b><u>Do NOT</u></b> use the <b>getvar()</b> function, that's for user variables which are completely different.
    </div>
    <form method="post">
        <button data-bs-toggle="modal" type="button" data-bs-target="#create-variable" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Variable</button><br><br>
        <button name="delvars" data-bs-toggle="modal" type="button" data-bs-target="#deleteallvars" class="dt-button buttons-print btn btn-danger mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Variables</button>
    </form>
    <br>
    <div id="create-variable" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title">Add Variable</h4>
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
                        <div class="alert alert-primary" role="alert">
                            If your variable is longer than a few hundred characters, you should use download() function <a href="https://keyauth.cc/app/?page=files">https://keyauth.cc/app/?page=files</a>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Variable Name:</label>
                            <input type="text" class="form-control" name="varname" placeholder="Name To Reference Variable By" required maxlength="49">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Variable Data: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Get string from KeyAuth server, where it's more secure"></i></label>
                                <textarea maxlength="1000" class="form-control" name="vardata" placeholder="Value of Variable" required rows="3"></textarea>
                        </div>
                        <br>
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
                        <p> Are you sure you want to delete all variables? This can not be undone.</p>
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
    </table>

</div>
<!--end::Container-->