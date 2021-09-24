<?php

ob_start();

include '../../../includes/connection.php';
include '../../../includes/functions.php';
session_start();

if (!isset($_SESSION['username'])) {
         header("Location: ../../../login/");
        exit();
}


	        $username = $_SESSION['username'];
            ($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
            $row = mysqli_fetch_array($result);
            
            $banned = $row['banned'];
			if (!is_null($banned))
			{
				echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>";
				session_destroy();
				exit();
			}
        
            $role = $row['role'];
            $_SESSION['role'] = $role;
			
			$darkmode = $row['darkmode'];
			
			    if($role == "Reseller" || $role == "Manager")
{
    die('Resellers or Managers Not Allowed Here');
}

			
                            
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 4 admin, bootstrap 4, css3 dashboard, bootstrap 4 dashboard, xtreme admin bootstrap 4 dashboard, frontend, responsive bootstrap 4 admin template, material design, material dashboard bootstrap 4 dashboard template">
    <meta name="description" content="Xtreme is powerful and clean admin dashboard template, inpired from Google's Material Design">
    <meta name="robots" content="noindex,nofollow">
    <title>KeyAuth - Upgrade</title>
	<script src="https://shoppy.gg/api/embed.js"></script>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../../static/images/favicon.png">
	<script src="../../files/assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Custom CSS -->
	<link href="../../files/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../files/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../../files/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../files/dist/css/style.min.css" rel="stylesheet">
	<script src="https://cdn.sellix.io/static/js/embed.js" ></script>
	<link href="https://cdn.sellix.io/static/css/embed.css" rel="stylesheet" />

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">



	                    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body data-theme="<?php if($darkmode == 0){echo "dark";}else{echo"light";}?>">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin1" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin1">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="../../files/assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="../../files/assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                             <!-- dark Logo text -->
                             <img src="../../files/assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                             <!-- Light Logo text -->    
                             <img src="../../files/assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin1">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav">
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://keyauth.com/discord/" target="discord"> <i class="mdi mdi-discord font-24"></i>
						</a>
						</li>
						<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://t.me/KeyAuth" target="telegram"> <i class="mdi mdi-telegram font-24"></i>
						</a>
						</li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $_SESSION['img']; ?>" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white mb-2">
                                    <div class=""><img src="<?php echo $_SESSION['img']; ?>" alt="user" class="img-circle" width="60"></div>
                                    <div class="ml-2">
                                        <h4 class="mb-0"><?php echo $_SESSION['username']; ?></h4>
                                        <p class=" mb-0"><?php echo $_SESSION['email']; ?></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="../../account/logs/"><i class="mdi mdi-folder-account font-18"></i> Account Logs</a>
                                <a class="dropdown-item" href="../../account/settings/"><i class="ti-settings mr-1 ml-1"></i> Account Settings</a>
                                <a class="dropdown-item" href="../../account/logout/"><i class="fa fa-power-off mr-1 ml-1"></i> Logout</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <?php
						sidebar($role);
						?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Upgrade</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
			
		
			
			
   
            <!-- ============================================================== -->
            <div class="container-fluid" id="content">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- File export -->
                <div class="row">
				<div class="col-md-4 col-sm-12">
				<div class="card" style="zoom: 1;">
				<div class="card-body">
				<div class="form-group">
				<h4 class="card-title">Tester</h4>
				<p>Limited Keys</p>
				<p>No File Uploads</p>
				<p>Limited Logs</p>
				<p>No Reseller System</p>
				<p>Limited Support</p>
				<p>No Discord Role</p>
				<button class="btn btn-lg btn-block font-medium btn-outline-success block-card">Already Have</button>
				</div>
				</div>
				</div>
				</div>
				<div class="col-md-4 col-sm-12">
				<div class="card">
				<div class="card-body">
				<div class="form-group">
				<h4 class="card-title">Developer</h4>
				<p>Unlimited Keys</p>
				<p>Unlimited Files (25mb each)</p>
				<p>Unlimited Logs</p>
				<p>Reseller System</p>
				<p>24.7.365 Support</p>
				<p>Discord Role</p>
				<?php
                              $role = $_SESSION['role'];
                              if($role == "developer" || $role == "seller")
                              {
                              echo'<button class="btn btn-lg btn-block font-medium btn-outline-warning block-sidenav">Already Have</button>';   
                              }
                              else
                              {
                              echo'<a data-shoppy-product="qgw9zmQ" data-shoppy-username="'.$_SESSION['username'].'" class="btn btn-lg btn-block font-medium btn-outline-warning block-sidenav">Purchase</a>';
                              }
                              
                              ?>
				</div>
				</div>
				</div>
				</div>
				<div class="col-md-4 col-sm-12">
				<div class="card">
				<div class="card-body">
				<div class="form-group">
				<h4 class="card-title">Seller</h4>
				<p>All developer features</p>
				<p>Discord Bot</p>
				<p>SellerAPI (auto send sellix)</p>
				<p>Customer App Panel</p>
				<p>24.7.365 prioritzed support</p>
				<p>Supreme Discord Role</p>
				<?php
                              $role = $_SESSION['role'];
							  if($role == "developer")
							  {
							  echo'<i>Use Coupon Code "alreadydev" for 50% off</i>'; 	  
							  }
                              if($role == "seller")
                              {
                              echo'<button class="btn btn-lg btn-block font-medium btn-outline-danger block-sidenav">Already Have</button>';   
                              }
                              else
                              {
                              echo'<a data-shoppy-product="lEbrHGK" data-shoppy-username="'.$_SESSION['username'].'" class="btn btn-lg btn-block font-medium btn-outline-danger block-sidenav">Purchase</a>';
                              }
                              
                              ?>
				</div>
				</div>
				</div>
				</div>
				<a data-sellix-product="60c4f32c22e03" data-sellix-custom-Username="<?php echo $_SESSION['username']; ?>" class="dt-button buttons-print btn btn-primary mr-1">Purchase Developer With PayPal</a>  <a data-sellix-product="60c4f57583012" data-sellix-custom-Username="<?php echo $_SESSION['username']; ?>" class="dt-button buttons-print btn btn-primary mr-1">Purchase Seller With PayPal</a>
				<br>
				<i>We recommend you purchase via Stripe due to fees with Sellix and PayPal, though if you must use PayPal here you go.</i>
				</div>
				</div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
       Copyright &copy; <script>document.write(new Date().getFullYear())</script> KeyAuth
</footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    
   
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../../files/assets/libs/popper-js/dist/umd/popper.min.js"></script>
    <script src="../../files/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="../../files/dist/js/app.min.js"></script>
    <script src="../../files/dist/js/app.init.dark.js"></script>
    <script src="../../files/dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../../files/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../../files/assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="../../files/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="../../files/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
   <script src="../../files/dist/js/feather.min.js"></script>
    <script src="../../files/dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="../../files/assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../../files/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <!--c3 charts -->
    <script src="../../files/assets/extra-libs/c3/d3.min.js"></script>
    <script src="../../files/assets/extra-libs/c3/c3.min.js"></script>
    <!--chartjs -->
    <script src="../../files/assets/libs/chart-js/dist/chart.min.js"></script>
    <script src="../../files/dist/js/pages/dashboards/dashboard1.js"></script>
		<script src="../../files/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
	    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
  
					

<script src="../../files/dist/js/pages/datatable/datatable-advanced.init.js"></script>
</body>
</html>