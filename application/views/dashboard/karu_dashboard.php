<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <span class="text-primary font-bold">Karu Dashboard</span>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-gray-400">Unit Control Center</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight"><?= $unit_name ?> Management</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Pantau kinerja unit, kehadiran tim, dan kelola antrean approval Anda.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('schedule/roster') ?>" class="bg-[#111418] text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-800 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">calendar_month</span>
                Kelola Roster Tim
            </a>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <!-- Key Unit Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Staff Count -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="relative z-10 flex items-center gap-4">
                <div class="size-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined !text-[32px]">groups</span>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest leading-none">Total Staf Unit</p>
                    <h3 class="text-3xl font-black mt-1"><?= $total_unit_staff ?> <span class="text-xs text-gray-400">Orang</span></h3>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-2 -bottom-2 text-7xl text-gray-50 group-hover:scale-110 transition-transform">group</span>
        </div>

        <!-- Attendance Today -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="relative z-10 flex items-center gap-4">
                <div class="size-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined !text-[32px]">task_alt</span>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest leading-none">Hadir Shift Ini</p>
                    <h3 class="text-3xl font-black mt-1 text-emerald-600"><?= $unit_attendance_today ?> <span class="text-xs text-gray-400">Siap</span></h3>
                </div>
            </div>
            <div class="absolute bottom-4 right-6">
                <p class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100"><?= $total_unit_staff > 0 ? round(($unit_attendance_today/$total_unit_staff)*100) : 0 ?>% Terisi</p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-indigo-600 rounded-[2rem] p-6 text-white shadow-xl relative overflow-hidden group">
            <div class="relative z-10 flex items-center gap-4">
                <div class="size-14 rounded-2xl bg-white/20 text-white flex items-center justify-center backdrop-blur-md">
                    <span class="material-symbols-outlined !text-[32px]">pending_actions</span>
                </div>
                <div>
                    <p class="text-white/40 text-[10px] font-black uppercase tracking-widest leading-none">Antrean Approval</p>
                    <h3 class="text-3xl font-black mt-1"><?= $unit_pending_leave ?> <span class="text-xs text-white/40">Request</span></h3>
                </div>
            </div>
            <a href="<?= base_url('leave_approval') ?>" class="absolute bottom-6 right-6 flex items-center gap-1 text-[10px] font-black uppercase tracking-widest hover:underline transition-all">
                Handle Now <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        <!-- Left: Unit Roster (Calendar Style) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden min-h-[500px]">
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h4 class="font-black text-[#111418] uppercase tracking-widest text-xs">Jadwal Shift Tim</h4>
                        <p class="text-[10px] text-gray-400 font-bold">Rencana kerja minggu ini</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="p-2 rounded-xl hover:bg-gray-200 transition-all"><span class="material-symbols-outlined !text-[20px]">chevron_left</span></button>
                        <button class="px-4 py-2 bg-white rounded-xl border border-gray-200 text-[10px] font-black uppercase tracking-widest">Minggu Ini</button>
                        <button class="p-2 rounded-xl hover:bg-gray-200 transition-all"><span class="material-symbols-outlined !text-[20px]">chevron_right</span></button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-2">
                            <thead>
                                <tr>
                                    <th class="p-4 rounded-2xl bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest" width="25%">Pegawai</th>
                                    <?php for($i=0; $i<7; $i++): ?>
                                        <th class="p-4 rounded-2xl bg-gray-50 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            <?= date('D', strtotime("+$i days")) ?><br>
                                            <span class="text-gray-900"><?= date('d', strtotime("+$i days")) ?></span>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="group">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs uppercase">AT</div>
                                            <div>
                                                <p class="text-xs font-black text-gray-900 uppercase leading-none">Ahmad Tohar</p>
                                                <p class="text-[9px] text-gray-400 font-bold mt-1">Perawat Mahir</p>
                                            </div>
                                        </div>
                                    </td>
                                    <?php for($i=0; $i<7; $i++): $is_even = $i % 2 == 0; ?>
                                        <td class="p-2">
                                            <div class="group relative flex flex-col items-center justify-center h-14 rounded-2xl border-2 <?= $is_even ? 'border-blue-100 bg-blue-50/30' : 'border-indigo-100 bg-indigo-50/30' ?> cursor-pointer hover:scale-105 transition-all">
                                                <span class="text-[10px] font-black <?= $is_even ? 'text-blue-600' : 'text-indigo-600' ?> uppercase"><?= $is_even ? 'P' : 'S' ?></span>
                                                <!-- Hover Badge -->
                                                <div class="absolute -top-10 scale-0 group-hover:scale-100 transition-all bg-gray-900 text-white text-[8px] font-black px-2 py-1 rounded shadow-xl z-50 whitespace-nowrap">
                                                    <?= $is_even ? 'SHIFT PAGI' : 'SHIFT SORE' ?>
                                                </div>
                                            </div>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <!-- Placeholder for more rows -->
                                <tr>
                                    <td colspan="8" class="p-10 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest italic opacity-50">
                                        Data tim lainnya dimuat secara otomatis...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Action & Notifications -->
        <div class="space-y-6">
            <!-- Information Center -->
            <div class="bg-[#111418] rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-6 font-mono">Operations Radar</h4>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="size-2 rounded-full bg-emerald-500 mt-1 animate-pulse"></div>
                        <div>
                            <p class="text-[11px] font-black uppercase leading-tight">Handover Shift Pagi</p>
                            <p class="text-[10px] text-white/40 mt-1">Selesai tepat waktu. 0 Masalah dilaporkan.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="size-2 rounded-full bg-amber-500 mt-1"></div>
                        <div>
                            <p class="text-[11px] font-black uppercase leading-tight">3 Berkas STR Segera Expired</p>
                            <p class="text-[10px] text-white/40 mt-1">Ingatkan staf untuk memperpanjang STR sebelum masa berlaku habis.</p>
                        </div>
                    </div>
                </div>
                <!-- Mini Decoration -->
                <div class="absolute -right-10 -top-10 size-40 bg-white/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Quick Access Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <a href="<?= base_url('leave_report') ?>" class="flex flex-col items-center justify-center gap-3 p-6 bg-white border border-gray-100 rounded-[2rem] shadow-sm hover:bg-blue-50 hover:border-blue-200 transition-all group">
                    <span class="material-symbols-outlined text-blue-600 !text-[28px] group-hover:scale-110 transition-transform">summarize</span>
                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-wider">Reports</span>
                </a>
                <a href="<?= base_url('employee') ?>" class="flex flex-col items-center justify-center gap-3 p-6 bg-white border border-gray-100 rounded-[2rem] shadow-sm hover:bg-emerald-50 hover:border-emerald-200 transition-all group">
                    <span class="material-symbols-outlined text-emerald-600 !text-[28px] group-hover:scale-110 transition-transform">badge</span>
                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-wider">Staff List</span>
                </a>
            </div>
            
            <button class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                Download Rekapitulasi Unit (PDF)
            </button>
        </div>
    </div>
</div>
