<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                <a class="hover:text-primary" href="<?= base_url('employee') ?>">← Kembali ke Data Pegawai</a>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Dokumen Pegawai</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="font-bold"><?= $employee->full_name ?></span> (NIP: <?= $employee->nip ?>) 
                | Kategori: <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-bold"><?= ucfirst($employee_category) ?></span>
            </p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-4">
            <button onclick="switchDocTab('mandatory')" id="tab-btn-mandatory" class="doc-tab-btn border-b-2 border-primary text-primary px-4 py-3 font-bold text-sm transition-all">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">task_alt</span>
                    Berkas Wajib (<?= $mandatory_total ?>)
                </span>
            </button>
            <button onclick="switchDocTab('supporting')" id="tab-btn-supporting" class="doc-tab-btn border-b-2 border-transparent text-gray-500 px-4 py-3 font-bold text-sm transition-all hover:text-gray-700">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">folder_open</span>
                    Berkas Pendukung (<?= $supporting_total ?>)
                </span>
            </button>
        </nav>
    </div>

    <!-- TAB 1: Berkas Wajib -->
    <div id="tab-mandatory" class="doc-tab-content">
        <!-- Progress Card -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 mb-6 text-white shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-sm font-semibold mb-1">Kelengkapan Berkas Akreditasi</p>
                    <h3 class="text-3xl font-black"><?= $uploaded_count ?> / <?= $mandatory_total ?> Dokumen</h3>
                    <p class="text-blue-100 text-xs mt-1"><?= round(($uploaded_count/$mandatory_total)*100) ?>% Lengkap</p>
                </div>
                <div class="text-right">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                        <span class="material-symbols-outlined text-5xl">verified</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 bg-white/20 rounded-full h-2 overflow-hidden">
                <div class="bg-white h-full transition-all" style="width: <?= round(($uploaded_count/$mandatory_total)*100) ?>%"></div>
            </div>
        </div>

        <!-- Checklist Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <h3 class="font-black text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">checklist</span>
                    Daftar Berkas Wajib
                </h3>
                <div class="space-y-3">
                    <?php foreach($mandatory_docs as $doc): ?>
                        <?php 
                            $uploaded = isset($doc->uploaded) && $doc->uploaded;
                            $expired = false;
                            if($uploaded && $doc->has_expiry && !empty($doc->expiry_date)) {
                                $expired = strtotime($doc->expiry_date) < time();
                            }
                        ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg <?= $uploaded ? ($expired ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200') : 'bg-gray-50 border-gray-200' ?>">
                            <div class="flex items-center gap-3">
                                <?php if($uploaded): ?>
                                    <?php if($expired): ?>
                                        <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-gray-400 text-2xl">radio_button_unchecked</span>
                                <?php endif; ?>
                                
                                <div>
                                    <p class="font-bold text-gray-900"><?= $doc->name ?></p>
                                    <?php if($uploaded): ?>
                                        <p class="text-xs text-gray-500">
                                            Upload: <?= date('d M Y', strtotime($doc->uploaded_at)) ?>
                                            <?php if($doc->has_expiry && $doc->expiry_date): ?>
                                                | Expired: <?= date('d M Y', strtotime($doc->expiry_date)) ?>
                                                <?php if($expired): ?>
                                                    <span class="text-red-600 font-bold">⚠️ EXPIRED!</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-xs text-gray-500">Belum diupload</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <?php if($uploaded): ?>
                                    <a href="<?= base_url($doc->file_path) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-500 text-white text-xs font-bold hover:bg-blue-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">visibility</span> Lihat
                                    </a>
                                    <button onclick="uploadMandatory(<?= $doc->type_id ?>, '<?= $doc->name ?>', <?= $doc->has_expiry ?>, <?= $doc->doc_id ?>)" class="px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">upload</span> Upload Ulang
                                    </button>
                                <?php else: ?>
                                    <button onclick="uploadMandatory(<?= $doc->type_id ?>, '<?= $doc->name ?>', <?= $doc->has_expiry ?>)" class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">upload</span> Upload
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Berkas Pendukung -->
    <div id="tab-supporting" class="doc-tab-content hidden">
        <div class="mb-4">
            <button onclick="openSupportingModal()" class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-5 rounded-lg shadow-md flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined">add</span>
                Tambah Berkas Pendukung
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4">
                <table class="w-full text-left" id="supportingDocsTable">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Jenis Dokumen</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Nomor</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Tgl Terbit</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Tgl Expired</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Mandatory -->
<div id="mandatoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" onclick="closeMandatoryModal()"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
            <h3 class="text-xl font-black text-gray-900 mb-4" id="mandatory-title">Upload Dokumen</h3>
            <form id="mandatoryForm" enctype="multipart/form-data">
                <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                <input type="hidden" name="document_type_id" id="m_type_id">
                <input type="hidden" name="is_supporting" value="0">
                <input type="hidden" name="id" id="m_doc_id">

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">File Dokumen <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90" required>
                </div>

                <div class="mb-4" id="m_expiry_field" style="display:none;">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Expired <span class="text-red-500">*</span></label>
                    <input type="date" name="expiry_date" id="m_expiry_date" class="w-full rounded-lg border-gray-300">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-2.5 rounded-lg">Upload</button>
                    <button type="button" onclick="closeMandatoryModal()" class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-lg">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supporting (sama seperti sebelumnya, lengkap dengan semua field) -->
<div id="supportingModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" onclick="closeSupportingModal()"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
            <h3 class="text-xl font-black text-gray-900 mb-4">Tambah Berkas Pendukung</h3>
            <form id="supportingForm" enctype="multipart/form-data">
                <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                <input type="hidden" name="is_supporting" value="1">
                <input type="hidden" name="id" id="s_doc_id">

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Dokumen <span class="text-red-500">*</span></label>
                    <select name="document_type_id" id="s_type_id" onchange="handleSupportingTypeChange()" class="w-full rounded-lg border-gray-300" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach($supporting_types as $type): ?>
                            <option value="<?= $type->id ?>" data-has-expiry="<?= $type->has_expiry ?>"><?= $type->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">File Dokumen <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Dokumen</label>
                        <input type="text" name="document_number" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Terbit</label>
                        <input type="date" name="issue_date" class="w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <div class="mb-4 hidden" id="s_expiry_field">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Expired</label>
                    <input type="date" name="expiry_date" id="s_expiry_date" class="w-full rounded-lg border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Penyelenggara/Penerbit</label>
                    <input type="text" name="issuer" class="w-full rounded-lg border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-2.5 rounded-lg">Simpan</button>
                    <button type="button" onclick="closeSupportingModal()" class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-lg">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var supportingTable;

$(document).ready(function() {
    supportingTable = $('#supportingDocsTable').DataTable({
        "ajax": "<?= base_url('employee_documents/get_supporting_json/'.$employee_id) ?>",
        "responsive": false,
        "autoWidth": false,
        "destroy": true,
        "language": { "search": "Cari:", "emptyTable": "Belum ada berkas pendukung" }
    });
});

function switchDocTab(tab) {
    $('.doc-tab-content').addClass('hidden');
    $('.doc-tab-btn').removeClass('border-primary text-primary').addClass('border-transparent text-gray-500');
    
    $('#tab-' + tab).removeClass('hidden');
    $('#tab-btn-' + tab).addClass('border-primary text-primary').removeClass('border-transparent text-gray-500');
}

function uploadMandatory(typeId, typeName, hasExpiry, docId = null) {
    $('#m_type_id').val(typeId);
    $('#m_doc_id').val(docId || '');
    $('#mandatory-title').text((docId ? 'Upload Ulang: ' : 'Upload: ') + typeName);
    
    if(hasExpiry == 1) {
        $('#m_expiry_field').show();
        $('#m_expiry_date').attr('required', true);
    } else {
        $('#m_expiry_field').hide();
        $('#m_expiry_date').attr('required', false);
    }
    
    $('#mandatoryModal').removeClass('hidden');
}

function closeMandatoryModal() {
    $('#mandatoryModal').addClass('hidden');
    $('#mandatoryForm')[0].reset();
}

$('#mandatoryForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '<?= base_url("employee_documents/store") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                Toast.fire({icon: 'success', title: response.message});
                closeMandatoryModal();
                location.reload(); // Reload untuk update progress
            } else {
                Swal.fire({icon: 'error', title: 'Gagal', html: response.message});
            }
        }
    });
});

function openSupportingModal() {
    $('#supportingModal').removeClass('hidden');
}

function closeSupportingModal() {
    $('#supportingModal').addClass('hidden');
    $('#supportingForm')[0].reset();
}

function handleSupportingTypeChange() {
    const select = document.getElementById('s_type_id');
    const hasExpiry = select.options[select.selectedIndex].getAttribute('data-has-expiry');
    
    if(hasExpiry == '1') {
        $('#s_expiry_field').removeClass('hidden');
    } else {
        $('#s_expiry_field').addClass('hidden');
    }
}

$('#supportingForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '<?= base_url("employee_documents/store") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                Toast.fire({icon: 'success', title: response.message});
                closeSupportingModal();
                supportingTable.ajax.reload(null, false);
            } else {
                Swal.fire({icon: 'error', title: 'Gagal', html: response.message});
            }
        }
    });
});

function deleteSupporting(id) {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: "File akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("employee_documents/delete/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        Toast.fire({icon: 'success', title: response.message});
                        supportingTable.ajax.reload(null, false);
                    }
                }
            });
        }
    });
}
</script>
