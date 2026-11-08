<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Kelola Tim &amp; Anggota</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <button type="button" onclick="openModalDialog('addMemberModal')"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Tambah Anggota Baru</span>
        </button>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Users Table Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3">Nama &amp; Akun</th>
                            <th class="px-5 py-3">Peran (Role)</th>
                            <th class="px-5 py-3">Jabatan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Tugas &amp; Proyek</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-xs">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === Auth::id())
                                                <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-medium">(Anda)</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-2 text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400 font-mono">{{ $user->email }}</span>
                                            @if($user->phone)
                                                <span class="text-slate-400">•</span>
                                                <a href="{{ $user->whatsappUrl('Halo ' . $user->name) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 font-mono text-emerald-600 dark:text-emerald-400 hover:underline" title="Hubungi via WhatsApp">
                                                    <span>{{ $user->phone }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $user->role->badgeClass() }}">
                                    {{ $user->role->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">
                                {{ $user->job_title ?: 'Anggota Tim' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">
                                <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $user->assigned_issues_count }}</span> tugas •
                                <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $user->projects_count }}</span> proyek
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit button -->
                                    <button type="button"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-role="{{ $user->role->value }}"
                                        data-user-job-title="{{ $user->job_title }}"
                                        data-user-phone="{{ $user->phone }}"
                                        data-user-is-active="{{ $user->is_active ? '1' : '0' }}"
                                        onclick="openEditMemberModal(this)"
                                        class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-600 dark:text-slate-300 transition" title="Edit Anggota">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <!-- Delete button (cannot delete self) -->
                                    @if($user->id !== Auth::id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-600 dark:text-slate-300 transition" title="Hapus Anggota">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3.5 border-t border-slate-200 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Native Dialog: Add New Team Member -->
    <dialog id="addMemberModal" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Anggota Tim Baru</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Buat akun untuk rekan kerja yang bergabung.</p>
            </div>
            <button type="button" onclick="closeModalDialog('addMemberModal')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="p-5 space-y-4 text-xs">
            @csrf

            <!-- Name -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Achmad Maulana"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email (Digunakan untuk Login) *</label>
                <input type="email" name="email" required placeholder="Contoh: maulana@project.local"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- WhatsApp / Phone -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                    <span>Nomor WhatsApp (Aktif)</span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-normal">untuk follow-up &amp; pengingat</span>
                </label>
                <input type="text" name="phone" placeholder="Contoh: 08123456789 atau 628123456789"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Role -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran (Role) *</label>
                    <select name="role" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="developer" selected>Developer</option>
                        <option value="scrum_master">Scrum Master</option>
                        <option value="product_owner">Product Owner</option>
                        <option value="project_manager">Project Manager</option>
                        <option value="viewer">Viewer (Read Only)</option>
                    </select>
                </div>

                <!-- Job Title -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan</label>
                    <input type="text" name="job_title" placeholder="Contoh: QA, Frontend"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Initial Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300">Kata Sandi Awal *</label>
                    <button type="button" onclick="generatePassword('new_user_pwd')" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline">
                        Acak Sandi
                    </button>
                </div>
                <input id="new_user_pwd" type="text" name="password" required value="password123"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Berikan kata sandi ini kepada rekan tim untuk login pertama kali.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModalDialog('addMemberModal')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                    Simpan Anggota
                </button>
            </div>
        </form>
    </dialog>

    <!-- Native Dialog: Edit Team Member -->
    <dialog id="editMemberModal" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Data Anggota Tim</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Perbarui peran atau kredensial anggota tim.</p>
            </div>
            <button type="button" onclick="closeModalDialog('editMemberModal')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <form id="editMemberForm" method="POST" class="p-5 space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email *</label>
                <input type="email" id="edit_email" name="email" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- WhatsApp / Phone -->
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp (Aktif)</label>
                <input type="text" id="edit_phone" name="phone" placeholder="Contoh: 08123456789 atau 628123456789"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Role -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran (Role) *</label>
                    <select id="edit_role" name="role" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="developer">Developer</option>
                        <option value="scrum_master">Scrum Master</option>
                        <option value="product_owner">Product Owner</option>
                        <option value="project_manager">Project Manager</option>
                        <option value="viewer">Viewer (Read Only)</option>
                    </select>
                </div>

                <!-- Job Title -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan</label>
                    <input type="text" id="edit_job_title" name="job_title"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Status -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Akun</label>
                    <select id="edit_status" name="is_active" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <!-- Reset Password (optional) -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Sandi (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModalDialog('editMemberModal')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    <script>
        function openEditMemberModal(el) {
            document.getElementById('editMemberForm').action = '/users/' + el.dataset.userId;
            document.getElementById('edit_name').value = el.dataset.userName;
            document.getElementById('edit_email').value = el.dataset.userEmail;
            document.getElementById('edit_role').value = el.dataset.userRole;
            document.getElementById('edit_job_title').value = el.dataset.userJobTitle || '';
            document.getElementById('edit_phone').value = el.dataset.userPhone || '';
            document.getElementById('edit_status').value = el.dataset.userIsActive;
            openModalDialog('editMemberModal');
        }

        function generatePassword(targetId) {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
            let pass = '';
            for (let i = 0; i < 10; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(targetId).value = pass;
        }
    </script>
</x-app-layout>
