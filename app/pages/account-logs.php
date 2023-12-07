<?php
if ($_SESSION['username'] == "demodeveloper" || $_SESSION['username'] == "demoseller") 
{
   dashboard\primary\error("Due to privacy purposes, demo accounts do not have access to account logs!");
   die();
}
?>
<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Account Logs</h1>
            <p class="text-xs text-gray-500">View the event history of your account.</p>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_account_logs"
                            class="w-full text-sm text-left text-gray-500">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">IP</th>
                                    <th class="px-6 py-3">User-Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = misc\mysql\query("SELECT * FROM `acclogs` WHERE `username` = ?", [$_SESSION['username']]);
                                    $rows = array();
                                    while ($r = mysqli_fetch_assoc($query->result)) 
                                    {
                                        $rows[] = $r;
                                    }

                                    foreach ($rows as $row) 
                                    {
                                    ?>
                                        <tr>
                                            <td>
                                                <script>
                                                    document.write(convertTimestamp(<?= $row["date"]; ?>));
                                                </script>
                                            </td>

                                            <td><a class="blur-sm hover:blur-none"><?= $row["ip"]; ?></a></td>
                                            <td><a class="blur-sm hover:blur-none"><?= $row["useragent"]; ?></a></td>
                                        </tr>
                                    <?php
                                    }
                                ?>
                            </tbody>
                        </table>
