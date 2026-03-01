<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Master Shift</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Master Data Shift</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Definisikan jam operasional masuk dan pulang untuk semua kategori unit.</p>
        </div>
        <button onclick="addNewShift()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-md active:scale-95">
            <span class="material-symbols-outlined">add</span>
            <span>Tambah Shift Baru</span>
        </button>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="masterShiftList">
        <!-- Dynamic Load -->
    </div>
</div>

<!-- Modal: Add/Edit Shift Form -->
<div id="shiftFormModal" class="fixed inset-0 z-[70] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-md" onclick="closeShiftFormModal()"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-md w-full p-8 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900" id="shiftFormTitle">Create New Shift</h3>
                <button onclick="closeShiftFormModal()" class="text-gray-400 hover:text-gray-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form id="shiftForm" class="space-y-6">
                <input type="hidden" name="id" id="shift_id">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Shift Identity Name</label>
                    <input type="text" name="name" id="shift_name" placeholder="Pagi (07:30 - 15:30), dll" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-5" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Clock IN (24 Jam)</label>
                        <input type="text" name="start_time" id="shift_start" placeholder="00:00" maxlength="5" class="time-mask w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-5" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Clock OUT (24 Jam)</label>
                        <input type="text" name="end_time" id="shift_end" placeholder="00:00" maxlength="5" class="time-mask w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-5" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Label Color</label>
                    <input type="color" name="color" id="shift_color" value="#3b82f6" class="w-full h-12 rounded-2xl border-none cursor-pointer p-1 bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Description / Notes</label>
                    <textarea name="description" id="shift_desc" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-5 h-24" placeholder="Keterangan shift (opsional)"></textarea>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest py-4 rounded-2xl shadow-lg shadow-blue-500/20 transition-all">Save Changes</button>
                    <button type="button" onclick="closeShiftFormModal()" class="px-8 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xs uppercase tracking-widest py-4 rounded-2xl transition-all">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadMasterShifts();

    $('#shiftForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("shifts/store_shift") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                Toast.fire({icon: 'success', title: res.message});
                closeShiftFormModal();
                loadMasterShifts();
            }
        });
    });

    // Time Mask for 24h format
    $('.time-mask').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val.length >= 2) {
            val = val.substring(0, 2) + ':' + val.substring(2, 4);
        }
        $(this).val(val);
    }).on('blur', function() {
        let val = $(this).val();
        let parts = val.split(':');
        if(parts.length === 2) {
            let h = parseInt(parts[0]) || 0;
            let m = parseInt(parts[1]) || 0;
            if(h > 23) h = 23;
            if(m > 59) m = 59;
            $(this).val(h.toString().padStart(2, '0') + ':' + m.toString().padStart(2, '0'));
        }
    });
});

function loadMasterShifts() {
    $.ajax({
        url: '<?= base_url("shifts/get_shifts_json") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            let html = '';
            res.data.forEach(s => {
                html += `
                <div class="bg-white rounded-[2rem] p-8 border border-gray-50 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="flex items-center justify-between mb-6">
                        <div class="size-14 rounded-2xl flex items-center justify-center" style="background-color: ${s.color}15; color: ${s.color}; border: 2px solid ${s.color}30;">
                            <span class="material-symbols-outlined !text-[32px]">schedule</span>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="editShift(${s.id}, \`${s.name}\`, '${s.start_time}', '${s.end_time}', '${s.color}', \`${s.description || ''}\`)" class="size-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            <button onclick="deleteShift(${s.id})" class="size-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 mb-1">${s.name}</h4>
                        <p class="text-gray-400 text-xs font-medium">${s.description || 'Tidak ada keterangan'}</p>
                    </div>
                    <div class="mt-8 pt-8 border-t border-gray-50 flex items-center justify-between">
                         <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Clock Time</p>
                            <p class="text-sm font-black text-gray-900 mt-1">${s.start_time.substring(0,5)} — ${s.end_time.substring(0,5)}</p>
                         </div>
                         <div class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border" style="color: ${s.color}; border-color: ${s.color}30; background-color: ${s.color}05">Active</div>
                    </div>
                </div>`;
            });
            $('#masterShiftList').html(html);
        }
    });
}

function addNewShift() {
    $('#shiftForm')[0].reset();
    $('#shift_id').val('');
    $('#shiftFormTitle').text('Create New Shift');
    $('#shiftFormModal').removeClass('hidden').show();
}

function editShift(id, name, start, end, color, desc) {
    $('#shift_id').val(id);
    $('#shift_name').val(name);
    $('#shift_start').val(start.substring(0, 5));
    $('#shift_end').val(end.substring(0, 5));
    $('#shift_color').val(color);
    $('#shift_desc').val(desc);
    $('#shiftFormTitle').text('Edit Shift Configuration');
    $('#shiftFormModal').removeClass('hidden').show();
}

function closeShiftFormModal() { $('#shiftFormModal').addClass('hidden').hide(); }

function deleteShift(id) {
    Swal.fire({ 
        title: 'Hapus Shift?', 
        text: 'Pastikan shift tidak sedang digunakan dalam jadwal karyawan!',
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonText: 'Ya, Hapus' 
    }).then(r => {
        if(r.isConfirmed) {
            $.ajax({
                url: '<?= base_url("shifts/delete_shift/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        Toast.fire({icon: 'success', title: res.message});
                        loadMasterShifts();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}
</script>
