<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
	die();
}
if($role == "Manager" && !($permissions & 1)) {
    misc\auditLog\send("Attempted (and failed) to view tokens.");
    dashboard\primary\error("You weren't granted permission to view this page!");
	die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
	die("Application not selected.");
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 lang class="text-xl font-semibold text-white-900 sm:text-2xl">Tokens</h1>
            <p class="text-xs text-gray-500">Given to users to manage blacklists.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_tokens" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">App</th>
                                    <th class="px-6 py-3">Token</th>
                                    <th class="px-6 py-3">Assigned</th>
                                    <th class="px-6 py-3">Banned</th>
                                    <th class="px-6 py-3">Reason</th>
                                    <th class="px-6 py-3">Hash</th>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
