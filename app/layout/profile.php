<?php
$query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$_SESSION['username']]);

if ($query->num_rows > 0) {
    while ($row_ = mysqli_fetch_array($query->result)) {
        $acclogs = $row_['acclogs'];
        $expiry = $row_["expires"];
        $emailVerify = $row_["emailVerify"];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: /login');
        exit;
    }
}

?>

<div class="w-full max-w-sm  border border-gray-700 rounded-lg shadow">
    <div class="flex justify-end px-4 pt-2">
        <button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 hover:opacity-60 focus:ring-0 p-1.5" type="button">
            <span class="sr-only">Open dropdown</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
            </svg>
        </button>
        <!-- Dropdown menu -->
        <form method="post">
            <div id="dropdown" class="z-10 hidden text-base list-none bg-[#09090d] rounded-lg shadow w-44">
                <ul class="py-2" aria-labelledby="dropdownButton">
                    <li>
                        <a href="?page=account-settings" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Account Settings</a>
                    </li>
                    <li>
                        <a href="?page=account-logs" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Account
                            Logs</a>
                    </li>
                    <li>
                        <a href="?page=logout" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Log Out</a>
                    </li>
                </ul>
            </div>
        </form>
    </div>
    <div class="flex flex-col items-center pb-4">
        <img class="w-20 h-20 rounded-full" src="<?= $_SESSION["img"]; ?>" alt="profile image" />
        <?php if ($role == "seller") { ?>
            <h5 class="mb-1 text-xl font-medium bg-blue-700 stars"><?= $_SESSION["username"]; ?></h5>
        <?php } else { ?>
            <h5 class="mb-1 text-xl font-medium text-blue-700"><?= $_SESSION["username"]; ?></h5>
        <?php } ?>

        <?php
        $cssClasses = "text-transparent bg-clip-text bg-gradient-to-r to-blue-600 from-sky-400 text-xs font-black mr-2 px-1.5 py-0.5 rounded border mb-2 mt-2";

        if ($role == "seller") {
            // If the role is "seller", add these classes
            $cssClasses .= " border-blue-400";
        } elseif ($role == "developer") {
            // If the role is "developer", add these classes
            $cssClasses .= " border-white-400";
        } else {
            // For any other role, you can define a default behavior here
        }

        // Finally, output the HTML with the calculated classes
        echo "<p class=\"$cssClasses\">" . strtoupper($role) . " PLAN</p>";
        ?>

        <?php
        $display = match ($role) {
            'tester' => '<label class="text-sm text-gray-400"><b>Expires:</b> Free Forever </label>',
            'developer' => '<label class="text-sm text-gray-400"><b>Expires:</b>  <span id="expiryLabel"></span></label>',
            'seller' => '<label class="text-sm text-gray-400" id="expirationLbl"><b>Expires:</b> <span id="expiryLabel"></span></label>',
            'Reseller' => '<label class="text-sm text-gray-400"><b>Expires:</b> Owner Decides </label>',
            'Manager' => '<label class="text-sm text-gray-400"><b>Expires:</b> Owner Decides </label>',
            'default' => '<label class="text-sm text-gray-400"><b>Expires:</b> Never </label>'
        };
        echo $display;

        if ($role === 'developer' || $role === 'seller') {
            echo '<script>';
            echo 'document.getElementById("expiryLabel").textContent = convertTimestamp(' . $expiry . ');';
            echo '</script>';
        }
        ?>
    </div>
</div>
<br>
