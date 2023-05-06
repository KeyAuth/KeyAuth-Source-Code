<?php
include '../includes/misc/autoload.phtml';

set_exception_handler(function ($exception) {
	error_log($exception);
	http_response_code(500);
	die("Error: " . $exception->getMessage());
});

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if ($_SESSION['role'] == "Reseller") {
	die("Resellers can't access this.");
}

if (!isset($_SESSION['app'])) {
	die("Application not selected.");
}

if (isset($_POST['draw'])) {

	// credits to https://makitweb.com/datatables-ajax-pagination-with-search-and-sort-php/

	$draw = intval($_POST['draw']);
	$row = intval($_POST['start']);
	$rowperpage = intval($_POST['length']); // Rows display per page
	$columnIndex = misc\etc\sanitize($_POST['order'][0]['column']); // Column index
	$columnName = misc\etc\sanitize($_POST['columns'][$columnIndex]['data']); // Column name
	$columnSortOrder = misc\etc\sanitize($_POST['order'][0]['dir']); // asc or desc
	$searchValue = misc\etc\sanitize($_POST['search']['value']); // Search value

	## Total number of records without filtering
	$sel = misc\mysql\query("select count(1) as allcount from `logs` where logapp = ?", [$_SESSION['app']]);
	$records = mysqli_fetch_assoc($sel->result);
	$totalRecords = $records['allcount'];

	$totalRecordwithFilter = $totalRecords;
	if (!is_null($searchValue)) { // don't double query if no search value was provided
		## Total number of record with filtering
		$sel = misc\mysql\query("select count(1) as allcount from `logs` WHERE 1  and (`logdata` like ? or `credential` like ? or `pcuser` like ? ) and logapp = ?", ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
		$records = mysqli_fetch_assoc($sel->result);
		$totalRecordwithFilter = $records['allcount'];
	}

	// whitelist certain column names and sort orders to prevent SQL injection
	if (!in_array($columnName, array("logdate", "logdata", "credential", "pcuser"))) {
		die("Column name is not whitelisted.");
	}

	if (!in_array($columnSortOrder, array("desc", "asc"))) {
		die("Column sort order is not whitelisted.");
	}

	## Fetch records
	$query = misc\mysql\query("select * from `logs` WHERE 1  and (`logdata` like ? or `credential` like ? or `pcuser` like ? ) and logapp = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
	$data = array();

	while ($row = mysqli_fetch_assoc($query->result)) {
		$data[] = array(
			"logdate" => '<div id="' . $row['id'] . '-logdate"><script>document.getElementById("' . $row['id'] . '-logdate").innerHTML=convertTimestamp(' . $row["logdate"] . ');</script></div>',
			"logdata" => $row['logdata'],
			"credential" => $row['credential'] ?? 'N/A',
			"pcuser" => $row['pcuser'] ?? 'N/A',
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
