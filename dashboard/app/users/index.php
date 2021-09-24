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
    <title>KeyAuth - Users</title>
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
?>
                <script type='text/javascript'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                <?php
        }
        else
        // otherwise if the user has more than one app, choose which app to load
        
        {
?>
                <script type='text/javascript'>
                
                        $(document).ready(function(){
        $("#changeapp").fadeIn(1900);
        });             
                
                </script>
                <?php
        }
    }
    else
    // if user doesnt have any apps created, take them to the screen to create an app
    
    {
?>
                <script type='text/javascript'>
                
                        $(document).ready(function(){
        $("#createapp").fadeIn(1900);
        });             
                
                </script>
                <?php
    }

}
else
// app already selected, load page like normal

{
?>
                <script type='text/javascript'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                <?php
}

?>
</head>
<body data-theme="<?php echo (($darkmode ? 1 : 0) ? 'light' : 'dark'); ?>">
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
                        <h4 class="page-title">Users</h4>
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
					<form method="POST">
					<button data-toggle="modal" type="button" data-target="#create-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create User</button>  <button data-toggle="modal" type="button" data-target="#extend-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-clock fa-sm text-white-50"></i> Extend User</button>  <button name="delusers" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to add all users?')"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Users</button>  <button name="resetall" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to reset HWID for all users?')"><i class="fas fa-redo-alt fa-sm text-white-50"></i> HWID Reset All Users</button>
                            </form>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=1lHjDeB3dA0" target="tutorial">https://youtube.com/watch?v=1lHjDeB3dA0</a> You may also join Discord and ask for help!
                                        </div>
<div id="create-user" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add User</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Username:</label>
                                                        <input type="text" class="form-control" name="username" placeholder="Username for user" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Password:</label>
                                                        <input type="password" class="form-control" name="password" placeholder="Password for user" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Subscription:</label>
                                                        <select name="sub" class="form-control">
														<?php
														($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
														if (mysqli_num_rows($result) > 0)
															{
																while ($row = mysqli_fetch_array($result))
																{
																	echo "  <option>". $row["name"]. "</option>";
																}
															}
														
														?>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Subscription Expiry:</label>
                                                        <?php
														echo '<input class="form-control" type="datetime-local" name="expiry" value="' . date("Y-m-d\TH:i", time()) . '" required>';
														?>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="adduser">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									<div id="extend-user" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Extend User</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">User:</label>
                                                        <select name="user" class="form-control">
														<?php
														($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
														if (mysqli_num_rows($result) > 0)
															{
																while ($row = mysqli_fetch_array($result))
																{
																	echo "  <option>". $row["username"]. "</option>";
																}
															}
														
														?>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Subscription:</label>
                                                        <select name="sub" class="form-control">
														<?php
														($result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `app` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
														if (mysqli_num_rows($result) > 0)
															{
																while ($row = mysqli_fetch_array($result))
																{
																	echo "  <option>". $row["name"]. "</option>";
																}
															}
														
														?>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Subscription Expiry:</label>
                                                        <?php
														echo '<input class="form-control" type="datetime-local" name="expiry" value="' . date("Y-m-d\TH:i", time()) . '" required>';
														?>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="extenduser">Add</button>
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

<script type="text/javascript">

var myLink = document.getElementById('mylink');

myLink.onclick = function(){


$(document).ready(function(){
        $("#content").fadeOut(100);
        $("#changeapp").fadeIn(1900);
        }); 

}


</script>
<div id="ban-user" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Ban User</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Ban reason:</label>
                                                        <input type="text" class="form-control" name="reason" placeholder="Reason for ban" required>
														<input type="hidden" class="banuser" name="un">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="banuser">Ban</button>
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
<th>Username</th>
<th>HWID</th>
<th>IP</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
		if($_SESSION['app']) {
        ($result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '".$_SESSION['app']."'")) or die(mysqli_error($link));
        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }

        foreach ($rows as $row)
        {

        $user = $row['username'];
		?>

                                                    <tr>

                                                    <td><?php echo $row["username"]; ?></td>

                                                    <td><?php echo $row["hwid"]; ?></td>
													
                                                    <td><?php echo $row["ip"] ?? "N/A"; ?></td>

                                                    <td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <form method="post"><button class="dropdown-item" name="deleteuser" value="<?php echo $user; ?>">Delete</button>
												<button class="dropdown-item" name="resetuser" value="<?php echo $user; ?>">Reset HWID</button>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-user" onclick="banuser('<?php echo $user; ?>')">Ban</a>
                                                <button class="dropdown-item" name="unbanuser" value="<?php echo $user; ?>">Unban</button>
                                                <div class="dropdown-divider"></div>
												<button class="dropdown-item" name="edituser" value="<?php echo $user; ?>">Edit</button></div></td></tr></form>
<?php
                                                

                                            }
                                            
		}

                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Username</th>
<th>HWID</th>
<th>IP</th>
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
				if(isset($_POST['deleteuser']))
				{
					$username = sanitize($_POST['deleteuser']);
					mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '".$_SESSION['app']."' AND `user` = '$username'");
					mysqli_query($link, "DELETE FROM `users` WHERE `app` = '".$_SESSION['app']."' AND `username` = '$username'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("User Successfully Deleted!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Delete User!");
					}
				}
								if(isset($_POST['resetuser']))
				{
					$un = sanitize($_POST['resetuser']);
					mysqli_query($link, "UPDATE `users` SET `hwid` = '' WHERE `app` = '".$_SESSION['app']."' AND `username` = '$un'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("User Successfully Reset");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Reset User");
					}
				}
				if(isset($_POST['banuser']))
				{
					$un = sanitize($_POST['un']);
					
					$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '".$_SESSION['app']."' AND `username` = '$un'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("User not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					$ip = $row["ip"];
					$reason = sanitize($_POST['reason']);
					
					mysqli_query($link, "UPDATE `users` SET `banned` = '$reason' WHERE `app` = '".$_SESSION['app']."' AND `username` = '$un'");
					
					if($hwid != NULL)
					{
					mysqli_query($link, "INSERT INTO `bans`(`hwid`,`type`, `app`) VALUES ('$hwid','hwid','".$_SESSION['app']."')");
					}
					if($ip != NULL)
					{
					mysqli_query($link, "INSERT INTO `bans`(`ip`,`type`, `app`) VALUES ('$ip','ip','".$_SESSION['app']."')");
					}
					success("User Successfully Banned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['unbanuser']))
				{
					$un = sanitize($_POST['unbanuser']);
					
					$result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '".$_SESSION['app']."' AND `username` = '$un'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("User not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					$ip = $row["ip"];
					
					mysqli_query($link, "UPDATE `users` SET `banned` = NULL WHERE `app` = '".$_SESSION['app']."' AND `username` = '$un'");
					mysqli_query($link, "DELETE FROM `bans` WHERE `hwid` = '$hwid' OR `ip` = '$ip' AND `app` = '".$_SESSION['app']."'");
					
					success("User Successfully Unbanned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['edituser']))
				{
					$un = sanitize($_POST['edituser']);
					
					$result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '".$_SESSION['app']."'");
                    if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("User not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
                    $row = mysqli_fetch_array($result);
					?>
					<div id="edit-user" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true"o ydo >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit User</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Password:</label>
                                                        <input type="password" class="form-control" name="pass" placeholder="Set new password, we cannot read old password because it's hashed with BCrypt">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Active Subscriptions:</label>
                                                        <select class="form-control" name="sub">
														<?php
														$result = mysqli_query($link, "SELECT * FROM `subs` WHERE `user` = '$un' AND `app` = '".$_SESSION['app']."' AND `expiry` > '".time()."'");
														
														$rows = array();
														while ($r = mysqli_fetch_assoc($result))
														{
															$rows[] = $r;
														}
														
														foreach ($rows as $subrow)
														{
														
														$value = "[" . $subrow['subscription'] . "]" . " - Expires: <script>document.write(convertTimestamp(" . $subrow["expiry"] . "));</script>";
														?>
														<option><?php echo $value; ?></option>
														<?php
														}
														?>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Additional HWID:</label>
                                                        <input type="text" class="form-control" name="hwid" placeholder="Enter HWID if you want this key to support multiple computers">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">HWID:</label>
                                                        <p><?php echo $row['hwid']; ?></p>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">IP:</label>
                                                        <p><?php echo $row['ip']; ?></p>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-warning waves-effect waves-light" value="<?php echo $un; ?>" name="deletesub">Delete Subscription</button>
                                                <button class="btn btn-danger waves-effect waves-light" value="<?php echo $un; ?>" name="saveuser">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									<?php
				}
				
				if(isset($_POST['saveuser']))
				{
					$un = sanitize($_POST['saveuser']);
					
					$hwid = sanitize($_POST['hwid']);
					
					$pass = sanitize($_POST['pass']);
					
					if(isset($hwid) && trim($hwid) != '')
					{
						$result = mysqli_query($link, "SELECT `hwid` FROM `users` WHERE `username` = '$un' AND `app` = '".$_SESSION['app']."'");                           
						$row = mysqli_fetch_array($result);                      
						$hwidd = $row["hwid"];

						$hwidd = $hwidd .= $hwid;

						mysqli_query($link, "UPDATE `users` SET `hwid` = '$hwidd' WHERE `username` = '$un' AND `app` = '".$_SESSION['app']."'");
					}
					
					if(isset($pass) && trim($pass) != '')
					{
						mysqli_query($link, "UPDATE `users` SET `password` = '".password_hash($pass, PASSWORD_BCRYPT)."' WHERE `username` = '$un' AND `app` = '".$_SESSION['app']."'");
					}
		
					success("Successfully Updated User");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['deletesub']))
				{
					$un = sanitize($_POST['deletesub']);
					
					$sub = sanitize($_POST['sub']);
					
					function get_string_between($string, $start, $end){
						$string = ' ' . $string;
						$ini = strpos($string, $start);
						if ($ini == 0) return '';
						$ini += strlen($start);
						$len = strpos($string, $end, $ini) - $ini;
						return substr($string, $ini, $len);
					}
					
					$sub = get_string_between($sub, '[', ']');
					
					mysqli_query($link, "DELETE FROM `subs` WHERE `app` = '".$_SESSION['app']."' AND `user` = '$un' AND `subscription` = '$sub'");
					if(mysqli_affected_rows($link) != 0)
					{
					success("Successfully Deleted User\'s Subscription");
					echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Delete User\'s Subscription!");
					}
					
				}
				
				if(isset($_POST['extenduser']))
				{
					$un = sanitize($_POST['user']);
					$sub = sanitize($_POST['sub']);
					$expiry = strtotime(sanitize($_POST['expiry']));
					
					$result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '".$_SESSION['app']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Subscription not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					else if($expiry < time())
					{
						mysqli_close($link);
						error("Please choose an expiry in the future!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;						
					}
					
					$result = mysqli_query($link, "SELECT * FROM `users` WHERE `username` = '$un' AND `app` = '".$_SESSION['app']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("User doesn\'t exist!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$un','$sub', '$expiry', '".$_SESSION['app']."')");
					if(mysqli_affected_rows($link) != 0)
					{
						success("User Successfully Extended!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Extend User!");
					}
					
				}
				
				if(isset($_POST['adduser']))
				{
					$un = sanitize($_POST['username']);
					$pw = password_hash(sanitize($_POST['password']),PASSWORD_BCRYPT);
					$sub = sanitize($_POST['sub']);
					$expiry = strtotime(sanitize($_POST['expiry']));
					
					$result = mysqli_query($link, "SELECT * FROM `subscriptions` WHERE `name` = '$sub' AND `app` = '".$_SESSION['app']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Subscription not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					else if($expiry < time())
					{
						mysqli_close($link);
						error("Please choose an expiry in the future!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;						
					}
					
					mysqli_query($link, "INSERT INTO `subs` (`user`, `subscription`, `expiry`, `app`) VALUES ('$un','$sub', '$expiry', '".$_SESSION['app']."')");
					mysqli_query($link, "INSERT INTO `users` (`username`, `password`, `hwid`, `app`) VALUES ('$un','$pw', '', '".$_SESSION['app']."')");
					if(mysqli_affected_rows($link) != 0)
					{
						success("User Successfully Created!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Create User!");
					}
					
				}
				
				if(isset($_POST['delusers']))
				{
					mysqli_query($link, "DELETE FROM `users` WHERE `app` = '".$_SESSION['app']."'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("Users Successfully Deleted!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Delete Users!");
					}
				}
				
				if(isset($_POST['resetall']))
				{
					mysqli_query($link, "UPDATE `users` SET `hwid` = '' WHERE `app` = '".$_SESSION['app']."'");
					if(mysqli_affected_rows($link) != 0)
					{
						success("Users Successfully Reset!");
						echo "<meta http-equiv='Refresh' Content='2'>";
					}
					else
					{
						mysqli_close($link);
						error("Failed To Reset Users!");
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

<script>
                        
		function banuser(username) {
		 var banuser = $('.banuser');
		 banuser.attr('value', username);
      }
                    </script>
</body>
</html>