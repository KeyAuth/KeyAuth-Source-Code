<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager") {
    misc\auditLog\send("Attempted (and failed) to view audit logs.");
    dashboard\primary\error("Managers aren't allowed to view audit logs.");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['delauditLogs'])){
   $resp = misc\auditLog\deleteAll();
   match($resp){
    'failure' => dashboard\primary\error("Failed to delete all audit logs"),
    'success' => dashboard\primary\success("Successfully deleted all audit logs!"),
    default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
   };
}
if (isset($_POST['exportAuditLogs'])) {
        echo "<meta http-equiv='Refresh' Content='0; url=download-types.php?type=auditLog'>";
        // get all rows, put in text file, download text file, delete text file.
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Audit Logs</h1>
            <p class="text-xs text-gray-500">Logs from Manager and Reseller actions. <a
                    href="https://keyauth.readme.io/reference/audit-logs" target="_blank"
                    class="text-blue-600 hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <form method="POST">
                        <!-- Audit Logs Functions -->
                        <button name="exportAuditLogs"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-download mr-2 mt-1"></i>Export Audit Logs
                        </button>
                        <!-- End Audit Logs Functions -->
                        <br>
                        <!-- Delete Audit Logs Functions -->
                        <button type="button"
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="delete-all-audit-logs-modal"
                            data-modal-target="delete-all-audit-logs-modal">
                            <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Audit Logs
                        </button>
                        <!-- End Delete Audit Logs Functions -->
                    </form>

                    <!-- Delete All Audit Logs Modal -->
                    <div id="delete-all-audit-logs-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            Audit logs. Are you sure you want to continue?
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your Audit Logs? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-audit-logs-modal" name="delauditLogs"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-audit-logs-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Audit Logs Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_webhooks" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">User</th>
                                    <th class="px-6 py-3">Event</th>
                                    <th class="px-6 py-3">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if ($_SESSION['app']) {
                                        $query = misc\mysql\query("SELECT * FROM `auditLog` WHERE `app` = ?", [$_SESSION['app']]);
                                        if ($query->num_rows > 0) {
                                            while ($row = mysqli_fetch_array($query->result)) {

                                                echo "<tr>";

                                                echo "  <td>" . $row["user"] . "</td>";

                                                echo "  <td>" . $row["event"] . "</td>";

                                                echo "  <td><script>document.write(convertTimestamp(" . $row["time"] . "));</script></td>";

                                                echo "</tr>";
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
