<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Pengaturan Sistem</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Konfigurasi Instansi</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Atur identitas rumah sakit dan lokasi absensi (Geofencing).</p>
        </div>
    </div>
</div>

<div class="px-6 pb-20">
    <form action="<?= base_url('settings/update') ?>" method="post" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Company Profile -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-2 rounded-xl">corporate_fare</span>
                        Profil Instansi
                    </h3>
                    
                    <!-- Logo Upload Interaction -->
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <div class="size-16 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center">
                                <?php if($settings->company_logo): ?>
                                    <img src="<?= base_url($settings->company_logo) ?>" id="logoPreview" class="w-full h-full object-contain p-1">
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-gray-300" id="logoIcon">image</span>
                                    <img src="" id="logoPreview" class="hidden w-full h-full object-contain p-1">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <label for="company_logo" class="cursor-pointer bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all inline-block shadow-lg">Ganti Logo</label>
                            <input type="file" name="company_logo" id="company_logo" class="hidden" onchange="previewImage(this)">
                            <p class="text-[9px] text-gray-400 mt-1 font-bold">Format: PNG/JPG (Max 2MB)</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Rumah Sakit / Perusahaan</label>
                        <input type="text" name="company_name" value="<?= $settings->company_name ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kontak / Telepon</label>
                            <input type="text" name="contact" value="<?= $settings->contact ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Resmi</label>
                            <input type="email" name="email" value="<?= $settings->email ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6">
                        </div>
                    </div>

                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-4 focus:ring-2 focus:ring-blue-500 border-none px-6"><?= $settings->address ?></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 mt-6 pt-6">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-gray-400">signature</span> Pengaturan Penanda Tangan HRD</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama HRD / Direktur</label>
                                <input type="text" name="hr_signature_name" value="<?= isset($settings->hr_signature_name) ? $settings->hr_signature_name : 'Bpk. Moiz Azhar' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6" placeholder="Cth: Bpk. Moiz Azhar">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jabatan Tanda Tangan</label>
                                <input type="text" name="hr_signature_title" value="<?= isset($settings->hr_signature_title) ? $settings->hr_signature_title : 'HRD & Finance Director' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6" placeholder="Cth: HRD Manager">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geofencing Settings -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-orange-600 bg-orange-50 p-2 rounded-xl">location_on</span>
                    Titik Lokasi Absensi (Geofencing)
                </h3>
                
                <p class="text-xs text-gray-400 mb-6">Tentukan koordinat pusat rumah sakit. Karyawan hanya bisa absen jika berada dalam radius yang ditentukan.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Latitude</label>
                        <input type="text" name="latitude" value="<?= $settings->latitude ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6" placeholder="-6.2088">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Longitude</label>
                        <input type="text" name="longitude" value="<?= $settings->longitude ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6" placeholder="106.8456">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Radius (Meter)</label>
                        <div class="relative">
                            <input type="number" name="radius_meters" value="<?= $settings->radius_meters ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-blue-500 border-none px-6 pr-12">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">M</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 rounded-2xl bg-blue-50 border border-blue-100 flex items-start gap-4">
                    <span class="material-symbols-outlined text-blue-600">info</span>
                    <div class="text-[11px] font-medium text-blue-700 leading-relaxed">
                        Tips: Buka Google Maps, klik kanan pada lokasi rumah sakit, lalu salin koordinat angka (Latitude & Longitude) ke form di atas. Radius ideal adalah 50 - 150 meter.
                    </div>
                </div>
            </div>

            <!-- UI Template Settings -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm mt-8">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-pink-600 bg-pink-50 p-2 rounded-xl">palette</span>
                    Pengaturan Template & Warna
                </h3>
                
                <p class="text-xs text-gray-400 mb-6 font-medium">Sesuaikan warna tampilan aplikasi (Header, Sidebar, Footer, dan Menu Aktif) agar sesuai dengan identitas instansi Anda.</p>

                <!-- Preset Themes -->
                <div class="mb-6 space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Tema Cepat (Pilih Preset / Kombinasi Warna Otomatis)</label>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" onclick="setTheme('#ffffff', '#ffffff', '#f8f9fa', '#2563eb')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#ffffff] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#ffffff] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#2563eb] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-blue-700">Clean Blue</span>
                        </button>
                        
                        <button type="button" onclick="setTheme('#1e293b', '#0f172a', '#1e293b', '#3b82f6')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-slate-500 hover:bg-slate-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#1e293b] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#0f172a] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#3b82f6] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-slate-700">Dark Mode</span>
                        </button>
                        
                        <button type="button" onclick="setTheme('#1e3a8a', '#172554', '#eff6ff', '#f59e0b')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#1e3a8a] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#172554] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#f59e0b] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-orange-700">Corporate Gold</span>
                        </button>

                        <button type="button" onclick="setTheme('#ffffff', '#064e3b', '#ecfdf5', '#10b981')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#ffffff] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#064e3b] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#10b981] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-green-700">Emerald Green</span>
                        </button>

                        <button type="button" onclick="setTheme('#ffffff', '#4c1d95', '#f5f3ff', '#a855f7')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#ffffff] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#4c1d95] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#a855f7] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-purple-700">Royal Purple</span>
                        </button>

                        <button type="button" onclick="setTheme('#ffffff', '#881337', '#fff1f2', '#f43f5e')" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-red-500 hover:bg-red-50 transition-all">
                            <div class="flex -space-x-1">
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#ffffff] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#881337] shadow-sm"></span>
                                <span class="w-4 h-4 rounded-full border border-gray-200 bg-[#f43f5e] shadow-sm z-10"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-red-700">Rose Red</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Warna Header</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="header_color" value="<?= isset($settings->header_color) ? $settings->header_color : '#ffffff' ?>" class="h-10 w-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" value="<?= isset($settings->header_color) ? $settings->header_color : '#ffffff' ?>" class="w-full rounded-xl border-gray-100 bg-gray-50 font-bold py-2.5 focus:ring-2 focus:ring-pink-500 border-none px-4 text-xs" oninput="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Warna Sidebar</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="sidebar_color" value="<?= isset($settings->sidebar_color) ? $settings->sidebar_color : '#ffffff' ?>" class="h-10 w-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" value="<?= isset($settings->sidebar_color) ? $settings->sidebar_color : '#ffffff' ?>" class="w-full rounded-xl border-gray-100 bg-gray-50 font-bold py-2.5 focus:ring-2 focus:ring-pink-500 border-none px-4 text-xs" oninput="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Warna Footer</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="footer_color" value="<?= isset($settings->footer_color) ? $settings->footer_color : '#f8f9fa' ?>" class="h-10 w-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" value="<?= isset($settings->footer_color) ? $settings->footer_color : '#f8f9fa' ?>" class="w-full rounded-xl border-gray-100 bg-gray-50 font-bold py-2.5 focus:ring-2 focus:ring-pink-500 border-none px-4 text-xs" oninput="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Menu Aktif (Aksen)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="menu_active_color" value="<?= isset($settings->menu_active_color) ? $settings->menu_active_color : '#2563eb' ?>" class="h-10 w-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" value="<?= isset($settings->menu_active_color) ? $settings->menu_active_color : '#2563eb' ?>" class="w-full rounded-xl border-gray-100 bg-gray-50 font-bold py-2.5 focus:ring-2 focus:ring-pink-500 border-none px-4 text-xs" oninput="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email / SMTP Settings -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm mt-8">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-purple-600 bg-purple-50 p-2 rounded-xl">forward_to_inbox</span>
                    Mail Server (Kirim Slip Otomatis)
                </h3>
                
                <p class="text-xs text-gray-400 mb-6 font-medium">Pengaturan Gateway pengiriman Slip Gaji Karyawan menuju kotak masuk email mereka secara langsung (Support Gmail / Hosting Email Cpanel).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Protocol / Metode</label>
                        <select name="protocol" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6">
                            <option value="smtp" <?= isset($smtp) && $smtp->protocol == 'smtp' ? 'selected' : '' ?>>SMTP (Disarankan)</option>
                            <option value="mail" <?= isset($smtp) && $smtp->protocol == 'mail' ? 'selected' : '' ?>>PHP Mail (Tanpa Auth)</option>
                            <option value="sendmail" <?= isset($smtp) && $smtp->protocol == 'sendmail' ? 'selected' : '' ?>>Sendmail Server</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= isset($smtp) ? $smtp->smtp_host : '' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6" placeholder="Contoh: ssl://smtp.gmail.com">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">PORT Jaringan</label>
                        <input type="number" name="smtp_port" value="<?= isset($smtp) ? $smtp->smtp_port : '' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6" placeholder="Misal: 465 atau 587">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Keamanan (Crypto)</label>
                        <select name="smtp_crypto" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6">
                            <option value="ssl" <?= isset($smtp) && $smtp->smtp_crypto == 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                            <option value="tls" <?= isset($smtp) && $smtp->smtp_crypto == 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                            <option value="" <?= isset($smtp) && $smtp->smtp_crypto == '' ? 'selected' : '' ?>>Tanpa Enkripsi (Bahaya)</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Alamat Email Pengirim</label>
                            <input type="email" name="smtp_user" value="<?= isset($smtp) ? $smtp->smtp_user : '' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6" placeholder="moizcare@gmail.com">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Password / App Password</label>
                            <input type="password" name="smtp_pass" value="<?= isset($smtp) ? $smtp->smtp_pass : '' ?>" class="w-full rounded-2xl border-gray-100 bg-gray-50 font-bold py-3.5 focus:ring-2 focus:ring-purple-500 border-none px-6" placeholder="* * * * * * * * * *">
                            <p class="text-[9px] text-gray-400 mt-2 font-bold ml-1">Gunakan "App Password" dari Google.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Save Action & Stats -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-gray-200">
                <h4 class="text-sm font-black uppercase tracking-widest mb-2 opacity-60">Status Konfigurasi</h4>
                <div class="flex items-center gap-3 mb-8">
                    <div class="size-3 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs font-bold">Sistem Geofencing Aktif</span>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95 flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">save</span>
                    Simpan Perubahan
                </button>
                
                <p class="text-[10px] text-gray-400 text-center mt-6">Terakhir diperbarui:<br><?= date('d M Y, H:i', strtotime($settings->updated_at)) ?></p>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pratinjau Keamanan</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Lock GPS</span>
                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-600 text-[10px] font-black">ENABLED</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Fake GPS Detection</span>
                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-600 text-[10px] font-black">ENABLED</span>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
function setTheme(header, sidebar, footer, active) {
    // Update color inputs
    document.querySelector('input[name="header_color"]').value = header;
    document.querySelector('input[name="header_color"]').nextElementSibling.value = header;
    
    document.querySelector('input[name="sidebar_color"]').value = sidebar;
    document.querySelector('input[name="sidebar_color"]').nextElementSibling.value = sidebar;
    
    document.querySelector('input[name="footer_color"]').value = footer;
    document.querySelector('input[name="footer_color"]').nextElementSibling.value = footer;
    
    document.querySelector('input[name="menu_active_color"]').value = active;
    document.querySelector('input[name="menu_active_color"]').nextElementSibling.value = active;

    // Calculate contrast text for sidebar
    let hex = sidebar.replace('#', '');
    if(hex.length === 3) hex = hex.split('').map(x => x + x).join('');
    let r = parseInt(hex.substr(0, 2), 16), g = parseInt(hex.substr(2, 2), 16), b = parseInt(hex.substr(4, 2), 16);
    let yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    let sidebarText = (yiq >= 128) ? '#111418' : '#ffffff';
    let sidebarMuted = (sidebarText === '#ffffff') ? '#94a3b8' : '#617589';
    let sidebarHover = (sidebarText === '#ffffff') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

    // Apply preview immediately 
    document.documentElement.style.setProperty('--theme-header-bg', header);
    document.documentElement.style.setProperty('--theme-sidebar-bg', sidebar);
    document.documentElement.style.setProperty('--theme-footer-bg', footer);
    document.documentElement.style.setProperty('--theme-menu-active', active);
    document.documentElement.style.setProperty('--theme-sidebar-text', sidebarText);
    document.documentElement.style.setProperty('--theme-sidebar-muted', sidebarMuted);
    document.documentElement.style.setProperty('--theme-sidebar-hover', sidebarHover);

    // Auto save the form in the background
    var formData = new FormData(document.querySelector('form'));
    fetch('<?= base_url("settings/update") ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            icon: 'success',
            title: 'Tema berhasil disimpan otomatis'
        });
    })
    .catch(error => {
        console.error('Error saving theme:', error);
    });
}

// Also hook color inputs to auto preview
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('change', function() {
        let h = document.querySelector('input[name="header_color"]').value;
        let s = document.querySelector('input[name="sidebar_color"]').value;
        let f = document.querySelector('input[name="footer_color"]').value;
        let a = document.querySelector('input[name="menu_active_color"]').value;
        setTheme(h, s, f, a); // The auto save will trigger from here
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result).removeClass('hidden');
            $('#logoIcon').addClass('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
