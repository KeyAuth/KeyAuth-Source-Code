<?php
include '../includes/connection.php';
require '../includes/misc/autoload.phtml';
require '../includes/dashboard/autoload.phtml';
require '../includes/api/shared/autoload.phtml';

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['username'])) {
	header("Location: ../app/");
	exit();
}
?>

<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <title>Keyauth - Register</title>
    <meta charset="utf-8" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://keyauth.cc" />

    <meta
        content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors"
        name="description" />
    <meta content="KeyAuth" name="author" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="KeyAuth, Cloud Authentication, Key Authentication,Authentication, API authentication,Security, Encryption authentication, Authenticated encryption, Cybersecurity, Developer, SaaS, Software Licensing, Licensing" />
    <meta property=”og:description”
        content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors" />
    <meta property="og:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png" />
    <meta property=”og:site_name” content="KeyAuth | Secure your software from piracy." />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="KeyAuth - Open Source Auth">
    <meta itemprop="description"
        content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors">

    <meta itemprop="image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@keyauth">
    <meta name="twitter:title" content="KeyAuth - Open Source Auth">

    <meta name="twitter:description"
        content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors">
    <meta name="twitter:creator" content="@keyauth">
    <meta name="twitter:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">


    <!-- Open Graph data -->
    <meta property="og:title" content="KeyAuth - Open Source Auth" />
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

<body id="kt_body" class="bg-dark">
    <!--begin::Main-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-up -->
        <div
            class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
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
                            <div class="text-gray-400 fw-bold fs-4">Already have an account?
                                <a href="../login/" class="link-primary fw-bolder">Sign in here</a>
                            </div>
                            <!--end::Link-->
                        </div>
                        <!--end::Heading-->


                        <!--begin::Input group-->
                        <div class="fv-row">
                            <!--begin::Col-->
                            <label class="form-label fw-bolder text-light fs-6">Username</label>
                            <input class="form-control text-light" type="text" required placeholder="Enter username"
                                name="username" autocomplete="on" />
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row">
                            <label class="form-label fw-bolder text-light fs-6">Email</label>
                            <input class="form-control text-light" type="email" required placeholder="Enter email"
                                name="email" autocomplete="on" />
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
                                    <input class="form-control text-light" type="password" required
                                        placeholder="Enter password" name="password" autocomplete="on" />
                                    <span
                                        class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                        data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                                <!--end::Input wrapper-->
                                <!--begin::Meter-->
                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                </div>
                                <!--end::Meter-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Hint-->
                            <div class="text-muted">Use 12 or more characters with a mix of letters, numbers &amp;
                                symbols.</div>
                            <!--end::Hint-->
                            <br>
                            <!--begin::Hint-->
                            <div class="text-muted">Don't share your account with anyone.<br>This is against ToS.
                            <br>    
                            With developer plan or higher, you can create accounts for other people to use.</div>
                            <!--end::Hint-->
                            <br>
                            <!--begin::Hint-->
                            <div class="text-muted">We recommend that you use <a href="https://bitwarden.com" target="_blank">https://bitwarden.com</a>
                            <br>    
                            It's a free password manager which is secure<br> and will make it easier for you to use different passwords.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Input group-->
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <label class="form-check form-check-custom form-check-solid form-check-inline">
                                <span class="form-check-label fw-bold text-gray-700 fs-6">Users bound by
                                    <a data-bs-toggle="modal" data-bs-target="#terms" class="ms-1 link-primary">Terms of
                                        Service</a> and
                                    <a data-bs-toggle="modal" data-bs-target="#privacy"
                                        class="ms-1 link-primary">Privacy Policy</a>.</span>
                            </label>
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
                    <a href="mailto:contact@keyauth.com" class="text-muted text-hover-primary px-2">Contact</a>
                    <a href="https://discord.gg/keyauth" class="text-muted text-hover-primary px-2">Contact Us</a>
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


    <div class="modal fade" tabindex="-1" id="terms">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-light">Terms of Service</h1>

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

                <div class="modal-body text-light">
                    <p>This may be modified at any time without further consent of the user.
                        Property of Nelson Cybersecurity LLC. Headquartered in Florida, hosted in New York.</p>

                    <h1 class="text-light">Copyright</h1>
                    <p>We don't host any of our user's files, downloading via our API acts as a proxy and the files are
                        never stored on the server's disk. Given this, you must contact the file host and notify them of
                        the alleged infringement.</p>

                    <h1 class="text-light">Account</h1>
                    <p>Account owners have the sole responsibility for their credentials, we are not responsible for the
                        loss, leaking, and use of these credentials unless through a security breach on our platform. We
                        make available numerous options to protect or recover your account, including 2FA and Password
                        Resets. Accounts are for individual use only, any multiple-party use is prohibited and may
                        result in the termination of your account.</p>

                    <h1 class="text-light">Applications</h1>
                    <p>You are responsible for the content uploaded or that communicates with KeyAuth. While we will
                        remove illegal content if we're made aware of it, "KeyAuth" is provided immunity from any legal
                        action held against anything uploaded by users on our service (KeyAuth 230 of the Communications
                        Decency Act). Emails from law enforcement or legal counsel regarding illegal content using our
                        service should be directed to EMAIL.</p>

                    <h1 class="text-light">Acceptable Use</h1>
                    <p>You agree to comply with all applicable legislation and regulations in connection with your use
                        of KeyAuth, this is not limited to your local laws. The use of our service to host, transmit, or
                        share any illegal data will result in an immediate termination of your account and a possible
                        law enforcement notification. We also forbid any attempt to abuse, spam, hack, or crack our
                        service without the written permission of Nelson Cybersecurity LLC. The following actions will
                        result in account termination:</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Attacks against our webserver, including DDoS attacks and exploitative attempts.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Creating a dispute after the refund period, seven days.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Attempting to libel KeyAuth to hurt its reputation.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Utilizing an unreasonable amount of server resources, i.e. creating hundreds of thousands of
                        users.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Violating KeyAuth's open-source license, i.e monetarily benefitting from KeyAuth source by
                        selling it as if you own it</p>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" tabindex="-1" id="privacy">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-light">Privacy Policy</h1>

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

                <div class="modal-body text-light">

                    <h1 class="text-light">Privacy</h1>
                    <p>It is pretty much necessary to store these details to fight fraudulent disputes. Otherwise, we'll
                        have insufficient evidence to win the dispute. Also I highly recommend you use the password
                        manager <a href="https://bitwarden.com" target="_blank">https://bitwarden.com</a>. You can use Bitwarden for free on multiple devices, and you can
                        also purchase their premium to unlock the ability to store 2FA codes in their browser extension
                        or mobile app.

                    <p>We collect the below-listed details. We'll try to keep this updated, you can also view
                        <a href="https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/db_structure.sql" target="_blank">https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/db_structure.sql</a></p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> IP address used to register account, last IP address to login to account, and only if account
                        logs are enabled on your account (they are by default), every IP address that has logged into
                        your account in the past week is saved in database. Also, regardless of whether account logs are
                        enabled, every IP address used to login to an account is sent to a private Discord webhook.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Passwords are hashed with BCrypt prior to being stored in the database. We do not log plain-text
                        passwords. With today's technology, BCrypt passwords are considered unable to decrypt to their
                        plain-text form.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Email (hashed with SHA1, not plain-text) used to register is stored in database, 2FA secret is stored if enabled.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> Your customer's Windows SID (hwid) is stored in database if sent to our API, their IP address is
                        stored, and their password is stored after being hashed with BCrypt. You're unable to get your
                        customer's plain-text password from our server.</p>

                    <span class="bullet bullet-dot me-5"></span>
                    <p> We use E-commerce platforms to handle our payments. From the orders on those platforms, we can
                        identify which person made the order. Though, we do not store any of your payment information in
                        our database.</p>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php
	if (isset($_POST['register'])) {
		$username = misc\etc\sanitize($_POST['username']);
		$password = misc\etc\sanitize($_POST['password']);
		$email = misc\etc\sanitize($_POST['email']);

		if (empty($username) || empty($password) || empty($email)) {
			dashboard\primary\error("You must specify username, password, and email.");
			return;
		}

		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number = preg_match('@[0-9]@', $password);
		if (!$uppercase || !$lowercase || !$number || strlen($password) < 12) {
			dashboard\primary\error("Password must have at least one capital letter, one lowercase letter, one number, and be at least 12 characters long.");
			return;
		}

        if(misc\etc\isPhonyEmail($email)) {
            dashboard\primary\error("Please use a real email. You will need email access if you want to change password, username or email ever.");
			return;
        }

		if (misc\etc\isBreached($_POST['password'])) {
			dashboard\primary\error("Password has been leaked in a data breach (not from us)! Please use different password.");
			return;
		}

		$result = mysqli_query($link, "SELECT 1 FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));

		if (mysqli_num_rows($result) == 1) {
			dashboard\primary\error("Username already taken!");
			return;
		}

		$email_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `email` = SHA1('$email')") or die(mysqli_error($link));
		$do_email_check = mysqli_num_rows($email_check);
		if ($do_email_check > 0) {
			dashboard\primary\error('Email already used by username: ' . mysqli_fetch_array($email_check)['username'] . '');
			return;
		}

		$pass_encrypted = password_hash($password, PASSWORD_BCRYPT);

		$ownerid = misc\etc\generateRandomString();
		$ip = api\shared\primary\getIp();



		mysqli_query($link, "INSERT INTO `accounts` (`username`, `email`, `password`, `ownerid`, `role`, `registrationip`) VALUES ('$username', SHA1('$email'), '$pass_encrypted', '$ownerid','tester', '$ip')") or die(mysqli_error($link));


		$_SESSION['logindate'] = time();
		$_SESSION['username'] = $username;
		$_SESSION['email'] = $email;
		$_SESSION['ownerid'] = $ownerid;
		$_SESSION['role'] = 'tester';
		$_SESSION['img'] = 'https://cdn.keyauth.cc/front/assets/img/favicon.png';
		mysqli_close($link);
		header("location: ../app/");
	}

	?>
</body>
<!--end::Body-->

</html>