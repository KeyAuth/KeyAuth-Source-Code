<?php
$page = isset($_GET['page']) ? $_GET['page'] : "index";
?>




<script src="https://unpkg.com/feather-icons"></script>

<html>

<head></head>

<body>
    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
        data-kt-scroll-offset="0">
        <!--begin::Menu-->
        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
            id="#kt_aside_menu" data-kt-menu="true">


            <div class="menu-item">
                <div class="menu-content pb-2">
                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Reseller</span>
                </div>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php if ($page == 'reseller-licenses') {
                                        echo 'active';
                                    } ?>" href="?page=reseller-licenses">
                    <span class="menu-icon">
                        <i data-feather="key"></i>
                    </span>
                    <span class="menu-title">Licenses</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php if ($page == 'reseller-users') {
                                        echo 'active';
                                    } ?>" href="?page=reseller-users">
                    <span class="menu-icon">
                        <i data-feather="users"></i>
                    </span>
                    <span class="menu-title">Users</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php if ($page == 'reseller-balance') {
                                        echo 'active';
                                    } ?>" href="?page=reseller-balance">
                    <span class="menu-icon">
                        <i data-feather="credit-card"></i>
                    </span>
                    <span class="menu-title">Balance</span>
                </a>
            </div>




        </div>
        <!--end::Menu-->
        <script>
        feather.replace()
        </script>
    </div>
    <!--end::Aside Menu-->
</body>

</html>