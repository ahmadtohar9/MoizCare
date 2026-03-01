<?php
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
$month_name = date('F', mktime(0, 0, 0, $current_month, 10));
?>
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Pengajuan Jadwal</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 bg-white px-5 py-4 rounded-3xl border border-gray-100 shadow-sm">
            <div class="size-12 rounded-2xl overflow-hidden ring-4 ring-gray-50">
                <img src="<?= (!empty($my_info->photo) && file_exists($my_info->photo)) ? base_url($my_info->photo) : 'https://ui-avatars.com/api/?name='.urlencode($my_info->full_name).'&background=random&color=fff' ?>" class="w-full h-full object-cover">
            </div>
            <div>
                <h4 class="text-sm font-black text-gray-900 leading-none"><?= $my_info->full_name ?></h4>
                <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1"><?= $my_info->nip ?> • <?= $my_info->unit_name ?></p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Periode Penjadwalan</p>
            <h2 class="text-2xl font-black text-gray-900 leading-tight"><?= $month_name ?> <?= $current_year ?></h2>
        </div>
    </div>
        
        <?php if($submission): ?>
            <div class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex flex-col">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status Pengajuan</p>
                    <div class="flex items-center gap-2 mt-1">
                        <?php 
                        $status_map = [
                            'draft' => ['color' => 'gray', 'label' => 'DRAFT'],
                            'pending' => ['color' => 'blue', 'label' => 'WAITING APPROVAL'],
                            'revision' => ['color' => 'red', 'label' => 'REVISION NEEDED'],
                            'approved' => ['color' => 'green', 'label' => 'PUBLISHED'],
                        ];
                        $s = $status_map[$submission->status];
                        ?>
                        <span class="size-2 rounded-full bg-<?= $s['color'] ?>-500 animate-pulse"></span>
                        <span class="text-xs font-black text-<?= $s['color'] ?>-600 uppercase"><?= $s['label'] ?></span>
                    </div>
                </div>
                <?php if($submission->status === 'revision'): ?>
                    <div class="h-8 w-px bg-gray-100"></div>
                    <div class="max-w-xs">
                        <p class="text-[9px] font-black text-red-400 uppercase tracking-widest leading-none">Catatan Karu:</p>
                        <p class="text-[10px] text-red-600 font-bold mt-1 italic">"<?= $submission->revision_note ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="px-6 pb-20">
    <form id="submissionForm" class="space-y-8">
        <input type="hidden" name="employee_id" value="<?= $my_info->id ?>">
        <!-- 1. Approver Selection -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-2 rounded-xl">person_search</span>
                Otoritas Approval (Pilih Karu)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <?php $user_role = $this->session->userdata('role'); ?>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Pilih Kepala Ruangan / Penanggung Jawab</label>
                    <select name="approver_id" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-5" required <?= ($submission && $submission->status === 'approved' && $user_role !== 'admin') ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Approver --</option>
                        <?php foreach($approvers as $apr): ?>
                            <option value="<?= $apr->id ?>" <?= ($submission && $submission->approver_id == $apr->id) ? 'selected' : '' ?>><?= $apr->full_name ?> (<?= $apr->nip ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p class="text-xs text-gray-400 font-medium italic">Pilih atasan atau kepala ruangan yang bertugas memverifikasi jadwal Anda di periode ini.</p>
            </div>
        </div>

        <!-- 2. Daily Roster -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                    <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-2 rounded-xl">calendar_view_month</span>
                    Penyusunan Roster Harian
                </h3>
                <div class="flex gap-2">
                    <?php if($my_info->unit_type !== 'medical'): ?>
                         <button type="button" onclick="useOfficeTemplate()" class="bg-amber-50 text-amber-700 px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all hover:bg-amber-100">
                             <span class="material-symbols-outlined text-sm">bolt</span> Gunakan Jam Kantor
                         </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                <?php for($i=1; $i<=$days_in_month; $i++): 
                    $date = sprintf("%04d-%02d-%02d", $current_year, $current_month, $i);
                    $day_name = date('l', strtotime($date));
                    $is_weekend = ($day_name === 'Saturday' || $day_name === 'Sunday');
                ?>
                <div class="bg-gray-50 rounded-2xl p-4 border border-transparent hover:border-blue-100 transition-all group overflow-hidden relative">
                    <div class="flex flex-col mb-3">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] <?= $is_weekend ? 'text-red-400' : 'text-gray-400' ?>"><?= $day_name ?></span>
                        <span class="text-xl font-black text-gray-900"><?= $i ?></span>
                    </div>
                    
                    <div class="space-y-2">
                         <label class="block cursor-pointer">
                             <input type="checkbox" name="shifts[<?= $date ?>][]" value="" class="sr-only peer off-checkbox" data-date="<?= $date ?>">
                             <div class="w-full text-center py-1.5 rounded-lg border border-transparent bg-white text-[9px] font-black uppercase text-gray-400 peer-checked:bg-red-500 peer-checked:text-white transition-all shadow-sm">OFF</div>
                         </label>
                         <?php foreach($shifts as $sh): ?>
                         <label class="block cursor-pointer">
                             <input type="checkbox" name="shifts[<?= $date ?>][]" value="<?= $sh->id ?>" class="sr-only peer shift-checkbox" data-date="<?= $date ?>" data-shname="<?= $sh->name ?>">
                             <div style="--sh-color: <?= $sh->color ?>;" 
                                  class="w-full text-center py-1.5 rounded-lg border border-transparent bg-white text-gray-400 peer-checked:bg-[var(--sh-color)] peer-checked:text-white transition-all shadow-sm flex flex-col items-center justify-center">
                                  <span class="text-[9px] font-black uppercase"><?= $sh->name ?></span>
                                  <span class="text-[7px] font-bold opacity-60 leading-none mt-0.5"><?= substr($sh->start_time, 0, 5) ?> - <?= substr($sh->end_time, 0, 5) ?></span>
                             </div>
                         </label>
                         <?php endforeach; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- 3. Actions -->
        <?php 
        $role = $this->session->userdata('role');
        $is_locked = ($submission && $submission->status === 'approved' && $role !== 'admin');
        if(!$is_locked): 
        ?>
        <div class="fixed bottom-8 right-8 z-[50]">
            <button type="submit" class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-widest transition-all shadow-[0_20px_50px_rgba(37,99,235,0.3)] hover:scale-105 active:scale-95 group">
                <span class="material-symbols-outlined text-xl">send</span>
                <span><?= $submission ? 'Perbarui Usulan Jadwal' : 'Kirim Usulan Jadwal' ?></span>
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<script>
$(document).ready(function() {
    // If there is an existing submission, populate its details
    <?php if($submission): ?>
        loadSubmissionDetails(<?= $submission->id ?>);
    <?php endif; ?>

    $('#submissionForm').on('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Kirim Jadwal?',
            text: "Jadwal akan dikirim ke Karu dan tidak bisa diubah sementara.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Kirim Sekarang'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show Progress
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '<?= base_url("self_schedule/submit_schedule") ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        Swal.close();
                        if(res.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    });
});

function loadSubmissionDetails(id) {
    // In a real app, maybe fetch via AJAX or just pass details from controller
    // For now, I'll add an AJAX call to get the daily shifts
    $.ajax({
        url: '<?= base_url("schedule/get_submission_details/") ?>' + id, // Need to add this endpoint or similar
        type: 'GET',
        dataType: 'json',
        success: function(data) {
             // 1. Set all to OFF by default
             $('.off-checkbox').prop('checked', true);
             
             // 2. Overwrite with shifts from DB
             data.forEach(d => {
                 $(`input[name="shifts[${d.date}][]"][value="${d.shift_id}"]`).prop('checked', true);
                 // Uncheck OFF for this specific date
                 $(`.off-checkbox[data-date="${d.date}"]`).prop('checked', false);
             });
        }
    });
}

function useOfficeTemplate() {
    // Set Mon-Fri to Shift Pagi or similar
    const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    // Find Pagi ID
    let morningId = "";
    $('input[type="radio"]').each(function() {
        let name = $(this).data('shname') || "";
        if(name.toLowerCase().includes('pagi') || name.toLowerCase().includes('08:00')) {
            morningId = $(this).val();
            return false;
        }
    });

    if(!morningId) {
        Swal.fire('Opps', 'Master Shift Pagi tidak ditemukan!', 'warning');
        return;
    }

    // Reset all to off first
    $('input[type="radio"][value=""]').prop('checked', true);

    // Apply only for workdays across the whole month
    $('.grid > div').each(function() {
        let dayName = $(this).find('span:first').text().trim();
        let dateKey = $(this).find('input').attr('name').match(/\[(.*?)\]/)[1];
        
        if(weekDays.includes(dayName)) {
            $(`input[name="shifts[${dateKey}]"][value="${morningId}"]`).prop('checked', true);
        }
    });

    Toast.fire({icon: 'info', title: 'Office hours template applied.'});
}

$(document).on('change', '.off-checkbox', function() {
    if($(this).is(':checked')) {
        const date = $(this).data('date');
        $(`.shift-checkbox[data-date="${date}"]`).prop('checked', false);
    }
});

$(document).on('change', '.shift-checkbox', function() {
    if($(this).is(':checked')) {
        const date = $(this).data('date');
        $(`.off-checkbox[data-date="${date}"]`).prop('checked', false);
    }
});
</script>
