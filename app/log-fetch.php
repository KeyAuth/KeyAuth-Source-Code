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
	$searchQuery = " and (`logdata` like '%".$searchValue."%' or 
			`credential` like '%".$searchValue."%' or 
			`pcuser` like'%".$searchValue."%' ) ";
	}
	
	## Total number of records without filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `logs` where logapp = '".$_SESSION['app']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$sel = mysqli_query($link,"select count(1) as allcount from `logs` WHERE 1 ".$searchQuery." and logapp = '".$_SESSION['app']."'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$empQuery = "select * from `logs` WHERE 1 ".$searchQuery." and logapp = '".$_SESSION['app']."' order by `".$columnName."` ".$columnSortOrder." limit ".$row.",".$rowperpage;
	// echo $empQuery;
	$empRecords = mysqli_query($link, $empQuery);
	$data = array();
	
	while ($row = mysqli_fetch_assoc($empRecords)) {
		$data[] = array( 
			"logdate"=>'<div id="'.$row['id'].'-logdate"><script>document.getElementById("'.$row['id'].'-logdate").innerHTML=convertTimestamp('.$row["logdate"].');</script></div>',
			"logdata"=>$row['logdata'],
			"credential"=>$row['credential'] ?? 'N/A',
			"pcuser"=>$row['pcuser'] ?? 'N/A',
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