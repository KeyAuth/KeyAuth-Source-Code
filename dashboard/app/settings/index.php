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
            
            $isbanned = $row['isbanned'];
            if($isbanned == "1")
            {
				echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>"; 
				session_destroy();
				exit();
            }
        
            $role = $row['role'];
            $_SESSION['role'] = $role;
			
			    if($role == "Reseller")
{
    die('Resellers Not Allowed Here');
}
			
			$darkmode = $row['darkmode'];

			
                            
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
    <title>KeyAuth - App Settings</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../../static/images/favicon.png">
	<script src="../../files/assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Custom CSS -->
	<link href="../../files/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../files/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../../files/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../files/dist/css/style.min.css" rel="stylesheet">
	

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">



	                    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<?php


if (!$_SESSION['app']) // no app selected yet
{
    

    $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "'"); // select all apps where owner is current user
    if (mysqli_num_rows($result) > 0) // if the user already owns an app, proceed to change app or load only app
    {

        if (mysqli_num_rows($result) == 1) // if the user only owns one app, load that app (they can still change app after it's loaded)
        {
            $row = mysqli_fetch_array($result);
            $_SESSION['name'] = $row["name"];
            $_SESSION['app'] = $row["secret"];
            $_SESSION['secret'] = $row["secret"];
            echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                ';
        }
        else // otherwise if the user has more than one app, choose which app to load
        {
            echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#changeapp").fadeIn(1900);
        });             
                
                </script>
                ';
        }
    }
    else // if user doesnt have any apps created, take them to the screen to create an app
    
    {
        echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#createapp").fadeIn(1900);
        });             
                
                </script>
                ';
    }

}
else // app already selected, load page like normal

{
    echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                ';
}

?>
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
                        <h4 class="page-title">App Settings</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
			
			<div class="main-panel" id="createapp" style="padding-left:30px;display:none;">
             <!-- Page Heading -->
             <br>
                    <h1 class="h3 mb-2 text-gray-800">Create an App</h1>
                    <br>
                    <br>
                    <form method="POST" action="">
   <input type="text" id="appname" name="appname" class="form-control" placeholder="Application Name..."></input>
  <br>
  <br>
   <button type="submit" name"ccreateapp" class="btn btn-primary" style="color:white;">Submit</button>
   </form>
        </div>
        
			
			<div class="main-panel" id="changeapp" style="padding-left:30px;display:none;">
             <!-- Page Heading -->
             <br>
                    <h1 class="h3 mb-2 text-gray-800">Choose an App</h1>
                    <br>
                    <br>
                    <form class="text-left" method="POST" action="">
<select class="form-control" name="taskOption">
        <?php
        $username = $_SESSION['username'];
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '$username'")) or die(mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    echo "  <option>". $row["name"]. "</option>";
                }
            }
        
        ?>
</select>    
    <!-- Do SQL query and print them out -->

  <br>
  <br>
   <button type="submit" name="change" class="btn btn-primary" style="color:white;">Submit</button><a style="padding-left:5px;color:#4e73df;" id="createe">Create Application</a>
   </form>
   <script type="text/javascript">

var myLink = document.getElementById('createe');

myLink.onclick = function(){


$(document).ready(function(){
        $("#changeapp").fadeOut(100);
        $("#createapp").fadeIn(1900);
        }); 

}


</script>
   <?php
           if (isset($_POST['change']))
        {
            $selectOption = sanitize($_POST['taskOption']);
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$selectOption' AND `owner` = '".$_SESSION['username']."'")) or die(mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $secret = $row["secret"];
                    $sellerkey = $row["sellerkey"];
                }
            }
            else
            {
							mysqli_close($link);
							error("You dont own application!");
							echo "<meta http-equiv='Refresh' Content='2'>";
                            return;
            }
            $_SESSION['secret'] = $secret;
            $_SESSION['app'] = $secret;
            $_SESSION['name'] = $selectOption;
            $_SESSION['sellerkey'] = $sellerkey;
			
            success("You have changed Applications!");
			echo "<meta http-equiv='Refresh' Content='2;'>";
        }
   ?>
   </div>
   
            <!-- ============================================================== -->
            <div class="container-fluid" id="content" style="display:none;">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- File export -->
                <div class="row">
                    <div class="col-12">
					<?php heador($role, $link); ?>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=1lHjDeB3dA0" target="tutorial">https://youtube.com/watch?v=1lHjDeB3dA0</a> You may also join Discord and ask for help!
                                        </div>
                    <div id="rename-app" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Rename Application</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Name:</label>
                                                        <input type="text" class="form-control" name="name" placeholder="New Application Name">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="renameapp">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>

<script type="text/javascript">

var myLink = document.getElementById('mylink');

myLink.onclick = function(){


$(document).ready(function(){
        $("#content").fadeOut(100);
        $("#changeapp").fadeIn(1900);
        }); 

}


</script>


						<?php
		if($_SESSION['app'])
		{
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    if ($row['enabled'] == "1"){
                    $enabled = "Enabled";
                    }
                    else
                    {
                    $enabled = "Disabled";
                    }
                    
                    if ($row['hwidcheck'] == "1"){
                    $hwidcheck = "Enabled";
                    }
                    else
                    {
                    $hwidcheck = "Disabled";    
                    }

                    $verr = $row['ver'];
                    $dll = $row['download'];
                    $wh = $row['webhook'];
                    $rs = $row['resellerstore'];
					
                    $whsecret = $row['sellixsecret'];
                    $dayproduct = $row['dayproduct'];
                    $weekproduct = $row['weekproduct'];
                    $monthproduct = $row['monthproduct'];
                    $lifetimeproduct = $row['lifetimeproduct'];
					
					
					$appdisabled = $row['appdisabled'];
					$usernametaken = $row['usernametaken'];
					$keynotfound = $row['keynotfound'];
					$keyused = $row['keyused'];
					$nosublevel = $row['nosublevel'];
					$usernamenotfound = $row['usernamenotfound'];
					$passmismatch = $row['passmismatch'];
					$hwidmismatch = $row['hwidmismatch'];
					$noactivesubs = $row['noactivesubs'];
					$hwidblacked = $row['hwidblacked'];
					$keypaused = $row['keypaused'];
					$keyexpired = $row['keyexpired'];
                }
            }
		}
            
?>

                        <div class="card">
                            <div class="card-body">
                                <form class="form" method="post">
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">Status</label>
                                        <div class="col-10">
											<select class="form-control" name="statusinput"><option><?php echo $enabled; 
                                                    
                                                    if($enabled == "Enabled")
                                                    {
                                                        echo"<option>Disabled</option>";
                                                    }
                                                    else
                                                    {
                                                        echo"<option>Enabled</option>";
                                                    }
                                                    
                                                    ?></option></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-2 col-form-label">HWID Lock</label>
                                        <div class="col-10">
											<select class="form-control" name="hwidinput"><option><?php echo $hwidcheck; 
                                                    
                                                    if($hwidcheck == "Enabled")
                                                    {
                                                        echo"<option>Disabled</option>";
                                                    }
                                                    else
                                                    {
                                                        echo"<option>Enabled</option>";
                                                    }
                                                    
                                                    ?></option></select>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Version</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="10" name="version" value="<?php echo $verr; ?>" placeholder="<?php echo $verr; ?>" placeholder="Application Verion..">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Download</label>
                                        <div class="col-10">
                                            <input class="form-control" name="download" value="<?php echo $dll; ?>" type="text" placeholder="URL Link That Will Be Opened If Version doesn't match (auto update)">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Webhook</label>
                                        <div class="col-10">
                                            <input class="form-control" name="webhook" value="<?php echo $wh; ?>" type="text" placeholder="Discord Webhook Link For Sending Notifications & Logs">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Reseller Store</label>
                                        <div class="col-10">
                                            <input class="form-control" name="resellerstore" value="<?php echo $rs; ?>" placeholder="If you don't want to use the inbuilt store for resellers, set a store link." type="text">
                                        </div>
                                    </div>
									<br>
									<br>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">App Disabled Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="appdisabled" id="defaultconfig-3" value="<?php echo $appdisabled; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Username Taken Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="usernametaken" id="defaultconfig-3" value="<?php echo $usernametaken; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Invalid Key Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="keynotfound" id="defaultconfig-3" value="<?php echo $keynotfound; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Used Key Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="keyused" id="defaultconfig-3" value="<?php echo $keyused; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">No Subs Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="nosublevel" id="defaultconfig-3" value="<?php echo $nosublevel; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Username Invalid Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="usernamenotfound" id="defaultconfig-3" value="<?php echo $usernamenotfound; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Password Mismatch Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="passmismatch" id="defaultconfig-3" value="<?php echo $passmismatch; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Hwid Mismatch Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="hwidmismatch" id="defaultconfig-3" value="<?php echo $hwidmismatch; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Expired Sub Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="noactivesubs" id="defaultconfig-3" value="<?php echo $noactivesubs; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Blacklisted Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="hwidblacked" id="defaultconfig-3" value="<?php echo $hwidblacked; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Paused Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="keypaused" id="defaultconfig-3" value="<?php echo $keypaused; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Expired Msg</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="100" name="keyexpired" id="defaultconfig-3" value="<?php echo $keyexpired; ?>" placeholder="Custom response you'd like. Max 100 chars">
                                        </div>
                                    </div>
									<br>
									<br>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Webhook Secret</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="32" name="webhooksecret" value="<?php echo $whsecret; ?>" id="defaultconfig-3" placeholder="Webhook secret found in General Shop Settings on Sellix">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Day Product ID</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="13" name="dayproduct" value="<?php echo $dayproduct; ?>" id="defaultconfig-3" placeholder="Product ID of Day Reseller Key Sellix Product">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Week Product ID</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="13" name="weekproduct" value="<?php echo $weekproduct; ?>" id="defaultconfig-3" placeholder="Product ID of Week Reseller Key Sellix Product">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Month Product ID</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="13" name="monthproduct" value="<?php echo $monthproduct; ?>" id="defaultconfig-3" placeholder="Product ID of Month Reseller Key Sellix Product">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Lifetime Product ID</label>
                                        <div class="col-10">
                                            <input class="form-control" maxlength="13" name="lifetimeproduct" value="<?php echo $lifetimeproduct; ?>" id="defaultconfig-3" placeholder="Product ID of Lifetime Reseller Key Sellix Product">
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Webhook Link</label>
                                        <div class="col-10">
                                            <label class="form-control"><?php
                                            echo '<a href="https://keyauth.com/api/reseller/?app='.$_SESSION['secret'].'" target="webhooklink" class="secretlink">https://keyauth.com/api/reseller/?app='.$_SESSION['secret'].'</a>';
                                        
											?></label>
                                        </div>
                                    </div>
                                    <button name="updatesettings" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                                </form>
                            </div>
                        </div>
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
if(isset($_POST['updatesettings']))
{
    $status = sanitize($_POST['statusinput']);
    $hwid = sanitize($_POST['hwidinput']);
    $ver = sanitize($_POST['version']);
    $dl = sanitize($_POST['download']);
    $webhooker = sanitize($_POST['webhook']);
    $resellerstorelink = sanitize($_POST['resellerstore']);
    
    if($status == "Enabled")
    {
        $status = 1;
    }
    else if($status == "Disabled")
    {
        $status = 0;
    }
    else
    {
        $status == $row['enabled'];
    }
    
    if($hwid == "Enabled")
    {
        $hwid = 1;
    }
    else if($hwid == "Disabled")
    {
        $hwid = 0;
    }
    else
    {
        $hwid == $row['hwidcheck'];
    }

    //$status = 1;
    //$hwid = 1;
    
 
    ($result = mysqli_query($link, "UPDATE `apps` SET `enabled` = '$status', `hwidcheck` = '$hwid', `ver` = '$ver', `download` = '$dl', `webhook` = '$webhooker', `resellerstore` = '$resellerstorelink' WHERE `secret` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
	
	
	$appdisabledpost = sanitize($_POST['appdisabled']);
	$usernametakenpost = sanitize($_POST['usernametaken']);
	$keynotfoundpost = sanitize($_POST['keynotfound']);
	$keyusedpost = sanitize($_POST['keyused']);
	$nosublevelpost = sanitize($_POST['nosublevel']);
	$usernamenotfoundpost = sanitize($_POST['usernamenotfound']);
	$passmismatchpost = sanitize($_POST['passmismatch']);
	$hwidmismatchpost = sanitize($_POST['hwidmismatch']);
	$noactivesubspost = sanitize($_POST['noactivesubs']);
	$hwidblackedpost = sanitize($_POST['hwidblacked']);
	$keypausedpost = sanitize($_POST['keypaused']);
	$keyexpiredpost = sanitize($_POST['keyexpired']);
	
	($result = mysqli_query($link, "UPDATE `apps` SET `appdisabled` = '$appdisabledpost', `usernametaken` = '$usernametakenpost', `keynotfound` = '$keynotfoundpost',`keyused` = '$keyusedpost', `nosublevel` = '$nosublevelpost', `usernamenotfound` = '$usernamenotfoundpost', `passmismatch` = '$passmismatchpost', `hwidmismatch` = '$hwidmismatchpost', `noactivesubs` = '$noactivesubspost', `hwidblacked` = '$hwidblackedpost', `keypaused` = '$keypausedpost', `keyexpired` = '$keyexpiredpost' WHERE `secret` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
    
	$webhooksecret = sanitize($_POST['webhooksecret']);
	$day = sanitize($_POST['dayproduct']);
	$week = sanitize($_POST['weekproduct']);
	$month = sanitize($_POST['monthproduct']);
	$life = sanitize($_POST['lifetimeproduct']);
	
	($result = mysqli_query($link, "UPDATE `apps` SET `sellixsecret` = '$webhooksecret', `dayproduct` = '$day', `weekproduct` = '$week', `monthproduct` = '$month',`lifetimeproduct` = '$life' WHERE `secret` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
	
    if($result)
    {
                            echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .success({
                                message: \'Updated Settings!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>";
    }
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