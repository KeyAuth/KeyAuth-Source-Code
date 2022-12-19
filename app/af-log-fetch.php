<?php
include '../includes/connection.php';
include '../includes/misc/autoload.phtml';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] != "developer" && $_SESSION['role'] != "seller") {
    die('Only paid users can access affiliate dashboard');
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
	$searchQuery = " and (`referrer` like '%".$searchValue."%' or 
			`username` like '%".$searchValue."%' or 
			`action` like'%".$searchValue."%' ) ";
	}
	
	## Total number of records without filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `afLogs` where afCode = '".$_SESSION['afCode']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `afLogs` WHERE 1 ".$searchQuery." and afCode = '".$_SESSION['afCode']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$empQuery = "select * from `afLogs` WHERE 1 ".$searchQuery." and afCode = '".$_SESSION['afCode']."' order by `".$columnName."` ".$columnSortOrder." limit ".$row.",".$rowperpage;
	// echo $empQuery;
	$empRecords = mysqli_query($link, $empQuery);
	$data = array();
	
	while ($row = mysqli_fetch_assoc($empRecords)) {
		$data[] = array( 
			"date"=>'<div id="'.$row['id'].'-logdate"><script>document.getElementById("'.$row['id'].'-logdate").innerHTML=convertTimestamp('.$row["date"].');</script></div>',
			"referrer"=>$row['referrer'] ?? 'N/A',
			"username"=>$row['username'],
			"action"=>$row['action'],
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