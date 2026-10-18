<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Kalender Proyek</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-medium">
                Google Calendar Sync
            </span>
        </div>
    </x-slot>

    <div class="space-y-6 w-full max-w-[1600px] mx-auto">
        <!-- Top Toolbar: Role Badge, Action Buttons, and Quick Controls -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left: Title & Role Information -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/25 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-white tracking-tight">&nbsp Jadwal Proyek</h2>
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Guide Modal Button -->
                    <button type="button" onclick="openModal('guideModal')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700/60 transition shadow-sm">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Panduan Hak Akses</span>
                    </button>

                    <!-- Open in Google Calendar -->
                    <a href="{{ $openCalendarUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700/60 transition shadow-sm" title="Buka langsung di aplikasi Google Calendar Anda">
                        <svg class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"></path>
                        </svg>
                        <span>Buka di Google Calendar</span>
                    </a>

                    @if($isProjectManager)
                    <!-- PM Only: Add Event in Google Calendar -->
                    <a href="{{ $createEventUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Jadwal Baru</span>
                    </a>

                    <!-- PM Only: Settings Button -->
                    <button type="button" onclick="openModal('settingsModal')" class="p-2 rounded-xl text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700/60 transition shadow-sm" title="Konfigurasi ID Google Calendar Bersama">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Workspace Projects & Sub-bar -->
        </div>

        <!-- TAB 1: GOOGLE CALENDAR EMBED VIEW (PRIMARY) -->
        <div id="googleCalendarView" class="space-y-6">
            <!-- Main Embed Frame (Full Width & Spacious Height) -->
            <div class="w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col relative min-h-[820px] lg:min-h-[880px]">
                <!-- Google Calendar Top Status Strip -->
                <div class="bg-slate-900 border-b border-slate-800 px-5 py-2.5 flex items-center justify-between text-xs text-slate-300">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-medium text-white">Google Calendar Live View</span>
                        @if(!empty($calendarSettings['calendar_id']))
                        <span class="text-slate-500 font-mono text-[11px] truncate max-w-[320px]">({{ $calendarSettings['calendar_id'] }})</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-slate-400">
                        <span>Zona Waktu: <strong class="text-slate-200">{{ $calendarSettings['timezone'] ?? 'Asia/Jakarta' }}</strong></span>
                        <a href="{{ $openCalendarUrl }}" target="_blank" class="text-indigo-400 hover:underline flex items-center gap-1 font-semibold">
                            Layar Penuh
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Iframe Container -->
                <div class="relative w-full flex-1 bg-white min-h-[800px] lg:min-h-[860px]">
                    <iframe id="gcalendarIframe" src="{{ $embedUrl }}" style="border: 0; width: 100%; height: 100%; min-height: 860px;" frameborder="0" scrolling="no" class="w-full h-full min-h-[800px] lg:min-h-[860px]" title="Google Calendar Proyek">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- TAB 2: LOCAL SPRINT & ISSUES SUMMARY (OPTIONAL OVERVIEW) -->
        <div id="localCalendarView" class="hidden space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between pb-4 border-b border-slate-800 mb-6 gap-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Daftar Proyek &amp; Iterasi Sprint</h3>
                        <p class="text-xs text-slate-400">Ringkasan proyek aktif dalam workspace AgilePM.</p>
                    </div>
                    <span class="px-3 py-1 rounded-xl bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-700">
                        Total Proyek: {{ $projects->count() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($projects as $proj)
                    <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-4 flex flex-col justify-between hover:border-slate-700 transition">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono font-bold text-xs border border-indigo-500/30">
                                    {{ $proj->key }}
                                </span>
                                <span class="text-[11px] text-slate-500">{{ $proj->sprints->count() }} Sprint</span>
                            </div>
                            <h4 class="text-sm font-bold text-white mb-1">{{ $proj->name }}</h4>
                            <p class="text-xs text-slate-400 line-clamp-2 mb-3">
                                {{ $proj->description ?: 'Tidak ada deskripsi proyek.' }}
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Anggota: {{ $proj->members->count() }}</span>
                            <a href="{{ route('projects.board', $proj) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                                Buka Board →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: SETELAN GOOGLE CALENDAR (PM ONLY) -->
    @if($isProjectManager)
    <div id="settingsModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm">
                        ⚙️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Setelan Google Calendar Bersama</h3>
                        <p class="text-xs text-slate-400">Konfigurasikan ID kalender tim proyek Anda</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('settingsModal')" class="text-slate-400 hover:text-white text-xl leading-none">×</button>
            </div>

            <form method="POST" action="{{ route('calendar.settings.update') }}" class="space-y-4">
                @csrf
                <!-- Calendar ID -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Google Calendar ID / Email Bersama *
                    </label>
                    <input type="text" name="calendar_id" value="{{ old('calendar_id', $calendarSettings['calendar_id'] ?? '') }}" placeholder="contoh: tim-proyek@group.calendar.google.com atau email@gmail.com" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">
                        Dapat dilihat di Google Calendar: <em>Setelan dan Berbagi → Integrasikan Kalender → ID Kalender</em>.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Timezone -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Zona Waktu</label>
                        <select name="timezone" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Asia/Jakarta" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>

                    <!-- View Mode -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Tampilan Awal</label>
                        <select name="view_mode" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="MONTH" {{ ($calendarSettings['view_mode'] ?? 'MONTH') === 'MONTH' ? 'selected' : '' }}>Bulanan (Month)</option>
                            <option value="WEEK" {{ ($calendarSettings['view_mode'] ?? '') === 'WEEK' ? 'selected' : '' }}>Mingguan (Week)</option>
                            <option value="AGENDA" {{ ($calendarSettings['view_mode'] ?? '') === 'AGENDA' ? 'selected' : '' }}>Agenda / List</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Embed URL (Optional) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        URL Embed Kustom (Opsional)
                    </label>
                    <input type="url" name="custom_embed_url" value="{{ old('custom_embed_url', $calendarSettings['custom_embed_url'] ?? '') }}" placeholder="https://calendar.google.com/calendar/embed?src=..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika sudah mengisi Calendar ID di atas.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeModal('settingsModal')" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 2: PANDUAN LENGKAP KONFIGURASI HAK AKSES GOOGLE CALENDAR -->
    <div id="guideModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-base shrink-0">
                        📖
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Panduan Konfigurasi Kalender Bersama</h3>
                        <p class="text-xs text-slate-400">Cara mengatur agar PM bisa membuat jadwal &amp; anggota hanya bisa melihat</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('guideModal')" class="text-slate-400 hover:text-white text-xl leading-none">×</button>
            </div>

            <div class="space-y-4 text-xs text-slate-300">
                <!-- Step 1 -->
                <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">1</span>
                        <h4 class="font-bold text-white text-sm">Buat Kalender Baru di Google Calendar</h4>
                    </div>
                    <p class="text-slate-400 leading-relaxed pl-8">
                        Buka <a href="https://calendar.google.com" target="_blank" class="text-indigo-400 underline font-semibold">Google Calendar</a> dengan akun utama/bersama. Di sidebar kiri pada bagian <strong>"Kalender lain" (Other calendars)</strong>, klik tanda <strong>+</strong> lalu pilih <strong>"Buat kalender baru" (Create new calendar)</strong>. Beri nama kalender (misal: <em>Jadwal Proyek AgilePM</em>).
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-950/70 border border-emerald-500/30 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs">2</span>
                        <h4 class="font-bold text-emerald-400 text-sm">Beri Izin Editor Kepada Project Manager (PM)</h4>
                    </div>
                    <div class="pl-8 space-y-1.5 text-slate-400 leading-relaxed">
                        <p>Di halaman setelan kalender tersebut, buka menu <strong>"Bagikan dengan orang atau grup tertentu"</strong> (Share with specific people).</p>
                        <p>Klik <strong>"Tambahkan orang"</strong>, masukkan email Project Manager / Scrum Master, lalu pilih izin:</p>
                        <div class="p-2.5 rounded-xl bg-emerald-950/30 border border-emerald-500/30 text-emerald-300 font-semibold my-1.5">
                            ✅ "Membuat perubahan pada acara" (Make changes to events)
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Dengan izin ini, Project Manager memiliki wewenang penuh untuk membuat, memindahkan, dan mengedit jadwal sprint atau milestone proyek.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-950/70 border border-sky-500/30 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-xs">3</span>
                        <h4 class="font-bold text-sky-400 text-sm">Beri Izin Read-Only Kepada Anggota Tim</h4>
                    </div>
                    <div class="pl-8 space-y-1.5 text-slate-400 leading-relaxed">
                        <p>Tambahkan email anggota tim yang terlibat (Developer, Designer, QA, Viewer) ke daftar sharing, lalu pilih izin:</p>
                        <div class="p-2.5 rounded-xl bg-sky-950/30 border border-sky-500/30 text-sky-300 font-semibold my-1.5">
                            👁️ "Lihat semua detail acara" (See all event details)
                        </div>
                        <p class="text-[11px] text-slate-400">
                            <strong>PENTING:</strong> Jangan pilih "Make changes". Dengan izin ini, anggota tim hanya bisa membaca/melihat jadwal proyek tanpa bisa mengutak-atik atau menghapus jadwal yang telah ditentukan oleh PM.
                        </p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">4</span>
                        <h4 class="font-bold text-white text-sm">Salin ID Kalender ke Aplikasi AgilePM</h4>
                    </div>
                    <div class="pl-8 space-y-1.5 text-slate-400 leading-relaxed">
                        <p>Di setelan kalender yang sama, gulir ke bawah ke bagian <strong>"Integrasikan kalender" (Integrate calendar)</strong>.</p>
                        <p>Salin <strong>ID Kalender</strong> (formatnya biasanya: <code class="text-indigo-300 bg-slate-900 px-1.5 py-0.5 rounded font-mono">c_xxxxxx@group.calendar.google.com</code> atau email akun bersama).</p>
                        <p>Di aplikasi AgilePM, klik tombol <strong>⚙️ Setelan Kalender</strong>, tempelkan ID tersebut, lalu simpan.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-800 mt-5">
                <button type="button" onclick="closeModal('guideModal')" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 transition">
                    Mengerti &amp; Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- UI Scripts -->
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        }

        function switchCalendarTab(tab) {
            const googleView = document.getElementById('googleCalendarView');
            const localView = document.getElementById('localCalendarView');
            const tabGoogleBtn = document.getElementById('tabGoogleBtn');
            const tabLocalBtn = document.getElementById('tabLocalBtn');

            if (tab === 'google') {
                if (googleView) googleView.classList.remove('hidden');
                if (localView) localView.classList.add('hidden');

                if (tabGoogleBtn) tabGoogleBtn.className = 'px-3.5 py-1.5 rounded-lg font-semibold text-xs transition bg-indigo-600 text-white shadow-sm flex items-center gap-1.5';
                if (tabLocalBtn) tabLocalBtn.className = 'px-3.5 py-1.5 rounded-lg font-medium text-xs transition text-slate-400 hover:text-white flex items-center gap-1.5';
            } else {
                if (googleView) googleView.classList.add('hidden');
                if (localView) localView.classList.remove('hidden');

                if (tabLocalBtn) tabLocalBtn.className = 'px-3.5 py-1.5 rounded-lg font-semibold text-xs transition bg-indigo-600 text-white shadow-sm flex items-center gap-1.5';
                if (tabGoogleBtn) tabGoogleBtn.className = 'px-3.5 py-1.5 rounded-lg font-medium text-xs transition text-slate-400 hover:text-white flex items-center gap-1.5';
            }
        }
    </script>
</x-app-layout>