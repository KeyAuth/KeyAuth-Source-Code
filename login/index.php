<?php
require '../includes/misc/autoload.phtml';
require '../includes/dashboard/autoload.phtml';
require '../includes/api/shared/autoload.phtml';

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    header("Location: ../app/");
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
?>
<html>
<!--begin::Head-->

<head>
    <base href="">
    <title>Keyauth - Login</title>
    <meta charset="utf-8" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://keyauth.cc" />

    <meta content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors" name="description" />
    <meta content="KeyAuth" name="author" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="KeyAuth, Cloud Authentication, Key Authentication,Authentication, API authentication,Security, Encryption authentication, Authenticated encryption, Cybersecurity, Developer, SaaS, Software Licensing, Licensing" />
    <meta property="og:description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors" />
    <meta property="og:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png" />
    <meta property="og:site_name" content="KeyAuth | Secure your software from piracy." />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="KeyAuth - Open Source Auth">
    <meta itemprop="description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors">

    <meta itemprop="image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@keyauth">
    <meta name="twitter:title" content="Keyauth - Login">

    <meta name="twitter:description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors">
    <meta name="twitter:creator" content="@keyauth">
    <meta name="twitter:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">


    <!-- Open Graph data -->
    <meta property="og:title" content="Keyauth - Login" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="./" />
    <link rel="shortcut icon" href="https://cdn.keyauth.cc/v2/assets/media/logos/favicon.ico" />

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.keyauth.cc/v2/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #2549e8;
            border-radius: 10px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #0a2bbf;
        }
    </style>

    <!-- Credits to https://stackoverflow.com/a/45656609 -->
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->


    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/64b7e24394cf5d49dc649411/1h5n4nmde';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->

<body id="kt_body" class="bg-dark">
    <!--begin::Main-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <!--begin::Logo-->
                <a href="../" class="mb-12">
                    <img alt="Logo" src="https://cdn.keyauth.cc/v2/assets/media/logos/favicon.ico" class="h-80px" />
                </a>
                <!--end::Logo-->
                <div class="alert alert-primary" role="alert">
                    Please join Telegram group <a href="https://t.me/keyauth" target="_blank">https://t.me/keyauth</a> 
                </div>
                <!--begin::Wrapper-->
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <!--begin::Form-->
                    <form class="form w-100" method="post">
                        <!--begin::Heading-->
                        <div class="text-center mb-10">
                            <!--begin::Title-->
                            <h1 class="text-light mb-3">Sign In to Keyauth</h1>
                            <!--end::Title-->
                            <!--begin::Link-->
                            <div class="text-gray-400 fw-bold fs-4">New Here?
                                <a href="../register/" class="link-primary fw-bolder">Create an Account</a>
                            </div>
                            <!--end::Link-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="form-label fs-6 fw-bolder text-light">Username</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control text-light" type="text" required name="username" placeholder="Enter username" autocomplete="on" />
                            <div class="form-group row">
                                </br>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack mb-2">
                                    <!--begin::Label-->
                                    <label class="form-label fw-bolder text-light fs-6 mb-0">Password</label>
                                    <!--end::Label-->
                                    <!--begin::Link-->
                                    <a href="../forgot/" class="link-primary fs-6 fw-bolder">Forgot Password ?</a>
                                    <!--end::Link-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Input-->
                                <input class="form-control text-light" type="password" required name="password" placeholder="Password" autocomplete="on" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-10">
                                <!--begin::Label-->
                                <label class="form-label fs-6 fw-bolder text-light">2FA</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control text-light" maxlength="6" placeholder="Two Factor Code (if applicable)" type="text" name="keyauthtwofactor" autocomplete="off" />
                                <div class="form-group row">
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <br>
                                <!--begin::Actions-->
                                <div class="text-center">
                                    <!--begin::Submit button-->
                                    <button name="login" class="btn btn-lg btn-primary w-100 mb-5">
                                        <span class="indicator-label">Continue</span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                    <!--end::Submit button-->

                                </div>
                                <!--end::Actions-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
            <div class="d-flex flex-center flex-column-auto p-10">
                <!--begin::Links-->
                <div class="d-flex align-items-center fw-bold fs-6">
                    <a href="https://keyauth.cc" class="text-muted text-hover-primary px-2">About</a>
                    <a href="https://keyauth.cc/app/?page=support" class="text-muted text-hover-primary px-2">Contact Us</a>
                </div>
                <!--end::Links-->
            </div>
            <!--end::Footer-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Main-->
    <!--begin::Javascript-->
    <!--begin::Global Javascript Bundle(used by all pages)-->
    <script src="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.bundle.js"></script>
    <script src="https://cdn.keyauth.cc/v2/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Page Custom Javascript(used by this page)-->
    <!--end::Page Custom Javascript-->
    <!--end::Javascript-->
    <?php
    if (isset($_POST['login'])) {
        $username = misc\etc\sanitize($_POST['username']);
        $password = misc\etc\sanitize($_POST['password']);

        $query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$username]);

        if ($query->num_rows < 1) {
            dashboard\primary\error("Account doesn't exist!");
            return;
        }
        while ($row = mysqli_fetch_array($query->result)) {
            $user = $row['username'];
            $pass = $row['password'];
            $id = $row['ownerid'];
            $email = $row['email'];
            $role = $row['role'];
            $app = misc\etc\sanitize($row['app']);
            $banned = $row['banned'];
            $locked = $row['locked'];
            $img = $row['img'];

            $owner = misc\etc\sanitize($row['owner']);
            $twofactor_optional = $row['twofactor'];
            $acclogs = $row['acclogs'];
            $google_Code = $row['googleAuthCode'];

            $regionSaved = $row['region'];
            $asNumSaved = $row['asNum'];
            $emailVerify = $row['emailVerify'];
            $securityKey = $row['securityKey'];
        }

        if (!is_null($banned)) {
            dashboard\primary\error("Banned: Reason: " . misc\etc\sanitize($banned));
            return;
        }

        if (!password_verify($password, $pass)) {
            dashboard\primary\error("Password is invalid!");
            return;
        }
        
        if ($locked) {
            header("location: ./accShare/");
            die();
        }
        
        if (misc\etc\isBreached($password)) {
            dashboard\primary\wh_log($logwebhook, "{$username} attempted to login with leaked password `{$password}`", $webhookun);
            dashboard\primary\error("Password has been leaked in a data breach (not from us)! You must click Forgot Password and change password.");
            return;
        }
        
        $ip = api\shared\primary\getIp();
        
        /*
        * Email verification
        * For paid customers, checks if ISP and region (aka state) match. If not, they must verify it's them via an email.
        * Customers can opt to disable email verification.
        * This code is also used to notify the KeyAuth owner of account sharing, since that's against our ToS.
        */
        if (in_array($role, array("developer", "seller")) && $username != "demoseller" && $username != "demodeveloper" && !empty($awsAccessKey)) {
            $url = "http://ip-api.com/json/{$ip}?fields=16910340"; // returns fields: region,as,proxy,hosting

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $resp = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if($httpcode == 429) {
                dashboard\primary\wh_log($logwebhook, "<@1131334631350878268> IP checking is rate limited", $webhookun);
                dashboard\primary\error("Login location is rate-limited! Please try again in a minute or so.");
                return;
            }
            else {
                $json = json_decode($resp, true);
                $region = $json["region"];
                $asNum = explode(" ", $json["as"])[0];
                if (!is_null($asNumSaved)) {
                    if ($asNum != $asNumSaved || $region != $regionSaved) {
                        // if user not using VPN and IP location changed, notify KeyAuth owner of account sharing
                        if(!$json->proxy && !$json->hosting && $region != $regionSaved) {
                            dashboard\primary\wh_log($logwebhook, "user `{$username}` detected account sharing **IP Address:** `{$ip}` **Old AS:** {$asNumSaved} **New AS:** {$asNum} **Old Region:** {$regionSaved} **New Region:** {$region}", $webhookun);
                            if(!$emailVerify) {
                                misc\mysql\query("UPDATE `accounts` SET `region` = ?,`asNum` = ?,`lastip` = ? WHERE `username` = ?",[$region, $asNum, $ip, $username]);
                            }
                        }
                        
                        if($emailVerify) { // only require email verification if enabled.
                            if ($twofactor_optional) {
                                // 2FA verification on new login location
                                $twofactor = misc\etc\sanitize($_POST['keyauthtwofactor']);
                                if (empty($twofactor)) {
                                    dashboard\primary\error("Please enter 2FA code!");
                                    return;
                                }
        
                                require_once '../auth/GoogleAuthenticator.php';
                                $gauth = new GoogleAuthenticator();
                                $checkResult = $gauth->verifyCode($google_Code, $twofactor, 2);
        
                                if (!$checkResult) {
                                    dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
                                    return;
                                }
                                
                                misc\mysql\query("UPDATE `accounts` SET `region` = ?,`asNum` = ?,`lastip` = ? WHERE `username` = ?",[$region, $asNum, $ip, $username]);
                            } else {
                                // email verification on new login location
                                header("location: ./emailVerify/");
                                die();
                            }
                        }
                    }
                }
                else {
                    misc\mysql\query("UPDATE `accounts` SET `region` = ?,`asNum` = ?,`lastip` = ? WHERE `username` = ?",[$region, $asNum, $ip, $username]);
                }
            }
        }
        
        if((!$emailVerify || $role == "tester") && $twofactor_optional) {
            require_once '../auth/GoogleAuthenticator.php';
            $gauth = new GoogleAuthenticator();
            $twofactor = misc\etc\sanitize($_POST['keyauthtwofactor']);
            $checkResult = $gauth->verifyCode($google_Code, $twofactor, 2);

            if (!$checkResult) {
                dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
                return;
            }
        }

        $_SESSION['username'] = $username;
        $_SESSION['ownerid'] = $id;
        $_SESSION['role'] = $role;
        $_SESSION['logindate'] = time();
        $_SESSION['img'] = $img;
        
        if($securityKey) {
            // set a temporary session variable to be used until the user completes WebAuthn
            unset($_SESSION['username']);
            $_SESSION['pendingUsername'] = $username;
            header("location: ./securityKey.html");
            die();
        }

        if ($role == "Reseller" || $role == "Manager") {
            ($query = misc\mysql\query("SELECT `secret`, `ownerid` FROM `apps` WHERE `name` = ? AND `owner` = ?",[$app, $owner]));
            if ($query->num_rows < 1) {
                dashboard\primary\error("Application you're assigned to no longer exists!");
                return;
            }
            while ($row = mysqli_fetch_array($query->result)) {
                $secret = $row["secret"];
                $ownerid = $row["ownerid"];
            }
            $_SESSION['app'] = $secret;
            $_SESSION['name'] = $app;
            $_SESSION['ownerid'] = $ownerid;
        }
        
        if ($acclogs) // check if account logs enabled
        {
            $ua = misc\etc\sanitize($_SERVER['HTTP_USER_AGENT']);
            misc\mysql\query("INSERT INTO `acclogs`(`username`, `date`, `ip`, `useragent`) VALUES (?, ?, ?, ?);",[$username, time(), $ip, $ua]); // insert ip log
            $ts = time() - 604800;
            misc\mysql\query("DELETE FROM `acclogs` WHERE `username` = ? AND `date` < ?",[$username, $ts]); // delete any account logs more than a week old
        }
        
        if(strtolower($username) != "mak" && strtolower($username) != "itsnetworking") {
            dashboard\primary\wh_log($logwebhook, "{$username} has logged into KeyAuth with IP `{$ip}`", $webhookun);
        }

        if ($role == "Reseller") {
            header("location: ../app/?page=reseller-licenses");
        } else if (!is_null($_SESSION['oldUrl'])) {
            header("location: " . $_SESSION['oldUrl']);
        } else {
            header("location: ../app/");
        }
    }
    ?>
</body>
<!--end::Body-->
</html>