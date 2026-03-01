<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Verifikasi Jadwal</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Persetujuan Roster Unit</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Verifikasi pengajuan jadwal kerja dari anggota tim Anda.</p>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left" id="approvalTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Pegawai</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Periode</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Tgl Submit</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach($submissions as $s): ?>
                <tr class="hover:bg-gray-50/50 transition-all">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs">
                                <?= substr($s->employee_name, 0, 2) ?>
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900"><?= $s->employee_name ?></p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><?= $s->unit_name ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4 text-sm font-bold text-gray-700">
                        <?= date('F', mktime(0,0,0, $s->period_month, 10)) ?> <?= $s->period_year ?>
                    </td>
                    <td class="px-8 py-4 text-xs font-medium text-gray-500">
                        <?= date('d M Y H:i', strtotime($s->submitted_at)) ?>
                    </td>
                    <td class="px-8 py-4">
                        <?php 
                        $status_colors = [
                            'pending' => 'bg-blue-50 text-blue-600',
                            'approved' => 'bg-green-50 text-green-600',
                            'revision' => 'bg-red-50 text-red-600',
                            'draft' => 'bg-gray-50 text-gray-400'
                        ];
                        ?>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?= $status_colors[$s->status] ?>">
                            <?= $s->status ?>
                        </span>
                    </td>
                    <td class="px-8 py-4 text-right">
                        <button onclick="openReviewModal(<?= $s->id ?>)" class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95">
                            Review Jadwal
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Review Submission -->
<div id="reviewModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeReviewModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-5xl overflow-hidden transform transition-all border border-gray-100 inline-block text-left align-middle">
            <!-- Header -->
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img id="rev_photo" src="" class="size-16 rounded-2xl object-cover bg-gray-100">
                    <div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight" id="rev_name">-</h2>
                        <p class="text-xs text-blue-600 font-black uppercase tracking-widest mt-0.5" id="rev_detail">-</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="approveRoster()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-500/20 active:scale-95 transition-all">Approve & Publish</button>
                    <button onclick="requestRevision()" class="bg-red-50 text-red-600 hover:bg-red-100 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest active:scale-95 transition-all">Minta Revisi</button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-8 max-h-[60vh] overflow-y-auto bg-gray-50/50">
                <div id="rev_grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    <!-- Dynamic Grid -->
                </div>
            </div>

            <!-- Note Footer -->
            <div class="p-8 border-t border-gray-50 bg-white">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan untuk Pegawai (Wajib jika revisi)</label>
                <textarea id="approval_note" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-4 focus:ring-2 focus:ring-blue-500 border-none px-6 h-24" placeholder="Contoh: Tanggal 15 tolong ditukar ke shift sore karena kekurangan orang..."></textarea>
            </div>
        </div>
    </div>
</div>

<script>
let activeSubmissionId = null;

function openReviewModal(id) {
    activeSubmissionId = id;
    $.ajax({
        url: '<?= base_url("schedule/get_submission_full_detail/") ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const s = data.submission;
            const details = data.details;

            $('#rev_name').text(s.full_name);
            $('#rev_detail').text(`NIP: ${s.nip} • Periode ${s.period_month}/${s.period_year}`);
            $('#rev_photo').attr('src', s.photo ? '<?= base_url() ?>' + s.photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(s.full_name)}&background=random`);
            $('#approval_note').val(s.revision_note || '');

            let html = '';
            const groupedDetails = {};
            details.forEach(d => {
                if(!groupedDetails[d.date]) groupedDetails[d.date] = [];
                groupedDetails[d.date].push(d);
            });

            // Get total days in month
            const year = s.period_year;
            const month = s.period_month; // 1-12
            const daysInMonth = new Date(year, month, 0).getDate();

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayShifts = groupedDetails[dateStr] || [];
                
                const dateObj = new Date(dateStr);
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                const isWeekend = (dayName === 'Saturday' || dayName === 'Sunday');
                
                let shiftsHtml = '';
                dayShifts.forEach(ds => {
                    if(ds.shift_name) {
                        shiftsHtml += `
                            <div class="mt-1.5 px-2 py-1.5 rounded-lg text-[9px] font-black uppercase text-white shadow-sm" style="background-color: ${ds.color}">
                                ${ds.shift_name}
                                <p class="text-[7px] opacity-70 font-bold">${ds.start_time.substring(0,5)} - ${ds.end_time.substring(0,5)}</p>
                            </div>`;
                    }
                });

                if(!shiftsHtml) {
                    shiftsHtml = `<div class="mt-2 text-[9px] font-black uppercase text-gray-300 italic tracking-widest text-center py-2 border border-dashed border-gray-200 rounded-xl">OFF</div>`;
                }

                html += `
                <div class="bg-white rounded-2xl p-4 border border-gray-50 shadow-sm flex flex-col min-h-[120px]">
                    <div class="flex flex-col mb-2">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] ${isWeekend ? 'text-red-400' : 'text-gray-300'}">${dayName.substring(0,3)}</span>
                        <span class="text-lg font-black text-gray-900 leading-none">${day}</span>
                    </div>
                    <div class="space-y-1">
                        ${shiftsHtml}
                    </div>
                </div>`;
            }
            $('#rev_grid').html(html);
            $('#reviewModal').removeClass('hidden').show();
        }
    });
}

function closeReviewModal() { $('#reviewModal').addClass('hidden').hide(); }

function approveRoster() {
    confirmAndProcess('approved', 'Konfirmasi Approve?', 'Jadwal akan dipublikasikan ke sistem HRD.');
}

function requestRevision() {
    const note = $('#approval_note').val();
    if(!note) {
        Swal.fire('Catatan Wajib', 'Mohon isi alasan revisi agar pegawai tahu apa yang harus diperbaiki.', 'warning');
        return;
    }
    confirmAndProcess('revision', 'Minta Revisi?', 'Pegawai akan diminta memperbaiki jadwal sesuai catatan.');
}

function confirmAndProcess(status, title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses'
    }).then(r => {
        if(r.isConfirmed) {
            $.ajax({
                url: '<?= base_url("schedule/process_approval") ?>',
                type: 'POST',
                data: {
                    id: activeSubmissionId,
                    status: status,
                    note: $('#approval_note').val()
                },
                dataType: 'json',
                success: function(res) {
                    Toast.fire({icon: 'success', title: res.message});
                    closeReviewModal();
                    location.reload();
                }
            });
        }
    });
}
</script>
