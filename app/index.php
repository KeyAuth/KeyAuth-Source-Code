<?php
include '../includes/misc/autoload.phtml';
include '../includes/dashboard/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['oldUrl'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login/");
    exit();
}

set_exception_handler(function ($exception) {
    error_log("\n--------------------------------------------------------------\n");
    error_log($exception);
    error_log("\nRequest data:");
    error_log(print_r($_POST, true));
    error_log("\n--------------------------------------------------------------");
    http_response_code(500);
    $errorMsg = str_replace($databaseUsername, "REDACTED", $exception->getMessage());
    \dashboard\primary\error($errorMsg);
});

$username = $_SESSION['username'];
$query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?",[$username]);
$row = mysqli_fetch_array($query->result);

$banned = $row['banned'];
$lastreset = $row['lastreset'];
if (!is_null($banned) || $_SESSION['logindate'] < $lastreset || $query->num_rows < 1) {
    echo "<meta http-equiv='Refresh' Content='0; url=../login/'>";
    session_destroy();
    exit();
}
$role = $row['role'];
$permissions = $row['permissions'];
$admin = $row['admin'];
$staff = $row['staff'];
$formBanned = $row['formBanned'];
$twofactor = $row['twofactor'];
$_SESSION['role'] = $role;

$expires = $row['expires'];
if (in_array($role, array("developer", "seller"))) {
    $_SESSION['timeleft'] = dashboard\primary\expireCheck($username, $expires);
}


if (!$_SESSION['app']) // no app selected yet
{
    $query = misc\mysql\query("SELECT `secret`, `name`, `banned`, `sellerkey` FROM `apps` WHERE `owner` = ? AND `ownerid` = ?",[$_SESSION['username'], $_SESSION['ownerid']]); // select all apps where owner is current user
    if ($query->num_rows == 1) // if the user only owns one app, load that app (they can still change app after it's loaded)
    {
        $row = mysqli_fetch_array($query->result);
        $_SESSION['name'] = $row["name"];
        $_SESSION["selectedApp"] = $row["name"];
        $_SESSION['app'] = $row["secret"];
        $_SESSION['sellerkey'] = $row["sellerkey"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <title>Keyauth - Open Source Auth</title>
    <link rel="shortcut icon" href="https://cdn.keyauth.cc/v2/assets/media/logos/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--begin::Page Vendor Stylesheets(used by this page)-->
    <link href="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.dark.bundle.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.keyauth.cc/v2/assets/css/style.dark.bundle.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css">
    <style>
    .secret {
        color: transparent;
        text-shadow: 0px 0px 5px #b2b9bf;
        transition: text-shadow 0.1s linear;
    }

    .secret:hover {
        text-shadow: 0px 0px 0px #b2b9bf;
    }

    .secretlink {
        color: transparent;
        text-shadow: 0px 0px 5px #007bff;
        transition: text-shadow 0.1s linear;
    }

    .secretlink:hover {
        text-shadow: 0px 0px 0px #007bff;
        color: transparent;
    }
    </style>

    <!-- Credits to https://stackoverflow.com/a/45656609 -->
    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<!--<body id="kt_body" class="page-loading-enabled header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px" data-kt-aside-minimize="on">'; -->

<body id="kt_body"
    class="page-loading-enabled header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed"
    style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px"';
<?php include 'layout/master.php' ?>

<?php include 'layout/_scrolltop.php' ?>

        <!--end::Modals-->
        <!--begin::Javascript-->
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.bundle.js"></script>
        <script src="https://cdn.keyauth.cc/v2/assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->
        <!--begin::Page Vendors Javascript(used by this page)-->
        <script src="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <script src="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.js"></script>
        <!--end::Page Vendors Javascript-->
        <!--end::Javascript-->
        <script src="https://cdn.keyauth.cc/dashboard/assets/libs/popper-js/dist/umd/popper.min.js"></script>
        <script src="https://cdn.keyauth.cc/dashboard/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        
        <script>
            // @see https://docs.headwayapp.co/widget for more configuration options.
            var HW_config = {
                selector: ".noti", // CSS selector where to inject the badge
                account: "yBgPqx"
            }
        </script>
        <script async src="https://cdn.headwayapp.co/widget.js"></script>
    </body>
    <!--end::Body-->
</html>