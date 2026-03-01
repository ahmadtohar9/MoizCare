<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Laporan Riwayat Cuti</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Laporan Rekap Cuti</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Pantau seluruh aktivitas pengajuan cuti masuk, diterima, maupun ditolak.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('leave_report/balances') ?>" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                Cek Saldo Cuti
            </a>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <!-- Filter Section -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1 block">Dari Tanggal</label>
                <input type="date" id="f_start" class="w-full rounded-xl border-none bg-gray-50 font-bold text-xs py-3 px-4 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1 block">Sampai Tanggal</label>
                <input type="date" id="f_end" class="w-full rounded-xl border-none bg-gray-50 font-bold text-xs py-3 px-4 focus:ring-2 focus:ring-blue-500">
            </div>
            <?php if($this->session->userdata('role') === 'admin'): ?>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1 block">Unit / Departemen</label>
                <select id="f_dept" class="w-full rounded-xl border-none bg-gray-50 font-bold text-xs py-3 px-4 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Departemen</option>
                    <?php foreach($departments as $d): ?>
                        <option value="<?= $d->id ?>"><?= $d->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <button onclick="reloadTable()" class="w-full bg-blue-600 text-white rounded-xl font-black text-xs uppercase py-3 shadow-lg shadow-blue-500/20 active:scale-95 transition-all">Filter Data</button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="reportTable">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="pb-4">#</th>
                        <th class="pb-4">Pegawai</th>
                        <th class="pb-4">Jenis</th>
                        <th class="pb-4">Periode</th>
                        <th class="pb-4">Durasi</th>
                        <th class="pb-4">Status</th>
                        <th class="pb-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium text-xs text-gray-700">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let reportTable;
    $(document).ready(function() {
        reportTable = $('#reportTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('leave_report/history_json') ?>",
                "data": function(d) {
                    d.start_date = $('#f_start').val();
                    d.end_date = $('#f_end').val();
                    d.unit_id = $('#f_dept').val();
                }
            },
            "language": { "search": "Cari Cepat:" },
            "columnDefs": [{ "className": "py-5", "targets": "_all" }]
        });
    });

    function reloadTable() {
        reportTable.ajax.reload();
    }
</script>
