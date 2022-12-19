<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
	die();
}
if($role == "Manager" && !($permissions & 32)) {
	die('You weren\'t granted permissions to view this page.');
}
if(!isset($_SESSION['app'])) {
	die("Application not selected.");
}

if (isset($_POST['genwebhook'])) {

    if ($_SESSION['role'] == "tester") {
        dashboard\primary\error("Free users can\'t create webhooks!");
    } else {
        $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
        $resp = misc\webhook\add($_POST['baselink'], $_POST['useragent'], $authed);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to add webhook!");
                break;
            case 'success':
                dashboard\primary\success("Successfully added webhook!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
}

if (isset($_POST['deletewebhook'])) {
    $resp = misc\webhook\deleteSingular($_POST['deletewebhook']);
    switch ($resp) {
        case 'failure':
            dashboard\primary\error("Failed to delete webhook!");
            break;
        case 'success':
            dashboard\primary\success("Successfully deleted webhook!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['editwebhook'])) {
    $webhook = misc\etc\sanitize($_POST['editwebhook']);

    $result = mysqli_query($link, "SELECT * FROM `webhooks` WHERE `webid` = '$webhook' AND `app` = '" . $_SESSION['app'] . "'");
    if (mysqli_num_rows($result) < 1) {
        mysqli_close($link);
        dashboard\primary\error("Webhook not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($result);

    $baselink = $row["baselink"];
    $useragent = $row["useragent"];

    echo '<div id="edit-webhook" class="modal show" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit Webhook</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Webhook Endpoint:</label>
                                                        <input type="text" class="form-control" name="baselink" value="' . $baselink . '" required>
														<input type="hidden" name="webhook" value="' . $webhook . '">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">User-Agent:</label>
                                                        <input type="text" class="form-control" name="useragent" value="' . $useragent . '" required>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="savewebhook">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>';
}

if (isset($_POST['savewebhook'])) {
    $webhook = misc\etc\sanitize($_POST['webhook']);

    $baselink = misc\etc\sanitize($_POST['baselink']);
    $useragent = misc\etc\sanitize($_POST['useragent']);

    mysqli_query($link, "UPDATE `webhooks` SET `baselink` = '$baselink',`useragent` = '$useragent' WHERE `webid` = '$webhook' AND `app` = '" . $_SESSION['app'] . "'");

    dashboard\primary\success("Successfully Updated Settings!");
    misc\cache\purge('KeyAuthWebhook:' . $_SESSION['app'] . ':' . $webhook);
}
?>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">

    <button data-bs-toggle="modal" type="button" data-bs-target="#create-webhook"
        class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i>
        Create Webhook</button>
    <br>


    <div id="create-webhook" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title">Add Webhooks</h4>
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
                    <form method="post">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Webhook Endpoint: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Webhooks can be used to send GET request with query paramaters from the KeyAuth server so you don't expose the link in your loader. The webhook function returns a string which is the response from the link. There is zero reason to send requests to links which need to be kept private in your loader without the webhok function. You run the risk of the link getting leaked."></i></label>
                            <input type="url" class="form-control" name="baselink"
                                placeholder="The Link You Want KeyAuth to Send Request to" required>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">User-Agent: <i
                                    class="fas fa-question-circle fa-lg text-white-50" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="This is useless to most people, but if the link requires a certain user agent to keep bad actors out, specify that user agent here."></i></label>
                            <input type="text" class="form-control" placeholder="Default: KeyAuth" name="useragent">
                        </div>
                        <div class="form-check">
                            <br>
                            <input class="form-check-input" name="authed" type="checkbox" id="flexCheckChecked" checked>
                            <label class="form-check-label" for="flexCheckChecked">
                                Authenticated <i class="fas fa-question-circle fa-lg text-white-50"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="If checked, KeyAuth will force user to be logged in to use."></i>
                            </label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" name="genwebhook">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <table id="kt_datatable_webhooks" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Webhook ID</th>
                <th>Webhook Endpoint</th>
                <th>User-Agent</th>
                <th>Authenticated</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if ($_SESSION['app']) {
                ($result = mysqli_query($link, "SELECT * FROM `webhooks` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {

                        echo "<tr>";

                        echo "  <td>" . $row["webid"] . "</td>";

                        echo "  <td>" . $row["baselink"] . "</td>";

                        echo "  <td>" . $row["useragent"] . "</td>";

                        echo "  <td>" . (($row["authed"] ? 1 : 0) ? 'True' : 'False') . "</td>";

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
					<button class="btn menu-link px-3" style="font-size:0.95rem;" name="deletewebhook" value="' . $row["webid"] . '">Delete</button>
				</div>
				<!--end::Menu item-->
				<!--begin::Menu item-->
				<div class="menu-item px-3">
					<button class="btn menu-link px-3" style="font-size:0.95rem;" name="editwebhook" value="' . $row["webid"] . '">Edit</button>
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
