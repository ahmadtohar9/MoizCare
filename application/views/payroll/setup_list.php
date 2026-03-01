<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <a class="hover:text-primary" href="<?= base_url('payroll') ?>">Kelola Gaji</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Setup Gaji Karyawan</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] text-3xl font-black leading-tight tracking-tight">Kustomisasi Gaji Pegawai</h1>
            <p class="text-gray-500 text-sm mt-1">Atur gaji pokok dan aktifkan tunjangan/potongan sesuai kebijakan masing-masing individu.</p>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6 text-blue-800 text-sm">
        <span class="font-bold">Info:</span> Pegawai yang belum disetup akan otomatis menggunakan tarif Gaji Pokok default UMR dan tidak mendapatkan tunjangan tambahan atau potongan default. Silakan atur komponen khusus per-karyawan lewat tombol <span class="font-bold border px-1 py-0.5 rounded border-blue-400">Atur Gaji</span>.
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="setupTable">
            <thead>
                <tr class="bg-gray-50 border-b border-[#e5e7eb]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Employee</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jabatan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Gaji Pokok</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Konfigurasi Tambahan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb]">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Setup -->
<div id="setupModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl w-full max-w-2xl shadow-2xl p-8 transform transition-all flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center mb-6 shrink-0 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-black text-gray-900" id="empNameTitle">Setup Gaji Budi</h3>
                    <p class="text-sm text-gray-500 font-mono mt-0.5" id="empNipTitle">NIP: -</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-xl transition-all self-start"><span class="material-symbols-outlined !text-[20px]">close</span></button>
            </div>
            
            <div class="overflow-y-auto pr-2 pb-4 -mr-2">
                <form id="setupForm" class="space-y-6">
                    <input type="hidden" name="employee_id" id="setup_employee_id">
                    
                    <!-- Basic Salary -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined !text-[16px]">payments</span> Gaji Pokok (Fixed Base)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="text" name="basic_salary" id="basic_salary" required onkeyup="formatRupiah(this)" class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-gray-900 font-mono shadow-sm">
                        </div>
                    </div>

                    <!-- Components Checkboxes -->
                    <div>
                        <h4 class="text-[11px] font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined !text-[16px]">extension</span> Custom Komponen Gaji</h4>
                        <p class="text-xs text-gray-500 mb-4">Centang komponen di bawah ini jika ingin menerapkannya khusus untuk pegawai ini. Anda juga dapat mengubah nominal default-nya.</p>
                        
                        <div class="space-y-3" id="componentsList">
                            <!-- Injected by JS -->
                        </div>

                        <!-- Ad-Hoc Components Container -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-[11px] font-black text-purple-600 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined !text-[16px]">edit_note</span> Tambahan / Potongan Dinamis (Ad-hoc)</h4>
                            <p class="text-xs text-gray-500 mb-4">Tambahkan tunjangan atau potongan baru yang hanya berlaku khusus dan eksklusif untuk pegawai ini tanpa mengotori Master Komponen.</p>
                            
                            <div class="space-y-3" id="adhocContainer">
                                <!-- Injected by JS -->
                            </div>

                            <button type="button" onclick="addAdhocRow()" class="mt-4 px-4 py-2 bg-purple-50 text-purple-600 hover:bg-purple-100 border border-purple-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 w-full justify-center">
                                <span class="material-symbols-outlined !text-[16px]">add_circle</span> Tambah Komponen Spesifik
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 shrink-0 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <div class="w-full sm:w-1/2 p-3 bg-blue-50 border border-blue-100 rounded-xl relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-blue-200/50"><span class="material-symbols-outlined !text-[80px]">calculate</span></div>
                    <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1 relative z-10">Estimasi Total Pendapatan</h5>
                    <p class="text-[10px] text-gray-500 mb-1 relative z-10">Berdasarkan data di atas (Asumsi Hadir Penuh).</p>
                    <div class="text-2xl font-black text-blue-900 tracking-tight relative z-10" id="preview_total">Rp 0</div>
                </div>

                <div class="flex gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                    <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none px-5 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                    <button type="button" onclick="saveSetup()" class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined !text-[18px]">done_all</span> Simpan Setup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Component Template (Hidden) -->
<template id="compRowTemplate">
    <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 bg-white hover:border-blue-300 transition-colors">
        <div class="pt-1">
            <input type="checkbox" name="components[]" value="{id}" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer comp-checkbox" id="chk_{id}" onchange="toggleCustomInput({id})">
        </div>
        <div class="flex-1">
            <label for="chk_{id}" class="cursor-pointer">
                <div class="font-bold text-gray-900 text-sm">{name}</div>
                <div class="flex items-center gap-2 mt-1">
                    {badge}
                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{basis}</span>
                </div>
            </label>
        </div>
        <div class="w-40 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
            <input type="text" name="custom_amount[{id}]" id="amt_{id}" value="{amount}" onkeyup="formatRupiah(this)" disabled class="w-full pl-8 pr-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-900 font-mono focus:bg-white focus:border-blue-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
        </div>
    </div>
</template>

<!-- Ad-hoc Template (Hidden) -->
<template id="adhocRowTemplate">
    <div class="flex flex-wrap md:flex-nowrap items-center gap-3 p-3 rounded-xl border border-purple-100 bg-purple-50/30 relative group">
        <button type="button" onclick="$(this).parent().remove()" class="absolute -right-2 -top-2 bg-red-100 text-red-600 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"><span class="material-symbols-outlined !text-[14px] block">close</span></button>
        
        <input type="text" name="adhoc_name[]" value="{name}" placeholder="Misal: Bonus Lembur Natal" required class="flex-1 min-w-[150px] py-2 px-3 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-900 focus:border-purple-500 transition-all">
        
        <select name="adhoc_type[]" class="w-32 py-2 px-3 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-700 focus:border-purple-500 transition-all">
            <option value="allowance" {type_all}>Tunjangan (+)</option>
            <option value="deduction" {type_ded}>Potongan (-)</option>
        </select>

        <select name="adhoc_basis[]" class="w-36 py-2 px-3 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-700 focus:border-purple-500 transition-all">
            <option value="fixed_monthly" {basis_fix}>Bulanan Tetap</option>
            <option value="per_attendance" {basis_att}>Per Kehadiran</option>
            <option value="per_late_day" {basis_late}>Per Hari Telat</option>
        </select>
        
        <div class="w-32 relative shrink-0">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
            <input type="text" name="adhoc_amount[]" value="{amount}" onkeyup="formatRupiah(this)" required class="w-full pl-8 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-900 font-mono focus:border-purple-500 transition-all">
        </div>
    </div>
</template>

<script>
    let table;
    $(document).ready(function() {
        table = $('#setupTable').DataTable({
            "ajax": "<?= base_url('payroll_setup/get_employees_json') ?>",
            "language": { "emptyTable": "Tidak ada data pegawai." },
            "columnDefs": [
                { "orderable": false, "targets": [5] }
            ]
        });
    });

    const calcLabels = {
        'fixed_monthly': 'Bulanan Tetap',
        'per_attendance': 'Per Kehadiran',
        'per_late_day': 'Per Hari Telat'
    };

    function setupEmp(id) {
        $.getJSON('<?= base_url("payroll_setup/get_employee_setup/") ?>' + id, function(res) {
            
            // Set Titles/Basic Salary
            $('#setup_employee_id').val(res.employee.id);
            $('#empNameTitle').text('Setup Gaji: ' + res.employee.full_name);
            $('#empNipTitle').text('NIP: ' + res.employee.nip);
            
            let basicStr = res.employee.basic_salary ? res.employee.basic_salary.toString() : '';
            // Lepas angka desimal dibelakang koma dari MySQL "10000000.00" menjadi "10000000"
            if(basicStr.includes('.')) basicStr = basicStr.split('.')[0];
            
            $('#basic_salary').val(basicStr);
            if(basicStr) formatRupiah(document.getElementById('basic_salary'));

            // Render Components
            let html = '';
            let tpl = $('#compRowTemplate').html();
            
            res.components.forEach(function(comp) {
                let badge = comp.type === 'allowance' ? 
                    '<span class="px-1.5 py-0.5 rounded bg-green-100 text-green-700 text-[9px] font-black uppercase tracking-wider">Tunjangan (+)</span>' : 
                    '<span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700 text-[9px] font-black uppercase tracking-wider">Potongan (-)</span>';
                
                let c_amt = comp.custom_amount ? comp.custom_amount.toString() : '0';
                if(c_amt.includes('.')) c_amt = c_amt.split('.')[0];
                
                let el = tpl.replace(/{id}/g, comp.component_id)
                            .replace(/{name}/g, comp.name)
                            .replace(/{badge}/g, badge)
                            .replace(/{basis}/g, calcLabels[comp.calculation_basis] || comp.calculation_basis)
                            .replace(/{amount}/g, c_amt);

                html += el;
            });
            
            $('#componentsList').html(html);

            // Re-check formatting for the newly added inputs
            res.components.forEach(function(comp) {
                let amtInput = document.getElementById('amt_' + comp.component_id);
                formatRupiah(amtInput); // Format it initially
                
                // If this component was active for this employee, check it and enable input
                if(comp.is_active_for_emp === 1) {
                    $('#chk_' + comp.component_id).prop('checked', true);
                    $(amtInput).prop('disabled', false).removeClass('bg-gray-100').addClass('bg-white');
                }
            });

            // Render Adhoc Components
            $('#adhocContainer').empty();
            res.adhoc.forEach(function(ah) {
                addAdhocRow(ah.name, ah.type, ah.calculation_basis, ah.amount);
            });

            calculatePreview();
            $('#setupModal').removeClass('hidden');
        });
    }

    function addAdhocRow(name = '', type = 'allowance', basis = 'fixed_monthly', amount = '') {
        let tpl = $('#adhocRowTemplate').html();
        
        if(amount) {
            amount = amount.toString();
            if(amount.includes('.')) amount = amount.split('.')[0];
        }
        
        let el = tpl.replace(/{name}/g, name)
                    .replace(/{type_all}/g, type === 'allowance' ? 'selected' : '')
                    .replace(/{type_ded}/g, type === 'deduction' ? 'selected' : '')
                    .replace(/{basis_fix}/g, basis === 'fixed_monthly' ? 'selected' : '')
                    .replace(/{basis_att}/g, basis === 'per_attendance' ? 'selected' : '')
                    .replace(/{basis_late}/g, basis === 'per_late_day' ? 'selected' : '')
                    .replace(/{amount}/g, amount);
                    
        let $row = $(el);
        $('#adhocContainer').append($row);
        
        if (amount) {
            formatRupiah($row.find('input[name="adhoc_amount[]"]')[0]);
        }
    }

    function toggleCustomInput(id) {
        let isChecked = $('#chk_' + id).is(':checked');
        let $input = $('#amt_' + id);
        
        if(isChecked) {
            $input.prop('disabled', false).removeClass('bg-gray-100').addClass('bg-white');
            $input.focus();
        } else {
            $input.prop('disabled', true).addClass('bg-gray-100').removeClass('bg-white');
        }
        calculatePreview();
    }

    function calculatePreview() {
        let basicStr = $('#basic_salary').val().replace(/[^0-9]/g, '');
        let total = parseInt(basicStr) || 0;

        // Sum components
        $('#componentsList .comp-checkbox:checked').each(function() {
            let id = $(this).val();
            let $row = $(this).closest('.flex');
            let typeText = $row.find('span.bg-green-100, span.bg-red-100').text();
            let amtStr = $('#amt_' + id).val().replace(/[^0-9]/g, '');
            let amt = parseInt(amtStr) || 0;

            if(typeText.includes('Tunjangan')) {
                total += amt;
            } else if(typeText.includes('Potongan')) {
                total -= amt;
            }
        });

        // Sum adhoc
        $('#adhocContainer .flex').each(function() {
            let type = $(this).find('select[name="adhoc_type[]"]').val();
            let amtStr = $(this).find('input[name="adhoc_amount[]"]').val().replace(/[^0-9]/g, '');
            let amt = parseInt(amtStr) || 0;

            if(type === 'allowance') {
                total += amt;
            } else if(type === 'deduction') {
                total -= amt;
            }
        });

        $('#preview_total').text('Rp ' + total.toLocaleString('id-ID'));
    }

    // Attach listeners for preview
    $(document).on('keyup change', '#basic_salary, #componentsList input[type="text"], #adhocContainer input[type="text"], #adhocContainer select', function() {
        calculatePreview();
    });

    function closeModal() {
        $('#setupModal').addClass('hidden');
    }

    // Auto format Rupiah
    function formatRupiah(obj) {
        let val = obj.value.replace(/[^,\d]/g, '').toString();
        let split = val.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        obj.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }

    function saveSetup() {
        if(!$('#basic_salary').val()) {
            Swal.fire('Error', 'Gaji Pokok wajib diisi walau 0.', 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url("payroll_setup/save_setup") ?>',
            type: 'POST',
            data: $('#setupForm').serialize(),
            success: function(res) {
                let r = JSON.parse(res);
                if(r.status === 'success') {
                    Toast.fire({icon: 'success', title: r.message});
                    table.ajax.reload(null, false);
                    closeModal();
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            }
        });
    }
</script>
