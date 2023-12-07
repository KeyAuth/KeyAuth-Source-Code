<?php
if (isset($_POST["shop"])) {
   dashboard\primary\error("Coming soon!");
}
?>

<nav class="fixed z-30 w-full bg-[#0f0f17] border-[#09090d]">
    <div class="py-3 px-3 lg:px-5 lg:pl-3">
        <div class="flex justify-between items-center">
            <div class="flex justify-start items-center">
                <div
                    class="hidden p-2 text-white rounded cursor-pointer lg:inline hover:opacity-60 transition duration-200 -ml-8">
                    <div class="w-6 h-6">
                    </div>
                </div>

                <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                    class="p-2 mr-2 text-white rounded cursor-pointer lg:hidden hover:opacity-60 focus:ring-0">
                    <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>

                <a href="?page-manage-apps">
                    <img src="https://cdn.keyauth.cc/v3/imgs/KeyauthBanner.png" alt="KeyAuth Icon"
                        style="max-width: 100px; height: auto;">
                </a>
            </div>


            <div class="hidden md:block">
                <a href="https://keyauth.readme.io" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 150px;">
                    <i class="lni lni-code mr-2 mt-1"></i>Documentation
                </a>

                <a href="https://github.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-github-original mr-2 mt-1"></i>Examples
                </a>

                <a href="https://youtube.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-youtube mr-2 mt-1"></i>YouTube
                </a>

                <a href="https://t.me/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-telegram-original mr-2 mt-1"></i>Telegram
                </a>

                <a href="https://twitter.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-twitter-original mr-2 mt-1"></i>Twitter
                </a>

                <a href="https://instagram.com/keyauthllc" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-instagram-original mr-2 mt-1"></i>Instagram
                </a>

                <a href="https://vaultcord.com" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-discord-alt mr-2 mt-1"></i>VaultCord
                </a>
            </div>

            <div id="dropdownDotsHorizontal"
                class="z-10 hidden bg-[#09090d] divide-y-2 divide-[#1c64f2] border-2 border-[#1c64f2] rounded-lg shadow w-44">
                <ul class="py-2 text-white text-xs" aria-labelledby="dropdownMenuIconHorizontalButton">
                    <li class="">
                        <a href="https://keyauth.readme.io" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-code mr-2 mt-1"></i>Documentation
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-github-original mr-2 mt-1"></i>Examples
                        </a>
                    </li>
                    <li>
                        <a href="https://youtube.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-youtube mr-2 mt-1"></i>YouTube
                        </a>
                    </li>
                    <li>
                        <a href="https://t.me/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-telegram-original mr-2 mt-1"></i>Telegram
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-twitter-original mr-2 mt-1"></i>Twitter
                        </a>
                    </li>
                    <li>
                        <a href="https://instagram.com/keyauthllc" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-instagram-original mr-2 mt-1"></i>Instagram
                        </a>
                    </li>
                    <li>
                        <a href="https://vaultcord.com" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-instagram-original mr-2 mt-1"></i>VaultCord
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
