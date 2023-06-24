<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager") {
    die('Managers not allowed to view audit logs');
}
if (!isset($_SESSION['app'])) {
    die("Application not selected.");
}
?>
    <!-- Include the jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
    $('div.modal-content').css('border', '2px solid #1b8adb');
    });
    </script>
<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
        <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>

        <table id="kt_datatable_webhooks" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
                <thead>
                        <tr class="fw-bolder fs-6 text-gray-800 px-7">
                                <th>User</th>
                                <th>Event</th>
                                <th>Time</th>
                        </tr>
                </thead>

                <tbody>
                        <?php
            if ($_SESSION['app']) {
                $query = misc\mysql\query("SELECT * FROM `auditLog` WHERE `app` = ?", [$_SESSION['app']]);
                if ($query->num_rows > 0) {
                    while ($row = mysqli_fetch_array($query->result)) {

                        echo "<tr>";

                        echo "  <td>" . $row["user"] . "</td>";

                        echo "  <td>" . $row["event"] . "</td>";

                        echo "  <td><script>document.write(convertTimestamp(" . $row["time"] . "));</script></td>";

                        echo "</tr>";
                    }
                }
            }

            ?>
                </tbody>

        </table>

</div>
<!--end::Container-->