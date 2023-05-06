<?php $page = misc\etc\sanitize(isset($_GET['page']) ? $_GET['page'] : "manage-apps"); ?>
<!--begin::Page title-->
<div data-kt-swapper="true" data-kt-swapper-mode="prepend"
    data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
    <!--begin::Title-->
    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><?php echo ucwords($page); ?>
        <!--begin::Separator-->
        <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
    </h1>
    <!--end::Title-->
</div>
<!--end::Page title-->