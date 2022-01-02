<?php
ob_start();

include '../../../includes/connection.php';
include '../../../includes/misc/autoload.phtml';
include '../../../includes/dashboard/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
	$timeleft = dashboard\primary\expireCheck($username, $expires);
}

if ($role != "developer" && $role != "seller")
{
    die("Must Upgrade To Manage Accounts");
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
    <title>KeyAuth - Manage Accounts</title>
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
</head>
<body data-theme="<?php if ($darkmode == 0)
{
    echo "dark";
}
else
{
    echo "light";
} ?>">
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
dashboard\primary\sidebar($role);
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
                        <h4 class="page-title">Manage Accounts</h4>
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
					<?php if($timeleft) { ?>
					<div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>
					<?php } ?>
					<button data-toggle="modal" type="button" data-target="#create-account" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Account</button>
							<br>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=uJ0Umy_C6Fg" target="tutorial">https://youtube.com/watch?v=uJ0Umy_C6Fg</a> You may also join Discord and ask for help!
                                        </div>
<div id="create-account" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Accounts</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Account Role: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="If reseller, account will be able to create as many keys as their balance allows. Balance can be given manually by you by editing account after it's made, or your reseller can purchase their balance. If manager, the account is locked to that certain application and can do just about everything except rename, pause, refresh secret, and delete application."></i></label>
                                                        <select name="role" class="form-control"><option value="Reseller">Reseller</option><option value="Manager">Manager</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Account Application:</label>
                                                        <select name="app" class="form-control"><?php
$username = $_SESSION['username'];
($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '$username'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option>" . $row["name"] . "</option>";
    }
}

?></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Account Username:</label>
                                                        <input type="text" class="form-control" placeholder="Username for account you manage" name="username" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Account Email:</label>
                                                        <input type="text" class="form-control" placeholder="Email for account you manage" name="email" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Account Password:</label>
                                                        <input type="password" class="form-control" placeholder="Password for account you manage" name="pw" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Allowed License Levels:</label>
                                                        <input type="text" class="form-control" placeholder="Enter Levels In Format: 1|2|3, Leave It Blank For All Levels" name="keylevels">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="createacc">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                    <?php

if (isset($_POST['createacc']))
{
    $role = misc\etc\sanitize($_POST['role']);

    $app = misc\etc\sanitize($_POST['app']);
    $username = misc\etc\sanitize($_POST['username']);
    $email = misc\etc\sanitize($_POST['email']);
    $password = misc\etc\sanitize($_POST['pw']);
    $keylevels = misc\etc\sanitize($_POST['keylevels']) ?? "N/A";


    $pass_encrypted = password_hash($password, PASSWORD_BCRYPT);
    $owner = $_SESSION['username'];

    $user_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `username` = '$username'") or die(mysqli_error($link));
    $do_user_check = mysqli_num_rows($user_check);

    if ($do_user_check > 0)
    {
        dashboard\primary\error("Username already taken!");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }
    $email_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
    $do_email_check = mysqli_num_rows($email_check);
    if ($do_email_check > 0)
    {
        dashboard\primary\error("Email already taken!");
        echo '<meta http-equiv="refresh" content="2">';
        return;
    }

    mysqli_query($link, "INSERT INTO `accounts` (`username`, `email`, `password`, `ownerid`, `role`, `app`, `owner`, `img`, `balance`, `keylevels`) VALUES ('$username','$email','$pass_encrypted','','$role','$app','$owner','https://i.imgur.com/TrwYFBa.png', '0|0|0|0|0|0', '$keylevels')") or die(mysqli_error($link));
    dashboard\primary\success("Successfully created account!");
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
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="file_export" class="table table-striped table-bordered display">
                                        <thead>
                                            <tr>
<th>Username</th>
<th>Role</th>
<th>Application</th>
<th>Balance</th>
<th>Allowed Levels</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `owner` = '" . $_SESSION['username'] . "'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {

        echo "<tr>";

        echo "  <td>" . $row["username"] . "</td>";

        echo "  <td>" . $row["role"] . "</td>";

        echo "  <td>" . $row["app"] . "</td>";

        if ($row["role"] == "Manager")
        {
            echo "  <td>N/A</td>";
        }
        else
        {
            echo "  <td>" . $row["balance"] . "</td>";
        }
        echo " <td>" . $row["keylevels"] . "</td>";
                // echo "  <td>". $row["status"]. "</td>";
        echo '<td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu"><form method="post">
                                                <button class="dropdown-item" name="deleteacc" value="' . $row['username'] . '">Delete</button>
												<button class="dropdown-item" name="editacc" value="' . $row['username'] . '">Edit</button></div></td></tr></form>';

    }

}

?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Username</th>
<th>Role</th>
<th>Application</th>
<th>Balance</th>
<th>Allowed Levels</th>
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
if (isset($_POST['deleteacc']))
{
    $account = misc\etc\sanitize($_POST['deleteacc']);
    mysqli_query($link, "DELETE FROM `accounts` WHERE `owner` = '" . $_SESSION['username'] . "' AND `username` = '$account'");
    if (mysqli_affected_rows($link) > 0)
    {
        dashboard\primary\success("Username already taken!");("Account Successfully Deleted!");
        echo "<meta http-equiv='Refresh' Content='2'>";
    }
    else
    {
        dashboard\primary\error("Failed To Delete Account!");
    }
}

if (isset($_POST['editacc']))
{
    $account = misc\etc\sanitize($_POST['editacc']);

    $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$account' AND `owner` = '" . $_SESSION['username'] . "'");
    if (mysqli_num_rows($result) == 0)
    {
        dashboard\primary\error("Account not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($result);

    $keylevels = $row['keylevels'];

    $balance = $row["balance"];

    $balance = explode("|", $balance);

    $day = $balance[0];

    $week = $balance[1];

    $month = $balance[2];

    $threemonth = $balance[3];

    $sixmonth = $balance[4];

    $life = $balance[5];

    

    echo '<div id="edit-account" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit Account</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Day Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of day keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="daybalance" value="' . $day . '" required>
														<input type="hidden" name="account" value="' . $account . '">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Week Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of week keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="weekbalance" value="' . $week . '" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Month Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of month keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="monthbalance" value="' . $month . '" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Three Month Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of three-month keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="threemonthbalance" value="' . $threemonth . '" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Six Month Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of six-month keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="sixmonthbalance" value="' . $sixmonth . '" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Lifetime Balance: <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="Number of lifetime keys account can create"></i></label>
                                                        <input type="text" class="form-control" name="lifebalance" value="' . $life . '" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Allowed License Levels:</label>
                                                        <input type="text" class="form-control" name="keylevels" placeholder="Enter Levels In Format: 1|2|3, Leave It Blank For All Levels" value="' . $keylevels . '">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Email:</label>
                                                        <input type="email" class="form-control" name="email" placeholder="Enter new email">
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Password:</label>
                                                        <input type="password" class="form-control" name="pw" placeholder="Enter new password">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <button class="btn btn-danger waves-effect waves-light" name="saveacc">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>';
}

if (isset($_POST['saveacc']))
{
    $account = misc\etc\sanitize($_POST['account']);

    $day = misc\etc\sanitize($_POST['daybalance']);
    $week = misc\etc\sanitize($_POST['weekbalance']);
    $month = misc\etc\sanitize($_POST['monthbalance']);
    $threemonth = misc\etc\sanitize($_POST['threemonthbalance']);
    $sixmonth = misc\etc\sanitize($_POST['sixmonthbalance']);
    $lifetime = misc\etc\sanitize($_POST['lifebalance']);
    $keylevels = misc\etc\sanitize($_POST['keylevels']) ?? "N/A";
	
	$email = misc\etc\sanitize($_POST['email']);
	$pw = misc\etc\sanitize($_POST['pw']);
	
	if(!empty($email))
	{
		$email_check = mysqli_query($link, "SELECT `username` FROM `accounts` WHERE `email` = '$email'") or die(mysqli_error($link));
		$do_email_check = mysqli_num_rows($email_check);
		if ($do_email_check > 0)
		{
			dashboard\primary\error("Email already taken!");
			echo '<meta http-equiv="refresh" content="5">';
			return;
		}
		mysqli_query($link, "UPDATE `accounts` SET email = '$email' WHERE `username` = '$account' AND `owner` = '" . $_SESSION['username'] . "'");
	}
	if(!empty($pw))
	{	
		$pw = password_hash($pw, PASSWORD_BCRYPT);
		mysqli_query($link, "UPDATE `accounts` SET password = '$pw' WHERE `username` = '$account' AND `owner` = '" . $_SESSION['username'] . "'");
	}

    $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;

    mysqli_query($link, "UPDATE `accounts` SET balance = '$balance', keylevels = '$keylevels' WHERE `username` = '$account' AND `owner` = '" . $_SESSION['username'] . "'");

    dashboard\primary\success("Successfully Updated Settings!");
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
</html>