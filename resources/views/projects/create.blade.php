<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Buat Proyek Baru</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-white">Inisiasi Proyek Manajemen (Agile / Scrum)</h2>
        </div>

        <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
            @csrf

            <!-- Step 1: Framework Template Selection (Modul I Gambar 1.23) -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                    1. Pilih Kerangka Kerja (Framework Template) *
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Scrum Option -->
                    <label id="card_scrum" onclick="selectFramework('scrum')"
                        class="framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 {{ old('type', 'scrum') === 'scrum' ? 'border-indigo-600 bg-indigo-950/30 shadow-md shadow-indigo-600/10' : 'border-slate-800 bg-slate-950/60 hover:border-slate-700' }}">
                        <input type="radio" name="type" id="radio_scrum" value="scrum" {{ old('type', 'scrum') === 'scrum' ? 'checked' : '' }} onchange="selectFramework('scrum')" class="sr-only">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-lg">
                                🔄
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="badge_scrum" class="{{ old('type', 'scrum') === 'scrum' ? '' : 'hidden' }} text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/30">Terpilih</span>
                                <div id="dot_scrum" class="w-5 h-5 rounded-full border-2 {{ old('type', 'scrum') === 'scrum' ? 'border-indigo-500 bg-indigo-500/20' : 'border-slate-700 bg-transparent' }} flex items-center justify-center transition">
                                    <div class="w-2.5 h-2.5 rounded-full {{ old('type', 'scrum') === 'scrum' ? 'bg-indigo-500' : 'bg-transparent' }} transition"></div>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-white text-sm">Scrum Project</h4>
                        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                            Bekerja dalam siklus berulang (sprint timebox 2-3 minggu), dengan backlog grooming, story points, dan pelaporan burndown.
                        </p>
                    </label>

                    <!-- Kanban Option -->
                    <label id="card_kanban" onclick="selectFramework('kanban')"
                        class="framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 {{ old('type') === 'kanban' ? 'border-emerald-500 bg-emerald-950/30 shadow-md shadow-emerald-500/10' : 'border-slate-800 bg-slate-950/60 hover:border-slate-700' }}">
                        <input type="radio" name="type" id="radio_kanban" value="kanban" {{ old('type') === 'kanban' ? 'checked' : '' }} onchange="selectFramework('kanban')" class="sr-only">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg">
                                📊
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="badge_kanban" class="{{ old('type') === 'kanban' ? '' : 'hidden' }} text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/30">Terpilih</span>
                                <div id="dot_kanban" class="w-5 h-5 rounded-full border-2 {{ old('type') === 'kanban' ? 'border-emerald-500 bg-emerald-500/20' : 'border-slate-700 bg-transparent' }} flex items-center justify-center transition">
                                    <div class="w-2.5 h-2.5 rounded-full {{ old('type') === 'kanban' ? 'bg-emerald-500' : 'bg-transparent' }} transition"></div>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-white text-sm">Kanban Project</h4>
                        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                            Visualisasi alur kerja fleksibel tanpa batasan waktu sprint, membatasi pekerjaan yang sedang berjalan (WIP Limits).
                        </p>
                    </label>
                </div>
            </div>

            <!-- Step 2: Project Details (Modul I Gambar 1.26) -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                    2. Informasi Proyek & Identitas Key
                </label>

                <!-- Project Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Proyek *</label>
                    <input type="text" id="project_name" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Sistem Pemesanan Tiket Bioskop"
                        oninput="generateKeyFromName(this.value)"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Key -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Project Key (Awalan Kode Masalah) *</label>
                        <span class="text-[11px] text-slate-500 font-mono">Contoh: PRK, WEB, TIKET</span>
                    </div>
                    <div class="relative">
                        <input type="text" id="project_key" name="key" value="{{ old('key') }}" required maxlength="10"
                            placeholder="PRK"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-indigo-400 font-mono font-bold uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Key akan menjadi prefix setiap issue, misalnya: <span id="preview_key" class="font-mono text-indigo-400 font-bold">PRK-1</span>, <span class="font-mono text-indigo-400 font-bold">PRK-2</span>.
                    </p>
                    @error('key')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi Proyek (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan tujuan, ruang lingkup, atau catatan proyek..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition">
                    Buat Proyek Sekarang &rarr;
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectFramework(type) {
            const radioScrum = document.getElementById('radio_scrum');
            const radioKanban = document.getElementById('radio_kanban');
            const cardScrum = document.getElementById('card_scrum');
            const cardKanban = document.getElementById('card_kanban');
            const dotScrum = document.getElementById('dot_scrum');
            const dotKanban = document.getElementById('dot_kanban');

            const badgeScrum = document.getElementById('badge_scrum');
            const badgeKanban = document.getElementById('badge_kanban');

            if (type === 'scrum') {
                radioScrum.checked = true;
                // Active Scrum
                cardScrum.className = 'framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 border-indigo-600 bg-indigo-950/30 shadow-md shadow-indigo-600/10';
                dotScrum.className = 'w-5 h-5 rounded-full border-2 border-indigo-500 bg-indigo-500/20 flex items-center justify-center transition';
                dotScrum.firstElementChild.className = 'w-2.5 h-2.5 rounded-full bg-indigo-500 transition';
                if (badgeScrum) badgeScrum.classList.remove('hidden');

                // Inactive Kanban
                cardKanban.className = 'framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 border-slate-800 bg-slate-950/60 hover:border-slate-700';
                dotKanban.className = 'w-5 h-5 rounded-full border-2 border-slate-700 bg-transparent flex items-center justify-center transition';
                dotKanban.firstElementChild.className = 'w-2.5 h-2.5 rounded-full bg-transparent transition';
                if (badgeKanban) badgeKanban.classList.add('hidden');
            } else {
                radioKanban.checked = true;
                // Active Kanban
                cardKanban.className = 'framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 border-emerald-500 bg-emerald-950/30 shadow-md shadow-emerald-500/10';
                dotKanban.className = 'w-5 h-5 rounded-full border-2 border-emerald-500 bg-emerald-500/20 flex items-center justify-center transition';
                dotKanban.firstElementChild.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 transition';
                if (badgeKanban) badgeKanban.classList.remove('hidden');

                // Inactive Scrum
                cardScrum.className = 'framework-card relative flex flex-col p-5 rounded-2xl cursor-pointer transition select-none border-2 border-slate-800 bg-slate-950/60 hover:border-slate-700';
                dotScrum.className = 'w-5 h-5 rounded-full border-2 border-slate-700 bg-transparent flex items-center justify-center transition';
                dotScrum.firstElementChild.className = 'w-2.5 h-2.5 rounded-full bg-transparent transition';
                if (badgeScrum) badgeScrum.classList.add('hidden');
            }
        }

        function generateKeyFromName(name) {
            if (!name) return;
            const words = name.trim().split(/\s+/);
            let key = '';
            if (words.length === 1) {
                key = words[0].substring(0, 4).toUpperCase();
            } else {
                for (let i = 0; i < Math.min(words.length, 4); i++) {
                    key += words[i].charAt(0).toUpperCase();
                }
            }
            // Filter non alpha
            key = key.replace(/[^A-Z0-9]/g, '');
            if (key) {
                document.getElementById('project_key').value = key;
                document.getElementById('preview_key').innerText = key + '-1';
            }
        }
    </script>
</x-app-layout>
