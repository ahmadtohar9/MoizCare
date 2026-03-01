<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Set Kuota Cuti</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Manajemen Kuota Pegawai</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Atur jatah cuti tahunan per pegawai secara individu.</p>
        </div>
        
        <!-- Filter Bar -->
        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Filter Unit / Departemen</label>
                <select id="filter_dept" class="rounded-xl border-none bg-gray-50 font-bold text-xs py-2 px-4 focus:ring-2 focus:ring-blue-500 min-w-[200px]">
                    <option value="">Semua Departemen</option>
                    <?php foreach($departments as $dept): ?>
                        <option value="<?= $dept->id ?>"><?= $dept->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-8 mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="quotaTable">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="pb-4">No</th>
                        <th class="pb-4">Pegawai</th>
                        <?php foreach($leave_types as $lt): ?>
                            <th class="pb-4 text-center"><?= $lt->name ?></th>
                        <?php endforeach; ?>
                        <th class="hidden">Unit ID</th> <!-- Hidden column for filtering -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $no=1; foreach($employees as $e): ?>
                        <tr data-employee-id="<?= $e->id ?>">
                            <td class="py-5 text-xs font-bold"><?= $no++ ?></td>
                            <td class="py-5">
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 text-sm"><?= $e->full_name ?></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none"><?= $e->nip ?> | <?= $e->unit_name ?></span>
                                </div>
                            </td>
                            <?php foreach($leave_types as $lt): ?>
                                <td class="py-5 px-4">
                                    <div class="flex flex-col items-center gap-1">
                                        <input type="number" 
                                               onchange="updateQuota(<?= $e->id ?>, <?= $lt->id ?>, this.value)" 
                                               class="w-16 text-center border-none bg-gray-50 rounded-xl font-black text-xs py-2 focus:ring-2 focus:ring-blue-500 quota-input" 
                                               data-type="<?= $lt->id ?>"
                                               placeholder="0">
                                        <span class="text-[8px] font-black text-gray-300 uppercase letter-spacing-widest">HARI</span>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                            <td class="hidden"><?= $e->unit_id ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let table;
    $(document).ready(function() {
        // 1. Initialize DataTable
        table = $('#quotaTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 25,
            "columnDefs": [
                { "orderable": false, "targets": [0, 2, 3, 4, 5] } // Non-sortable inputs
            ],
            "language": { "search": "Cari Pegawai:", "emptyTable": "Belum ada data pegawai" }
        });

        // 2. Custom Filtering Logic for Department
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let selectedDept = $('#filter_dept').val();
                let tableDeptId = data[6]; // Index for Unit ID hidden column

                if (selectedDept === "" || selectedDept === tableDeptId) {
                    return true;
                }
                return false;
            }
        );

        $('#filter_dept').on('change', function() {
            table.draw();
        });

        // 3. Initial Load of Quotas (Optimized after table init)
        loadAllQuotas();
    });

    function loadAllQuotas() {
        <?php foreach($employees as $e): ?>
            $.get('<?= base_url('leave_settings/get_employee_quotas/'.$e->id) ?>', function(data) {
                let quotas = JSON.parse(data);
                quotas.forEach(q => {
                    $(`tr[data-employee-id="${q.employee_id}"] input[data-type="${q.leave_type_id}"]`).val(q.total_quota);
                });
            });
        <?php endforeach; ?>
    }

    function updateQuota(empId, typeId, total) {
        $.ajax({
            url: '<?= base_url('leave_settings/save_quota') ?>',
            type: 'POST',
            data: { employee_id: empId, leave_type_id: typeId, total_quota: total },
            dataType: 'json',
            success: function(res) {
                console.log('Quota updated');
                // Could add a small green success indicator here
             }
        });
    }
</script>
