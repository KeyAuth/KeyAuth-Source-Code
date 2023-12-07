<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if($role == "Manager" && !($permissions & 32)) {
    misc\auditLog\send("Attempted (and failed) to view webhooks.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['genwebhook'])) {

    if ($_SESSION['role'] == "tester") {
        dashboard\primary\error("You must upgrade to developer or seller to use webhooks!");
    } else {
        $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
        $resp = misc\webhook\add($_POST['webhookname'], $_POST['baselink'], $_POST['useragent'], $authed);
        match($resp){
            'invalid_url' => dashboard\primary\error("URL isn't a valid URL"),
            'no_local' => dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet!"),
            'failure' => dashboard\primary\error("Failed to add webhook!"),
            'success' => dashboard\primary\success("Successfully added webhook!"),
            'empty_webhookname' => dashboard\primary\error("Wehbook name can not be empty!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
}

if (isset($_POST['deletewebhook'])) {
    $resp = misc\webhook\deleteSingular($_POST['deletewebhook']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete webhook!"),
        'success' => dashboard\primary\success("Successfully deleted webhook!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['delallwebhooks'])){
    $resp = misc\webhook\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all webhooks!"),
        'success' => dashboard\primary\success("Successfully deleted all webhooks!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['editwebhook'])) {
    $webhook = misc\etc\sanitize($_POST['editwebhook']);

    $query = misc\mysql\query("SELECT * FROM `webhooks` WHERE `webid` = ? AND `app` = ?",[$webhook, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Webhook not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($query->result);

    $baselink = $row["baselink"];
    $useragent = $row["useragent"];

    echo  '
    <div id="edit-webhook-modal" tabindex="-1" aria-hidden="true"
        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit Webhook</h3>
                    <form class="space-y-6" method="POST">
                        <div>

                        <div class="relative mb-4">
                        <input type="text" id="baselink" name="baselink"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                            placeholder=" " autocomplete="on" value="' . $baselink . '" required>
                        <label for="baselink"
                            class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Base URL:</label>
                            <input type="hidden" name="webhook" value="' . $webhook . '">
                    </div>

                    <div class="relative mb-4">
                    <input type="text" id="useragent" name="useragent"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                        placeholder=" " autocomplete="on" value="' . $useragent . '" required>
                    <label for="useragent"
                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User-Agent:</label>
                </div>
                       
                        </div>
                        <button name="savewebhook"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" value="' . $file . '">Save
                            Changes</button>
                        <button
                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onClick="window.location.href=window.location.href">Cancel
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Account Modal -->';
}

if (isset($_POST['savewebhook'])) {
    $webhook = misc\etc\sanitize($_POST['webhook']);

    $baselink = misc\etc\sanitize($_POST['baselink']);
    $useragent = misc\etc\sanitize($_POST['useragent']);

    if (!filter_var($baselink, FILTER_VALIDATE_URL)) {
        dashboard\primary\error("URL isn't a valid URL");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    if(str_contains($baselink, "localhost") || str_contains($baselink, "127.0.0.1")) {
        dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    misc\mysql\query("UPDATE `webhooks` SET `baselink` = ?,`useragent` = ? WHERE `webid` = ? AND `app` = ?",[$baselink, $useragent, $webhook, $_SESSION['app']]);

    dashboard\primary\success("Successfully Updated Settings!");
    misc\cache\purge('KeyAuthWebhook:' . $_SESSION['app'] . ':' . $webhook);
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Webhooks</h1>
            <p class="text-xs text-gray-500">Send and receive secure requests. <a
                    href="https://keyauth.readme.io/reference/webhooks-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">

                    <!-- Alert Box -->
                    <div id="alert-4" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ml-3 text-sm font-medium text-yellow-500">
                            People often this mistake this for Discord webhooks. Please view our <a
                                href="https://keyauth.readme.io/reference/webhooks-1"
                                class="font-semibold underline hover:no-underline">Documentation</a> to learn how to
                            send Discord webhooks.
                        </div>
                    </div>
                    <!-- End Alert Box -->

                    <!-- Webhooks Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-webhook-modal" data-modal-target="create-webhook-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Webhook
                    </button>
                    <!-- End Webhooks Functions -->

                    <br>

                    <!-- Delete Webhooks Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-webhooks-modal" data-modal-target="delete-all-webhooks-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Webhooks
                    </button>
                    <!-- End Delete Webhooks Functions -->

                    <!-- Create Create A Webhook Modal -->
                    <div id="create-webhook-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Generate A Webhook</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="webhookname" name="webhookname"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="webhookname"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Webhook
                                                    Name (optional)</label>
                                            </div>
                                            <div class="relative mb-4">
                                                <input type="text" id="baselink" name="baselink"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="baselink"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Webhook
                                                    Endpoint</label>
                                            </div>
                                            <div class="relative mb-4">
                                                <input type="text" id="useragent" name="useragent"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="useragent"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User
                                                    Agent (Default is KeyAuth)</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input checked id="authed" type="checkbox" name="authed"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500   focus:ring-2  ">
                                                <label for="authed"
                                                    class="ml-2 text-sm font-medium text-white-900 ">Authenticated</label>
                                            </div>
                                        </div>
                                        <button type="submit" name="genwebhook"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Generate
                                            Webhook</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create A Webhook Modal -->

                    <!-- Delete All Webhooks Modal -->
                    <div id="delete-all-webhooks-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            webhooks. This can not be undone.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your webhooks?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-webhooks-modal" name="delallwebhooks"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-webhooks-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Webhooks Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_webhooks" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Endpoint</th>
                                    <th class="px-6 py-3">User-Agent</th>
                                    <th class="px-6 py-3">Authenticated</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `webhooks` WHERE `app` = ?",[$_SESSION['app']]);
                if ($query->num_rows > 0) {
                    while ($row = mysqli_fetch_array($query->result)) {

                        echo "<tr>";

                        echo "  <td>" . $row["webid"] . "</td>";

                        echo "  <td><span class=\"blur-sm hover:blur-none\">" . $row["baselink"] . "</span></td>";

                        echo "  <td>" . $row["useragent"] . "</td>";

                        echo "  <td>" . (($row["authed"] ? 1 : 0) ? 'True' : 'False') . "</td>";

                        echo '<form method="POST">
                                <td>

                                <div x-data="{ open: false }" class="z-0">
                                <button x-on:click="open = true" class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4" type="button">
                                        <span class="mr-1">Actions</span>
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"  style="margin-top:3px">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                </button>
                                <ul x-show="open" x-on:click.away="open = false" class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1" style="min-width:15rem">
                                        <li>
                                                <button name="deletewebhook" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                value="' . $row["webid"] . '">
                                                Delete Webhook
                                                </button>
                                        </li>
                                        <li>
                                                <button name="editwebhook" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                value="' . $row["webid"] . '">
                                                Edit Webhook
                                                </button>
                                        </li>
                                        </ul>
                                        </div>
                                    </tr>
                                </td>
                              </form>';
                    }
                }
            }

            ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
