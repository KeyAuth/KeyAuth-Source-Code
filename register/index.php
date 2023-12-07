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
    \dashboard\primary\error($exception->getMessage());
});
?>

<!DOCTYPE html>
<html lang="en" class="bg-[#09090d] text-white overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="title" content="KeyAuth - Open Source Auth">

    <meta content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors" name="description" />
    <meta content="KeyAuth" name="author" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="KeyAuth, Cloud Authentication, Key Authentication,Authentication, API authentication,Security, Encryption authentication, Authenticated encryption, Cybersecurity, Developer, SaaS, Software Licensing, Licensing" />
    <meta property="og:description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors" />
    <meta property="og:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png" />
    <meta property="og:site_name" content="KeyAuth | Secure your software from piracy." />
    <link rel="shortcut icon" type="image/jpg" href="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="KeyAuth - Open Source Auth">
    <meta itemprop="description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors">
    <meta itemprop="image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@keyauth">
    <meta name="twitter:title" content="KeyAuth - Open Source Auth">

    <meta name="twitter:description" content="Secure your software against piracy, an issue causing $422 million in losses annually - Fair pricing & Features not seen in competitors">
    <meta name="twitter:creator" content="@keyauth">
    <meta name="twitter:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">

    <!-- Open Graph data -->
    <meta property="og:title" content="KeyAuth - Open Source Auth" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="./" />

    <title>KeyAuth - Register</title>

    <!-- Canonical SEO -->
    <link rel="canonical" href="https://keyauth.cc" />

    <link rel="stylesheet" href="https://cdn.keyauth.cc/v3/scripts/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.keyauth.cc/v3/dist/output.css">

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</head>

<body>
    <header class="">
        <nav class="border-gray-200 px-4 lg:px-6 py-2.5 mb-14">
            <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                <a href="../" class="flex items-center">
                    <img src="https://cdn.keyauth.cc/v2/assets/media/logos/logo-1-dark.png" class="mr-3 h-12 mt-2" alt="KeyAuth Logo" />
                </a>
                <div class="flex items-center lg:order-2">
                    <a href="../login" class="text-white focus:ring-0 font-medium rounded-lg text-sm px-4 py-2 lg:px-5 lg:py-2.5 mr-2 hover:opacity-60 transition duration-200 focus:outline-none focus:ring-gray-800">
                        Client Area
                    </a>
                    <a href="../register" class="text-white focus:ring-0 font-medium rounded-lg text-sm px-4 py-2 lg:px-5 lg:py-2.5 mr-2 bg-blue-600 hover:opacity-80 focus:outline-none focus:ring-primary-800 transition duration-200">
                        Onboard Now
                    </a>
                    <button data-collapse-toggle="mmenu" type="button" class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200   " aria-controls="mmenu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mmenu">
                    <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                        <li>
                            <a href="../" class="block py-2 pr-4 pl-3 border-b lg:hover:bg-transparent lg:border-0 lg:p-0 text-gray-400 hover:bg-gray-700 hover:text-white lg:hover:bg-transparent border-gray-700 transition duration-200" aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="../#features" class="block py-2 pr-4 pl-3 border-b lg:hover:bg-transparent lg:border-0 lg:p-0 text-gray-400 hover:bg-gray-700 hover:text-white lg:hover:bg-transparent border-gray-700 transition duration-200">Features</a>
                        </li>
                        <li>
                            <a href="../#plans" class="block py-2 pr-4 pl-3 border-b lg:hover:bg-transparent lg:border-0 lg:p-0 text-gray-400 hover:bg-gray-700 hover:text-white lg:hover:bg-transparent border-gray-700 transition duration-200">
                                Plans
                            </a>
                        </li>
                        <li>
                            <a href="../#team" class="block py-2 pr-4 pl-3 border-b lg:hover:bg-transparent lg:border-0 lg:p-0 text-gray-400 hover:bg-gray-700 hover:text-white lg:hover:bg-transparent border-gray-700 transition duration-200">
                                Our Team
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section>
        <div class="relative flex flex-wrap md:-m-8 ml-8 md:ml-24">
            <div class="w-full md:w-1/2 md:p-8">
                <div class="md:max-w-lg md:mx-auto md:pt-36">
                <h2
                    class="mb-7 md:mb-12 text-3xl md:text-6xl font-bold font-heading tracking-px-n leading-tight text-center">
                        Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r to-blue-600 from-sky-400">KeyAuth</span>!
                    </h2>
                    <h3 class="mb-9 text-sm md:text-xl font-bold font-heading leading-normal">
                        The best authentication platform for your software.
                    </h3>
                </div>
            </div>
            <div class="w-full md:w-1/2 md:p-8 -ml-4 md:-ml-0">
                <div class="p-4 py-16 flex flex-col justify-center h-full md:-ml-32">
                    <form class="md:max-w-lg md:ml-48 space-y-4 md:space-y-6" method="post">
                        <div class="relative mb-4">
                            <input type="text" id="username" name="username" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-border-gray-300 appearance-none focus:ring-0 peer" placeholder=" " autocomplete="on" required>
                            <label for="username" class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                        </div>
                        <div class="relative mb-4">
                            <input type="email" id="email" name="email" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-border-gray-300 appearance-none focus:ring-0 peer" placeholder=" " autocomplete="on" required>
                            <label for="email" class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Email</label>
                        </div>

                        <div class="relative mb-4">
                            <input type="password" id="password" name="password" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-border-gray-300 appearance-none focus:ring-0 peer" placeholder=" " data-popover-target="popover-password" data-popover-placement="bottom" autocomplete="on" required>
                            <label for="password" class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password</label>
                        </div>

                        <div class="relative mb-4">
                            <input type="password" id="confirmpassword" name="confirmpassword" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-border-gray-300 appearance-none focus:ring-0 peer" placeholder=" " data-popover-target="popover-password" data-popover-placement="bottom" autocomplete="on" required>
                            <label for="confirmpassword" class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Confirm Password</label>
                        </div>

                        <div data-popover id="popover-password" role="tooltip" class="absolute invisible inline-block text-sm text-white transition-opacity duration-300 bg-[#09090d] border border-[#0f0f17] rounded-lg shadow-sm opacity-0 w-72">
                            <div class="p-3 space-y-2">
                                <h3 class="font-semibold text-white">Must have at least 12 characters</h3>
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="h-1 bg-gray-200" id="pass_strength_one"></div>
                                    <div class="h-1 bg-gray-200" id="pass_strength_two"></div>
                                    <div class="h-1 bg-gray-200" id="pass_strength_three"></div>
                                    <div class="h-1 bg-gray-200" id="pass_strength_four"></div>
                                </div>
                                <p>It’s better to have:</p>
                                <ul>
                                    <li class="flex items-center mb-1">
                                        <svg class="w-3.5 h-3.5 mr-2 text-green-400  " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                                        </svg>
                                        Upper & lower case letters
                                    </li>
                                    <li class="flex items-center mb-1">
                                        <svg class="w-3.5 h-3.5 mr-2 text-green-400  " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                                        </svg>
                                        A symbol(s) (#$&)
                                    </li>
                                </ul>
                            </div>
                            <div data-popper-arrow></div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-white">I Agree To:</span>
                            </div>
                            <ul class="items-center w-full text-sm font-medium border-1 rounded-lg sm:flex bg-[#0f0f17] text-white">
                                <li class="w-full border-b border-gray-700 sm:border-b-0 sm:border-r">
                                    <div class="flex items-center pl-3">
                                        <input name="wontshareCB" id="wontshareCB" type="checkbox" value="" class="w-4 h-4 rounded focus:ring-blue-600 ring-offset-gray-700 focus:ring-offset-gray-700 focus:ring-0 bg-[#09090d] border-[#09090d]">
                                        <label for="wontshareCB" class="w-full py-3 ml-2 text-sm font-medium text-gray-300">Not
                                            share my account</label>
                                    </div>
                                </li>
                                <li class="w-full border-b border-gray-700 sm:border-b-0 sm:border-r ">
                                    <div class="flex items-center pl-3">
                                        <input name="wontdisputeCB" id="wontdisputeCB" type="checkbox" value="" class="w-4 h-4 rounded focus:ring-blue-600 ring-offset-gray-700 focus:ring-offset-gray-700 focus:ring-0 bg-[#09090d] border-[#09090d]">
                                        <label for="wontdisputeCB" class="w-full py-3 ml-2 text-sm font-medium text-gray-300">Not make
                                            any disputes (contact support)</label>
                                    </div>
                                </li>
                                <li class="w-full border-b border-gray-700 sm:border-b-0">
                                    <div class="flex items-center pl-3">
                                        <input name="tosCB" id="tosCB" type="checkbox" value="" class="w-4 h-4 rounded focus:ring-blue-600 ring-offset-gray-700 focus:ring-offset-gray-700 focus:ring-0 bg-[#09090d] border-[#09090d]">
                                        <label for="tosCB" class="w-full py-3 ml-2 text-sm font-medium text-gray-300">Follow
                                            <a href="/terms" target="_blank" class="text-blue-600  hover:underline">Terms and
                                                Conditions</a></label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <button name="register" class="text-white border-2 hover:bg-white hover:text-black focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full mt-10">
                        <span class="inline-flex">
                                    Register Now
                                    <svg class="w-3.5 h-3.5 ml-2 mt-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"></path>
                                    </svg></span>
                        </button>

                        <div class="text-sm font-medium text-white mb-4">
                            Need a good password manager? Use <a href="https://bitwarden.com/" target="_blank" class="hover:underline text-blue-500">Bitwarden</a> it's free!
                        </div>

                        <div class="text-sm font-medium text-white">
                            Have an Account? <a href="../login" class="hover:underline text-blue-500">Login</a>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="mt-32">
        <div class="p-4 py-6 mx-auto max-w-screen-xl md:p-8 lg:-10 pt-32 md:pt-0">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-6">
                <div class="col-span-2">
                    <a href="../" class="flex items-center mb-2 text-2xl font-semibold text-white lg:mb-0">
                        KeyAuth LLC
                    </a>
                    <p class="my-4 font-light text-gray-400">
                        KeyAuth is a game-changing, affordable and easy to use licensing solution for your software.
                    </p>
                    <ul class="flex mt-5 space-x-6">
                        <li>
                            <a target="_blank" href="https://youtube.com/keyauth" class="hover:text-white text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21.593 7.203a2.506 2.506 0 0 0-1.762-1.766c-1.566-.43-7.83-.437-7.83-.437s-6.265-.007-7.832.404a2.56 2.56 0 0 0-1.766 1.778c-.413 1.566-.417 4.814-.417 4.814s-.004 3.264.406 4.814c.23.857.905 1.534 1.763 1.765 1.582.43 7.83.437 7.83.437s6.265.007 7.831-.403a2.515 2.515 0 0 0 1.767-1.763c.414-1.565.417-4.812.417-4.812s.02-3.265-.407-4.831ZM9.996 15.005l.005-6 5.207 3.005-5.212 2.995Z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="https://github.com/KeyAuth/" class="hover:text-white text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M12.026 2a9.973 9.973 0 0 0-9.974 9.974c0 4.406 2.857 8.145 6.82 9.465.5.09.68-.217.68-.481 0-.237-.008-.865-.011-1.696-2.775.602-3.361-1.338-3.361-1.338-.452-1.152-1.107-1.459-1.107-1.459-.905-.619.069-.605.069-.605 1.002.07 1.527 1.028 1.527 1.028.89 1.524 2.336 1.084 2.902.829.09-.645.35-1.085.635-1.334-2.214-.251-4.542-1.107-4.542-4.93 0-1.087.389-1.979 1.024-2.675-.101-.253-.446-1.268.099-2.64 0 0 .837-.269 2.742 1.021a9.582 9.582 0 0 1 2.496-.336 9.555 9.555 0 0 1 2.496.336c1.906-1.291 2.742-1.021 2.742-1.021.545 1.372.203 2.387.099 2.64.64.696 1.024 1.587 1.024 2.675 0 3.833-2.33 4.675-4.552 4.922.355.308.675.916.675 1.846 0 1.334-.012 2.41-.012 2.737 0 .267.178.577.687.479C19.146 20.115 22 16.379 22 11.974 22 6.465 17.535 2 12.026 2Z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="https://twitter.com/KeyAuth" class="hover:text-white text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.633 7.994c.013.175.013.349.013.523 0 5.325-4.053 11.46-11.46 11.46A11.38 11.38 0 0 1 2 18.169c.324.037.636.05.973.05a8.07 8.07 0 0 0 5.001-1.721 4.036 4.036 0 0 1-3.767-2.793c.249.037.499.062.761.062.361 0 .724-.05 1.061-.137a4.027 4.027 0 0 1-3.23-3.953v-.05a4.05 4.05 0 0 0 1.82.51 4.022 4.022 0 0 1-1.796-3.353c0-.748.199-1.434.548-2.032a11.457 11.457 0 0 0 8.306 4.215c-.062-.3-.1-.611-.1-.923a4.024 4.024 0 0 1 4.028-4.028c1.16 0 2.207.486 2.943 1.272a7.957 7.957 0 0 0 2.556-.973c-.3.93-.93 1.72-1.771 2.22a8.074 8.074 0 0 0 2.319-.624 8.646 8.646 0 0 1-2.019 2.083Z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="#" class="hover:text-white text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.999 7.375a4.624 4.624 0 1 0 0 9.248 4.624 4.624 0 0 0 0-9.248Zm0 7.627a3.004 3.004 0 1 1 0-6.008 3.004 3.004 0 0 1 0 6.008Z">
                                    </path>
                                    <path d="M16.805 8.289a1.078 1.078 0 1 0 0-2.156 1.078 1.078 0 0 0 0 2.156Z"></path>
                                    <path d="M20.533 6.114A4.605 4.605 0 0 0 17.9 3.482a6.607 6.607 0 0 0-2.186-.42c-.963-.042-1.268-.054-3.71-.054s-2.755 0-3.71.054a6.554 6.554 0 0 0-2.184.42 4.6 4.6 0 0 0-2.633 2.632A6.585 6.585 0 0 0 3.058 8.3c-.043.962-.056 1.267-.056 3.71 0 2.442 0 2.753.056 3.71.015.748.156 1.486.419 2.187a4.61 4.61 0 0 0 2.634 2.632 6.583 6.583 0 0 0 2.185.45c.963.042 1.268.055 3.71.055s2.755 0 3.71-.055a6.616 6.616 0 0 0 2.186-.42 4.613 4.613 0 0 0 2.633-2.632c.263-.7.404-1.438.419-2.186.043-.962.056-1.267.056-3.71s0-2.753-.056-3.71a6.583 6.583 0 0 0-.421-2.217Zm-1.218 9.532a5.046 5.046 0 0 1-.311 1.688 2.987 2.987 0 0 1-1.712 1.71c-.535.2-1.1.305-1.67.312-.95.044-1.218.055-3.654.055-2.438 0-2.687 0-3.655-.055a4.961 4.961 0 0 1-1.67-.311 2.985 2.985 0 0 1-1.718-1.711 5.08 5.08 0 0 1-.311-1.67c-.043-.95-.053-1.217-.053-3.653 0-2.437 0-2.686.053-3.655a5.038 5.038 0 0 1 .311-1.687c.305-.79.93-1.41 1.719-1.712a5.01 5.01 0 0 1 1.669-.311c.95-.043 1.218-.055 3.655-.055s2.687 0 3.654.055a4.96 4.96 0 0 1 1.67.31 2.99 2.99 0 0 1 1.712 1.713 5.06 5.06 0 0 1 .311 1.669c.043.95.054 1.218.054 3.655 0 2.436 0 2.698-.043 3.654h-.011v-.001Z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="https://www.tiktok.com/@keyauth" class="hover:text-white text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1h-.04Z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="lg:mx-auto">
                    <h3 class="mb-6 text-sm font-semibold uppercase text-white">Links</h3>
                    <ul class="text-gray-500 ">
                        <li class="mb-4">
                            <a href="https://www.youtube.com/keyauth" target="_blank" class="hover:underline">Youtube</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://linkedin.com" target="_blank" class="hover:underline">Linkedin</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://github.com/keyauth" target="_blank" class="hover:underline">GitHub</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://keyauth.readme.io" target="_blank" class="hover:underline">Documentation</a>
                        </li>
                    </ul>
                </div>
                <div class="lg:mx-auto">
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">
                        Most Used Examples
                    </h2>
                    <ul class="text-gray-500 ">
                        <li class="mb-4">
                            <a target="_blank" href="https://github.com/KeyAuth/KeyAuth-CPP-Example" class="hover:underline">
                                C++ <span class="text-xs">(CPP)</span>
                            </a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="https://github.com/KeyAuth/KeyAuth-CSHARP-Example" class="hover:underline">C# <span class="text-xs">(CSharp)</span></a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="https://github.com/mazkdevf/KeyAuth-JS-Example" class="hover:underline">JavaScript <span class="text-xs">(JS)</span></a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="https://github.com/KeyAuth/KeyAuth-Python-Example" class="hover:underline">Python <span class="text-xs">(PY)</span></a>
                        </li>
                    </ul>
                </div>
                <div class="lg:mx-auto">
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Other & Support</h2>
                    <ul class="text-gray-500 ">
                        <li class="mb-4">
                            <a target="_blank" href="https://keyauth.tawk.help" class="hover:underline">
                                Support Center
                            </a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="../free-trial/" class="hover:underline">
                                Demo Accounts
                            </a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="https://t.me/keyauth" class="hover:underline">
                                Telegram
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="lg:mx-auto">
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Legal</h2>
                    <ul class="text-gray-500 ">
                        <li class="mb-4">
                            <a target="_blank" href="../terms" class="hover:underline">Terms of Service</a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="../terms#privacy" class="hover:underline">Privacy Policy</a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/LICENSE" class="hover:underline">Licensing</a>
                        </li>
                        <li class="mb-4">
                            <a target="_blank" href="../gdpr" class="hover:underline">GDPR</a>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-6 border-[#0f0f17] sm:mx-auto lg:my-8">

            <span class="block mb-6 text-sm text-gray-400 lg:mb-0 text-center">© 2020 - 2023 <a href="../" class="hover:underline">KeyAuth LLC</a>. All Rights Reserved.
            </span>
        </div>
    </footer>

    <!-- jqeury -->
    <script src="https://cdn.keyauth.cc/v3/scripts/jquery.min.js"></script>

    <!--Flowbite JS-->
    <script src="https://cdn.keyauth.cc/v3/dist/flowbite.js"></script>
    <script>
        $("#password").on("input", function() {
            var value = $(this).val();
            var strength = 0;
            if (value.length > 12) {
                strength += 15;
            }
            if (value.match(/[a-z]+/)) {
                strength += 25;
            }
            if (value.match(/[A-Z]+/)) {
                strength += 25;
            }
            if (value.match(/[0-9]+/)) {
                strength += 25;
            }

            if (value.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/)) {
                strength += 25;
            }

            if (value.length === 0) {
                $("#pass_strength_one, #pass_strength_two, #pass_strength_three, #pass_strength_four").css("background-color", "#ffffff");
            } else if (strength >= 1 && strength <= 25) {
                $("#pass_strength_one").css("background-color", "#ff0000");
                $("#pass_strength_two, #pass_strength_three, #pass_strength_four").css("background-color", "#ffffff");
            } else if (strength >= 26 && strength <= 50) {
                $("#pass_strength_one, #pass_strength_two").css("background-color", "#ff5a00");
                $("#pass_strength_three, #pass_strength_four").css("background-color", "#ffffff");
            } else if (strength >= 51 && strength <= 75) {
                $("#pass_strength_one, #pass_strength_two, #pass_strength_three").css("background-color", "#6acc1a");
                $("#pass_strength_four").css("background-color", "#ffffff");
            } else if (strength >= 76 && strength <= 100) {
                $("#pass_strength_one, #pass_strength_two, #pass_strength_three, #pass_strength_four").css("background-color", "#0c9b18");
            }
        });
    </script>

    <?php
    if (isset($_POST['register'])) {
        $username = misc\etc\sanitize($_POST['username']);
        $password = misc\etc\sanitize($_POST['password']);
        $confirmPass = misc\etc\sanitize($_POST['confirmpassword']);
        $email = misc\etc\sanitize($_POST['email']);
        $pattern = '/\b(http|https)\b|\.(com|win|rar|zip|gov|uk|gg|business|org|cc)\b/i';
        if (empty($username) || empty($password) || empty($email)) {
            dashboard\primary\error("You must specify username, password, and email.");
            return;
        }
        if(preg_match($pattern, $username)){
            dashboard\primary\error("Username can not contain link features.");
            return;
        }
        if (!isset($_POST['wontshareCB'])) {
            dashboard\primary\error("You must agree to not share your account with anyone");
            return;
        }
        if (!isset($_POST['wontdisputeCB'])) {
            dashboard\primary\error("You must agree you won't dispute any charges. (contact support first)");
            return;
        }
        if (!isset($_POST['tosCB'])) {
            dashboard\primary\error("You must agree to the Terms of Service and Privacy Policy");
            return;
        }
        if($password !== $confirmPass){
            dashboard\primary\error("Passwords do not match!");
            return;
        }
        if (strlen($password) >= 33){
            dashboard\primary\error("Password must be less than 33 characters!");
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
        $htmlContent = "<html>
                                        <body>
                                                <h1>Welcome!</h1>
                                                <p>Please join our Telegram group for updates and chat <a href=\"https://t.me/keyauth\">https://t.me/keyauth</a></p>
                                                <p>KeyAuth code can be seen here <a href=\"https://github.com/KeyAuth/\">https://github.com/KeyAuth/</a></p>
                                                <p>KeyAuth API documentation can be seen here <a href=\"https://keyauth.readme.io/\">https://keyauth.readme.io/</a></p>
                                                <p>Please leave a review on TrustPilot if you enjoy KeyAuth <a href=\"https://trustpilot.com/review/keyauth.com\">https://trustpilot.com/review/keyauth.com</a></p>
                                                <p style=\"margin-top: 20px;\">Thanks,<br><b>KeyAuth.</b></p>
                                        </body>
                                        </html>";
        misc\email\send($username, $email, $htmlContent, "Welcome to KeyAuth");
        $_SESSION['logindate'] = time();
        $_SESSION['username'] = $username;
        $_SESSION['ownerid'] = $ownerid;
        $_SESSION['role'] = 'tester';
        $_SESSION['img'] = 'https://cdn.keyauth.cc/front/assets/img/favicon.png';
        header("location: ../app/");
    }
    ?>
</body>

</html>
