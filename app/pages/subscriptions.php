<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 4)) {
    misc\auditLog\send("Attempted (and failed) to view subscriptions.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['addsub'])) {
    if (!is_numeric($_POST['level'])) {
        dashboard\primary\error("Level must be a number!");
    } else {
        $resp = misc\sub\add($_POST['subname'], $_POST['level']);
        match($resp){
            'failure' => dashboard\primary\error("Failed to create subscription!"),
            'success' => dashboard\primary\success("Successfully created subscription!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
}

if (isset($_POST['deletesub'])) {
    $resp = misc\sub\deleteSingular($_POST['deletesub']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete subscription"),
        'success' => dashboard\primary\success("Successfully deleted subscription"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['delallsubs'])){
    $resp = misc\sub\deleteall();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete subscriptions!"),
        'success' => dashboard\primary\success("Successfully deleted all subscriptions!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['editsub'])) {
    $subscription = misc\etc\sanitize($_POST['editsub']);
    $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `name` = ? AND `app` = ?", [$subscription, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Subscription not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($query->result);
    $level = $row["level"];
    echo  '
    <div id="edit-file-modal" tabindex="-1" aria-hidden="true"
        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit Subscription</h3>
                    <form class="space-y-6" method="POST">
                        <div>

                        <div class="relative mb-4">
                        <input type="text" inputmode="numeric" id="level" name="level"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                            placeholder=" " autocomplete="on" value="' . $level . '" maxlength="12" required>
                            <input type="hidden" name="subscription" value="' . $subscription . '">
                        <label for="level"
                            class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription Level:</label>
                    </div>

                    
                    </div>

                        <button name="savesub"
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
    <!-- End Edit Account Modal -->';
}
if (isset($_POST['savesub'])) {
    $subscription = misc\etc\sanitize($_POST['subscription']);
    $level = misc\etc\sanitize($_POST['level']);

    if (!is_numeric($_POST['level'])) {
        dashboard\primary\error("Level must be a number!");
    } else {
        misc\mysql\query("UPDATE `subscriptions` SET `level` = ? WHERE `name` = ? AND `app` = ?", [$level, $subscription, $_SESSION['app']]);
        if ($_SESSION['role'] == "seller") {
            misc\cache\purge('KeyAuthSubscriptions:' . $_SESSION['app']);
        }
        dashboard\primary\success("Successfully Updated Subscription!");
        echo "<meta http-equiv='Refresh' Content='2'>";
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Subscriptions</h1>
            <p class="text-xs text-gray-500">Subscriptions act as levels/tiers. <a
                    href="https://keyauth.readme.io/reference/subscriptions-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Subscripion Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-subscription-modal" data-modal-target="create-subscription-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Subscription
                    </button>
                    <!-- End Subscription Functions -->

                    <br>

                    <!-- Delete Subscription Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-subs-modal" data-modal-target="delete-all-subs-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Subscriptions
                    </button>
                    <!-- End Delete Subscription Functions -->

                    <!-- Create Subscription Modal -->
                    <div id="create-subscription-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Create A Subscription</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4">
                                                <input type="text" id="subname" name="subname"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="subname"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                                    Name</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" id="level" name="level"
                                                    maxlength="12"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="level"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                                    Level</label>
                                            </div>


                                        </div>
                                        <button type="submit" name="addsub"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create
                                            Subscription</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create Subscription Modal -->

                    <!-- Delete All Subscriptions Modal -->
                    <div id="delete-all-subs-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            subscriptions. This can not be undone.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your subscriptions?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-subs-modal" name="delallsubs"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-subs-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Keys Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_subs" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Subscription Name</th>
                                    <th class="px-6 py-3">License Level</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($_SESSION['app']) {
                                    $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ?", [$_SESSION['app']]);
                                    if ($query->num_rows > 0){
                                        while ($row = mysqli_fetch_array($query->result)){
                                            echo "<tr>";
                                            echo "<td>" . $row["name"] . "</td>";
                                            echo "<td>" . $row["level"] . "</td>";

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
                                                            <button name="deletesub" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                            value="' . $row["name"] . '">
                                                            Delete Subscription
                                                            </button>
                                                    </li>
                                                    <li>
                                                            <button name="editsub" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            value="' . $row["name"] . '">
                                                            Edit Subscription
                                                            </button>
                                                    </li>
                                            </ul>
                                            </div>
                                            </td>
                                            </tr>
                                        </form>';
                                        }
                                    }
                                }
                            ?>

                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                    
