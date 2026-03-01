<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Master Jenis Dokumen</h1>
            <p class="text-sm text-gray-500 mt-1">Atur daftar berkas wajib akreditasi dan tracking masa berlaku.</p>
        </div>
        <div class="flex gap-3">
            <button id="btnSaveOrder" onclick="saveOrder()" class="hidden bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-5 rounded-lg shadow-md flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined">save</span>
                Simpan Urutan
            </button>
            <button onclick="openTypeModal()" class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-5 rounded-lg shadow-md flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined">add</span>
                Tambah Jenis Dokumen
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4">
            <table class="w-full text-left" id="typesTable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Nama Dokumen</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Berlaku Untuk</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Wajib</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Tracking Expired</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="sortable-types"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tabungan Jenis Dokumen -->
<div id="typeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" onclick="closeTypeModal()"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
            <h3 class="text-xl font-black text-gray-900 mb-5 pb-3 border-b border-gray-200" id="typeModalTitle">Tambah Jenis Dokumen</h3>
            <form id="typeForm">
                <input type="hidden" name="id" id="type_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="type_name" class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kumpulan Kategori</label>
                        <select name="category" id="type_category" class="w-full rounded-lg border-gray-300">
                            <option value="Pribadi">Pribadi</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kepegawaian">Kepegawaian</option>
                            <option value="Sertifikasi">Sertifikasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berlaku Untuk</label>
                        <select name="applicable_to" id="type_applicable_to" class="w-full rounded-lg border-gray-300">
                            <option value="all">Semua Pegawai</option>
                            <option value="non-medical">Non-Medis Only</option>
                            <option value="medical">Medis (Perawat/Bidan)</option>
                            <option value="doctor">Dokter Only</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3 p-4 bg-gray-50 rounded-lg border border-gray-200 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Berkas Wajib</p>
                            <p class="text-[10px] text-gray-500">Muncul di Checklist Akreditasi pegawai.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_mandatory" id="type_is_mandatory" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Tracking Masa Berlaku</p>
                            <p class="text-[10px] text-gray-500">Aktifkan input tanggal expired & reminder.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="has_expiry" id="type_has_expiry" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-2.5 rounded-lg transition-all">Simpan Perubahan</button>
                    <button type="button" onclick="closeTypeModal()" class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-lg transition-all">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
var typesTable;
$(document).ready(function() {
    typesTable = $('#typesTable').DataTable({
        "ajax": "<?= base_url('documents/get_types_json') ?>",
        "responsive": false,
        "autoWidth": false,
        "destroy": true,
        "paging": false, // Required for sorting all items or handle nested sorting
        "language": { "search": "Cari Dokumen:", "emptyTable": "Belum ada jenis dokumen" },
        "drawCallback": function() {
            initSortable();
        }
    });
});

function initSortable() {
    $("#sortable-types").sortable({
        handle: ".handle",
        update: function(event, ui) {
            $('#btnSaveOrder').removeClass('hidden');
            // Re-index number column
            refreshRowNumbers();
        }
    }).disableSelection();
}

function refreshRowNumbers() {
    $('#sortable-types tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function saveOrder() {
    let ids = [];
    $('#sortable-types tr').each(function() {
        ids.push($(this).attr('id')); // DT_RowId puts ID here
    });

    $.ajax({
        url: '<?= base_url("documents/update_type_order") ?>',
        type: 'POST',
        data: { ids: ids },
        dataType: 'json',
        success: function(resp) {
            if(resp.status === 'success') {
                Toast.fire({icon: 'success', title: resp.message});
                $('#btnSaveOrder').addClass('hidden');
                typesTable.ajax.reload(null, false);
            }
        }
    });
}

function openTypeModal() {
    $('#typeForm')[0].reset();
    $('#type_id').val('');
    $('#typeModalTitle').text('Tambah Jenis Dokumen');
    $('#typeModal').removeClass('hidden');
}

function closeTypeModal() {
    $('#typeModal').addClass('hidden');
}

function editType(id) {
    $.ajax({
        url: '<?= base_url("documents/get_type_detail/") ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#type_id').val(data.id);
            $('#type_name').val(data.name);
            $('#type_category').val(data.category);
            $('#type_applicable_to').val(data.applicable_to);
            $('#type_is_mandatory').prop('checked', data.is_mandatory == 1);
            $('#type_has_expiry').prop('checked', data.has_expiry == 1);
            
            $('#typeModalTitle').text('Edit Jenis Dokumen');
            $('#typeModal').removeClass('hidden');
        }
    });
}

$('#typeForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '<?= base_url("documents/store_type") ?>',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(resp) {
            if(resp.status === 'success') {
                Toast.fire({icon: 'success', title: resp.message});
                closeTypeModal();
                typesTable.ajax.reload(null, false);
            } else {
                Swal.fire('Gagal', resp.message, 'error');
            }
        }
    });
});

function deleteType(id) {
    Swal.fire({
        title: 'Hapus Jenis Dokumen?',
        text: "Pastikan tidak ada dokumen pegawai yang menggunakan tipe ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("documents/delete_type/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        Toast.fire({icon: 'success', title: resp.message});
                        typesTable.ajax.reload(null, false);
                    }
                }
            });
        }
    });
}
</script>
