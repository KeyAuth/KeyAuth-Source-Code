<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}

if (isset($_POST['selectApp'])) {
    $appName = misc\etc\sanitize($_POST['selectApp']);
    $query = misc\mysql\query("SELECT `secret`, `name`, `banned`, `sellerkey` FROM `apps` WHERE `owner` = ? AND `name` = ?", [$_SESSION['username'], $appName]);

    if ($query->num_rows < 1) {
        dashboard\primary\error("Application not found!");
    } else {
        $row = mysqli_fetch_array($query->result);
        $banned = $row["banned"];

        if ($banned) {
            dashboard\primary\error("Application is banned!");
        } else {
            $_SESSION["app"] = $row["secret"];
            $_SESSION["name"] = $appName;
            $_SESSION["selectedApp"] = $row["name"];
            $_SESSION['sellerkey'] = $row["sellerkey"];

            echo '<meta http-equiv="refresh" content="0">'; // needed to refresh nav sidebar
            dashboard\primary\success("Successfully Selected the App!");
        }
    }
}

if (isset($_POST['create_app'])) {

    $appname = misc\etc\sanitize($_POST['appname']);
    if ($appname == "") {
        dashboard\primary\error("Input a valid name");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }

    $query = misc\mysql\query("SELECT 1 FROM `apps` WHERE name = ? AND owner = ?", [$appname, $_SESSION['username']]);
    if ($query->num_rows > 0) {
        dashboard\primary\error("You already own application with this name!");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }

    $owner = $_SESSION['username'];

    if ($role == "tester") {
        $num_rows = misc\mysql\query("SELECT * FROM `apps` WHERE `owner` = ? AND `ownerid` = ?", [$_SESSION['username'], $_SESSION['ownerid']])->num_rows;

        if ($num_rows > 0) {
            dashboard\primary\error("Tester plan only supports one application!");
            echo '<meta http-equiv="refresh" content="2">';
            return;
        }
    }

    if ($role == "Manager") {
        dashboard\primary\error("Manager Accounts Are Not Allowed To Create Applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }

    $clientsecret = hash('sha256', misc\etc\generateRandomString());
    $algos = array(
        'ripemd128',
        'md5',
        'md4',
        'tiger128,4',
        'haval128,3',
        'haval128,4',
        'haval128,5'
    );
    $sellerkey = hash($algos[array_rand($algos)], misc\etc\generateRandomString());
    misc\mysql\query("INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('default', '1', ?)", [$clientsecret]);
    $query = misc\mysql\query("INSERT INTO `apps` (`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES (?, ?, ?, ?, '1', '1', ?)", [$_SESSION['username'], $appname, $clientsecret, $_SESSION['ownerid'], $sellerkey]);

    if ($query->affected_rows != 0) {
        $_SESSION['secret'] = $clientsecret;
        dashboard\primary\success("Successfully Created App!");
        $_SESSION['app'] = $clientsecret;
        $_SESSION["selectedapp"] = $appname;
        $_SESSION['name'] = $appname;
        $_SESSION['sellerkey'] = $sellerkey;
    } else {
        dashboard\primary\error("Failed to create application!");
    }
}

if (isset($_POST['rename_app'])) {
    $appname = misc\etc\sanitize($_POST['appname']);
    if ($appname == "") {
        dashboard\primary\error("Input a valid name");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    if ($role == "Manager") {
        dashboard\primary\error("Manager Accounts Aren't Allowed To Rename Applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    $query = misc\mysql\query("SELECT 1 FROM `apps` WHERE `owner` = ? AND `name` = ?", [$_SESSION['username'], $appname]);
    if ($query->num_rows > 0) {
        dashboard\primary\error("You already have an application with this name!");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    misc\mysql\query("UPDATE `accounts` SET `app` = ? WHERE `app` = ? AND `owner` = ?", [$appname, $_SESSION['name'], $_SESSION['username']]);
    $query = misc\mysql\query("UPDATE `apps` SET `name` = ? WHERE `secret` = ? AND `owner` = ?", [$appname, $_SESSION['app'], $_SESSION['username']]);

    if ($query->affected_rows != 0) {
        $oldName = $_SESSION['name'];
        $_SESSION['name'] = $appname;
        dashboard\primary\success("Successfully Renamed App!");
        misc\cache\purge('KeyAuthApp:' . $oldName . ':' . $_SESSION['ownerid']);
        if ($_SESSION['role'] == "seller") {
            $query = misc\mysql\query("SELECT `customDomain`, `sellerkey`, `customDomainAPI` FROM `apps` WHERE `owner` = ? AND `name` = ?", [$_SESSION['username'], $appname]);
            $row = mysqli_fetch_array($query->result);
            misc\cache\purge('KeyAuthAppPanel:' . $row['customDomain']);
            misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
        }
        $_SESSION["selectedapp"] = $appname;
    } else {
        dashboard\primary\error("Application Renamed Failed!");
    }
}

if (isset($_POST['pauseapp'])) {
    if ($role == "Manager") {
        dashboard\primary\error("Manager accounts aren't allowed to pause applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    misc\cache\purgePattern('KeyAuthSubs:' . $_SESSION["app"]);
    misc\app\pause();
    dashboard\primary\success("Paused application and any active subscriptions!");
}
if (isset($_POST['unpauseapp'])) {
    if ($role == "Manager") {
        dashboard\primary\error("Manager accounts aren't allowed to unpause applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    misc\cache\purgePattern('KeyAuthSubs:' . $_SESSION["app"]);
    misc\app\unpause();
    dashboard\primary\success("Unpaused application and any paused subscriptions!");
}

if (isset($_POST['refreshapp'])) {
    if ($role == "Manager") {
        dashboard\primary\error("Manager Accounts Aren't Allowed To Refresh Applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    $gen = misc\etc\generateRandomString();
    $new_secret = hash('sha256', $gen);
    $query = misc\mysql\query("UPDATE `apps` SET `secret` = ? WHERE `secret` = ? AND `owner` = ?", [$new_secret, $_SESSION['app'], $_SESSION['username']]);
    $_SESSION['secret'] = $new_secret;
    if ($query->affected_rows != 0) {
        misc\mysql\query("UPDATE `bans` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `buttons` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `chatmsgs` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `chatmutes` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `chats` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `files` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `keys` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `logs` SET `logapp` = ? WHERE `logapp` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `sessions` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `subs` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `subscriptions` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `users` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `uservars` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `vars` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        misc\mysql\query("UPDATE `webhooks` SET `app` = ? WHERE `app` = ?", [$new_secret, $_SESSION['app']]);
        $_SESSION['app'] = $new_secret;
        misc\cache\purge('KeyAuthApp:' . $_SESSION['name'] . ':' . $_SESSION['ownerid']);
        if ($_SESSION['role'] == "seller" || $_SESSION['role'] == "developer") {
            $query = misc\mysql\query("SELECT `sellerkey`,`customDomainAPI` FROM `apps` WHERE `owner` = ? AND `name` = ?", [$_SESSION['username'], $_SESSION['name']]);
            $row = mysqli_fetch_array($query->result);
            misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
            misc\cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
        }
        dashboard\primary\success("Successfully Refreshed App!");
    } else {
        dashboard\primary\error("Application Refresh Failed!");
    }
}

if (isset($_POST['deleteapp'])) {
    if ($role == "Manager") {
        dashboard\primary\error("Manager Accounts Aren't Allowed To Delete Applications");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }

    $app = $_SESSION['app'];
    $query = misc\mysql\query("DELETE FROM `apps` WHERE `secret` = ?", [$app]);
    if ($query->affected_rows != 0) {
        misc\mysql\query("DELETE FROM `bans` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `buttons` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `chatmsgs` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `chatmutes` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `chats` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `files` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `keys` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `logs` WHERE `logapp` = ?", [$app]);
        misc\mysql\query("DELETE FROM `sessions` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `subs` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `subscriptions` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `users` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `uservars` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `vars` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `webhooks` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `auditLog` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `sellerLogs` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `whitelist` WHERE `app` = ?", [$app]);
        misc\mysql\query("DELETE FROM `accounts` WHERE `app` = ? AND `owner` = ?", [$_SESSION['name'], $_SESSION['username']]);

        misc\cache\purge('KeyAuthApp:' . $_SESSION['name'] . ':' . $_SESSION['ownerid']);
        if ($_SESSION['role'] == "seller" || $_SESSION['role'] == "developer") {
            $query = misc\mysql\query("SELECT `sellerkey`,`customDomainAPI` FROM `apps` WHERE `owner` = ? AND `name` = ?", [$_SESSION['username'], $_SESSION['name']]);
            $row = mysqli_fetch_array($query->result);
            misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
            misc\cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
        }

        $_SESSION['app'] = NULL;
        dashboard\primary\success("Successfully deleted App!");
        $query = misc\mysql\query("SELECT * FROM `apps` WHERE `owner` = ? AND `ownerid` = ?", [$_SESSION['username'], $_SESSION['ownerid']]); // select all apps where owner is current user
        if ($query->num_rows == 1) // if the user only owns one app, select that app, otherwise select no app 
        {
            $row = mysqli_fetch_array($query->result);
            $_SESSION['name'] = $row["name"];
            $_SESSION["selectedApp"] = $row["name"];
            $_SESSION['app'] = $row["secret"];
        } else {
            $_SESSION['name'] = NULL;
            $_SESSION["selectedApp"] = NULL;
        }
    } else {
        dashboard\primary\error("Application Deletion Failed!");
    }
}

if (isset($_SESSION["app"])) {
    $appsecret = $_SESSION["app"];
    $query = misc\mysql\query("SELECT * FROM `apps` WHERE `secret` = ?", [$appsecret]);

    $row = mysqli_fetch_array($query->result);
    $appname = $row["name"];
    $secret = $row["secret"];
    $version = $row["ver"];
    $ownerid = $row["ownerid"];
    $paused = $row["paused"];

    $_SESSION["secret"] = $secret;
}

if (isset($_POST["closeAlert"])) {
    $query = misc\mysql\query("UPDATE `accounts` SET `alert` = ? WHERE `username` = ?", [NULL, $_SESSION["username"]]);

    if ($query->affected_rows != 0) {
        dashboard\primary\success("Successfully closed staff alert.");
    } else {
        dashboard\primary\error("This alert can not be closed. Please contact staff.");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] mt-4 md:mt-12 rounded-xl">
        <div class="mb-4 p-8">
            <?php if ($alertMsg != NULL){ ?>
            <!-- Alert Box -->
            <div id="alert" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div class="ml-3 text-sm font-medium text-yellow-500">
                    New Message From Staff: <?= $alertMsg; ?>
                </div>
                <form method="post">
                    <button name="closeAlert"
                        class="ml-auto -mx-1.5 -my-1.5 bg-[#0f0f17] text-yellow-500 rounded-lg focus:ring-2 focus:ring-yellow-400 p-1.5 hover:bg-yellow-200 inline-flex items-center justify-center h-8 w-8  "
                        data-dismiss-target="#alert" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </form>
            </div>
            <?php } ?>
            <!-- End Alert Box -->
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">

                <?php

                if (isset($appname)) {
                ?>
                Manage Applications - <?= $appname; ?>
                <?php
                } else {
                ?>
                Manage Applications
                <?php
                }

                ?>
            </h1>
            <p class="text-xs text-gray-500">This is where it all begins. <a
                    href="https://keyauth.readme.io/reference/manage-application" target="_blank"
                    class="text-blue-600 hover:underline">Learn More</a>.</p>
            <br>

            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <?php if (isset($_SESSION["app"])) { ?>
                    <p class="text-base text-gray-300">Application Credentials</p>
                    <p class="text-xs text-gray-500">Simply replace the placeholder code in the
                        example with these</p>
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab"
                            data-tabs-toggle="#myTabContent" role="tablist">
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="csharp-tab" data-tabs-target="#csharp" type="button" role="tab"
                                    aria-controls="csharp" aria-selected="false">C#</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="cplusplus-tab" data-tabs-target="#cplusplus" type="button" role="tab"
                                    aria-controls="cplusplus" aria-selected="false">C++</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="python-tab" data-tabs-target="#python" type="button" role="tab"
                                    aria-controls="python" aria-selected="false">Python</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="php-tab" data-tabs-target="#php" type="button" role="tab" aria-controls="php"
                                    aria-selected="false">PHP</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="js-tab" data-tabs-target="#js" type="button" role="tab" aria-controls="js"
                                    aria-selected="false">JavaScript</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="j-tab" data-tabs-target="#j" type="button" role="tab" aria-controls="j"
                                    aria-selected="false">Java</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="vb-tab" data-tabs-target="#vb" type="button" role="tab" aria-controls="vb"
                                    aria-selected="false">VB.Net</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="rust-tab" data-tabs-target="#rust" type="button" role="tab" aria-controls="rust"
                                    aria-selected="false">Rust</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="go-tab" data-tabs-target="#go" type="button" role="tab" aria-controls="go"
                                    aria-selected="false">Go</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="lua-tab" data-tabs-target="#lua" type="button" role="tab" aria-controls="lua"
                                    aria-selected="false">Lua</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="ruby-tab" data-tabs-target="#ruby" type="button" role="tab" aria-controls="ruby"
                                    aria-selected="false">Ruby</button>
                            </li>
                            <li role="presentation">
                                <button
                                    class="inline-block p-4 rounded-t-lg text-gray-400 hover:text-white transition duration-200"
                                    id="perl-tab" data-tabs-target="#perl" type="button" role="tab" aria-controls="perl"
                                    aria-selected="false">Perl</button>
                            </li>
                        </ul>
                    </div>
                    <div id="myTabContent">
                        <div class="p-4 rounded-lg bg-[#09090d]" id="csharp" role="tabpanel"
                            aria-labelledby="csharp-tab">
                            <pre id="csharp-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
public static api KeyAuthApp = new api(
    name: "<?= $appname; ?>",
    ownerid: "<?= $ownerid; ?>",
    secret: "<?= $secret; ?>",
    version: "<?= $version; ?>"
);</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="csharp-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-CSHARP-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                C# Example</a>
                            <a href="https://github.com/KeyAuth/KeyAuth-Unity-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Unity Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="cplusplus" role="tabpanel"
                            aria-labelledby="cplusplus-tab">
                            <pre id="cpp-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
std::string name = skCrypt("<?= $appname; ?>").decrypt();
std::string ownerid = skCrypt("<?= $ownerid; ?>").decrypt();
std::string secret = skCrypt("<?= $secret; ?>").decrypt();
std::string version = skCrypt("<?= $version; ?>").decrypt();
std::string url = skCrypt("https://keyauth.win/api/1.2/").decrypt(); // change if you're self-hosting</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="cpp-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-CPP-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                C++ Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="python" role="tabpanel"
                            aria-labelledby="python-tab">
                            <pre id="py-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
keyauthapp = api(
    name = "<?= $appname; ?>",
    ownerid = "<?= $ownerid; ?>",
    secret = "<?= $secret; ?>",
    version = "<?= $version; ?>",
    hash_to_check = getchecksum()
)</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="py-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-Python-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Py Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="php" role="tabpanel" aria-labelledby="php-tab">
                            <pre id="php-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
$name = "<?= $appname; ?>";
$ownerid = "<?= $ownerid; ?>";</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="php-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-PHP-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                PHP Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="js" role="tabpanel" aria-labelledby="js-tab">
                            <pre id="js-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
const KeyAuthApp = new KeyAuth(
    "<?= $appname; ?>",
    "<?= $ownerid; ?>",
    "<?= $secret; ?>",
    "<?= $version; ?>",
);</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="js-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/mazkdevf/KeyAuth-JS-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                JS Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="j" role="tabpanel" aria-labelledby="j-tab">
                            <pre id="j-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
private static String ownerid = "<?= $ownerid; ?>",
private static String appname = "<?= $appname; ?>",
private static String version = "<?= $version; ?>"</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="j-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth-Archive/KeyAuth-JAVA-api" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Java Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="vb" role="tabpanel" aria-labelledby="vb-tab">
                            <pre id="vb-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
Private Shared name As String = "<?= $appname; ?>"
Private Shared ownerid As String = "<?= $ownerid; ?>"
Private Shared secret As String = "<?= $secret; ?>"
Private Shared version As String = "<?= $version; ?>"</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="vb-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-VB-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                VB Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="rust" role="tabpanel" aria-labelledby="rust-tab">
                            <pre id="rust-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
let mut keyauthapp = keyauth::v1_2::KeyauthApi::new(
    "<?= $appname; ?>",
    "<?= $ownerid; ?>",
    "<?= $secret; ?>",
    "<?= $version; ?>",
    "https://keyauth.win/api/1.2/", // This is the API URL, change this to your custom domain if you have it enabled
);</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="rust-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/KeyAuth/KeyAuth-Rust-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Rust Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="go" role="tabpanel" aria-labelledby="go-tab">
                            <pre id="go-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
var name = "<?= $appname; ?>"
var ownerid =  "<?= $ownerid; ?>"
var version = "<?= $version; ?>"</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="go-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/mazkdevf/KeyAuth-Go-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Go Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="lua" role="tabpanel" aria-labelledby="lua-tab">
                            <pre id="lua-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
local name = "<?= $appname; ?>";
local ownerid = "<?= $ownerid; ?>";
local version = "<?= $version; ?>";</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="lua-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/mazkdevf/KeyAuth-Lua-Examples" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Lua Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="ruby" role="tabpanel" aria-labelledby="ruby-tab">
                            <pre id="ruby-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
KeyAuth.new.Api(
    "<?= $appname; ?>",
    "<?= $ownerid; ?>",
    "<?= $secret; ?>",
    "<?= $version; ?>"
)</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="ruby-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/mazkdevf/KeyAuth-Ruby-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Ruby Example</a>
                        </div>
                        <div class="p-4 rounded-lg bg-[#09090d]" id="perl" role="tabpanel" aria-labelledby="perl-tab">
                            <pre id="perl-creds" class="copy-target text-gray-400 bg-[#09090d] overflow-x-auto">
KeyAuth::Api(
    "<?= $appname; ?>",
    "<?= $ownerid; ?>",
    "<?= $secret; ?>",
    "<?= $version; ?>"
);</pre>
                            <br>
                            <button type="button"
                                class="copy-button mt-3 inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200"
                                data-copy-target="perl-creds">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2ZM4 20V10h10l.002 10H4Zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10Z">
                                    </path>
                                    <path d="M6 12h6v2H6v-2Zm0 4h6v2H6v-2Z"></path>
                                </svg>

                                Copy Credentials</button>
                            <a href="https://github.com/mazkdevf/KeyAuth-Perl-Example" target="_blank" type="button"
                                class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 transition duration-200">

                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3h-8Z"></path>
                                    <path
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7Z">
                                    </path>
                                </svg>

                                View
                                Perl Example</a>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="mt-3 gap-1.5 grid grid-cols-1 sm:grid-cols-2 md:block md:grid-cols-0">
                        <?php if ($_SESSION['role'] != "Manager") { ?>
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="create-app-modal" data-modal-target="create-app-modal"> <i
                                class="lni lni-circle-plus mr-2 mt-1"></i>
                            Create Application
                        </button>

                        <?php } ?>
                        <?php if (isset($_SESSION['app']) & $_SESSION['role'] != "Manager") { ?>
                        <button
                            class="inline-flex text-white bg-purple-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="rename-app-modal" data-modal-target="rename-app-modal"> <i
                                class="lni lni-pencil-alt mr-2 mt-1"></i>
                            Rename Application
                        </button>

                        <?php if (!$paused) { ?>
                        <button
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="pause-app-modal" data-modal-target="pause-app-modal"> <i
                                class="lni lni-pause mr-2 mt-1"></i>
                            Pause Application & Users
                        </button>
                        <?php } else { ?>
                        <button
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="unpause-app-modal" data-modal-target="unpause-app-modal"> <i
                                class="lni lni-play mr-2 mt-1"></i>
                            Unpause Application & Users
                        </button>
                        <?php } ?>

                        <button
                            class="inline-flex text-white bg-green-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="refresh-app-modal" data-modal-target="refresh-app-modal"> <i
                                class="lni lni-reload mr-2 mt-1"></i>
                            Refresh Application Secret
                        </button>

                        <button
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-toggle="delete-app-modal" data-modal-target="delete-app-modal"> <i
                                class="lni lni-trash-can mr-2 mt-1"></i>
                            Delete Application
                        </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#09090d] block sm:flex items-center justify-between lg:mt-5">
            <div class="mb-1 w-full bg-[#0f0f17] mt-4 md:mt-2 rounded-xl">
                <?php if ($_SESSION['role'] != "Manager") { ?>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5 pb-5 pl-3 pr-3">
                    <table class="w-full text-sm text-left text-white">
                        <thead>
                            <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                <th class="px-6 py-3">Application Name</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                    $rows = array();
                    $query = misc\mysql\query("SELECT * FROM `apps` WHERE `owner` = ? AND `ownerid` = ? ORDER BY `name` ASC", [$_SESSION['username'], $_SESSION['ownerid']]);
                    while ($r = mysqli_fetch_assoc($query->result)) {
                        $rows[] = $r;
                    }

                    foreach ($rows as $row) {
                        $appName = $row['name'];
                        $paused = $row['paused'];


                        if (isset($_SESSION["selectedApp"])) {
                            $appSelected = ($_SESSION["selectedApp"] == $appName);
                        } else {
                            $appSelected = 0;
                        }
                    ?>
                            <tr class="hover:bg-[#09090d]">
                                <td class="px-6 py-2">
                                    <?= $appName ?>
                                </td>
                                <td class="px-5 py-2">
                                    <span
                                        class="bg-<?= $paused == 1 ? 'orange' : 'green'; ?>-800 text-<?= $paused == 1 ? 'orange' : 'green'; ?>-100 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">
                                        <?= $paused == 1 ? 'Paused' : 'Active'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-2 grid place-items-end pr-6">
                                    <form method="POST">
                                        <button value="<?= $appName ?>" name="selectApp" class="flex items-center">
                                            <span
                                                class="h-2.5 w-2.5 rounded-full bg-<?= $appSelected == 1 ? 'blue' : 'red'; ?>-500"></span>
                                            <span class="ml-2"><?= $appSelected == 1 ? 'Selected' : 'Select'; ?></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Create New App Modal -->
        <div id="create-app-modal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                    <div class="px-6 py-6 lg:px-8">
                        <h3 class="mb-4 text-xl font-medium text-white-900">Create A New Application</h3>
                        <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                        <form class="space-y-6" method="POST">
                            <div>
                                <div class="relative">
                                    <input type="text" id="appname" name="appname"
                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                        placeholder=" " autocomplete="on" required="">
                                    <label for="appname"
                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Application
                                        Name</label>
                                </div>
                            </div>
                            <button type="submit" name="create_app"
                                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create
                                App</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Create A App Modal -->

        <!-- Rename App Modal -->
        <div id="rename-app-modal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-[#0f0f17] border border-purple-700 rounded-lg shadow">
                    <div class="px-6 py-6 lg:px-8">
                        <h3 class="mb-4 text-xl font-medium text-white-900">Rename Application</h3>
                        <form class="space-y-6" method="POST">
                            <div class="relative mb-4">
                                <input type="text" id="appname" name="appname"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer focus:border-purple-700"
                                    placeholder=" " autocomplete="on" required="">
                                <label for="appname"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-purple-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">New
                                    Application
                                    Name</label>
                            </div>
                            <button type="submit" name="rename_app"
                                class="w-full text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Rename
                                Application</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Rename App Modal -->

        <!-- Pause App and Users Modal -->
        <div id="pause-app-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-yellow-700  rounded-lg shadow">
                    <div class="p-6 text-center">
                        <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-[#0f0f17]"
                            role="alert">
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium text-white">Notice! Pausing your app and users will make your
                                    application
                                    unuseable until you unpause it.</span>
                            </div>
                        </div>
                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                            you want
                            to
                            pause your application and users?</h3>
                        <form method="POST">
                            <button data-modal-hide="pause-app-modal" name="pauseapp"
                                class="text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button data-modal-hide="pause-app-modal" type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Pause App and Users Modal -->

        <!-- Unpause App and Users Modal -->
        <div id="unpause-app-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-yellow-700  rounded-lg shadow">
                    <div class="p-6 text-center">
                        <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-[#0f0f17]"
                            role="alert">
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium text-white">Notice! You're about to unpause your application.
                                    Making it
                                    accesible to all users.</span>
                            </div>
                        </div>
                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                            you want
                            to
                            unpause your application and users?</h3>
                        <form method="POST">
                            <button data-modal-target="unpause-app-modal" data-modal-hide="unpause-app-modal"
                                name="unpauseapp"
                                class="text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button data-modal-target="unpause-app-modal" data-modal-hide="unpause-app-modal"
                                type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Unpause App and Users Modal -->

        <!-- Refresh App Secret Modal -->
        <div id="refresh-app-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-green-700 rounded-lg shadow">
                    <div class="p-6 text-center">
                        <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-[#0f0f17]"
                            role="alert">
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium text-white">Notice! Make sure you change your application
                                    secret in
                                    your
                                    program after refreshing.</span>
                            </div>
                        </div>
                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                            you want
                            to
                            refresh your application secret?</h3>
                        <form method="POST">
                            <button data-modal-hide="refresh-app-modal" name="refreshapp"
                                class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button data-modal-hide="refresh-app-modal" type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Refresh App Modal -->

        <!-- Delete App Modal -->
        <div id="delete-app-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                    <div class="p-6 text-center">
                        <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-700 rounded-lg bg-[#0f0f17]"
                            role="alert">
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium text-red-400">Notice! You're about to delete your application.
                                    <b>This
                                        can
                                        NOT be undone</b></span>
                            </div>
                        </div>
                        <h3 class="mb-5 text-lg font-normal text-gray-200">Please enter your application name to delete
                            it. App
                            Name is: "<?= $appname; ?>"</h3>
                        <form method="POST">
                            <div>
                                <div class="relative mb-4">
                                    <input type="text" id="confirmappname" name="confirmappname"
                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer focus:border-red-700"
                                        placeholder=" " autocomplete="on" required="">
                                    <label for="confirmappname"
                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-red-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Application
                                        Name</label>
                                </div>
                            </div>
                            <button data-modal-hide="delete-app-modal" name="deleteapp"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button data-modal-hide="delete-app-modal" type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete App Modal -->

        <!-- Copy Credentials To Clipboard -->
        <script>
        function copyContent(targetId) {
            const codeBlock = document.getElementById(targetId);
            const codeContent = codeBlock.textContent;

            const tempTextarea = document.createElement("textarea");
            tempTextarea.value = codeContent;
            document.body.appendChild(tempTextarea);

            tempTextarea.select();
            document.execCommand("copy");

            document.body.removeChild(tempTextarea);

            Swal.fire({
                icon: "success",
                title: "Copied Credentials To Clipboard!",
                showConfirmButton: false,
                timer: 1500,
                iconColor: '#00ff15',
                // change popup classes
                customClass: {
                    popup: "bg-[#0f0f17] border-[#09090d] rounded-lg",
                    title: "text-white",
                    content: "text-white",
                    confirmButton: "hidden",
                    cancelButton: "hidden",
                },
            })

            /* alert("Copied Credentials To Clipboard!"); */
        }

        // Attach click event listeners to all copy buttons
        const copyButtons = document.querySelectorAll(".copy-button");
        copyButtons.forEach(button => {
            button.addEventListener("click", () => {
                const targetId = button.getAttribute("data-copy-target");
                copyContent(targetId);
            });
        });
        </script>

        <script>
        function highlightCode() {
            var pres = document.querySelectorAll("pre");
            for (var i = 0; i < pres.length; i++) {
                hljs.highlightElement(pres[i]);
            }
        }
        highlightCode();
        </script>

        <!-- Include the jQuery library -->
        
