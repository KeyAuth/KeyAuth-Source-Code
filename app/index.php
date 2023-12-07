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

<!DOCTYPE html>
<html lang="en" class="bg-[#09090d] text-white overflow-x-hidden dark">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdn.keyauth.cc/v3/scripts/highlight.css">
    
    <script src="https://cdn.keyauth.cc/v3/scripts/highlight.min.js"></script>
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="shortcut icon" type="image/jpg" href="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <link href="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />

    <title>KeyAuth - Open Source Auth</title>

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

    <link rel="stylesheet" href="https://cdn.keyauth.cc/v3/dist/dashboard.css">
    
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="https://cdn.keyauth.cc/v3/dist/output.css">
    <script src="https://cdn.keyauth.cc/v3/dist/flowbite.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.keyauth.cc/v3/scripts/jquery.min.js"></script>
</head>
<!--end::Head-->
<!--begin::Body-->
<body>
    <div data-loader id="loader" class="bg-[#0f0f17]">
        <div class="text-center grid h-screen place-items-center">
            <div role="status" class="mt-96">
                <svg aria-hidden="true" class="inline w-8 h-8 mr-2 text-[#09090d] animate-spin fill-blue-600"
                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill" />
                </svg>
            </div>
            <div class="block mt-32">
                <span class="text-xs font-semibold text-gray-500">
                    Loading taking a while? <a href="?page=manage-apps" class="text-blue-500 hover:underline">Please
                        feel free to return.</a>
                </span>
            </div>
            <?php
            if (!isset($_SESSION['app'])) { ?>
            <a href="?page=manage-apps">
                <?php } ?>
        </div>
    </div>

    <?php include 'layout/master.php' ?>

    <script src="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.bundle.js"></script>
    <script src="https://cdn.keyauth.cc/v2/assets/js/scripts.bundle.js"></script>

    <script src="https://cdn.keyauth.cc/dashboard/webauthn.js"></script>
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <script src="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="https://cdn.keyauth.cc/v2/assets/plugins/custom/datatables/datatables.js"></script>

    <script src="https://cdn.keyauth.cc/v3/scripts/sidebar.js"></script>
    <script src="https://cdn.keyauth.cc/v3/dist/dashboard.min.js"></script>
</body>

</html>
