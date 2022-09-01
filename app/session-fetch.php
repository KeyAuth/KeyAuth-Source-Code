<?php
include '../includes/connection.php';
include '../includes/misc/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SESSION['role'] == "Reseller") {
	die("Resellers can't access this.");
}

if(!isset($_SESSION['app'])) {
	die("Application not selected.");
}

if(isset($_POST['draw']) ) {
	
	// credits to https://makitweb.com/datatables-ajax-pagination-with-search-and-sort-php/
	
	$draw = misc\etc\sanitize($_POST['draw']);
	$row = misc\etc\sanitize($_POST['start']);
	$rowperpage = misc\etc\sanitize($_POST['length']); // Rows display per page
	$columnIndex = misc\etc\sanitize($_POST['order'][0]['column']); // Column index
	$columnName = misc\etc\sanitize($_POST['columns'][$columnIndex]['data']); // Column name
	$columnSortOrder = misc\etc\sanitize($_POST['order'][0]['dir']); // asc or desc
	$searchValue = misc\etc\sanitize($_POST['search']['value']); // Search value
	
	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
	$searchQuery = " and (`id` like '%".$searchValue."%' or 
			`credential` like '%".$searchValue."%' or 
			`ip` like'%".$searchValue."%' ) ";
	}
	
	## Total number of records without filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `sessions` where app = '".$_SESSION['app']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `sessions` WHERE 1 ".$searchQuery." and app = '".$_SESSION['app']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$empQuery = "select * from `sessions` WHERE 1 ".$searchQuery." and app = '".$_SESSION['app']."' order by `".$columnName."` ".$columnSortOrder." limit ".$row.",".$rowperpage;
	// echo $empQuery;
	$empRecords = mysqli_query($link, $empQuery);
	$data = array();
	
	while ($row = mysqli_fetch_assoc($empRecords)) {
		$data[] = array( 
			"id"=>$row['id'],
			"credential"=>$row["credential"] ?? 'N/A',
			"expiry"=>'<div id="'.$row['id'].'-expiry"><script>document.getElementById("'.$row['id'].'-expiry").innerHTML=convertTimestamp('.$row["expiry"].');</script></div>',
			"validated"=>($row['validated'] ? 1 : 0) ? 'true' : 'false',
			"ip"=>$row['ip'] ?? 'N/A',
			"actions"=>'<form method="POST"><td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="svg-icon svg-icon-5 m-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/></svg></span></a><div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4"><div class="menu-item px-3"><button class="btn menu-link px-3" style="font-size:0.95rem;" name="kill" value="'.$row["id"].'">Kill</button></div></div></td></tr></form>',
		);
	}
	
	## Response
	$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $totalRecords,
	"iTotalDisplayRecords" => $totalRecordwithFilter,
	"aaData" => $data
	);
	
	die(json_encode($response));

}

die("Request not from datatables, aborted.");