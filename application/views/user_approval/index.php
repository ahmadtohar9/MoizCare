<div class="px-6 py-4 flex flex-col gap-1">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
        <a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a>
        <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
        <span class="text-primary">Manajemen Akses</span>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-tight">Manajemen Akun User</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola persetujuan registrasi dan status aktifasi akun perawat/staf.</p>
        </div>
    </div>
</div>

<div class="px-6">
    <!-- Tab Navigation -->
    <div class="flex gap-8 border-b border-gray-100 mb-6">
        <button onclick="switchTab('pending')" id="tab-pending" class="px-2 py-4 text-xs font-black uppercase tracking-widest border-b-2 border-primary text-primary transition-all">Antrean Approval</button>
        <button onclick="switchTab('all')" id="tab-all" class="px-2 py-4 text-xs font-black uppercase tracking-widest border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all">Seluruh Pengguna</button>
    </div>

    <!-- Content: Pending Users -->
    <div id="section-pending" class="tab-content transition-all duration-300">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Pegawai</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Departemen</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php 
                    $pending_count = 0;
                    foreach($all_users as $u): if($u->status !== 'pending') continue; $pending_count++;
                    ?>
                    <tr class="hover:bg-gray-50/50 transition-all" id="user-row-<?= $u->id ?>">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xs">
                                    <?= substr($u->full_name, 0, 2) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900"><?= $u->full_name ?></p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">NIP: <?= $u->nip ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest border border-blue-100 text-center mb-1"><?= $u->unit_name ?></span>
                                <select id="role-<?= $u->id ?>" class="w-40 border-none bg-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest px-3 py-2.5 focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
                                    <option value="user" <?= $u->role === 'user' ? 'selected' : '' ?>>USER / STAF</option>
                                    <option value="karu" <?= $u->role === 'karu' ? 'selected' : '' ?>>KARU / KEPALA</option>
                                    <option value="admin" <?= $u->role === 'admin' ? 'selected' : '' ?>>HRD / ADMIN</option>
                                </select>
                            </div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="processUser(<?= $u->id ?>, 'approve')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95">Approve</button>
                                <button onclick="processUser(<?= $u->id ?>, 'reject')" class="bg-white text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-100 transition-all">Tolak</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if($pending_count == 0): ?>
                    <tr><td colspan="3" class="px-8 py-20 text-center text-gray-300 font-bold italic">Tidak ada registrasi baru yang menunggu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Content: All Users -->
    <div id="section-all" class="tab-content hidden transition-all duration-300">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Pegawai</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Role</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($all_users as $u): ?>
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center font-black text-xs">
                                    <?= substr($u->full_name, 0, 2) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900"><?= $u->full_name ?></p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><?= $u->unit_name ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <select id="role-all-<?= $u->id ?>" onchange="processUser(<?= $u->id ?>, 'update_role', this.value)" class="w-40 border-none bg-blue-50 text-blue-700 rounded-xl text-[10px] font-black uppercase tracking-widest px-3 py-2.5 focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
                                <option value="user" <?= $u->role === 'user' ? 'selected' : '' ?>>USER / STAF</option>
                                <option value="karu" <?= $u->role === 'karu' ? 'selected' : '' ?>>KARU / KEPALA</option>
                                <option value="admin" <?= $u->role === 'admin' ? 'selected' : '' ?>>HRD / ADMIN</option>
                            </select>
                        </td>
                        <td class="px-8 py-4">
                            <?php 
                            $status_styles = [
                                'active' => 'bg-green-50 text-green-600 border-green-100',
                                'pending' => 'bg-orange-50 text-orange-600 border-orange-100',
                                'inactive' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'rejected' => 'bg-red-50 text-red-600 border-red-100'
                            ];
                            ?>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border <?= $status_styles[$u->status] ?>">
                                <?= $u->status ?>
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <?php if($u->status === 'active'): ?>
                                    <button onclick="processUser(<?= $u->id ?>, 'block')" class="text-red-500 hover:bg-red-50 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Nonaktifkan</button>
                                <?php elseif($u->status === 'inactive' || $u->status === 'rejected'): ?>
                                    <button onclick="processUser(<?= $u->id ?>, 'activate')" class="text-green-600 hover:bg-green-50 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Aktifkan Kembali</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    $('.tab-content').addClass('hidden');
    $(`#section-${tab}`).removeClass('hidden');
    
    // Change Tab Styles
    $('#tab-pending, #tab-all').removeClass('border-primary text-primary').addClass('border-transparent text-gray-400');
    $(`#tab-${tab}`).addClass('border-primary text-primary').removeClass('border-transparent text-gray-400');
}

function processUser(id, action, roleValue = null) {
    let title = 'Konfirmasi Perubahan?';
    if(action === 'approve') title = 'Terima Registrasi?';
    if(action === 'block') title = 'Nonaktifkan Akun?';
    if(action === 'activate') title = 'Aktifkan Akun?';
    if(action === 'update_role') title = 'Ubah Role User?';

    let selectedRole = roleValue;
    if (!selectedRole) {
        // Try to find the dropdown in either tab
        selectedRole = $(`#role-${id}`).val() || $(`#role-all-${id}`).val();
    }

    Swal.fire({
        title: title,
        text: action === 'update_role' ? "Role user akan diperbarui." : "Status akses user akan langsung berubah.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan'
    }).then((r) => {
        if (r.isConfirmed) {
            $.ajax({
                url: '<?= base_url("user_approval/process/") ?>' + id + '/' + action,
                type: 'POST',
                data: { role: selectedRole },
                dataType: 'json',
                success: function(res) {
                    Toast.fire({icon: 'success', title: res.message}).then(() => {
                        if(action !== 'update_role') location.reload();
                    });
                }
            });
        } else if (action === 'update_role') {
            location.reload(); // Revert dropdown if cancelled
        }
    });
}
</script>

