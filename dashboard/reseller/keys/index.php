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
            ($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = 	'$username'")) or die(mysqli_error($link));
            $row = mysqli_fetch_array($result);
        
            $role = $row['role'];
            $_SESSION['role'] = $role;
			
			$darkmode = $row['darkmode'];

            $keylevels = $row['keylevels'];

			
                            
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 4 admin, bootstrap 4, css3 dashboard, bootstrap 4 dashboard, xtreme admin bootstrap 4 dashboard, frontend, responsive bootstrap 4 admin template, material design, material dashboard bootstrap 4 dashboard template">
    <meta name="description"
        content="Xtreme is powerful and clean admin dashboard template, inpired from Google's Material Design">
    <meta name="robots" content="noindex,nofollow">
    <title>KeyAuth - Reseller Licenses</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

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
<script type='text/javascript'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
</head>

<body data-theme="<?php if($darkmode == 0){echo "dark";}else{echo"light";}?>">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin1" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin1">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
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
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-icon.png" alt="homepage"
                                class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                            <!-- dark Logo text -->
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo text -->
                            <img src="https://cdn.keyauth.uk/dashboard/assets/images/logo-light-text.png" class="light-logo"
                                alt="homepage" />
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin1">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item d-none d-md-block"><a
                                class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
                                data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav">
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark"
                                href="https://keyauth.com/discord/" target="discord"> <i
                                    class="mdi mdi-discord font-24"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://t.me/KeyAuth"
                                target="telegram"> <i class="mdi mdi-telegram font-24"></i>
                            </a>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href=""
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                                    src="<?php echo $_SESSION['img']; ?>" alt="user" class="rounded-circle"
                                    width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white mb-2">
                                    <div class=""><img src="<?php echo $_SESSION['img']; ?>" alt="user"
                                            class="img-circle" width="60"></div>
                                    <div class="ml-2">
                                        <h4 class="mb-0"><?php echo $_SESSION['username']; ?></h4>
                                        <p class=" mb-0"><?php echo $_SESSION['email']; ?></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="../../account/logs/"><i
                                        class="mdi mdi-folder-account font-18"></i> Account Logs</a>
                                <a class="dropdown-item" href="../../account/settings/"><i
                                        class="ti-settings mr-1 ml-1"></i> Account Settings</a>
                                <a class="dropdown-item" href="../../account/logout/"><i
                                        class="fa fa-power-off mr-1 ml-1"></i> Logout</a>
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

                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Reseller</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="../../reseller/keys/" aria-expanded="false"><i data-feather="key"></i><span
                                    class="hide-menu">Licenses</span></a></li>
						<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="../../reseller/users/" aria-expanded="false"><i data-feather="users"></i><span
                                    class="hide-menu">Users</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="../../reseller/balance/" aria-expanded="false"><i
                                    data-feather="credit-card"></i><span class="hide-menu">Balance</span></a></li>
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
                        <h4 class="page-title">Reseller Licenses</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->



            <!-- ============================================================== -->
            <div class="container-fluid" id="content" style="display:none;">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- File export -->
                <div class="row">
                    <div class="col-12">
                        <form method="POST">
                            <button data-toggle="modal" type="button" data-target="#create-keys"
                                class="dt-button buttons-print btn btn-primary mr-1"><i
                                    class="fas fa-plus-circle fa-sm text-white-50"></i> Create keys</button>
                        </form>
                        <br>
                        <div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a
                                href="https://youtube.com/watch?v=uJ0Umy_C6Fg"
                                target="tutorial">https://youtube.com/watch?v=uJ0Umy_C6Fg</a> You may also join Discord
                            and ask for help!
                        </div>
                        <div id="create-keys" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                            aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header d-flex align-items-center">
                                        <h4 class="modal-title">Add Licenses</h4>
                                        <button type="button" class="close ml-auto" data-dismiss="modal"
                                            aria-hidden="true">x</button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <div class="form-group">
                                                <label for="recipient-name" class="control-label">Amount:</label>
                                                <input type="number" class="form-control" name="amount"
                                                    placeholder="Default 1">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipient-name" class="control-label">Key Mask:</label>
                                                <input type="text" class="form-control"
                                                    value="XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX"
                                                    placeholder="Key Format. X is capital random char, x is lowercase"
                                                    name="mask" required>
                                            </div>

                                            <?php 
                                                    
                                                    if ($keylevels != "N/A"){

                                                        $keylevels = explode("|", $keylevels);
                                                        
                                                       

                                                        foreach ($keylevels as $levels) {
                                                           $options .= '<option>' . $levels . '</option>';
                                                        }                                                       

                                                        echo'
                                                            <div class="form-group">
                                                            <label for="recipient-name" class="control-label">Key Level:</label>
                                                            <select name="level" class="form-control">' . $options . '</select>
                                                            </div>
                                                        ';

                                                    } else{
                                                        echo'
                                                        <div class="form-group">
                                                        <label for="recipient-name" class="control-label">License Level:</label>
                                                        <input type="text" class="form-control" name="level" placeholder="Default 1">
                                                        </div>
                                                        ';
                                                    }
                                                    
                                                    
                                                    ?>


                                            <div class="form-group">
                                                <label for="recipient-name" class="control-label">License Note:</label>
                                                <input type="text" class="form-control" name="note"
                                                    placeholder="Optional, e.g. this license was for Joe">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipient-name" class="control-label">License Expiry
                                                    Duration:</label>
                                                <select name="expiry" class="form-control">
                                                    <option>1 Day</option>
                                                    <option>1 Week</option>
                                                    <option>1 Month</option>
                                                    <option>3 Month</option>
                                                    <option>6 Month</option>
                                                    <option>Lifetime</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect"
                                            data-dismiss="modal">Close</button>
                                        <button class="btn btn-danger waves-effect waves-light"
                                            name="genkeys">Add</button>
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
			mysqli_query($link, "INSERT INTO `keys` (`key`, `note`, `expires`, `status`, `level`, `genby`, `gendate`, `app`) VALUES ('$license',NULLIF('$note', ''), '$expiry','Not Used','$level','" . $_SESSION['username'] . "', '" . time() . "', '" . $_SESSION['app'] . "')");
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
                                
                                $level = sanitize($_POST['level']);
                                $note = sanitize($_POST['note']);
                                
								
								if($keylevels != "N/A" && !in_array($level,$keylevels))
								{
								error("Not Authorized To Use That Level");
								echo "<meta http-equiv='Refresh' Content='2;'>";
								return;	
								}
								
                                if(!isset($amount) || trim($amount) == '')
                                {
                                $amount = 1;
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
								
								$expiry = sanitize($_POST['expiry']);
								
								$result = mysqli_query($link, "SELECT `balance` FROM `accounts` WHERE `username` = '".$_SESSION['username']."'");
                            
                                $row = mysqli_fetch_array($result);
                            
                                $balance = $row["balance"];
                            
                                $balance = explode("|", $balance);
                            
                                $day = $balance[0];
                                $week = $balance[1];
                                $month = $balance[2];
                                $threemonth = $balance[3];
                                $sixmonth = $balance[4];
                                $lifetime = $balance[5];
                                
                                if($expiry == "1 Day")
                                {
                                    $expiry = 86400;
                                    $day = $day - $amount;
                                }
                                else if($expiry == "1 Week")
                                {
                                    $expiry = 604800;
                                    $week = $week - $amount;
                                }
                                else if($expiry == "1 Month")
                                {
                                    $expiry = 2.592e+6;
                                    $month = $month - $amount;
                                }
                                else if($expiry == "3 Month")
                                {
                                    $expiry = 7.862e+6;
                                    $threemonth = $threemonth - $amount;
                                }
                                else if($expiry == "6 Month")
                                {
                                    $expiry = 1.572e+7;
                                    $sixmonth = $sixmonth - $amount;
                                }
                                else if($expiry == "Lifetime")
                                {
                                    $expiry = 8.6391e+8;
                                    $lifetime = $lifetime - $amount;
                                }
                                else
                                {
                                echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Invalid Expiry!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>";
                            return;    
                                }
								
								if($day < 0 || $month < 0 || $week < 0 || $threemonth < 0 || $sixmonth < 0 || $lifetime < 0)
                                {
                                      echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .error({
                                message: \'Not Enough Balance!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
                            echo "<meta http-equiv='Refresh' Content='2;'>";
                            return;
                                }
                                
                                
                               $balance = $day . '|' . $week . '|' . $month . '|' . $threemonth . '|' . $sixmonth . '|' . $lifetime;
							   
                                $mask = sanitize($_POST['mask']);
								
								$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$mask'");
                                if(mysqli_num_rows($result) !== 0)
								{
									mysqli_close($link);
									error("Key already exists, try a different one!");
									echo "<meta http-equiv='Refresh' Content='2'>";
									return;
								}
								
								
								// mask instead of format
								// check if amount is over one and mask does not contain any Xs
                                if($amount > 1 && strpos($mask, 'X') === false && strpos($mask, 'x') === false)
                                {
								mysqli_close($link);
                                error("Can\'t do custom key with amount greater than one");
                                echo "<meta http-equiv='Refresh' Content='4;'>";
								return;
                                }
								
								
                                $key = license($amount,$mask,$expiry,$level,$link);
								
                                if($result)
                                {
                                mysqli_query($link, "UPDATE `accounts` SET `balance` = '$balance' WHERE `username` = '".$_SESSION['username']."'");
								
								wh_log($logwebhook, "{$username} has created {$amount} keys", $webhookun);
								
                                
                                if($amount > 1)
                                {
                                echo "<meta http-equiv='Refresh' Content='0; url=downloadbulk.php'>";
								}
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
                            
							
							

                    ?>

                        <script type="text/javascript">
                        var myLink = document.getElementById('mylink');

                        myLink.onclick = function() {


                            $(document).ready(function() {
                                $("#content").fadeOut(100);
                                $("#changeapp").fadeIn(1900);
                            });

                        }
                        </script>
                        <div id="ban-key" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                            aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header d-flex align-items-center">
                                        <h4 class="modal-title">Ban License</h4>
                                        <button type="button" class="close ml-auto" data-dismiss="modal"
                                            aria-hidden="true">�</button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <div class="form-group">
                                                <label for="recipient-name" class="control-label">Ban reason:</label>
                                                <input type="text" class="form-control" name="reason"
                                                    placeholder="Reason for ban" required>
                                                <input type="hidden" class="bankey" name="key">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect"
                                            data-dismiss="modal">Close</button>
                                        <button class="btn btn-danger waves-effect waves-light"
                                            name="bankey">Ban</button>
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
                                                <th>Generated By</th>
                                                <th>Duration</th>
                                                <th>Note</th>
                                                <th>Used By</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
		if($_SESSION['app']) {
        ($result = mysqli_query($link, "SELECT * FROM `keys` WHERE `genby` = '".$_SESSION['username']."'")) or die(mysqli_error($link));
        
		$rows = array();
        while ($r = mysqli_fetch_assoc($result))
        {
            $rows[] = $r;
        }

        foreach ($rows as $row)
        {

        $key = $row['key'];
		$badge = $row['status'] == "Not Used" ? 'badge badge-success' : 'badge badge-danger';
				?>
                                                    <tr>

                                                    <td><?php echo $key; ?></td>

                                                    <td><?php echo $row["genby"]; ?></td>
                                                    
                                                    <td><?php echo $row["expires"] / 86400 ?> Day(s)</td>
                                                    <td><?php echo $row["note"] ?? "N/A"; ?></td>
													<td><?php echo $row["usedby"]; ?></td>
                                                    <td><label class="<?php echo $badge; ?>"><?php echo $row['status']; ?></label></td>

                                            <form method="POST"><td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" name="deletekey" value="<?php echo $key; ?>">Delete</button>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ban-key" onclick="bankey('<?php echo $key; ?>')">Ban</a>
                                                <button class="dropdown-item" name="unbankey" value="<?php echo $key; ?>">Unban</button>
                                                <div class="dropdown-divider"></div>
												<?php
                                                }

                                            }

                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Key</th>
                                                <th>Generated By</th>
                                                <th>Duration</th>
                                                <th>Note</th>
												<th>Used By</th>
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
					mysqli_query($link, "DELETE FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
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
					mysqli_query($link, "UPDATE `keys` SET `hwid` = '' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
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
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					$reason = sanitize($_POST['reason']);
					
					mysqli_query($link, "UPDATE `keys` SET `banned` = '$reason', `status` = 'Banned' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					
					if($hwid != NULL)
					{
					mysqli_query($link, "INSERT INTO `bans`(`hwid`, `app`) VALUES ('$hwid','".$_SESSION['app']."')");
					}
					success("Key Successfully Banned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['unbankey']))
				{
					$key = sanitize($_POST['unbankey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key not Found!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$hwid = $row["hwid"];
					
					mysqli_query($link, "UPDATE `keys` SET `banned` = NULL, `status` = 'Used' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key'");
					mysqli_query($link, "DELETE FROM `bans` WHERE `hwid` = '$hwid' AND `app` = '".$_SESSION['app']."'");
					
					success("Key Successfully Unbanned!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['pausekey']))
				{
					$key = sanitize($_POST['pausekey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `status` = 'Used' AND `genby` = '".$_SESSION['username']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key isn\'t used!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$expires = $row['expires'];
					
					$exp = $expires - time();
					mysqli_query($link, "UPDATE `keys` SET `status` = 'Paused', `expires` = '$exp' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
					
					success("Key Successfully Paused!");
					echo "<meta http-equiv='Refresh' Content='2'>";
				}
				
				if(isset($_POST['unpausekey']))
				{
					$key = sanitize($_POST['unpausekey']);
					
					$result = mysqli_query($link, "SELECT * FROM `keys` WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `status` = 'Paused' AND `genby` = '".$_SESSION['username']."'");
					if(mysqli_num_rows($result) == 0)
					{
						mysqli_close($link);
						error("Key isn\'t paused!");
						echo "<meta http-equiv='Refresh' Content='2'>";
						return;
					}
					
					$row = mysqli_fetch_array($result);
					$expires = $row['expires'];
					
					$exp = $expires + time();
					mysqli_query($link, "UPDATE `keys` SET `status` = 'Used', `expires` = '$exp' WHERE `app` = '".$_SESSION['app']."' AND `key` = '$key' AND `genby` = '".$_SESSION['username']."'");
					
					success("Key Successfully Unpaused!");
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
                Copyright &copy; <script>
                document.write(new Date().getFullYear())
                </script> KeyAuth
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
    function bankey(key) {
        var bankey = $('.bankey');
        bankey.attr('value', key);
    }
    </script>
</body>

</html>