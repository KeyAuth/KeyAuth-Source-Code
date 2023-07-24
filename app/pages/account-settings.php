<?php
if ($_SESSION['username'] == "demodeveloper" || $_SESSION['username'] == "demoseller") 
{
    die("OwnerID: " . $row['ownerid'] . "<br>that's the only thing you need on this page.");
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
?>

<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>
    <script src="https://cdn.keyauth.cc/dashboard/webauthn.js"></script>
    <?php

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
        if (empty($_POST['scan_code'])) 
        {
            dashboard\primary\error("You forgot to enter 2FA code!");
        }

        $code = misc\etc\sanitize($_POST['scan_code']);

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
        if (empty($_POST['scan_code'])) 
        {
            dashboard\primary\error("You forgot to enter 2FA code!");
        }

        $code = misc\etc\sanitize($_POST['scan_code']);

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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Username</label>
                            <div class="col-10">
                                <label class="form-control"><?php echo $_SESSION['username']; ?></label>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">OwnerID</label>
                            <div class="col-10">
                                <label class="form-control"><?php echo $_SESSION['ownerid'] ?? "Manager or Reseller accounts don't have OwnerIDs as they can't create apps"; ?></label>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Subscription Expires</label>
                            <div class="col-10">
                                <label class="form-control">
                                    <script>
                                        document.write(convertTimestamp(<?php echo $expiry; ?>));
                                    </script>
                                </label>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-tel-input" class="col-2 col-form-label">Account logs</label>
                            <div class="col-10">
                                <select class="form-control" name="acclogs">
                                    <option value="1" <?= $acclogs == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $acclogs == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-tel-input" class="col-2 col-form-label">New Login Location Verification</label>
                            <div class="col-10">
                                <select class="form-control" name="emailVerify">
                                    <option value="1" <?= $emailVerify == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $emailVerify == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-tel-input" class="col-2 col-form-label">Password</label>
                            <div class="col-10">
                                <div class="form-control">Change password here
                                    <?php echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/forgot/" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/forgot/</a>'; ?>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Profile Image</label>
                            <div class="col-10">
                                <input class="form-control" name="pfp" type="url" maxlength="200" placeholder="Enter link to image for profile picture">
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Email</label>
                            <div class="col-10">
                                <div class="form-control">Change email here
                                    <?php echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/changeEmail/" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/changeEmail/</a>'; ?>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Username</label>
                            <div class="col-10">
                                <div class="form-control">Change username here
                                    <?php echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/changeUsername/" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/changeUsername/</a>'; ?>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Account Deletion</label>
                            <div class="col-10">
                                <div class="form-control">Delete account here
                                    <?php echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/deleteAccount/" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/deleteAccount/</a>'; ?>
                                </div>
                            </div>
                        </div>

                        <br>

                        <button name="updatesettings" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                        <?php if (!$twofactor) 
                        {
                            echo '                <a class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#twofa"><i class="fa fa-lock"></i>Enable 2FA</a>';
                        } 
                        else 
                        {
                            echo '                <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disabletwofa"><i class="fa fa-lock"></i>Disable 2FA</a>';
                        } 
                        ?>
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#webauthn"><i class="fab fa-usb"></i>FIDO2 WebAuthn (Security Keys)</a>
                    </form>


                    <?php
                    if (!$twofactor) 
                    {
                    ?>
                        <!--begin::Modal - 2fa App-->
                        <div class="modal fade" id="twofa" tabindex="-1" aria-hidden="true">
                            <!--begin::Modal dialog-->
                            <div class="modal-dialog modal-dialog-centered mw-900px">
                                <!--begin::Modal content-->
                                <div class="modal-content">
                                    <!--begin::Modal header-->
                                    <div class="modal-header">
                                        <h2 class="modal-title">2 Factor Authentication</h2>
                                        <!--begin::Close-->
                                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                        <!--end::Close-->
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <div class="current" data-kt-stepper-element="content">
                                                <div class="w-100">
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-10">
                                                        <!--begin::Label-->
                                                        <label class="d-flex align-items-center fs-5 fw-bold mb-2">
                                                            <span class="required">2FA Code</span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <label>Scan this QR code into your 2FA App.</label>
                                                        </br>
                                                        </br>

                                                        <img src="<?php echo $google_QR_Code ?>" />
                                                        </br>
                                                        </br>
                                                        <label>Alternatively, you can set it manually, code:
                                                            <code><?php echo $code_2factor ?></code></label>
                                                        </br>
                                                        </br>
                                                        <input type="text" name="scan_code" id="scan_code" maxlength="6" placeholder="6 Digit Code from 2FA app" class="form-control mb-4" required>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button name="submit_code" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!--end::Modal header-->
                                </div>
                                <!--end::Modal content-->
                            </div>
                            <!--end::Modal dialog-->
                        </div>
                        <!--end::Modal - 2fa App-->
                    <?php
                    }
                    ?>

                    <!--begin::Modal - disable 2fa App-->
                    <div class="modal fade" id="disabletwofa" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-900px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <h2 class="modal-title">Disable 2 Factor Authentication</h2>

                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                        <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                            </svg>
                                        </span>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="current" data-kt-stepper-element="content">
                                            <div class="w-100">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="d-flex align-items-center fs-5 fw-bold mb-2">
                                                        <span class="required">2FA Code</span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="text" name="scan_code" id="scan_code" maxlength="6" placeholder="6 Digit Code from 2FA app" class="form-control mb-4" required>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button name="submit_code_disable" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>
                                <!--end::Modal header-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - disable 2fa App-->

                    <!--begin::Modal - disable 2fa App-->
                    <div class="modal fade" id="webauthn" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-900px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <h2 class="modal-title">FIDO2 WebAuthn (Security Keys)</h2>

                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                        <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                            </svg>
                                        </span>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="current" data-kt-stepper-element="content">
                                            <div class="w-100">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <?php
                                                    $query = misc\mysql\query("SELECT * FROM `securityKeys` WHERE `username` = ?", [$_SESSION['username']]);
                                                    if ($query->num_rows > 0) 
                                                    {
                                                        while ($row = mysqli_fetch_array($query->result)) 
                                                        {
                                                            echo $row["name"] . "  <button style=\"border: none;padding:0;background:0;color:#FF0000;padding-left:5px;\" value=\"" . $row["name"] . "\"name=\"deleteWebauthn\" onclick=\"return confirm('Are you sure you want to delete security key? This can not be undone.')\">Delete</button><br>";
                                                        }
                                                        echo "<br>";
                                                    }
                                                    ?>
                                                    <label class="d-flex align-items-center fs-5 fw-bold mb-2">
                                                        <span class="required">Name</span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="text" name="webauthn_name" id="webauthn_name" maxlength="99" placeholder="Pick a name for security key" class="form-control mb-4">
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" onclick="newregistration()" class="btn btn-primary">Register</button>
                                        </div>
                                    </div>
                                </form>
                                <!--end::Modal header-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - disable 2fa App-->
                </div>
            </div>
        </div>
    </div>
    <!-- Show / hide columns dynamically -->
    <!-- Column rendering -->
    <!-- Row grouping -->
    <!-- Multiple table control element -->
</div>
<!--end::Container-->