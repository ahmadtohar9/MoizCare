<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Kelola Gaji</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] text-3xl font-black leading-tight tracking-tight">Kalkulasi Penggajian</h1>
            <p class="text-gray-500 text-sm mt-1">Buat, tinjau, dan setujui slip gaji per bulan berdasarkan absensi pegawai.</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md">
            <span class="material-symbols-outlined">add_task</span>
            <span>Generate Gaji Bulan Baru</span>
        </button>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="periodsTable">
            <thead>
                <tr class="bg-gray-50 border-b border-[#e5e7eb]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Bulan/Tahun</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jumlah Slip</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Total Gaji Dibayarkan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb]">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate -->
<div id="genModal" class="hidden fixed inset-0 z-40 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-xl w-full max-w-md shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Generate Penggajian</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form id="genForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
                    <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                        <?php 
                        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        for($i=1; $i<=12; $i++) {
                            $sel = ($i == date('n')) ? 'selected' : '';
                            echo "<option value='$i' $sel>{$months[$i]}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Tahun</label>
                    <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                        <?php 
                        $cy = date('Y');
                        for($y=$cy-2; $y<=$cy+1; $y++) {
                            $sel = ($y == $cy) ? 'selected' : '';
                            echo "<option value='$y' $sel>$y</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg mt-4 text-sm text-blue-800">
                    <p class="font-bold flex items-center gap-1 mb-1"><span class="material-symbols-outlined text-[16px]">info</span> Informasi</p>
                    <p class="text-xs">Sistem akan mengalkulasi otomatis absensi per pegawai, keterlambatan, dan tunjangan harian berdasarkan data jadwal absen final bulan bersangkutan.</p>
                </div>
            </form>

            <div class="mt-6 flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                <button onclick="submitGen()" id="btnGen" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-primary/90">
                    Kalkulasi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var table;
    $(document).ready(function() {
        table = $('#periodsTable').DataTable({
            "ajax": "<?= base_url('payroll/get_periods_json') ?>",
            "language": { "emptyTable": "Belum ada history penggajian." }
        });
    });

    function openModal() { $('#genModal').removeClass('hidden'); }
    function closeModal() { $('#genModal').addClass('hidden'); }

    function submitGen() {
        $('#btnGen').html('<span class="material-symbols-outlined animate-spin text-[16px]">refresh</span> Processing...').attr('disabled', true);
        
        $.ajax({
            url: '<?= base_url("payroll/generate") ?>',
            type: 'POST',
            data: $('#genForm').serialize(),
            dataType: 'json',
            success: function(res) {
                $('#btnGen').html('Kalkulasi Sekarang').attr('disabled', false);
                if(res.status === 'success') {
                    Toast.fire({icon: 'success', title: res.message});
                    table.ajax.reload();
                    closeModal();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    }

    function deletePeriod(id) {
        Swal.fire({
            title: 'Hapus Draft Penggajian?',
            text: "Data slip dan perhitungan di bulan ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if(result.isConfirmed) {
                $.post('<?= base_url("payroll/delete_period/") ?>' + id, function(res) {
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
