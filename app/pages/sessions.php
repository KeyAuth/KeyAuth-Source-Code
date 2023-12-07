<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if($role == "Manager" && !($permissions & 16)) {
    misc\auditLog\send("Attempted (and failed) to view sessions.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['killall'])) {
    $resp = misc\session\killAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to kill all sessions!"),
        'success' => dashboard\primary\success("Successfully killed all sessions!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['kill'])) {
    $resp = misc\session\killSingular($_POST['kill']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to kill session!"),
        'success' => dashboard\primary\success("Successfully killed session!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Sessions</h1>
            <p class="text-xs text-gray-500">All of your active sessions. (Active Users) <a
                    href="https://keyauth.readme.io/reference/sessions-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Session Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="kill-all-sessions-modal" data-modal-target="kill-all-sessions-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Kill All Sessions
                    </button>
                    <!-- End Sessions Functions -->

                    <!--Kill All Sessions Modal -->
                    <div id="kill-all-sessions-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to kill all of
                                            your active sessions. Requiring everyone to log into your app again.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                                        you want
                                        to
                                        kill your active sessions?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="kill-all-sessions-modal" name="killall"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="kill-all-sessions-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Kill All Sessions Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_sessions" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Credentials</th>
                                    <th class="px-6 py-3">Expires</th>
                                    <th class="px-6 py-3">Authenticated</th>
                                    <th class="px-6 py-3">IP Adress</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                    
