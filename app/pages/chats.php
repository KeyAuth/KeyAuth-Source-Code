<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 8)) {
    die('You weren\'t granted permissions to view this page.');
}
if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}

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
            dashboard\primary\error("User doesn't exist!");
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
    $query = misc\mysql\query("SELECT * FROM `chats` WHERE `name` = ? AND `app` = ?", [$chan, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Channel not found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
?>
   
    <div id="edit-user" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true" o ydo>

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Edit Channel</h4>

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

                            <label for="recipient-name" class="control-label">Chat cooldown
                                Unit:</label>

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

                    <button type="button" onClick="window.location.href=window.location.href" class="btn btn-secondary" data-dismiss="modal">Close</button>

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
    $query = misc\mysql\query("UPDATE `chats` SET `delay` = ? WHERE `app` = ? AND `name` = ?", [$delay, $_SESSION['app'], $chan]);
    if ($query->affected_rows > 0) // check query impacted something, else show error
    {
        dashboard\primary\success("Successfully updated channel!");
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
    <form method="POST">
        <button data-bs-toggle="modal" type="button" data-bs-target="#create-channel" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
            Create Channel</button>
        <button data-bs-toggle="modal" type="button" data-bs-target="#unmute-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-undo fa-sm text-white-50"></i> Unmute
            User</button><br><br>
        <button data-bs-toggle="modal" type="button" data-bs-target="#clear-channel" class="dt-button buttons-print btn btn-danger mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Clear channel</button>
    </form>


    <div id="create-channel" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Add Channels</h4>

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

                            <label for="recipient-name" class="control-label">Name:</label>

                            <input class="form-control" name="name" placeholder="Chat channel name" required>

                        </div>

                        <div class="form-group">

                            <label for="recipient-name" class="control-label">Chat cooldown
                                Unit:</label>

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

                            <label for="recipient-name" class="control-label">Chat cooldown:
                                <i class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip" data-bs-placement="top" title="Delay users will have to wait to send their next message"></i></label>

                            <input name="delay" type="number" class="form-control" placeholder="Multiplied by selected delay unit" required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="addchannel">Add</button>

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

                            <label for="recipient-name" class="control-label">Name:</label>

                            <select class="form-control" name="user">

                                <?php

                                $query = misc\mysql\query("SELECT * FROM `chatmutes` WHERE `app` = ?", [$_SESSION['app']]);
                                $rows = array();
                                while ($r = mysqli_fetch_assoc($query->result)) {
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

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="unmuteuser">Unmute</button>

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

                            <label for="recipient-name" class="control-label">Channel
                                name:</label>

                            <select class="form-control" name="channel">

                                <?php
                                $query = misc\mysql\query("SELECT * FROM `chats` WHERE `app` = ?", [$_SESSION['app']]);
                                $rows = array();
                                while ($r = mysqli_fetch_assoc($query->result)) {
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

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="clearchannel">Clear</button>

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
    <div id="mute-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header d-flex align-items-center">

                    <h4 class="modal-title">Mute User</h4>

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

                            <label for="recipient-name" class="control-label">Unit Of Time
                                Muted:</label>

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

                            <label for="recipient-name" class="control-label">Time
                                Muted:</label>

                            <input class="form-control" name="time" placeholder="Multiplied by selected unit of time muted" required>

                        </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-danger" name="muteuser">Mute</button>

                    </form>

                </div>

            </div>

        </div>

    </div>
    <table id="kt_datatable_chats" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Name</th>
                <th>Delay</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>



            <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `chats` WHERE `app` = ?", [$_SESSION['app']]);
                $rows = array();
                while ($r = mysqli_fetch_assoc($query->result)) {
                    $rows[] = $r;
                }
                foreach ($rows as $row) {
                    $chan = $row['name'];
            ?>



                    <tr>



                        <td><?php echo $chan; ?></td>



                        <td><?php echo dashboard\primary\time2str(time() - $row["delay"]); ?></td>



                        <form method="POST">
                            <td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                <!--begin::Menu-->
                                <div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletechan" value="<?php echo $chan; ?>">Delete</button>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <button class="btn menu-link px-3" style="font-size:0.95rem;" name="editchan" value="<?php echo $chan; ?>">Edit</button>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                            </td>
                    </tr>
                    </form>

            <?php
                }
            }
            ?>

        </tbody>

    </table>

    <br><br>


    <table id="kt_datatable_messages" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
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
                $query = misc\mysql\query("SELECT * FROM `chatmsgs` WHERE `app` = ?", [$_SESSION['app']]);
                $rows = array();
                while ($r = mysqli_fetch_assoc($query->result)) {
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
                            <td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                <!--begin::Menu-->
                                <div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletemsg" value="<?php echo $row["id"]; ?>">Delete</button>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#mute-user" onclick="muteuser('<?php echo $user; ?>')">Mute</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                            </td>
                    </tr>
                    </form>

            <?php
                }
            }
            ?>

        </tbody>

    </table>

    <script>
        function muteuser(key) {

            var muteuser = $('.muteuser');

            muteuser.attr('value', key);

        }
    </script>
</div>




<!--end::Container-->