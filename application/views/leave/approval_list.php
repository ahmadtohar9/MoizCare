<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Manajemen Approval Cuti</span>
    </div>
    <div class="flex flex-col">
        <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Persetujuan Cuti Pegawai</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Lakukan verifikasi dan persetujuan permohonan cuti sesuai hierarki.</p>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-6 mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="approvalTable">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="pb-4">No</th>
                        <th class="pb-4">Pegawai</th>
                        <th class="pb-4">Jenis & Tanggal</th>
                        <th class="pb-4">Durasi</th>
                        <th class="pb-4">Alasan</th>
                        <th class="pb-4">Status</th>
                        <th class="pb-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $no=1; foreach($requests as $r): ?>
                        <tr>
                            <td class="py-4 text-xs font-bold"><?= $no++ ?></td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 text-sm"><?= $r->full_name ?></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none"><?= $r->nip ?> | <?= $r->unit_name ?></span>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase"><?= $r->leave_name ?></span>
                                <p class="text-[10px] font-bold text-gray-500 mt-1"><?= date('d/m/Y', strtotime($r->start_date)) ?> - <?= date('d/m/Y', strtotime($r->end_date)) ?></p>
                            </td>
                            <td class="py-4 font-black">
                                <?= $r->total_days ?> HARI
                            </td>
                            <td class="py-4 text-xs text-gray-600 font-medium italic">
                                "<?= $r->reason ?>"
                            </td>
                            <td class="py-4">
                                <?php if($r->status == 'pending'): ?>
                                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest border border-amber-100">Pending</span>
                                <?php elseif($r->status == 'approved_karu'): ?>
                                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-100">Acc Karu</span>
                                <?php elseif($r->status == 'approved'): ?>
                                    <span class="px-3 py-1 rounded-full bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest border border-green-100">Selesai</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-widest border border-red-100">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4">
                                <?php 
                                    $role = $this->session->userdata('role');
                                    $can_process = false;
                                    if($role === 'karu' && $r->status === 'pending') $can_process = true;
                                    if($role === 'admin' && $r->status === 'approved_karu') $can_process = true;
                                ?>
                                <?php if($can_process): ?>
                                    <button onclick="openProcessModal(<?= $r->id ?>, '<?= $r->full_name ?>')" class="bg-gray-900 hover:bg-black text-white size-10 rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95">
                                        <span class="material-symbols-outlined !text-[20px]">verified</span>
                                    </button>
                                <?php else: ?>
                                    <span class="text-[9px] font-bold text-gray-300 uppercase italic">Archived</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Process -->
<div id="modalProcess" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[999] flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-sm overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-8">
            <h3 class="text-xl font-black text-gray-900 mb-1">Keputusan Cuti</h3>
            <p id="employeeNameLabel" class="text-xs text-blue-600 font-black uppercase tracking-widest mb-8"></p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Catatan (Opsional)</label>
                    <textarea id="processNote" rows="3" class="w-full rounded-2xl border-none bg-gray-50 font-bold px-5 py-4 focus:ring-2 focus:ring-blue-500" placeholder="Berikan alasan persetujuan/penolakan..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button onclick="processRequest('reject')" class="py-4 bg-rose-50 text-rose-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-rose-100 transition-all">Tolak</button>
                    <button onclick="processRequest('approve')" class="py-4 bg-emerald-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">Setujui</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let activeRequestId = null;

    function openProcessModal(id, name) {
        activeRequestId = id;
        $('#employeeNameLabel').text(name);
        $('#modalProcess').removeClass('hidden');
    }

    function processRequest(action) {
        let note = $('#processNote').val();
        
        Swal.fire({
            title: 'Konfirmasi',
            text: `Anda yakin ingin ${action === 'approve' ? 'menyetujui' : 'menolak'} pengajuan ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan'
        }).then(r => {
            if(r.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('leave_approval/process') ?>',
                    type: 'POST',
                    data: { id: activeRequestId, action: action, note: note },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }
                });
            }
        });
    }

    $(document).on('click', function(e) {
        if ($(e.target).closest('.bg-white').length === 0 && !$(e.target).is('button')) {
            $('#modalProcess').addClass('hidden');
        }
    });
</script>
