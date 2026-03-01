<!-- Page Heading & Breadcrumbs -->
<div>
<nav class="flex gap-2 text-sm font-medium text-[#617589] dark:text-[#94a3b8] mb-2">
<a class="hover:text-primary transition-colors" href="#">Home</a>
<span>/</span>
<span class="text-[#111418] dark:text-white">Dashboard Overview</span>
</nav>
<div class="flex items-end justify-between">
<div>
<h2 class="text-3xl font-black tracking-tight text-[#111418] dark:text-white">Executive Dashboard Overview</h2>
<p class="text-[#617589] dark:text-[#94a3b8]">Real-time workforce monitoring and compliance tracking.</p>
</div>
<button class="bg-primary hover:bg-primary/90 text-white px-4 py-2.5 rounded-lg flex items-center gap-2 text-sm font-bold shadow-lg shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-lg">file_download</span>
                            Export Report
                        </button>
</div>
</div>
<!-- Summary Cards Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Pegawai -->
    <div class="bg-gray-900 rounded-[2rem] p-6 text-white shadow-xl relative overflow-hidden group border border-white/5">
        <div class="relative z-10">
            <p class="text-white/40 text-[10px] font-black uppercase tracking-widest">Total Pegawai</p>
            <h3 class="text-4xl font-black mt-2"><?= number_format($total_employees) ?></h3>
            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-black bg-blue-500 text-white w-fit px-3 py-1 rounded-full uppercase tracking-tighter">
                <span class="material-symbols-outlined text-[12px]">badge</span>
                <span>Database Terpusat</span>
            </div>
        </div>
        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-white/5 group-hover:scale-110 transition-transform">group</span>
    </div>

    <!-- Hadir Hari Ini -->
    <div class="bg-white rounded-[2rem] p-6 text-[#111418] shadow-xl relative overflow-hidden group border border-gray-100">
        <div class="relative z-10">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Hadir Hari Ini</p>
            <h3 class="text-4xl font-black mt-2 text-emerald-600"><?= number_format($attendance_today) ?></h3>
            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-black bg-emerald-50 text-emerald-600 w-fit px-3 py-1 rounded-full uppercase tracking-tighter border border-emerald-100">
                <span class="material-symbols-outlined text-[12px]">check_circle</span>
                <span><?= $total_employees > 0 ? round(($attendance_today/$total_employees)*100) : 0 ?>% Terverifikasi</span>
            </div>
        </div>
        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-emerald-500/5 group-hover:scale-110 transition-transform">how_to_reg</span>
    </div>

    <!-- Berkas Expired -->
    <div class="bg-white rounded-[2rem] p-6 text-[#111418] shadow-xl relative overflow-hidden group border border-gray-100">
        <div class="relative z-10">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Berkas Expired</p>
            <h3 class="text-4xl font-black mt-2 text-rose-600"><?= $expired_docs_count ?></h3>
            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-black bg-rose-50 text-rose-600 w-fit px-3 py-1 rounded-full uppercase tracking-tighter border border-rose-100">
                <span class="material-symbols-outlined text-[12px]">assignment_late</span>
                <span>Butuh Tindakan</span>
            </div>
        </div>
        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-rose-500/5 group-hover:scale-110 transition-transform">folder_managed</span>
    </div>

    <!-- Antrean Cuti -->
    <div class="bg-indigo-600 rounded-[2rem] p-6 text-white shadow-xl relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-white/40 text-[10px] font-black uppercase tracking-widest">Antrean Cuti</p>
            <h3 class="text-4xl font-black mt-2"><?= $pending_leaves ?></h3>
            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-black bg-white/20 text-white w-fit px-3 py-1 rounded-full uppercase tracking-tighter border border-white/10 backdrop-blur-sm">
                <span class="material-symbols-outlined text-[12px]">watch_later</span>
                <span>Menunggu Approval</span>
            </div>
        </div>
        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-white/10 group-hover:scale-110 transition-transform">event_busy</span>
    </div>
</div>

<!-- 2-Column Layout Main Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mt-8">
    <!-- Left: Chart Area -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-[#1a2530] border border-[#e5e7eb] dark:border-[#2d3a4b] rounded-[2.5rem] overflow-hidden shadow-sm">
            <div class="px-8 py-6 border-b border-[#e5e7eb] dark:border-[#2d3a4b] flex items-center justify-between">
                <div>
                    <h4 class="font-black text-[#111418] dark:text-white uppercase tracking-widest text-xs">Attendance Analytics</h4>
                    <p class="text-[10px] text-gray-400 font-bold tracking-tight">Tren kehadiran 14 hari terakhir</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 italic">Live Feed</span>
                </div>
            </div>
            <div class="p-4 relative min-h-[300px]" id="attendanceChart">
                <!-- ApexCharts will render here -->
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Secondary list: Recent Activity -->
            <div class="bg-white dark:bg-[#1a2530] border border-[#e5e7eb] dark:border-[#2d3a4b] rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-[#e5e7eb] dark:border-[#2d3a4b] flex items-center justify-between bg-gray-50/50">
                    <h4 class="font-black text-[#111418] dark:text-white uppercase tracking-widest text-[10px]">Aktivitas Berkas</h4>
                    <a class="text-primary text-[9px] font-black uppercase tracking-widest hover:underline" href="<?= base_url('documents') ?>">Semua</a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-[#2d3a4b] flex-1 overflow-y-auto max-h-[300px]">
                    <?php if(empty($recent_activities)): ?>
                        <p class="p-10 text-center text-xs text-gray-400 font-bold italic">Belum ada aktivitas baru.</p>
                    <?php else: ?>
                        <?php foreach($recent_activities as $act): ?>
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-[#2d3a4b] transition-all">
                            <div class="size-10 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex shrink-0 items-center justify-center text-blue-600 shadow-sm border border-blue-100/50">
                                <span class="material-symbols-outlined !text-[20px]">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black text-gray-900 uppercase tracking-tight truncate">Upload: <span class="text-primary"><?= $act->doc_type ?></span></p>
                                <p class="text-[9px] text-[#617589] font-bold uppercase tracking-widest opacity-60 mt-0.5 truncate"><?= $act->full_name ?> • <?= date('d M H:i', strtotime($act->uploaded_at)) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dept Composition Chart -->
            <div class="bg-white dark:bg-[#1a2530] border border-[#e5e7eb] dark:border-[#2d3a4b] rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-[#e5e7eb] dark:border-[#2d3a4b] flex items-center justify-between bg-gray-50/50">
                    <h4 class="font-black text-[#111418] dark:text-white uppercase tracking-widest text-[10px]">Distribusi Pegawai</h4>
                </div>
                <div class="p-4 flex-1 flex items-center justify-center min-h-[300px]" id="deptChart">
                    <!-- ApexCharts will render here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Upcoming Document Expiries (Refined UI) -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm">
            <div class="bg-indigo-600 px-6 py-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-white text-xl">event_upcoming</span>
                <h4 class="font-black text-white text-[10px] uppercase tracking-widest">Document Alerts</h4>
            </div>
            <div class="p-6 space-y-4">
                <?php if(empty($upcoming_expiries)): ?>
                    <div class="p-6 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <span class="material-symbols-outlined text-gray-300 text-3xl">verified</span>
                        <p class="text-[10px] text-gray-400 mt-2 font-black uppercase tracking-widest">Dokumen Terkendali</p>
                    </div>
                <?php else: ?>
                    <?php foreach($upcoming_expiries as $doc): 
                        $days = (strtotime($doc->expiry_date) - strtotime(date('Y-m-d'))) / 86400;
                        $color = $days <= 7 ? 'text-rose-600 bg-rose-50 border-rose-100' : 'text-amber-600 bg-amber-50 border-amber-100';
                    ?>
                    <div class="p-4 rounded-[1.5rem] border <?= $color ?> shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-tight"><?= $doc->full_name ?></p>
                        <p class="text-[9px] opacity-70 font-bold uppercase tracking-widest mt-0.5"><?= $doc->type_name ?></p>
                        <div class="mt-3 flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest">
                            <span class="material-symbols-outlined !text-[12px]">schedule</span>
                            <span>H-<?= $days ?> Hari lagi</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="<?= base_url('documents') ?>" class="block text-center w-full py-3 text-[9px] font-black text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all uppercase tracking-[0.2em] mt-4">Full Document Center</a>
            </div>
        </div>

        <!-- Late Check-ins -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm">
            <div class="bg-amber-500 px-6 py-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-white text-xl">access_time</span>
                <h4 class="font-black text-white text-[10px] uppercase tracking-widest">Exception Logs</h4>
            </div>
            <div class="p-6 space-y-4">
                <?php if(empty($late_checkins)): ?>
                    <div class="p-6 text-center bg-gray-50 rounded-2xl">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Disiplin Terjaga</p>
                    </div>
                <?php else: ?>
                    <?php foreach($late_checkins as $late): ?>
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                        <img class="size-10 rounded-xl object-cover shadow-sm bg-white" src="<?= $late->photo ? base_url($late->photo) : 'https://ui-avatars.com/api/?name='.urlencode($late->full_name) ?>"/>
                        <div class="flex-1">
                            <p class="text-xs font-black uppercase tracking-tight line-clamp-1"><?= $late->full_name ?></p>
                            <span class="text-[9px] font-bold text-rose-500 uppercase">Terlambat: <?= date('H:i', strtotime($late->clock_in)) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <button class="w-full py-3 bg-gray-900 text-white text-[9px] font-black rounded-xl transition-all uppercase tracking-widest shadow-lg shadow-gray-200 mt-2">Broadcast Reminder</button>
            </div>
        </div>
    </div>
</div>
</div>
<?php // End main space-y-8 from header ?>
</div>

<!-- ApexCharts scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. Attendance Trend Chart (Bar) ---
    <?php
        $att_labels = [];
        $att_data = [];
        foreach($attendance_trends as $t) {
            $att_labels[] = $t['label'];
            $att_data[] = $t['count'];
        }
    ?>
    var attOptions = {
        series: [{
            name: 'Kehadiran',
            data: <?= json_encode($att_data) ?>
        }],
        chart: {
            type: 'bar',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#1173d4'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 0
        },
        xaxis: {
            categories: <?= json_encode($att_labels) ?>,
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '10px',
                    fontWeight: '700',
                    cssClass: 'apexcharts-xaxis-label',
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '10px',
                    fontWeight: '700'
                }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Pegawai"
                }
            }
        }
    };
    var attChart = new ApexCharts(document.querySelector("#attendanceChart"), attOptions);
    attChart.render();


    // --- 2. Department Composition Chart (Donut) ---
    <?php
        $dept_labels = [];
        $dept_data = [];
        foreach($dept_composition as $d) {
            $dept_labels[] = $d->name ?: 'Tidak Ada Dept';
            $dept_data[] = (int) $d->count;
        }
    ?>
    var deptOptions = {
        series: <?= json_encode($dept_data) ?>,
        labels: <?= json_encode($dept_labels) ?>,
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#1173d4', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6', '#ec4899', '#14b8a6'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: {
                            fontSize: '10px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 800,
                            color: '#6b7280'
                        },
                        value: {
                            fontSize: '24px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 900,
                            color: '#111827',
                            formatter: function (val) { return val }
                        },
                        total: {
                            show: true,
                            label: 'TOTAL',
                            fontSize: '10px',
                            fontWeight: 900,
                            color: '#9ca3af',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            colors: '#ffffff',
            width: 2
        },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '10px',
            fontFamily: 'Inter, sans-serif',
            fontWeight: 800,
            markers: {
                radius: 12,
                width: 8,
                height: 8,
            },
            itemMargin: {
                horizontal: 8,
                vertical: 4
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Orang"
                }
            }
        }
    };
    var deptChart = new ApexCharts(document.querySelector("#deptChart"), deptOptions);
    deptChart.render();
});
</script>
