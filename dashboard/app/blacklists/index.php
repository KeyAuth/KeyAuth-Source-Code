<?php
include '../../../includes/connection.php';
include '../../../includes/misc/autoload.phtml';
include '../../../includes/dashboard/autoload.phtml';
dashboard\primary\head();

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
?>
<!-- =============================================================== -->

<div class="container-fluid" id="content" style="display:none;">

    <!-- ============================================================== -->

    <!-- Start Page Content -->

    <!-- ============================================================== -->

    <!-- File export -->

    <div class="row">

        <div class="col-12">

            <?php dashboard\primary\heador(); // display app info and buttons to change, delete, and pause app
            ?>

            <?php if ($_SESSION['timeleft']) { ?>

                <div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>

            <?php
            } ?>

            <form method="post">

                <button data-toggle="modal" type="button" data-target="#create-blacklist" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Blacklist</button> <button name="delblacks" onclick="return confirm('Are you sure you want to delete all blacklists?')" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Blacklists</button>
            </form>

            <br>

            <div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=1lHjDeB3dA0" target="tutorial">https://youtube.com/watch?v=1lHjDeB3dA0</a> You may also join Discord and ask for help!

            </div>

            <div id="create-blacklist" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header d-flex align-items-center">

                            <h4 class="modal-title">Add Blacklist</h4>

                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

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

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger waves-effect waves-light" name="addblack">Add</button>

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

                                    <th>Blacklist Data</th>

                                    <th>Blacklist Type</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                if ($_SESSION['app']) {
                                    ($result = mysqli_query($link, "SELECT * FROM `bans` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<tr>";
                                            $data = $row["hwid"] ?? $row["ip"]; // display either hwid or IP, depending which one isn't null
                                            echo "  <td>" . $data . "</td>";
                                            echo "  <td>" . $row["type"] . "</td>";
                                            // echo "  <td>". $row["status"]. "</td>";
                                            echo '<td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                                Manage

                                            </button>

                                            <div class="dropdown-menu"><form method="post">

                                                <button class="dropdown-item" name="deleteblack" value="' . $data . '">Delete</button><input type="hidden" name="type" value="' . $row["type"] . '"></div></td></tr></form>';
                                        }
                                    }
                                }
                                ?>

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th>Blacklist Data</th>

                                    <th>Blacklist Type</th>

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