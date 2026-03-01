<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <a class="hover:text-primary" href="<?= base_url('payroll') ?>">Kelola Gaji</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Master Komponen Gaji</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] text-3xl font-black leading-tight tracking-tight">Master Komponen Gaji</h1>
            <p class="text-gray-500 text-sm mt-1">Setup aturan tunjangan dan potongan yang berlaku pada slip gaji otomatis.</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md">
            <span class="material-symbols-outlined">add_circle</span>
            <span>Tambah Komponen</span>
        </button>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="compTable">
            <thead>
                <tr class="bg-gray-50 border-b border-[#e5e7eb]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Nama Komponen</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Tipe</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Besaran Nominal & Basis Hitung</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb]">
            </tbody>
        </table>
    </div>
</div>

<!-- Form Modal -->
<div id="compModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl w-full max-w-lg shadow-2xl p-8 transform transition-all h-auto max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-6 shrink-0">
                <h3 class="text-xl font-black text-gray-900" id="modalTitle">Tambah Komponen Gaji</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-xl transition-all"><span class="material-symbols-outlined !text-[20px]">close</span></button>
            </div>
            
            <div class="overflow-y-auto pr-2 pb-4 -mr-2">
                <form id="compForm" class="space-y-5">
                    <input type="hidden" name="id" id="comp_id">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Komponen</label>
                        <input type="text" name="name" id="name" required placeholder="Cth: Tunjangan Makan" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-gray-900">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe</label>
                            <select name="type" id="type" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-gray-900">
                                <option value="allowance">Tunjangan (+)</option>
                                <option value="deduction">Potongan (-)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Basis Perhitungan</label>
                            <select name="calculation_basis" id="calculation_basis" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-gray-900">
                                <option value="fixed_monthly">Bulanan Tetap</option>
                                <option value="per_attendance">Dikalikan Hari Hadir</option>
                                <option value="per_late_day">Dikalikan Hari Telat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="text" name="amount" id="amount" required onkeyup="formatRupiah(this)" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-gray-900 font-mono">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-3 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                        <span class="text-xs font-bold text-blue-900">Komponen Aktif Secara Default</span>
                    </div>

                    <div class="bg-amber-50 p-4 rounded-xl text-xs text-amber-800 leading-relaxed border border-amber-100 mt-4">
                        <p class="font-black uppercase tracking-widest mb-1 text-[10px] flex items-center gap-1.5"><span class="material-symbols-outlined !text-[14px]">lightbulb</span> Info Kalkulasi</p>
                        Jika kamu memilih <span class="font-bold border-b border-amber-300">Dikalikan Hari Hadir</span>, sistem akan otomatis mengalikan nominal di atas dengan jumlah kehadiran pegawai per bulan. Sama halnya untuk telat.
                    </div>
                </form>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 shrink-0">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                <button type="button" onclick="saveComponent()" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[18px]">save</span> Simpan Komponen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let table;
    $(document).ready(function() {
        table = $('#compTable').DataTable({
            "ajax": "<?= base_url('payroll_components/get_json') ?>",
            "language": { "emptyTable": "Belum ada komponen dsetup." }
        });
    });

    function openModal() {
        $('#compForm')[0].reset();
        $('#comp_id').val('');
        $('#modalTitle').text('Tambah Komponen Gaji');
        $('#is_active').prop('checked', true);
        $('#compModal').removeClass('hidden');
    }

    function closeModal() {
        $('#compModal').addClass('hidden');
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

    function editComp(id) {
        $.get('<?= base_url("payroll_components/get/") ?>' + id, function(data) {
            let res = JSON.parse(data);
            $('#comp_id').val(res.id);
            $('#name').val(res.name);
            $('#type').val(res.type);
            $('#calculation_basis').val(res.calculation_basis);
            
            // Re-format amount
            $('#amount').val(res.amount.replace(/\.00$/, ''));
            formatRupiah(document.getElementById('amount'));
            
            $('#is_active').prop('checked', res.is_active == 1);
            
            $('#modalTitle').text('Edit Komponen Gaji');
            $('#compModal').removeClass('hidden');
        });
    }

    function saveComponent() {
        if(!$('#name').val() || !$('#amount').val()) {
            Swal.fire('Error', 'Nama dan nominal wajib diisi', 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url("payroll_components/store") ?>',
            type: 'POST',
            data: $('#compForm').serialize(),
            success: function(res) {
                let r = JSON.parse(res);
                if(r.status == 'success') {
                    Toast.fire({icon: 'success', title: r.message});
                    table.ajax.reload();
                    closeModal();
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            }
        });
    }

    function deleteComp(id) {
        Swal.fire({
            title: 'Hapus Komponen?',
            text: "Komponen ini gaakan ditambahkan lagi di kalkulasi slip gaji bulan depan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if(result.isConfirmed) {
                $.post('<?= base_url("payroll_components/delete/") ?>' + id, function(res) {
                    let r = JSON.parse(res);
                    if(r.status == 'success') {
                        Toast.fire({icon: 'success', title: r.message});
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', r.message, 'error');
                    }
                });
            }
        });
    }
</script>
