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

                <button data-toggle="modal" type="button" data-target="#create-channel" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Channel</button> <button data-toggle="modal" type="button" data-target="#clear-channel" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Clear channel</button> <button data-toggle="modal" type="button" data-target="#unmute-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-undo fa-sm text-white-50"></i> Unmute User</button>

            </form>

            <br>

            <div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=oLj04x0k1RI" target="tutorial">https://youtube.com/watch?v=oLj04x0k1RI</a> You may also join Discord and ask for help!</div>

            <div id="create-channel" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header d-flex align-items-center">

                            <h4 class="modal-title">Add Channels</h4>

                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>

                        </div>

                        <div class="modal-body">

                            <form method="post">

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Name:</label>

                                    <input class="form-control" name="name" placeholder="Chat channel name" required>

                                </div>

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Chat cooldown Unit:</label>

                                    <select name="unit" class="form-control">
                                        <option value="1">Seconds</option>
                                        <option value="60">Minutes</option>
                                        <option value="3600">Hours</option>
                                        <option value="86400">Days</option>
                                        <option value="604800">Weeks</option>
                                        <option value="2629743">Months</option>
                                        <option value="31556926">Years</option>
                                        <option value="315569260">Lifetime</option>
                                    </select>

                                </div>

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Chat cooldown: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Delay users will have to wait to send their next message"></i></label>

                                    <input name="delay" type="number" class="form-control" placeholder="Multiplied by selected delay unit" required>

                                </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger waves-effect waves-light" name="addchannel">Add</button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>



            <div id="unmute-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header d-flex align-items-center">

                            <h4 class="modal-title">Unmute User</h4>

                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>

                        </div>

                        <div class="modal-body">

                            <form method="post">

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Name:</label>

                                    <select class="form-control" name="user">

                                        <?php
                                        ($result = mysqli_query($link, "SELECT * FROM `chatmutes` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                        $rows = array();
                                        while ($r = mysqli_fetch_assoc($result)) {
                                            $rows[] = $r;
                                        }
                                        foreach ($rows as $row) {
                                        ?>

                                            <option><?php echo $row["user"]; ?></option>

                                        <?php
                                        } ?>

                                    </select>

                                </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger waves-effect waves-light" name="unmuteuser">Unmute</button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>



            <div id="clear-channel" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header d-flex align-items-center">

                            <h4 class="modal-title">Clear Channel</h4>

                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>

                        </div>

                        <div class="modal-body">

                            <form method="post">

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Channel name:</label>

                                    <select class="form-control" name="channel">

                                        <?php
                                        ($result = mysqli_query($link, "SELECT * FROM `chats` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                        $rows = array();
                                        while ($r = mysqli_fetch_assoc($result)) {
                                            $rows[] = $r;
                                        }
                                        foreach ($rows as $row) {
                                        ?>

                                            <option><?php echo $row["name"]; ?></option>

                                        <?php
                                        } ?>

                                    </select>

                                </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger waves-effect waves-light" name="clearchannel">Clear</button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

            <?php
            if (isset($_POST['addchannel'])) {
                if ($_SESSION['role'] != "seller") {
                    dashboard\primary\error("You must upgrade to seller to create chat channels");
                } else {
                    $unit = misc\etc\sanitize($_POST['unit']);
                    $delay = misc\etc\sanitize($_POST['delay']);
                    $delay = $delay * $unit;
                    $resp = misc\chat\createChannel($_POST['name'], $delay);
                    switch ($resp) {
                        case 'failure':
                            dashboard\primary\error("Failed to create channel!");
                            break;
                        case 'success':
                            dashboard\primary\success("Successfully created channel!");
                            break;
                        default:
                            dashboard\primary\error("Unhandled Error! Contact us if you need help");
                            break;
                    }
                }
            }
            ?>



            <script type="text/javascript">
                var myLink = document.getElementById('mylink');



                myLink.onclick = function() {





                    $(document).ready(function() {

                        $("#content").fadeOut(100);

                        $("#changeapp").fadeIn(1900);

                    });



                }
            </script>

            <div id="mute-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header d-flex align-items-center">

                            <h4 class="modal-title">Mute User</h4>

                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>

                        </div>

                        <div class="modal-body">

                            <form method="post">

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Unit Of Time Muted:</label>

                                    <select name="muted" class="form-control">
                                        <option value="86400">Days</option>
                                        <option value="60">Minutes</option>
                                        <option value="3600">Hours</option>
                                        <option value="1">Seconds</option>
                                        <option value="604800">Weeks</option>
                                        <option value="2629743">Months</option>
                                        <option value="31556926">Years</option>
                                        <option value="315569260">Lifetime</option>
                                    </select>

                                    <input type="hidden" class="muteuser" name="user">

                                </div>

                                <div class="form-group">

                                    <label for="recipient-name" class="control-label">Time Muted:</label>

                                    <input class="form-control" name="time" placeholder="Multiplied by selected unit of time muted">

                                </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                            <button class="btn btn-danger waves-effect waves-light" name="muteuser">Ban</button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>
            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="file_export_two" class="table table-striped table-bordered display">

                            <thead>

                                <tr>

                                    <th>Name</th>

                                    <th>Delay</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>



                                <?php
                                if ($_SESSION['app']) {
                                    ($result = mysqli_query($link, "SELECT * FROM `chats` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                    $rows = array();
                                    while ($r = mysqli_fetch_assoc($result)) {
                                        $rows[] = $r;
                                    }
                                    foreach ($rows as $row) {
                                        $chan = $row['name'];
                                ?>



                                        <tr>



                                            <td><?php echo $chan; ?></td>



                                            <td><?php echo dashboard\primary\time2str(time() - $row["delay"]); ?></td>



                                            <form method="POST">
                                                <td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                                        Manage

                                                    </button>

                                                    <div class="dropdown-menu">

                                                        <button class="dropdown-item" name="deletechan" value="<?php echo $chan; ?>">Delete</button>

                                                        <button class="dropdown-item" name="editchan" value="<?php echo $chan; ?>">Edit</button>
                                                    </div>
                                                </td>
                                        </tr>
                                        </form>

                                <?php
                                    }
                                }
                                ?>

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th>Name</th>

                                    <th>Delay</th>

                                    <th>Action</th>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">


                        <table id="file_export" class="table table-striped table-bordered display">

                            <thead>

                                <tr>

                                    <th>Author</th>

                                    <th>Message</th>

                                    <th>Time Sent</th>

                                    <th>Channel</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>



                                <?php
                                if ($_SESSION['app']) {
                                    ($result = mysqli_query($link, "SELECT * FROM `chatmsgs` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                                    $rows = array();
                                    while ($r = mysqli_fetch_assoc($result)) {
                                        $rows[] = $r;
                                    }
                                    foreach ($rows as $row) {
                                        $user = $row['author'];
                                ?>



                                        <tr>



                                            <td><?php echo $user; ?></td>



                                            <td><?php echo $row["message"]; ?></td>



                                            <td>
                                                <script>
                                                    document.write(convertTimestamp(<?php echo $row["timestamp"]; ?>));
                                                </script>
                                            </td>



                                            <td><?php echo $row["channel"]; ?></td>



                                            <form method="POST">
                                                <td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                                        Manage

                                                    </button>

                                                    <div class="dropdown-menu">

                                                        <button class="dropdown-item" name="deletemsg" value="<?php echo $row["id"]; ?>">Delete</button>

                                                        <a class="dropdown-item" data-toggle="modal" data-target="#mute-user" onclick="muteuser('<?php echo $user; ?>')">Mute</a>
                                                    </div>
                                                </td>
                                        </tr>
                                        </form>

                                <?php
                                    }
                                }
                                ?>

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th>Author</th>

                                    <th>Message</th>

                                    <th>Time Sent</th>

                                    <th>Channel</th>

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
    if (isset($_POST['deletemsg'])) {
        $resp = misc\chat\deleteMessage($_POST['deletemsg']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to delete message!");
                break;
            case 'success':
                dashboard\primary\success("Successfully deleted message!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['muteuser'])) {
        $muted = misc\etc\sanitize($_POST['muted']);
        $time = misc\etc\sanitize($_POST['time']);
        $time = $time * $muted + time();

        $resp = misc\chat\muteUser($_POST['user'], $time);
        switch ($resp) {
            case 'missing':
                dashboard\primary\error("User doesn\'t exist!");
                break;
            case 'failure':
                dashboard\primary\error("Failed to mute user!");
                break;
            case 'success':
                dashboard\primary\success("Successfully muted user!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['unmuteuser'])) {
        $resp = misc\chat\unMuteUser($_POST['user']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to unmute user!");
                break;
            case 'success':
                dashboard\primary\success("Successfully unmuted user!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['clearchannel'])) {
        $resp = misc\chat\clearChannel($_POST['channel']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to clear channel!");
                break;
            case 'success':
                dashboard\primary\success("Successfully cleared channel!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['editchan'])) {
        $chan = misc\etc\sanitize($_POST['editchan']);
        $result = mysqli_query($link, "SELECT * FROM `chats` WHERE `name` = '$chan' AND `app` = '" . $_SESSION['app'] . "'");
        if (mysqli_num_rows($result) == 0) {
            mysqli_close($link);
            dashboard\primary\error("Channel not Found!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }
    ?>

        <div id="edit-user" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true" o ydo>

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header d-flex align-items-center">

                        <h4 class="modal-title">Edit Channel</h4>

                        <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>

                    </div>

                    <div class="modal-body">

                        <form method="post">

                            <div class="form-group">

                                <label for="recipient-name" class="control-label">Chat cooldown Unit:</label>

                                <select name="unit" class="form-control">
                                    <option value="1">Seconds</option>
                                    <option value="60">Minutes</option>
                                    <option value="3600">Hours</option>
                                    <option value="86400">Days</option>
                                    <option value="604800">Weeks</option>
                                    <option value="2629743">Months</option>
                                    <option value="31556926">Years</option>
                                    <option value="315569260">Lifetime</option>
                                </select>

                            </div>

                            <div class="form-group">

                                <label for="recipient-name" class="control-label">Chat cooldown: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Delay users will have to wait to send their next message"></i></label>

                                <input name="delay" type="number" class="form-control" placeholder="Multiplied by selected delay unit" required>

                            </div>
                    </div>

                    <div class="modal-footer">

                        <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                        <button class="btn btn-danger waves-effect waves-light" value="<?php echo $chan; ?>" name="savechan">Save</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    <?php
    }
    if (isset($_POST['savechan'])) {
        $chan = misc\etc\sanitize($_POST['savechan']);
        $unit = misc\etc\sanitize($_POST['unit']);
        $delay = misc\etc\sanitize($_POST['delay']);
        $delay = $delay * $unit;
        mysqli_query($link, "UPDATE `chats` SET `delay` = '$delay' WHERE `app` = '" . $_SESSION['app'] . "' AND `name` = '$chan'");
        if (mysqli_affected_rows($link) > 0) // check query impacted something, else show error

        {
            dashboard\primary\success("Successfully updated channel!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        } else {
            dashboard\primary\error("Failed To update channel!");
        }
    }
    if (isset($_POST['deletechan'])) {
        $resp = misc\chat\deleteChannel($_POST['deletechan']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to delete channel!");
                break;
            case 'success':
                dashboard\primary\success("Successfully deleted channel!");
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



<script>
    function muteuser(key) {

        var muteuser = $('.muteuser');

        muteuser.attr('value', key);

    }

    $('#file_export_two').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>

</body>
</html>