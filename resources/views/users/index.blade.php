<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                <span>Kelola Tim & Anggota</span>
                <span class="text-xs font-normal text-slate-400 bg-slate-800 px-2.5 py-1 rounded-full">
                    Total: {{ $users->total() }} Akun
                </span>
            </div>
            <button type="button" onclick="openAddMemberModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>+ Tambah Anggota Baru</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Architecture Explanation Banner -->
        <div class="bg-indigo-950/30 border border-indigo-500/20 rounded-2xl p-4 flex items-start gap-3">
            <div class="p-2 bg-indigo-500/10 text-indigo-400 rounded-xl shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="text-xs text-indigo-200">
                <strong class="font-bold text-indigo-300 block mb-0.5">Sistem Autentikasi Khusus (No Public Registration)</strong>
                Aplikasi ini dirancang khusus untuk lingkungan profesional/akademik tertutup: pengunjung umum tidak dapat mendaftar sendiri. Anda sebagai Project Manager memiliki wewenang penuh untuk menambahkan akun anggota tim, menentukan role (Product Owner, Scrum Master, Developer), serta memberikan kredensial login kepada mereka.
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Nama & Akun</th>
                            <th class="px-6 py-4">Peran (Role)</th>
                            <th class="px-6 py-4">Jabatan (Job Title)</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tugas & Proyek</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white text-xs ring-2 ring-indigo-500/20">
                                            {{ $user->initials() }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-100 flex items-center gap-1.5">
                                                <span>{{ $user->name }}</span>
                                                @if($user->id === Auth::id())
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-800 text-indigo-400 font-medium">(Anda)</span>
                                                @endif
                                            </div>
                                            <div class="text-slate-400 font-mono text-[11px]">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $user->role->badgeClass() }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-300">
                                    {{ $user->job_title ?: 'Anggota Tim' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-500 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    <span class="text-slate-200 font-bold">{{ $user->assigned_issues_count }}</span> tugas • 
                                    <span class="text-slate-200 font-bold">{{ $user->projects_count }}</span> proyek
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit button -->
                                        <button type="button"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role->value }}"
                                            data-user-job-title="{{ $user->job_title }}"
                                            data-user-is-active="{{ $user->is_active ? '1' : '0' }}"
                                            onclick="openEditMemberModal(this)"
                                            class="p-1.5 rounded-lg bg-slate-800 hover:bg-indigo-600 hover:text-white text-slate-400 transition" title="Edit Anggota">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <!-- Delete button (cannot delete self) -->
                                        @if($user->id !== Auth::id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-400 transition" title="Hapus Anggota">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

            <div class="p-4 border-t border-slate-800">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Add New Team Member -->
    <div id="addMemberModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-white">Tambah Anggota Tim Baru</h3>
                    <p class="text-xs text-slate-400">Buat kredensial login untuk rekan kerja yang bergabung.</p>
                </div>
                <button type="button" onclick="closeAddMemberModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Contoh: Achmad Maulana"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email (Digunakan untuk Login) *</label>
                    <input type="email" name="email" required placeholder="Contoh: maulana@project.local"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Peran (Scrum Role) *</label>
                        <select name="role" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="developer" selected>Developer</option>
                            <option value="scrum_master">Scrum Master</option>
                            <option value="product_owner">Product Owner</option>
                            <option value="project_manager">Project Manager</option>
                            <option value="viewer">Viewer (Read Only)</option>
                        </select>
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Jabatan / Spesialisasi</label>
                        <input type="text" name="job_title" placeholder="e.g. Frontend, QA, UI/UX"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Initial Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Kata Sandi Awal *</label>
                        <button type="button" onclick="generatePassword('new_user_pwd')" class="text-[11px] text-indigo-400 hover:underline">
                            Acak Sandi
                        </button>
                    </div>
                    <input id="new_user_pwd" type="text" name="password" required value="password123"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-500 mt-1">Berikan kata sandi ini kepada rekan tim Anda untuk login pertama kali.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        Simpan Anggota
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Team Member -->
    <div id="editMemberModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-white">Edit Data Anggota Tim</h3>
                    <p class="text-xs text-slate-400" id="editMemberSubhead">Perbarui role atau kredensial anggota tim.</p>
                </div>
                <button type="button" onclick="closeEditMemberModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form id="editMemberForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Lengkap *</label>
                    <input type="text" id="edit_name" name="name" required
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email *</label>
                    <input type="email" id="edit_email" name="email" required
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Peran (Role) *</label>
                        <select id="edit_role" name="role" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="developer">Developer</option>
                            <option value="scrum_master">Scrum Master</option>
                            <option value="product_owner">Product Owner</option>
                            <option value="project_manager">Project Manager</option>
                            <option value="viewer">Viewer (Read Only)</option>
                        </select>
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Jabatan</label>
                        <input type="text" id="edit_job_title" name="job_title"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Status Akun</label>
                        <select id="edit_status" name="is_active" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    <!-- Reset Password (optional) -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Ganti Sandi (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddMemberModal() {
            document.getElementById('addMemberModal').classList.remove('hidden');
        }

        function closeAddMemberModal() {
            document.getElementById('addMemberModal').classList.add('hidden');
        }

        function openEditMemberModal(el) {
            document.getElementById('editMemberForm').action = '/users/' + el.dataset.userId;
            document.getElementById('edit_name').value = el.dataset.userName;
            document.getElementById('edit_email').value = el.dataset.userEmail;
            document.getElementById('edit_role').value = el.dataset.userRole;
            document.getElementById('edit_job_title').value = el.dataset.userJobTitle || '';
            document.getElementById('edit_status').value = el.dataset.userIsActive;
            document.getElementById('editMemberModal').classList.remove('hidden');
        }

        function closeEditMemberModal() {
            document.getElementById('editMemberModal').classList.add('hidden');
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
