<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if($role == "Manager" && !($permissions & 128)) {
    misc\auditLog\send("Attempted (and failed) to view vars.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['genvar'])) {
    if ($_SESSION['role'] == "tester") {
        if(strlen($_POST['vardata']) > 100) {
            dashboard\primary\error("Must upgrade to developer or seller to create variables longer than 100 characters!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }

        $query = misc\mysql\query("SELECT count(*) AS 'numVars' FROM `vars` WHERE `app` = ?",[$_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        $numVars = $row["numVars"];
        if($numVars >= 5) {
            dashboard\primary\error("Must upgrade to developer or seller to create more than 5 variables!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }
    }
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\variable\add($_POST['varname'], $_POST['vardata'], $authed);
    match($resp){
        'exists' => dashboard\primary\error("Variable name already exists!"),
        'too_long' => dashboard\primary\error("Variable too long! Must be 1000 characters or less"),
        'failure' => dashboard\primary\error("Failed to create variable"),
        'success' => dashboard\primary\success("Successfully created variable"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['delvars'])) {
    $resp = misc\variable\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all variables"),
        'success' => dashboard\primary\success("Successfully deleted all variables"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['deletevar'])) {
    $resp = misc\variable\deleteSingular($_POST['deletevar']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete variable"),
        'success' => dashboard\primary\success("Successfully deleted variable"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

// edit modal
if (isset($_POST['editvar'])) {
    $variable = misc\etc\sanitize($_POST['editvar']);

    $query = misc\mysql\query("SELECT * FROM `vars` WHERE `varid` = ? AND `app` = ?",[$variable, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Variable not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($query->result);

    $data = $row["msg"];

    echo  '
    <div id="edit-variable-modal" tabindex="-1" aria-hidden="true"
        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit Variable</h3>
                    <form class="space-y-6" method="POST">
                        <div>

                        <div class="relative mb-4">
                            <label for="msg" name="msg"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                Variable Data</label>
                            <textarea id="msg" name="msg" rows="4"
                                class="block p-2.5 w-full text-sm text-white-900 bg-[#0f0f17] rounded-lg border border-gray-700 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="" maxlength="1000" required>' . $data . '</textarea>
                                <input type="hidden" name="variable" value="' . $variable . '">
                        </div>

                        </div>
                        <div class="flex items-center mb-4">
                        <input id="authed" name="authed" type="checkbox"
                            class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            checked>
                        <label for="authed"
                            class="ml-2 text-sm font-medium text-white-900">Authenticated</label>
                    </div>

                        <button name="savevar"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save
                            Changes</button>
                        <button
                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onClick="window.location.href=window.location.href">Cancel
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Var Modal -->';
}

if (isset($_POST['savevar'])) {
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\variable\edit($_POST['variable'], $_POST['msg'], $authed);
    switch ($resp) {
        case 'success':
            misc\cache\purge('KeyAuthVar:' . $_SESSION['app'] . ':' . $_POST['variable']);
            dashboard\primary\success("Successfully edited variable!");
            break;
    }
    match($resp){
        'failure' => dashboard\primary\error("Failed to edit variable!"),
        'too_long' => dashboard\primary\error("Variable too long! Must be 1000 characters or less"),
        'success' => dashboard\primary\success("Successfully edited variable!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Global Variables</h1>
            <p class="text-xs text-gray-500">Pass, assign, obtain data globally. <a
                    href="https://keyauth.readme.io/reference/variables-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
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
                        These are global variables. You must use 'var()', not get/setvar()(aka user variables). Please
                        view our <a href="https://keyauth.readme.io/reference/variables-1"
                            class="font-semibold underline hover:no-underline">Documentation</a> to learn how to use
                        global variables.
                    </div>
                </div>
                <!-- End Alert Box -->

                <!-- Global Variable Functions -->
                <button
                    class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                    data-modal-toggle="set-global-var-modal" data-modal-target="set-global-var-modal">
                    <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Global Variable
                </button>
                <!-- End Global Variable Functions -->

                <br>

                <!-- Delete Global Variable Functions -->
                <button
                    class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                    data-modal-toggle="delete-all-vars-modal" data-modal-target="delete-all-vars-modal">
                    <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Variables
                </button>
                <!-- End Delete Global Variable Functions -->

                <!-- Set Global Var Modal -->
                <div id="set-global-var-modal" tabindex="-1" aria-hidden="true"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                            <div class="px-6 py-6 lg:px-8">
                                <h3 class="mb-4 text-xl font-medium text-white-900">Set Global Variable</h3>
                                <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                <form class="space-y-6" method="POST">
                                    <div>
                                        <div class="relative mb-4">
                                            <input type="text" id="varname" name="varname"
                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                placeholder=" " autocomplete="on" required>
                                            <label for="varname"
                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Variable
                                                Name</label>
                                        </div>

                                        <div class="relative mb-4">
                                            <label for="vardata" name="vardata"
                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                                Variable Data</label>
                                            <textarea id="vardata" name="vardata" rows="4"
                                                class="block p-2.5 w-full text-sm text-white-900 bg-[#0f0f17] rounded-lg border border-gray-700 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="" maxlength="1000" required></textarea>
                                        </div>
                                    </div>

                                    <div class="flex items-center mb-4">
                                        <input id="authed" name="authed" type="checkbox" 
                                            class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                            checked>
                                        <label for="authed"
                                            class="ml-2 text-sm font-medium text-white-900">Authenticated</label>
                                    </div>
                                    <button name="genvar"
                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Set
                                        Global Var</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Create Global Var Modal -->

                <!-- Delete All Vars Modal -->
                <div id="delete-all-vars-modal" tabindex="-1"
                    class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                            <div class="p-6 text-center">
                                <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                    role="alert">
                                    <span class="sr-only">Info</span>
                                    <div>
                                        <span class="font-medium">Notice!</span> You're about to delete all of your
                                        global variables. This can not be undone.
                                    </div>
                                </div>
                                <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                    all of your global variables?</h3>
                                <form method="POST">
                                    <button data-modal-hide="delete-all-vars-modal" name="delvars"
                                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                        Yes, I'm sure
                                    </button>
                                    <button data-modal-hide="delete-all-vars-modal" type="button"
                                        class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                        cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Delete All Vars Modal -->

                <!-- START TABLE -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                    <table id="kt_datatable_vars" class="w-full text-sm text-left text-white">
                        <thead>
                            <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                <th class="px-6 py-3">Variable Name</th>
                                <th class="px-6 py-3">Variable Data</th>
                                <th class="px-6 py-3">Autheticated</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
