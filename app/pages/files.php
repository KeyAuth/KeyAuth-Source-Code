<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 64)) {
    misc\auditLog\send("Attempted (and failed) to view files.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['addfile'])) {
    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;
    $resp = misc\upload\add($_POST['url'], $authed);
    match($resp){
        'invalid' => dashboard\primary\error("URL not valid!"),
        'no_local' => dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet!"),
        'failure' => dashboard\primary\error("Failed to add file!"),
        'success' => dashboard\primary\success("Successfully added file!"),
        'tester_file_exceed' => dashboard\primary\error("Tester plan may only upload files up to 10MB. Upgrade for larger file size!"),
        'dev_file_exceed' => dashboard\primary\error("File size limit is 50MB. Please upgrade for larger file size!"),
        'seller_file_exceed' => dashboard\primary\error("File size limit is 75MB. This is the MAX"),
        'name_too_large' => dashboard\primary\error("File name is too large. Rename it to something shorter"),
        'invalid_extension' => dashboard\primary\error("Invalid extension for given link!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['delfiles'])) {
    $resp = misc\upload\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all files!"),
        'success' => dashboard\primary\success("Successfully deleted all files!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['deletefile'])) {
    $resp = misc\upload\deleteSingular($_POST['deletefile']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all files!"),
        'success' => dashboard\primary\success("Successfully deleted all files!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['editfile'])) {
    $file = misc\etc\sanitize($_POST['editfile']);

    echo  '
    <div id="edit-file-modal" tabindex="-1" aria-hidden="true"
        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit File</h3>
                    <form class="space-y-6" method="POST">
                        <div>

                        <div class="relative mb-4">
                        <input type="text" id="url" name="url"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                            placeholder=" " autocomplete="on" required>
                        <label for="url"
                            class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">File URL:</label>
                    </div>

                        </div>
                        <div class="flex items-center mb-4">
                        <input id="authed" name="authed" type="checkbox"
                            class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            checked>
                        <label for="authed"
                            class="ml-2 text-sm font-medium text-white-900">Authenticated</label>
                    </div>

                        <button name="savefile"
                            value="' . $file . '"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save
                            Changes</button>
                        <button
                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onClick="window.location.href=window.location.href">Cancel
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Files Modal -->';
}

if (isset($_POST['savefile'])) {
    $fileid = misc\etc\sanitize($_POST['savefile']);
    $url = misc\etc\sanitize($_POST['url']);

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        dashboard\primary\error("Invalid Url!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if (strpos($url, "cdn.discordapp.com") !== false) {
        $urlParts = explode("?", $url);
        $url = $urlParts[0];
    }

    $requiredExtension = array(
        ".zip", ".pdf", ".tiff", ".png", ".exe", ".psd", ".mp3", ".mp4",
        ".jar", ".xls", ".csv", ".bmp", ".txt", ".xml", ".rar", ".jpg", 
        ".doc", ".eps", ".avi", ".mov", ".apk", ".ios", ".sys", ".dll", ".js",
        ".cpp", ".c", ".java", ".py", ".php", ".html", ".css", ".xml", ".json",
        ".sql", ".rb", ".swift", ".go", ".pl", ".bat", ".cs", ".gif", ".txt", ".efi"
      );
    
    $linkExtension = strtolower(substr($url, strrpos($url, ".")));
    
    if (!in_array($linkExtension, $requiredExtension)){
        dashboard\primary\error("Invalid extension for given link!");
        return;
    }

    if(str_contains($url, "localhost") || str_contains($url, "127.0.0.1") || str_contains($url, "file:/")) {
        dashboard\primary\error("URL can't be a local path! Must be a remote URL accessible by the open internet");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $file = file_get_contents($url);

    $filesize = strlen($file);

    if ($filesize > 10000000 && $role == "tester") {
        dashboard\primary\error("Users with tester plan may only upload files up to 10MB. Paid plans may upload up to 75MB.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    } else if ($filesize > 50000000 && ($role == "developer" || $role == "Manager")) {
        dashboard\primary\error("File size limit is 50 MB. Upgrade your account to gain a total of 75mb.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    } else if ($filesize > 75000000) {
        dashboard\primary\error("File size limit is 75 MB.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $fn = basename($url);
    $fs = misc\etc\formatBytes($filesize);

    if(strlen($fn) > 49) {
        dashboard\primary\error("File name is too large! Rename it to have a shorter name.");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $authed = misc\etc\sanitize($_POST['authed']) == NULL ? 0 : 1;

    $query = misc\mysql\query("UPDATE `files` SET `name` = ?,`size` = ?,`url` = ?, `uploaddate` = ?, `authed` = ? WHERE `app` = ? AND `id` = ?", [$fn, $fs, $url, time(), $authed, $_SESSION['app'], $fileid]);

    if ($query->affected_rows != 0) {
        misc\cache\purge('KeyAuthFile:' . ($secret ?? $_SESSION['app']) . ':' . $fileid);
        dashboard\primary\success("Successfully Updated File!");
    } else {
        dashboard\primary\error("Failed to update file");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Files</h1>
            <p class="text-xs text-gray-500">Let your users download files you upload here. <a
                    href="https://keyauth.readme.io/reference/files-1" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Alert Box -->
                    <div id="alert" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ml-3 text-sm font-medium text-yellow-500">
                            Files not working? Make sure you're using a direct download link. View our <a
                                href="https://keyauth.readme.io/reference/files-1"
                                class="font-semibold underline hover:no-underline">Documentation</a> to learn how to
                            learn more.
                        </div>
                    </div>
                    <!-- End Alert Box -->

                    <!-- Files Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-file-modal" data-modal-target="create-file-modal">
                        <i class="lni lni-upload mr-2 mt-1"></i>Upload File
                    </button>
                    <!-- End Files Functions -->

                    <br>

                    <!-- Delete Files Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-files-modal" data-modal-target="delete-all-files-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Files
                    </button>
                    <!-- End Delete Files Functions -->

                    <!-- Create New File Modal -->
                    <div id="create-file-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Upload A New File</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="url" name="url"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="url"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">File
                                                    Direct Download Link</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input checked id="authed" type="checkbox" name="authed"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500  focus:ring-2  ">
                                                <label for="authed"
                                                    class="ml-2 text-sm font-medium text-white-900 ">Authenticated</label>
                                            </div>
                                        </div>
                                        <button type="submit" name="addfile"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add
                                            File</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create A File Modal -->

                    <!-- Delete All Files Modal -->
                    <div id="delete-all-files-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            file!. <b>This can
                                                NOT be undone</b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                                        you want
                                        to
                                        delete all of your files?</h3>
                                    <form method="POST">
                                        <button name="delfiles"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-files-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Files Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_files" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Filename</th>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Size</th>
                                    <th class="px-6 py-3">Upload Date</th>
                                    <th class="px-6 py-3">Authenticated</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($_SESSION['app']){
                                    $query = misc\mysql\query("SELECT * FROM `files` WHERE `app` = ?", [$_SESSION['app']]);
                                    if ($query->num_rows > 0) {
                                        while ($row = mysqli_fetch_array($query->result)){
                                            echo "<tr>";

                                            echo "  <td>" . $row["name"] . "</td>";
                    
                                            echo "  <td>" . $row["id"] . "</td>";
                    
                                            echo "  <td>" . $row["size"] . "</td>";
                    
                                            echo "  <td><script>document.write(convertTimestamp(" . $row["uploaddate"] . "));</script></td>";
                    
                                            echo "  <td>" . (($row["authed"] ? 1 : 0) ? 'True' : 'False') . "</td>";

                                            echo '<form method="POST">
                                            <td>

                                            <div x-data="{ open: false }" class="z-0">
                                            <button x-on:click="open = true" class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4" type="button">
                                                    <span class="mr-1">Actions</span>
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"  style="margin-top:3px">
                                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                                    </svg>
                                            </button>
                                            <ul x-show="open" x-on:click.away="open = false" class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1" style="min-width:15rem">
                                                    <li>
                                                            <button name="deletefile" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                            value="' . $row["id"] . '">
                                                            Delete File
                                                            </button>
                                                    </li>
                                                    <li>
                                                            <a href="' . $row['url'] . '" target="_blank" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                            value="' . urlencode($row["username"]) . '">
                                                            Download File
                                                            </a>
                                                    </li>   
                                                    <li>
                                                            <button name="editfile" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            value="' . $row["id"] . '">
                                                            Edit File
                                                            </button>
                                                    </li>
                                                    </ul>
                                                    </div>
                                            </td>
                                            </tr>
                                            </form>
                                            ';
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <!-- Include the jQuery library -->
                    
