<aside id="app-sidebar" class="w-64 bg-sidebar dark:bg-[#1a2530] border-r border-[#e5e7eb] dark:border-[#2d3a4b] flex flex-col fixed h-full z-40 overflow-hidden -translate-x-full md:translate-x-0 transition-transform duration-300">
<div class="p-6 border-b border-[#e5e7eb] dark:border-[#2d3a4b] flex items-center gap-3">
<div class="bg-primary p-1.5 rounded-lg text-white">
<span class="material-symbols-outlined text-2xl">clinical_notes</span>
</div>
<h1 class="font-bold text-lg tracking-tight text-sidebar-text">Moiz Care HRIS</h1>
</div>
<?php
$seg1 = $this->uri->segment(1);
$seg2 = $this->uri->segment(2);
$active_class = "bg-primary text-white shadow-sm font-bold";
$inactive_class = "text-sidebar-text hover:bg-sidebar-hover transition-colors font-medium";

$get_class = function($s1, $s2='') use ($seg1, $seg2, $active_class, $inactive_class) {
    if ($s2 === '') {
        return ($s1 === $seg1 && empty($seg2)) ? $active_class : $inactive_class;
    }
    return ($s1 === $seg1 && $s2 === $seg2) ? $active_class : $inactive_class;
};
?>
<nav class="flex-1 px-4 py-6 flex flex-col gap-2 overflow-y-auto">
    <!-- Dashboard -->
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('dashboard') ?>" href="<?= base_url('dashboard') ?>">
        <span class="material-symbols-outlined text-[22px]">dashboard</span>
        <span class="text-sm">Dashboard</span>
    </a>

    <!-- Kepegawaian -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Kepegawaian</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('employee') ?>" href="<?= base_url('employee') ?>">
            <span class="material-symbols-outlined text-[20px]">badge</span>
            <span class="text-sm">Data Pegawai</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('employee', 'positions') ?>" href="<?= base_url('employee/positions') ?>">
            <span class="material-symbols-outlined text-[20px]">work</span>
            <span class="text-sm">Jabatan</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('employee', 'departments') ?>" href="<?= base_url('employee/departments') ?>">
            <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
            <span class="text-sm">Departemen</span>
        </a>
        <?php if($this->session->userdata('role') === 'admin'): ?>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('user_approval') ?>" href="<?= base_url('user_approval') ?>">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span class="text-sm">Persetujuan Akun</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Berkas Pegawai -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Berkas Pegawai</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('documents') ?>" href="<?= base_url('documents') ?>">
            <span class="material-symbols-outlined text-[20px]">folder_shared</span>
            <span class="text-sm">Daftar Berkas</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('documents', 'types') ?>" href="<?= base_url('documents/types') ?>">
            <span class="material-symbols-outlined text-[20px]">settings_applications</span>
            <span class="text-sm">Master Jenis Dokumen</span>
        </a>
    </div>

    <!-- Absensi -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Absensi</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('attendance', 'log') ?>" href="<?= base_url('attendance/log') ?>">
            <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
            <span class="text-sm">Absen Masuk/Pulang</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('attendance', 'history') ?>" href="<?= base_url('attendance/history') ?>">
            <span class="material-symbols-outlined text-[20px]">history</span>
            <span class="text-sm">Riwayat Absensi</span>
        </a>
    </div>

    <!-- Manajemen Cuti -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Manajemen Cuti</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('leave', 'my_leave') ?>" href="<?= base_url('leave/my_leave') ?>">
            <span class="material-symbols-outlined text-[20px]">event_busy</span>
            <span class="text-sm">Cuti & Izin Saya</span>
        </a>
        <?php if(in_array($this->session->userdata('role'), ['admin', 'karu'])): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('leave_approval') ?>" href="<?= base_url('leave_approval') ?>">
                <span class="material-symbols-outlined text-[20px]">rule_folder</span>
                <span class="text-sm">Persetujuan Cuti</span>
            </a>
        <?php endif; ?>
        <?php if($this->session->userdata('role') === 'admin'): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('leave_settings', 'quotas') ?>" href="<?= base_url('leave_settings/quotas') ?>">
                <span class="material-symbols-outlined text-[20px]">rebase_edit</span>
                <span class="text-sm">Set Kuota Cuti</span>
            </a>
        <?php endif; ?>
        <?php if(in_array($this->session->userdata('role'), ['admin', 'karu'])): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('leave_report') ?>" href="<?= base_url('leave_report') ?>">
                <span class="material-symbols-outlined text-[20px]">summarize</span>
                <span class="text-sm">Laporan Rekap Cuti</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Jadwal Kerja -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Jadwal Kerja</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('self_schedule') ?>" href="<?= base_url('self_schedule') ?>">
            <span class="material-symbols-outlined text-[20px]">calendar_add_on</span>
            <span class="text-sm">Usulan Jadwal Saya</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('schedule', 'roster') ?>" href="<?= base_url('schedule/roster') ?>">
            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
            <span class="text-sm">Manajemen Roster Tim</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('shifts') ?>" href="<?= base_url('shifts') ?>">
            <span class="material-symbols-outlined text-[20px]">schedule</span>
            <span class="text-sm">Master Data Shift</span>
        </a>
    </div>

    <!-- Penggajian -->
    <div>
        <p class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider text-sidebar-muted">Penggajian</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('my_payroll') ?>" href="<?= base_url('my_payroll') ?>">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            <span class="text-sm">Slip Gaji Saya</span>
        </a>
        <?php if(in_array($this->session->userdata('role'), ['admin', 'hrd'])): ?>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('payroll') ?>" href="<?= base_url('payroll') ?>">
            <span class="material-symbols-outlined text-[20px]">request_quote</span>
            <span class="text-sm">Rekap Gaji Utama</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('payroll_setup') ?>" href="<?= base_url('payroll_setup') ?>">
            <span class="material-symbols-outlined text-[20px]">tune</span>
            <span class="text-sm">Setup Gaji Karyawan</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('payroll_components') ?>" href="<?= base_url('payroll_components') ?>">
            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            <span class="text-sm">Master Komponen Gaji</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Laporan -->
    <div class="mt-auto pt-4 border-t border-[#e5e7eb] dark:border-[#2d3a4b]">
         <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $get_class('reports') ?>" href="<?= base_url('reports') ?>">
            <span class="material-symbols-outlined text-[20px]">bar_chart</span>
            <span class="text-sm">Laporan</span>
        </a>
    </div>

    <!-- System Settings -->
    <?php if($this->session->userdata('role') === 'admin'): ?>
    <div class="mt-8 mb-4 px-3">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Pengaturan</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= $get_class('settings') ?>" href="<?= base_url('settings') ?>">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span class="text-sm font-bold">Setelan App</span>
        </a>
    </div>
    <?php endif; ?>

    <!-- Logout -->
    <div class="mt-auto mb-6 px-3">
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 transition-all font-bold" href="<?= base_url('auth/logout') ?>">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm">Keluar Sistem</span>
        </a>
    </div>
    <div class="h-8"></div> <!-- Spacer for scrolling -->
</nav>
</aside>

<!-- Mobile Sidebar Overlay (Invisible initially, fades in) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 hidden md:hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('main-content');
        const isMobile = window.innerWidth < 768; // Tailwind md breakpoint
        
        if (isMobile) {
            // Mobile toggle behavior (Overlay)
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300); 
            }
        } else {
            // Desktop toggle behavior (Push content)
            if (sidebar.classList.contains('md:translate-x-0') && !sidebar.classList.contains('md:-translate-x-full')) {
                // Hide sidebar
                sidebar.classList.remove('md:translate-x-0');
                sidebar.classList.add('md:-translate-x-full');
                mainContent.classList.remove('md:ml-64');
            } else {
                // Show sidebar
                sidebar.classList.remove('md:-translate-x-full');
                sidebar.classList.add('md:translate-x-0');
                mainContent.classList.add('md:ml-64');
            }
        }
    }

    // Auto-scroll the active menu element using scroll container's scrollTop so the main page doesn't jump
    document.addEventListener("DOMContentLoaded", function() {
        const activeLink = document.querySelector('nav a.bg-primary');
        const navContainer = document.querySelector('nav');
        if (activeLink && navContainer) {
            setTimeout(() => {
                const linkRect = activeLink.getBoundingClientRect();
                const navRect = navContainer.getBoundingClientRect();
                // Scroll the container so the active link is near the middle
                navContainer.scrollTo({
                    top: activeLink.offsetTop - (navRect.height / 2) + (linkRect.height / 2),
                    behavior: 'smooth'
                });
            }, 100);
        }
    });

    // Handle initial state on resize so layout doesn't break
    window.addEventListener('resize', () => {
        const sidebar = document.getElementById('app-sidebar');
        const mainContent = document.getElementById('main-content');
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            // Reset to default mobile state when resizing down
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            mainContent.classList.remove('md:ml-64');
        } else {
            // Reset to default desktop state when resizing up
            sidebar.classList.remove('md:-translate-x-full');
            sidebar.classList.add('md:translate-x-0');
            mainContent.classList.add('md:ml-64');
        }
    });
</script>
