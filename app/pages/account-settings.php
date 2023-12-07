<?php
if ($_SESSION['username'] == "demodeveloper" || $_SESSION['username'] == "demoseller") 
{
   dashboard\primary\error("Demo accounts do not have access here... view manage apps for your owner ID!");
   die();
}

$twofactor = $row['twofactor'];

require_once '../auth/GoogleAuthenticator.php';
$gauth = new GoogleAuthenticator();
if ($row["googleAuthCode"] == NULL) 
{
    $code_2factor = $gauth->createSecret();
    misc\mysql\query("UPDATE `accounts` SET `googleAuthCode` = ? WHERE `username` = ?", [$code_2factor, $_SESSION['username']]);
} 
else 
{
    $code_2factor = $row["googleAuthCode"];
}

$google_QR_Code = $gauth->getQRCodeGoogleUrl($_SESSION['username'], $code_2factor, 'KeyAuth');

$query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$_SESSION['username']]);

if ($query->num_rows > 0) 
{
    while ($row = mysqli_fetch_array($query->result)) 
    {
        $acclogs = $row['acclogs'];
        $expiry = $row["expires"];
        $emailVerify = $row["emailVerify"];
    }
}

if (isset($_POST['updatesettings'])) 
    {
        $pfp = misc\etc\sanitize($_POST['pfp']);
        $acclogs = misc\etc\sanitize($_POST['acclogs']);
        $emailVerify = misc\etc\sanitize($_POST['emailVerify']);
        misc\mysql\query("UPDATE `accounts` SET `acclogs` = ? WHERE `username` = ?", [$acclogs, $_SESSION['username']]);

        if ($acclogs == 0) 
        {
            misc\mysql\query("DELETE FROM `acclogs` WHERE `username` = ?", [$_SESSION['username']]); // delete all account logs
        }

        misc\mysql\query("UPDATE `accounts` SET `emailVerify` = ? WHERE `username` = ?", [$emailVerify, $_SESSION['username']]);
        if (isset($_POST['pfp']) && trim($_POST['pfp']) != '') 
        {
            if (!filter_var($pfp, FILTER_VALIDATE_URL)) {
                dashboard\primary\error("Invalid Url For Profile Image!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            if (strpos($pfp, "file:///") !== false) {
                dashboard\primary\error("Url must start with https://");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $_SESSION['img'] = $pfp;
            misc\mysql\query("UPDATE `accounts` SET `img` = ? WHERE `username` = ?", [$pfp, $_SESSION['username']]);
        }

        dashboard\primary\success("Updated Account Settings!");
    }

    if (isset($_POST['submit_code'])) 
    {
        $code = misc\etc\sanitize($_POST['scan_code1'] . ($_POST['scan_code2']) . ($_POST['scan_code3']) . ($_POST['scan_code4']) . ($_POST['scan_code5']) . ($_POST['scan_code6']));

        $query = misc\mysql\query("SELECT `googleAuthCode` from `accounts` WHERE `username` = ?", [$_SESSION['username']]);

        while ($row = mysqli_fetch_array($query->result)) 
        {
            $secret_code = $row['googleAuthCode'];
        }

        $checkResult = $gauth->verifyCode($secret_code, $code, 2);

        if ($checkResult) 
        {
            $query = misc\mysql\query("UPDATE `accounts` SET `twofactor` = '1' WHERE `username` = ?", [$_SESSION['username']]);
            if ($query->affected_rows > 0) 
            {
                echo "<meta http-equiv='Refresh' Content='2;'>";
                dashboard\primary\success("Two-factor security has been successfully activated on your account!");
                dashboard\primary\wh_log($logwebhook, "{$username} has enabled 2FA", $webhookun);
            } 
            else 
            {
                echo "<meta http-equiv='Refresh' Content='2;'>";
                dashboard\primary\wh_log($logwebhook, "{$username} has disabled 2FA", $webhookun);
                dashboard\primary\success("Two-factor security has been successfully disabled on your account!");
            }
        } 
        else 
        {
            dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
        }
    }

    if (isset($_POST['submit_code_disable'])) 
    {
        $code = misc\etc\sanitize($_POST['scan_code1'] . ($_POST['scan_code2']) . ($_POST['scan_code2']) . ($_POST['scan_code3']) . ($_POST['scan_code4']) . ($_POST['scan_code5']));

        $query = misc\mysql\query("SELECT `googleAuthCode` from `accounts` WHERE `username` = ?", [$_SESSION['username']]);

        while ($row = mysqli_fetch_array($query->result)) 
        {
            $secret_code = $row['googleAuthCode'];
        }

        $checkResult = $gauth->verifyCode($secret_code, $code, 2);

        if ($checkResult) 
        {
            $query = misc\mysql\query("UPDATE `accounts` SET `twofactor` = '0', `googleAuthCode` = NULL WHERE `username` = ?", [$_SESSION['username']]);

            if ($query->affected_rows > 0) 
            {
                dashboard\primary\success("Successfully disabled 2FA!");
            } 
            else 
            {
                dashboard\primary\error("Failed to disable 2FA!");
            }
        } 
        else 
        {
            dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
        }
    }

    if (isset($_POST['deleteWebauthn'])) 
    {
        $name = misc\etc\sanitize($_POST['deleteWebauthn']);

        $query = misc\mysql\query("DELETE FROM `securityKeys` WHERE `name` = ? AND `username` = ?", [$name, $_SESSION['username']]);

        if ($query->affected_rows > 0) 
        {
            $query = misc\mysql\query("SELECT 1 FROM `securityKeys` WHERE `username` = ?", [$_SESSION['username']]);
            if ($query->num_rows == 0) 
            {
                misc\mysql\query("UPDATE `accounts` SET `securityKey` = 0 WHERE `username` = ?", [$_SESSION['username']]);
            }
            dashboard\primary\success("Successfully deleted security key");
        } 
        else 
        {
            dashboard\primary\error("Failed to delete security key!");
        }
    }

?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Account Settings</h1>
            <p class="text-xs text-gray-500">Manage your account.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <br>
                    <form method="post">
                        <div id="lol" class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-8 gap-2">
                            <div class="relative mb-4">
                                <select id="acclogs" name="acclogs"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="1" <?= $acclogs == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $acclogs == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                                <label for="acclogs"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Account
                                    Logs</label>
                            </div>

                            <div class="relative mb-4">
                                <select id="emailVerify" name="emailVerify"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="1" <?= $emailVerify == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $emailVerify == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                                <label for="emailVerify"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">New
                                    Location Alerts
                                </label>
                            </div>
                        </div>

                        <div class="relative mb-4 mt-4">
                            <input type="text" name="username"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on" value="<?= $_SESSION['username'];?>" readonly>
                            <label for="username"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                        </div>

                        <?php 
                            if ($_SESSION["role"] != "Reseller"){ ?>
                        <div class="relative mb-4 mt-4">
                            <input type="text" name="ownerid"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on"
                                value="<?= $_SESSION['ownerid'] ?? "Manager or Reseller accounts do not have OwnerIDs as they can't create applications.";?>"
                                readonly>
                            <label for="ownerid"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">OwnerID
                            </label>
                        </div>
                        <?php } ?>

                        <div class="relative mb-4 mt-4">
                            <input type="text" name="expires"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " value="<?php if ($_SESSION["role"] == "tester")?> Free Forever "
                                autocomplete="on" readonly>
                            <?php if($_SESSION["role"] == "tester"){ ?>
                            <label for="expires"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                Expires: </label>
                            <?php } ?>
                            <label for="expires"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                Expires </label>
                        </div>

                        <script>
                        var expiryInput = document.querySelector('input[name="expires"]');
                        var expiryValue = <?= $expiry ?>;
                        expiryInput.value = convertTimestamp(expiryValue);
                        </script>

                        <div class="relative mb-4 mt-4">
                            <input type="url" name="pfp" id="pfp"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " max="200" value="<?= $_SESSION['img']; ?>" autocomplete="on">
                            <label for="pfp"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Profile
                                Picture URL</label>
                        </div>

                        <!-- Button Functions -->
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            name="updatesettings">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i>Save
                        </button>

                        <?php if (!$twofactor){
                            echo '<a class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 cursor-pointer"
                            data-modal-target="enable-2fa-modal" data-modal-toggle="enable-2fa-modal"><i class="lni lni-shield mr-2 mt-1"></i>Enable 2FA</a>';
                        } else {
                            echo '<a class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 cursor-pointer" 
                            data-modal-target="disable-2fa-modal" data-modal-toggle="disable-2fa-modal"><i class="lni lni-shield mr-2 mt-1"></i>Disable 2FA</a>';
                        }
                        ?>

                        <button type="button"
                            class="inline-flex text-white bg-purple-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-target="fido2-modal" data-modal-toggle="fido2-modal">
                            <i class="lni lni-shield mr-2 mt-1"></i>FIDO2 Webauthn (Security Key)
                        </button>

                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?>/forgot/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Password
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/changeEmail/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Email
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/changeUsername/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Username
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/deleteAccount/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-trash-can mr-2 mt-1"></i>Delete Account
                        </a>
                    </form>
                    <!-- End Button Functions -->

                    <?php 
                    if (!$twofactor) {?>
                    <!-- Enable 2fa Modal -->
                    <div id="enable-2fa-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Enable 2FA (Two Factor
                                        Authentication)</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <label class="mb-5">Scan this QR code into your 2FA App.</label>
                                            <img class="mb-5 mt-5" src="<?= $google_QR_Code ?>" />
                                            <label class="mb-5">Can't scan the QR code? Manually set it instead, code:
                                                <code class="text-blue-700"><?= $code_2factor ?></code></label>
                                            <br><br>
                                            <div id="otp" class="tfa-container">
                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code1"
                                                    name="scan_code1"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" onpaste="handlePaste(event)"
                                                    required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code2"
                                                    name="scan_code2"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code3"
                                                    name="scan_code3"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code4"
                                                    name="scan_code4"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code5"
                                                    name="scan_code5"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code6"
                                                    name="scan_code6"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>
                                            </div>

                                            <script
                                                src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
                                            </script>
                                            <script>



                                            </script>
                                        </div>
                                        <button name="submit_code" id="submit_code"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                            disabled>Enable
                                            2FA</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Enable 2fa Modal -->
                    <?php } ?>

                    <!-- Disable 2fa Modal -->
                    <div id="disable-2fa-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                <button type="button"
                                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                    data-modal-hide="disable-2fa-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Disable 2FA (Two Factor
                                        Authentication)</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4 mt-5">
                                                <input type="text" id="scan_code" name="scan_code"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="scan_code"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">6
                                                    Digit Code from your 2FA app</label>
                                            </div>
                                        </div>
                                        <button name="submit_code_disable"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Disable
                                            2FA</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Disable 2fa Modal -->

                    <!-- FIDO2 Modal -->
                    <form class="space-y-6" method="POST">
                        <div id="fido2-modal" tabindex="-1" aria-hidden="true"
                            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                    <div class="px-6 py-6 lg:px-8">
                                        <h3 class="mb-4 text-xl font-medium text-white-900">FIDO2 WebAuthn (Security
                                            Keys)</h3>
                                        <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="webauthn_name" name="webauthn_name"
                                                    placeholder=" " maxlength="99"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                    autocomplete="on">
                                                <label for="webauthn_name"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                                    New Security Key Name</label>
                                            </div>
                                        </div>
                                        <button type="button" onclick="newregistration()"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mb-5">Register
                                            New Security Key</button>

                                        <h3 class="mb-4 text-xl font-medium text-white-900">Existing Security Keys</h3>
                                        <?php
                                                $query = misc\mysql\query("SELECT * FROM `securityKeys` WHERE `username` = ?", [$_SESSION['username']]);
                                                    if ($query->num_rows > 0) {
                                                        while ($row = mysqli_fetch_array($query->result)) {
                                                            ?>
                                        <button type="submit" name="deleteWebauthn" value="<?= $row["name"]; ?>"
                                            onclick="return confirm('Are you sure you want to delete your security key? This cannot be undone.')"
                                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Delete
                                            Security Key: <?= $row["name"]; ?></button>
                                        <?php
                                                            }
                                                        }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- End FIDO2 Modal -->

                    <!-- Include the jQuery library -->
                    

                    <script>
                    function handlePaste(event) {
                        event.preventDefault();
                        var clipboardData = event.clipboardData || window.clipboardData;
                        var pastedData = clipboardData.getData('text');
                        var inputs = document.querySelectorAll(".tfa-container input");

                        // Check if the pasted data is exactly 6 digits long
                        if (/^\d{6}$/.test(pastedData)) {
                            for (var i = 0; i < 6; i++) {
                                inputs[i].value = pastedData.charAt(i);
                            }
                        }
                    }

                    function updateButtonState(inputs) {
                        var allNumeric = true;

                        // Check if all input fields have numeric values
                        for (var i = 0; i < inputs.length; i++) {
                            if (!/^\d$/.test(inputs[i].value)) {
                                allNumeric = false;
                                break;
                            }
                        }

                        // Enable or disable the button based on the input values
                        var button = document.getElementById('submit_code');
                        button.disabled = !allNumeric;
                    }

                    // Add event listeners to each input field to monitor changes
                    var inputs = document.querySelectorAll(".tfa-container input");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener('input', function() {
                            updateButtonState(inputs);
                        });
                    }
                    </script>
