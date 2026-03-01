<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Moiz Care HIS Login</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1173d4",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen font-display text-gray-900 dark:text-gray-100 flex items-center justify-center relative bg-[#101922]">
    
    <!-- Full-screen Background Image with Gradient Overlays -->
    <div class="fixed inset-0 w-full h-full bg-cover bg-center bg-no-repeat lg:scale-105" style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80');"></div>
    <div class="fixed inset-0 bg-primary/60 lg:bg-primary/40 mix-blend-multiply"></div>
    <div class="fixed inset-0 bg-gradient-to-t lg:bg-gradient-to-br from-[#101922] lg:from-[#101922]/90 via-[#101922]/90 lg:via-[#101922]/60 to-[#101922]/40 lg:to-primary/40"></div>

    <!-- Decorative Ambient Glows -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none hidden lg:block">
        <div class="absolute -top-[20%] -right-[10%] w-[50vw] h-[50vw] rounded-full bg-primary/30 blur-[120px]"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[50vw] h-[50vw] rounded-full bg-blue-400/20 blur-[120px]"></div>
    </div>

    <!-- Main Content Container -->
    <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10 flex min-h-[100dvh] lg:min-h-screen py-6 sm:py-10 flex-col lg:flex-row items-center justify-center lg:justify-between gap-6 sm:gap-10 lg:gap-20">
        
        <!-- Branding Section (Responsive) -->
        <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left mt-0 lg:mt-0 pt-2 lg:pt-0">
            <div class="flex items-center gap-2.5 sm:gap-3 mb-4 sm:mb-8 lg:mb-12 animate-in fade-in slide-in-from-bottom-8 duration-700">
                <div class="w-11 h-11 sm:w-14 sm:h-14 bg-white/10 backdrop-blur-xl rounded-[14px] sm:rounded-2xl flex items-center justify-center text-white border border-white/20 shadow-xl">
                    <span class="material-symbols-outlined text-[28px] sm:text-4xl">medical_services</span>
                </div>
                <h2 class="text-white text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight drop-shadow-md">Moiz Care HIS</h2>
            </div>
            
            <h1 class="text-[32px] sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-2 sm:mb-4 lg:mb-6 leading-[1.15] drop-shadow-xl animate-in fade-in slide-in-from-bottom-8 duration-700 delay-150 fill-mode-both">
                Healthcare <br class="hidden lg:block"/> Reimagined.
            </h1>
            <p class="text-blue-50/90 text-sm sm:text-base lg:text-xl mb-4 lg:mb-10 max-w-xl leading-relaxed font-medium drop-shadow-md animate-in fade-in slide-in-from-bottom-8 duration-700 delay-300 fill-mode-both hidden sm:block">
                Sistem Informasi Terintegrasi untuk manajemen pelayanan kesehatan yang lebih responsif, aman, dan efisien.
            </p>
            
            <div class="flex flex-row flex-wrap items-center justify-center lg:justify-start gap-2.5 sm:gap-4 lg:gap-6 text-white/90 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-500 fill-mode-both hidden sm:flex">
                <div class="flex items-center gap-2 lg:gap-2.5 bg-white/10 backdrop-blur-md px-4 py-2 lg:px-5 lg:py-2.5 rounded-full border border-white/10 shadow-lg">
                    <span class="material-symbols-outlined text-white/90 shrink-0 text-lg lg:text-xl">bolt</span>
                    <span class="text-xs lg:text-sm font-bold tracking-wide">Cepat & Responsif</span>
                </div>
                <div class="flex items-center gap-2 lg:gap-2.5 bg-white/10 backdrop-blur-md px-4 py-2 lg:px-5 lg:py-2.5 rounded-full border border-white/10 shadow-lg">
                    <span class="material-symbols-outlined text-white/90 shrink-0 text-lg lg:text-xl">encrypted</span>
                    <span class="text-xs lg:text-sm font-bold tracking-wide">Aman Terenkripsi</span>
                </div>
            </div>
        </div>

        <!-- Right Glass Login Card (Optimized for Mobile) -->
        <div class="w-full max-w-[400px] sm:max-w-md lg:max-w-none lg:w-[480px] shrink-0 animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-200 lg:delay-300 fill-mode-both flex flex-col justify-center pb-8 sm:pb-0 mx-auto lg:mx-0">
            <div class="bg-white/95 dark:bg-[#1a2632]/95 backdrop-blur-2xl rounded-[1.75rem] lg:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.12)] lg:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] border border-white/50 dark:border-gray-700/50 p-6 sm:p-8 lg:p-10 relative overflow-hidden">
                
                <!-- Inner Card Glow -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 lg:w-40 lg:h-40 rounded-full bg-primary/10 blur-[30px] lg:blur-[40px] pointer-events-none"></div>

                <div class="mb-6 lg:mb-10 relative text-center sm:text-left">
                    <h2 class="text-2xl lg:text-3xl font-black text-gray-900 dark:text-white tracking-tight" id="formTitle">Selamat Datang</h2>
                    <p class="text-[13px] lg:text-base text-gray-500 dark:text-gray-400 mt-1 lg:mt-2 font-medium" id="formSubtitle">Akses portal HIS dengan NIP Anda.</p>
                </div>

                <!-- Action Panel (Error Message) -->
                <?php if($this->session->flashdata('error')): ?>
                <div class="mb-5 lg:mb-6 animate-in zoom-in-95 duration-300">
                    <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-3 lg:p-4 dark:bg-red-900/10 dark:border-red-900/30">
                        <span class="material-symbols-outlined text-red-500 text-lg lg:text-xl mt-0.5">error</span>
                        <div class="flex-1">
                            <p class="text-red-800 dark:text-red-300 text-xs lg:text-sm font-bold">Autentikasi Gagal</p>
                            <p class="text-red-700 dark:text-red-400 text-xs lg:text-sm font-medium mt-0.5 lg:mt-1 leading-relaxed"><?php echo $this->session->flashdata('error'); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('success')): ?>
                <div class="mb-5 lg:mb-6 animate-in zoom-in-95 duration-300">
                    <div class="flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 p-3 lg:p-4 dark:bg-green-900/10 dark:border-green-900/30">
                        <span class="material-symbols-outlined text-green-500 text-lg lg:text-xl mt-0.5">check_circle</span>
                        <div class="flex-1">
                            <p class="text-green-800 dark:text-green-300 text-xs lg:text-sm font-bold">Berhasil</p>
                            <p class="text-green-700 dark:text-green-400 text-xs lg:text-sm font-medium mt-0.5 lg:mt-1 leading-relaxed"><?php echo $this->session->flashdata('success'); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-4 lg:space-y-6 relative" action="<?= base_url('auth/login_process') ?>" method="post">
                    <div class="space-y-1 lg:space-y-1.5">
                        <label class="block text-[13px] lg:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">Username / NIP</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 lg:pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px] lg:text-[22px]">person</span>
                            </div>
                            <input name="nip" class="block w-full pl-10 lg:pl-12 pr-4 py-3.5 lg:py-4 border-2 border-transparent bg-gray-100/80 dark:bg-[#101922] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary/50 focus:bg-white dark:focus:bg-[#101922] focus:ring-4 focus:ring-primary/10 rounded-xl lg:rounded-2xl transition-all font-semibold font-display shadow-inner text-sm leading-tight" placeholder="Masukkan NIP Anda" type="text" required />
                        </div>
                    </div>
                    <div class="space-y-1 lg:space-y-1.5">
                        <div class="flex items-center justify-between ml-1">
                            <label class="block text-[13px] lg:text-sm font-bold text-gray-700 dark:text-gray-300">Password</label>
                            <a class="text-[11px] lg:text-sm font-bold text-primary hover:text-primary/80 transition-colors" href="#">Lupa password?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 lg:pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px] lg:text-[22px]">lock</span>
                            </div>
                            <input name="password" class="block w-full pl-10 lg:pl-12 pr-4 py-3.5 lg:py-4 border-2 border-transparent bg-gray-100/80 dark:bg-[#101922] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary/50 focus:bg-white dark:focus:bg-[#101922] focus:ring-4 focus:ring-primary/10 rounded-xl lg:rounded-2xl transition-all font-semibold font-display shadow-inner text-sm leading-tight" placeholder="••••••••" type="password" required />
                        </div>
                    </div>
                    
                    <div class="flex items-center ml-1 pb-1 lg:pb-0">
                        <input class="h-3.5 w-3.5 lg:h-4 lg:w-4 text-primary focus:ring-primary/20 border-gray-300 dark:border-gray-700 rounded cursor-pointer bg-gray-50 dark:bg-[#101922] transition-colors" id="remember-me" name="remember-me" type="checkbox"/>
                        <label class="ml-2 lg:ml-3 block text-[11px] lg:text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer" for="remember-me">Ingat saya di perangkat ini</label>
                    </div>

                    <button class="w-full mt-2 group relative flex justify-center items-center gap-2 lg:gap-3 py-3.5 lg:py-4 px-4 rounded-xl lg:rounded-2xl shadow-[0_8px_16px_rgb(17,115,212,0.25)] lg:shadow-lg lg:shadow-primary/25 overflow-hidden focus:outline-none focus:ring-4 focus:ring-primary/30 active:scale-[0.98] transition-all" type="submit">
                        <div class="absolute inset-0 w-full h-full bg-primary group-hover:bg-blue-600 transition-colors"></div>
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                        <span class="relative text-xs lg:text-sm font-black text-white tracking-widest uppercase">Sign In to Dashboard</span>
                        <span class="relative material-symbols-outlined text-[18px] lg:text-[20px] text-white">login</span>
                    </button>
                    
                    <style>
                        @keyframes shimmer { 100% { transform: translateX(100%); } }
                    </style>
                </form>

                <!-- Register Form (Interactive Step) -->
                <form id="registerForm" class="hidden space-y-4 lg:space-y-6 relative" action="<?= base_url('auth/register_process') ?>" method="post">
                    <input type="hidden" name="nip" id="true_nip">
                    <div id="nipInputArea" class="space-y-4">
                        <div class="space-y-1 lg:space-y-1.5">
                            <label class="block text-xs lg:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">Cari Data Pegawai</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 lg:pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px] lg:text-[22px]">badge</span>
                                </div>
                                <input id="reg_query" class="block w-full pl-10 lg:pl-12 pr-4 py-3.5 lg:py-4 border-2 border-transparent bg-gray-100/80 dark:bg-[#101922] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary/50 focus:bg-white dark:focus:bg-[#101922] focus:ring-4 focus:ring-primary/10 rounded-xl lg:rounded-2xl transition-all font-semibold font-display shadow-inner text-sm leading-tight" placeholder="Contoh NIP atau Nama Anda" type="text" required />
                            </div>
                            <p class="text-[10px] lg:text-xs text-gray-500 font-medium ml-1">Minimal 3 karakter untuk pencarian nama.</p>
                        </div>
                        
                        <button type="button" onclick="verifyNip()" id="btnVerifyNip" class="w-full group relative flex justify-center items-center gap-2 py-3.5 lg:py-4 px-4 border-2 border-primary rounded-xl lg:rounded-2xl text-xs lg:text-sm font-black text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-4 focus:ring-primary/30 active:scale-[0.98] transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px] lg:text-[20px] group-hover:scale-110 transition-transform">search</span>
                            <span class="tracking-widest uppercase">Verifikasi NIP</span>
                        </button>
                    </div>

                    <!-- Verified Info Section (Hidden) -->
                    <div id="verifiedArea" class="hidden space-y-4 lg:space-y-6 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="flex items-center gap-3 lg:gap-4 bg-[#f0fdf4] dark:bg-green-900/10 p-4 lg:p-5 rounded-xl lg:rounded-2xl border border-[#bbf7d0] dark:border-green-900/30">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-green-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-green-500/20">
                                <span class="material-symbols-outlined text-[22px] lg:text-[26px]">verified_user</span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-[9px] lg:text-[10px] font-black text-green-700 dark:text-green-500 uppercase tracking-widest leading-none mb-1 lg:mb-1.5">Pegawai Terverifikasi</p>
                                <p id="empRealName" class="text-sm lg:text-base font-extrabold text-gray-900 dark:text-white leading-tight truncate">-</p>
                                <div class="flex items-center gap-1 lg:gap-1.5 mt-1 lg:mt-1.5">
                                    <span class="material-symbols-outlined text-green-600/70 text-[12px] lg:text-[14px]">badge</span>
                                    <p id="empRealNip" class="text-[10px] lg:text-xs text-green-700/80 dark:text-green-400 font-bold leading-none uppercase tracking-wider">-</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1 lg:space-y-1.5">
                            <label class="block text-xs lg:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">Buat Password Akun</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 lg:pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px] lg:text-[22px]">key</span>
                                </div>
                                <input name="password" class="block w-full pl-10 lg:pl-12 pr-4 py-3.5 lg:py-4 border-2 border-transparent bg-gray-100/80 dark:bg-[#101922] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary/50 focus:bg-white dark:focus:bg-[#101922] focus:ring-4 focus:ring-primary/10 rounded-xl lg:rounded-2xl transition-all font-semibold font-display shadow-inner text-sm leading-tight" placeholder="Minimal 6 karakter" type="password" required />
                            </div>
                        </div>
                        <button class="w-full mt-2 group relative flex justify-center items-center py-3.5 lg:py-4 px-4 bg-primary hover:bg-blue-600 rounded-xl lg:rounded-2xl shadow-[0_8px_16px_rgb(17,115,212,0.25)] lg:shadow-lg lg:shadow-primary/25 text-white focus:outline-none focus:ring-4 focus:ring-primary/30 active:scale-[0.98] transition-all" type="submit">
                            <span class="text-xs lg:text-sm font-black tracking-widest uppercase">Ajukan Pendaftaran</span>
                        </button>
                    </div>
                </form>

                <div class="mt-5 lg:mt-8 text-center" id="loginFooter">
                    <p class="text-[11px] lg:text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Belum memiliki akses sistem? 
                        <button onclick="toggleAuth(true)" class="text-primary font-bold hover:underline transition-colors ml-0.5">Mendaftar Disini</button>
                    </p>
                </div>
                <div class="mt-5 lg:mt-8 text-center hidden" id="registerFooter">
                    <p class="text-[11px] lg:text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Sudah punya akses login? 
                        <button onclick="toggleAuth(false)" class="text-primary font-bold hover:underline transition-colors ml-0.5">Masuk ke Portal</button>
                    </p>
                </div>
            </div>

            <!-- Footer Link Outside Card -->
            <div class="mt-4 lg:mt-8 text-center lg:text-center text-white/50 w-full animate-in fade-in slide-in-from-bottom-4 duration-1000 delay-500 fill-mode-both pb-2">
                <p class="text-[10px] lg:text-xs font-semibold leading-relaxed tracking-wider">
                    &copy; 2024 Moiz Care Healthcare Management.<br class="block sm:hidden">
                    <span class="hidden sm:inline"> &middot; </span>v2.4.0 &middot; All Rights Reserved.
                </p>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function toggleAuth(showRegister) {
        const loginForm = document.getElementById('loginForm');
        const regForm = document.getElementById('registerForm');
        const loginFooter = document.getElementById('loginFooter');
        const regFooter = document.getElementById('registerFooter');
        const title = document.getElementById('formTitle');
        const subtitle = document.getElementById('formSubtitle');

        if(showRegister) {
            loginForm.classList.add('hidden');
            loginFooter.classList.add('hidden');
            regForm.classList.remove('hidden');
            regFooter.classList.remove('hidden');
            title.innerText = 'Registrasi Akun';
            subtitle.innerText = 'Daftarkan akun NIP untuk akses layanan.';
            
            // Reset Verification State
            document.getElementById('nipInputArea').classList.remove('hidden');
            document.getElementById('verifiedArea').classList.add('hidden');
            document.getElementById('reg_query').value = '';
        } else {
            regForm.classList.add('hidden');
            regFooter.classList.add('hidden');
            loginForm.classList.remove('hidden');
            loginFooter.classList.remove('hidden');
            title.innerText = 'Selamat Datang';
            subtitle.innerText = 'Akses portal HIS dengan NIP Anda.';
        }
    }

    function verifyNip() {
        const query = $('#reg_query').val();
        if(!query || query.length < 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap masukkan NIP yang valid atau nama untuk verifikasi.',
                confirmButtonColor: '#1173d4',
                customClass: {
                    container: 'font-display',
                    popup: 'rounded-2xl md:rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)]',
                    confirmButton: 'rounded-xl px-6 md:px-8 py-2 md:py-3 text-xs md:text-sm font-black tracking-widest uppercase'
                }
            });
            return;
        }

        const btn = $('#btnVerifyNip');
        btn.prop('disabled', true).html('<span class="material-symbols-outlined animate-spin text-[18px] md:text-[20px]">autorenew</span><span class="tracking-widest uppercase font-black">Memproses...</span>');

        $.ajax({
            url: '<?= base_url("auth/search_employee") ?>',
            type: 'POST',
            data: { query: query },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px] md:text-[20px] group-hover:scale-110 transition-transform">search</span><span class="tracking-widest uppercase font-black">Cari Pegawai Lain</span>');
                if(res.status === 'success') {
                    $('#empRealName').text(res.name);
                    $('#empRealNip').text(res.nip);
                    $('#true_nip').val(res.nip);
                    $('#nipInputArea').addClass('hidden');
                    $('#verifiedArea').removeClass('hidden');
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ditemukan',
                        text: res.message,
                        confirmButtonColor: '#1173d4',
                        customClass: {
                            container: 'font-display',
                            popup: 'rounded-2xl md:rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)]',
                            confirmButton: 'rounded-xl px-6 md:px-8 py-2 md:py-3 text-xs md:text-sm font-black tracking-widest uppercase'
                        }
                    });
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px] md:text-[20px] group-hover:scale-110 transition-transform">search</span><span class="tracking-widest uppercase font-black">Coba Lagi</span>');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi gangguan koneksi ke server. Silakan coba lagi.',
                    confirmButtonColor: '#1173d4',
                    customClass: {
                        container: 'font-display',
                        popup: 'rounded-2xl md:rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)]',
                        confirmButton: 'rounded-xl px-6 md:px-8 py-2 md:py-3 text-xs md:text-sm font-black tracking-widest uppercase'
                    }
                });
            }
        });
    }
    </script>
</body>
</html>
