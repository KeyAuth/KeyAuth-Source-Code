<?php
$page = isset($_GET['page']) ? $_GET['page'] : "manage-apps";

require '../app/layout/topbar.php';

?>

<div class="flex overflow-hidden pt-16 bg-[#09090d]">
    
    <?php
    if ($_SESSION["role"] == 'Reseller'){
        include '../app/layout/reselleraside.php';
    } else{
        include '../app/layout/aside.php';
    }
    
    ?>

    <div class="hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>

    <?php
    if (str_contains($page, ".")) {
        require '../404_error.html';
    }

    ?>
    <div id="main-content" class="overflow-y-auto relative w-full h-full lg:ml-64">
        <main>
        <?php
            require __DIR__ . "/../pages/{$page}.php";
            ?>

        </main>

        <?php 
    require '../app/layout/footer.php';
    ?>
    </div>
</div>
