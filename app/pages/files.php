<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 64)) {
    die('You weren\'t granted permissions to view this page.');
}
if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}

if (isset($_POST['addfile'])) {
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\upload\add($_POST['url'], $authed);
    switch ($resp) {
        case 'invalid':
            dashboard\primary\error("URL not valid!");
            break;
        case 'no_local':
            dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet");
            break;
        case 'failure':
            dashboard\primary\error("Failed to add file!");
            break;
        case 'success':
            dashboard\primary\success("Successfully added file!");
            break;
        case 'tester_file_exceed':
            dashboard\primary\error("Tester plan may only upload files up to 10MB. Upgrade for larger file size.");
            break;
        case 'dev_file_exceed':
            dashboard\primary\error("File size limit is 50 MB.");
            break;
        case 'seller_file_exceed':
            dashboard\primary\error("File size limit is 75 MB.");
            break;
        case 'name_too_large':
            dashboard\primary\error("File name is too large! Rename it to have a shorter name.");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['delfiles'])) {
    $resp = misc\upload\deleteAll();
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete all files!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted all files!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['deletefile'])) {
    $resp = misc\upload\deleteSingular($_POST['deletefile']);
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete all files!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted all files!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['editfile'])) {
    $file = misc\etc\sanitize($_POST['editfile']);

    echo '<div id="edit-file" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title">Edit File</h4>
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
                                                        <label for="recipient-name" class="control-label">File URL: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="We recommend sending the file in a Discord DM where it won\'t get deleted. Then copy link and put here. Make sure the link has the file extension at the end, .exe or whatever. If it doesn\'t, the download will not work."></i></label>
                                                        <input type="url" class="form-control" name="url" placeholder="Link to file" required>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
                                                    </label>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" value="' . $file . '" name="savefile">Save</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </div>';
}

if (isset($_POST['savefile'])) {
    $fileid = misc\etc\sanitize($_POST['savefile']);
    $url = misc\etc\sanitize($_POST['url']);

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        dashboard\primary\error("Invalid Url!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if(str_contains($url, "localhost") || str_contains($url, "127.0.0.1") || str_contains($url, "file:/")) {
        dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $file = file_get_contents($url);

    $filesize = strlen($file);

    if ($filesize > 10000000 && $role == "tester") {
        dashboard\primary\error("Users with tester plan may only upload files up to 10MB. Paid plans may upload up to 75MB.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    } else if ($filesize > 50000000 && ($role == "developer" || $role == "Manager")) {
        dashboard\primary\error("File size limit is 50 MB. Upgrade your account to gain a total of 75mb.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    } else if ($filesize > 75000000) {
        dashboard\primary\error("File size limit is 75 MB.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $fn = basename($url);
    $fs = misc\etc\formatBytes($filesize);

    if(strlen($fn) > 49) {
        dashboard\primary\error("File name is too large! Rename it to have a shorter name.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;

    $query = misc\mysql\query("UPDATE `files` SET `name` = ?,`size` = ?,`url` = ?, `uploaddate` = ?, `authed` = ? WHERE `app` = ? AND `id` = ?", [$fn, $fs, $url, time(), $authed, $_SESSION['app'], $fileid]);

    if ($query->affected_rows != 0) {
        misc\cache\purge('KeyAuthFile:' . ($secret ?? $_SESSION['app']) . ':' . $fileid);
        dashboard\primary\success("Successfully Updated File!");
    } else {
        dashboard\primary\error("Failed to update file");
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
    <div class="alert alert-warning" role="alert">
        You must use a <b><u>direct URL</u></b> or the file will not work. Read here <a href="https://docs.keyauth.cc/website/dashboard/files" target="_blank">https://docs.keyauth.cc/website/dashboard/files</a>
    </div>
    <form method="POST">
        <button data-bs-toggle="modal" type="button" data-bs-target="#create-file" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Create File</button><br><br>
        <button type="button" class="dt-button buttons-print btn btn-danger mr-1" data-bs-toggle="modal" type="button" data-bs-target="#deleteallfiles"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All
            Files</button>
    </form>
    <br>
    <div id="create-file" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title">Add File</h4>
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
                            <label for="recipient-name" class="control-label">File URL: <i class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip" data-bs-placement="top" title="We recommend sending the file in a Discord DM where it won't get deleted. Then copy link and put here. Make sure the link has the file extension at the end, .exe or whatever. If it doesn't, the download will not work."></i></label>
                            <input type="url" class="form-control" name="url" placeholder="Link to file" required>
                        </div>
                        <div class="form-check">
                            <br>
                            <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                            <label class="form-check-label" for="flexCheckChecked">
                                Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip" data-bs-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
                            </label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" name="addfile">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <table id="kt_datatable_files" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Filename</th>
                <th>File ID</th>
                <th>Filesize</th>
                <th>Upload Date</th>
                <th>Authenticated</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `files` WHERE `app` = ?", [$_SESSION['app']]);
                if ($query->num_rows > 0) {
                    while ($row = mysqli_fetch_array($query->result)) {
                        echo "<tr>";

                        echo "  <td>" . $row["name"] . "</td>";

                        echo "  <td>" . $row["id"] . "</td>";

                        echo "  <td>" . $row["size"] . "</td>";

                        echo "  <td><script>document.write(convertTimestamp(" . $row["uploaddate"] . "));</script></td>";

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
                    <button class="btn menu-link px-3" style="font-size:0.95rem;" name="editfile" value="' . $row["id"] . '">Edit</button>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletefile" value="' . $row["id"] . '">Delete</button>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a class="btn menu-link px-3" style="font-size:0.95rem;" href="' . $row['url'] . '">Download</a>
                </div>
                <!--end::Menu item-->
            </div></td></tr></form>';
                    }
                }
            }

            ?>
        </tbody>

    </table>


    <div class="modal fade" tabindex="-1" id="deleteallfiles">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete All Files</h2>

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
                        <p> Are you sure you want to delete all files? This can not be undone.</p>
                    </label>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                        <button name="delfiles" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Container-->