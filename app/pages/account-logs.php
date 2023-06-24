<!--begin::Container-->
<div id="kt_content_container" class="container-xxl">
    <script src="https://cdn.keyauth.cc/dashboard/unixtolocal.js"></script>

    <table id="kt_datatable_account_logs" class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
            <tr class="fw-bolder fs-6 text-gray-800 px-7">
                <th>Date</th>
                <th>IP Address</th>
                <th>User Agent</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = misc\mysql\query("SELECT * FROM `acclogs` WHERE `username` = ?", [$_SESSION['username']]);
            $rows = array();
            while ($r = mysqli_fetch_assoc($query->result)) 
            {
                $rows[] = $r;
            }

            foreach ($rows as $row) 
            {
            ?>

                <tr>
                    <td>
                        <script>
                            document.write(convertTimestamp(<?php echo $row["date"]; ?>));
                        </script>
                    </td>

                    <td><?php echo $row["ip"]; ?></td>

                    <td><?php echo $row["useragent"]; ?></td>
                </tr>
            <?php
            }

            ?>
        </tbody>
    </table>
</div>
<!--end::Container-->