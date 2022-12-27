<?php
if ($_SESSION['role'] == "Reseller") {
	header("location: ./?page=reseller-licenses");
	die();
}
?>

<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">

    <?php

	if (isset($_POST['selectApp'])) {
		$appName = misc\etc\sanitize($_POST['selectApp']);
		($result = mysqli_query($link, "SELECT `secret`, `name`, `banned` FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '$appName'"));
		
		if (mysqli_num_rows($result) < 1) {
			dashboard\primary\error("Application not found!");
		}
		else {
			$row = mysqli_fetch_array($result);
			$banned = $row["banned"];
			
			if($banned) {
				dashboard\primary\error("Application is banned!");
			}
			else {
				$_SESSION["app"] = $row["secret"];
				$_SESSION["name"] = $appName;
				$_SESSION["selectedApp"] = $row["name"];
				
				echo '<meta http-equiv="refresh" content="0">'; // needed to refresh nav sidebar
				dashboard\primary\success("Successfully Selected the App!");
			}
		}
	}

	if (isset($_POST['create_app'])) {

		$appname = misc\etc\sanitize($_POST['appname']);
		if ($appname == "") {
			dashboard\primary\error("Input a valid name");
			return;
		}

		$result = mysqli_query($link, "SELECT * FROM apps WHERE name='$appname' AND owner='" . $_SESSION['username'] . "'");
		if (mysqli_num_rows($result) > 0) {
			mysqli_close($link);
			dashboard\primary\error("You already own application with this name!");
			return;
		}

		$owner = $_SESSION['username'];

		if ($role == "tester") {
			$result = mysqli_query($link, "SELECT * FROM apps WHERE owner='$owner'");

			if (mysqli_num_rows($result) > 0) {
				mysqli_close($link);
				dashboard\primary\error("Tester plan only supports one application!");
				return;
			}
		}

		if ($role == "Manager") {
			mysqli_close($link);
			dashboard\primary\error("Manager Accounts Are Not Allowed To Create Applications");
			return;
		}

		$ownerid = $_SESSION['ownerid'];
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
		mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('default', '1', '$clientsecret')");
		mysqli_query($link, "INSERT INTO `apps`(`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES ('" . $owner . "','" . $appname . "','" . $clientsecret . "','$ownerid', '1','1','$sellerkey')");
		if (mysqli_affected_rows($link) != 0) {
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
			return;
		}
		if ($role == "Manager") {
			dashboard\primary\error("Manager Accounts Aren\'t Allowed To Rename Applications");
			return;
		}
		$result = mysqli_query($link, "SELECT 1 FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '$appname'");
		$num = mysqli_num_rows($result);
		if ($num > 0) {
			dashboard\primary\error("You already have an application with this name!");
			return;
		}
		mysqli_query($link, "UPDATE `accounts` SET `app` = '$appname' WHERE `app` = '" . $_SESSION['name'] . "' AND `owner` = '" . $_SESSION['username'] . "'");
		mysqli_query($link, "UPDATE `apps` SET `name` = '$appname' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");
		$oldName = $_SESSION['name'];
		$_SESSION['name'] = $appname;
		if (mysqli_affected_rows($link) != 0) {
			dashboard\primary\success("Successfully Renamed App!");
			misc\cache\purge('KeyAuthApp:' . $oldName . ':' . $_SESSION['ownerid']);
			if ($_SESSION['role'] == "seller") {
				$result = mysqli_query($link, "SELECT `customDomain`, `sellerkey`, `customDomainAPI` FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '$appname'");
				$row = mysqli_fetch_array($result);
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
			dashboard\primary\error("Manager accounts aren\'t allowed to pause applications");
			return;
		}
		misc\cache\purgePattern('KeyAuthSubs:' . $_SESSION["app"]);
		misc\app\pause();
		dashboard\primary\success("Paused application and any active subscriptions!");
	}
	if (isset($_POST['unpauseapp'])) {
		if ($role == "Manager") {
			dashboard\primary\error("Manager accounts aren\'t allowed to unpause applications");
			return;
		}
		misc\cache\purgePattern('KeyAuthSubs:' . $_SESSION["app"]);
		misc\app\unpause();
		dashboard\primary\success("Unpaused application and any paused subscriptions!");
	}

	if (isset($_POST['refreshapp'])) {
		if ($role == "Manager") {
			dashboard\primary\error("Manager Accounts Aren\'t Allowed To Refresh Applications");
			return;
		}
        $gen = misc\etc\generateRandomString();
		$new_secret = hash('sha256', $gen);
		mysqli_query($link, "UPDATE `apps` SET `secret` = '$new_secret' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");
		$_SESSION['secret'] = $new_secret;
		if (mysqli_affected_rows($link) != 0) {
			mysqli_query($link, "UPDATE `bans` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `buttons` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `chatmsgs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `chatmutes` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `chats` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `files` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `keys` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `logs` SET `logapp` = '$new_secret' WHERE `logapp` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `sessions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `subs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `subscriptions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `users` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `uservars ` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `vars` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			mysqli_query($link, "UPDATE `webhooks` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
			$_SESSION['app'] = $new_secret;
			misc\cache\purge('KeyAuthApp:' . $_SESSION['name'] . ':' . $_SESSION['ownerid']);
			if ($_SESSION['role'] == "seller" || $_SESSION['role'] == "developer") {
				$result = mysqli_query($link, "SELECT `sellerkey`,`customDomainAPI` FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '" . $_SESSION['name'] . "'");
				$row = mysqli_fetch_array($result);
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
			error("Manager Accounts Aren\'t Allowed To Delete Applications");
			return;
		}
		$app = $_SESSION['app'];
		mysqli_query($link, "DELETE FROM `apps` WHERE `secret` = '$app'") or die(mysqli_error($link));
		if (mysqli_affected_rows($link) != 0) {
			mysqli_query($link, "DELETE FROM `files` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete files
			mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete keys
			mysqli_query($link, "DELETE FROM `logs` WHERE `logapp` = '$app'") or die(mysqli_error($link)); // delete logs
			mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete bans
			mysqli_query($link, "DELETE FROM `buttons` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete buttons
			mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete chat messages
			mysqli_query($link, "DELETE FROM `chatmutes` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete chat mutes
			mysqli_query($link, "DELETE FROM `chats` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete chat channels
			mysqli_query($link, "DELETE FROM `sessions` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete sessionss
			mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete user subscriptions
			mysqli_query($link, "DELETE FROM `subscriptions` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete app subscriptions
			mysqli_query($link, "DELETE FROM `users` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete users
			mysqli_query($link, "DELETE FROM `uservars` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete user variables
			mysqli_query($link, "DELETE FROM `vars` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete app variables
			mysqli_query($link, "DELETE FROM `webhooks` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete webhooks
			mysqli_query($link, "DELETE FROM `accounts` WHERE `app` = '" . $_SESSION['name'] . "' AND `owner` = '" . $_SESSION['username'] . "'") or die(mysqli_error($link)); // delete webhooks

			misc\cache\purge('KeyAuthApp:' . $_SESSION['name'] . ':' . $_SESSION['ownerid']);
            if ($_SESSION['role'] == "seller" || $_SESSION['role'] == "developer") {
				$result = mysqli_query($link, "SELECT `sellerkey`,`customDomainAPI` FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '" . $_SESSION['name'] . "'");
				$row = mysqli_fetch_array($result);
				misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
				misc\cache\purge('KeyAuthApp:' . $row['customDomainAPI']);
			}

			$_SESSION['app'] = NULL;
			dashboard\primary\success("Successfully deleted App!");
			$result = mysqli_query($link, "SELECT `name`, `secret` FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "'"); // select all apps where owner is current user
			if (mysqli_num_rows($result) == 1) // if the user only owns one app, load that app (they can still change app after it's loaded)
			{
				$row = mysqli_fetch_array($result);
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
		($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '$appsecret'")) or die(mysqli_error($link));

		$row = mysqli_fetch_array($result);
		$appname = $row["name"];
		$secret = $row["secret"];
		$version = $row["ver"];
		$ownerid = $row["ownerid"];

		$_SESSION["secret"] = $secret;
	?>
    <div class="card mb-xl-8">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">App credentials code</span>
                <span class="text-muted mt-1 fw-bold fs-7">Simply replace the placeholder code in the example with
                    these</span>
            </h3>
            <div class="card-toolbar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4 me-1 active"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_1">C#</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4 me-1"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_2">C++</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_3">Python</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_4">PHP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_5">JavaScript</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_6">Java</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_7">VB.NET</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_8">Rust</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_9">Go</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_10">Lua</a>
                    </li>
					<li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_11">Ruby</a>
                    </li>
					<li class="nav-item">
                        <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4"
                            data-bs-toggle="tab" href="#kt_table_widget_5_tab_12">Perl</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade active show" id="kt_table_widget_5_tab_1">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">public static api KeyAuthApp = new api(
    name: "<?php echo $appname; ?>",
    ownerid: "<?php echo $ownerid; ?>",
    secret: "<?php echo $secret; ?>",
    version: "<?php echo $version; ?>"
);</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-CSHARP-Example"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-CSHARP-Example</a>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="kt_table_widget_5_tab_2">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">std::string name = "<?php echo $appname; ?>"; // application name. right above the blurred text aka the secret on the licenses tab among other tabs
std::string ownerid = "<?php echo $ownerid; ?>"; // ownerid, found in account settings. click your profile picture on top right of dashboard and then account settings.
std::string secret = "<?php echo $secret; ?>"; // app secret, the blurred text on licenses tab and other tabs
std::string version = "<?php echo $version; ?>"; // leave alone unless you've changed version on website</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-CPP-Example"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-CPP-Example</a>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="kt_table_widget_5_tab_3">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">keyauthapp = api(
    name = "<?php echo $appname; ?>",
    ownerid = "<?php echo $ownerid; ?>",
    secret = "<?php echo $secret; ?>",
    version = "<?php echo $version; ?>",
    hash_to_check = getchecksum()
)</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-Python-Example/"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-Python-Example/</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_4">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">$name = "<?php echo $appname; ?>"; // your application name
$ownerid = "<?php echo $ownerid; ?>"; // your KeyAuth account's ownerid, located in account settings</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-PHP-Example"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-PHP-Example</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_5">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">const KeyAuthApp = new KeyAuth(
    "<?php echo $appname; ?>", // Application Name
    "<?php echo $ownerid; ?>", // OwnerID
    "<?php echo $secret; ?>", // Application Secret
    "<?php echo $version; ?>" // Application Version
);</code>
                    <br>
                    Repository: <a href="https://github.com/mazkdevf/KeyAuth-JS-Example"
                        target="_blank">https://github.com/mazkdevf/KeyAuth-JS-Example</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_6">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">private static String ownerid = "<?php echo $ownerid; ?>"; // You can find out the owner id in the profile settings keyauth.com
private static String appname = "<?php echo $appname; ?>"; // Application name
private static String version = "<?php echo $version; ?>"; // Application version</code>
                    <br>
                    Repository: <a href="https://github.com/SprayDown/KeyAuth-JAVA-api"
                        target="_blank">https://github.com/SprayDown/KeyAuth-JAVA-api</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_7">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">Private Shared name As String = "<?php echo $appname; ?>" ' Application name, found in dashboard
Private Shared ownerid As String = "<?php echo $ownerid; ?>" ' Ownerid, found in account settings of dashboard
Private Shared secret As String = "<?php echo $secret; ?>" ' Application name, found in dashboard. It's the blurred text beneath application name
Private Shared version As String = "<?php echo $version; ?>"</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-VB-Example"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-VB-Example</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_8">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">let mut keyauthapp = keyauth::KeyauthApi::new(
    "<?php echo $appname; ?>", // This should be your application name, you can find this in your dashboard
    "<?php echo $ownerid; ?>", // This is your ownerid, you can find this in your user settings (where you change your password)
    obfstr::obfstr!("<?php echo $secret; ?>"), // This is your app secret
    "<?php echo $version; ?>",
);</code>
                    <br>
                    Repository: <a href="https://github.com/KeyAuth/KeyAuth-Rust-Example"
                        target="_blank">https://github.com/KeyAuth/KeyAuth-Rust-Example</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_9">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">var name = "<?php echo $appname; ?>"
var ownerid = "<?php echo $ownerid; ?>"
var version = "<?php echo $version; ?>"</code>
                    <br>
                    Repository: <a href="https://github.com/mazkdevf/KeyAuth-Go-Example"
                        target="_blank">https://github.com/mazkdevf/KeyAuth-Go-Example</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_10">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">Name = "<?php echo $appname; ?>" --* Application Name
Ownerid = "<?php echo $ownerid; ?>" --* OwnerID
APPVersion = "<?php echo $version; ?>" --* Application Version</code>
                    <br>
                    Repository: <a href="https://github.com/mazkdevf/KeyAuth-Lua-Examples"
                        target="_blank">https://github.com/mazkdevf/KeyAuth-Lua-Examples</a>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="kt_table_widget_5_tab_11">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">KeyAuth.new.Api(
    "<?php echo $appname; ?>", # Application Name
    "<?php echo $ownerid; ?>", # Application OwnerID
    "<?php echo $secret; ?>", # Application Secret
    "<?php echo $version; ?>" # Applicaiton Version
)</code>
                    <br>
                    Repository: <a href="https://github.com/mazkdevf/KeyAuth-Ruby-Example"
                        target="_blank">https://github.com/mazkdevf/KeyAuth-Ruby-Example</a>
                    <!--end::Table-->
                </div>
				<div class="tab-pane fade" id="kt_table_widget_5_tab_12">
                    <!--begin::Table container-->
                    <code style="display: block;white-space:pre-wrap;">KeyAuth::Api(
    "<?php echo $appname; ?>",
    "<?php echo $ownerid; ?>",
    "<?php echo $secret; ?>",
    "<?php echo $version; ?>"
);</code>
                    <br>
                    Repository: <a href="https://github.com/mazkdevf/KeyAuth-Perl-Example"
                        target="_blank">https://github.com/mazkdevf/KeyAuth-Perl-Example</a>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
            </div>
        </div>
        <!--end::Body-->
    </div>
    <?php
	} 
	if($_SESSION['role'] != "Manager") {
	?>
    <a class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#create_app">Create App</a>
	<?php
	}
	if(isset($_SESSION['app']) && $_SESSION['role'] != "Manager") {
	?>
    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#rename_app">Rename App</a>
    <?php
	($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
	$row = mysqli_fetch_array($result);
	if (!$row['paused']) {
	?>
    <a class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#pause_app">Pause App & Users</a>
    <?php
	} else {
	?>
    <a class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#unpause_app">Unpause App & Users</a>
    <?php
	}
	?>
    <a class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#refresh_app">Refresh App Secret</a>
    <a class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete_app">Delete App</a>
	<?php
	}
	if($_SESSION['role'] != "Manager") {
	?>
    <br></br>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="file_export" class="table table-striped table-bordered display">
                    <th>Application</th>
                    <th>Action</th>
                    <?php
					$username = $_SESSION['username'];
					$rows = array();
					($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '$username'"));
					while ($r = mysqli_fetch_assoc($result)) {
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
                    <tr>

                        <td><?php echo $appName ?> <span style="display:<?= $paused == 1 ? 'inline' : 'none'; ?>;"
                                class="badge badge-warning">Paused</span> </td>
                        <form method="POST">
                            <td><button value="<?php echo $appName ?>" name="selectApp"
                                    class="btn btn-<?= $appSelected == 1 ? 'success' : 'secondary'; ?> aria-expanded="
                                    false"> <?= $appSelected == 1 ? 'Selected' : 'Select'; ?> </button>
                                <?php } ?>
                        </form>


                </table>
            </div>
        </div>
    </div>
	<?php
	}
	?>


    <!--begin::Modal - Create App-->
    <div class="modal fade" id="create_app" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Create App</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
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
                                        <span class="required">App Name</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Specify your unique app name"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" placeholder="Application Name"
                                        class="form-control form-control-lg form-control-solid" name="appname" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="create_app" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
                <!--end::Modal header-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Create App-->


    <!--begin::Modal - Rename App-->
    <div class="modal fade" id="rename_app" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Rename App</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
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
                                        <span class="required">App Name</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Specify your unique app name"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" placeholder="New Application Name"
                                        class="form-control form-control-lg form-control-solid" name="appname" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="rename_app" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
                <!--end::Modal header-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Rename App-->


    <!--begin::Modal - Pause App-->
    <div class="modal fade" tabindex="-1" id="pause_app">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Pause Application</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to pause app & all users? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                    <form method="post">
                        <button name="pauseapp" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Pause App-->


    <!--begin::Modal - Unpause App-->
    <div class="modal fade" tabindex="-1" id="unpause_app">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Unpause Application</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to unpause app & all users? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                    <form method="post">
                        <button name="unpauseapp" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Unpause App-->

    <!--begin::Modal - Refresh App-->
    <div class="modal fade" tabindex="-1" id="refresh_app">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Refresh Application</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to reset application secret? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                    <form method="post">
                        <button name="refreshapp" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Refresh App-->

    <!--begin::Modal - Delete App-->
    <div class="modal fade" tabindex="-1" id="delete_app">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="modal-title">Delete Application</h2>

                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <label class="fs-5 fw-bold mb-2">
                        <p> Are you sure you want to delete application? </p>
                    </label>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">No</button>
                    <form method="post">
                        <button name="deleteapp" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Delete App-->
</div>
<!--end::Container-->