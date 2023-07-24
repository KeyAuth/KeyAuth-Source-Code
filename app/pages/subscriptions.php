<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 4)) {
    die('You weren\'t granted permissions to view this page.');
}
if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}
if (isset($_POST['addsub'])) {
    if (!is_numeric($_POST['level'])) {
        dashboard\primary\error("Level must be a number!");
    } else {
        $resp = misc\sub\add($_POST['subname'], $_POST['level']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to create subscription!");
                break;
            case 'success':
                dashboard\primary\success("Successfully created subscription!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
}

if (isset($_POST['deletesub'])) {
    $resp = misc\sub\deleteSingular($_POST['deletesub']);
    switch ($resp) {
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
if (isset($_POST['editsub'])) {
    $subscription = misc\etc\sanitize($_POST['editsub']);
    $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `name` = ? AND `app` = ?", [$subscription, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Subscription not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($query->result);
    $level = $row["level"];
    echo '<div id="edit-webhook" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header d-flex align-items-center">

                                                <h4 class="modal-title">Edit Subscription</h4>

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

                                                        <label for="recipient-name" class="control-label">Subscription Level:</label>

                                                        <input type="text" class="form-control" name="level" value="' . $level . '" maxlength="12" required>

                                                        <input type="hidden" name="subscription" value="' . $subscription . '">

                                                    </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                                <button class="btn btn-danger waves-effect waves-light" name="savesub">Save</button>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                    </div>';
}
if (isset($_POST['savesub'])) {
    $subscription = misc\etc\sanitize($_POST['subscription']);
    $level = misc\etc\sanitize($_POST['level']);

    if (!is_numeric($_POST['level'])) {
        dashboard\primary\error("Level must be a number!");
    } else {
        misc\mysql\query("UPDATE `subscriptions` SET `level` = ? WHERE `name` = ? AND `app` = ?", [$level, $subscription, $_SESSION['app']]);
        if ($_SESSION['role'] == "seller") {
            misc\cache\purge('KeyAuthSubscriptions:' . $_SESSION['app']);
        }
        dashboard\primary\success("Successfully Updated Subscription!");
        echo "<meta http-equiv='Refresh' Content='2'>";
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

    <button data-bs-toggle="modal" type="button" data-bs-target="#create-subscription" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
        Create Subscription</button>
    <br></br>
    <div class="modal fade" tabindex="-1" id="create-subscription">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Add Subscription</h4>

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

                            <label for="recipient-name" class="control-label">Subscription Name:</label>

                            <input type="text" class="form-control" name="subname" placeholder="Anything you want" required maxlength="49">

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Subscription Level: <i class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip" data-bs-placement="top" title="When the keys you create are redeemed, KeyAuth will assign the subscriptions with the same level as the key to the user being created. So basically, you can have several user ranks aka subscriptions."></i></label>

                            <input type="text" class="form-control" placeholder="License key level (number only)" name="level" maxlength="12" required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="addsub">Add</button>

                    </form>

                </div>

            </div>

        </div>
    </div>

    <table id="kt_datatable_subs" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Subscription Name</th>
                <th>License Level</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ?", [$_SESSION['app']]);
                if ($query->num_rows > 0) {
                    while ($row = mysqli_fetch_array($query->result)) {
                        echo "<tr>";
                        echo "  <td>" . $row["name"] . "</td>";
                        echo "  <td>" . $row["level"] . "</td>";
                        // echo "  <td>". $row["status"]. "</td>";

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
                                                                <button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletesub" value="' . $row["name"] . '">Delete</button>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <button class="btn menu-link px-3" style="font-size:0.95rem;" name="editsub" value="' . $row["name"] . '">Edit</button>
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