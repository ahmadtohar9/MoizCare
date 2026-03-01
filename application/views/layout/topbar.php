<header class="h-16 bg-header dark:bg-[#1a2530] border-b border-[#e5e7eb] dark:border-[#2d3a4b] flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20">
<div class="flex items-center gap-4 flex-1">
<button onclick="toggleSidebar()" class="p-2 text-[#617589] hover:bg-[#f0f2f4] dark:hover:bg-[#2d3a4b] rounded-lg relative flex items-center justify-center transition-colors">
    <span class="material-symbols-outlined text-[24px]">menu</span>
</button>
<div class="relative w-full max-w-md hidden sm:block">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#617589] text-xl">search</span>
<input class="w-full pl-10 pr-4 py-2 bg-[#f0f2f4] dark:bg-[#2d3a4b] border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50 dark:text-white" placeholder="Search employee or document..." type="text"/>
</div>
</div>
<div class="flex items-center gap-4">
<button class="p-2 text-[#617589] hover:bg-[#f0f2f4] dark:hover:bg-[#2d3a4b] rounded-lg relative">
<span class="material-symbols-outlined text-[24px]">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-[#1a2530]"></span>
</button>
<div class="h-8 w-[1px] bg-[#e5e7eb] dark:bg-[#2d3a4b]"></div>
<div class="flex items-center gap-3 cursor-pointer group">
<div class="text-right hidden sm:block">
<p class="text-sm font-bold"><?= $this->session->userdata('full_name'); ?></p>
<p class="text-[11px] text-[#617589]"><?= ucfirst($this->session->userdata('role')); ?></p>
</div>
<div class="size-10 rounded-full border-2 border-primary/20 p-0.5">
<img class="w-full h-full rounded-full object-cover" data-alt="Profile picture" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcCFSaLrUcXFpw-sWLzJcw3hQU2JKwzMLhDqjDxdvBmJow0NvmWC2Rp2XKKE0KJyG4Rw-1dcthcXYbNWbp2FE3PWZD5gnrDMw9CX-uMd-uNfNXKKLaXxCS8LrQ5NXsaDfR6m8cENSn-0tKDhDO8UHoH5v6auDMY-6uC0uSPTWRx4oTZfl57Z9HueJJ6rLsF8qzIa8-V4XnzdWwnJtKIQrLt9i0KaPnzRDHSy0Cnt_RrmWiWe0Wk24rMXp4C0EZR5Q3C9rY-jrXf-Ut"/>
</div>
</div>
</div>
</header>
