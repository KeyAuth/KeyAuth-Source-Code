<?php
include '../../../includes/connection.php';
include '../../../includes/misc/autoload.phtml';
include '../../../includes/dashboard/autoload.phtml';
dashboard\primary\head();
?>
<!-- =============================================================== -->

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

            <form method="post">

                <button name="killall" onclick="return confirm('Are you sure you want to kill all user\'s sessions?')" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Kill All Sessions</button>

            </form>

            <br>

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

                                    <th>ID</th>

                                    <th>Credential</th>

                                    <th>Expires</th>

                                    <th>Authenticated</th>

                                    <th>IP Address</th>

                                    <th>Manage</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                if ($_SESSION['app']) {
                                    ($result = mysqli_query($link, "SELECT * FROM `sessions` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_array($result)) {
                                            $cred = $row["credential"] ?? "N/A";
                                            echo "<tr>";
                                            echo "  <td>" . $row["id"] . "</td>";
                                            echo "  <td>" . $cred . "</td>";
                                            echo "  <td><script>document.write(convertTimestamp(" . $row["expiry"] . "));</script></td>";
                                            echo "  <td>" . (($row['validated'] ? 1 : 0) ? 'true' : 'false') . "</td>";
                                            $ip = $row["ip"] ?? "N/A";
                                            echo "  <td>" . $ip . "</td>";
                                            // echo "  <td>". $row["status"]. "</td>";
                                            echo '<td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                                Manage

                                            </button>

                                            <div class="dropdown-menu"><form method="post">

                                                <button class="dropdown-item" name="kill" value="' . $row['id'] . '">Kill</button></div></td></tr></form>';
                                        }
                                    }
                                }
                                ?>

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th>ID</th>

                                    <th>Credential</th>

                                    <th>Expires</th>

                                    <th>Authenticated</th>

                                    <th>IP Address</th>

                                    <th>Manage</th>

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
    if (isset($_POST['killall'])) {
        $resp = misc\session\killAll();
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to kill all sessions!");
                break;
            case 'success':
                dashboard\primary\success("Successfully killed all sessions!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['kill'])) {
        $resp = misc\session\killSingular($_POST['kill']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to kill session!");
                break;
            case 'success':
                dashboard\primary\success("Successfully killed session!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
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