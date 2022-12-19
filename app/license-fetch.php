<?php
include '../includes/connection.php';
include '../includes/misc/autoload.phtml';

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

	$draw = misc\etc\sanitize($_POST['draw']);
	$row = misc\etc\sanitize($_POST['start']);
	$rowperpage = misc\etc\sanitize($_POST['length']); // Rows display per page
	$columnIndex = misc\etc\sanitize($_POST['order'][0]['column']); // Column index
	$columnName = misc\etc\sanitize($_POST['columns'][$columnIndex]['data']); // Column name
	$columnSortOrder = misc\etc\sanitize($_POST['order'][0]['dir']); // asc or desc
	$searchValue = misc\etc\sanitize($_POST['search']['value']); // Search value

	## Search 
	$searchQuery = " ";
	if ($searchValue != '') {
		$searchQuery = " and (`key` like '%" . $searchValue . "%' or 
			`note` like '%" . $searchValue . "%' or 
			`genby` like'%" . $searchValue . "%' or 
			`usedby` like'%" . $searchValue . "%' ) ";
	}

	## Total number of records without filtering
	$sel = mysqli_query($link, "select count(1) as allcount from `keys` where app = '" . $_SESSION['app'] . "'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];

	## Total number of record with filtering
	$sel = mysqli_query($link, "select count(1) as allcount from `keys` WHERE 1 " . $searchQuery . " and app = '" . $_SESSION['app'] . "'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];

	## Fetch records
	$empQuery = "select * from `keys` WHERE 1 " . $searchQuery . " and app = '" . $_SESSION['app'] . "' order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
	// echo $empQuery;
	$empRecords = mysqli_query($link, $empQuery);
	
	$data = array();

	while ($row = mysqli_fetch_assoc($empRecords)) {

		## If only one or two keys exists then we will use custom margin to fix the bugging menu
		$banBtns = "";
		if ($row['status'] == "Banned") { $banBtns = '<button class="btn menu-link px-3" style="font-size:0.95rem;" name="unbankey" value="' . $row['key'] . '">Unban</button>'; } else { $banBtns = '<a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#ban-key" onclick="bankey(\'' . $row["key"] . '\')">Ban</a>'; }

		$MarginManager = "";
		if ($totalRecordwithFilter < 2) { $MarginManager = "margin-bottom: 20px;"; } else { $MarginManager = "margin-bottom: 0px;"; }

		$data[] = array(
			"key" => $row['key'],
			"gendate" => '<div id="' . $row['key'] . '-gendate"><script>document.getElementById("' . $row['key'] . '-gendate").innerHTML=convertTimestamp(' . $row["gendate"] . ');</script></div>',
			"genby" => $row['genby'],
			"expires" => ($row["expires"] / 86400) . ' Day(s)',
			"note" => $row['note'] ?? 'N/A',
			"usedon" => (!is_null($row["usedon"])) ? '<div id="' . $row['key'] . '-usedon"><script>document.getElementById("' . $row['key'] . '-usedon").innerHTML=convertTimestamp(' . $row["usedon"] . ');</script></div>' : 'N/A',
			"usedby" => ($row["usedby"] == $row['key']) ? 'Same as key' : $row["usedby"] ?? 'N/A',
			"status" => '<label class="' . (($row['status'] == "Not Used") ? 'badge badge-light-success' : 'badge badge-light-danger') . '">' . $row['status'] . '</label>',
			"actions" => '<form method="POST" style="' . $MarginManager . '"><td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="svg-icon svg-icon-5 m-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/></svg></span></a><div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4"><div class="menu-item px-3"><a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#del-key" onclick="delkey(\'' . $row["key"] . '\')">Delete</a></div><div class="menu-item px-3">' . $banBtns . '</div><div class="menu-item px-3"><button class="btn menu-link px-3" style="font-size:0.95rem;" name="editkey" value="' . $row['key'] . '">Edit</button></div></div></td></tr></form>',
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