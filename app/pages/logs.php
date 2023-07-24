<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if($role == "Manager" && !($permissions & 256)) {
    die('You weren\'t granted permissions to view this page.');
}
if(!isset($_SESSION['app'])) {
    die("Application not selected.");
}
if (isset($_POST['dellogs'])) {
    $resp = misc\logging\deleteAll();
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete all logs!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted all logs!");
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
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>
    <div class="alert alert-primary" role="alert">
            The maximum log message length is 275 characters. Logs are automatically deleted after 1 month. If you set Discord webhook at <a href="https://keyauth.cc/app/?page=app-settings">https://keyauth.cc/app/?page=app-settings</a>, logs will go to Discord and not display on our site.
    </div>
    <form method="post">
        <button type="button" data-bs-toggle="modal" type="button" data-bs-target="#del-logs"
            class="dt-button buttons-print btn btn-danger mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i>
            Delete All Logs</button>
    </form>


    <div class="modal fade" tabindex="-1" id="del-logs">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Logs</h2>

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
                        <p> Are you sure you want to delete all logs? This can not be undone.</p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="dellogs" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php
    if($_SESSION['role'] == "tester") {
        $query = misc\mysql\query("SELECT count(*) AS 'numLogs' FROM `logs` WHERE `logapp` = ?",[$_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        $numLogs = $row["numLogs"];
        if($numLogs >= 20) {
            ?><div class="alert alert-danger" role="alert">You have hit 20 logs! Upgrade to developer or seller to store more logs.</div><?php
        }
    }
    ?>
    
    <table id="kt_datatable_logs" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Log Date</th>
                <th>Log Data</th>
                <th>Credential</th>
                <th>Device Name</th>
            </tr>
        </thead>
    </table>
</div>
<!--end::Container-->