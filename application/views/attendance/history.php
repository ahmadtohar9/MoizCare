<!-- Page Breadcrumbs & Header -->
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Riwayat Absensi</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Laporan Kehadiran</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Data riwayat absensi seluruh pegawai.</p>
        </div>
        
        <!-- Filters & Actions -->
        <div class="flex flex-wrap items-end gap-3 bg-white dark:bg-gray-800 p-4 rounded-[1.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                <input type="date" id="start_date" class="rounded-xl border-none bg-gray-50 font-bold text-xs py-2.5 px-4 focus:ring-2 focus:ring-blue-500" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                <input type="date" id="end_date" class="rounded-xl border-none bg-gray-50 font-bold text-xs py-2.5 px-4 focus:ring-2 focus:ring-blue-500" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="flex flex-col gap-1.5 min-w-[200px]">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Filter Departemen</label>
                <select id="department_id" class="rounded-xl border-none bg-gray-50 font-bold text-xs py-2.5 px-4 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Departemen</option>
                    <?php foreach($departments as $dept): ?>
                        <option value="<?= $dept->id ?>"><?= $dept->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button onclick="refreshTable()" class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-all active:scale-95" title="Filter / Refresh">
                <span class="material-symbols-outlined">filter_list</span>
            </button>
            <div class="h-10 w-px bg-gray-100 mx-1"></div>
            <button onclick="exportPDF()" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg active:scale-95">
                <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                Cetak PDF
            </button>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-[#e5e7eb] dark:border-[#2d3748] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="attendanceTable">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-[#e5e7eb] dark:border-[#2d3748]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest" width="5%">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Pegawai</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jam Masuk</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jam Pulang</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-[#f59e0b]">Terlambat</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-blue-500">Lembur</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2d3748]">
                <!-- AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
    let table;
    $(document).ready(function() {
        table = $('#attendanceTable').DataTable({
            "ajax": {
                "url": "<?= base_url('attendance/get_history_json') ?>",
                "data": function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.department_id = $('#department_id').val();
                }
            },
            "responsive": true,
            "autoWidth": false,
            "order": [[ 2, "desc" ]], // Sort by Date
            "language": { "search": "Cari Log:", "emptyTable": "Belum ada data absensi" }
        });
    });

    function refreshTable() {
        table.ajax.reload();
    }

    function exportPDF() {
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        const dept = $('#department_id').val();
        window.open(`<?= base_url('attendance/export_pdf') ?>?start_date=${start}&end_date=${end}&department_id=${dept}`, '_blank');
    }
</script>
