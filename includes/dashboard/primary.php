<?php
namespace dashboard\primary;

use misc\etc;
function head()
{
	global $link;
    if (session_status() === PHP_SESSION_NONE)
    {
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
    if (!is_null($banned) || $_SESSION['logindate'] < $lastreset || mysqli_num_rows($result) === 0)
    {
        echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>";
        session_destroy();
        exit();
    }
    $role = $row['role'];
    $_SESSION['role'] = $role;

    $expires = $row['expires'];
    if (in_array($role, array(
        "developer",
        "seller"
    )))
    {
        $_SESSION['timeleft'] = expireCheck($username, $expires);
    }

    if ($role == "Reseller")
    {
        die('Resellers Not Allowed Here');
    }

    $darkmode = $row['darkmode'];

    $list[] = $row['format'];
    $list[] = $row['amount'];
    $list[] = $row['lvl'];
    $list[] = $row['note'];
    $list[] = $row['duration'];
	
	$_SESSION['licensePresave'] = $list;

?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KeyAuth - Dashboard</title>
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
        $selectOption = etc\sanitize($_POST['taskOption']);
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
   <?php
}
function time2str($date)
{
    $now = time();
    $diff = $now - $date;
    if ($diff < 60)
    {
        return sprintf($diff > 1 ? '%s seconds' : 'second', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 60)
    {
        return sprintf($diff > 1 ? '%s minutes' : 'minute', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 24)
    {
        return sprintf($diff > 1 ? '%s hours' : 'hour', $diff);
    }
    $diff = floor($diff / 24);
    if ($diff < 7)
    {
        return sprintf($diff > 1 ? '%s days' : 'day', $diff);
    }
    if ($diff < 30)
    {
        $diff = floor($diff / 7);
        return sprintf($diff > 1 ? '%s weeks' : 'week', $diff);
    }
    $diff = floor($diff / 30);
    if ($diff < 12)
    {
        return sprintf($diff > 1 ? '%s months' : 'month', $diff);
    }
    $diff = date('Y', $now) - date('Y', $date);
    return sprintf($diff > 1 ? '%s years' : 'year', $diff);
}
function expireCheck($username, $expires)
{
    global $link;
    if ($expires < time())
    {
        $_SESSION['role'] = "tester";
        mysqli_query($link, "UPDATE `accounts` SET `role` = 'tester' WHERE `username` = '$username'");
    }
    if ($expires - time() < 2629743) // account expires in month
    
    {
        return true;
    }
    else
    {
        return false;
    }
}
function wh_log($webhook_url, $msg, $un)
{
    $timestamp = date("c", strtotime("now"));
    $json_data = json_encode([
    // Message
    "content" => $msg,
    // Username
    "username" => "$un", ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
}
function heador()
{
    global $link; // needed to refrence active MySQL connection
    $role = $_SESSION['role'];
    if ($role != "Manager")
    {
		?>
		<div class="card"> <div class="card-body">
        <form class="text-left" method="POST">

        <div class="form-group row">

                                        <label for="example-tel-input" class="col-2 col-form-label">Application selected: </label>

                                        <div class="col-10">

                                            <label class="form-control" style="height:auto;"><?php echo $_SESSION['name']; ?></label>

                                      </div>

                                    </div>



                                    <div class="form-group row">

                                        <label for="example-tel-input" class="col-2 col-form-label">Application secret: </label>

                                        <div class="col-10">

                                            <label class="form-control" style="height:auto;">

                                            <div class="secret"><?php echo $_SESSION['secret']; ?></div>

                                        </label>

                                    </div>

                                    </div>

        

        <button data-toggle="modal" type="button" id="mylink" class="dt-button buttons-print btn btn-primary mr-1"> <i class="fas fa-plus-circle fa-sm text-white-50"></i> Change / Create Application</button>

        <button data-toggle="modal" type="button" data-target="#rename-app" class="dt-button buttons-print btn btn-info mr-1"> <i class="fas fa-edit fa-sm text-white-50"></i> Rename Application</button>
		<?php
        ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
        $row = mysqli_fetch_array($result);
        if (!$row['paused'])
        {
			?>
            <button name="pauseapp" class="dt-button buttons-print btn btn-warning mr-1" onclick="return confirm('Are you sure you want to pause app & all users?')"> <i class="fas fa-clock fa-sm text-white-50"></i> Pause App & Users</button>
			<?php
		}
        else
        {
            ?>
			<button name="unpauseapp" class="dt-button buttons-print btn btn-warning mr-1" onclick="return confirm('Are you sure you want to unpause app & all users?')"> <i class="fas fa-clock fa-sm text-white-50"></i> Unpause App & Users</button>
			<?php
        }
        ?>

        <button name="refreshapp" class="dt-button buttons-print btn btn-success mr-1" onclick="return confirm('Are you sure you want to reset application secret?')"> <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh Application Secret</button>

        <button name="deleteapp" class="dt-button buttons-print btn btn-danger mr-1" onclick="return confirm('Are you sure you want to delete application?')"> <i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete Application</button>

        </form>
		
		</div></div>
		<?php
        if (isset($_POST['deleteapp']))
        {
            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Delete Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $app = $_SESSION['app'];
            $owner = $_SESSION['username'];
            mysqli_query($link, "DELETE FROM `files` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete files
            mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '$app'") or die(mysqli_error($link)); // delete keys
            mysqli_query($link, "DELETE FROM `logs` WHERE `logapp` = '$app'") or die(mysqli_error($link)); // delete logs
            mysqli_query($link, "DELETE FROM `apps` WHERE `secret` = '$app'") or die(mysqli_error($link));
            if (mysqli_affected_rows($link) != 0)
            {
                $_SESSION['app'] = NULL;
                success("Successfully deleted App!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
            else
            {
                error("Application Deletion Failed!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
        }
        if (isset($_POST['pauseapp']))
        {
            if ($role == "Manager")
            {
                error("Manager accounts aren\'t allowed To pause applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `expiry` > '" . time() . "'");
            while ($row = mysqli_fetch_array($result))
            {
                $expires = $row['expiry'];
                $exp = $expires - time();
                mysqli_query($link, "UPDATE `subs` SET `paused` = 1, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");
            }
            mysqli_query($link, "UPDATE `apps` SET `paused` = 1 WHERE `secret` = '" . $_SESSION['app'] . "'");
            success("Paused application and any active subscriptions!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }
        if (isset($_POST['unpauseapp']))
        {
            if ($role == "Manager")
            {
                error("Manager accounts aren\'t allowed To unpause applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $result = mysqli_query($link, "SELECT * FROM `subs` WHERE `app` = '" . $_SESSION['app'] . "' AND `paused` = 1");
            while ($row = mysqli_fetch_array($result))
            {
                $expires = $row['expiry'];
                $exp = $expires + time();
                mysqli_query($link, "UPDATE `subs` SET `paused` = 0, `expiry` = '$exp' WHERE `app` = '" . $_SESSION['app'] . "' AND `id` = '" . $row['id'] . "'");
            }
            mysqli_query($link, "UPDATE `apps` SET `paused` = 0 WHERE `secret` = '" . $_SESSION['app'] . "'");
            success("Unpaused application and any paused subscriptions!");
            echo "<meta http-equiv='Refresh' Content='2'>";
        }
        if (isset($_POST['refreshapp']))
        {
            $gen = etc\generateRandomString();
            $new_secret = hash('sha256', $gen);
            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Refresh Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            mysqli_query($link, "UPDATE `bans` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `files` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `keys` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `logs` SET `logapp` = '$new_secret' WHERE `logapp` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `subs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `subscriptions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `users` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `vars` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `webhooks` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `chatmsgs` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `chats` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `sessions` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `uservars` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `chatmutes` SET `app` = '$new_secret' WHERE `app` = '" . $_SESSION['app'] . "'");
            mysqli_query($link, "UPDATE `apps` SET `secret` = '$new_secret' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");
            $_SESSION['app'] = $new_secret;
            $_SESSION['secret'] = $new_secret;
            if (mysqli_affected_rows($link) != 0)
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
        if (isset($_POST['renameapp']))
        {
            $name = etc\sanitize($_POST['name']);
            if ($role == "Manager")
            {
                error("Manager Accounts Aren\'t Allowed To Rename Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "' AND `name` = '$name'");
            $num = mysqli_num_rows($result);
            if ($num > 0)
            {
                error("You already have an application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            mysqli_query($link, "UPDATE `apps` SET `name` = '$name' WHERE `secret` = '" . $_SESSION['app'] . "' AND `owner` = '" . $_SESSION['username'] . "'");
            $_SESSION['name'] = $name;
            if (mysqli_affected_rows($link) != 0)
            {
                success("Successfully Renamed App!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
            else
            {
                error("Application Renamed Failed!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
            }
        }
        if (isset($_POST['appname']))
        {
            $appname = etc\sanitize($_POST['appname']);
            $result = mysqli_query($link, "SELECT * FROM apps WHERE name='$appname' AND owner='" . $_SESSION['username'] . "'");
            if (mysqli_num_rows($result) > 0)
            {
                mysqli_close($link);
                error("You already own application with this name!");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $owner = $_SESSION['username'];
            if ($role == "tester")
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
            if ($role == "Manager")
            {
                mysqli_close($link);
                error("Manager Accounts Are Not Allowed To Create Applications");
                echo "<meta http-equiv='Refresh' Content='2;'>";
                return;
            }
            $ownerid = $_SESSION['ownerid'];
            $clientsecret = hash('sha256', etc\generateRandomString());
            $algos = array(
                'ripemd128',
                'md5',
                'md4',
                'tiger128,4',
                'haval128,3',
                'haval128,4',
                'haval128,5'
            );
            $sellerkey = hash($algos[array_rand($algos) ], etc\generateRandomString());
            mysqli_query($link, "INSERT INTO `subscriptions` (`name`, `level`, `app`) VALUES ('default', '1', '$clientsecret')");
            mysqli_query($link, "INSERT INTO `apps`(`owner`, `name`, `secret`, `ownerid`, `enabled`, `hwidcheck`, `sellerkey`) VALUES ('" . $owner . "','" . $appname . "','" . $clientsecret . "','$ownerid', '1','1','$sellerkey')");
            if (mysqli_affected_rows($link) != 0)
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
                error("Failed to create application!");
            }
        }
    }
}
function sidebar($role)
{
    echo '<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Application</span></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/licenses/" aria-expanded="false"><i data-feather="key"></i><span class="hide-menu">Licenses</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/users/" aria-expanded="false"><i data-feather="users"></i><span class="hide-menu">Users</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/subscriptions/" aria-expanded="false"><i data-feather="bar-chart"></i><span class="hide-menu">Subscriptions</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/chats/" aria-expanded="false"><i data-feather="message-square"></i><span class="hide-menu">Chats</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/sessions/" aria-expanded="false"><i data-feather="clock"></i><span class="hide-menu">Sessions</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/webhooks/" aria-expanded="false"><i data-feather="server"></i><span class="hide-menu">Webhooks</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/files/" aria-expanded="false"><i data-feather="paperclip"></i><span class="hide-menu">Files</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/variables/" aria-expanded="false"><i data-feather="file-text"></i><span class="hide-menu">Variables</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/logs/" aria-expanded="false"><i data-feather="database"></i><span class="hide-menu">Logs</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/blacklists/" aria-expanded="false"><i data-feather="user-x"></i><span class="hide-menu">Blacklists</span></a></li>

	<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../app/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>

	<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Account</span></li>';
    if ($role == "developer" || $role == "seller")
    {
        echo '                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/manage/" aria-expanded="false"><i data-feather="sliders"></i><span class="hide-menu">Manage</span></a></li>';
    }
    echo '                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/upgrade/" aria-expanded="false"><i data-feather="activity"></i><span class="hide-menu">Upgrade</span></a></li>                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../account/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>';
    if ($role == "seller")
    {
        echo '<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Seller</span></li>
		<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../seller/settings/" aria-expanded="false"><i data-feather="settings"></i><span class="hide-menu">Settings</span></a></li>
		<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="../../seller/buttons/" aria-expanded="false"><i data-feather="x-circle"></i><span class="hide-menu">Buttons</span></a></li>';
    }
}
function error($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .error({

                                message: \'' . $msg . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}
function success($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .success({

                                message: \'' . $msg . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}

?>