<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Jadwal Kerja</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Penjadwalan Karyawan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Atur jadwal shift mingguan untuk medis dan non-medis.</p>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar: Employee List -->
        <div class="lg:col-span-1 border-r border-gray-100 pr-4">
            <div class="space-y-3 mb-6">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">search</span>
                    <input type="text" id="searchEmp" placeholder="Cari pegawai..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border-gray-100 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-blue-500 border-none transition-all">
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">corporate_fare</span>
                    <select id="filterDept" class="w-full pl-9 pr-4 py-2.5 rounded-xl border-gray-100 bg-gray-50 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-blue-500 border-none transition-all">
                        <option value="">Semua Departemen</option>
                        <?php foreach($units as $u): ?>
                            <option value="<?= $u->name ?>"><?= $u->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="space-y-4 max-h-[65vh] overflow-y-auto custom-scrollbar pr-2" id="empList">
                <?php foreach($grouped_employees as $unit => $emps): ?>
                <div class="unit-group" data-unit="<?= $unit ?>">
                    <div class="flex items-center gap-2 mb-2 px-2">
                        <span class="size-1.5 rounded-full bg-blue-500"></span>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest"><?= $unit ?></h4>
                    </div>
                    <div class="space-y-1">
                        <?php foreach($emps as $emp): 
                            $isMedical = $emp->unit_type === 'medical';
                        ?>
                        <button onclick="loadWeeklySchedule(<?= $emp->id ?>, '<?= $emp->full_name ?>', '<?= $emp->unit_type ?>')" class="emp-btn w-full flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-all group text-left border border-transparent" data-name="<?= strtolower($emp->full_name) ?>" id="emp-<?= $emp->id ?>">
                            <div class="size-8 rounded-lg <?= $isMedical ? 'bg-indigo-50 text-indigo-600' : 'bg-orange-50 text-orange-600' ?> flex items-center justify-center font-black text-[10px] group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <?= $isMedical ? 'MD' : 'MG' ?>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900 leading-tight"><?= $emp->full_name ?></p>
                                <p class="text-[9px] text-gray-400 font-medium"><?= $emp->nip ?></p>
                            </div>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Area: Weekly Assignment -->
        <div class="lg:col-span-3">
            <div id="welcomeState" class="h-full flex flex-col items-center justify-center py-20 text-center">
                <div class="size-20 rounded-3xl bg-blue-50 flex items-center justify-center text-blue-600 mb-6">
                    <span class="material-symbols-outlined !text-[40px]">event_repeat</span>
                </div>
                <h3 class="text-xl font-black text-gray-900">Penjadwalan Global</h3>
                <p class="text-sm text-gray-400 mt-1 max-w-xs">Klik pada nama pegawai untuk mengatur jadwal mingguan. Klik kembali untuk menutup.</p>
            </div>

            <div id="scheduleFormArea" class="hidden">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <button onclick="hideScheduleArea()" class="size-10 rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </button>
                        <div>
                            <div id="typeBadge" class="inline-block px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest mb-1.5 border">MANAGEMENT</div>
                            <h2 class="text-2xl font-black text-gray-900" id="selectedEmpName">-</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="btnStandardHours" onclick="setStandardHours()" class="hidden flex items-center gap-2 bg-amber-50 text-amber-700 px-4 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all hover:bg-amber-100">
                            <span class="material-symbols-outlined text-lg">bolt</span>
                            Terapkan Jam Kantor
                        </button>
                        <button onclick="saveWeeklySchedule()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                            <span class="material-symbols-outlined text-lg">save</span>
                            Simpan Jadwal
                        </button>
                    </div>
                </div>

                <form id="scheduleForm" class="space-y-4">
                    <input type="hidden" name="employee_id" id="form_employee_id">
                    
                    <?php 
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach($days as $day): 
                    ?>
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 flex items-center gap-6 group hover:border-blue-200 transition-all shadow-sm">
                        <div class="w-24 md:w-32 border-r border-gray-50 uppercase tracking-widest text-[10px] font-black text-gray-400 group-hover:text-blue-600 transition-colors">
                            <?= $day ?>
                        </div>
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            <label class="relative group/radio cursor-pointer">
                                <input type="checkbox" name="schedule[<?= $day ?>][]" value="" class="sr-only peer off-checkbox" data-day="<?= $day ?>">
                                <div class="px-2 py-2 rounded-xl border border-transparent bg-white text-gray-400 flex items-center justify-center font-black text-[9px] uppercase tracking-widest peer-checked:bg-red-500 peer-checked:text-white transition-all text-center h-full shadow-sm hover:bg-gray-50">OFF</div>
                            </label>
                            <?php foreach($shifts as $sh): ?>
                            <label class="relative group/radio cursor-pointer">
                                <input type="checkbox" name="schedule[<?= $day ?>][]" value="<?= $sh->id ?>" class="sr-only peer shift-checkbox" data-day="<?= $day ?>" data-shname="<?= $sh->name ?>">
                                <div style="--shift-color: <?= $sh->color ?>;" 
                                     class="px-2 py-2 rounded-xl border border-transparent bg-white text-gray-400 flex flex-col items-center justify-center peer-checked:bg-[var(--shift-color)] peer-checked:text-white transition-all text-center h-full shadow-sm hover:bg-gray-50">
                                    <span class="font-black text-[9px] uppercase tracking-wider leading-tight"><?= $sh->name ?></span>
                                    <span class="text-[8px] font-medium opacity-60 mt-1"><?= substr($sh->start_time, 0, 5) ?> - <?= substr($sh->end_time, 0, 5) ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#searchEmp, #filterDept').on('keyup change', function() {
        let q = $('#searchEmp').val().toLowerCase();
        let dept = $('#filterDept').val();
        
        $('.unit-group').each(function() {
            let unitName = $(this).data('unit');
            let hasVisibleEmp = false;
            
            $(this).find('.emp-btn').each(function() {
                let empName = $(this).data('name');
                let matchesSearch = empName.includes(q);
                let matchesDept = (dept === "" || unitName === dept);
                
                if(matchesSearch && matchesDept) {
                    $(this).show();
                    hasVisibleEmp = true;
                } else {
                    $(this).hide();
                }
            });
            
            $(this).toggle(hasVisibleEmp);
        });
    });

    // Handle Checkbox logic (OFF vs Shifts)
    $(document).on('change', '.off-checkbox', function() {
        if($(this).is(':checked')) {
            const day = $(this).data('day');
            $(`.shift-checkbox[data-day="${day}"]`).prop('checked', false);
        }
    });

    $(document).on('change', '.shift-checkbox', function() {
        if($(this).is(':checked')) {
            const day = $(this).data('day');
            $(`.off-checkbox[data-day="${day}"]`).prop('checked', false);
        }
    });
});

var currentActiveEmp = null;

function loadWeeklySchedule(empId, name, type) {
    // Toggle behavior: if already active, hide it
    if(currentActiveEmp === empId) {
        hideScheduleArea();
        return;
    }

    currentActiveEmp = empId;
    $('#welcomeState').hide();
    $('#scheduleFormArea').removeClass('hidden').show();
    $('#selectedEmpName').text(name);
    $('#form_employee_id').val(empId);

    const badge = $('#typeBadge');
    if(type === 'medical') {
        badge.text('MEDICAL / NURSING').addClass('bg-indigo-50 text-indigo-600 border-indigo-100').removeClass('bg-orange-50 text-orange-600 border-orange-100');
        $('#btnStandardHours').addClass('hidden');
    } else {
        badge.text('MANAGEMENT / NON-MEDIS').addClass('bg-orange-50 text-orange-600 border-orange-100').removeClass('bg-indigo-50 text-indigo-600 border-indigo-100');
        $('#btnStandardHours').removeClass('hidden');
    }

    $('.emp-btn').removeClass('bg-blue-600 text-white shadow-lg shadow-blue-600/20 ring-4 ring-blue-50 border-transparent').addClass('bg-transparent border-transparent');
    $('.emp-btn p:first-child').removeClass('text-white').addClass('text-gray-900');
    $('.emp-btn p:last-child').removeClass('text-white').addClass('text-gray-400');

    $(`#emp-${empId}`).addClass('bg-blue-600 text-white shadow-lg shadow-blue-600/20 ring-4 ring-blue-50').removeClass('border-transparent bg-transparent');
    $(`#emp-${empId} p`).removeClass('text-gray-900 text-gray-400').addClass('text-white');

    $('#scheduleForm')[0].reset();
    $.ajax({
        url: '<?= base_url("schedule/get_employee_weekly_shifts/") ?>' + empId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
             // 1. Set all to OFF by default
             $('.off-checkbox').prop('checked', true);
             
             // 2. Overwrite with shifts from DB
             data.forEach(d => {
                 $(`input[name="schedule[${d.day_of_week}][]"][value="${d.shift_id}"]`).prop('checked', true);
                 // Uncheck OFF for this specific day
                 $(`.off-checkbox[data-day="${d.day_of_week}"]`).prop('checked', false);
             });
        }
    });
}

function hideScheduleArea() {
    currentActiveEmp = null;
    $('#scheduleFormArea').addClass('hidden').hide();
    $('#welcomeState').show();
    $('.emp-btn').removeClass('bg-blue-600 text-white shadow-lg shadow-blue-600/20 ring-4 ring-blue-50 border-transparent').addClass('bg-transparent border-transparent');
    $('.emp-btn p:first-child').removeClass('text-white').addClass('text-gray-900');
    $('.emp-btn p:last-child').removeClass('text-white').addClass('text-gray-400');
}

function setStandardHours() {
    $('input[type="radio"][value=""]').prop('checked', true);
    let morningShiftId = "";
    $('input[type="radio"]').each(function() {
        let shName = $(this).data('shname') || "";
        if(shName.toLowerCase().includes('08:00') || shName.toLowerCase().includes('morning') || shName.toLowerCase().includes('pagi')) {
            morningShiftId = $(this).val();
            return false;
        }
    });

    if(!morningShiftId) {
        Swal.fire('Opps', 'Pagi/Morning shift not found!', 'warning');
        return;
    }

    ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'].forEach(day => {
        $(`input[name="schedule[${day}]"][value="${morningShiftId}"]`).prop('checked', true);
    });
    Toast.fire({icon: 'info', title: 'Senin-Jumat Pagi diterapkan.'});
}

function saveWeeklySchedule() {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    
    // Disable and Show Loading
    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-outlined animate-spin">sync</span> MEMPROSES...`;

    $.ajax({
        url: '<?= base_url("schedule/save_weekly_schedule") ?>',
        type: 'POST',
        data: $('#scheduleForm').serialize(),
        dataType: 'json',
        success: function(res) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            Toast.fire({icon: 'success', title: res.message});
            hideScheduleArea();
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            Toast.fire({icon: 'error', title: 'Terjadi kesalahan sistem'});
        }
    });
}
</script>
