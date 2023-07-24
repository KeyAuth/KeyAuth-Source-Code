                                                                                        <!--begin::Menu-->
                                                                                        <div
                                                                                            class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px">
                                                                                            <!--begin::Menu item-->
                                                                                            <div class="menu-item px-3">
                                                                                                <div class="menu-content d-flex align-items-center px-3">
                                                                                                    <!--begin::Avatar-->
                                                                                                    <div class="symbol symbol-50px me-5">
                                                                                                        <img alt="Logo" src=" <?php echo $_SESSION['img']; ?> " />
                                                                                                    </div>
                                                                                                    <!--end::Avatar-->
                                                                                                    <!--begin::Username-->
                                                                                                    <div class="d-flex flex-column">
                                                                                                        <div class="fw-bolder d-flex align-items-center fs-5"> <?php echo $_SESSION["username"]; ?>
                                                                                                            <span
                                                                                                                class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2"><?php echo $_SESSION['role']; ?></span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <!--end::Username-->
                                                                                                </div>
                                                                                            </div>
                                                                                            <!--end::Menu item-->
                                                                                            <!--begin::Menu separator-->
                                                                                            <div class="separator my-2"></div>
                                                                                            <!--end::Menu separator-->
                                                                                            <!--begin::Menu item-->
                                                                                            <div class="menu-item px-5">
                                                                                                <a href="?page=account-logs" class="menu-link px-5"> Account Logs</a>
                                                                                            </div>
                                                                                            <!--end::Menu item-->
                                                                                            <!--begin::Menu item-->
                                                                                            <div class="menu-item px-5 my-1">
                                                                                                <a href="?page=account-settings" class="menu-link px-5"> Account Settings</a>
                                                                                            </div>
                                                                                            <!--end::Menu item-->
                                                                                            <!--begin::Menu item-->
                                                                                            <div class="menu-item px-5">
                                                                                                <a href="?page=logout" class="menu-link px-5"> Sign Out</a>
                                                                                            </div>
                                                                                            <!--end::Menu item-->

                                                                                        </div>
                                                                                        <!--end::Menu-->