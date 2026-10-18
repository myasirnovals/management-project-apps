<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                    {{ $project->key }}
                </span>
                <span class="text-sm font-bold text-white">Komponen Proyek (Modul I.F)</span>
            </div>
            <button type="button" onclick="openCreateComponentModal()"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Buat Komponen</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Handbook explanation banner -->
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
            <strong>Apa itu Komponen?</strong> Project component adalah pengelompokan kebutuhan yang logis dalam sebuah software project (contoh: Front-end, Back-end, Dokumentasi, UI/UX). Komponen membantu menstrukturkan issue ke dalam modul spesifik dan menunjuk penanggung jawab (Component Lead).
        </div>

        <!-- Components Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Nama Komponen</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4">Component Lead</th>
                            <th class="px-6 py-4">Terkait Issue</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($components as $comp)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-100 flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold text-[11px]">
                                            🧩
                                        </div>
                                        <span>{{ $comp->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-400 max-w-md">
                                    {{ $comp->description ?: '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($comp->lead)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center">
                                                {{ $comp->lead->initials() }}
                                            </div>
                                            <span class="text-slate-200 font-medium">{{ $comp->lead->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-500 italic">Project Default</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-bold text-[11px]">
                                        {{ $comp->issues_count }} issues
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('projects.components.destroy', [$project, $comp]) }}" onsubmit="return confirm('Hapus komponen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Hapus Komponen">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500 text-xs">
                                    Belum ada komponen yang dibuat untuk proyek ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Create Component (Modul I Gambar 1.33) -->
    <div id="createComponentModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Create Component</h3>
                <button type="button" onclick="closeCreateComponentModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('projects.components.store', $project) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Komponen *</label>
                    <input type="text" name="name" required placeholder="Contoh: Authentication, Frontend, API"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Fungsi atau cakupan modul..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Component Lead</label>
                    <select name="lead_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                        <option value="">-- Project Default --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCreateComponentModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500">Simpan Komponen</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateComponentModal() { document.getElementById('createComponentModal').classList.remove('hidden'); }
        function closeCreateComponentModal() { document.getElementById('createComponentModal').classList.add('hidden'); }
    </script>
</x-app-layout>
