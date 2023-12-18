<?php

include '../includes/misc/autoload.phtml';

set_exception_handler(function ($exception) {
	error_log("\n--------------------------------------------------------------\n");
	error_log($exception);
	error_log("\nRequest data:");
	error_log(print_r($_POST, true));
	error_log("\n--------------------------------------------------------------");
	http_response_code(500);
	die("Error: " . $exception->getMessage());
});

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if ($_SESSION['role'] == "Reseller") {
	die("Resellers can't access this.");
}

if ($_SESSION['role'] == "Manager") {
	die("Managers can't access this.");
}

if (!isset($_SESSION['app'])) {
	dashboard\primary\error("Application not selected");
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
	$sel = misc\mysql\query("SELECT count(1) AS allcount FROM `tokens` WHERE app = ?", [$_SESSION['app']]);
	$records = mysqli_fetch_assoc($sel->result);
	$totalRecords = $records['allcount'];

	$totalRecordwithFilter = $totalRecords;
	if (!is_null($searchValue)) { // don't double query if no search value was provided
		## Total number of record with filtering
		$sel = misc\mysql\query("SELECT count(1) AS allcount FROM `tokens` WHERE 1 AND (`token` LIKE ? OR `assigned` LIKE ? OR `status` LIKE ? OR `reason` LIKE ? ) AND app = ?", ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
		$records = mysqli_fetch_assoc($sel->result);
		$totalRecordwithFilter = $records['allcount'];
	}

	// whitelist certain column names and sort orders to prevent SQL injection
	if (!in_array($columnName, array("app", "token", "assigned", "banned", "reason", "hash", "type", "status"))) {
		die("Column name is not whitelisted.");
	}

	if (!in_array($columnSortOrder, array("desc", "asc"))) {
		die("Column sort order is not whitelisted.");
	}

	## Fetch records
	if (!is_null($searchValue)){
		$query = misc\mysql\query("SELECT * FROM `tokens` WHERE (`token` LIKE ? OR `assigned` LIKE ? OR `status` LIKE ? or `reason` LIKE ?) AND `app` = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
	} else {
		$query = misc\mysql\query("SELECT * FROM `tokens` WHERE `app` = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, [$_SESSION['app']]);
	}
	
	$data = array();
	
	while ($row = mysqli_fetch_assoc($query->result)) {

		## If only one or two keys exists then we will use custom margin to fix the bugging menu
		$banBtns = "";
		if ($row['banned']) {
			$banBtns = '<button class="btn menu-link px-3" style="font-size:0.95rem;" name="unbankey" value="' . $row['key'] . '">Unban</button>';
		} else {
			$banBtns = '<a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#ban-key" onclick="bankey(\'' . $row["key"] . '\')">Ban</a>';
		}

		$MarginManager = "";
		if ($totalRecordwithFilter < 2) {
			$MarginManager = "margin-bottom: 20px;";
		} else {
			$MarginManager = "margin-bottom: 0px;";
		}

		$data[] = array(
			"app" => $_SESSION['name'],
			"token" => $row['token'],
			"assigned" => $row['assigned'] ?? "N\A",
			"banned" => '<label class="' . ($row['banned'] == 1 ? 'text-red-700' : 'text-green-700') . '">' . ($row['banned'] == 1 ? 'banned' : 'unbanned') . '</label>',
            "reason" => is_null($row['reason']) ? 'N/A' : $row['reason'],
			"hash" => is_null($row['hash']) ? 'N/A' : $row['reason'],
			"type" => $row['type'],
			"status" => $row['status'],
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


?>