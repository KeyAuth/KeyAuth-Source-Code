<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 512)) {
    die('You weren\'t granted permissions to view this page.');
}
if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}
if (isset($_POST['addblack'])) {
    $resp = misc\blacklist\add($_POST['blackdata'], $_POST['blacktype']);
    switch ($resp) {
        case 'invalid':
            dashboard\primary\error("Invalid blacklist type!");
            break;
        case 'failure':
            dashboard\primary\error("Failed to add blacklist!");
            break;
        case 'success':
            dashboard\primary\success("Successfully added blacklist!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}
if (isset($_POST['delblacks'])) {
    $resp = misc\blacklist\deleteAll();
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete all blacklists!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted all blacklists!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['deleteblack'])) {
    $resp = misc\blacklist\deleteSingular($_POST['deleteblack'], $_POST['type']);
    switch ($resp) {
        case 'invalid':
            dashboard\primary\error("Invalid blacklist type!");
            break;
        case 'failure':
            dashboard\primary\error("Failed to delete blacklist!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted blacklist!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}
if (isset($_POST['addwhite'])) {
    $resp = misc\blacklist\addWhite($_POST['ip']);
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to add whitelist!");
            break;
        case 'success':
            dashboard\primary\success("Successfully added whitelist!");
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
        You can't sort by "Blacklist Data" column, however you can search something and it will display all rows containing that text
    </div>
    <form method="post">

        <button data-bs-toggle="modal" type="button" data-bs-target="#create-blacklist" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Create Blacklist</button>
        <button data-bs-toggle="modal" type="button" data-bs-target="#create-whitelist" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Create Whitelist</button><br><br>
        <button data-bs-toggle="modal" type="button" data-bs-target="#delete-blacklists" class="dt-button buttons-print btn btn-danger mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i>
            Delete All Blacklists</button>
    </form>
    <br>



    <div id="create-blacklist" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Add Blacklist</h4>

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

                            <label for="recipient-name" class="control-label">Blacklist Type:</label>

                            <select name="blacktype" class="form-control">
                                <option>IP Address</option>
                                <option>Hardware ID</option>
                            </select>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Blacklist Data:</label>

                            <input type="text" class="form-control" placeholder="IP or HWID to blacklist" name="blackdata" required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="addblack">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>



    <div class="modal fade" tabindex="-1" id="delete-blacklists">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Blacklists</h2>

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
                        <p> Are you sure you want to delete all blacklists? This can not be undone.</p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="delblacks" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="create-whitelist" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Add Whitelist</h4>

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

                            <label for="recipient-name" class="control-label">IP Address:</label>

                            <input type="text" class="form-control" placeholder="IP to whitelist from VPN check" name="ip" required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="addwhite">Add</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <table id="kt_datatable_blacklists" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Blacklist Data</th>
                <th>Blacklist Type</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

</div>
<!--end::Container-->