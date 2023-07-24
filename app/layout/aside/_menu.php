                                                <?php

                                                $set_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                                                $lang_code = substr($set_lang, 0, 2);

                                                $page = isset($_GET['page']) ? $_GET['page'] : "manage-apps";
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
                                                                <a class="menu-link <?php if ($page == 'manage-apps') {
                                                                                                                                echo 'active';
                                                                                                                        } ?>" href="?page=manage-apps">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="grid"></i>
                                                                    </span>
                                                                    <span class="menu-title">Manage Applications</span>
                                                                </a>
                                                            </div>

                                                            <?php
                                                                        if (isset($_SESSION["app"])) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <div class="menu-content pb-2">
                                                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Dashboard</span>
                                                                </div>
                                                            </div>
                                                                        <?php
                                                                        if(!($role == "Manager" && !($permissions & 1))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'licenses') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=licenses">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="key"></i>
                                                                    </span>
                                                                    <span class="menu-title">Licenses</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 2))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'users') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=users">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="users"></i>
                                                                    </span>
                                                                    <span class="menu-title">Users</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 4))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'subscriptions') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=subscriptions">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="bar-chart"></i>
                                                                    </span>
                                                                    <span class="menu-title">Subscriptions</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 8))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'chats') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=chats">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="message-square"></i>
                                                                    </span>
                                                                    <span class="menu-title">Chats</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 16))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'sessions') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=sessions">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="clock"></i>
                                                                    </span>
                                                                    <span class="menu-title">Sessions</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 32))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'webhooks') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=webhooks">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="server"></i>
                                                                    </span>
                                                                    <span class="menu-title">Webhooks</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 64))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'files') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=files">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="paperclip"></i>
                                                                    </span>
                                                                    <span class="menu-title">Files</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 128))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'vars') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=vars">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="file-text"></i>
                                                                    </span>
                                                                    <span class="menu-title">Variables</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 256))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'logs') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=logs">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="database"></i>
                                                                    </span>
                                                                    <span class="menu-title">Logs</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 512))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'blacklists') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=blacklists">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="user-x"></i>
                                                                    </span>
                                                                    <span class="menu-title">Blacklists</span>
                                                                </a>
                                                            </div>
                                                                        <?php
                                                                        }
                                                                        if(!($role == "Manager" && !($permissions & 1024))) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'app-settings') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=app-settings">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="settings"></i>
                                                                    </span>
                                                                    <span class="menu-title">Settings</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        }
                                                                        if($role != "Manager") {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'audit-log') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=audit-log">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="book-open"></i>
                                                                    </span>
                                                                    <span class="menu-title">Audit Logs</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        }

                                                                        }
                                                                        ?>

                                                            <div class="menu-item">
                                                                <div class="menu-content pb-2">
                                                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Account</span>
                                                                </div>
                                                            </div>

                                                                        <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'forms') {
                                                                                                                                echo 'active';
                                                                                                                        } ?>" href="?page=forms">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="folder-plus"></i>
                                                                    </span>
                                                                    <span class="menu-title">Forms</span>
                                                                </a>
                                                            </div>

                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'support') {
                                                                                                                                echo 'active';
                                                                                                                        } ?>" href="?page=support">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="life-buoy"></i>
                                                                    </span>
                                                                    <span class="menu-title">Support Chat</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        if ($role == "developer" || $role == "seller") {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == "manage-accs") {
                                                                                                                                        echo "active";
                                                                                                                                } ?> " href="?page=manage-accs">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="sliders"></i>
                                                                    </span>
                                                                    <span class="menu-title">Manage Accounts</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        }

                                                                        ?>

                                                            <?php
                                                                        if ($role != "Manager") {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'upgrade') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=upgrade">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="activity"></i>
                                                                    </span>
                                                                    <span class="menu-title">Upgrade</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        }

                                                                        ?>

                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'account-settings') {
                                                                                                                                echo 'active';
                                                                                                                        } ?>" href="?page=account-settings">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="settings"></i>
                                                                    </span>
                                                                    <span class="menu-title">Settings</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        if ($role == "seller" && isset($_SESSION["app"])) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <div class="menu-content pb-2">
                                                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Seller</span>
                                                                </div>
                                                            </div>


                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'seller-settings') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=seller-settings">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="settings"></i>
                                                                    </span>
                                                                    <span class="menu-title">Settings</span>
                                                                </a>
                                                            </div>

                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'webloader') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=webloader">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="download-cloud"></i>
                                                                    </span>
                                                                    <span class="menu-title">Web Loader</span>
                                                                </a>
                                                            </div>
                                                                        <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'seller-logs') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=seller-logs">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="database"></i>
                                                                    </span>
                                                                    <span class="menu-title">Logs</span>
                                                                </a>
                                                            </div>
                                                            <?php
                                                                        }
                                                                        ?>

                                                                        <?php
                                                                        if ($staff) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <div class="menu-content pb-2">
                                                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Staff</span>
                                                                </div>
                                                            </div>


                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'staff-panel') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=staff-panel">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="lock"></i>
                                                                    </span>
                                                                    <span class="menu-title">Panel</span>
                                                                </a>
                                                            </div>

                                                            <?php
                                                                        }
                                                                        ?>


                                                            <?php
                                                                        if ($admin) {
                                                                        ?>
                                                            <div class="menu-item">
                                                                <div class="menu-content pb-2">
                                                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Admin</span>
                                                                </div>
                                                            </div>


                                                            <div class="menu-item">
                                                                <a class="menu-link <?php if ($page == 'admin-panel') {
                                                                                                                                        echo 'active';
                                                                                                                                } ?>" href="?page=admin-panel">
                                                                    <span class="menu-icon">
                                                                        <i data-feather="lock"></i>
                                                                    </span>
                                                                    <span class="menu-title">Panel</span>
                                                                </a>
                                                            </div>

                                                            <?php
                                                                        }
                                                                        ?>


                                                        </div>
                                                        <!--end::Menu-->
                                                        <script>
                                                        feather.replace()
                                                        </script>
                                                    </div>
                                                    <!--end::Aside Menu-->
                                                </body>

                                                </html>