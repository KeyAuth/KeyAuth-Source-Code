<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if($role == "Manager" && !($permissions & 256)) {
    misc\auditLog\send("Attempted (and failed) to view logs.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['dellogs'])) {
    $resp = misc\logging\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all logs!"),
        'success' => dashboard\primary\success("Successfully deleted all logs!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['dllogs'])){
    echo "<meta http-equiv='Refresh' Content='0; url=download-types.php?type=logs'>";
}

if($_SESSION['role'] == "tester") {
    $query = misc\mysql\query("SELECT count(*) AS 'numLogs' FROM `logs` WHERE `logapp` = ?",[$_SESSION['app']]);
    $row = mysqli_fetch_array($query->result);
    $numLogs = $row["numLogs"];
    if($numLogs >= 20) {
        ?><div class="alert alert-danger" role="alert">You have hit 20 logs! Upgrade to developer or seller to store
    more logs.</div><?php
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Logs</h1>
            <p class="text-xs text-gray-500">Keep track of user actions. <a
                    href="https://keyauth.readme.io/reference/logs-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div id="alert" class="flex items-center p-4 mb-4 text-yellow-500 rounded-lg bg-[#09090d]" role="alert">
                <span class="sr-only">Info</span>
                <div class="ml-3 text-sm font-medium text-yellow-500">
                    The maximum log message length is 275 characters. Logs are automatically deleted after 1 month. If
                    you set Discord webhook at <a
                        href="https://keyauth.cc/app/?page=app-settings">https://keyauth.cc/app/?page=app-settings</a>,
                    logs will go to Discord and not display on our site.
                </div>
            </div>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <form method="POST">
                        <!-- Logs Functions -->
                        <button name="dllogs"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-download mr-2 mt-1"></i>Export Logs
                        </button>
                        <!-- End Logs Functions -->

                        <br>

                        <!-- Delete Logs Functions -->
                        <button type="button"
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="delete-all-logs-modal" data-modal-target="delete-all-logs-modal">
                            <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Logs
                        </button>
                        <!-- End Delete Logs Functions -->
                    </form>
                    <!-- Delete All Logs Modal -->
                    <div id="delete-all-logs-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of
                                            your logs. <b>This can
                                                NOT be undone</b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                                        you want
                                        to
                                        delete your logs?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-logs-modal" name="dellogs"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-logs-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Logs Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_logs" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Log Date</th>
                                    <th class="px-6 py-3">Log Data</th>
                                    <th class="px-6 py-3">Credential</th>
                                    <th class="px-6 py-3">Device Name</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
