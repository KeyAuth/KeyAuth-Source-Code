<?php
include '../../../includes/connection.php';
include '../../../includes/functions.php';
session_start();

if (!isset($_SESSION['username']))
{
    header("Location: ../../../login/");
    exit();
}

$username = $_SESSION['username'];
($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link));
$row = mysqli_fetch_array($result);

$banned = $row['banned'];
$lastreset = $row['lastreset'];
if (!is_null($banned) || $_SESSION['logindate'] < $lastreset)
{
    echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>";
    session_destroy();
    exit();
}
$role = $row['role'];
$_SESSION['role'] = $role;

$expires = $row['expires'];
$timeleft = false;
if(in_array($role,array("developer", "seller")))
{
	$timeleft = expire_check($username, $expires);
}

if ($role == "Reseller")
{
    die('Resellers Not Allowed Here');
}

$darkmode = $row['darkmode'];

$format = $row['format'];
$amt = $row['amount'];
$lvl = $row['lvl'];
$note = $row['note'];
$dur = $row['duration'];

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
    <title>KeyAuth - Chats</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdn.keyauth.uk/static/images/favicon.png">
	<script src="https://cdn.keyauth.uk/dashboard/assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Custom CSS -->
	<link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="https://cdn.keyauth.uk/dashboard/dist/css/style.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

	<script src="https://cdn.keyauth.uk/dashboard/unixtolocal.js"></script>

	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	
	<script>
	$(document).ready(function () {
	//change selectboxes to selectize mode to be searchable
	$("select").select2();
	});
	</script>
	                    
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
                        <h4 class="page-title">Chats</h4>
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
$result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '$username'");

$rows = array();
while ($r = mysqli_fetch_assoc($result))
{
    $rows[] = $r;
}

foreach ($rows as $row)
{

    $appname = $row['name'];
?>
        <option><?php echo $appname; ?></option>
        <?php
}
?>
</select>
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
    ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$selectOption' AND `owner` = '" . $_SESSION['username'] . "'")) or die(mysqli_error($link));
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
					<div class="card"> <div class="card-body">
					<?php heador($role, $link); ?>
					</div></div>
					<?php if($timeleft) { ?>
					<div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>
					<?php } ?>
					<form method="POST">
					<button data-toggle="modal" type="button" data-target="#create-channel" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Channel</button>  <button data-toggle="modal" type="button" data-target="#clear-channel" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Clear channel</button>  <button data-toggle="modal" type="button" data-target="#unmute-user" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-undo fa-sm text-white-50"></i> Unmute User</button>
                            </form>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=oLj04x0k1RI" target="tutorial">https://youtube.com/watch?v=oLj04x0k1RI</a> You may also join Discord and ask for help!</div>
<div id="create-channel" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Channels</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Name:</label>
                                                        <input class="form-control" name="name" placeholder="Chat channel name">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Chat cooldown Unit:</label>
                                                        <select name="unit" class="form-control"><option value="1">Seconds</option><option value="60">Minutes</option><option value="3600">Hours</option><option value="86400">Days</option><option value="604800">Weeks</option><option value="2629743">Months</option><option value="31556926">Years</option><option value="315569260">Lifetime</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Chat cooldown: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Delay users will have to wait to send their next message"></i></label>
                                                        <input name="delay" type="number" class="form-control" placeholder="Multiplied by selected delay unit" required>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="addchannel">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									
									<div id="unmute-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Unmute User</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Name:</label>
                                                        <select class="form-control" name="user">
														<?php
														($result = mysqli_query($link, "SELECT * FROM `chatmutes` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));

														$rows = array();
														while ($r = mysqli_fetch_assoc($result))
														{
															$rows[] = $r;
														}
												
														foreach ($rows as $row)
														{
														?>
														<option><?php echo $row["user"]; ?></option>
														<?php } ?>
														</select>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="unmuteuser">Unmute</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
					
<div id="clear-channel" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Clear Channel</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Channel name:</label>
                                                        <select class="form-control" name="channel">
														<?php
														($result = mysqli_query($link, "SELECT * FROM `chats` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));

														$rows = array();
														while ($r = mysqli_fetch_assoc($result))
														{
															$rows[] = $r;
														}
												
														foreach ($rows as $row)
														{
														?>
														<option><?php echo $row["name"]; ?></option>
														<?php } ?>
														</select>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="clearchannel">Clear</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                    <?php

	if (isset($_POST['addchannel']))
	{
		if($role != "seller")
		{
			error("You must upgrade to seller to create chat channels");
			echo "<meta http-equiv='Refresh' Content='3'>";
			return;
		}

		$name = sanitize($_POST['name']);
		$unit = sanitize($_POST['unit']);
		$delay = sanitize($_POST['delay']);
	
		$delay = $delay * $unit;
		
		mysqli_query($link, "INSERT INTO `chats` (`name`, `delay`, `app`) VALUES ('$name','$delay','" . $_SESSION['app'] . "')");
		
		success("Successfully created channel!");
		echo "<meta http-equiv='Refresh' Content='3'>";
    
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
<div id="mute-user" class="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Mute User</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Unit Of Time Muted:</label>
                                                        <select name="muted" class="form-control"><option value="86400">Days</option><option value="60">Minutes</option><option value="3600">Hours</option><option value="1">Seconds</option><option value="604800">Weeks</option><option value="2629743">Months</option><option value="31556926">Years</option><option value="315569260">Lifetime</option></select>
														<input type="hidden" class="muteuser" name="user">
													</div>
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Time Muted:</label>
                                                        <input class="form-control" name="time" placeholder="Multiplied by selected unit of time muted">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="muteuser">Ban</button>
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
<th>Author</th>
<th>Message</th>
<th>Time Sent</th>
<th>Channel</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

<?php
    if ($_SESSION['app'])
    {
        ($result = mysqli_query($link, "SELECT * FROM `chatmsgs` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));

        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }

        foreach ($rows as $row)
        {

            $user = $row['author'];
?>

													<tr>

                                                    <td><?php echo $user; ?></td>
													
													<td><?php echo $row["message"]; ?></td>
													
													<td><script>document.write(convertTimestamp(<?php echo $row["timestamp"]; ?>));</script></td>
                                                    
                                                    <td><?php echo $row["channel"]; ?></td>
													
                                            <form method="POST"><td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" name="deletemsg" value="<?php echo $row["message"]; ?>">Delete</button>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#mute-user" onclick="muteuser('<?php echo $user; ?>')">Mute</a></div></td></tr></form>
<?php
        }
    }

?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Author</th>
<th>Message</th>
<th>Time Sent</th>
<th>Channel</th>
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
    if (isset($_POST['deletemsg']))
    {
        $msg = sanitize($_POST['deletemsg']);
        mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '" . $_SESSION['app'] . "' AND `message` = '$msg'");
        if (mysqli_affected_rows($link) != 0) // check query impacted something, else show error
        
        {
            success("Message Successfully Deleted!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }
        else
        {
            mysqli_close($link);
            error("Failed To Delete Message!");
        }
    }
    if (isset($_POST['muteuser']))
    {
        $user = sanitize($_POST['user']);

        $result = mysqli_query($link, "SELECT * FROM `users` WHERE `app` = '" . $_SESSION['app'] . "' AND `username` = '$user'");
        if (mysqli_num_rows($result) == 0) // check if key exists
        {
            mysqli_close($link);
            error("User not Found!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
        }
		
		$muted = sanitize($_POST['muted']);
        $time = sanitize($_POST['time']);
		$time = $time * $muted + time();

        mysqli_query($link, "INSERT INTO `chatmutes` (`user`, `time`, `app`) VALUES ('$user','$time','" . $_SESSION['app'] . "')");
        success("Key Successfully Banned!");
        echo "<meta http-equiv='Refresh' Content='2'>";
    }
	
	if (isset($_POST['unmuteuser']))
    {
        $user = sanitize($_POST['user']);
        mysqli_query($link, "DELETE FROM `chatmutes` WHERE `app` = '" . $_SESSION['app'] . "' AND `user` = '$user'"); // delete any subscriptions created with key
        if (mysqli_affected_rows($link) != 0) // check query impacted something, else show error
        
        {
            success("User Successfully Unmuted!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }
        else
        {
            mysqli_close($link);
            error("Failed To Unmute User!");
        }
    }
	
	if (isset($_POST['clearchannel']))
    {
        $channel = sanitize($_POST['channel']);
        mysqli_query($link, "DELETE FROM `chatmsgs` WHERE `app` = '" . $_SESSION['app'] . "' AND `channel` = '$channel'"); // delete any subscriptions created with key
        if (mysqli_affected_rows($link) != 0) // check query impacted something, else show error
        
        {
            success("Channel Successfully Cleared!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }
        else
        {
            mysqli_close($link);
            error("Failed To Clear Channel!");
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

<script>
                        
		function muteuser(key) {
		 var muteuser = $('.muteuser');
		 muteuser.attr('value', key);
      }
                    </script>
</body>
</html>