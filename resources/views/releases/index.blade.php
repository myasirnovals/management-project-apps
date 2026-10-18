<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <span class="text-slate-700">|</span>
                <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                    {{ $project->key }}
                </span>
                <span class="text-sm font-bold text-white">Manajemen Rilis & Versi (Modul V)</span>
            </div>
            <button type="button" onclick="openCreateVersionModal()"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Buat Versi Rilis</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Handbook explanation banner -->
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
            <strong>Mengapa Release Management Penting?</strong> Segala kegiatan seperti requirement, desain aplikasi, coding, dan testing, semua dilakukan demi untuk menghasilkan software yang bisa dirilis secara berkala dan terukur. Di sini Anda dapat mengunci ruang lingkup rilis (Release Scope Lock) dan melacak apakah semua target fitur siap sebelum diluncurkan.
        </div>

        <div class="space-y-6">
            @forelse($versions as $v)
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center font-bold text-xs">
                                    🚀
                                </span>
                                <h3 class="text-base font-bold text-white">{{ $v->name }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $v->status->badgeClasses() }}">
                                    {{ $v->status->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $v->description ?: 'Tidak ada deskripsi rilis.' }}
                            </p>
                        </div>

                        <!-- Action: Release or Delete -->
                        <div class="flex items-center gap-2">
                            @if($v->status->value === 'unreleased')
                                <button type="button"
                                    data-version-id="{{ $v->id }}"
                                    data-version-name="{{ $v->name }}"
                                    onclick="openReleaseModal(this)"
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/30 transition">
                                    Rilis Versi Ini (Release) &rarr;
                                </button>
                            @else
                                <span class="text-xs text-emerald-400 font-bold bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20">
                                    ✓ Dirilis pada {{ $v->release_date ? $v->release_date->format('d M Y') : '-' }}
                                </span>
                            @endif

                            <form method="POST" action="{{ route('projects.releases.destroy', [$project, $v]) }}" onsubmit="return confirm('Hapus versi rilis ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-400 transition" title="Hapus Versi">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Progress Statistics Bar (Modul V Gambar 5.14) -->
                    <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-400">Progres Kesiapan Rilis</span>
                            <div class="flex items-center gap-3">
                                <span class="text-emerald-400 font-bold">{{ $v->completed_issues_count }} Done</span>
                                <span class="text-amber-400 font-bold">{{ $v->remaining_issues_count }} Remaining</span>
                                <span class="text-white font-bold">({{ $v->progress_percentage }}%)</span>
                            </div>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full" @style(["width: {$v->progress_percentage}%"])></div>
                        </div>
                    </div>

                    <!-- Issues included in version -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Issue Terkait dalam Versi Ini:</h4>
                        <div class="space-y-1.5">
                            @forelse($v->issues as $issue)
                                <div class="p-2.5 bg-slate-950/40 border border-slate-800/80 rounded-xl flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-indigo-400 font-bold text-[11px]">{{ $issue->issue_key }}</span>
                                        <span class="text-slate-200">{{ $issue->title }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-slate-500 text-xs italic py-2">
                                    Belum ada issue yang dikaitkan dengan Fix Version ini. Anda dapat mengaitkannya saat membuat atau mengedit issue.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-slate-900 border border-slate-800 rounded-3xl text-slate-500 text-xs">
                    Belum ada versi rilis yang dibuat. Tekan tombol "+ Buat Versi Rilis" di kanan atas.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal: Create Version (Modul V Gambar 5.4) -->
    <div id="createVersionModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Create Version (Buat Versi)</h3>
                <button type="button" onclick="closeCreateVersionModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('projects.releases.store', $project) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Versi *</label>
                    <input type="text" name="name" required placeholder="Contoh: SistemInformasi Ver1.1"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Release Date</label>
                        <input type="date" name="release_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi Pembaruan</label>
                    <textarea name="description" rows="2" placeholder="Update website to include..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCreateVersionModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500">Simpan Versi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Release Confirmation (Scope Lock - Modul V Gambar 5.15) -->
    <div id="releaseScopeModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-base font-bold text-white mb-2" id="release_title">Rilis Versi</h3>
            <p class="text-xs text-slate-400 mb-4" id="release_desc"></p>

            <form id="releaseForm" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                        <input type="checkbox" name="ignore_unresolved" value="1" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600">
                        <span>Abaikan & tetap rilis meskipun masih ada issue yang belum selesai</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeReleaseModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500">Konfirmasi Rilis</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateVersionModal() { document.getElementById('createVersionModal').classList.remove('hidden'); }
        function closeCreateVersionModal() { document.getElementById('createVersionModal').classList.add('hidden'); }

        function openReleaseModal(el) {
            const versionId = el.dataset.versionId;
            const versionName = el.dataset.versionName;
            document.getElementById('release_title').innerText = `Konfirmasi Rilis ${versionName}`;
            document.getElementById('release_desc').innerText = `Memulai tahap deployment untuk ${versionName}.`;
            document.getElementById('releaseForm').action = `/projects/{{ $project->id }}/releases/${versionId}/release`;
            document.getElementById('releaseScopeModal').classList.remove('hidden');
        }
        function closeReleaseModal() { document.getElementById('releaseScopeModal').classList.add('hidden'); }
    </script>
</x-app-layout>
