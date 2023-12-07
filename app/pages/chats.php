<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 8)) {
    misc\auditLog\send("Attempted (and failed) to view chats.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['deletemsg'])) {
    $resp = misc\chat\deleteMessage($_POST['deletemsg']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete message!"),
        'success' => dashboard\primary\success("Successfully deleted message!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['muteuser'])) {
    $muted = misc\etc\sanitize($_POST['muted']);
    $time = misc\etc\sanitize($_POST['time']);
    $time = $time * $muted + time();

    $resp = misc\chat\muteUser($_POST['user'], $time);
    match($resp){
        'missing' => dashboard\primary\error("User doesn't exist!"),
        'failure' => dashboard\primary\error("Failed to mute user!"),
        'success' => dashboard\primary\success("Successfully muted user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['unmuteuser'])) {
    $resp = misc\chat\unMuteUser($_POST['user']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to unmute user!"),
        'success' => dashboard\primary\success("Successfully unmuted user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['clearchannel'])) {
    $resp = misc\chat\clearChannel($_POST['channel']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to clear channel!"),
        'success' => dashboard\primary\success("Successfully cleared channel!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['deleteallchatchannels'])){
    $resp = misc\chat\deleteAllChannels();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all chat channels!"),
        'success' => dashboard\primary\success("Successfully deleted all chat channels!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['editchan'])) {
    $chan = misc\etc\sanitize($_POST['editchan']);
    $query = misc\mysql\query("SELECT * FROM `chats` WHERE `name` = ? AND `app` = ?", [$chan, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Channel not found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
?>

<!-- Edit Chat Channel Modal -->
<div id="edit-chat-channel-modal" tabindex="-1" aria-hidden="true"
    class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
            <div class="px-6 py-6 lg:px-8">
                <h3 class="mb-4 text-xl font-medium text-white-900">Edit Chat Channel</h3>
                <form class="space-y-6" method="POST">
                    <div>
                        <div class="relative mb-4">
                            <select id="unit" name="unit"
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
                            <label for="unit"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Chat
                                Cooldown Unit</label>
                        </div>

                        <div class="relative mb-4">
                            <input type="text" inputmode="numeric" id="delay" name="delay"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on">
                            <label for="delay"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Cooldown
                                Time * By Unit</label>
                        </div>
                    </div>

                    <button name="savechan" value="<?= $chan; ?>"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save
                        Changes</button>

                    <button onClick="window.location.href=window.location.href"
                        class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Chat Channel Modal -->

<?php
}
if (isset($_POST['savechan'])) {
    $chan = misc\etc\sanitize($_POST['savechan']);
    $unit = misc\etc\sanitize($_POST['unit']);
    $delay = misc\etc\sanitize($_POST['delay']);
    $delay = $delay * $unit;
    $query = misc\mysql\query("UPDATE `chats` SET `delay` = ? WHERE `app` = ? AND `name` = ?", [$delay, $_SESSION['app'], $chan]);
    if ($query->affected_rows > 0) // check query impacted something, else show error
    {
        dashboard\primary\success("Successfully updated channel!");
    } else {
        dashboard\primary\error("Failed To update channel!");
    }
}
if (isset($_POST['deletechan'])) {
    $resp = misc\chat\deleteChannel($_POST['deletechan']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete channel!"),
        'success' => dashboard\primary\success("Successfully deleted channel!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['addchannel'])) {
    if ($_SESSION['role'] != "seller") {
        dashboard\primary\error("You must upgrade to seller to create chat channels");
    } else {
        $unit = misc\etc\sanitize($_POST['unit']);
        $delay = misc\etc\sanitize($_POST['delay']);
        $delay = $delay * $unit;
        $resp = misc\chat\createChannel($_POST['name'], $delay);
        match($resp){
            'failure' => dashboard\primary\error("Failed to create channel!"),
            'success' => dashboard\primary\success("Successfully created channel!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
}
?>


<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Chats</h1>
            <p class="text-xs text-gray-500">Allow your users to chat with each other. <a
                    href="https://keyauth.readme.io/reference/chats-1" target="_blank"
                    class="text-blue-600 hover:underline">Learn More</a>.
            </p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Chat Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-chat-channel-modal" data-modal-target="create-chat-channel-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Chat Channel
                    </button>
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="unmute-user-modal" data-modal-target="unmute-user-modal">
                        <i class="lni lni-volume-mute mr-2 mt-1"></i>Unmute User
                    </button>
                    <!-- End Chat Functions -->

                    <br>

                    <!-- Delete Chat Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="clear-chat-channel-modal" data-modal-target="clear-chat-channel-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Clear Chat Channel
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-chat-channels-modal" data-modal-target="delete-all-chat-channels-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Chat Channels
                    </button>

                    <!-- Create Chat Channel Modal -->
                    <div id="create-chat-channel-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Create Chat Channel</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="name" name="name"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="name"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Chat
                                                    Channel Name</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="unit" name="unit"
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
                                                <label for="unit"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Chat
                                                    Cooldown Unit</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" id="delay" name="delay"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="delay"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Cooldown
                                                    Time * By Unit</label>
                                            </div>
                                        </div>

                                        <button name="addchannel"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create
                                            Chat Cannel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create Chat Channel Modal -->

                    <!-- Clear Chat Channel Modal -->
                    <div id="clear-chat-channel-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                        </svg>
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to clear a chat
                                            channel. This will delete all messages in this channel. This can not be
                                            undone!
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to clear
                                        the chat channel? This can not be undone.</h3>
                                    <div class="relative mb-4">
                                        <select type="text" id="channel" name="channel"
                                            class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                            <?php
                                                        $query = misc\mysql\query("SELECT * FROM `chats` WHERE `app` = ?", [$_SESSION['app']]);
                                                        $rows = array();
                                                        while ($r = mysqli_fetch_assoc($query->result)) {
                                                            $rows[] = $r;
                                                        }
                                                        foreach ($rows as $row) {
                                                        ?>

                                            <option><?= $row["name"]; ?></option>

                                            <?php
                                                    } ?>
                                        </select>
                                        <label for="channel"
                                            class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Select
                                            Channel</label>
                                    </div>
                                    <form method="POST">
                                        <button data-modal-hide="clear-chat-channel-modal" name="clearchannel"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="clear-chat-channel-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Unmute User Modal -->

                    <!-- Unmute User Channel Modal -->
                    <div id="unmute-user-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <form method="POST">
                                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to
                                            unmute
                                            this user?</h3>
                                        <div class="relative mb-4">
                                            <select type="text" id="user" name="user"
                                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <?php
                                                        $query = misc\mysql\query("SELECT * FROM `chatmutes` WHERE `app` = ?", [$_SESSION['app']]);
                                                        $rows = array();
                                                        while ($r = mysqli_fetch_assoc($query->result)) {
                                                            $rows[] = $r;
                                                        }
                                                        foreach ($rows as $row) {
                                                        ?>

                                                <option><?= $row["user"]; ?></option>

                                                <?php
                                                        } ?>
                                            </select>
                                            <label for="user"
                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Select
                                                User To Unmute</label>
                                        </div>
                                        <button data-modal-hide="unmute-user-modal" name="unmuteuser"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="unmute-user-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unmute User Modal -->

                    <!-- Delete All Chat Channels Modal -->
                    <div id="delete-all-chat-channels-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                        </svg>
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            chat channels. This can not be undone!
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your chat channels?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-chat-channels-modal"
                                            name="deleteallchatchannels"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-chat-channels-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Chat Channels Modal -->

                    <!-- Mute User Modal -->
                    <div id="mute-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed grid place-items-center hidden h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Mute User</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <select id="muted" name="muted"
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
                                                <label for="muted"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Mute
                                                    Unit</label>

                                                <input type="hidden" class="muteuser" name="user">

                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" id="time" name="time"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="time"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Mute
                                                    Duration:</label>
                                            </div>
                                        </div>

                                        <button name="muteuser"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Mute
                                            User</button>

                                        <button type="button" onclick="closeModal('mute-user-modal')"
                                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Mute User Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_chats" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Chat Name</th>
                                    <th class="px-6 py-3">Message Delay</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>



                                <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `chats` WHERE `app` = ?", [$_SESSION['app']]);
                $rows = array();
                while ($r = mysqli_fetch_assoc($query->result)) {
                    $rows[] = $r;
                }
                foreach ($rows as $row) {
                    $chan = $row['name'];
            ?>



                                <tr>



                                    <td><?= $chan; ?></td>



                                    <td><?= dashboard\primary\time2str(time() - $row["delay"]); ?></td>

                                    <form method="POST">
                                        <td>
                                            <div x-data="{ open: false }" class="z-0">
                                                <button x-on:click="open = true"
                                                    class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4"
                                                    type="button">
                                                    <span class="mr-1">Actions</span>
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" style="margin-top:3px">
                                                        <path
                                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                                    </svg>
                                                </button>
                                                <ul x-show="open" x-on:click.away="open = false"
                                                    class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1"
                                                    style="min-width:15rem">
                                                    <li>
                                                        <button name="deletechan"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                            value="<?= $chan; ?>">
                                                            Delete Channel
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button name="editchan"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            value="<?= $chan; ?>">
                                                            Edit Channel
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                </tr>
                                </form>

                                <?php
                }
            }
            ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- END CHAT TABLE -->
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <br>

                    <!-- START MESSAGES TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_messages" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Author</th>
                                    <th class="px-6 py-3">Message</th>
                                    <th class="px-6 py-3">Time Sent</th>
                                    <th class="px-6 py-3">Channel</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if ($_SESSION['app']) {
                                    $query = misc\mysql\query("SELECT * FROM `chatmsgs` WHERE `app` = ?", [$_SESSION['app']]);
                                    $rows = array();
                                    while ($r = mysqli_fetch_assoc($query->result)) {
                                        $rows[] = $r;
                                    }
                                    foreach ($rows as $row) {
                                        $user = $row['author'];
                                ?>

                                <tr>
                                    <td><?= $user; ?></td>
                                    <td><?= $row["message"]; ?></td>
                                    <td>
                                        <script>
                                        document.write(convertTimestamp(<?= $row["timestamp"]; ?>));
                                        </script>
                                    </td>
                                    <td><?= $row["channel"]; ?></td>

                                    <form method="POST">
                                        <td>

                                            <div x-data="{ open: false }" class="z-0">
                                                <button x-on:click="open = true"
                                                    class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4"
                                                    type="button">
                                                    <span class="mr-1">Actions</span>
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" style="margin-top:3px">
                                                        <path
                                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                                    </svg>
                                                </button>
                                                <ul x-show="open" x-on:click.away="open = false"
                                                    class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1"
                                                    style="min-width:15rem">
                                                    <li>
                                                        <button name="deletemsg"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                            value="<?= $row["id"]; ?>">
                                                            Delete Message
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            onclick="muteuser('<?= $user; ?>')">
                                                            Mute User
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                </tr>
                                </form>

                                <?php
    }
}
?>

                            </tbody>
                        </table>
                    </div>
                    <!-- END MESSAGES TABLE -->
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <script>
                    function muteuser(key) {

                        var muteuser = $('.muteuser');

                        muteuser.attr('value', key);

                        openModal('mute-user-modal');
                    }
                    </script>
