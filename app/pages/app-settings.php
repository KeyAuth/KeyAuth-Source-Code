<?php
if ($_SESSION['role'] == "Reseller") 
{
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 1024)) 
{
    die('You weren\'t granted permissions to view this page.');
}
if (!isset($_SESSION['app'])) 
{
    die("Application not selected.");
}
if (isset($_POST['addhash'])) 
{
    $resp = misc\app\addHash($_POST['hash']);
    switch ($resp) 
    {
        case 'failure':
            dashboard\primary\error("Failed add hash!");
            break;
        case 'success':
            dashboard\primary\success("Added hash successfully");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['resethash'])) 
{
    $resp = misc\app\resetHash();
    switch ($resp) 
    {
        case 'failure':
            dashboard\primary\error("Failed reset hash!");
            break;
        case 'success':
            dashboard\primary\success("Reset hash successfully");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['updatesettings'])) 
{
    $status = misc\etc\sanitize($_POST['statusinput']);
    $hwid = misc\etc\sanitize($_POST['hwidinput']);
    $forceHwid = misc\etc\sanitize($_POST['forceHwid']);
    $minHwid = misc\etc\sanitize($_POST['minHwid']);
    $vpn = misc\etc\sanitize($_POST['vpninput']);
    $hashstatus = misc\etc\sanitize($_POST['hashinput']);
    $ver = misc\etc\sanitize($_POST['version']);
    $dl = misc\etc\sanitize($_POST['download']);
    $webdl = misc\etc\sanitize($_POST['webdownload']);
    $webhook = misc\etc\sanitize($_POST['webhook']);
    $resellerstorelink = misc\etc\sanitize($_POST['resellerstore']);
    $panelstatus = misc\etc\sanitize($_POST['panelstatus']);
    $customDomain = misc\etc\sanitize($_POST['customDomain']);
    $cooldownexpiry = misc\etc\sanitize($_POST['cooldownexpiry']);
    $cooldownduration = misc\etc\sanitize($_POST['cooldownduration']) * $cooldownexpiry;
    $sessionexpiry = misc\etc\sanitize($_POST['sessionexpiry']);
    $sessionduration = misc\etc\sanitize($_POST['sessionduration']) * $sessionexpiry;
    $minUsernameLength = misc\etc\sanitize($_POST['minUsernameLength']);
    $blockLeakedPasswords = misc\etc\sanitize($_POST['blockLeakedPasswords']);
    $appdisabledpost = misc\etc\sanitize($_POST['appdisabled']);
    $usernametakenpost = misc\etc\sanitize($_POST['usernametaken']);
    $keynotfoundpost = misc\etc\sanitize($_POST['keynotfound']);
    $keyusedpost = misc\etc\sanitize($_POST['keyused']);
    $nosublevelpost = misc\etc\sanitize($_POST['nosublevel']);
    $usernamenotfoundpost = misc\etc\sanitize($_POST['usernamenotfound']);
    $passmismatchpost = misc\etc\sanitize($_POST['passmismatch']);
    $hwidmismatchpost = misc\etc\sanitize($_POST['hwidmismatch']);
    $noactivesubspost = misc\etc\sanitize($_POST['noactivesubs']);
    $hwidblackedpost = misc\etc\sanitize($_POST['hwidblacked']);
    $pausedsubpost = misc\etc\sanitize($_POST['pausedsub']);
    $vpnblockedpost = misc\etc\sanitize($_POST['vpnblocked']);
    $keybannedpost = misc\etc\sanitize($_POST['keybanned']);
    $userbannedpost = misc\etc\sanitize($_POST['userbanned']);
    $sessionunauthedpost = misc\etc\sanitize($_POST['sessionunauthed']);
    $hashcheckfailpost = misc\etc\sanitize($_POST['hashcheckfail']);
    $shoppywebhooksecret = misc\etc\sanitize($_POST['shoppywebhooksecret']);
    $shoppyday = misc\etc\sanitize($_POST['shoppydayproduct']);
    $shoppyweek = misc\etc\sanitize($_POST['shoppyweekproduct']);
    $shoppymonth = misc\etc\sanitize($_POST['shoppymonthproduct']);
    $shoppylife = misc\etc\sanitize($_POST['shoppylifetimeproduct']);
    $sellixwebhooksecret = misc\etc\sanitize($_POST['sellixwebhooksecret']);
    $sellixday = misc\etc\sanitize($_POST['sellixdayproduct']);
    $sellixweek = misc\etc\sanitize($_POST['sellixweekproduct']);
    $sellixmonth = misc\etc\sanitize($_POST['sellixmonthproduct']);
    $sellixlife = misc\etc\sanitize($_POST['sellixlifetimeproduct']);
    $sellappwebhooksecret = misc\etc\sanitize($_POST['sellappwebhooksecret']);
    $sellappday = misc\etc\sanitize($_POST['sellappdayproduct']);
    $sellappweek = misc\etc\sanitize($_POST['sellappweekproduct']);
    $sellappmonth = misc\etc\sanitize($_POST['sellappmonthproduct']);
    $sellapplife = misc\etc\sanitize($_POST['sellapplifetimeproduct']);
    $customerPanelIcon = misc\etc\sanitize($_POST['customerPanelIcon']);
    $loggedInMsg = misc\etc\sanitize($_POST['loggedInMsg']);
    $pausedApp = misc\etc\sanitize($_POST['pausedApp']);
    $unTooShort = misc\etc\sanitize($_POST['unTooShort']);
    $pwLeaked = misc\etc\sanitize($_POST['pwLeaked']);
    $chatHitDelay = misc\etc\sanitize($_POST['chatHitDelay']);

    if (!is_null($customDomain) && ($_SESSION['role'] == "seller" || $_SESSION['role'] == "Manager")) 
    {
        if (strpos($customDomain, "http") === 0) 
        {
            dashboard\primary\error("Do not include protocol. Your custom domain should be entered as panel.example.com not https://panel.example.com or http://panel.example.com");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        $query = misc\mysql\query("SELECT `name`, `owner` FROM `apps` WHERE `customDomain` = ? AND `secret` != ?", [$customDomain, $_SESSION['app']]);
        if ($query->num_rows > 0) 
        {
            $row = mysqli_fetch_array($query->result);
            $name = $row["name"];
            $owner = $row["owner"];
            dashboard\primary\error("The domain {$customDomain} is already being used on app named \"{$name}\" owned by {$owner}. Use a different domain or subdomain please.");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
    } 
    else if (!empty($customDomain)) 
    {
        dashboard\primary\error("You must have seller plan to utilize customer panel");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if($_SESSION['role'] == "tester" && !is_null($webhook)) 
    {
        dashboard\primary\error("You must upgrade to developer or seller to use Discord webhook!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if((!is_null($webhook) && !str_contains($webhook, "discord")) || (!is_null($webhook) && str_contains($webhook, "localhost")) || (!is_null($webhook) && str_contains($webhook, "127.0.0.1"))) 
    {
        dashboard\primary\error("Webhook URL is supposed to be a Discord webhook!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if($_SESSION['role'] == "tester" && $vpn) 
    {
        dashboard\primary\error("You must upgrade to developer or seller to use VPN block!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if (!empty($shoppywebhooksecret) && !empty($sellixwebhooksecret)) 
    {
        dashboard\primary\error("You cannot utilize Sellix and Shoppy simultaneously due to conflicting JavaScript code");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if ($sessionduration > 604800) 
    {
        dashboard\primary\error("Session duration can be at most 7 days/1 week");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $query = misc\mysql\query(
        "UPDATE `apps` SET 
                `cooldown` = ?,
                `customDomain` = NULLIF(?, ''),
                `session` = ?,
                `cooldownUnit` = ?,
                `sessionUnit` = ?,
                `appdisabled` = ?,
                `hashcheckfail` = ?,
                `sessionunauthed` = ?,
                `userbanned` = ?,
                `vpnblocked` = ?,
                `keybanned` = ?,
                `usernametaken` = ?,
                `keynotfound` = ?,
                `keyused` = ?,
                `nosublevel` = ?,
                `usernamenotfound` = ?,
                `passmismatch` = ?,
                `hwidmismatch` = ?,
                `noactivesubs` = ?,
                `hwidblacked` = ?,
                `pausedsub` = ?,
                `enabled` = ?,
                `minUsernameLength` = ?,
                `blockLeakedPasswords` = ?,
                `hashcheck` = ?,
                `hwidcheck` = ?,
                `forceHwid` = ?,
                `minHwid` = ?,
                `vpnblock` = ?,
                `ver` = ?,
                `download` = NULLIF(?, ''),
                `webdownload` = NULLIF(?, ''),
                `webhook` = NULLIF(?, ''),
                `resellerstore` = NULLIF(?, ''),
                `sellixsecret` = NULLIF(?, ''),
                `sellixdayproduct` = NULLIF(?, ''),
                `sellixweekproduct` = NULLIF(?, ''),
                `sellixmonthproduct` = NULLIF(?, ''),
                `sellixlifetimeproduct` = NULLIF(?, ''),
                `sellappsecret` = NULLIF(?, ''),
                `sellappdayproduct` = NULLIF(?, ''),
                `sellappweekproduct` = NULLIF(?, ''),
                `sellappmonthproduct` = NULLIF(?, ''),
                `sellapplifetimeproduct` = NULLIF(?, ''),
                `shoppysecret` = NULLIF(?, ''),
                `shoppydayproduct` = NULLIF(?, ''),
                `shoppyweekproduct` = NULLIF(?, ''),
                `shoppymonthproduct` = NULLIF(?, ''),
                `shoppylifetimeproduct` = NULLIF(?, ''),
                `customerPanelIcon` = ?,
                `panelstatus` = ?,
                `loggedInMsg` = ?,
                `pausedApp` = ?,
                `unTooShort` = ?,
                `pwLeaked` = ?,
                `chatHitDelay` = ?
        WHERE `secret` = ?",
        [
            $cooldownduration,
            $customDomain,
            $sessionduration,
            $cooldownexpiry,
            $sessionexpiry,
            $appdisabledpost,
            $hashcheckfailpost,
            $sessionunauthedpost,
            $userbannedpost,
            $vpnblockedpost,
            $keybannedpost,
            $usernametakenpost,
            $keynotfoundpost,
            $keyusedpost,
            $nosublevelpost,
            $usernamenotfoundpost,
            $passmismatchpost,
            $hwidmismatchpost,
            $noactivesubspost,
            $hwidblackedpost,
            $pausedsubpost,
            $status,
            $minUsernameLength,
            $blockLeakedPasswords,
            $hashstatus,
            $hwid,
            $forceHwid,
            $minHwid,
            $vpn,
            $ver,
            $dl,
            $webdl,
            $webhook,
            $resellerstorelink,
            $sellixwebhooksecret,
            $sellixday,
            $sellixweek,
            $sellixmonth,
            $sellixlife,
            $sellappwebhooksecret,
            $sellappday,
            $sellappweek,
            $sellappmonth,
            $sellapplife,
            $shoppywebhooksecret,
            $shoppyday,
            $shoppyweek,
            $shoppymonth,
            $shoppylife,
            $customerPanelIcon,
            $panelstatus,
            $loggedInMsg,
            $pausedApp,
            $unTooShort,
            $pwLeaked,
            $chatHitDelay,
            $_SESSION['app']
        ]
    );

    if ($query->affected_rows > 0) 
    {
        misc\cache\purge('KeyAuthApp:' . $_SESSION["name"] . ':' . $_SESSION['ownerid']);
        dashboard\primary\success("Successfully set settings!");
    } 
    else 
    {
        dashboard\primary\error("Failed to set settings!");
    }
}

?>
    <!-- Include the jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
    $('div.modal-content').css('border', '2px solid #1b8adb');
    });
    </script>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
        <form method="post">
                <button data-bs-toggle="modal" type="button" data-bs-target="#add-hash"
                        class="dt-button buttons-print btn btn-primary mr-1"><i
                        class="fas fa-plus-circle fa-sm text-white-50"></i>
                        Add hash</button><br><br>
                <button data-bs-toggle="modal" type="button" data-bs-target="#reset-hash"
                        class="dt-button buttons-print btn btn-danger mr-1"><i
                        class="fas fa-redo-alt fa-sm text-white-50"></i>
                        Reset program hash</button>
        </form>
        <br>

        <div id="add-hash" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
                style="display: none;">
                <div class="modal-dialog">
                        <div class="modal-content">
                                <div class="modal-header d-flex align-items-center">
                                        <h4 class="modal-title">Add hash</h4>
                                        <!--begin::Close-->
                                        <div class="btn btn-sm btn-icon btn-active-color-primary"
                                                data-bs-dismiss="modal">
                                                <span class="svg-icon svg-icon-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                        height="2" rx="1"
                                                                        transform="rotate(-45 6 17.3137)"
                                                                        fill="black" />
                                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                                        transform="rotate(45 7.41422 6)" fill="black" />
                                                        </svg>
                                                </span>
                                        </div>
                                        <!--end::Close-->
                                </div>
                                <div class="modal-body">
                                        <form method="post">
                                                <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Application
                                                                hash:</label>
                                                        <input type="text" class="form-control" name="hash"
                                                                placeholder="MD5 program hash to add" required>
                                                </div>
                                </div>
                                <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-danger waves-effect waves-light"
                                                name="addhash">Add</button>
                                        </form>
                                </div>
                        </div>
                </div>
                <br>
        </div>

        <div class="modal fade" tabindex="-1" id="reset-hash">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-900px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                        <h2 class="modal-title">Reset Hash</h2>
                                        <!--begin::Close-->
                                        <div class="btn btn-sm btn-icon btn-active-color-primary"
                                                data-bs-dismiss="modal">
                                                <span class="svg-icon svg-icon-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                        height="2" rx="1"
                                                                        transform="rotate(-45 6 17.3137)"
                                                                        fill="black" />
                                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                                        transform="rotate(45 7.41422 6)" fill="black" />
                                                        </svg>
                                                </span>
                                        </div>
                                        <!--end::Close-->
                                </div>
                                <div class="modal-body">
                                        <label class="fs-5 fw-bold mb-2">
                                                <p> Are you sure you want to reset hash? Only do if you're releasing new
                                                        program. This can not be undone.</p>
                                        </label>
                                </div>
                                <div class="modal-footer">
                                        <form method="post">
                                                <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                <button name="resethash" class="btn btn-danger">Yes</button>
                                        </form>
                                </div>
                        </div>
                </div>
        </div>

        <?php
    if ($_SESSION['app']) {
        $query = misc\mysql\query("SELECT * FROM `apps` WHERE `secret` = ?", [$_SESSION['app']]);
        if ($query->num_rows > 0) {
            while ($row = mysqli_fetch_array($query->result)) 
            {
                $enabled = $row['enabled'];
                $hwidcheck = $row['hwidcheck'];
                $forceHwid = $row['forceHwid'];
                $minHwid = $row['minHwid'];
                $vpnblock = $row['vpnblock'];
                $panelstatus = $row['panelstatus'];
                $customDomain = $row['customDomain'];
                $hashcheck = $row['hashcheck'];
                $cooldown = $row['cooldown'];
                $session = $row['session'];
                $cooldownUnit = $row['cooldownUnit'];
                $sessionUnit = $row['sessionUnit'];
                $verr = $row['ver'];
                $dll = $row['download'];
                $webdll = $row['webdownload'];
                $wh = $row['webhook'];
                $rs = $row['resellerstore'];
                $sellappwhsecret = $row['sellappsecret'];
                $sellappdayproduct = $row['sellappdayproduct'];
                $sellappweekproduct = $row['sellappweekproduct'];
                $sellappmonthproduct = $row['sellappmonthproduct'];
                $sellapplifetimeproduct = $row['sellapplifetimeproduct'];
                $sellixwhsecret = $row['sellixsecret'];
                $sellixdayproduct = $row['sellixdayproduct'];
                $sellixweekproduct = $row['sellixweekproduct'];
                $sellixmonthproduct = $row['sellixmonthproduct'];
                $sellixlifetimeproduct = $row['sellixlifetimeproduct'];
                $shoppywhsecret = $row['shoppysecret'];
                $shoppydayproduct = $row['shoppydayproduct'];
                $shoppyweekproduct = $row['shoppyweekproduct'];
                $shoppymonthproduct = $row['shoppymonthproduct'];
                $shoppylifetimeproduct = $row['shoppylifetimeproduct'];
                $appdisabled = $row['appdisabled'];
                $usernametaken = $row['usernametaken'];
                $keynotfound = $row['keynotfound'];
                $keyused = $row['keyused'];
                $nosublevel = $row['nosublevel'];
                $usernamenotfound = $row['usernamenotfound'];
                $passmismatch = $row['passmismatch'];
                $hwidmismatch = $row['hwidmismatch'];
                $noactivesubs = $row['noactivesubs'];
                $hwidblacked = $row['hwidblacked'];
                $pausedsub = $row['pausedsub'];
                $vpnblocked = $row['vpnblocked'];
                $keybanned = $row['keybanned'];
                $userbanned = $row['userbanned'];
                $sessionunauthed  = $row['sessionunauthed'];
                $hashcheckfail  = $row['hashcheckfail'];
                $minUsernameLength  = $row['minUsernameLength'];
                $blockLeakedPasswords  = $row['blockLeakedPasswords'];
                $customerPanelIcon  = $row['customerPanelIcon'];
                $loggedInMsg  = $row['loggedInMsg'];
                $pausedApp  = $row['pausedApp'];
                $unTooShort  = $row['unTooShort'];
                $pwLeaked  = $row['pwLeaked'];
                $chatHitDelay  = $row['chatHitDelay'];
            }
        }
    }

    ?>
        <div class="card">
                <div class="card-body">
                        <form class="form" method="post">
                                <button name="updatesettings" class="btn btn-success"> <i class="fa fa-check"></i> Save
                                        settings</button>
                                <br>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Status <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Allow people to open application or not"></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="statusinput">
                                                        <option value="0"
                                                                <?= $enabled == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $enabled == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">API custom domain <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Most auths get blocked by some internet providers falsely. Using a custom domain will fix this, watch tutorial video on youtube.com/keyauth"></i></label>
                                        <div class="col-10">
                                                <label class="form-control" style="height:auto;">
                                                        You just need to set a CNAME record to <code>api.keyauth.win</code> Make sure you're using Cloudflare! 
                                                </label>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">HWID Lock <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Lock user to a value from your user's computer which only changes if they reinstall windows. Use this to prevent people sharing your product"></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="hwidinput">
                                                        <option value="0"
                                                                <?= $hwidcheck == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $hwidcheck == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Force HWID Lock <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Deny users logging in with blank HWID (disable for PHP example)"></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="forceHwid">
                                                        <option value="0"
                                                                <?= $forceHwid == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $forceHwid == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Minimum HWID length <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Deny users logging in with a HWID shorter than this number in characters."></i></label>
                                        <div class="col-10">
                                                <input class="form-control" type="number" name="minHwid"
                                                        value="<?php echo $minHwid; ?>" placeholder="Minimum HWID length in characters">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">VPN Block <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Block IP addresses associated with VPNs"></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="vpninput">
                                                        <option value="0"
                                                                <?= $vpnblock == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $vpnblock == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Hash Check <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Checks whether the application has been modified since the last time you pressed reset hash button. Used to stop people altering your program to bypass it."></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="hashinput">
                                                        <option value="0"
                                                                <?= $hashcheck == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $hashcheck == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Version <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="If you change this, the download link will be opened on your user's computer when they run the loader with the old version."></i></label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="5" name="version"
                                                        value="<?php echo $verr; ?>" placeholder="Application Verion.." required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Download</label>
                                        <div class="col-10">
                                                <input class="form-control" name="download" value="<?php echo $dll; ?>"
                                                        type="text"
                                                        placeholder="URL Link That Will Be Opened If Version doesn't match (auto update)" maxlength="120">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Web Download</label>
                                        <div class="col-10">
                                                <input class="form-control" name="webdownload"
                                                        value="<?php echo $webdll; ?>" type="text"
                                                        placeholder="URL link for web loader (this will enable web loader if not empty)" maxlength="120">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Webhook <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="This is where you put Discord webhooks, not the webhooks page."></i></label>
                                        <div class="col-10">
                                                <input class="form-control" name="webhook" value="<?php echo $wh; ?>"
                                                        type="text"
                                                        placeholder="Discord Webhook Link For Sending Notifications & Logs">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Reseller Store <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="If you're not using built-in reseller system, set a link will show to your resellers for them to buy keys."></i></label>
                                        <div class="col-10">
                                                <input class="form-control" name="resellerstore"
                                                        value="<?php echo $rs; ?>"
                                                        placeholder="If you don't want to use the inbuilt store for resellers, set a store link."
                                                        type="text">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Customer Panel <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Allows your customers to log in with their username and password (which if using just key is the same) and reset their HWID and download latest application from KeyAuth website"></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="panelstatus">
                                                        <option value="0"
                                                                <?= $panelstatus == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled</option>
                                                        <option value="1"
                                                                <?= $panelstatus == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Customer panel
                                                link</label>
                                        <div class="col-10">
                                                <label class="form-control" style="height:auto;"><?php
                                                                            echo '<a href="https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/panel/' . urlencode($_SESSION['username']) . '/' . urlencode($_SESSION['name']) . '" target="_blank">https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/panel/' . $_SESSION['username'] . '/' . $_SESSION['name'] . '</a>';
                                                                            ?></label>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Customer panel
                                                custom domain <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Custom domain for customer panel. Please lookup KeyAuth YouTube video for custom domains for instructions"></i></label>
                                        <div class="col-10">
                                                <input class="form-control" name="customDomain"
                                                        value="<?php echo $customDomain; ?>"
                                                        placeholder="panel.example.com" type="text">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Customer panel icon
                                                <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Image shown in SEO and next to title in browser tab"></i></label>
                                        <div class="col-10">
                                                <input class="form-control" name="customerPanelIcon"
                                                        value="<?php echo $customerPanelIcon; ?>"
                                                        placeholder="https://cdn.keyauth.cc/front/assets/img/favicon.png"
                                                        type="text" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">HWID Reset Cooldown
                                                Unit: <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Delay between the last time your customer reset their HWID to when they can reset again. That way, they can't reset it once, allow their friend to login, and then reset it again for themselves."></i></label>
                                        <div class="col-10">
                                                <select name="cooldownexpiry" class="form-control">
                                                        <option value="86400"
                                                                <?= $cooldownUnit == 86400 ? ' selected="selected"' : ''; ?>>
                                                                Days
                                                        </option>
                                                        <option value="60"
                                                                <?= $cooldownUnit == 60 ? ' selected="selected"' : ''; ?>>
                                                                Minutes
                                                        </option>
                                                        <option value="3600"
                                                                <?= $cooldownUnit == 3600 ? ' selected="selected"' : ''; ?>>
                                                                Hours
                                                        </option>
                                                        <option value="1"
                                                                <?= $cooldownUnit == 1 ? ' selected="selected"' : ''; ?>>
                                                                Seconds</option>
                                                        <option value="604800"
                                                                <?= $cooldownUnit == 604800 ? ' selected="selected"' : ''; ?>>
                                                                Weeks
                                                        </option>
                                                        <option value="2629743"
                                                                <?= $cooldownUnit == 2629743 ? ' selected="selected"' : ''; ?>>
                                                                Months</option>
                                                        <option value="31556926"
                                                                <?= $cooldownUnit == 31556926 ? ' selected="selected"' : ''; ?>>
                                                                Years</option>
                                                        <option value="315569260"
                                                                <?= $cooldownUnit == 315569260 ? ' selected="selected"' : ''; ?>>
                                                                Lifetime</option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">HWID Reset Cooldown
                                                Duration:</label>
                                        <div class="col-10">
                                                <input name="cooldownduration" type="number" class="form-control"
                                                        value="<?php echo $cooldown / $cooldownUnit; ?>"
                                                        placeholder="Multiplied by selected cooldown unit" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Session Expiry Unit:
                                                <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="This is how long your users can stay logged in for. After it pasts this time, if you call any functions that require a session it will close the loader."></i></label>
                                        <div class="col-10">
                                                <select name="sessionexpiry" class="form-control">
                                                        <option value="86400"
                                                                <?= $sessionUnit == 86400 ? ' selected="selected"' : ''; ?>>
                                                                Days
                                                        </option>
                                                        <option value="60"
                                                                <?= $sessionUnit == 60 ? ' selected="selected"' : ''; ?>>
                                                                Minutes</option>
                                                        <option value="3600"
                                                                <?= $sessionUnit == 3600 ? ' selected="selected"' : ''; ?>>
                                                                Hours
                                                        </option>
                                                        <option value="1"
                                                                <?= $sessionUnit == 1 ? ' selected="selected"' : ''; ?>>
                                                                Seconds</option>
                                                        <option value="604800"
                                                                <?= $sessionUnit == 604800 ? ' selected="selected"' : ''; ?>>
                                                                Weeks
                                                        </option>
                                                </select>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Session Expiry
                                                Duration:</label>
                                        <div class="col-10">
                                                <input name="sessionduration" type="number" class="form-control"
                                                        value="<?php echo $session / $sessionUnit; ?>"
                                                        placeholder="Multiplied by selected expiry unit" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Minimum username
                                                length: <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Reject registrations with username having character count under this number"></i></label>
                                        <div class="col-10">
                                                <input name="minUsernameLength" type="number" class="form-control"
                                                        value="<?php echo $minUsernameLength; ?>" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Block leaked
                                                passwords <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Uses HaveIBeenPwned API to securely check if password has been leaked without sharing password."></i></label>
                                        <div class="col-10">
                                                <select class="form-control" name="blockLeakedPasswords">
                                                        <option value="0"
                                                                <?= $blockLeakedPasswords == 0 ? ' selected="selected"' : ''; ?>>
                                                                Disabled
                                                        </option>
                                                        <option value="1"
                                                                <?= $blockLeakedPasswords == 1 ? ' selected="selected"' : ''; ?>>
                                                                Enabled
                                                        </option>
                                                </select>
                                        </div>
                                </div>
                                <br> <br> <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">App Disabled Msg <i
                                                        class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="All the textboxes in this section are the custom error responses. Success messages aren't custom since you shouldn't need to show it."></i></label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="appdisabled"
                                                        id="defaultconfig-3" value="<?php echo $appdisabled; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Hash check
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="hashcheckfail"
                                                        id="defaultconfig-3" value="<?php echo $hashcheckfail; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">VPN Blocked
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="vpnblocked"
                                                        id="defaultconfig-3" value="<?php echo $vpnblocked; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Username Taken
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="usernametaken"
                                                        id="defaultconfig-3" value="<?php echo $usernametaken; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Invalid Key
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="keynotfound"
                                                        id="defaultconfig-3" value="<?php echo $keynotfound; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Used Key Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="keyused"
                                                        id="defaultconfig-3" value="<?php echo $keyused; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Key Banned
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="keybanned"
                                                        id="defaultconfig-3" value="<?php echo $keybanned; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">No Subs Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="nosublevel"
                                                        id="defaultconfig-3" value="<?php echo $nosublevel; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">User Banned
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="userbanned"
                                                        id="defaultconfig-3" value="<?php echo $userbanned; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Username Invalid
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="usernamenotfound"
                                                        id="defaultconfig-3" value="<?php echo $usernamenotfound; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Password Mismatch
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="passmismatch"
                                                        id="defaultconfig-3" value="<?php echo $passmismatch; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Hwid Mismatch
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="hwidmismatch"
                                                        id="defaultconfig-3" value="<?php echo $hwidmismatch; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Expired Sub
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="noactivesubs"
                                                        id="defaultconfig-3" value="<?php echo $noactivesubs; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Blacklisted
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="hwidblacked"
                                                        id="defaultconfig-3" value="<?php echo $hwidblacked; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Paused Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="pausedsub"
                                                        id="defaultconfig-3" value="<?php echo $pausedsub; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Session
                                                Unauthenticated Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="sessionunauthed"
                                                        id="defaultconfig-3" value="<?php echo $sessionunauthed; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Successful login
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="loggedInMsg"
                                                        id="defaultconfig-3" value="<?php echo $loggedInMsg; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Paused application
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="pausedApp"
                                                        id="defaultconfig-3" value="<?php echo $pausedApp; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Username too short
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="unTooShort"
                                                        id="defaultconfig-3" value="<?php echo $unTooShort; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Password leaked
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="pwLeaked"
                                                        id="defaultconfig-3" value="<?php echo $pwLeaked; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Chat delay hit
                                                Msg</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="100" name="chatHitDelay"
                                                        id="defaultconfig-3" value="<?php echo $chatHitDelay; ?>"
                                                        placeholder="Custom response you'd like. Max 100 chars" required>
                                        </div>
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Reseller Webhook
                                                Link <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="This is the same if you're using Sellix or Shoppy, create webhook with this link for the event order:paid"></i></label>
                                        <div class="col-10">
                                                <label class="form-control"
                                                        style="height:auto;"><?php echo '<a href="https://' . (($_SERVER['HTTP_HOST'] == "keyauth.cc") ? "keyauth.win" : $_SERVER['HTTP_HOST']) . '/api/reseller/?app=' . $_SESSION['secret'] . '" target="target_" class="secretlink">https://' . (($_SERVER['HTTP_HOST'] == "keyauth.cc") ? "keyauth.win" : $_SERVER['HTTP_HOST']) . '/api/reseller/?app=' . $_SESSION['secret'] . '</a>'; ?></label>
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Shoppy Webhook
                                                Secret <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Shoppy webhook secret for reseller system"></i></label>
                                        <div class="col-10">
                                                <input class="form-control secret" maxlength="16"
                                                        name="shoppywebhooksecret"
                                                        value="<?php echo $shoppywhsecret; ?>" id="defaultconfig-3"
                                                        placeholder="Webhook secret found in General Shop Settings on Shoppy">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Shoppy Day Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="7" name="shoppydayproduct"
                                                        value="<?php echo $shoppydayproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Day Reseller Key Shoppy Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Shoppy Week Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="7" name="shoppyweekproduct"
                                                        value="<?php echo $shoppyweekproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Week Reseller Key Shoppy Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Shoppy Month Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="7" name="shoppymonthproduct"
                                                        value="<?php echo $shoppymonthproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Month Reseller Key Shoppy Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Shoppy Lifetime
                                                Product ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="7" name="shoppylifetimeproduct"
                                                        value="<?php echo $shoppylifetimeproduct; ?>"
                                                        id="defaultconfig-3"
                                                        placeholder="Product ID of Lifetime Reseller Key Shoppy Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Sellix Webhook
                                                Secret <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Sellix webhook secret for reseller system"></i></label>
                                        <div class="col-10">
                                                <input class="form-control secret" maxlength="32"
                                                        name="sellixwebhooksecret"
                                                        value="<?php echo $sellixwhsecret; ?>" id="defaultconfig-3"
                                                        placeholder="Webhook secret found in General Shop Settings on Sellix">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Sellix Day Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="13" name="sellixdayproduct"
                                                        value="<?php echo $sellixdayproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Day Reseller Key Sellix Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Sellix Week Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="13" name="sellixweekproduct"
                                                        value="<?php echo $sellixweekproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Week Reseller Key Sellix Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Sellix Month Product
                                                ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="13" name="sellixmonthproduct"
                                                        value="<?php echo $sellixmonthproduct; ?>" id="defaultconfig-3"
                                                        placeholder="Product ID of Month Reseller Key Sellix Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Sellix Lifetime
                                                Product ID</label>
                                        <div class="col-10">
                                                <input class="form-control" maxlength="13" name="sellixlifetimeproduct"
                                                        value="<?php echo $sellixlifetimeproduct; ?>"
                                                        id="defaultconfig-3"
                                                        placeholder="Product ID of Lifetime Reseller Key Sellix Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">SellApp Webhook
                                                Secret <i class="fas fa-question-circle fa-lg text-white-50"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="SellApp webhook secret for reseller system"></i></label>
                                        <div class="col-10">
                                                <input class="form-control secret" maxlength="64"
                                                        name="sellappwebhooksecret"
                                                        value="<?php echo $sellappwhsecret; ?>" id="defaultconfig-3"
                                                        placeholder="Webhook secret found in Developer Settings on SellApp">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">SellApp Day Product
                                                URL</label>
                                        <div class="col-10">
                                                <input class="form-control" name="sellappdayproduct"
                                                        value="<?php echo $sellappdayproduct; ?>" id="defaultconfig-3"
                                                        placeholder="URL for Day Reseller Key SellApp Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">SellApp Week Product
                                                URL</label>
                                        <div class="col-10">
                                                <input class="form-control" name="sellappweekproduct"
                                                        value="<?php echo $sellappweekproduct; ?>" id="defaultconfig-3"
                                                        placeholder="URL for Week Reseller Key SellApp Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">SellApp Month
                                                Product URL</label>
                                        <div class="col-10">
                                                <input class="form-control" name="sellappmonthproduct"
                                                        value="<?php echo $sellappmonthproduct; ?>" id="defaultconfig-3"
                                                        placeholder="URL for Month Reseller Key SellApp Product">
                                        </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">SellApp Lifetime
                                                Product URL</label>
                                        <div class="col-10">
                                                <input class="form-control" name="sellapplifetimeproduct"
                                                        value="<?php echo $sellapplifetimeproduct; ?>"
                                                        id="defaultconfig-3"
                                                        placeholder="URL for Lifetime Reseller Key SellApp Product">
                                        </div>
                                </div>
                        </form>
                </div>
        </div>
</div>
<!--end::Container-->