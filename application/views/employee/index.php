<!-- Page Breadcrumbs & Header -->
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Employee Directory</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Employee Directory</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Central database for all staff members at Moiz Care.</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md active:scale-95">
            <span class="material-symbols-outlined">person_add</span>
            <span>Add New Employee</span>
        </button>
    </div>
</div>

<!-- Table Section starts immediately -->
<!-- Table Section -->
<div class="px-6 pb-10">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-[#e5e7eb] dark:border-[#2d3748] overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="employeeTable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-[#e5e7eb] dark:border-[#2d3748]">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Photo</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Full Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">NIP</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Position</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Department</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2d3748]">
                    <!-- Data Loaded via Ajax -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Employee -->
<div id="employeeModal" class="hidden fixed inset-0 z-40 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Large Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modal-title">Form Data Pegawai</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 max-w-[20px]"><span class="material-symbols-outlined">close</span></button>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 pt-4 border-b border-gray-200 dark:border-gray-600">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="switchTab('tab-profile')" id="btn-tab-profile" class="tab-btn border-primary text-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <span class="material-symbols-outlined align-bottom mr-1 text-[18px]">person</span> Profil Utama
                    </button>
                    <button onclick="switchTab('tab-personal')" id="btn-tab-personal" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <span class="material-symbols-outlined align-bottom mr-1 text-[18px]">id_card</span> Data Pribadi
                    </button>
                    <button onclick="switchTab('tab-employment')" id="btn-tab-employment" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <span class="material-symbols-outlined align-bottom mr-1 text-[18px]">badge</span> Kepegawaian
                    </button>
                    <button onclick="switchTab('tab-family')" id="btn-tab-family" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm hidden" id="family-tab-head">
                        <span class="material-symbols-outlined align-bottom mr-1 text-[18px]">diversity_3</span> Keluarga
                    </button>
                </nav>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <form id="employeeForm" class="space-y-6">
                    <input type="hidden" name="id" id="emp_id">

                    <!-- TAB 1: PROFIL UTAMA -->
                    <div id="tab-profile" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Photo Upload -->
                            <div class="md:col-span-2 flex items-center gap-4 p-4 border rounded-lg bg-gray-50 border-dashed border-gray-300">
                                <div class="shrink-0 size-20 rounded-full bg-gray-200 overflow-hidden border border-gray-300">
                                    <img id="preview-photo" src="https://ui-avatars.com/api/?name=User&background=random" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Foto Profil</label>
                                    <input type="file" name="photo" accept="image/*" onchange="previewImage(this)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                                    <p class="text-[10px] text-gray-500 mt-1">Format: JPG/PNG, Max 2MB. Rasio 1:1.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP / ID Pegawai <span class="text-red-500">*</span></label>
                                <input type="text" name="nip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen <span class="text-red-500">*</span></label>
                                <select name="unit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required>
                                    <option value="">Pilih Departemen</option>
                                    <?php foreach($units as $u): ?>
                                        <option value="<?= $u->id ?>"><?= $u->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan <span class="text-red-500">*</span></label>
                                <select name="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required>
                                    <option value="">Pilih Jabatan</option>
                                    <?php foreach($positions as $p): ?>
                                        <option value="<?= $p->id ?>"><?= $p->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Handphone</label>
                                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: DATA PRIBADI -->
                    <div id="tab-personal" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIK (KTP)</label>
                                <input type="text" name="nik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ibu Kandung</label>
                                <input type="text" name="mothers_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Lahir</label>
                                <input type="text" name="place_of_birth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                                <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agama</label>
                                <select name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pernikahan</label>
                                <select name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                                    <option value="single">Lajang (Single)</option>
                                    <option value="married">Menikah</option>
                                    <option value="widow">Janda</option>
                                    <option value="widower">Duda</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat KTP</label>
                                <textarea name="address_ktp" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Domisili <span class="text-xs text-gray-500">(Kosongkan jika sama)</span></label>
                                <textarea name="address_domicile" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: KEPEGAWAIAN -->
                    <div id="tab-employment" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Kepegawaian</label>
                                <select name="status_employee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                                    <option value="permanent">Tetap (Permanent)</option>
                                    <option value="contract">Kontrak (PKWT)</option>
                                    <option value="intern">Magang (Intern)</option>
                                    <option value="probation">Probation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Masuk (Join Date)</label>
                                <input type="date" name="join_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NPWP</label>
                                <input type="text" name="npwp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">BPJS Kesehatan</label>
                                <input type="text" name="bpjs_kesehatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">BPJS Ketenagakerjaan</label>
                                <input type="text" name="bpjs_ketenagakerjaan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            </div>
                        </div>

                        <!-- Resign Section -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                             <h4 class="text-sm font-bold text-red-500 uppercase tracking-wide mb-4">Pengakhiran Kerja (Khusus Keluar)</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-red-50 p-4 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Keluar (Resign)</label>
                                    <input type="date" name="resign_date" class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 sm:text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Alasan Keluar</label>
                                    <textarea name="resign_reason" rows="2" class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 sm:text-sm"></textarea>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- TAB 4: FAMILY (Only Show when Updating) -->
                    <div id="tab-family" class="tab-content hidden">
                        <div id="family-warning" class="text-center py-10 text-gray-500">
                            Silakan simpan data pegawai terlebih dahulu sebelum menambah data keluarga.
                        </div>
                        <div id="family-container" class="hidden">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold">Daftar Anggota Keluarga</h4>
                                <button type="button" onclick="openFamilyForm()" class="text-sm bg-primary text-white px-3 py-1.5 rounded-lg flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Anggota
                                </button>
                            </div>
                            <table class="w-full text-left border text-sm" id="familyTable">
                                <thead class="bg-gray-50 font-bold text-gray-600">
                                    <tr>
                                        <th class="p-2 border">No</th>
                                        <th class="p-2 border">Nama</th>
                                        <th class="p-2 border">Hubungan</th>
                                        <th class="p-2 border">Tgl Lahir</th>
                                        <th class="p-2 border">Pendidikan</th>
                                        <th class="p-2 border">Pekerjaan</th>
                                        <th class="p-2 border">Tanggungan?</th>
                                        <th class="p-2 border text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <!-- Mini Form Add Family -->
                            <div id="familyFormBox" class="mt-6 p-4 border rounded-lg bg-gray-50 hidden">
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="font-bold" id="fam_title">Input Keluarga Baru</h5>
                                    <button onclick="resetFamilyForm()" id="fam_btn_cancel" type="button" class="text-xs text-red-500 underline hidden">Batal Edit</button>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                    <input type="hidden" id="fam_id">
                                    <input type="text" id="fam_name" placeholder="Nama Lengkap" class="rounded border-gray-300 text-sm">
                                    <select id="fam_relation" class="rounded border-gray-300 text-sm">
                                        <option value="spouse">Suami / Istri</option>
                                        <option value="child">Anak</option>
                                        <option value="father">Ayah</option>
                                        <option value="mother">Ibu</option>
                                        <option value="sibling">Saudara</option>
                                    </select>
                                    <select id="fam_gender" class="rounded border-gray-300 text-sm">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <input type="date" id="fam_dob" class="rounded border-gray-300 text-sm">
                                    <input type="text" id="fam_edu" placeholder="Pendidikan" class="rounded border-gray-300 text-sm">
                                    <input type="text" id="fam_job" placeholder="Pekerjaan" class="rounded border-gray-300 text-sm">
                                    <select id="fam_dep" class="rounded border-gray-300 text-sm">
                                        <option value="0">Tidak Ditanggung</option>
                                        <option value="1">Ditanggung (Tunjangan)</option>
                                    </select>
                                    <button type="button" onclick="submitFamily()" id="fam_btn_submit" class="bg-green-600 text-white rounded font-bold">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer Actions -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex flex-row-reverse border-t border-gray-200 dark:border-gray-600">
                <button type="button" onclick="submitForm()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-primary text-base font-bold text-white hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Simpan Data Pegawai
                </button>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Employee Detail (Premium Dossier Design) -->
<div id="viewModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <!-- Backdrop with Blur -->
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeViewModal()"></div>
        
        <!-- Modal Content Container -->
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden transform transition-all border border-gray-100">
            
            <!-- Modal Header (White & Clean) -->
            <div class="p-8 border-b border-gray-100 relative">
                <button onclick="closeViewModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <!-- Photo Container -->
                    <div class="relative group">
                        <div class="size-32 rounded-2xl overflow-hidden ring-4 ring-gray-50 shadow-md">
                            <img id="view_photo" src="" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="absolute -bottom-2 -right-2 size-6 bg-green-500 border-4 border-white rounded-full"></div>
                    </div>

                    <!-- Basic Stats -->
                    <div class="flex-1 text-center md:text-left pt-2">
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                            <h2 id="view_name" class="text-3xl font-black text-gray-900 tracking-tight">Nama Pegawai</h2>
                            <span id="view_status_badge" class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest">ACTIVE</span>
                        </div>
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-y-1 gap-x-4 text-sm font-medium text-gray-500">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-gray-400 text-lg">work</span>
                                <span id="header_position">Position</span>
                                <span class="text-gray-300">•</span>
                                <span id="view_nip" class="font-bold text-gray-600">NIP: -</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span id="header_unit">Department</span>
                                <span class="text-gray-300">•</span>
                                <span id="header_shift">Shift Not Set</span>
                            </div>
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-end gap-3 pb-2">
                        <button onclick="editFromView()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold text-sm hover:bg-gray-50 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-lg">edit</span>
                            Edit Profile
                        </button>
                        <button onclick="printEmployeePdf()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                            <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                            Print PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Bar -->
            <div class="px-8 border-b border-gray-100 bg-gray-50/50">
                <nav class="flex gap-8">
                    <button onclick="switchViewTab('profile')" id="view_btn_profile" class="px-2 py-4 border-b-2 border-blue-600 text-blue-600 font-bold text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-lg">person</span> Profil
                    </button>
                    <button onclick="switchViewTab('berkas')" id="view_btn_berkas" class="px-2 py-4 border-b-2 border-transparent text-gray-500 font-bold text-sm flex items-center gap-2 hover:text-gray-700 transition-all">
                        <span class="material-symbols-outlined text-lg">folder_open</span> Berkas
                    </button>
                    <button onclick="switchViewTab('absensi')" id="view_btn_absensi" class="px-2 py-4 border-b-2 border-transparent text-gray-500 font-bold text-sm flex items-center gap-2 hover:text-gray-700 transition-all">
                        <span class="material-symbols-outlined text-lg">calendar_today</span> Absensi
                    </button>
                    <button onclick="switchViewTab('jadwal')" id="view_btn_jadwal" class="px-2 py-4 border-b-2 border-transparent text-gray-500 font-bold text-sm flex items-center gap-2 hover:text-gray-700 transition-all">
                        <span class="material-symbols-outlined text-lg">schedule</span> Jadwal
                    </button>
                </nav>
            </div>

            <!-- Modal Body Container -->
            <div class="p-10 min-h-[500px]">
                <!-- TAB 1: Profil -->
                <div id="view_tab_profile" class="view-tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12">
                    
                    <!-- Column 1: Personal Information -->
                    <div>
                        <h4 class="flex items-center gap-2 font-black text-blue-600 mb-8 uppercase tracking-widest text-xs">
                            <span class="material-symbols-outlined !text-[20px]">account_circle</span>
                            Personal Information
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-y-8 gap-x-6">
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Full Name</p>
                                <p id="view_name_content" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">NIK</p>
                                <p id="view_nik" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Gender</p>
                                <p id="view_gender" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Birth Place / Date</p>
                                <p id="view_ttl" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Religion</p>
                                <p id="view_religion" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Phone</p>
                                <p id="view_phone" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Address</p>
                                <p id="view_address" class="text-sm font-bold text-gray-900 leading-relaxed">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Employment Data -->
                    <div>
                        <h4 class="flex items-center gap-2 font-black text-blue-600 mb-8 uppercase tracking-widest text-xs">
                            <span class="material-symbols-outlined !text-[20px]">business_center</span>
                            Employment Data
                        </h4>

                        <div class="grid grid-cols-2 gap-y-8 gap-x-6">
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Employee ID</p>
                                <p id="view_id_content" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Position</p>
                                <p id="view_position_content" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Department</p>
                                <p id="view_unit_content" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Join Date</p>
                                <p id="view_join" class="text-sm font-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Information Section -->
                <div class="mt-12 pt-10 border-t border-gray-100">
                    <h4 class="flex items-center gap-2 font-black text-blue-600 mb-8 uppercase tracking-widest text-xs">
                        <span class="material-symbols-outlined !text-[20px]">diversity_3</span>
                        Family Information
                    </h4>
                    
                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="w-full text-left" id="view_family_table">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Name</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Relation</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Gender</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Birth Date</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Dependent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <!-- Ajax Load -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Info Area -->
                <div class="mt-12 p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-gray-400">info</span>
                        <p class="text-xs font-bold text-gray-500">Last updated by Admin on <span id="view_updated_at"><?= date('M d, Y') ?></span></p>
                    </div>
                    <button class="text-xs font-black text-blue-600 uppercase hover:underline">View History Logs</button>
                </div>
            </div>

            <!-- TAB 2: Berkas -->
            <div id="view_tab_berkas" class="view-tab-content hidden">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h4 class="font-black text-gray-900 border-l-4 border-blue-600 pl-3">Daftar Dokumen Digital</h4>
                        <p class="text-xs text-gray-500 mt-1">Klik pada dokumen untuk melihat atau mengunduh.</p>
                    </div>
                    <button id="btn_manage_docs" class="text-xs font-black text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                        KELOLA SEMUA BERKAS
                    </button>
                </div>

                <div id="view_docs_list" class="space-y-12 pb-20">
                    <!-- Ajax Load Previews -->
                </div>
            </div>

            <!-- TAB 3: Absensi -->
            <div id="view_tab_absensi" class="view-tab-content hidden">
                <div class="mb-8">
                    <h4 class="font-black text-gray-900 border-l-4 border-blue-600 pl-3">Riwayat Kehadiran (30 Hari Terakhir)</h4>
                    <p class="text-xs text-gray-500 mt-1">Data absensi diambil otomatis dari sistem check-in.</p>
                </div>
                <div class="overflow-hidden border border-gray-100 rounded-2xl">
                    <table class="w-full text-left" id="view_attendance_table">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Shift</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">In/Out</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Ajax Load -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: Jadwal -->
            <div id="view_tab_jadwal" class="view-tab-content hidden">
                <div class="mb-8">
                    <h4 class="font-black text-gray-900 border-l-4 border-blue-600 pl-3">Jadwal Shift Aktif</h4>
                    <p class="text-xs text-gray-500 mt-1">Menampilkan jadwal dari 7 hari lalu hingga 14 hari ke depan.</p>
                </div>
                <div class="overflow-hidden border border-gray-100 rounded-2xl">
                    <table class="w-full text-left" id="view_schedule_table">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Shift</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jam Kerja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Ajax Load -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    var table; 

    $(document).ready(function() {
        // Destroy existing if initialized via footer global
        if($.fn.DataTable.isDataTable('#employeeTable')) {
            $('#employeeTable').DataTable().destroy();
        }

        table = $('#employeeTable').DataTable({
            "ajax": "<?= base_url('employee/get_employees_json') ?>",
            "responsive": false, 
            "autoWidth": false,
            "destroy": true,
            "language": { "search": "Cari Pegawai:", "emptyTable": "Tidak ada data pegawai" }
        });
    });

    // Tab Switching Logic
    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-primary', 'text-primary');
            el.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById('btn-'+tabId).classList.add('border-primary', 'text-primary');
        document.getElementById('btn-'+tabId).classList.remove('border-transparent', 'text-gray-500');
    }

    // --- Modal Logic ---
    function openModal() {
        document.getElementById('employeeForm').reset();
        document.getElementById('emp_id').value = ''; 
        document.getElementById('modal-title').innerText = 'Tambah Pegawai Baru';
        
        // Hide Family Tab on Create
        document.getElementById('btn-tab-family').classList.add('hidden');
        document.getElementById('family-warning').classList.remove('hidden');
        document.getElementById('family-container').classList.add('hidden');

        switchTab('tab-profile'); // Default tab
        document.getElementById('employeeModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('employeeModal').classList.add('hidden');
    }

    // --- Edit Logic & Populate Data ---
    function editEmployee(id) {
        $.ajax({
            url: '<?= base_url("employee/edit_employee/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                document.getElementById('modal-title').innerText = 'Edit Data Pegawai';
                document.getElementById('emp_id').value = data.id;

                // Tab 1
                $('[name="nip"]').val(data.nip);
                $('[name="full_name"]').val(data.full_name);
                $('[name="unit_id"]').val(data.unit_id);
                $('[name="position_id"]').val(data.position_id);
                $('[name="email"]').val(data.email);
                $('[name="phone"]').val(data.phone);

                // Tab 2
                $('[name="nik"]').val(data.nik);
                $('[name="mothers_name"]').val(data.mothers_name);
                $('[name="place_of_birth"]').val(data.place_of_birth);
                $('[name="date_of_birth"]').val(data.date_of_birth);
                $('[name="gender"]').val(data.gender);
                $('[name="religion"]').val(data.religion);
                $('[name="marital_status"]').val(data.marital_status);
                $('[name="address_ktp"]').val(data.address_ktp);
                $('[name="address_domicile"]').val(data.address_domicile);

                // Tab 3
                $('[name="status_employee"]').val(data.status_employee);
                $('[name="join_date"]').val(data.join_date);
                $('[name="npwp"]').val(data.npwp);
                $('[name="bpjs_kesehatan"]').val(data.bpjs_kesehatan);
                $('[name="bpjs_ketenagakerjaan"]').val(data.bpjs_ketenagakerjaan);
                // Resign
                $('[name="resign_date"]').val(data.resign_date);
                $('[name="resign_reason"]').val(data.resign_reason);

                // Show Family Tab
                document.getElementById('btn-tab-family').classList.remove('hidden');
                document.getElementById('family-warning').classList.add('hidden');
                document.getElementById('family-container').classList.remove('hidden');
                loadFamily(id);

                document.getElementById('employeeModal').classList.remove('hidden');
                switchTab('tab-profile');
            }
        });
    }

    // --- Helper ---
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { $('#preview-photo').attr('src', e.target.result); }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // --- Family Logic ---
    function loadFamily(empId) {
        $.ajax({
            url: '<?= base_url("employee/get_family_json/") ?>' + empId,
            dataType: 'json',
            success: function(resp) {
                let html = '';
                if(resp.data.length === 0) {
                    html = '<tr><td colspan="8" class="text-center p-4 text-gray-500">Belum ada data keluarga</td></tr>';
                } else {
                    resp.data.forEach(row => {
                        html += `<tr>
                            <td class="p-2 border">${row[0]}</td>
                            <td class="p-2 border">${row[1]}</td>
                            <td class="p-2 border">${row[2]}</td>
                            <td class="p-2 border">${row[3]}</td>
                            <td class="p-2 border">${row[4]}</td>
                            <td class="p-2 border">${row[5]}</td>
                            <td class="p-2 border">${row[6]}</td>
                            <td class="p-2 border">${row[7]}</td>
                        </tr>`;
                    });
                }
                $('#familyTable tbody').html(html);
            }
        });
    }

    function openFamilyForm() { 
        $('#familyFormBox').removeClass('hidden'); 
    }

    function editFamily(id) {
        $.ajax({
            url: '<?= base_url("employee/get_family_detail/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate Form
                $('#fam_id').val(data.id);
                $('#fam_name').val(data.name);
                $('#fam_relation').val(data.relation);
                $('#fam_gender').val(data.gender);
                $('#fam_dob').val(data.date_of_birth);
                $('#fam_edu').val(data.education);
                $('#fam_job').val(data.job);
                $('#fam_dep').val(data.is_dependent);

                // Change UI Mode to Edit
                $('#familyFormBox').removeClass('hidden');
                $('#fam_title').text('Edit Data Keluarga');
                $('#fam_btn_submit').text('Update').removeClass('bg-green-600').addClass('bg-amber-500');
                $('#fam_btn_cancel').removeClass('hidden');
            }
        });
    }

    function resetFamilyForm() {
        $('#fam_id').val('');
        $('#fam_name').val('');
        $('#fam_title').text('Input Keluarga Baru');
        $('#fam_btn_submit').text('Simpan').removeClass('bg-amber-500').addClass('bg-green-600');
        $('#fam_btn_cancel').addClass('hidden');
    }

    function submitFamily() {
        let empId = $('#emp_id').val();
        let data = {
            id: $('#fam_id').val(), // ID for Update logic
            employee_id: empId,
            name: $('#fam_name').val(),
            relation: $('#fam_relation').val(),
            gender: $('#fam_gender').val(),
            date_of_birth: $('#fam_dob').val(),
            education: $('#fam_edu').val(),
            job: $('#fam_job').val(),
            is_dependent: $('#fam_dep').val()
        };

        if(!data.name) return alert('Nama wajib diisi');

        $.ajax({
            url: '<?= base_url("employee/store_family") ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success') {
                    Toast.fire({icon: 'success', title: data.id ? 'Data diupdate' : 'Keluarga ditambahkan'});
                    loadFamily(empId);
                    
                    // Reset fields to Add Mode
                    $('#fam_name').val(''); 
                    $('#fam_dob').val('');
                    $('#fam_edu').val('');
                    $('#fam_job').val('');
                    resetFamilyForm();
                } else {
                    alert('Gagal simpan');
                }
            }
        });
    }
    
    function deleteFamily(id) {
        if(confirm('Hapus data keluarga ini?')) {
            let empId = $('#emp_id').val();
            $.ajax({
                url: '<?= base_url("employee/delete_family/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function() { loadFamily(empId); }
            });
        }
    }

    // --- Main Submit Logic (Updated for Consistency) ---
    function submitForm() {
        const form = document.getElementById('employeeForm');
        const formData = new FormData(form);

        $.ajax({
            url: '<?= base_url("employee/store") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    Toast.fire({icon: 'success', title: response.message});
                    
                    // Reload Main Table instantly
                    table.ajax.reload(null, false);

                    // Logic: If new insert, keep modal open for Family. If update, close.
                    if(!document.getElementById('emp_id').value) {
                        // It was an Insert
                        document.getElementById('emp_id').value = response.id;
                        
                        // Enable Family Tab
                        document.getElementById('btn-tab-family').classList.remove('hidden');
                        document.getElementById('family-warning').classList.add('hidden');
                        document.getElementById('family-container').classList.remove('hidden');
                        loadFamily(response.id);
                        
                        // Notify user to continue
                        Swal.fire({
                            title: 'Data Utama Tersimpan!',
                            text: 'Silakan lanjut isi Data Keluarga atau tutup jika selesai.',
                            icon: 'success',
                            confirmButtonText: 'Lanjut Isi Keluarga',
                            showCancelButton: true,
                            cancelButtonText: 'Selesai & Tutup'
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                closeModal();
                            } else {
                                switchTab('tab-family'); // Auto move to family tab
                            }
                        });
                    } else {
                        // Update Mode -> Close Modal
                        closeModal();
                    }
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Validasi Gagal', 
                        html: '<div class="text-left bg-red-50 p-4 rounded-2xl border border-red-100 text-red-600 text-xs font-bold leading-relaxed">' + response.message + '</div>',
                        confirmButtonText: 'Tinjau Kembali',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-[2.5rem]',
                            confirmButton: 'rounded-xl font-black uppercase tracking-widest text-xs px-6 py-3'
                        }
                    });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' });
            }
        });
    }

    // --- View Employee Detail ---
    var current_view_id = null;
    function printEmployeePdf() {
        if(current_view_id) {
            window.open('<?= base_url("employee/print_pdf/") ?>' + current_view_id, '_blank');
        }
    }

    function viewEmployee(id) {
        current_view_id = id;
        switchViewTab('profile'); // Reset to profile tab
        $.ajax({
            url: '<?= base_url("employee/get_full_employee_detail/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const emp = data.employee;
                const docs = data.documents;

                // Simple Helpers
                const val = (v) => v ? v : '-';
                const dateFmt = (d) => d ? new Date(d).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';

                // Photo Handling
                $('#view_photo').attr('src', emp.photo_url);

                // Header Info
                $('#view_name').text(emp.full_name);
                $('#view_nip').text('NIP: ' + (emp.nip || '-'));
                $('#header_position').text(emp.position_name || '-');
                $('#header_unit').text(emp.unit_name || '-');
                $('#header_shift').text('Shift A (Morning)');
                
                // Status Badge
                const status = (emp.status_employee || 'ACTIVE').toUpperCase();
                $('#view_status_badge').text(status);
                if(status === 'INACTIVE' || status === 'RESIGNED') {
                    $('#view_status_badge').removeClass('bg-green-100 text-green-700').addClass('bg-red-100 text-red-700 font-bold');
                } else {
                    $('#view_status_badge').removeClass('bg-red-100 text-red-700').addClass('bg-green-100 text-green-700 font-bold');
                }

                // Personal Info (Column 1)
                $('#view_name_content').text(emp.full_name);
                $('#view_nik').text(val(emp.nik));
                $('#view_gender').text(val(emp.gender) === 'L' ? 'Laki-laki' : 'Perempuan');
                $('#view_ttl').text((emp.place_of_birth || '-') + ', ' + dateFmt(emp.date_of_birth));
                $('#view_religion').text(val(emp.religion));
                $('#view_phone').text(val(emp.phone));
                $('#view_address').text(val(emp.address_domicile) || val(emp.address_ktp));

                // Employment Data (Column 2)
                $('#view_id_content').text(emp.nip || '-');
                $('#view_position_content').text(emp.position_name || '-');
                $('#view_unit_content').text(emp.unit_name || '-');
                $('#view_join').text(dateFmt(emp.join_date));
                
                $('#view_updated_at').text(dateFmt(emp.updated_at || emp.created_at));

                // POPULATE FAMILY TABLE
                let famHtml = '';
                if(data.families && data.families.length > 0) {
                    const relMap = {'spouse':'Suami/Istri', 'child':'Anak', 'father':'Ayah', 'mother':'Ibu', 'sibling':'Saudara'};
                    data.families.forEach(f => {
                        famHtml += `
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">${f.name}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${relMap[f.relation] || f.relation}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${f.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${dateFmt(f.date_of_birth)}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                ${f.is_dependent == 1 ? '<span class="px-2 py-1 rounded-full bg-green-50 text-green-700 text-[10px] font-black uppercase">Yes</span>' : '<span class="text-gray-300 text-[10px] uppercase font-bold">No</span>'}
                            </td>
                        </tr>`;
                    });
                } else {
                    famHtml = '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 italic text-sm">No family data recorded.</td></tr>';
                }
                $('#view_family_table tbody').html(famHtml);

                // POPULATE DOCUMENTS TAB (Direct Preview Feed)
                let docHtml = '';
                if(docs.length === 0) {
                    docHtml = '<div class="p-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200"><span class="material-symbols-outlined text-5xl text-gray-300">folder_off</span><p class="text-sm text-gray-500 mt-4 font-medium">Belum ada dokumen digital yang diupload untuk pegawai ini.</p></div>';
                } else {
                    docs.forEach(d => {
                        const isExpired = d.expiry_date && new Date(d.expiry_date) < new Date();
                        const fileExt = d.file_path.split('.').pop().toLowerCase();
                        const fileUrl = '<?= base_url() ?>' + d.file_path;
                        
                        let previewHtml = '';
                        if(['jpg', 'jpeg', 'png'].includes(fileExt)) {
                            previewHtml = `<img src="${fileUrl}" class="w-full h-auto rounded-2xl shadow-sm border border-gray-100" loading="lazy">`;
                        } else if(fileExt === 'pdf') {
                            previewHtml = `<iframe src="${fileUrl}#toolbar=0" class="w-full h-[600px] rounded-2xl shadow-sm border border-gray-100" frameborder="0"></iframe>`;
                        } else {
                            previewHtml = `<div class="p-10 bg-gray-50 rounded-2xl text-center border border-gray-100"><span class="material-symbols-outlined text-4xl text-gray-400">insert_drive_file</span><p class="text-xs text-gray-500 mt-2">Format ${fileExt.toUpperCase()} tidak mendukung preview langsung.</p><a href="${fileUrl}" target="_blank" class="text-blue-600 font-bold text-xs mt-2 inline-block underline">Download File</a></div>`;
                        }

                        docHtml += `
                        <div class="document-item border-b border-gray-100 pb-12 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">description</span>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black text-gray-900">${d.type_name || 'Dokumen'}</h5>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                            ${d.document_number || '-'} • Upload: ${d.uploaded_at.split(' ')[0]} 
                                            ${d.expiry_date ? `• <span class="${isExpired ? 'text-red-500' : 'text-gray-500'}">Expiry: ${d.expiry_date}</span>` : ''}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    ${isExpired ? '<span class="px-2 py-1 rounded bg-red-100 text-red-700 text-[10px] font-black uppercase flex items-center gap-1"><span class="material-symbols-outlined text-sm">warning</span> EXPIRED</span>' : ''}
                                    <a href="${fileUrl}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 font-black text-[10px] hover:bg-gray-200 transition-all uppercase tracking-widest flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">open_in_new</span> Buka Full
                                    </a>
                                </div>
                            </div>
                            <div class="preview-container bg-white rounded-3xl p-2 border border-gray-50 shadow-inner">
                                ${previewHtml}
                            </div>
                        </div>`;
                    });
                }
                $('#view_docs_list').html(docHtml);
                $('#btn_manage_docs').attr('onclick', `window.location.href='<?= base_url() ?>employee_documents/index/${id}'`);

                // POPULATE ATTENDANCE TAB
                let attHtml = '';
                if(data.attendances && data.attendances.length > 0) {
                    data.attendances.forEach(a => {
                        let statusBadge = '';
                        if(a.status === 'present') statusBadge = '<span class="px-2 py-1 rounded bg-green-100 text-green-700 text-[10px] font-black uppercase">Hadir</span>';
                        else if(a.status === 'late') statusBadge = '<span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase">Terlambat</span>';
                        else statusBadge = '<span class="px-2 py-1 rounded bg-red-100 text-red-700 text-[10px] font-black uppercase">' + a.status + '</span>';

                        attHtml += `
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">${dateFmt(a.date)}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${a.shift_name || '-'}</td>
                            <td class="px-6 py-4 text-sm font-mono font-bold text-gray-800">
                                ${(a.clock_in ? a.clock_in.substring(0,5) : '--:--')} / ${(a.clock_out ? a.clock_out.substring(0,5) : '--:--')}
                            </td>
                            <td class="px-6 py-4 text-sm">${statusBadge}</td>
                        </tr>`;
                    });
                } else {
                    attHtml = '<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic text-sm">Belum ada riwayat absensi.</td></tr>';
                }
                $('#view_attendance_table tbody').html(attHtml);

                // POPULATE SCHEDULE TAB
                let schHtml = '';
                if(data.schedules && data.schedules.length > 0) {
                    data.schedules.forEach(s => {
                        schHtml += `
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">${dateFmt(s.date)}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="flex items-center gap-2">
                                    <span class="size-3 rounded-full" style="background-color: ${s.color || '#3b82f6'}"></span>
                                    ${s.shift_name}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                ${s.start_time.substring(0,5)} - ${s.end_time.substring(0,5)}
                            </td>
                        </tr>`;
                    });
                } else {
                    schHtml = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic text-sm">Tidak ada jadwal dalam rentang waktu terdekat.</td></tr>';
                }
                $('#view_schedule_table tbody').html(schHtml);

                // Final Show
                $('#viewModal').removeClass('hidden').css('display', 'block');
            },
            error: function() {
                Toast.fire({icon: 'error', title: 'Gagal memuat data'});
            }
        });
    }

    function switchViewTab(tab) {
        $('.view-tab-content').addClass('hidden');
        $('#view_tab_' + tab).removeClass('hidden');

        // Nav UI
        $('#view_btn_profile, #view_btn_berkas, #view_btn_absensi, #view_btn_jadwal').removeClass('border-blue-600 text-blue-600').addClass('border-transparent text-gray-500');
        $('#view_btn_' + tab).addClass('border-blue-600 text-blue-600').removeClass('border-transparent text-gray-500');
    }

    function editFromView() {
        if(current_view_id) {
            closeViewModal();
            editEmployee(current_view_id);
        }
    }

    function closeViewModal() {
        $('#viewModal').addClass('hidden').hide();
        current_view_id = null;
    }
</script>
