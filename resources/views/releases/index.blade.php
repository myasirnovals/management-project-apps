@php
    $canManageReleases = Auth::user()->canManageReleases();
@endphp

<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                {{ $project->key }}
            </span>
            <span class="text-base font-bold text-slate-900 dark:text-white">Manajemen Versi &amp; Rilis</span>
        </div>
    </x-slot>

    @if($canManageReleases)
        <x-slot name="headerActions">
            <button type="button" command="show-modal" commandfor="createVersionModal" class="btn-primary !text-xs !py-1.5 !px-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Versi Rilis</span>
            </button>
        </x-slot>
    @endif

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="app-card p-4 text-xs text-slate-700 dark:text-slate-300">
            <p>
                <strong>Manajemen Rilis:</strong> Kunci ruang lingkup rilis dan pantau kesiapan seluruh user story, tugas, atau bug fix sebelum peluncuran perangkat lunak ke pengguna akhir.
            </p>
        </div>

        <div class="space-y-4">
            @forelse($versions as $v)
                <div class="app-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $v->name }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $v->status->badgeClasses() }}">
                                    {{ $v->status->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $v->description ?: 'Tidak ada deskripsi rilis.' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            @if($v->status->value === 'unreleased')
                                @if($canManageReleases)
                                    <button type="button"
                                        data-version-id="{{ $v->id }}"
                                        data-version-name="{{ $v->name }}"
                                        onclick="openReleaseModal(this)"
                                        class="btn-primary !text-xs !py-1.5 !px-3">
                                        Rilis Versi Ini
                                    </button>
                                @endif
                            @else
                                <span class="text-xs text-emerald-800 dark:text-emerald-200 font-semibold bg-emerald-50 dark:bg-emerald-950 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                    Dirilis pada {{ $v->release_date ? $v->release_date->format('d M Y') : 'Selesai' }}
                                </span>
                            @endif

                            @if($canManageReleases)
                                <button type="button"
                                    data-id="{{ $v->id }}"
                                    data-name="{{ $v->name }}"
                                    data-desc="{{ $v->description }}"
                                    data-start="{{ $v->start_date ? $v->start_date->format('Y-m-d') : '' }}"
                                    data-release="{{ $v->release_date ? $v->release_date->format('Y-m-d') : '' }}"
                                    onclick="openEditVersionModal(this)"
                                    class="btn-secondary !text-xs !py-1 !px-2.5">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('projects.releases.destroy', [$project, $v]) }}" onsubmit="return confirm('Hapus versi rilis ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !text-xs !py-1 !px-2.5" title="Hapus Versi">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between text-xs mb-1.5 flex-wrap gap-2">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">Kesiapan Rilis</span>
                            <div class="flex items-center gap-3 font-mono">
                                <span class="text-emerald-700 dark:text-emerald-300 font-bold">{{ $v->completed_issues_count }} Selesai</span>
                                <span class="text-amber-700 dark:text-amber-300 font-bold">{{ $v->remaining_issues_count }} Tersisa</span>
                                <span class="text-slate-800 dark:text-white font-bold">({{ $v->progress_percentage }}%)</span>
                            </div>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" @style(["width: {$v->progress_percentage}%"])></div>
                        </div>
                    </div>

                    <!-- Issues List in this Version -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Issue Terkait:</h4>
                        <div class="space-y-1.5">
                            @forelse($v->issues as $issue)
                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-between text-xs gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono text-blue-700 dark:text-blue-300 font-bold text-[11px] shrink-0">{{ $issue->issue_key }}</span>
                                        <span class="text-slate-800 dark:text-slate-200 truncate">{{ $issue->title }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium border shrink-0 {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-slate-500 dark:text-slate-400 text-xs italic py-1">
                                    Belum ada issue yang dikaitkan ke versi ini. Pilih versi ini pada formulir issue untuk menautkannya.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="app-card p-8 text-center text-xs text-slate-500 dark:text-slate-400">
                    Belum ada versi rilis yang dibuat.
                </div>
            @endforelse
        </div>
    </div>

    @if($canManageReleases)
        <!-- Modal: Create Version (<dialog>) -->
        <dialog id="createVersionModal" closedby="any" aria-labelledby="create_ver_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="create_ver_title" class="text-base font-bold text-slate-900 dark:text-white">Buat Versi Rilis Baru</h3>
                    <button type="button" command="close" commandfor="createVersionModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form method="POST" action="{{ route('projects.releases.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="create_ver_name" class="form-label">Nama Versi *</label>
                        <input id="create_ver_name" type="text" name="name" required placeholder="Contoh: v1.0.0 atau Rilis Kasir Q1"
                            class="form-input">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_ver_start" class="form-label">Tanggal Mulai</label>
                            <input id="create_ver_start" type="date" name="start_date" value="{{ date('Y-m-d') }}"
                                class="form-input">
                        </div>
                        <div>
                            <label for="create_ver_release" class="form-label">Target Tanggal Rilis</label>
                            <input id="create_ver_release" type="date" name="release_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                                class="form-input">
                        </div>
                    </div>

                    <div>
                        <label for="create_ver_desc" class="form-label">Deskripsi Pembaruan</label>
                        <textarea id="create_ver_desc" name="description" rows="2" placeholder="Catatan rilis atau fokus modul..."
                            class="form-input"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="createVersionModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Simpan Versi</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Modal: Edit Version (<dialog>) -->
        <dialog id="editVersionModal" closedby="any" aria-labelledby="edit_ver_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="edit_ver_title" class="text-base font-bold text-slate-900 dark:text-white">Edit Versi Rilis</h3>
                    <button type="button" command="close" commandfor="editVersionModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form id="editVersionForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_ver_name" class="form-label">Nama Versi *</label>
                        <input id="edit_ver_name" type="text" name="name" required class="form-input">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_ver_start" class="form-label">Tanggal Mulai</label>
                            <input id="edit_ver_start" type="date" name="start_date" class="form-input">
                        </div>
                        <div>
                            <label for="edit_ver_release" class="form-label">Target Tanggal Rilis</label>
                            <input id="edit_ver_release" type="date" name="release_date" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label for="edit_ver_desc" class="form-label">Deskripsi Pembaruan</label>
                        <textarea id="edit_ver_desc" name="description" rows="2" class="form-input"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="editVersionModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Perbarui Versi</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Modal: Release Scope Confirmation (<dialog>) -->
        <dialog id="releaseScopeModal" closedby="any" aria-labelledby="release_title">
            <div class="p-5 sm:p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2" id="release_title">Konfirmasi Rilis</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-4" id="release_desc"></p>

                <form id="releaseForm" method="POST" class="space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="ignore_unresolved" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-700 focus:ring-blue-600">
                            <span>Tetap rilis meskipun masih terdapat issue yang belum berstatus selesai</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="releaseScopeModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Konfirmasi Rilis</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            function openEditVersionModal(btn) {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                const desc = btn.dataset.desc || '';
                const start = btn.dataset.start || '';
                const release = btn.dataset.release || '';

                const form = document.getElementById('editVersionForm');
                if (form) form.action = `/projects/{{ $project->code }}/releases/${id}`;
                const nameEl = document.getElementById('edit_ver_name');
                if (nameEl) nameEl.value = name;
                const descEl = document.getElementById('edit_ver_desc');
                if (descEl) descEl.value = desc;
                const startEl = document.getElementById('edit_ver_start');
                if (startEl) startEl.value = start;
                const relEl = document.getElementById('edit_ver_release');
                if (relEl) relEl.value = release;

                openModalDialog('editVersionModal');
            }

            function openReleaseModal(el) {
                const versionId = el.dataset.versionId;
                const versionName = el.dataset.versionName;
                document.getElementById('release_title').innerText = `Konfirmasi Rilis ${versionName}`;
                document.getElementById('release_desc').innerText = `Tandai versi ${versionName} sebagai selesai dirilis.`;
                document.getElementById('releaseForm').action = `/projects/{{ $project->code }}/releases/${versionId}/release`;
                openModalDialog('releaseScopeModal');
            }
        </script>
    @endif
</x-app-layout>
