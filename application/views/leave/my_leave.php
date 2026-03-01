<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Cuti & Izin Saya</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Portal Cuti Pegawai</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola permohonan cuti dan pantau sisa kuota anda.</p>
        </div>
        <button onclick="$('#modalRequest').removeClass('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add_circle</span>
            Ajukan Cuti Baru
        </button>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Quota Info -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-gray-900 rounded-[2.5rem] p-6 text-white shadow-xl">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-6 font-mono text-center">Sisa Kuota <?= date('Y') ?></h3>
                <div class="space-y-4">
                    <?php if(empty($quotas)): ?>
                        <p class="text-[10px] text-gray-500 text-center italic">Kuota belum diset oleh HRD.</p>
                    <?php endif; ?>
                    <?php foreach($quotas as $q): ?>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <p class="text-[9px] font-black uppercase opacity-40 leading-none"><?= $q->leave_name ?></p>
                            <div class="flex items-end justify-between mt-2">
                                <span class="text-2xl font-black"><?= $q->total_quota - $q->used_quota ?></span>
                                <span class="text-[9px] font-bold opacity-60 italic">HARI LAGI</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/10 rounded-full mt-3 overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: <?= ($q->used_quota / $q->total_quota) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- History Requests -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-6">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">history</span>
                    Riwayat Pengajuan
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="pb-4">Jenis Cuti</th>
                                <th class="pb-4">Tanggal</th>
                                <th class="pb-4">Durasi</th>
                                <th class="pb-4">Status</th>
                                <th class="pb-4">Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 search-table">
                            <?php if(empty($requests)): ?>
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center opacity-20">
                                            <span class="material-symbols-outlined text-6xl">format_list_bulleted</span>
                                            <p class="text-xs font-black uppercase tracking-widest mt-2">Belum ada riwayat pengajuan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($requests as $r): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4">
                                        <p class="font-black text-gray-900 text-xs"><?= $r->leave_name ?></p>
                                        <p class="text-[9px] text-blue-500 font-black uppercase tracking-widest mt-0.5"><?= date('d M Y', strtotime($r->created_at)) ?></p>
                                    </td>
                                    <td class="py-4 text-[11px] font-bold text-gray-600">
                                        <?= date('d M', strtotime($r->start_date)) ?> - <?= date('d M Y', strtotime($r->end_date)) ?>
                                    </td>
                                    <td class="py-4 text-[11px] font-black"><?= $r->total_days ?> Hari</td>
                                    <td class="py-4">
                                        <?php if($r->status == 'pending'): ?>
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest border border-amber-100">Menunggu Karu</span>
                                        <?php elseif($r->status == 'approved_karu'): ?>
                                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-100">Di-Acc Karu</span>
                                        <?php elseif($r->status == 'approved'): ?>
                                            <span class="px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-[9px] font-black uppercase tracking-widest border border-green-200">Disetujui HRD</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-[9px] font-black uppercase tracking-widest border border-red-200">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex gap-1.5 p-1 bg-gray-50 rounded-xl border border-gray-100">
                                                <div class="size-6 rounded-lg flex items-center justify-center <?= $r->karu_id ? 'bg-green-500 text-white shadow-sm' : 'bg-white text-gray-300' ?>" title="Karu approval">
                                                    <span class="material-symbols-outlined !text-[12px] font-black"><?= $r->karu_id ? 'check' : 'person' ?></span>
                                                </div>
                                                <div class="size-6 rounded-lg flex items-center justify-center <?= $r->status == 'approved' ? 'bg-blue-500 text-white shadow-sm' : 'bg-white text-gray-300' ?>" title="HRD approval">
                                                    <span class="material-symbols-outlined !text-[12px] font-black"><?= $r->status == 'approved' ? 'check' : 'badge' ?></span>
                                                </div>
                                            </div>
                                            
                                            <?php if($r->status === 'pending'): ?>
                                                <button onclick="cancelRequest(<?= $r->id ?>)" class="size-8 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm active:scale-90" title="Batalkan Pengajuan">
                                                    <span class="material-symbols-outlined !text-[18px]">close</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Request -->
<div id="modalRequest" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[999] flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl animate-in zoom-in duration-200">
        <div class="p-8">
            <h3 class="text-xl font-black text-gray-900 mb-2">Form Pengajuan Cuti</h3>
            <p class="text-xs text-gray-500 mb-8 font-bold">Pastikan sisa kuota anda masih mencukupi.</p>
            
            <form action="<?= base_url('leave/submit_request') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Jenis Cuti / Izin</label>
                    <select name="leave_type_id" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-3.5 focus:ring-2 focus:ring-blue-500" required>
                        <?php foreach($leave_types as $lt): ?>
                            <option value="<?= $lt->id ?>"><?= $lt->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-3.5 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-3.5 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Karu / Atasan Langsung</label>
                    <select name="karu_id" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-3.5 focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Penanggung Jawab --</option>
                        <?php foreach($approvers as $app): ?>
                            <option value="<?= $app->user_id ?>"><?= $app->full_name ?> (<?= $app->unit_name ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Alasan / Keterangan</label>
                    <textarea name="reason" rows="3" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-4 focus:ring-2 focus:ring-blue-500" placeholder="Jelaskan alasan permohonan..." required></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Lampiran Dokumen (Opsional)</label>
                    <input type="file" name="attachment" class="w-full text-xs font-bold text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="$('#modalRequest').addClass('hidden')" class="flex-1 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gray-200 active:scale-95 transition-all">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cancelRequest(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Data pengajuan akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl font-black px-6 py-3 uppercase tracking-wider text-xs',
                cancelButton: 'rounded-xl font-black px-6 py-3 uppercase tracking-wider text-xs'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('leave/delete_request/') ?>' + id;
            }
        });
    }
</script>
