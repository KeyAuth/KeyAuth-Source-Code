<?php

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/connection.php';
require '../includes/misc/autoload.phtml';
require '../includes/dashboard/autoload.phtml';

$username = $_SESSION['username'];
$result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'");
$row = mysqli_fetch_array($result);

$banned = $row['banned'];
$twofactor = $row['twofactor'];

if (!is_null($banned))
{
	echo "<meta http-equiv='Refresh' Content='0; url=../login/'>";
	session_destroy();
	exit();
}

$admin = $row['admin'];

if(!$admin)
{
	?>
<html>
<head>
<title>SUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="nosnippet, nofollow, noindex" />
<style>
body{
color:white;text-align:center;padding-top:5%;font-family:Helvetica,Arial,sans-serif;
margin-top:10%;
background:#101010;
} 
.-main-text
{
font-size:13px;
white-space:pre;
display:block;
}
</style>
</head>
<body>
<span class="-main-text">⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣠⣤⣤⣤⣤⣤⣶⣦⣤⣄⡀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⢀⣴⣿⡿⠛⠉⠙⠛⠛⠛⠛⠻⢿⣿⣷⣤⡀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⣼⣿⠋⠀⠀⠀⠀⠀⠀⠀⢀⣀⣀⠈⢻⣿⣿⡄⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⣸⣿⡏⠀⠀⠀⣠⣶⣾⣿⣿⣿⠿⠿⠿⢿⣿⣿⣿⣄⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⣿⣿⠁⠀⠀⢰⣿⣿⣯⠁⠀⠀⠀⠀⠀⠀⠀⠈⠙⢿⣷⡄⠀
⠀⠀⣀⣤⣴⣶⣶⣿⡟⠀⠀⠀⢸⣿⣿⣿⣆⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⣷⠀
⠀⢰⣿⡟⠋⠉⣹⣿⡇⠀⠀⠀⠘⣿⣿⣿⣿⣷⣦⣤⣤⣤⣶⣶⣶⣶⣿⣿⣿⠀
⠀⢸⣿⡇⠀⠀⣿⣿⡇⠀⠀⠀⠀⠹⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠃⠀
⠀⣸⣿⡇⠀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠉⠻⠿⣿⣿⣿⣿⡿⠿⠿⠛⢻⣿⡇⠀⠀
⠀⣿⣿⠁⠀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⣧⠀⠀
⠀⣿⣿⠀⠀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⣿⠀⠀
⠀⣿⣿⠀⠀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⣿⠀⠀
⠀⢿⣿⡆⠀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⡇⠀⠀
⠀⠸⣿⣧⡀⠀⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⣿⠃⠀⠀
⠀⠀⠛⢿⣿⣿⣿⣿⣇⠀⠀⠀⠀⠀⣰⣿⣿⣷⣶⣶⣶⣶⠶⠀⢠⣿⣿⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⣿⣿⠀⠀⠀⠀⠀⣿⣿⡇⠀⣽⣿⡏⠁⠀⠀⢸⣿⡇⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⣿⣿⠀⠀⠀⠀⠀⣿⣿⡇⠀⢹⣿⡆⠀⠀⠀⣸⣿⠇⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⢿⣿⣦⣄⣀⣠⣴⣿⣿⠁⠀⠈⠻⣿⣿⣿⣿⡿⠏⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠈⠛⠻⠿⠿⠿⠿⠋⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</span>
</body>
<audio autoplay src='https://cdn.keyauth.uk/sus.mp3' loop preload type='audio/mp3'></audio>
</html>
<?php
die();
}

if(!$twofactor)
{
	die("Admin accounts must have 2FA enabled");
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>KeyAuth - Admin</title>
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdn.keyauth.uk/static/images/favicon.png">
	<script src="https://cdn.keyauth.uk/dashboard/assets/libs/jquery/dist/jquery.min.js"></script>
	<link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="https://cdn.keyauth.uk/dashboard/dist/css/style.min.css" rel="stylesheet">	
	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
	<script src="https://cdn.keyauth.uk/dashboard/unixtolocal.js"></script>
	
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
</head>
<body data-theme="dark">
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin1" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <header class="topbar" data-navbarbg="skin1">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <a class="navbar-brand">
                        <b class="logo-icon">
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <span class="logo-text">
                             <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-text.png" alt="homepage" class="dark-logo" />   
                             <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                        </span>
                    </a>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin1">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://keyauth.com/discord/" target="discord"> <i class="mdi mdi-discord font-24"></i>
						</a>
						</li>
						<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://t.me/KeyAuth" target="telegram"> <i class="mdi mdi-telegram font-24"></i>
						</a>
						</li>
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
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Admin</span></li>
						<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" aria-expanded="false"><i data-feather="move"></i><span class="hide-menu">Panel</span></a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Admin</h4>
                    </div>
                </div>
            </div>
            <div class="container-fluid" id="content">
                <div class="row">
                    <div class="col-12">
					<form method="POST">
					<button data-toggle="modal" type="button" data-target="#check-order" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Check Order</button>  <button data-toggle="modal" type="button" data-target="#search-email" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-envelope-open fa-sm text-white-50"></i> Search With Email</button>  <button data-toggle="modal" type="button" data-target="#search-username" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-users fa-sm text-white-50"></i> Search With Username</button>
                            </form>
							<br>
<div id="check-order" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Check Order</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Order ID:</label>
                                                        <input class="form-control" name="orderid" placeholder="Shoppy Order ID">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="checkorder">Check</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
<div id="search-email" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Search With Email</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Email:</label>
                                                        <input class="form-control" name="email" placeholder="Email">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="searchemail">Search</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
<div id="search-username" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Search With Username</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Username:</label>
                                                        <input class="form-control" name="un" placeholder="Username">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="searchusername">Search</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
                    <?php
if (isset($_POST['checkorder']))
{

    $orderid = misc\etc\sanitize($_POST['orderid']);
	$url = "https://shoppy.gg/api/v1/orders/{$orderid}";

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	$headers = array(
	"User-Agent: KeyAuth", // must set a useragent for Shoppy API, anything.
	"Authorization: {$APIkey}", // shoppy API key, variable found in includes/connection.php
	);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	//for debug only!
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	$resp = curl_exec($curl);
	curl_close($curl);
	
	$json = json_decode($resp);
	
	if($json->message == "Requested resource not found")
	{
		dashboard\primary\error("Order not found");	
	}
	else
	{
		dashboard\primary\success("Order from " . $json->email . " for $" . $json->price . " was found");
	}
}

if (isset($_POST['searchemail']))
{
	$email = misc\etc\sanitize($_POST['email']);
    header("Location:./?email=". $email);
}

if (isset($_POST['searchusername']))
{
	$un = misc\etc\sanitize($_POST['un']);
	header("Location: ./?username=" . $un);
	die();
}

?>
<div id="ban-acc" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Ban Account</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Ban reason:</label>
                                                        <input type="text" class="form-control" name="reason" placeholder="Reason for ban" required>
														<input type="hidden" class="banacc" name="acc">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="banacc">Ban</button>
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
<th>Email</th>
<th>Role</th>
<th>Ban Status</th>
<th>2FA Status</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

<?php
		$un = misc\etc\sanitize($_GET['username']);
		$email = misc\etc\sanitize($_GET['email']);
        $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$un' OR `email` = '$email'");

        $rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }

        foreach ($rows as $row)
        {

        $un = $row['username'];
		$ban = $row['banned'] == NULL ? 'False' : 'True';
		$totp = (($row['twofactor'] ? 1 : 0) ? 'True' : 'False');
?>

													<tr>

                                                    <td><?php echo $un; ?></td>
											
                                                    <td><?php echo $row["email"]; ?></td>
													
                                                    <td><?php echo $row["role"]; ?></td>
													
                                                    <td><?php echo $ban; ?></td>
													
                                                    <td><?php echo $totp; ?></td>

                                            <form method="POST"><td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-acc" onclick="banacc('<?php echo $un; ?>')">Ban</a>
                                                <button class="dropdown-item" name="unbanacc" value="<?php echo $un; ?>">Unban</button>
                                                <div class="dropdown-divider"></div>
												<button class="dropdown-item" name="editacc" value="<?php echo $un; ?>">Edit</button></div></td></tr></form>
<?php

        }

?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Ban Status</th>
<th>2FA Status</th>
<th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
    <?php
    if (isset($_POST['banacc']))
    {
        $un = misc\etc\sanitize($_POST['acc']);
        $reason = misc\etc\sanitize($_POST['reason']);

        mysqli_query($link, "UPDATE `accounts` SET `banned` = '$reason' WHERE `username` = '$un'"); // set account to banned
        mysqli_query($link, "UPDATE `apps` SET `banned` = '1' WHERE `owner` = '$un'"); // ban all apps owned by account

	mysqli_query($link, "DELETE FROM `bids` WHERE `username` = '$un'");
		
	dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has banned user `{$un}` for reason `{$reason}`", $adminwebhookun);
		
        dashboard\primary\success("Account Banned!");
    }

    if (isset($_POST['unbanacc']))
    {
        $un = misc\etc\sanitize($_POST['unbanacc']);

        mysqli_query($link, "UPDATE `accounts` SET `banned` = NULL WHERE `username` = '$un'"); // set account to not banned
        mysqli_query($link, "UPDATE `apps` SET `banned` = '0' WHERE `owner` = '$un'"); // unban all apps owned by account
		
	dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has unbanned user `{$un}`", $adminwebhookun);
		
        dashboard\primary\success("Account Unbanned!");
    }

    if (isset($_POST['editacc']))
    {
        $un = misc\etc\sanitize($_POST['editacc']);

        $result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$un'");
        $row = mysqli_fetch_array($result);
		$role = $row['role'];
		$totp = $row['twofactor'];
		?>
        <div id="edit-key" class="modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block;" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Edit Account</h4>
                                                <button type="button" onClick="window.location.href=window.location.href" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post"> 
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Email:</label>
                                                        <input type="text" class="form-control" name="email" value="<?php echo $row['email']; ?>" required>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Role:</label>
                                                        <select class="form-control" name="role">
															<option value="seller" <?=$role == 'seller' ? ' selected="selected"' : '';?>>seller</option>
															<option value="developer" <?=$role == 'developer' ? ' selected="selected"' : '';?>>developer</option>
															<option value="tester" <?=$role == 'tester' ? ' selected="selected"' : '';?>>tester</option>
														</select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">2FA Status:</label>
                                                        <select class="form-control" name="totp">
															<option value="0" <?=$totp == 0 ? ' selected="selected"' : '';?>>false</option>
															<option value="1" <?=$totp == 1 ? ' selected="selected"' : '';?>>true</option>
														</select>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onClick="window.location.href=window.location.href" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" value="<?php echo $un; ?>" name="saveacc">Save</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									<?php
    }

    if (isset($_POST['saveacc']))
    {	
        $un = misc\etc\sanitize($_POST['saveacc']);
        $email = misc\etc\sanitize($_POST['email']);
		$role = misc\etc\sanitize($_POST['role']);
		$totp = misc\etc\sanitize($_POST['totp']);
		switch($role)
		{
			case 'seller':
				$expires = time() + 31556926;
				break;
			case 'developer':
				$expires = time() + 31556926;
				break;
			case 'tester':
				$expires = NULL;
				break;
			default:
				error("Invalid role!");
				echo "<meta http-equiv='Refresh' Content='2'>";
				return;
		}

        mysqli_query($link, "UPDATE `accounts` SET `email` = '$email',`role` = '$role', `expires` = NULLIF('$expires', ''), `twofactor` = '$totp' WHERE `username` = '$un'");

	dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has updated user `{$un}` email to `{$email}`, role to `{$role}`, and 2FA status to `{$totp}`", $adminwebhookun);
		
        dashboard\primary\success("Updated Account!");
    }
?>
            </div>
            <footer class="footer text-center">
       Copyright &copy; <script>document.write(new Date().getFullYear())</script> KeyAuth
</footer>
        </div>
    </div>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/popper-js/dist/umd/popper.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app.init.dark.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/app-style-switcher.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/waves.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/sidebarmenu.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/feather.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/custom.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/d3.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/c3/c3.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/assets/libs/chart-js/dist/chart.min.js"></script>
    <script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/dashboards/dashboard1.js"></script>
	<script src="https://cdn.keyauth.uk/dashboard/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
	<script src="https://cdn.keyauth.uk/dashboard/dist/js/pages/datatable/datatable-advanced.init.js"></script>
<script>
                        
		function banacc(un) {
		 var banacc = $('.banacc');
		 banacc.attr('value', un);
      }
</script>
</body>
</html>
