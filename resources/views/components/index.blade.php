@php
    $canManageComponents = Auth::user()->canManageComponents();
@endphp

<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                {{ $project->key }}
            </span>
            <span class="text-base font-bold text-slate-900 dark:text-white">Komponen Arsitektur Proyek</span>
        </div>
    </x-slot>

    @if($canManageComponents)
        <x-slot name="headerActions">
            <button type="button" command="show-modal" commandfor="createComponentModal" class="btn-primary !text-xs !py-1.5 !px-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Komponen</span>
            </button>
        </x-slot>
    @endif

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="app-card p-4 text-xs text-slate-700 dark:text-slate-300">
            <p>
                <strong>Komponen Modul:</strong> Mengelompokkan issue dan backlog ke dalam sub-sistem atau lapisan fungsional (seperti Frontend, Backend API, Basis Data, atau Keamanan) untuk memudahkan pembagian penanggung jawab teknis.
            </p>
        </div>

        <div class="app-card">
            <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Komponen</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola penanggung jawab dan pemetaan issue</p>
                </div>
                <span class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400">
                    {{ $components->count() }} Komponen
                </span>
            </div>

            <div class="table-wrapper">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 text-xs border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th scope="col" class="px-5 py-3">Nama Komponen</th>
                            <th scope="col" class="px-5 py-3">Deskripsi</th>
                            <th scope="col" class="px-5 py-3">Penanggung Jawab (Lead)</th>
                            <th scope="col" class="px-5 py-3">Issue Terkait</th>
                            @if($canManageComponents)
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-xs">
                        @forelse($components as $comp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $comp->name }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 dark:text-slate-400 max-w-sm">
                                    {{ $comp->description ?: 'Tidak ada deskripsi.' }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($comp->lead)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 text-[10px] font-bold">
                                                {{ $comp->lead->initials() }}
                                            </span>
                                            <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $comp->lead->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic">Default Proyek</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[11px] font-semibold">
                                        {{ $comp->issues_count }} issue
                                    </span>
                                </td>
                                @if($canManageComponents)
                                    <td class="px-5 py-3 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button"
                                                data-id="{{ $comp->id }}"
                                                data-name="{{ $comp->name }}"
                                                data-desc="{{ $comp->description }}"
                                                data-lead="{{ $comp->lead_id }}"
                                                onclick="openEditComponentModal(this)"
                                                class="btn-secondary !text-xs !py-1 !px-2.5">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('projects.components.destroy', [$project, $comp]) }}" onsubmit="return confirm('Hapus komponen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger !text-xs !py-1 !px-2.5">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageComponents ? 5 : 4 }}" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada komponen yang dibuat untuk proyek ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($canManageComponents)
        <!-- Modal Create Component -->
        <dialog id="createComponentModal" closedby="any" aria-labelledby="create_comp_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="create_comp_title" class="text-base font-bold text-slate-900 dark:text-white">Buat Komponen Baru</h3>
                    <button type="button" command="close" commandfor="createComponentModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form method="POST" action="{{ route('projects.components.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="create_comp_name" class="form-label">Nama Komponen *</label>
                        <input id="create_comp_name" type="text" name="name" required placeholder="Contoh: Frontend, Backend API, Kasir"
                            class="form-input">
                    </div>

                    <div>
                        <label for="create_comp_desc" class="form-label">Deskripsi</label>
                        <textarea id="create_comp_desc" name="description" rows="2" placeholder="Cakupan teknis atau modul sistem..."
                            class="form-input"></textarea>
                    </div>

                    <div>
                        <label for="create_comp_lead" class="form-label">Penanggung Jawab (Lead)</label>
                        <select id="create_comp_lead" name="lead_id" class="form-select">
                            <option value="">Default Proyek</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="createComponentModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Simpan Komponen</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Modal Edit Component -->
        <dialog id="editComponentModal" closedby="any" aria-labelledby="edit_comp_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="edit_comp_title" class="text-base font-bold text-slate-900 dark:text-white">Edit Komponen</h3>
                    <button type="button" command="close" commandfor="editComponentModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form id="editComponentForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_comp_name" class="form-label">Nama Komponen *</label>
                        <input id="edit_comp_name" type="text" name="name" required class="form-input">
                    </div>

                    <div>
                        <label for="edit_comp_desc" class="form-label">Deskripsi</label>
                        <textarea id="edit_comp_desc" name="description" rows="2" class="form-input"></textarea>
                    </div>

                    <div>
                        <label for="edit_comp_lead" class="form-label">Penanggung Jawab (Lead)</label>
                        <select id="edit_comp_lead" name="lead_id" class="form-select">
                            <option value="">Default Proyek</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="editComponentModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Perbarui Komponen</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            function openEditComponentModal(btn) {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                const desc = btn.dataset.desc || '';
                const lead = btn.dataset.lead || '';

                const form = document.getElementById('editComponentForm');
                if (form) form.action = `/projects/{{ $project->code }}/components/${id}`;
                const nameEl = document.getElementById('edit_comp_name');
                if (nameEl) nameEl.value = name;
                const descEl = document.getElementById('edit_comp_desc');
                if (descEl) descEl.value = desc;
                const leadEl = document.getElementById('edit_comp_lead');
                if (leadEl) leadEl.value = lead;

                openModalDialog('editComponentModal');
            }
        </script>
    @endif
</x-app-layout>
