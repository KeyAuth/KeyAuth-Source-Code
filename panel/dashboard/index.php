<?php

include '../../includes/connection.php';
require '../../includes/misc/autoload.phtml';
require '../../includes/dashboard/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['un'])) {
    die("not logged in");
}

$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '".$_SESSION['panelapp']."'");
            $row = mysqli_fetch_array($result);
			
			$name = $row["name"];
			$download = $row["download"];
			$webdownload = $row["webdownload"];
			$appcooldown = $row["cooldown"];

    

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
    <title><?php echo $name; ?> Panel</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdn.keyauth.uk/static/images/favicon.png">
	<script src="https://cdn.keyauth.uk/dashboard/assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Custom CSS -->
	<link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="https://cdn.keyauth.uk/dashboard/dist/css/style.min.css" rel="stylesheet">
	

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
	<script src="https://cdn.keyauth.uk/dashboard/unixtolocal.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">



	                    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body data-theme="dark">
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
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                             <!-- dark Logo text -->
                             <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                             <!-- Light Logo text -->    
                             <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
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
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://i.imgur.com/TrwYFBa.png" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white mb-2">
                                    <div class=""><img src="https://i.imgur.com/TrwYFBa.png" alt="user" class="img-circle" width="60"></div>
                                    <div class="ml-2">
                                        <h4 class="mb-0"><?php echo $_SESSION['un']; ?></h4>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="../../dashboard/account/logout/"><i class="fa fa-power-off mr-1 ml-1"></i> Logout</a>
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
                        <h4 class="page-title"><?php echo $name; ?> Panel</h4>
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
                    <div class="col-12">
					

                        <div class="card">
                            <div class="card-body">	
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Application Download</label>
                                        <div class="col-10">
                                            <a href="<?php echo $download; ?>" style="color:#00FFFF;" target="appdownload"><?php echo $download; ?></a>
                                        </div>
                                    </div>
                                <?php

                            $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '".$_SESSION['panelapp']."' AND `username` = '".$_SESSION['un']."'");

                            

                            $row = mysqli_fetch_array($result);

                            $today = time();

                            $cooldown = $row["cooldown"];

                            if(is_null($cooldown))

                            {

                            echo'<form method="post">

<button name="resethwid" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-redo-alt fa-sm text-white-50"></i> Reset HWID</button></form>';   

                            }

                            else

                            {

                            if ($today > $cooldown)

                            {

                            echo'<form method="post">

<button name="resethwid" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-redo-alt fa-sm text-white-50"></i> Reset HWID</button></form>';

                            }

                            else

                            {

                                echo '<div style="color:red;">You can\'t reset HWID again until <script>document.write(convertTimestamp('.$cooldown.'));</script></div>';

                            }

                            }

                        

                        ?>
                            </div>
                        </div>
						<?php if (!is_null($webdownload)) { ?>
						<div class="card">
                            <div class="card-body">	
                                <label for="example-tel-input" class="col-2 col-form-label">Web Loader</label>
                                <br>
								<br>
								<div class="col-10" style="display:none;" id="buttons">
                                            <?php
											($result = mysqli_query($link, "SELECT * FROM `buttons` WHERE `app` = '".$_SESSION['panelapp']."'")) or die(mysqli_error($link));
											$rows = array();
											while ($r = mysqli_fetch_assoc($result))
											{
												$rows[] = $r;
											}
									
											foreach ($rows as $row)
											{
											?>
											<button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="doButton(this.value)"value="<?php echo $row['value']; ?>"><?php echo $row['text']; ?></button>
											<?php
											}
											?>
								</div>
								<div class="col-10" id="handshake">
								<a onclick="handshake()" href="<?php echo $webdownload; ?>" style="color:white;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Download</a>
								</div>
							</div>
                        </div>
						<?php } ?>
                    </div>
                </div>
                <!-- Show / hide columns dynamically -->
                
                <!-- Column rendering -->
                
                <!-- Row grouping -->
                
                <!-- Multiple table control element -->
                
                <!-- DOM / jQuery events -->
                
                <!-- Complex headers with column visibility -->
                
                <!-- language file -->
                
                <!-- Setting defaults -->
                
                <!-- Footer callback -->
                
                <?php

        if (isset($_POST['resethwid']))

        {

        $today = time();

        $cooldown = $today + $appcooldown;

        mysqli_query($link, "UPDATE `users` SET `hwid` = '', `cooldown` = '$cooldown' WHERE `app` = '".$_SESSION['panelapp']."' AND `username` = '".$_SESSION['un']."'");

        dashboard\primary\success("Reset HWID!");
        echo "<meta http-equiv='Refresh' Content='2;'>";   

        }

        ?>
        
		
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
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
    <script>
	<?php
			$result = mysqli_query($link, "SELECT `password` FROM `users` WHERE `username` = '".$_SESSION['un']."' AND `app` = '".$_SESSION['panelapp']."'");
            $row = mysqli_fetch_array($result);
			
			$token = md5(substr($row["password"], -5));
			// $token = "aa";
	?>
	var going = 1;
	function handshake()
	{
		setTimeout(function() { 
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open( "GET", "http://localhost:1337/handshake?user=<?php echo $_SESSION['un']; ?>&token=<?php echo $token; ?>");
		xmlHttp.onload = function () {
			going = 0;
			switch(xmlHttp.status) {
				case 420:
					console.log("returned SHEESH :)");
					$("#handshake").fadeOut(100);
					$("#buttons").fadeIn(1900);
					break;
				default:
					alert(xmlHttp.statusText);
					break;
			}
		};
		xmlHttp.send();
			if (going == 1) {
			handshake();
			}
		}, 3000)
	}
	function doButton(value)
	{
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open( "GET", "http://localhost:1337/" + value);
		xmlHttp.send();
	}
	</script>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/popper-js/dist/umd/popper.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.init.dark.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
   <script src="https://cdn.keyauth.uk/dashboard/dist/js/feather.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <!--c3 charts -->
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/d3.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.js"></script>
    <!--chartjs -->
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chart-js/dist/chart.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/dashboards/dashboard1.js"></script>
		<script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
	    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
  
					

<script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/datatable/datatable-advanced.init.js"></script>
</body>
</html>