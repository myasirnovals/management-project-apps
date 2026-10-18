<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Pengaturan Proyek: {{ $project->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Project General Settings (Modul IV.B Gambar 4.3 - 4.6) -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-white">Pengaturan Umum Proyek</h3>
                <p class="text-xs text-slate-400 mt-0.5">Konfigurasi nama, deskripsi, dan kerangka kerja proyek ini.</p>
            </div>

            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Proyek *</label>
                        <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Project Key (Permanen)</label>
                        <input type="text" value="{{ $project->key }}" disabled
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-400 font-mono font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tipe Kerangka Kerja</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="scrum" {{ $project->type === 'scrum' ? 'selected' : '' }}>Scrum (Sprint-based)</option>
                            <option value="kanban" {{ $project->type === 'kanban' ? 'selected' : '' }}>Kanban (Continuous flow)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status Proyek</label>
                        <select name="status" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Diarsipkan (Archived)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Project Members Section (Modul I Gambar 1.28 - 1.31) -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Anggota Tim Proyek (Teammates)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tentukan siapa saja yang memiliki akses dan peran dalam proyek {{ $project->name }}.</p>
                </div>
            </div>

            <!-- Form: Add member to project -->
            <form method="POST" action="{{ route('projects.members.add', $project) }}" class="p-4 bg-slate-950/60 border border-slate-800 rounded-2xl flex flex-col sm:flex-row items-end gap-3">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Anggota Tim</label>
                    <select name="user_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih dari Akun Terdaftar --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }}) - {{ $u->role->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Peran di Proyek Ini</label>
                    <select name="role" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="developer" selected>Developer</option>
                        <option value="scrum_master">Scrum Master</option>
                        <option value="product_owner">Product Owner</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shrink-0">
                    + Tambahkan
                </button>
            </form>

            <!-- Members List Table -->
            <div class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden">
                @foreach($project->members as $member)
                    <div class="p-4 bg-slate-950/40 flex items-center justify-between hover:bg-slate-800/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-xs text-white">
                                {{ $member->initials() }}
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white flex items-center gap-2">
                                    <span>{{ $member->name }}</span>
                                    @if($member->id === $project->owner_id)
                                        <span class="text-[10px] text-amber-400 bg-amber-500/10 px-2 py-0.2 rounded-full border border-amber-500/20">Owner</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400">{{ $member->email }} • {{ $member->pivot->role }}</div>
                            </div>
                        </div>

                        @if($member->id !== $project->owner_id)
                            <form method="POST" action="{{ route('projects.members.remove', [$project, $member]) }}" onsubmit="return confirm('Keluarkan anggota ini dari proyek?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold p-1 hover:bg-rose-500/10 rounded transition">
                                    Keluarkan
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-rose-950/20 border border-rose-900/40 rounded-3xl p-6 sm:p-8 space-y-4">
            <h4 class="text-sm font-bold text-rose-400 uppercase tracking-wider">Zona Bahaya</h4>
            <p class="text-xs text-slate-400">
                Menghapus proyek ini akan menghapus semua issue, sprint, komponen, dan rilis yang terkait secara permanen. Tindakan ini tidak dapat dibatalkan.
            </p>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 transition">
                    Hapus Proyek Ini
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
