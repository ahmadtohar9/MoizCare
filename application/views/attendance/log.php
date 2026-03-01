<!-- Page Header -->
<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Absensi Cerdas</span>
    </div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Presensi Lokasi & Selfie</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gunakan GPS dan Kamera untuk mencatat kehadiran kerja.</p>
        </div>
        <div id="liveClock" class="text-3xl font-black text-primary font-mono tabular-nums bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm">00:00:00</div>
    </div>
</div>

<div class="px-6 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">
        
        <!-- Left: Camera & Presence Control -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="bg-white rounded-[2.5rem] p-4 border border-gray-100 shadow-sm relative overflow-hidden group">
                <!-- Camera View -->
                <div class="aspect-video md:aspect-[16/9] bg-gray-900 rounded-[2rem] overflow-hidden relative shadow-inner">
                    <video id="webcam" autoplay playsinline class="w-full h-full object-cover -scale-x-100"></video>
                    <canvas id="photoCanvas" class="hidden"></canvas>
                    
                    <!-- Overlay Info -->
                    <div class="absolute inset-0 pointer-events-none p-6 flex flex-col justify-between">
                        <div class="flex justify-start">
                            <div class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-xl text-white flex items-center gap-2 border border-white/20">
                                <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest">Live Camera</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-end">
                            <div id="locationBadge" class="bg-black/40 backdrop-blur-md px-4 py-3 rounded-2xl text-white border border-white/20 flex items-center gap-3">
                                <span class="material-symbols-outlined text-orange-400">my_location</span>
                                <div>
                                    <p class="text-[8px] font-black uppercase opacity-60 leading-none">Status Lokasi</p>
                                    <p id="distanceText" class="text-xs font-black mt-1">Mendeteksi GPS...</p>
                                </div>
                            </div>
                            <div class="bg-black/40 backdrop-blur-md px-4 py-3 rounded-2xl text-white border border-white/20 flex items-center gap-3">
                                <span class="material-symbols-outlined text-blue-400">schedule</span>
                                <div>
                                    <p class="text-[8px] font-black uppercase opacity-60 leading-none">Shift Terdeteksi</p>
                                    <p class="text-xs font-black mt-1"><?= $today_shift ? $today_shift->shift_name . ' (' . substr($today_shift->start_time, 0, 5) . ')' : 'JADWAL OFF' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="flex flex-col md:flex-row gap-4 mt-6 p-2">
                    <?php if(!$today_log): ?>
                        <button onclick="takePicture('in')" id="btnPunch" class="flex-1 py-5 bg-blue-600 hover:bg-blue-700 text-white rounded-3xl shadow-xl shadow-blue-600/20 font-black uppercase tracking-widest flex items-center justify-center gap-3 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale">
                            <span class="material-symbols-outlined text-2xl">fingerprint</span>
                            Check In Masuk
                        </button>
                    <?php elseif($today_log->clock_in && !$today_log->clock_out): ?>
                        <!-- Sudah Masuk, Belum Pulang -->
                        <?php 
                        $role = $this->session->userdata('role');
                        $is_early = false;
                        $end_ts_val = null;
                        if($today_shift) {
                            $start_ts = strtotime($today_log->date . ' ' . $today_shift->start_time);
                            $end_ts = strtotime($today_log->date . ' ' . $today_shift->end_time);
                            
                            // Adjust if shift crosses midnight
                            if($end_ts < $start_ts) {
                                $end_ts += 86400;
                            }
                            $end_ts_val = $end_ts * 1000; // for javascript (milliseconds)
                            $is_early = (time() < $end_ts);
                        }
                        $can_punch_out = (!$is_early);
                        ?>
                        <button onclick="takePicture('out')" id="btnPunch" 
                                data-end-ts="<?= $end_ts_val ?>"
                                class="w-full py-5 bg-rose-600 hover:bg-rose-700 text-white rounded-3xl shadow-xl shadow-rose-600/20 font-black uppercase tracking-widest flex items-center justify-center gap-3 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
                                <?= !$can_punch_out ? 'disabled' : '' ?>>
                            <span class="material-symbols-outlined text-2xl">exit_to_app</span>
                            Check Out Pulang
                        </button>

                        <?php if(!$can_punch_out): ?>
                            <div id="earlyWarning" class="mt-4 px-4 py-3 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3">
                                <span class="material-symbols-outlined text-rose-500 animate-pulse">lock</span>
                                <p class="text-[10px] font-bold text-rose-700 uppercase tracking-widest leading-tight">
                                    Tombol terkunci sampai jam <?= substr($today_shift->end_time, 0, 5) ?>.
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="flex-1 p-6 bg-green-50 rounded-3xl border border-green-100 flex items-center justify-center gap-4">
                            <span class="material-symbols-outlined text-green-600 text-4xl">verified</span>
                            <div class="text-green-700 font-black uppercase tracking-widest">Presensi Hari Ini Telah Lengkap</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Log Status & Map Summary -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Today's Info -->
            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] opacity-40 mb-6 font-mono">Status Harian</h3>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10">
                            <span class="material-symbols-outlined text-indigo-400">login</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase opacity-40 leading-none">Absen Masuk</p>
                            <p class="text-xl font-black mt-1"><?= $today_log && $today_log->clock_in ? date('H:i:s', strtotime($today_log->clock_in)) : '-- : --' ?></p>
                        </div>
                        <?php if($today_log && $today_log->photo_in): ?>
                            <img src="<?= base_url($today_log->photo_in) ?>" class="size-12 rounded-xl object-cover border-2 border-white/20 ml-auto pointer-events-auto cursor-zoom-in" onclick="Swal.fire({imageUrl: this.src})">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10">
                            <span class="material-symbols-outlined text-rose-400">logout</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase opacity-40 leading-none">Absen Pulang</p>
                            <p class="text-xl font-black mt-1"><?= $today_log && $today_log->clock_out ? date('H:i:s', strtotime($today_log->clock_out)) : '-- : --' ?></p>
                        </div>
                        <?php if($today_log && $today_log->photo_out): ?>
                            <img src="<?= base_url($today_log->photo_out) ?>" class="size-12 rounded-xl object-cover border-2 border-white/20 ml-auto pointer-events-auto cursor-zoom-in" onclick="Swal.fire({imageUrl: this.src})">
                        <?php endif; ?>
                    </div>

                    <div class="pt-6 mt-6 border-t border-white/10">
                         <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold opacity-40 uppercase tracking-widest">Keterangan</span>
                            <?php if($today_log): ?>
                                <span class="px-3 py-1 rounded-full bg-white/10 text-[9px] font-black uppercase tracking-widest border border-white/10"><?= $today_log->status ?></span>
                            <?php endif; ?>
                         </div>
                         <p class="text-xs font-bold leading-relaxed"><?= $today_shift ? "Shift hari ini: {$today_shift->shift_name} ({$today_shift->start_time} - {$today_shift->end_time})" : "Anda libur hari ini." ?></p>
                    </div>
                </div>
            </div>

            <!-- Distance Alert -->
            <div id="distanceCard" class="bg-gray-50 rounded-[2.5rem] p-8 border-2 border-dashed border-gray-200">
                <div class="flex flex-col items-center text-center gap-4">
                    <span class="material-symbols-outlined text-gray-300 text-5xl">radar</span>
                    <div>
                        <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Radius Absensi</p>
                        <p class="text-[11px] font-bold text-gray-400 leading-relaxed mt-2">Pastikan anda berada di radius <span class="text-blue-600"><?= $settings->radius_meters ?> Meter</span> dari <?= $settings->company_name ?>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let userLat = null, userLong = null;
    let stream = null;

    // 1. Live Clock & Auto-Unlock
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour12: false });
        $('#liveClock').text(time);

        // Auto unlock Check-out button
        const btn = $('#btnPunch');
        const endTs = parseInt(btn.data('end-ts'));
        const userRole = '<?= $this->session->userdata("role") ?>';

        if(!isNaN(endTs) && userRole !== 'admin') {
            if(Date.now() >= endTs) {
                btn.prop('disabled', false);
                $('#earlyWarning').fadeOut();
            }
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Camera Start
    const video = document.getElementById('webcam');
    async function startCamera() {
        const constraints = [
            { video: { facingMode: "user" } }, // Coba kamera depan dulu
            { video: true } // Kalau gagal, coba kamera apa aja yang ada
        ];

        let success = false;
        for (const constraint of constraints) {
            try {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                stream = await navigator.mediaDevices.getUserMedia(constraint);
                video.srcObject = stream;
                success = true;
                console.log("Camera started with constraint:", constraint);
                break; 
            } catch (err) {
                console.warn("Failed to start camera with constraint:", constraint, err);
            }
        }

        if (!success) {
            Swal.fire({
                title: 'Kamera Tidak Terdeteksi',
                text: 'Pastikan kamera sudah terpasang, izin (Permission) sudah di-Allow, dan tidak sedang dipakai aplikasi lain.',
                icon: 'warning',
                confirmButtonText: 'Coba Lagi'
            }).then(() => location.reload());
            $('#btnPunch').prop('disabled', true);
        }
    }
    startCamera();

    // 3. Geolocation Tracker
    function trackLocation() {
        if (!navigator.geolocation) {
            $('#distanceText').text('GPS Tidak Didukung');
            return;
        }

        navigator.geolocation.watchPosition(
            (pos) => {
                userLat = pos.coords.latitude;
                userLong = pos.coords.longitude;
                updateDistanceUI();
            },
            (err) => {
                $('#distanceText').text('Gagal Akses GPS');
                $('#btnPunch').prop('disabled', true);
            },
            { enableHighAccuracy: true }
        );
    }
    trackLocation();

    function updateDistanceUI() {
        // Calculate Distance client-side for feedback
        const hospitalLat = <?= $settings->latitude ?>;
        const hospitalLong = <?= $settings->longitude ?>;
        const radiusLimit = <?= $settings->radius_meters ?>;

        const distance = getDistance(userLat, userLong, hospitalLat, hospitalLong);
        
        if (distance <= radiusLimit) {
            $('#distanceText').text('Dalam Jangkauan (' + Math.round(distance) + 'm)').addClass('text-green-400').removeClass('text-red-400');
            $('#locationBadge').addClass('bg-green-500/30 border-green-500/20');
            $('#btnPunch').prop('disabled', false);
        } else {
            $('#distanceText').text('Luar Jangkauan (' + Math.round(distance) + 'm)').addClass('text-red-400').removeClass('text-green-400');
            $('#locationBadge').removeClass('bg-green-500/30');
            $('#btnPunch').prop('disabled', true);
        }
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // meters
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // 4. Presence Action
    function takePicture(type) {
        const canvas = document.getElementById('photoCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        
        // Flip canvas for selfie
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg');

        Swal.fire({
            title: 'Kirim Presensi?',
            text: 'Pastikan wajah terlihat jelas.',
            imageUrl: imageData,
            imageWidth: 200,
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Sekarang'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("attendance/punch") ?>',
                    type: 'POST',
                    data: {
                        type: type,
                        lat: userLat,
                        long: userLong,
                        image: imageData
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }
                });
            }
        });
    }
</script>
