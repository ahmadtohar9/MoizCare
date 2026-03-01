<?php
$today = date('Y-m-d');
?>
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Berkas Pegawai</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Daftar Berkas</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manajemen dokumen digital dan arsip kepegawaian.</p>
        </div>
        <div class="flex gap-3">
             <a href="<?= base_url('documents/types') ?>" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm hover:bg-gray-50">
                <span class="material-symbols-outlined text-lg">settings</span>
                <span>Atur Jenis Berkas</span>
            </a>
            <button onclick="openUploadModal()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                <span class="material-symbols-outlined text-lg">upload_file</span>
                <span>Upload Berkas Baru</span>
            </button>
        </div>
    </div>
</div>

<div class="px-6 pb-10">
    <!-- Stats Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-3xl shadow-xl shadow-blue-500/10 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-125 transition-transform">
                <span class="material-symbols-outlined !text-[100px]">folder_special</span>
            </div>
            <p class="text-blue-100 text-xs font-black uppercase tracking-widest mb-1">Total Berkas Upload</p>
            <h3 class="text-4xl font-black mb-1"><?= $this->db->count_all('employee_documents') ?></h3>
            <p class="text-blue-200 text-[10px] font-bold">Tersebar di <?= count($employees) ?> Pegawai</p>
        </div>

        <div class="bg-white border border-gray-100 p-6 rounded-3xl shadow-sm flex items-center justify-between group hover:border-amber-200 transition-colors">
            <div>
                 <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Akan Segera Expired</p>
                 <h3 class="text-3xl font-black text-amber-500"><?= $warning_count ?></h3>
                 <p class="text-gray-400 text-[10px] font-medium mt-1">Dalam 30 hari ke depan</p>
            </div>
            <div class="size-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
        </div>

        <div class="bg-white border border-gray-100 p-6 rounded-3xl shadow-sm flex items-center justify-between group hover:border-red-200 transition-colors">
            <div>
                 <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Berkas Expired</p>
                 <h3 class="text-3xl font-black text-red-600"><?= $expired_count ?></h3>
                 <p class="text-gray-400 text-[10px] font-medium mt-1">Memerlukan update segera</p>
            </div>
            <div class="size-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-2xl">error</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input type="text" id="employeeSearch" placeholder="Cari nama pegawai atau NIP..." class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-gray-100 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
        </div>
        <select id="unitFilter" class="w-full md:w-64 py-3.5 px-4 rounded-2xl border-gray-100 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 font-bold text-gray-700">
            <option value="">Semua Departemen</option>
            <?php foreach($units as $u): ?>
                <option value="<?= $u->name ?>"><?= $u->name ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Employee Berkas Grid -->
    <div id="employeeGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($employees as $emp): ?>
        <div class="employee-card bg-white rounded-3xl p-6 border border-gray-50 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group" 
             data-name="<?= strtolower($emp->full_name) ?>" 
             data-nip="<?= $emp->nip ?>" 
             data-unit="<?= $emp->unit_name ?>">
            
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-2xl overflow-hidden ring-4 ring-gray-50 group-hover:ring-blue-100 transition-all">
                        <img src="<?= $emp->photo_url ?>" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-gray-900 leading-tight"><?= $emp->full_name ?></h4>
                        <p class="text-xs text-blue-600 font-black uppercase tracking-wider mt-0.5"><?= $emp->nip ?></p>
                        <p class="text-[10px] text-gray-400 font-bold mt-1"><?= $emp->unit_name ?> • <?= $emp->position_name ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4 mb-6">
                <div class="flex items-center justify-between mb-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                    <span>Dokumen Digital</span>
                    <span class="text-blue-600 font-black"><?= $emp->doc_count ?> Berkas</span>
                </div>
                <div class="flex -space-x-2">
                    <?php if($emp->doc_count > 0): ?>
                        <div class="size-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center border-2 border-white ring-1 ring-blue-200">
                            <span class="material-symbols-outlined text-lg">description</span>
                        </div>
                        <div class="size-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center border-2 border-white ring-1 ring-green-200">
                            <span class="material-symbols-outlined text-lg">verified</span>
                        </div>
                    <?php else: ?>
                        <p class="text-[10px] text-gray-300 italic font-medium">Belum ada dokumen yang diupload</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="viewEmployeeDocs(<?= $emp->id ?>)" class="flex-1 bg-gray-900 hover:bg-black text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                    Detail Berkas
                </button>
                <button onclick="quickUpload(<?= $emp->id ?>, '<?= $emp->full_name ?>')" class="size-11 rounded-2xl border border-gray-100 text-gray-400 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-100 transition-all flex items-center justify-center">
                    <span class="material-symbols-outlined">add</span>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden py-20 text-center">
        <span class="material-symbols-outlined text-6xl text-gray-200">person_off</span>
        <h4 class="text-lg font-black text-gray-400 mt-4 uppercase tracking-widest">Pegawai Tidak Ditemukan</h4>
        <p class="text-sm text-gray-300">Coba gunakan kata kunci pencarian lain.</p>
    </div>
</div>

<!-- Modal Dossier Detail (Reusing the premium look) -->
<div id="viewModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-10">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeViewModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden transform transition-all border border-gray-100">
            <!-- Header -->
            <div class="p-8 border-b border-gray-100 relative">
                <button onclick="closeViewModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <div class="size-24 rounded-2xl overflow-hidden ring-4 ring-gray-50 shadow-md">
                        <img id="detail_photo" src="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 text-center md:text-left pt-2">
                        <h2 id="detail_name" class="text-2xl font-black text-gray-900 tracking-tight">Nama Pegawai</h2>
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-y-1 gap-x-4 text-xs font-bold text-gray-500 mt-1 uppercase tracking-widest">
                            <span id="detail_nip">NIP: -</span>
                            <span class="text-gray-300">•</span>
                            <span id="detail_unit">-</span>
                        </div>
                    </div>
                    <div class="flex gap-2 pb-2">
                        <button id="btn_manage_full" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-500/20">Checklist Akreditasi</button>
                    </div>
                </div>
            </div>
            
            <!-- Document Preview Feed -->
            <div class="p-10 max-h-[70vh] overflow-y-auto bg-gray-50/50">
                <h4 class="font-black text-gray-400 text-[10px] uppercase tracking-[0.2em] mb-8 border-l-4 border-blue-600 pl-4">Digital Document Feed</h4>
                <div id="document_feed" class="space-y-12">
                     <!-- Ajax Load Previews -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal (Simplified) -->
<div id="uploadModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeUploadModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8">
            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-2 rounded-xl">upload_file</span>
                Upload Dokumen Baru
            </h3>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Pegawai</label>
                    <select name="employee_id" id="empSelect" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold focus:ring-2 focus:ring-blue-500">
                         <option value="">-- Cari Pegawai --</option>
                         <?php foreach($employees as $emp): ?>
                            <option value="<?= $emp->id ?>"><?= $emp->nip ?> - <?= $emp->full_name ?></option>
                         <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Dokumen</label>
                    <select name="document_type" id="typeSelect" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold focus:ring-2 focus:ring-blue-500">
                        <?php foreach($doc_types as $type): ?>
                            <option value="<?= $type->id ?>"><?= $type->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih File</label>
                    <div class="border-2 border-dashed border-gray-100 rounded-3xl p-6 text-center hover:border-blue-200 transition-all group">
                         <input type="file" name="file_upload" id="file_upload" class="hidden" required onchange="updateFileName(this)">
                         <label for="file_upload" class="cursor-pointer">
                              <span class="material-symbols-outlined text-4xl text-gray-200 group-hover:text-blue-500 transition-colors">cloud_upload</span>
                              <p id="fileName" class="text-xs text-gray-500 mt-2">Pilih PDF atau Gambar (Max 5MB)</p>
                         </label>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest py-4 rounded-2xl shadow-lg shadow-blue-500/20 transition-all">Upload Berkas</button>
                    <button type="button" onclick="closeUploadModal()" class="px-8 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xs uppercase tracking-widest py-4 rounded-2xl transition-all">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Live Search
    $('#employeeSearch').on('keyup', filterGrid);
    $('#unitFilter').on('change', filterGrid);
});

function filterGrid() {
    let search = $('#employeeSearch').val().toLowerCase();
    let unit = $('#unitFilter').val();
    let visibleCount = 0;

    $('.employee-card').each(function() {
        let name = $(this).data('name');
        let nip = $(this).data('nip');
        let cardUnit = $(this).data('unit');

        let matchesSearch = name.includes(search) || nip.includes(search);
        let matchesUnit = unit === "" || cardUnit === unit;

        if(matchesSearch && matchesUnit) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    if(visibleCount == 0) $('#emptyState').show(); else $('#emptyState').hide();
}

function viewEmployeeDocs(empId) {
    $.ajax({
        url: '<?= base_url("employee/get_full_employee_detail/") ?>' + empId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const emp = data.employee;
            const docs = data.documents;

            $('#detail_photo').attr('src', emp.photo_url);
            $('#detail_name').text(emp.full_name);
            $('#detail_nip').text('NIP: ' + emp.nip);
            $('#detail_unit').text(emp.unit_name);
            
            $('#btn_manage_full').attr('onclick', `window.location.href='<?= base_url() ?>employee_documents/index/${empId}'`);

            let docHtml = '';
            if(docs.length === 0) {
                docHtml = '<div class="p-20 text-center bg-gray-50/50 rounded-3xl border-2 border-dashed border-gray-100"><span class="material-symbols-outlined text-5xl text-gray-200">folder_off</span><p class="text-sm text-gray-400 mt-4 font-bold uppercase tracking-widest">No Documents Found</p></div>';
            } else {
                docs.forEach(d => {
                    const ext = d.file_path.split('.').pop().toLowerCase();
                    const url = '<?= base_url() ?>' + d.file_path;
                    let preview = '';
                    if(['jpg','jpeg','png'].includes(ext)) {
                        preview = `<img src="${url}" class="w-full rounded-2xl">`;
                    } else if(ext === 'pdf') {
                        preview = `<iframe src="${url}#toolbar=0" class="w-full h-[500px] rounded-2xl" frameborder="0"></iframe>`;
                    }

                    docHtml += `
                    <div class="bg-white rounded-3xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold font-mono text-xs">DOC</div>
                                <div>
                                    <h5 class="text-sm font-black text-gray-900">${d.type_name || 'Dokumen Pegawai'}</h5>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Uploaded: ${d.uploaded_at.split(' ')[0]}</p>
                                </div>
                            </div>
                            <button onclick="deleteDoc(${d.id})" class="text-gray-300 hover:text-red-500 transition-colors"><span class="material-symbols-outlined">delete</span></button>
                        </div>
                        <div class="bg-gray-50 rounded-2xl overflow-hidden">${preview}</div>
                    </div>`;
                });
            }
            $('#document_feed').html(docHtml);
            $('#viewModal').removeClass('hidden').css('display', 'block');
        }
    });
}

function openUploadModal() { $('#uploadModal').removeClass('hidden').show(); }
function closeUploadModal() { $('#uploadModal').addClass('hidden').hide(); }
function closeViewModal() { $('#viewModal').addClass('hidden').hide(); }

function quickUpload(id, name) {
    $('#empSelect').val(id);
    openUploadModal();
}

function updateFileName(input) {
    if(input.files && input.files[0]) {
        $('#fileName').text(input.files[0].name).addClass('text-blue-600 font-bold');
    }
}

$('#uploadForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
        url: '<?= base_url("documents/store") ?>',
        type: 'POST',
        data: formData,
        processData: false, contentType: false, dataType: 'json',
        success: function(resp) {
            if(resp.status === 'success') {
                 Toast.fire({icon: 'success', title: resp.message});
                 location.reload(); 
            } else {
                 Swal.fire('Gagal', resp.message, 'error');
            }
        }
    });
});

function deleteDoc(id) {
    Swal.fire({ title: 'Hapus Berkas?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus' })
    .then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("documents/delete/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        Toast.fire({icon: 'success', title: resp.message});
                        location.reload();
                    }
                }
            });
        }
    });
}
</script>
