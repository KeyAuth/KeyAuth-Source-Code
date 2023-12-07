<nav class="flex mb-5" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="?page=manage-apps"
                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white">
                <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </svg>
                Manage Apps
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="lni lni-angle-double-right mr-2"></i>
                <label class="inline-flex items-center text-sm font-medium text-gray-400">Current App:
                    <?= $_SESSION['selectedApp']; ?> </label>
            </div>
        </li>
    </ol>
</nav>
