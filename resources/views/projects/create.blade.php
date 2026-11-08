<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Buat Proyek Baru</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Inisiasi Proyek Baru</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pilih kerangka kerja kerja dan masukkan identitas kode issue proyek.</p>
        </div>

        <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
            @csrf

            <!-- Step 1: Framework Template Selection -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    1. Kerangka Kerja Proyek *
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Scrum Option -->
                    <label id="card_scrum" onclick="selectFramework('scrum')"
                        class="framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 {{ old('type', 'scrum') === 'scrum' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700' }}">
                        <input type="radio" name="type" id="radio_scrum" value="scrum" {{ old('type', 'scrum') === 'scrum' ? 'checked' : '' }} onchange="selectFramework('scrum')" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-sm">
                                SCR
                            </span>
                            <div class="flex items-center gap-2">
                                <span id="badge_scrum" class="{{ old('type', 'scrum') === 'scrum' ? '' : 'hidden' }} text-[10px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">Terpilih</span>
                                <div id="dot_scrum" class="w-4 h-4 rounded-full border-2 {{ old('type', 'scrum') === 'scrum' ? 'border-blue-600 bg-blue-600' : 'border-slate-400 dark:border-slate-600 bg-transparent' }} flex items-center justify-center transition">
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm">Scrum Project</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Bekerja dalam iterasi sprint timebox (1 hingga 4 minggu), dengan backlog grooming, story points, dan pelaporan burndown chart.
                        </p>
                    </label>

                    <!-- Kanban Option -->
                    <label id="card_kanban" onclick="selectFramework('kanban')"
                        class="framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 {{ old('type') === 'kanban' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700' }}">
                        <input type="radio" name="type" id="radio_kanban" value="kanban" {{ old('type') === 'kanban' ? 'checked' : '' }} onchange="selectFramework('kanban')" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-sm">
                                KBN
                            </span>
                            <div class="flex items-center gap-2">
                                <span id="badge_kanban" class="{{ old('type') === 'kanban' ? '' : 'hidden' }} text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/40 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">Terpilih</span>
                                <div id="dot_kanban" class="w-4 h-4 rounded-full border-2 {{ old('type') === 'kanban' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-400 dark:border-slate-600 bg-transparent' }} flex items-center justify-center transition">
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm">Kanban Project</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Alur kerja visual berkelanjutan tanpa batasan waktu sprint, ideal untuk pengelolaan operasional dengan batas WIP (Work in Progress).
                        </p>
                    </label>
                </div>
            </div>

            <!-- Step 2: Project Details -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    2. Identitas Proyek &amp; Key
                </label>

                <!-- Project Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Proyek *</label>
                    <input type="text" id="project_name" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Sistem Informasi Toko Koperasi"
                        oninput="generateKeyFromName(this.value)"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Key -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Project Key (Prefix Kode Issue) *</label>
                        <span class="text-[11px] text-slate-500 font-mono">Contoh: TOKO, KOP, WEB</span>
                    </div>
                    <div>
                        <input type="text" id="project_key" name="key" value="{{ old('key') }}" required maxlength="10"
                            placeholder="TOKO"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-blue-700 dark:text-blue-300 font-mono font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                        Prefix ini akan disematkan di setiap kartu issue, contoh: <span id="preview_key" class="font-mono text-blue-600 dark:text-blue-400 font-bold">TOKO-1</span>, <span class="font-mono text-blue-600 dark:text-blue-400 font-bold">TOKO-2</span>.
                    </p>
                    @error('key')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Proyek (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan tujuan, batasan ruang lingkup, atau catatan proyek..."
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                    Buat Proyek
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
                cardScrum.className = 'framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 border-blue-600 bg-blue-50/50 dark:bg-blue-950/20';
                dotScrum.className = 'w-4 h-4 rounded-full border-2 border-blue-600 bg-blue-600 flex items-center justify-center transition';
                if (badgeScrum) badgeScrum.classList.remove('hidden');

                cardKanban.className = 'framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700';
                dotKanban.className = 'w-4 h-4 rounded-full border-2 border-slate-400 dark:border-slate-600 bg-transparent flex items-center justify-center transition';
                if (badgeKanban) badgeKanban.classList.add('hidden');
            } else {
                radioKanban.checked = true;
                cardKanban.className = 'framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20';
                dotKanban.className = 'w-4 h-4 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center transition';
                if (badgeKanban) badgeKanban.classList.remove('hidden');

                cardScrum.className = 'framework-card relative flex flex-col p-4 rounded-xl cursor-pointer transition select-none border-2 border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700';
                dotScrum.className = 'w-4 h-4 rounded-full border-2 border-slate-400 dark:border-slate-600 bg-transparent flex items-center justify-center transition';
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
            key = key.replace(/[^A-Z0-9]/g, '');
            if (key) {
                document.getElementById('project_key').value = key;
                document.getElementById('preview_key').innerText = key + '-1';
            }
        }
    </script>
</x-app-layout>
