<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <a class="hover:text-primary" href="<?= base_url('payroll') ?>">Kelola Gaji</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary"><?= $period_name ?></span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] text-3xl font-black leading-tight tracking-tight">Detail Pegawai Bulan <?= $period_name ?></h1>
            <p class="text-gray-500 text-sm mt-1">Daftar slip gaji per individu yang dikalkulasi otomatis.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-xl shadow-sm">
            <?php 
                $st = $period->status;
                if($st == 'draft') echo '<span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined !text-[16px]">edit_note</span> DRAFT</span>';
                else if($st == 'approved') echo '<span class="px-3 py-1 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined !text-[16px]">verified</span> APPROVED</span>';
                else if($st == 'paid') echo '<span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined !text-[16px]">task_alt</span> PAID</span>';
            ?>
            
            <?php if($st == 'draft'): ?>
            <button onclick="changeStatus('approved')" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow hover:bg-blue-700 transition flex items-center gap-2"><span class="material-symbols-outlined !text-[18px]">verified</span> Setujui (Approved)</button>
            <?php elseif($st == 'approved'): ?>
            
            <a href="<?= base_url('payroll/export_sender/'.$period->id) ?>" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-bold shadow hover:bg-purple-700 transition flex items-center gap-2" style="display:none;"><span class="material-symbols-outlined !text-[18px]">quick_zip</span> Unduh WA Sender (ZIP)</a>
            <button onclick="changeStatus('draft')" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-bold border border-red-200 hover:bg-red-100 transition flex items-center gap-2"><span class="material-symbols-outlined !text-[18px]">undo</span> Batal Approved</button>
            <button onclick="broadcastEmail()" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-bold shadow hover:bg-green-600 transition flex items-center gap-2"><span class="material-symbols-outlined !text-[18px]">mail</span> Broadcast via Email</button>
            <button onclick="changeStatus('paid')" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow hover:bg-blue-700 transition flex items-center gap-2"><span class="material-symbols-outlined !text-[18px]">payments</span> Tandai Dibayar</button>
            
            <?php elseif($st == 'paid'): ?>
            
            <a href="<?= base_url('payroll/export_sender/'.$period->id) ?>" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-bold shadow hover:bg-purple-700 transition flex items-center gap-2" style="display:none;"><span class="material-symbols-outlined !text-[18px]">quick_zip</span> Unduh WA Sender (ZIP)</a>
            <button onclick="broadcastEmail()" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-bold shadow hover:bg-green-600 transition flex items-center gap-2"><span class="material-symbols-outlined !text-[18px]">mail</span> Broadcast via Email</button>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white rounded-xl shadow-sm border border-[#e5e7eb] overflow-hidden p-4">
        <table class="w-full text-left border-collapse" id="slipsTable">
            <thead>
                <tr class="bg-gray-50 border-b border-[#e5e7eb]">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Informasi Pegawai</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jabatan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Total Hadir</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Gaji Bersih (THP)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Aksi Slip</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e5e7eb]">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal View Detail -->
<div id="slipDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
        <div class="relative bg-white rounded-2xl w-full max-w-lg shadow-2xl p-0 transform transition-all overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 p-6 text-white flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-black" id="mdl_emp_name">-</h3>
                    <p class="text-blue-200 text-sm mt-0.5 font-medium" id="mdl_emp_info">-</p>
                </div>
                <button onclick="closeDetailModal()" class="text-white hover:text-blue-100 bg-blue-700 hover:bg-blue-800 p-1.5 rounded-lg transition-all"><span class="material-symbols-outlined !text-[20px] block">close</span></button>
            </div>
            
            <div class="p-6 bg-gray-50/50">
                <div class="flex justify-between items-center mb-6 py-3 px-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest" id="mdl_period">Bulan</span>
                    <div class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined !text-[14px]">event_available</span>
                        Hadir: <span id="mdl_att">-</span>
                    </div>
                </div>

                <!-- Basic -->
                <div class="mb-4">
                    <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 px-1">GAJI POKOK</h4>
                    <div class="bg-white p-3 rounded-xl border border-gray-200 flex justify-between shadow-sm">
                        <span class="text-gray-700 font-bold text-sm">Gaji Pokok Utama</span>
                        <span class="font-black text-gray-900" id="mdl_basic">Rp 0</span>
                    </div>
                </div>

                <!-- Allowances -->
                <div class="mb-4">
                    <h4 class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1.5 px-1 flex items-center gap-1"><span class="material-symbols-outlined !text-[14px]">add_circle</span> TAMBAHAN / TUNJANGAN</h4>
                    <div class="bg-white p-3 rounded-xl border border-green-100 shadow-sm">
                        <div id="mdl_allowances" class="mb-2"></div>
                        <div class="border-t border-dashed border-gray-200 pt-2 flex justify-between font-bold">
                            <span class="text-green-700 text-sm">Total Tambahan</span>
                            <span class="text-green-700" id="mdl_total_allow">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="mb-6">
                    <h4 class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1.5 px-1 flex items-center gap-1"><span class="material-symbols-outlined !text-[14px]">remove_circle</span> POTONGAN</h4>
                    <div class="bg-white p-3 rounded-xl border border-red-100 shadow-sm">
                        <div id="mdl_deductions" class="mb-2"></div>
                        <div class="border-t border-dashed border-gray-200 pt-2 flex justify-between font-bold">
                            <span class="text-red-700 text-sm">Total Potongan</span>
                            <span class="text-red-700" id="mdl_total_deduct">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Net Salary -->
                <div class="bg-blue-600 p-4 rounded-xl shadow-md flex justify-between items-center text-white">
                    <div>
                        <div class="text-[10px] font-bold text-blue-200 uppercase tracking-widest mb-0.5">Pendapatan Bersih (THP)</div>
                        <div class="text-xs text-blue-100">Take Home Pay</div>
                    </div>
                    <div class="text-2xl font-black tracking-tight" id="mdl_net_salary">Rp 0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var table;
    $(document).ready(function() {
        table = $('#slipsTable').DataTable({
            "ajax": "<?= base_url('payroll/get_slips_json/'.$period->id) ?>",
            "language": { "emptyTable": "Belum ada detail slip gaji." },
            "columnDefs": [
                { "orderable": false, "targets": [5] }
            ]
        });
    });

    function changeStatus(new_status) {
        let msg = '';
        if(new_status === 'approved') msg = 'Setujui draft penggajian ini? Slip akan dicetak final untuk pegawai.';
        else if(new_status === 'draft') msg = 'Batalkan approval? Anda akan dapat mengedit atau men-generate ulang slip yang salah.';
        else msg = 'Tandai bahwa semua gaji telah ditransfer?';
        
        Swal.fire({
            title: new_status === 'draft' ? 'Batal Approved?' : 'Ubah Status Penggajian',
            text: msg,
            icon: new_status === 'draft' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: new_status === 'draft' ? '#dc2626' : '#2563eb',
            confirmButtonText: 'Ya, Lanjutkan'
        }).then((result) => {
            if(result.isConfirmed) {
                $.post('<?= base_url("payroll/change_status") ?>', { id: <?= $period->id ?>, status: new_status }, function(res) {
                    let r = JSON.parse(res);
                    if(r.status == 'success') {
                        Toast.fire({icon: 'success', title: r.message});
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire('Error', r.message, 'error');
                    }
                });
            }
        });
    }

    function recalcIndividual(slip_id, employee_id) {
        Swal.fire({
            title: 'Kalkulasi Ulang?',
            text: 'Tarik ulang data gaji terbaru (Setup Pokok/Tunjangan/Potongan yang diperbarui) untuk pegawai ini Saja?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ya, Hitung Ulang'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url("payroll/recalculate_individual") ?>', { slip_id: slip_id, employee_id: employee_id, period_id: <?= $period->id ?> }, function(res) {
                    try {
                        let r = JSON.parse(res);
                        if(r.status === 'success') {
                            Toast.fire({icon: 'success', title: r.message});
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', r.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error', 'Gagal memproses feedback server.', 'error');
                    }
                });
            }
        });
    }

    function broadcastEmail() {
        Swal.fire({
            title: 'Kirim Slip via Email?',
            html: 'Sistem akan otomatis me-generate PDF dan mengirimkannya langsung sebagai attachment (lampiran) ke email resmi setiap pegawai satu per satu.<br><br><span class="text-sm font-bold text-gray-700" id="emailProgress"></span>',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 !text-[18px]">mail</span> Mulai Broadcast Email',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading swal murni
                Swal.fire({
                    title: 'Memproses Pengiriman...',
                    html: '<div id="realEmailProgress">Menyiapkan data penerima...</div><br><progress id="emailProgressBar" class="w-full" value="0" max="100"></progress>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });

                $.get('<?= base_url("payroll/get_slips_raw/".$period->id) ?>', async function(response) {
                    try {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        // Filter out employees without email
                        let targets = res.data.filter(s => s.email && s.email !== '');
                        let total = targets.length;
                        
                        if(total > 0) {
                            let sentCount = 0;
                            let errorCount = 0;

                            for(let i = 0; i < total; i++) {
                                let slip = targets[i];
                                $('#realEmailProgress').text(`Mengirim email ke ${slip.full_name} (${i+1}/${total})...`);
                                $('#emailProgressBar').val(((i) / total) * 100);
                                
                                try {
                                    let req = await $.post('<?= base_url("payroll/send_email_single") ?>', { slip_id: slip.id });
                                    let r = typeof req === 'string' ? JSON.parse(req) : req;
                                    if(r.status === 'success') {
                                        sentCount++;
                                    } else {
                                        errorCount++;
                                        console.error('Err sending to', slip.email, r.message);
                                    }
                                } catch (err) {
                                    errorCount++;
                                }
                                
                                // Jeda 1.5 detik agar mesin memori stabil dan Gmail tidak memblokir karena spamming
                                await new Promise(r => setTimeout(r, 1500));
                            }
                            
                            $('#emailProgressBar').val(100);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Broadcast Email Selesai!',
                                html: `<b>Berhasil terkirim:</b> ${sentCount} Email<br><b>Gagal:</b> ${errorCount} Email`,
                            });
                            
                        } else {
                            Swal.fire('Kosong', 'Tidak ada profil pegawai dengan alamat email yang valid untuk dikirimi.', 'warning');
                        }
                    } catch(e) {
                         Swal.fire('Error', 'Gagal memproses pengiriman data.', 'error');
                    }
                });
            }
        });
    }

    function sendSingleEmail(slipId) {
        Swal.fire({
            title: 'Kirim Email?',
            text: 'Slip gaji akan di-generate PDF dan dikirimkan ke email pegawai ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Email',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.post('<?= base_url("payroll/send_email_single") ?>', { slip_id: slipId })
                    .then(response => {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if(res.status !== 'success') {
                            throw new Error(res.message || 'Gagal mengirim email');
                        }
                        return res;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Terkirim!',
                    text: 'Slip gaji telah sukses dilesatkan ke email tujuan.',
                    timer: 2000
                });
            }
        });
    }

    // Modal & JS View Slip Detail
    function viewSlipDetail(slip_id) {
        $.getJSON('<?= base_url("payroll/get_slip_detail/") ?>' + slip_id, function(res) {
            if(res.status == 'success') {
                let slip = res.slip;
                let details = res.details;
                
                $('#mdl_emp_name').text(slip.full_name);
                $('#mdl_emp_info').text(slip.nip + ' • ' + (slip.position_name || '-'));
                $('#mdl_period').text('Bulan ' + slip.period_name);
                
                $('#mdl_att').text(slip.attendance_count + ' Hari');
                $('#mdl_basic').text(formatRupiahJS(slip.basic_salary));
                
                let htmlAllow = '';
                let htmlDeduct = '';
                
                details.forEach(function(d) {
                    if(d.type == 'allowance') {
                        htmlAllow += `<div class="flex justify-between items-center text-sm py-1 border-b border-gray-100 last:border-0"><span class="text-gray-600">${d.description}</span><span class="font-bold text-gray-900">${formatRupiahJS(d.amount)}</span></div>`;
                    } else {
                        htmlDeduct += `<div class="flex justify-between items-center text-sm py-1 border-b border-gray-100 last:border-0"><span class="text-gray-600">${d.description}</span><span class="font-bold text-gray-900">${formatRupiahJS(d.amount)}</span></div>`;
                    }
                });
                
                if(htmlAllow == '') htmlAllow = '<div class="text-xs text-gray-400 italic py-1">Tidak ada tambahan</div>';
                if(htmlDeduct == '') htmlDeduct = '<div class="text-xs text-gray-400 italic py-1">Tidak ada potongan</div>';
                
                $('#mdl_allowances').html(htmlAllow);
                $('#mdl_deductions').html(htmlDeduct);
                
                $('#mdl_total_allow').text(formatRupiahJS(slip.total_allowance));
                $('#mdl_total_deduct').text(formatRupiahJS(slip.total_deduction));
                $('#mdl_net_salary').text(formatRupiahJS(slip.net_salary));

                $('#slipDetailModal').removeClass('hidden');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    }

    function closeDetailModal() {
        $('#slipDetailModal').addClass('hidden');
    }

    function formatRupiahJS(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }
</script>
