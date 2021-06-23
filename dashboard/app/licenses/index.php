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
			$format = $row['format'];

			
                            
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
    <title>KeyAuth - Licenses</title>
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

	<script src="../../files/unixtolocal.js"></script>

	                    
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
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://reddit.com/r/KeyAuth" target="reddit"> <i class="mdi mdi-reddit font-24"></i>
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
                        <h4 class="page-title">Licenses</h4>
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
        <?php
        if (isset($_POST['appname']))
        {
            $appname = sanitize($_POST['appname']);
            $result = mysqli_query($link, "SELECT * FROM apps WHERE name='$appname' AND owner='".$_SESSION['username']."'");
            if (mysqli_num_rows($result) > 0)
            {
				mysqli_close($link);
				error("You already own application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            
            $owner = $_SESSION['username'];
			
			if($role == "tester")
            {
			$result = mysqli_query($link, "SELECT * FROM apps WHERE owner='$owner'");

            if (mysqli_num_rows($result) > 0)
            {
				mysqli_close($link);
				error("Tester plan only supports one application!");
                echo "<meta http-equiv='Refresh' Content='2;'>";

                return;
            }
			
			}
			
			if($role == "Manager")
            {
				mysqli_close($link);
				error("Manager Accounts Are Not Allowed To Create Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
			}
			
            $ownerid = $_SESSION['ownerid'];
            $gen = generateRandomString();
            $clientsecret = hash('sha256', $gen);
            $sellerkey = generateRandomString();
            $result = mysqli_query($link, "INSERT INTO `apps`(`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES ('".$owner."','".$appname."','".$clientsecret."','$ownerid', '1','1','$sellerkey')");
            if($result)
            {
                $_SESSION['secret'] = $clientsecret;
				success("Successfully Created App!");
                $_SESSION['app'] = $clientsecret;
                $_SESSION['name'] = $appname;
                $_SESSION['sellerkey'] = $sellerkey;
                echo "<meta http-equiv='Refresh' Content='2;'>";     
            }
            else
            {
                printf("Error message: %s\n", $link->error);
            }
        }
        
        
        ?>
			
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
							error("You don\'t own application!");
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
					<form method="POST">
					<button data-toggle="modal" type="button" data-target="#create-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create keys</button>  <button data-toggle="modal" type="button" data-target="#import-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Import keys</button>  <button data-toggle="modal" type="button" data-target="#comp-keys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Compensate</button><br><br><button name="dlkeys" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-download fa-sm text-white-50"></i> Download All keys</button>  <button name="delkeys" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to add all keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All keys</button>  <button name="delexpkeys" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to add all expired keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Expired keys</button>  <button name="resetall" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to reset HWID for all keys?')"><i class="fas fa-redo-alt fa-sm text-white-50"></i> HWID Reset All Keys</button>  <button name="deleteallunused" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to delete all unused keys?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Unused Keys</button>  
                            </form>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=uJ0Umy_C6Fg" target="tutorial">https://youtube.com/watch?v=uJ0Umy_C6Fg</a> You may also join Discord and ask for help!
                                        </div>
<div id="create-keys" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Licenses</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Amount:</label>
                                                        <input type="number" class="form-control" name="amount" placeholder="Default 1">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Key Mask:</label>
                                                        <input type="text" class="form-control" value="<?php echo $format; ?>" value="XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX" placeholder="Key Format. X is capital random char, x is lowercase" name="mask" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Level:</label>
                                                        <input type="text" class="form-control" name="level" placeholder="Default 1">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Note:</label>
                                                        <input type="text" class="form-control" name="note" placeholder="Optional, e.g. this license was for Joe">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Expiry Unit:</label>
                                                        <select name="unit" class="form-control"><option>Days</option><option>Minutes</option><option>Hours</option><option>Seconds</option><option>Weeks</option><option>Months</option><option>Years</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Expiry Duration:</label>
                                                        <input name="expiry" type="number" class="form-control" placeholder="Multiplied by selected Expiry unit" required>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="genkeys">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
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
					
<div id="import-keys" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Import Licenses</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Keys:</label>
                                                        <input class="form-control" name="keys" placeholder="Enter Keys In Format: key,level,days|key,level,days">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="importkeys">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									
<div id="comp-keys" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Compensate Licenses</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Unit Of Time To Add:</label>
                                                        <select name="unit" class="form-control"><option>Days</option><option>Minutes</option><option>Hours</option><option>Seconds</option><option>Weeks</option><option>Months</option><option>Years</option></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Time To Add:</label>
                                                        <input class="form-control" name="time" placeholder="Multiplied by selected unit of time">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="compp">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                    <?php
					
					
							function license_masking($mask)
			{
				$mask_arr = str_split($mask);
                $size_of_mask = count($mask_arr);
                for($i = 0; $i < $size_of_mask; $i++)
				{
                    if($mask_arr[$i] === 'X')
					{
                        $mask_arr[$i] = random_string_upper(1);
					}
					else if($mask_arr[$i] === 'x')
					{
                        $mask_arr[$i] = random_string_lower(1);
					}
				}
				return implode('', $mask_arr);
			}

			function license($amount,$mask,$expiry,$level,$link)
			{
				
			$licenses = array();
			
			for ($i = 0; $i < $amount; $i++) {
	
			$license = license_masking($mask);
			mysqli_query($link, "INSERT INTO `keys` (`key`, `note`, `expires`, `lastlogin`, `hwid`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$license','', '$expiry', '','','Not Used','$level','".$_SESSION['username']."', '".time()."', '".$_SESSION['app']."')");
			// echo $key;
			$licenses[] = $license;
			}

			return $licenses;
			}
                                        
                            if(isset($_POST['genkeys']))
                            {
                                
                                $amount = sanitize($_POST['amount']);
                                if($amount > 100)
                                {
								mysqli_close($link);
								error("Generating Keys has been limited to 100 per time to reduce accidental spam. Please try again.");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;
                                }
                                $abc = time();
                                
                                $level = sanitize($_POST['level']);
                                $note = sanitize($_POST['note']);
                                
                                if(!isset($amount) || trim($amount) == '')
                                {
                                $amount = 1;
                                }

                                if(!isset($note) || trim($note) == '')
                                {
                                $note == "default note";
                                }
                                
                                if(!isset($level) || trim($level) == '')
                                {
                                $level = 1;
                                }
                                $expiry = sanitize($_POST['expiry']);
                                
                                 if(!isset($expiry) || trim($expiry) == '')
                                {
								mysqli_close($link);
                                error("No Expiry Set!");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;
                                }
                                else
                                {
                                
                                if (!is_numeric($expiry))
                                {
								mysqli_close($link);
								error("Only Numbers Allowed For Expiry!");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;
                                }
                                else
                                {
                                if($role == "tester")
                                {
                                $result = mysqli_query($link, "SELECT * FROM `keys` WHERE `genby` = '".$_SESSION['username']."'");
								$currkeys = mysqli_num_rows($result);
								if($currkeys == 0 && $amount > 25)
								{
								mysqli_close($link);
                                error("Tester Plan Only Allows For One Key, please upgrade!");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;	
								}
								else if($currkeys == 0)
								{
									goto a;
								}
								
								
                                if($currkeys + $amount > 25)
                                {
								mysqli_close($link);
                                error("Tester Plan Only Allows For One Key, please upgrade!");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;
                                }
								a:
                                }
								
								$unit = sanitize($_POST['unit']);
								if($unit == "Days")
								{
									$multiplier = 86400;
								}
								else if($unit == "Minutes")
								{
									$multiplier = 60;
								}
								else if($unit == "Hours")
								{
									$multiplier = 3600;
								}
								else if($unit == "Seconds")
								{
									$multiplier = 1;
								}
								else if($unit == "Weeks")
								{
									$multiplier = 604800;
								}
								else if($unit == "Months")
								{
									$multiplier = 2.628e+6;
								}
								else if($unit == "Years")
								{
									$multiplier = 31535965.4396976;
								}
								
                                    
                                $expiry = $expiry * $multiplier;  
                                $mask = sanitize($_POST['mask']);
								
								
								// mask instead of format
								// check if amount is over one and mask does not contain any Xs
                                if($amount > 1 && strpos($mask, 'X') === false && strpos($mask, 'x') === false)
                                {
								mysqli_close($link);
                                error("Can\'t do custom key with amount greater than one");
                                echo "<meta http-equiv='Refresh' Content='4;'>";
								return;
                                }
								
								$time = time();
								
								
                                $key = license($amount,$mask,$expiry,$level,$link);
                                
                                if($result) // change to affected rows
                                {
								
								// webhook start
								$timestamp = date("c", strtotime("now"));

								$json_data = json_encode([
									// Message
									"content" => "".$_SESSION['username']." has created {$amount} keys",
									
									// Username
									"username" => "KeyAuth Logs",
								
								], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
								
								
								$ch = curl_init("webhook_link_here");
								curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/json'));
								curl_setopt($ch,CURLOPT_POST,1);
								curl_setopt($ch,CURLOPT_POSTFIELDS,$json_data);
								curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
								curl_setopt($ch,CURLOPT_HEADER,0);
								
								curl_exec($ch);
								curl_close($ch);
								// webhook end
								
								mysqli_query($link, "UPDATE `accounts` SET `format` = '$mask' WHERE `username` = '".$_SESSION['username']."'");
                                
                                if($amount > 1)
                                {
                                echo "<meta http-equiv='Refresh' Content='0; url=downloadbulk.php?time=". $time ."'>";
								}
								else
								{
echo "<script>
navigator.clipboard.writeText('".array_values($key)[0]."');
</script>";
echo "<meta http-equiv='Refresh' Content='4;'>"; 
echo '
            <script type=\'text/javascript\'>
                
            const notyf = new Notyf();
            notyf
            .success({
                message: \'License Created And Copied To Clipboard!\',
                duration: 3500,
                dismissible: true
            });                
                
        </script>
        ';
								}
                                }
                                }
                                }
                            }
							
							if(isset($_POST['importkeys']))
    {
     if($role == "tester")
     {
	 mysqli_close($link);
     echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Cant import keys with tester account! Must upgrade.\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>"; 
                            return;
     }


    $keys = sanitize($_POST['keys']);
    // die($keys);
    $text = explode ("|", $keys);

    str_replace('"', "", $text);
    str_replace("'", "", $text);



    foreach ($text as $line) {
       
       $array = explode(',', $line);
       $first = $array [0];
	   if(!isset($first) || $first == '')
	   {
		   mysqli_close($link);
		   echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Invalid Format, Please watch tutorial video!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>"; 
                            return;
	   }
       $second = $array [1];
	   if(!isset($second) || $second == '')
	   {
		   mysqli_close($link);
		   echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Invalid Format, Please watch tutorial video!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>"; 
                            return;
	   }
       $third = $array [2];
	   if(!isset($third) || $third == '')
	   {
		   mysqli_close($link);
		   echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Invalid Format, Please watch tutorial video!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>"; 
                            return;
	   }
       $expiry = $third * 86400;  
       mysqli_query($link, "INSERT INTO `keys` (`key`, `expires`, `lastlogin`, `hwid`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$first','$expiry', '','','Not Used','$second','".$_SESSION['username']."','".time()."','".$_SESSION['app']."')");
    }
    }
	
	if(isset($_POST['compp']))
    {


    $time = sanitize($_POST['time']);

    if(!is_numeric($time))
    {
		   mysqli_close($link);
           echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Numeric Value Only.\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>";
                            return;
    }
	
	$unit = sanitize($_POST['unit']);
								if($unit == "Days")
								{
									$multiplier = 86400;
								}
								else if($unit == "Minutes")
								{
									$multiplier = 60;
								}
								else if($unit == "Hours")
								{
									$multiplier = 3600;
								}
								else if($unit == "Seconds")
								{
									$multiplier = 1;
								}
								else if($unit == "Weeks")
								{
									$multiplier = 604800;
								}
								else if($unit == "Months")
								{
									$multiplier = 2.628e+6;
								}
								else if($unit == "Years")
								{
									$multiplier = 31535965.4396976;
								}
								
                                    
                                $time = $time * $multiplier;

    mysqli_query($link, "UPDATE `keys` SET `expires` = `expires`+$time WHERE `app` = '".$_SESSION['app']."' AND `status` = 'Used'");
	
	
	if(mysqli_affected_rows($link) != 0)
			{
				mysqli_close($link);
				success("Compensated All Used Licenses!");
				echo "<meta http-equiv='Refresh' Content='2;'>";
			}
			else
			{
				mysqli_close($link);
				error("Didn\'t find any used Licenses To Compenate!");
				echo "<meta http-equiv='Refresh' Content='2;'>"; 
                            return;
			}
    }
				
		if (isset($_POST['refreshapp']))
        {
			$gen = generateRandomString();
            $new_secret = hash('sha256', $gen);
			
			if($role == "Manager")
			{
			error("Manager Accounts Aren\'t Allowed To Refresh Applications");
			echo "<meta http-equiv='Refresh' Content='2;'>";
			return;
			}
			
            mysqli_query($link, "UPDATE `apps` SET `secret` = '$new_secret' WHERE `secret` = '".$_SESSION['app']."' AND `owner` = '".$_SESSION['username']."'");
            mysqli_query($link, "UPDATE `bans` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `files` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `keys` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `logs` SET `logapp` = '$new_secret' WHERE `logapp` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `subs` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `subscriptions` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `users` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `vars` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            mysqli_query($link, "UPDATE `webhooks` SET `app` = '$new_secret' WHERE `app` = '".$_SESSION['app']."'");
            
			$_SESSION['app'] = $new_secret;
			$_SESSION['secret'] = $new_secret;
			
			if(mysqli_affected_rows($link) != 0)
			{
			success("Successfully Refreshed App!");
			echo "<meta http-equiv='Refresh' Content='2;'>";         
			}
			else
			{
			error("Application Refresh Failed!");
			echo "<meta http-equiv='Refresh' Content='2;'>";
			}
		}
        
        
        if (isset($_POST['dlkeys']))
        {
        
        echo "<meta http-equiv='Refresh' Content='0; url=download.php'>";
        // get all rows, put in text file, download text file, delete text file.
        }
        
                if (isset($_POST['delkeys']))
        {
            mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '".$_SESSION['app']."'");
            if(mysqli_affected_rows($link) != 0)
			{
				success("Deleted All Keys!");
			}
			else
			{
				mysqli_close($link);
				error("Didn\'t find any keys!");
			}
        }


                if (isset($_POST['delexpkeys']))
        {
            $result = mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `status` != 'Not Used' AND `expires` < ".time()."");
			if(mysqli_affected_rows($link) != 0)
			{
				success("Deleted All Expired Keys!");
			}
			else
			{
				mysqli_close($link);
				error("Didn\'t find any expired keys!");
			}
							
							
            
        }
		
		                if (isset($_POST['deleteallunused']))
        {
            mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `status` = 'Not Used'");
			if(mysqli_affected_rows($link) != 0)
			{
				success("Deleted All Unused Keys!");
			}
			else
			{
				mysqli_close($link);
				error("Didn\'t find any used keys!");
			}
        }
		
		                if (isset($_POST['resetall']))
        {
            mysqli_query($link, "UPDATE `keys` SET `hwid` = '' WHERE `app` = '".$_SESSION['app']."' AND `status` != 'Not Used'");
			if(mysqli_affected_rows($link) != 0)
			{
				success("Reset HWID for all Keys!");
			}
			else
			{
				mysqli_close($link);
				error("Didn\'t find any used keys!");
			}
        }
                    ?>

<script type="text/javascript">

var myLink = document.getElementById('mylink');

myLink.onclick = function(){


$(document).ready(function(){
        $("#content").fadeOut(100);
        $("#changeapp").fadeIn(1900);
        }); 

}


</script>
<div id="ban-key" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Ban License</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Ban reason:</label>
                                                        <input type="text" class="form-control" name="reason" placeholder="Reason for ban" required>
														<input type="hidden" class="bankey" name="key">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="bankey">Ban</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="file_export" class="table table-striped table-bordered display">
                                        <thead>
                                            <tr>
<th>Key</th>
<th>Creation Date</th>
<th>Generated By</th>
<th>Expires</th>
<th>Last Login</th>
<th>Status</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
		if($_SESSION['app']) {
        ($result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {

                                                    echo "<tr>";

                                                    echo "  <td>". $row["key"]. "</td>";
													
													echo "<td><script>document.write(convertTimestamp(". $row["gendate"] ."));</script></td>";

                                                    echo "  <td>". $row["genby"]. "</td>";
                                                    
                                                    if($row["status"] == "Used")
                                                    {
                                                    echo "
                                                        <td><script>document.write(convertTimestamp(". $row["expires"] ."));</script></td>
                                                        <td><script>document.write(convertTimestamp(". $row["lastlogin"] ."));</script></td>
                                                        <td><label class=\"badge badge-danger\">Used</label></td>
                                                        ";
                                                    }
                                                    else if($row["status"] == "Banned")
                                                    {
                                                    echo"
                                                        <td><script>document.write(convertTimestamp(". $row["expires"] ."));</script></td>
                                                        <td><script>document.write(convertTimestamp(". $row["lastlogin"] ."));</script></td>
                                                        <td><label class=\"badge badge-danger\">Banned</label></td>";
                                                    }
                                                    else if($row["status"] == "Expired")
                                                    {
                                                    echo "
                                                        <td><script>document.write(convertTimestamp(". $row["expires"] ."));</script></td>
                                                        <td><script>document.write(convertTimestamp(". $row["lastlogin"] ."));</script></td>
                                                        <td><label class=\"badge badge-danger\">Expired</label></td>
                                                        ";
                                                    }
						    else if($row["status"] == "Paused")
                                                    {
                                                    echo "
                                                        <td>". ($row["expires"]/86400) ." Day(s)</td>
                                                        <td><script>document.write(convertTimestamp(". $row["lastlogin"] ."));</script></td>
                                                        <td><label class=\"badge badge-warning\">Paused</label></td>
                                                        ";
                                                    }
						    else
						    {
							echo"
                                                        <td>". ($row["expires"]/86400) ." Day(s)</td>
                                                        <td>N/A</td>
                                                        <td><label class=\"badge badge-success\">Not Used</label></td>";
					 	    }
                                                    
                                                    // echo "  <td>". $row["status"]. "</td>";

                                                    echo'<form method="POST"><td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" name="deletekey" value="' . $row['key'] . '">Delete</button>
                                                <button class="dropdown-item" name="resetkey" value="' . $row['key'] . '">Reset HWID</button>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-key" onclick="bankey(\'' . $row['key'] . '\')">Ban</a>
                                                <button class="dropdown-item" name="unbankey" value="' . $row['key'] . '">Unban</button>
                                                <button class="dropdown-item" name="pausekey" value="' . $row['key'] . '">Pause</button>
                                                <button class="dropdown-item" name="unpausekey" value="' . $row['key'] . '">Unpause</button>
                                                <div class="dropdown-divider"></div>
												<button class="dropdown-item" name="editkey" value="' . $row['key'] . '">Edit</button></div></td></tr></form>';

                                                }

                                            }
                                            
		}

                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Key</th>
<th>Creation Date</th>
<th>Generated By</th>
<th>Expires</th>
<th>Last Login</th>
<th>Status</th>
<th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
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
				if(isset($_POST['deletekey']))
				{
					$key = sanitize($_POST['deletekey']);
					mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("Key Successfully Deleted!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Delete Key!");
					}
				}
				if(isset($_POST['resetkey']))
				{
					$key = sanitize($_POST['resetkey']);
					mysqli_query($link, "UPDATE `keys` SET `hwid` = '' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("Key Successfully Reset!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Reset Key!");
					}
				}
				if(isset($_POST['bankey']))
				{
					$key = sanitize($_POST['key']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					$ip = $row["ip"];
					$reason = sanitize($_POST['reason']);
					
					mysqli_query($link, "UPDATE `keys` SET `banned` = '$reason', `status` = 'Banned' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					
					if($hwid != NULL)
					{
					mysqli_query($link, "INSERT INTO `bans`(`hwid`,`type`, `app`) VALUES ('$hwid','hwid','".$_SESSION['app']."')");
					}
					if($ip != NULL)
					{
					mysqli_query($link, "INSERT INTO `bans`(`ip`,`type`, `app`) VALUES ('$ip','ip','".$_SESSION['app']."')");
					}
					success("Key Successfully Banned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['unbankey']))
				{
					$key = sanitize($_POST['unbankey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					$ip = $row["ip"];
					
					mysqli_query($link, "UPDATE `keys` SET `banned` = NULL, `status` = 'Used' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					mysqli_query($link, "DELETE FROM `bans` WHERE `hwid` = '$hwid' OR `ip` = '$ip' AND `app` = '".$_SESSION['app']."'");
					
					success("Key Successfully Unbanned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['pausekey']))
				{
					$key = sanitize($_POST['pausekey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `status` = 'Used'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key isn\'t used!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$exp = mysqli_fetch_array($result)["expires"] - time();
					mysqli_query($link, "UPDATE `keys` SET `status` = 'Paused', `expires` = '$exp' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					
					success("Key Successfully Paused!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['unpausekey']))
				{
					$key = sanitize($_POST['unpausekey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `status` = 'Paused'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key isn\'t paused!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$exp = mysqli_fetch_array($result)["expires"] + time();
					mysqli_query($link, "UPDATE `keys` SET `status` = 'Used', `expires` = '$exp' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					
					success("Key Successfully Unpaused!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['editkey']))
				{
					$key = sanitize($_POST['editkey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `key` = '$key' AND `app` = '".$_SESSION['app']."'");
                    if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
                    $row = mysqli_fetch_array($result);
					
					$expiry = date("Y-m-d\TH:i", $row["expires"]);
					$currtime = date("Y-m-d\TH:i", time());
					
					echo'<div id="ban-key" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true"o ydo >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit License</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Key Level:</label>
                                                        <input type="text" class="form-control" name="level" value="' . $row['level'] . '" required>
														<input type="hidden" name="key" value="' . $key . '">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Key Expiry:</label>
                                                        <input class="form-control" type="datetime-local" name="expiry" value="' . date("Y-m-d\TH:i", $row["expires"]) . '" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Additional HWID:</label>
                                                        <input type="text" class="form-control" name="hwid" placeholder="Enter HWID if you want this key to support multiple computers">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">HWID:</label>
                                                        <p>' . $row['hwid'] . '</p>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">IP:</label>
                                                        <p>' . $row['ip'] . '</p>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="savekey">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>';
				}
				
				if(isset($_POST['savekey']))
				{
					$key = sanitize($_POST['key']);
					
					$expiry = sanitize($_POST['expiry']);
					$level = sanitize($_POST['level']);
					$hwid = sanitize($_POST['hwid']);
					
					$expiry = strtotime($expiry);
					
					if(isset($hwid) && trim($hwid) != '')
					{
						$result = mysqli_query($link, "SELECT `hwid` FROM `keys` WHERE `key` = '$key' AND `app` = '".$_SESSION['app']."'");                           

						$hwid = mysqli_fetch_array($result)["hwid"] .= $hwid;

						mysqli_query($link, "UPDATE `keys` SET `hwid` = '$hwid' WHERE `key` = '$key' AND `app` = '".$_SESSION['app']."'");
					}

					mysqli_query($link, "UPDATE `keys` SET `expires` = '$expiry',`level` = '$level' WHERE `key` = '$key' AND `app` = '".$_SESSION['app']."'");
		
					success("Successfully Updated Settings!");
					echo "<meta http-equiv='Refresh' Content='2'>";
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

<script>
                        
		function bankey(key) {
		 var bankey = $('.bankey');
		 bankey.attr('value', key);
      }
                    </script>
</body>
</html>