<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Slip Gaji Saya</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] text-3xl font-black leading-tight tracking-tight">Dokumen Gaji Anda</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar riwayat rincian pembayaran gaji dari bulan ke bulan.</p>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6 text-blue-800 text-sm">
        Sistem hanya akan menampilkan slip gaji yang telah disetujui (Rilis) oleh HRD.
        Untuk mengunduh, tekan tombol <span class="font-bold">Download Slip</span>.
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="mySlipsTable">
            <thead>
                <tr class="bg-gray-50 border-b border-[#e5e7eb]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Bulan Gaji</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Masa Kerja (Hadir & Telat)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Take Home Pay</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb]">
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#mySlipsTable').DataTable({
            "ajax": "<?= base_url('my_payroll/get_my_slips_json') ?>",
            "language": { "emptyTable": "Belum ada riwayat slip gaji yang dirilis untuk Anda." }
        });
    });
</script>
