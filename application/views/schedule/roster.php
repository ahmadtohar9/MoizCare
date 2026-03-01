<!-- Page Header -->
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary font-bold">Roster Management</span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Roster Global Unit</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola rotasi dan pembagian tugas staf secara visual.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="$('#modalAssign').removeClass('hidden')" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20 active:scale-95">
                <span class="material-symbols-outlined text-lg">add_task</span>
                Tambah Penugasan
            </button>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mt-6">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-white/5 p-8 shadow-sm">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Shift Legend</h4>
                <div class="space-y-3">
                    <?php foreach($shifts as $s): ?>
                    <div class="flex items-center gap-3">
                        <div class="size-4 rounded-lg flex-shrink-0" style="background-color: <?= $s->color ?>;"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase leading-none"><?= $s->name ?></p>
                            <p class="text-[9px] text-gray-400 font-bold mt-1"><?= substr($s->start_time, 0, 5) ?> - <?= substr($s->end_time, 0, 5) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-10 pt-8 border-t border-gray-50 dark:border-white/5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Cari Staf</label>
                    <input type="text" id="staffFilter" placeholder="Nama atau NIP..." class="w-full rounded-2xl border-none bg-gray-50 dark:bg-white/5 font-bold text-xs px-5 py-3.5 focus:ring-2 focus:ring-blue-500">
                </div>
                
                <?php if($this->session->userdata('role') === 'admin'): ?>
                <div class="mt-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Pilih Unit</label>
                    <select id="unitFilter" onchange="window.location.href='?unit_id='+this.value" class="w-full rounded-2xl border-none bg-gray-50 dark:bg-white/5 font-bold text-xs px-5 py-3.5 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Unit</option>
                        <?php foreach($units as $u): ?>
                            <option value="<?= $u->id ?>" <?= $this->input->get('unit_id') == $u->id ? 'selected' : '' ?>><?= $u->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <!-- Stats Helper -->
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <span class="material-symbols-outlined !text-[32px] mb-4">analytics</span>
                    <h5 class="text-lg font-black leading-tight mb-2">Pemerataan Tabular</h5>
                    <p class="text-xs font-bold opacity-60">Selesaikan penugasan bulan ini sebelum tanggal 25 untuk sinkronisasi payroll.</p>
                </div>
                <div class="absolute -right-8 -bottom-8 size-32 bg-white/10 rounded-full blur-2xl"></div>
            </div>
        </div>

        <!-- Calendar Area -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-900 rounded-[3rem] border border-gray-100 dark:border-white/5 shadow-2xl p-8 overflow-hidden">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Assign -->
<div id="modalAssign" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[999] flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-gray-900 rounded-[3rem] w-full max-w-lg overflow-hidden shadow-2xl animate-in zoom-in duration-200">
        <div class="p-10">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">Task Assignment</h3>
                    <p class="text-xs text-gray-500 font-bold mt-1">Tentukan shift untuk staf terpilih.</p>
                </div>
                <button onclick="$('#modalAssign').addClass('hidden')" class="size-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form id="formAssign" class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Pegawai</label>
                    <select name="employee_id" class="w-full rounded-2xl border-none bg-gray-50 dark:bg-white/5 font-black px-6 py-4 focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Anggota Tim --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?= $emp->id ?>"><?= $emp->full_name ?> (<?= $emp->nip ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal Tugas</label>
                    <input type="date" name="date" class="w-full rounded-2xl border-none bg-gray-50 dark:bg-white/5 font-black px-6 py-4 focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Shift</label>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <?php foreach($shifts as $s): ?>
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 border-gray-50 cursor-pointer hover:border-blue-200 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 transition-all">
                            <input type="radio" name="shift_id" value="<?= $s->id ?>" class="absolute top-4 right-4 text-blue-600 focus:ring-0" required>
                            <span class="text-[10px] font-black uppercase text-gray-500 mb-1"><?= $s->name ?></span>
                            <span class="text-xs font-bold text-gray-900"><?= substr($s->start_time, 0, 5) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-4 bg-gray-900 hover:bg-black text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-gray-200 active:scale-95 transition-all">
                        Simpan Roster
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FullCalendar Integration -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
    .fc { font-family: 'Inter', sans-serif; --fc-border-color: #f3f4f6; }
    .fc .fc-toolbar-title { font-weight: 900; text-transform: uppercase; letter-spacing: -0.02em; font-size: 1.25rem; color: #111827; }
    .fc .fc-button-primary { background: #f9fafb; border: none; color: #374151; font-weight: 800; font-size: 11px; text-transform: uppercase; padding: 10px 20px; border-radius: 12px; }
    .fc .fc-button-primary:hover { background: #f3f4f6; color: #111827; }
    .fc .fc-button-primary:disabled { background: #f9fafb; opacity: 0.5; }
    .fc .fc-col-header-cell-cushion { padding: 15px 0; font-size: 10px; font-weight: 900; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.1em; }
    .fc-event { border-radius: 8px; border: none; padding: 2px 6px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); cursor: pointer; transition: all 0.2s; }
    .fc-event:hover { transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
    .fc-event .fc-event-title { font-weight: 800; font-size: 10px; text-transform: uppercase; }
    .fc-day-today { background-color: #f0f9ff !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: '<?= base_url("schedule/get_roster_json") ?>?unit_id=<?= $this->input->get('unit_id') ?>',
            eventClick: function(info) {
                Swal.fire({
                    title: 'Hapus Tugas?',
                    text: "Tugas " + info.event.title + " akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus!',
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl font-black px-6 py-3 uppercase text-xs' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.get('<?= base_url("schedule/delete_assignment/") ?>' + info.event.id, function(res) {
                            calendar.refetchEvents();
                            Swal.fire('Terhapus!', 'Penugasan telah dihapus.', 'success');
                        });
                    }
                });
            },
            dateClick: function(info) {
                $('[name="date"]').val(info.dateStr);
                $('#modalAssign').removeClass('hidden');
            }
        });
        calendar.render();

        $('#formAssign').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            $.post('<?= base_url("schedule/save_assignment") ?>', $(this).serialize(), function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    $('#modalAssign').addClass('hidden');
                    calendar.refetchEvents();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                    $('#formAssign')[0].reset();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
                btn.prop('disabled', false).text('Simpan Roster');
            });
        });

        // Simple real-time search on events
        $('#staffFilter').on('input', function() {
            const query = $(this).val().toLowerCase();
            document.querySelectorAll('.fc-event').forEach(eventEl => {
                const text = eventEl.innerText.toLowerCase();
                eventEl.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
</script>
