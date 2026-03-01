<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <a class="hover:text-primary" href="<?= base_url('leave_report') ?>">Laporan Cuti</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Saldo & Sisa Cuti</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Saldo & Sisa Cuti</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Rekapitulasi sisa hari jatah cuti seluruh pegawai tahun <?= date('Y') ?>.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('leave_report') ?>" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">history</span>
                Kembali ke Riwayat
            </a>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <!-- Filter Section -->
    <?php if($this->session->userdata('role') === 'admin'): ?>
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-1 block">Filter Unit / Departemen</label>
                <select id="f_dept" class="w-full rounded-xl border-none bg-gray-50 font-bold text-xs py-3 px-4 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Departemen</option>
                    <?php foreach($departments as $d): ?>
                        <option value="<?= $d->id ?>"><?= $d->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pt-5">
                <button onclick="reloadTable()" class="bg-gray-900 text-white px-8 py-3 rounded-xl font-black text-xs uppercase shadow-lg shadow-gray-300 active:scale-95 transition-all">Terapkan Filter</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="balanceTable">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="pb-4" width="5%">#</th>
                        <th class="pb-4" width="25%">Data Pegawai</th>
                        <th class="pb-4">Rekapitulasi Saldo (Sisa vs Terpakai)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium text-xs text-gray-700">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let balanceTable;
    $(document).ready(function() {
        balanceTable = $('#balanceTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('leave_report/balances_json') ?>",
                "data": function(d) {
                    d.unit_id = $('#f_dept').val();
                }
            },
            "language": { "search": "Cari Nama Pegawai:" },
            "columnDefs": [{ "className": "py-6", "targets": "_all" }]
        });
    });

    function reloadTable() {
        balanceTable.ajax.reload();
    }
</script>
