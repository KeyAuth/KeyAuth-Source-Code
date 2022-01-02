<?php
ob_start();
include '../../../includes/connection.php';
include '../../../includes/misc/autoload.phtml';
include '../../../includes/dashboard/autoload.phtml';
dashboard\primary\head();

if ($_SESSION['role'] != "seller")
{
    header("Location: ../../app/licenses/");
    exit();
}
?>
   
            <!-- ============================================================== -->
            <div class="container-fluid" id="content" style="display:none;">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- File export -->
                <div class="row">
                    <div class="col-12">
					<?php dashboard\primary\heador(); ?>
					<?php if($_SESSION['timeleft']) { ?>
					<div class="alert alert-warning alert-rounded">Your account subscription expires, in less than a month, check account details for exact date.</div>
					<?php } ?>
					<form method="post">
					<button name="refreshseller" class="dt-button buttons-print btn btn-primary mr-1" onclick="return confirm('Are you sure you want to refresh Seller Key? This will make it such that previous links to manage your application will NOT work. Only refresh seller key if your current one got revealed to an unauthorized user.')"><i class="fas fa-redo-alt fa-sm text-white-50"></i> Refresh Seller Key</button></form><br>
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
($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `secret` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        $sellerkey = $row['sellerkey'];
    }
}

?>

                        <div class="card">
                            <div class="card-body">
                                <form class="form" method="post">
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Seller Key</label>
                                        <div class="col-10">
                                            <label class="form-control"><p class="secret"><?php echo $sellerkey; ?></p></label>
                                        </div>
                                    </div>
									<div class="form-group row">
                                        <label for="example-tel-input" class="col-2 col-form-label">Seller Link <i class="fas fa-question-circle fa-lg text-white-50" data-toggle="tooltip" data-placement="top" title="You can use this link with Shoppy or Sellix dynamic product type to automatically send key to customer so you never have to restock."></i></label>
                                        <div class="col-10">
                                            <label class="form-control" style="height:auto;"><?php
echo '<a href="https://'.($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST']).'/api/seller/?sellerkey=' . $sellerkey . '&type=add&expiry=1&mask=XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX&level=1&amount=1&format=text" target="_blank" class="secretlink">https://'.($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST']).'/api/seller/?sellerkey=' . $sellerkey . '&type=add&expiry=1&mask=XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX&level=1&amount=1&format=text</a>';

?></label>
                                        </div>
                                    </div>
                                    <a href="JavaScript:newPopup('https://discord.com/api/oauth2/authorize?client_id=866538681308545054&amp;permissions=268443648&amp;scope=bot');" class="btn btn-info"> <i class="fab fa-discord"></i>  Add Discord Bot</a>
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
if (isset($_POST['refreshseller']))
{
    $algos = array(
        'ripemd128',
        'md5',
        'md4',
        'tiger128,4',
        'haval128,3',
        'haval128,4',
        'haval128,5'
    );
    $sellerkey = hash($algos[array_rand($algos) ], misc\etc\generateRandomString());
    mysqli_query($link, "UPDATE `apps` SET `sellerkey` = '$sellerkey' WHERE `secret` = '" . $_SESSION['app'] . "'");
    echo '
                            <script type=\'text/javascript\'>
                            
                            const notyf = new Notyf();
                            notyf
                              .success({
                                message: \'Refreshed Seller Key!\',
                                duration: 3500,
                                dismissible: true
                              });                
                            
                            </script>
                            ';
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

<script type="text/javascript">
// Popup window code
function newPopup(url) {
	popupWindow = window.open(
		url,'popUpWindow','menubar=no,width=500,height=777,location=no,resizable=no,scrollbars=yes,status=no')
}
</script>
</body>
</html>