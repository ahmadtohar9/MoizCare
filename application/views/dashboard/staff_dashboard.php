<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <span class="text-primary">Staff Portal</span>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-gray-400">Attendance Center</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Portal Kehadiran Online</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Selamat datang, <b><?= $employee->full_name ?></b>. Pastikan anda berada di area RS saat absen.</p>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="max-w-[900px] mx-auto space-y-8 mt-6">
        
        <?php if($this->session->flashdata('success')): ?>
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm animate-in fade-in zoom-in duration-300">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="text-sm font-black uppercase tracking-wide"><?= $this->session->flashdata('success') ?></p>
            </div>
        <?php endif; ?>

        <!-- Attendance Card (Main Visual) -->
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row">
            <!-- Left: Map/Visual Side -->
            <div class="w-full md:w-1/3 bg-[#f0f9ff] dark:bg-blue-900/10 flex flex-col items-center justify-center p-10 border-r border-gray-50 dark:border-gray-800">
                <div class="size-32 rounded-3xl bg-white dark:bg-gray-800 shadow-lg flex items-center justify-center mb-6 relative">
                    <span class="material-symbols-outlined !text-[64px] text-blue-600">location_on</span>
                    <div class="absolute -top-2 -right-2 size-8 bg-blue-100 rounded-full flex items-center justify-center animate-bounce">
                        <span class="material-symbols-outlined !text-[16px] text-blue-600 font-black">satellite_alt</span>
                    </div>
                </div>
                <p class="text-[10px] font-black text-blue-500/60 uppercase tracking-[0.2em] text-center">Location Status</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white text-center mt-1">RSUD Pratama Moiz Care</p>
            </div>

            <!-- Right: Clock & Form Side -->
            <div class="flex-1 p-10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Real-Time Digital Clock</p>
                <h3 id="digital-clock" class="text-6xl font-black text-[#111418] dark:text-white tracking-tighter tabular-nums mb-2">00:00:00</h3>
                <p class="text-primary font-black uppercase tracking-widest text-[11px] mb-8 opacity-60"><?= date('l, d F Y') ?></p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-8 border-t border-gray-50 dark:border-gray-800">
                    <div class="flex flex-col gap-1">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Shift Hari Ini</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?= $today_shift ? $today_shift->shift_name : '<span class="text-rose-500">Libur / Belum di-Set</span>' ?></p>
                        <?php if($today_shift): ?>
                            <p class="text-[10px] text-blue-500 font-bold"><?= date('H:i', strtotime($today_shift->start_time)) ?> - <?= date('H:i', strtotime($today_shift->end_time)) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status Absen</p>
                        <?php if($attendance): ?>
                            <span class="w-fit px-3 py-1 rounded-lg bg-green-50 text-green-600 text-[10px] font-black uppercase tracking-widest border border-green-100">SUDAH ABSEN</span>
                        <?php else: ?>
                            <span class="w-fit px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest border border-amber-100">BELUM ABSEN</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Clock In -->
            <button onclick="handlePresence('in')" class="<?= $attendance ? 'opacity-50 grayscale cursor-not-allowed' : 'hover:scale-[1.02] active:scale-95' ?> group flex items-center gap-6 p-8 bg-emerald-500 rounded-[2rem] border-4 border-emerald-400 shadow-xl shadow-emerald-500/20 text-white transition-all">
                <div class="size-16 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm group-hover:rotate-12 transition-transform">
                    <span class="material-symbols-outlined !text-[32px]">login</span>
                </div>
                <div class="text-left">
                    <h4 class="text-2xl font-black tracking-tight leading-none">Absen Masuk</h4>
                    <p class="text-xs font-bold opacity-70 mt-1 uppercase tracking-widest"><?= $attendance && $attendance->clock_in ? 'Sudah: '.date('H:i', strtotime($attendance->clock_in)) : 'Mulai Tugas Anda' ?></p>
                </div>
            </button>

            <!-- Clock Out -->
            <button onclick="handlePresence('out')" class="<?= !$attendance || $attendance->clock_out ? 'opacity-50 grayscale cursor-not-allowed' : 'hover:scale-[1.02] active:scale-95' ?> group flex items-center gap-6 p-8 bg-rose-500 rounded-[2rem] border-4 border-rose-400 shadow-xl shadow-rose-500/20 text-white transition-all">
                <div class="size-16 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm group-hover:-rotate-12 transition-transform">
                    <span class="material-symbols-outlined !text-[32px]">logout</span>
                </div>
                <div class="text-left">
                    <h4 class="text-2xl font-black tracking-tight leading-none">Absen Pulang</h4>
                    <p class="text-xs font-bold opacity-70 mt-1 uppercase tracking-widest"><?= $attendance && $attendance->clock_out ? 'Selesai: '.date('H:i', strtotime($attendance->clock_out)) : 'Selesaikan Tugas' ?></p>
                </div>
            </button>
        </div>

        <!-- Footer Help -->
        <div class="p-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-[2rem] border border-gray-100 dark:border-gray-800">
            <h5 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Butuh Bantuan?</h5>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="<?= base_url('leave/my_leave') ?>" class="flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-lg text-primary group-hover:shake transition-all">event_note</span>
                    <span class="text-xs font-bold text-gray-900 dark:text-gray-300 underline underline-offset-4 decoration-blue-500/30">Ajukan Cuti / Izin</span>
                </a>
                <a href="#" class="flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-lg text-rose-500">support_agent</span>
                    <span class="text-xs font-bold text-gray-900 dark:text-gray-300 underline underline-offset-4 decoration-rose-500/30">Hubungi IT / HRD</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const clock = document.getElementById('digital-clock');
        if (clock) {
            clock.innerText = now.toLocaleTimeString('en-US', { hour12: false });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    function handlePresence(type) {
        // Redirect to full attendance page for GPS and Camera validation
        window.location.href = '<?= base_url("attendance/log") ?>';
    }
</script>
