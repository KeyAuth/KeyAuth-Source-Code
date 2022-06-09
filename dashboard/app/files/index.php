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
            <?php if ($_SESSION['timeleft']) { ?>
                <div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>
            <?php
            } ?>
            <form method="POST">
                <button data-toggle="modal" type="button" data-target="#create-files" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Files</button> <button name="delfiles" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to add all files?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Files</button>
            </form>
            <br>
            <div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=1lHjDeB3dA0" target="tutorial">https://youtube.com/watch?v=1lHjDeB3dA0</a> You may also join Discord and ask for help!
            </div>
            <div id="create-files" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header d-flex align-items-center">
                            <h4 class="modal-title">Add Files</h4>
                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="recipient-name" class="control-label">File URL: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="We recommend sending the file in a Discord DM where it won't get deleted. Then copy link and put here. Make sure the link has the file extension at the end, .exe or whatever. If it doesn't, the download will not work."></i></label>
                                    <input type="text" class="form-control" name="url" placeholder="Link to file">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Authenticated <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If checked, KeyAuth will force user to be logged in to use."></i>
                                    </label>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <button class="btn btn-danger waves-effect waves-light" name="addfile">Add</button>
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
                            <button class="btn btn-danger waves-effect waves-light" name="renameapp">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                var myLink = document.getElementById('mylink');

                myLink.onclick = function() {


                    $(document).ready(function() {
                        $("#content").fadeOut(100);
                        $("#changeapp").fadeIn(1900);
                    });

                }
            </script>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file_export" class="table table-striped table-bordered display">
                            <thead>
                                <tr>
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
                                    ($result = mysqli_query($link, "SELECT * FROM `files` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {

                                            echo "<tr>";

                                            echo "  <td>" . $row["name"] . "</td>";

                                            echo "  <td>" . $row["id"] . "</td>";

                                            echo "  <td>" . $row["size"] . "</td>";

                                            echo "  <td><script>document.write(convertTimestamp(" . $row["uploaddate"] . "));</script></td>";

                                            echo "  <td>" . (($row["authed"] ? 1 : 0) ? 'True' : 'False') . "</td>";

                                            echo '<td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
												</button>
												<div class="dropdown-menu"><form method="post">
												<button class="dropdown-item" name="editfile" value="' . $row['id'] . '">Edit</button>
                                                <button class="dropdown-item" name="deletefile" value="' . $row['id'] . '">Delete</button>
												<a class="dropdown-item" href="' . $row['url'] . '">Download</a>
												</div></td></tr></form>';
                                        }
                                    }
                                }

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Filename</th>
                                    <th>File ID</th>
                                    <th>Filesize</th>
                                    <th>Upload Date</th>
                                    <th>Authenticated</th>
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
    if (isset($_POST['addfile'])) {
        $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
        $resp = misc\upload\add($_POST['url'], $authed);
        switch ($resp) {
            case 'invalid':
                dashboard\primary\error("URL not valid!");
                break;
            case 'failure':
                dashboard\primary\error("Failed to add file!");
                break;
            case 'success':
                dashboard\primary\success("Successfully added file!");
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
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">File URL: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="We recommend sending the file in a Discord DM where it won\'t get deleted. Then copy link and put here. Make sure the link has the file extension at the end, .exe or whatever. If it doesn\'t, the download will not work."></i></label>
                                                        <input type="text" class="form-control" name="url" placeholder="Link to file">
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
            return;
        }

        $file = file_get_contents($url);

        $filesize = strlen($file);

        if ($filesize > 10000000 && $role == "tester") {
            dashboard\primary\error("Users with tester plan may only upload files up to 10MB. Paid plans may upload up to 50MB.");
            return;
        } else if ($filesize > 50000000) {
            dashboard\primary\error("File size limit is 50 MB.");
            return;
        }

        $fn = basename($url);
        $fs = misc\etc\formatBytes($filesize);

        $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;

        mysqli_query($link, "UPDATE `files` SET `name` = '$fn',`size` = '$fs',`url` = '$url', `uploaddate` = '" . time() . "', `authed` = '$authed' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '$fileid'");

        if (mysqli_affected_rows($link) != 0) {
            dashboard\primary\success("Successfully Updated File!");
            echo "<meta http-equiv='Refresh' Content='2;'>";
        } else {
            dashboard\primary\error("Failed to update file");
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
    Copyright &copy; 2020-<script>
        document.write(new Date().getFullYear())
    </script> KeyAuth
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

<script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/email/email.js"></script>
<script src="https://cdn.keyauth.uk/dashboard/assets/libs/summernote/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.keyauth.uk/dashboard/assets/libs/dropzone/dist/min/dropzone.min.js"></script>

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
</body>

</html>