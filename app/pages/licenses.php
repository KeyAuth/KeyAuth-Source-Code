<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}

if ($role == "Manager" && !($permissions & 1)) {
    misc\auditLog\send("Attempted (and failed) to view licenses.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("You must select an app first.");
    header("location: ./?page=manage-apps");
    die();
}

if (isset($_POST['unbankey'])) {
    $resp = misc\license\unban($_POST['unbankey']);
    match ($resp) {
        'failure' => dashboard\primary\error("Failed to unban license!"),
        'success' => dashboard\primary\success("Successfully unbanned license!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['genkeys'])) {
    if (empty(trim($_POST['mask']))) {
        dashboard\primary\error("You must specify a key mask.");
    } else {
        $key = misc\license\createLicense($_POST['amount'], $_POST['mask'], $_POST['duration'], $_POST['level'], $_POST['note'], $_POST['expiry']);
        switch ($key) {
            case 'max_keys':
                dashboard\primary\error("You can only generate 100 licenses at a time");
                break;
            case 'tester_limit':
                dashboard\primary\error("Tester plan only allows for 10 licenses, please upgrade!");
                break;
            case 'dupe_custom_key':
                dashboard\primary\error("Can't do custom key with amount greater than one");
                break;
            default:
                $mask = misc\etc\sanitize($_POST['mask']);
                $amount = intval($_POST['amount']);
                $level = misc\etc\sanitize($_POST['level']);
                $note = misc\etc\sanitize($_POST['note']);
                $duration = misc\etc\sanitize($_POST['duration']);
                $expiry = misc\etc\sanitize($_POST['expiry']);

                misc\mysql\query("UPDATE `apps` SET `format` = ?,`amount` = ?,`lvl` = ?,`note` = ?,`duration` = ?,`unit` = ? WHERE `secret` = ?", [$mask, $amount, $level, $note, $duration, $expiry, $_SESSION['app']]);
                if ($_POST['amount'] > 1) {
                    $_SESSION['keys_array'] = $key;
                } else {
                    echo "<script>navigator.clipboard.writeText('" . array_values($key)[0] . "');</script>";
                    dashboard\primary\success("License Created And Copied To Clipboard!");
                }
                break;
        }
    }
}

if (isset($_POST['editkey'])) {
    $key = misc\etc\sanitize($_POST['editkey']);
    $query = misc\mysql\query("SELECT * FROM `keys` WHERE `key` = ? AND `app` = ?",[$key, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Key not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($query->result);
?>
<!-- Edit User Modal -->
<div id="edit-key-modal" tabindex="-1" aria-hidden="true"
    class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
            <div class="px-6 py-6 lg:px-8">
                <h3 class="mb-4 text-xl font-medium text-white-900">Edit License</h3>
                <form class="space-y-6" method="POST">
                    <div>

                        <div class="relative mb-4">
                            <input type="text" id="level" name="level"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " value="<?= $row['level']; ?>" autocomplete="on">
                            <label for="level"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Level:</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="expiry" name="expiry"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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

                        <div class="relative mb-4">
                            <input type="text" id="duration" name="duration"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" ">
                            <label for="duration"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Duration:</label>
                        </div>

                        <div class="relative mb-4">
                            <input type="text" id="editNote" name="editNote"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" ">
                            <label for="editNote"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">New
                                Note:</label>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">

                        <button name="savekey"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            value="<?= $key; ?>">Save Changes</button>

                        <button
                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            onClick="window.location.href=window.location.href">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit User Modal -->
<?php
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 lang class="text-xl font-semibold text-white-900 sm:text-2xl">Licenses</h1>
            <p class="text-xs text-gray-500">Licenses allow your users to register on your application.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <form method="POST">
                        <!-- Key Functions -->
                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="create-key-modal" data-modal-target="create-key-modal">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i> Create Keys
                        </button>

                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="add-time-modal" data-modal-target="add-time-modal">
                            <i class="lni lni-timer mr-2 mt-1"></i>Add Time To Unused Keys
                        </button>

                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="import-key-modal" data-modal-target="import-key-modal">
                            <i class="lni lni-upload mr-2 mt-1"></i>Import Keys
                        </button>

                        <button name="dlkeys"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-download mr-2 mt-1"></i>Export Keys
                        </button>
                    </form>
                    <!-- End Key Functions -->

                    <!-- Delete Key Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-keys-modal" data-modal-target="delete-all-keys-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Keys
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-used-keys-modal" data-modal-target="delete-all-used-keys-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Used Keys
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-unused-keys-modal"
                        data-modal-target="delete-all-unused-keys-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i></i>Delete All Unused Keys
                    </button>
                    <!-- End Delete Key Functions -->

                    <?php
    if (isset($_SESSION['keys_array'])) {
        $list = $_SESSION['keys_array'];
        $keys = NULL;
        for ($i = 0; $i < count($list); $i++) {
            $keys .= "" . $list[$i] . "<br>";
        }
        echo "<div class=\"card\"> <div class=\"card-body\" id=\"multi-keys\"> $keys </div> </div> <br>";
        echo "<button onclick=\"copyToClipboard()\" class=\"inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200\">Copy new licenses</button>";
        unset($_SESSION['keys_array']);
    }
    ?>

                    <!-- Create Key Modal -->
                    <div id="create-key-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <?php
                            $query = misc\mysql\query("SELECT `format`, `amount`, `lvl`, `note`, `duration`, `unit` FROM `apps` WHERE `secret` = ?", [$_SESSION['app']]);
                            $row = mysqli_fetch_array($query->result);

                            $format = $row['format'];
                            $amt = $row['amount'];
                            $lvl = $row['lvl'];
                            $note = $row['note'];
                            $dur = $row['duration'];
                            $unit = $row['unit'];
                            ?>
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="text-xl font-medium text-white-900">Create A New Key</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" min="1" max="100" id="amount"
                                                    name="amount"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on"
                                                    value="<?php if (!is_null($amt)) {                                                                                                                                                                                                                                                                                echo $amt;                                                                                                                                                                                                                                                                         } ?>"
                                                    required data-popover-target="amount-popover">
                                                <label for="amount"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Amount</label>

                                                <?php dashboard\primary\popover("amount-popover", "License Amount", "The amount of licenses you would like to create."); ?>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" maxlength="49" id="mask" name="mask"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="*****-*****-*****-*****" autocomplete="on"
                                                    value="<?php if (!is_null($format)) { echo $format; } else { echo "*****-*****-*****-*****-*****"; } ?>"
                                                    required data-popover-target="mask-popover">
                                                <label for="mask"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Mask</label>

                                                <?php dashboard\primary\popover("mask-popover", "License Mask", "The format of the license. You can use * to generate random characters."); ?>
                                            </div>

                                            <div class="flex items-center mb-4">
                                                <input id="lowercaseLetters" name="lowercaseLetters" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked data-popover-target="lowercase-popover">
                                                <label for="lowercaseLetters"
                                                    class="ml-2 text-sm font-medium text-white-900">Include
                                                    Lowercase Letters</label>

                                                <?php dashboard\primary\popover("lowercase-popover", "Lowercase Letters", "Include lowercase letters in your license."); ?>
                                            </div>
                                            <div class="flex items-center">
                                                <input checked id="capitalLetters" name="capitalLetters" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked data-popover-target="capital-popover">
                                                <label for="capitalLetters"
                                                    class="ml-2 text-sm font-medium text-white-900">Include
                                                    Uppercase Letters</label>

                                                <?php dashboard\primary\popover("capital-popover", "Capital Letters", "Include capital letters in your license."); ?>
                                            </div>

                                            <div class="relative mb-4 pt-3">
                                                <select id="level" name="level"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                    data-popover-target="level-popover">
                                                    <?php
                                                    $query = misc\mysql\query("SELECT DISTINCT `level` FROM `subscriptions` WHERE `app` = ? ORDER BY `level` ASC", [$_SESSION['app']]);
                                                    if ($query->num_rows > 0) {
                                                        while ($row = mysqli_fetch_array($query->result)) {
                                                            $queryName = misc\mysql\query("SELECT `name` FROM `subscriptions` WHERE `level` = ? AND `app` = ?", [$row["level"], $_SESSION['app']]);
                                                            $name = " (";
                                                            $count = 0;
                                                            while ($rowSubs = mysqli_fetch_array($queryName->result)) {
                                                                $count++;
                                                                if ($count > 1) {
                                                                    $name .= ", " . $rowSubs["name"];
                                                                } else {
                                                                    $name .= $rowSubs["name"];
                                                                }
                                                            }
                                                            $name .= ")";
                                                    ?>

                                                    <option <?= $lvl == $row["level"] ? 'selected="selected"' : ''; ?>
                                                        value="<?= $row["level"]; ?>">
                                                        <?= $row["level"] . $name; ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                                <?php dashboard\primary\popover("level-popover", "License Level", "The level/subscription you would like to assign to your license(s)."); ?>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" maxlength="69" id="note" name="note"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on"
                                                    value="<?php if (!is_null($note)) {                                                                                                                                                                                                                                               } ?>"
                                                    data-popover-target="note-popover">
                                                <label for="note"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Note</label>

                                                <?php dashboard\primary\popover("note-popover", "License Note", "A unique message for a license."); ?>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="expiry" name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                    data-popover-target="expiry-popover">
                                                    <option value="1" <?= $unit == 1 ? 'selected="selected"' : ''; ?>>
                                                        Seconds
                                                    </option>
                                                    <option value="60" <?= $unit == 60 ? 'selected="selected"' : ''; ?>>
                                                        Minutes
                                                    </option>
                                                    <option value="3600"
                                                        <?= $unit == 3600 ? 'selected="selected"' : ''; ?>>
                                                        Hours
                                                    </option>
                                                    <option value="86400"
                                                        <?= $unit == 86400 ? 'selected="selected"' : ''; ?>>
                                                        Days
                                                    </option>
                                                    <option value="604800"
                                                        <?= $unit == 604800 ? 'selected="selected"' : ''; ?>>
                                                        Weeks
                                                    </option>
                                                    <option value="2629743"
                                                        <?= $unit == 2629743 ? 'selected="selected"' : ''; ?>>
                                                        Months
                                                    </option>
                                                    <option value="31556926"
                                                        <?= $unit == 31556926 ? 'selected="selected"' : ''; ?>>
                                                        Years
                                                    </option>
                                                    <option value="315569260"
                                                        <?= $unit == 315569260 ? 'selected="selected"' : ''; ?>>
                                                        Lifetime
                                                    </option>
                                                </select>
                                                <label for="expiry"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Expiry Unit</label>

                                                <?php dashboard\primary\popover("expiry-popover", "License Expiry (unit)", "The unit the license will expire in."); ?>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" maxlength="4" id="duration"
                                                    name="duration"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" pattern="\d*"
                                                    value="<?php if (!is_null($dur)) {                                                                                                                                                                                                                                                                                 } ?>"
                                                    required data-popover-target="duration-popover">
                                                <label for="duration"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Duration</label>

                                                <?php dashboard\primary\popover("duration-popover", "License Expiry (duration)", "The duration the license will expire in. (Unit * Duration = Expiry)"); ?>
                                            </div>
                                        </div>
                                        <button type="submit" name="genkeys"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Generate
                                            Keys</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create Key Modal -->

                    <!-- Add Time To Key Modal -->
                    <div id="add-time-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Add Time To Unused Licenses</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4  ">
                                                <select id="expiry" name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <option value="1" selected>Seconds</option>
                                                    <option value="60">Minutes</option>
                                                    <option value="3600">Hours</option>
                                                    <option value="86400">Days</option>
                                                    <option value="604800">Weeks</option>
                                                    <option value="2629743">Months</option>
                                                    <option value="31556926">Years</option>
                                                    <option value="315569260">Lifetime</option>
                                                </select>
                                                <label for="expiry"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Unit
                                                    of Time To Add</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" min="1" id="time" name="time"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    autocomplete="on" placeholder="" required>
                                                <label for="time"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Time
                                                    To Add</label>
                                            </div>

                                        </div>
                                        <button name="addtime"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add
                                            Time</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Add Time To Key Modal -->

                    <!-- Import Keys Modal -->
                    <div id="import-key-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Import Licenses .json</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST" enctype="multipart/form-data">
                                        <div class="relative">
                                            <input
                                                class="block w-full text-sm text-gray-400 border border-gray-700 rounded-lg cursor-pointer focus:outline-none"
                                                id="file_input" name="file_input" type="file">
                                        </div>
                                        <button type="submit" name="importkeysFile"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Import
                                            Licenses</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Import Keys Modal -->

                    <!-- Delete All Keys Modal -->
                    <div id="delete-all-keys-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> This will not delete users (prevent
                                            them from logging in). Go to https://keyauth.cc/app/?page=users for that.
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your keys? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-keys-modal" name="delkeys"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-keys-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Keys Modal -->

                    <!-- Delete All Used Keys Modal -->
                    <div id="delete-all-used-keys-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> This will not delete users (prevent
                                            them from logging in). Go to https://keyauth.cc/app/?page=users for that.
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your used keys? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-used-keys-modal" name="deleteallused"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-used-keys-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Used Keys Modal -->

                    <!-- Delete All Unused Keys Modal -->
                    <div id="delete-all-unused-keys-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> This will not delete users (prevent
                                            them from logging in). Go to https://keyauth.cc/app/?page=users for that.
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your unused keys? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-unused-keys-modal" name="deleteallunused"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-unused-keys-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Unused Keys Modal -->

                    <!-- Delete Key Modal -->
                    <div id="del-key" tabindex="-1"
                        class="modal fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> This will not delete the user
                                            (prevent them from logging in) unless you check Delete User Too
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        this key? This can not be undone.</h3>
                                    <form method="POST">
                                        <div class="flex items-center mb-4">
                                            <input id="delUserToo" name="delUserToo" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                checked>
                                            <label for="delUserToo"
                                                class="ml-2 text-sm font-medium text-white-900">Delete user too</label>
                                        </div>

                                        <button name="deletekey"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2 delkey">
                                            Yes, I'm sure
                                        </button>
                                        <button type="button" onclick="closeModal('del-key')"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete Key Modal -->

                    <!-- Ban License Modal Actions-->
                    <div id="ban-key-modal" tabindex="-1"
                        class="modal fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-700 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium text-red-400">Notice! This will not ban the user
                                                (prevent them from logging in) unless you check Ban User Too</b></span>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to ban this
                                        license?
                                        <form method="POST">
                                            <div>
                                                <div class="relative mb-4 pt-4">
                                                    <input type="text" id="reason" name="reason"
                                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer focus:border-red-700"
                                                        placeholder=" " autocomplete="on" required>
                                                    <label for="reason"
                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-red-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Ban
                                                        Reason</label>
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-4 pt-4">
                                                <input id="banUserToo" name="banUserToo" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked>
                                                <label for="banUserToo"
                                                    class="ml-2 text-sm font-medium text-white-900">Ban User
                                                    Too?</label>
                                            </div>
                                            <button name="bankey"
                                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2 bankey">
                                                Yes, I'm sure
                                            </button>
                                            <button type="button" onclick="closeModal('ban-key-modal')"
                                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                                cancel</button>
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ban License Modal Actions-->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_licenses" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="border-2 border-gray-200 text-blue-700 px-7">
                                    <th scope="col" class="px-6 py-3">Key</th>
                                    <th scope="col" class="px-6 py-3">Creation Date</th>
                                    <th scope="col" class="px-6 py-3">Generated By</th>
                                    <th scope="col" class="px-6 py-3">Duration</th>
                                    <th scope="col" class="px-6 py-3">Note</th>
                                    <th scope="col" class="px-6 py-3">Used On</th>
                                    <th scope="col" class="px-6 py-3">Used By</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <!-- Include the jQuery library -->


                    <script>
                    $(document).keydown(function(event) {
                        if (event.key === 'Escape') {
                            $('[data-modal]').each(function() {
                                if (!$(this).hasClass('hidden')) {
                                    const modalName = $(this).data('modal');
                                    closeModal(modalName);
                                }
                            });
                        }
                    });

                    function bankey(key) {
                        var bankey = $('.bankey');
                        bankey.attr('value', key);

                        openModal('ban-key-modal');
                    }

                    function delkey(key) {
                        var delkey = $('.delkey');
                        delkey.attr('value', key);

                        openModal('del-key');
                    }

                    function copyToClipboard() {
                        const cardBodyContent = document.getElementById('multi-keys').innerText;

                        const formattedContent = cardBodyContent.replace(/<br>/g, '\n');

                        const textarea = document.createElement('textarea');
                        textarea.value = formattedContent;

                        textarea.style.position = 'fixed';
                        textarea.style.opacity = 0;

                        document.body.appendChild(textarea);

                        textarea.select();
                        document.execCommand('copy');

                        document.body.removeChild(textarea);

                        alert('Copied new licenses!');
                    }
                    </script>
                    <!-- END TABLE -->

                </div>

                <?php
                if (isset($_POST['delkeys'])) {
                    if ($_SESSION['role'] == "tester") {
                        dashboard\primary\error("Free tester accounts can't delete keys, upgrade your account to delete keys!");
                        echo "<meta http-equiv='Refresh' Content='2'>";
                        return;
                    }
                    $resp = misc\license\deleteAll();
                    match ($resp) {
                        'failure' => dashboard\primary\error("Didn't find any keys!"),
                        'success' => dashboard\primary\success("Deleted All Keys!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }

                if (isset($_POST['addtime'])) {
                    $resp = misc\license\addTime($_POST['time'], $_POST['expiry']);
                    match ($resp) {
                        'failure' => dashboard\primary\error("Failed to add time!"),
                        'success' => dashboard\primary\success("Added time to unused licenses!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }

                if (isset($_POST['deleteallunused'])) {
                    if ($_SESSION['role'] == "tester") {
                        dashboard\primary\error("Free tester accounts can't delete keys, upgrade your account to delete keys!");
                        echo "<meta http-equiv='Refresh' Content='2'>";
                        return;
                    }
                    $resp = misc\license\deleteAllUnused();
                    match ($resp) {
                        'failure' => dashboard\primary\error("Didn't find any unused keys!"),
                        'success' => dashboard\primary\success("Deleted all unused keys!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }

                if (isset($_POST['deleteallused'])) {
                    if ($_SESSION['role'] == "tester") {
                        dashboard\primary\error("Free tester accounts can't delete keys, upgrade your account to delete keys!");
                        echo "<meta http-equiv='Refresh' Content='2'>";
                        return;
                    }
                    $resp = misc\license\deleteAllUsed();
                    match ($resp) {
                        'failure' => dashboard\primary\error("Didn't find any used keys!"),
                        'success' => dashboard\primary\success("Deleted all used keys!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }

                if (isset($_POST['savekey'])) {
                    $key = misc\etc\sanitize($_POST['savekey']);
                    $level = misc\etc\sanitize($_POST['level']);
                    $duration = misc\etc\sanitize($_POST['duration']);
                    $note = misc\etc\sanitize($_POST['editNote']);
                    if (!empty($duration)) {
                        $expiry = misc\etc\sanitize($_POST['expiry']);
                        $duration = $duration * $expiry;
                        misc\mysql\query("UPDATE `keys` SET `expires` = ? WHERE `key` = ? AND `app` = ?",[$duration, $key, $_SESSION['app']]);
                    }
                    misc\mysql\query("UPDATE `keys` SET `note` = ?, `level` = ? WHERE `key` = ? AND `app` = ?",[$note, $level, $key, $_SESSION['app']]);
                    misc\cache\purge('KeyAuthKey:' . $_SESSION['app'] . ':' . $key);
                    misc\cache\purge('KeyAuthKeys:' . $_SESSION['app']);
                    dashboard\primary\success("Successfully Updated Settings!");
                }

                if (isset($_POST['importkeysFile'])) {
                    if ($_FILES['file_input']['error'] == UPLOAD_ERR_OK) {
                        $fileContent = file_get_contents($_FILES['file_input']['tmp_name']);
                        $jsonArray = json_decode($fileContent, true);
                
                        if ($jsonArray === null) {
                            dashboard\primary\error("Invalid JSON format!");
                            echo "<meta http-equiv='Refresh' Content='2;'>";
                            return;
                        }
                
                        foreach ($jsonArray as $keyData) {
                            $key_format = $keyData['key'];
                            $level_format = $keyData['level'];
                            $expiry_format = $keyData['expiry'];
                
                            if (!isset($key_format) || $key_format == '' || !isset($level_format) || $level_format == '' || !isset($expiry_format) || $expiry_format == '') {
                                dashboard\primary\error("Invalid Format!");
                                echo "<meta http-equiv='Refresh' Content='2;'>";
                                return;
                            }
                
                            $expiry = $expiry_format * 86400;
                            misc\mysql\query("INSERT INTO `keys` (`key`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES (?, ?,'Not Used', ?, ?, ?, ?)", [$key_format, $expiry, $level_format, $_SESSION['username'], time(), $_SESSION['app']]);
                        }
                
                        dashboard\primary\success("Successfully imported licenses!");
                    } else {
                        dashboard\primary\error("File upload failed!");
                        echo "<meta http-equiv='Refresh' Content='2;'>";
                        return;
                    }
                }

                if (isset($_POST['deletekey'])) {
                    if ($_SESSION['role'] == "tester") {
                        dashboard\primary\error("Free tester accounts can't delete keys, upgrade your account to delete keys!");
                        echo "<meta http-equiv='Refresh' Content='2'>";
                        return;
                    }
                    $userToo = ($_POST['delUserToo'] == "on") ? 1 : 0;
                    $resp = misc\license\deleteSingular($_POST['deletekey'], $userToo);
                    match ($resp) {
                        'failure' => dashboard\primary\error("Failed to delete license!"),
                        'success' => dashboard\primary\success("Successfully deleted license!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }

                if (isset($_POST['dlkeys'])) {
                    echo "<meta http-equiv='Refresh' Content='0; url=download-types.php?type=licenses'>";
                    // get all rows, put in text file, download text file, delete text file.
                }

                if (isset($_POST['bankey'])) {
                    $userToo = ($_POST['banUserToo'] == "on") ? 1 : 0;
                    $resp = misc\license\ban($_POST['bankey'], $_POST['reason'], $userToo);
                    match ($resp) {
                        'failure' => dashboard\primary\error("Failed to ban license!"),
                        'success' => dashboard\primary\success("Successfully banned license!"),
                        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
                    };
                }
