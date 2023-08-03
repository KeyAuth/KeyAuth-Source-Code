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

<html lang="en">
<!--begin::Head-->

<head>
        <base href="">
        <title>Keyauth - Register</title>
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
        <meta name="twitter:title" content="Keyauth - Register">

        <meta name="twitter:description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors">
        <meta name="twitter:creator" content="@keyauth">
        <meta name="twitter:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">


        <!-- Open Graph data -->
        <meta property="og:title" content="Keyauth - Register" />
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
                <!--begin::Authentication - Sign-up -->
                <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
                        <!--begin::Content-->
                        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                                <!--begin::Logo-->
                                <a href="../" class="mb-12">
                                        <img alt="Logo" src="https://cdn.keyauth.cc/v2/assets/media/logos/favicon.ico" class="h-80px" />
                                </a>
                                <!--end::Logo-->
                                <!--begin::Wrapper-->
                                <form method="post">
                                        <div class="bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                                                <!--begin::Form-->
                                                <!--begin::Heading-->
                                                <div class="mb-10 text-center">
                                                        <!--begin::Title-->
                                                        <h1 class="text-light mb-3">Create an Account</h1>
                                                        <!--end::Title-->
                                                        <!--begin::Link-->
                                                        <div class="text-gray-400 fw-bold fs-4">Already have an
                                                                account?
                                                                <a href="../login/" class="link-primary fw-bolder">Sign
                                                                        in here</a>
                                                        </div>
                                                        <!--end::Link-->
                                                </div>
                                                <!--end::Heading-->


                                                <!--begin::Input group-->
                                                <div class="fv-row">
                                                        <!--begin::Col-->
                                                        <label class="form-label fw-bolder text-light fs-6">Username</label>
                                                        <input class="form-control text-light" type="text" required placeholder="Enter username" name="username" autocomplete="on" />
                                                        <!--end::Col-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row">
                                                        <label class="form-label fw-bolder text-light fs-6">Email</label>
                                                        <input class="form-control text-light" type="email" required placeholder="Enter email" name="email" autocomplete="on" />
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="mb-10 fv-row" data-kt-password-meter="true">
                                                        <!--begin::Wrapper-->
                                                        <div class="mb-1">
                                                                <!--begin::Label-->
                                                                <label class="form-label fw-bolder text-light fs-6">Password</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input wrapper-->
                                                                <div class="position-relative mb-3">
                                                                        <input class="form-control text-light" type="password" required placeholder="Enter password" name="password" autocomplete="on" />
                                                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                                                                <i class="bi bi-eye-slash fs-2"></i>
                                                                                <i class="bi bi-eye fs-2 d-none"></i>
                                                                        </span>
                                                                </div>
                                                                <!--end::Input wrapper-->
                                                                <!--begin::Meter-->
                                                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                                        </div>
                                                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                                        </div>
                                                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                                        </div>
                                                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px">
                                                                        </div>
                                                                </div>
                                                                <!--end::Meter-->
                                                        </div>
                                                        <!--end::Wrapper-->
                                                        <!--begin::Hint-->
                                                        <div class="text-muted">Use 12 or more characters with a mix of
                                                                letters, numbers &amp;
                                                                symbols.</div>
                                                        <!--end::Hint-->
                                                        <br>
                                                        <!--begin::Hint-->
                                                        <div class="text-muted">Do <u style="color:red;">NOT share your account</u> with
                                                                anyone.<br>This is against ToS.
                                                                <br>
                                                                With developer plan or higher, you can create accounts
                                                                for other people to use.
                                                        </div>
                                                        <!--end::Hint-->
                                                        <br>
                                                        <!--begin::Hint-->
                                                        <div class="text-muted">Do <u style="color:red;">NOT use a fake email</u>. You need a real email for account recovery/password reset.
                                                        </div>
                                                        <!--end::Hint-->
                                                        <br>
                                                        <!--begin::Hint-->
                                                        <div class="text-muted">We recommend that you use a password manager such as <a href="https://bitwarden.com" target="_blank">https://bitwarden.com</a>
                                                        </div>
                                                        <!--end::Hint-->
                                                </div>

                                                <!--end::Input group=-->
                                                <!--begin::Input group-->
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <!--<div class="fv-row mb-10">
                                                                <label class="form-check form-check-custom form-check-solid form-check-inline">
                                                                        <span class="form-check-label fw-bold text-gray-700 fs-6">Users bound by
                                                                                <a href="https://keyauth.cc/terms/" target="_blank" class="ms-1 link-primary">Terms of
                                                                                        Service</a> and
                                                                                <a href="https://keyauth.cc/terms/#privacy" target="_blank" class="ms-1 link-primary">Privacy Policy</a>.</span>
                                                                </label>
                                                        </div>-->

                                                <div class="text-center" style="color:white;">
                                                        <form action="" method="post">
                                                                <input type="checkbox" name="cb">
                                                                I agree to the
                                                                <a href="https://keyauth.cc/terms" target="_blank" class="ms-1 link-primary">Terms of Service</a>
                                                                and
                                                                <a href="https://keyauth.cc/terms/#privacy" target="_blank" class="ms-1 link-primary">
                                                                        Privacy Policy</a>
                                                        </form>
                                                        <br>
                                                        <br>
                                                </div>

                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="text-center">
                                                        <button name="register" class="btn btn-lg btn-primary">
                                                                <span class="indicator-label">Submit</span>
                                                                <span class="indicator-progress">Please wait...
                                                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                </div>

                                                <!--end::Actions-->
                                                <!--end::Form-->
                                        </div>
                                        <!--end::Wrapper-->
                        </div>
                        </form>
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
                <!--end::Authentication - Sign-up-->
        </div>
        <!--end::Main-->
        <!--begin::Javascript-->
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="https://cdn.keyauth.cc/v2/assets/plugins/global/plugins.bundle.js"></script>
        <script src="https://cdn.keyauth.cc/v2/assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->
        <!--end::Javascript-->
        <?php
        if (isset($_POST['register'])) {
                $username = misc\etc\sanitize($_POST['username']);
                $password = misc\etc\sanitize($_POST['password']);
                $email = misc\etc\sanitize($_POST['email']);
                if (empty($username) || empty($password) || empty($email)) {
                        dashboard\primary\error("You must specify username, password, and email.");
                        return;
                }
                if (!isset($_POST['cb'])) {
                        dashboard\primary\error("You must agree to the Terms of Service and Privacy Policy");
                        return;
                }
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number = preg_match('@[0-9]@', $password);
                if (!$uppercase || !$lowercase || !$number || strlen($password) < 12) {
                        dashboard\primary\error("Password must have at least one capital letter, one lowercase letter, one number, and be at least 12 characters long.");
                        return;
                }
                if (misc\etc\isPhonyEmail($email)) {
                        dashboard\primary\error("Please use a real email. You will need email access to reset password, new login location if you have enabled, etc.");
                        dashboard\primary\wh_log($logwebhook, "{$username} has failed email validation with `{$email}`", $webhookun);
                        return;
                }
                if (misc\etc\isBreached($password)) {
                        dashboard\primary\wh_log($logwebhook, "{$username} attempted to register with leaked password `{$password}`", $webhookun);
                        dashboard\primary\error("Password has been leaked in a data breach (not from us)! Please use different password.");
                        return;
                }
                $query = misc\mysql\query("SELECT 1 FROM `accounts` WHERE `username` = ?", [$username]);
                if ($query->num_rows == 1) {
                        dashboard\primary\error("Username already taken!");
                        return;
                }
                $query = misc\mysql\query("SELECT `username` FROM `accounts` WHERE `email` = SHA1(?)", [$email]);
                if ($query->num_rows > 0) {
                        dashboard\primary\error('Email already used by username: ' . mysqli_fetch_array($query->result)['username'] . '');
                        return;
                }
                $pass_encrypted = password_hash($password, PASSWORD_BCRYPT);
                $ownerid = misc\etc\generateRandomString();
                $ip = api\shared\primary\getIp();
                misc\mysql\query("INSERT INTO `accounts` (`username`, `email`, `password`, `ownerid`, `role`, `registrationip`) VALUES (?, SHA1(LOWER(?)), ?, ?, 'tester', ?)", [$username, $email, $pass_encrypted, $ownerid, $ip]);
                dashboard\primary\wh_log($logwebhook, "{$username} has registered successfully", $webhookun);

                $body = '<div class="f-fallback">
                    <h1>Hello <i>'.$username.'</i>,</h1>
                    <p>Please join our Telegram group for updates and chat <a href="https://t.me/keyauth">https://t.me/keyauth</a></p>
                    <p>KeyAuth code can be seen here <a href="https://github.com/KeyAuth/">https://github.com/KeyAuth/</a></p>
                    <p>KeyAuth API documentation can be seen here <a href="https://keyauth.readme.io/">https://keyauth.readme.io/</a></p>
                    <p>Please review us on TrustPilot, we greatly appreciate it <a href="https://trustpilot.com/review/keyauth.com">https://trustpilot.com/review/keyauth.com</a></p>
                    <p>Thanks,
                      <br>The KeyAuth team</p>
                </div>';

                misc\email\send($username, $email, $body, "Welcome to KeyAuth");
                $_SESSION['logindate'] = time();
                $_SESSION['username'] = $username;
                $_SESSION['ownerid'] = $ownerid;
                $_SESSION['role'] = 'tester';
                $_SESSION['img'] = 'https://cdn.keyauth.cc/front/assets/img/favicon.png';
                header("location: ../app/");
        }
        ?>
</body>
<!--end::Body-->

</html>