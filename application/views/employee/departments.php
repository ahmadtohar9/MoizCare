<!-- Page Breadcrumbs & Header -->
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Master Departemen</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Data Departemen</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manajemen unit kerja RS (Poli, Rawat Inap, Manajemen, dll).</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md active:scale-95">
            <span class="material-symbols-outlined">add_circle</span>
            <span>Tambah Unit</span>
        </button>
    </div>
</div>

<div class="px-6 pb-10">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-[#e5e7eb] dark:border-[#2d3748] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="departmentTable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-[#e5e7eb] dark:border-[#2d3748]">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest" width="5%">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Nama Unit</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Tipe</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2d3748]">
                    <!-- AJAX Loaded -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Tambah Departemen / Unit</h3>
                <form id="formInput">
                    <input type="hidden" name="id" id="department_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Unit</label>
                        <input type="text" id="department_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                        <select name="type" id="department_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            <option value="non-medical">Non-Medis (Manajemen/Office)</option>
                            <option value="medical">Medis (Poli/IGD/Farmasi)</option>
                        </select>
                    </div>
                     <div class="mb-4">
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="description" id="department_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    var table; 
    
    $(document).ready(function() {
        table = $('#departmentTable').DataTable({
            "ajax": "<?= base_url('employee/get_departments_json') ?>",
            "responsive": true,
            "autoWidth": false,
            "language": { "search": "Cari:", "emptyTable": "Tidak ada data" }
        });
    });

    function openModal() { 
        document.getElementById('formInput').reset();
        document.getElementById('department_id').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Departemen / Unit';
        document.getElementById('modal').classList.remove('hidden'); 
    }
    
    function closeModal() { document.getElementById('modal').classList.add('hidden'); }
    
    function editItem(id) {
        $.ajax({
            url: '<?= base_url("employee/get_department_detail/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                document.getElementById('department_id').value = data.id;
                document.getElementById('department_name').value = data.name;
                document.getElementById('department_type').value = data.type;
                document.getElementById('department_description').value = data.description;
                document.getElementById('modalTitle').innerText = 'Edit Departemen';
                document.getElementById('modal').classList.remove('hidden');
            }
        });
    }

    function submitForm() {
        const formData = new FormData(document.getElementById('formInput'));
        $.ajax({
            url: '<?= base_url("employee/store_department") ?>',
            type: 'POST',
            data: formData,
            processData: false, contentType: false, dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success') { 
                    Toast.fire({icon: 'success', title: resp.message}); 
                    closeModal(); 
                    table.ajax.reload(null, false); 
                } else {
                    Toast.fire({icon: 'error', title: resp.message});
                }
            }
        });
    }

    function deleteItem(id) {
        Swal.fire({
            title: 'Hapus Unit?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("employee/delete_department/") ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(resp) {
                         if(resp.status === 'success') { 
                             Toast.fire({icon: 'success', title: resp.message}); 
                             table.ajax.reload(null, false); 
                         }
                    }
                });
            }
        })
    }
</script>
