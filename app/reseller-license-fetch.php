<?php
include '../includes/misc/autoload.phtml';

set_exception_handler(function ($exception) {
        error_log("\n--------------------------------------------------------------\n");
        error_log($exception);
        error_log("\nRequest data:");
        error_log(print_r($_POST, true));
        error_log("\n--------------------------------------------------------------");
        http_response_code(500);
        $errorMsg = str_replace($databaseUsername, "REDACTED", $exception->getMessage());
        die("Error: " . $errorMsg);
});

if (session_status() === PHP_SESSION_NONE) {
        session_start();
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

        // whitelist certain column names and sort orders to prevent SQL injection
        if (!in_array($columnName, array("key", "gendate", "expires", "note", "usedon", "usedby", "status"))) {
                die("Column name is not whitelisted.");
        }

        if (!in_array($columnSortOrder, array("desc", "asc"))) {
                die("Column sort order is not whitelisted.");
        }

        if (!is_null($searchValue)) {
                $query = misc\mysql\query("select * from `keys` WHERE (`key` like ? or `note` like ? or `usedby` like ? ) and app = ? and genby = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app'], $_SESSION['username']]);
        }
        else {
                $query = misc\mysql\query("select * from `keys` WHERE app = ? and genby = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, [$_SESSION['app'], $_SESSION['username']]);
        }
        
        $data = array();

        while ($row = mysqli_fetch_assoc($query->result)) {
                $data[] = array(
                        "key" => $row['key'],
                        "gendate" => '<div id="' . $row['key'] . '-gendate"><script>document.getElementById("' . $row['key'] . '-gendate").innerHTML=convertTimestamp(' . $row["gendate"] . ');</script></div>',
                        "expires" => ($row["expires"] / 86400) . ' Day(s)',
                        "note" => $row['note'] ?? 'N/A',
                        "usedon" => (!is_null($row["usedon"])) ? '<div id="' . $row['key'] . '-usedon"><script>document.getElementById("' . $row['key'] . '-usedon").innerHTML=convertTimestamp(' . $row["usedon"] . ');</script></div>' : 'N/A',
                        "usedby" => ($row["usedby"] == $row['key']) ? 'Same as key' : $row["usedby"] ?? 'N/A',
                        "status" => '<label class="' . (($row['status'] == "Not Used") ? 'badge badge-light-success' : 'badge badge-light-danger') . '">' . $row['status'] . '</label>',
                        "actions" => '<form method="POST"><td><a class="btn btn-sm btn-light btn-active-light-primary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="svg-icon svg-icon-5 m-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/></svg></span></a><div class="dropdown-menu menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4"><div class="menu-item px-3"><a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#del-key" onclick="delkey(\'' . $row["key"] . '\')">Delete</a></div><div class="menu-item px-3"><a class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#ban-key" onclick="bankey(\'' . $row["key"] . '\')">Ban</a></div><div class="menu-item px-3"><button class="btn menu-link px-3" style="font-size:0.95rem;" name="unbankey" value="' . $row['key'] . '">Unban</button></div></div></td></tr></form>',
                );
        }

        ## Response
        $response = array(
                "draw" => intval($draw),
                "aaData" => $data
        );

        die(json_encode($response));
}

die("Request not from datatables, aborted.");
