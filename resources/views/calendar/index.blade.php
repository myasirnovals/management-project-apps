<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Kalender Proyek</span>
        </div>
    </x-slot>

    <div class="space-y-6 w-full max-w-[1600px] mx-auto">
        <!-- Top Toolbar -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                        CAL
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Jadwal Proyek</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Sinkronisasi aktivitas sprint dan milestone ke Google Calendar.</p>
                    </div>
                </div>

                <!-- Right: Action Buttons & Tab Switcher -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Tab Switcher -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-950 rounded-xl p-1 border border-slate-200 dark:border-slate-800 text-xs">
                        <button type="button" id="tabGoogleBtn" onclick="switchCalendarTab('google')"
                            class="px-3 py-1 rounded-lg font-semibold transition bg-blue-600 text-white shadow-sm">
                            Google Calendar
                        </button>
                        <button type="button" id="tabLocalBtn" onclick="switchCalendarTab('local')"
                            class="px-3 py-1 rounded-lg font-medium transition text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                            Daftar Proyek &amp; Sprint
                        </button>
                    </div>

                    <!-- Guide Modal Button -->
                    <button type="button" onclick="openModalDialog('guideModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition">
                        <span>Panduan Hak Akses</span>
                    </button>

                    <!-- Open in Google Calendar -->
                    <a href="{{ $openCalendarUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition" title="Buka langsung di Google Calendar">
                        <span>Buka di Google Calendar</span>
                    </a>

                    @if($isProjectManager)
                        <!-- PM Only: Add Event in Google Calendar -->
                        <a href="{{ $createEventUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Buat Jadwal Baru</span>
                        </a>

                        <!-- PM Only: Settings Button -->
                        <button type="button" onclick="openModalDialog('settingsModal')" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition" title="Konfigurasi ID Google Calendar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 1: GOOGLE CALENDAR EMBED VIEW -->
        <div id="googleCalendarView" class="space-y-6">
            <div class="w-full bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col relative min-h-[720px]">
                <!-- Status Strip -->
                <div class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 px-4 py-2.5 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">Google Calendar Live View</span>
                        @if(!empty($calendarSettings['calendar_id']))
                            <span class="text-slate-500 font-mono text-[11px] truncate max-w-[320px]">({{ $calendarSettings['calendar_id'] }})</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        <span>Zona Waktu: <strong class="text-slate-700 dark:text-slate-200">{{ $calendarSettings['timezone'] ?? 'Asia/Jakarta' }}</strong></span>
                        <a href="{{ $openCalendarUrl }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 font-semibold">
                            Layar Penuh
                        </a>
                    </div>
                </div>

                <!-- Iframe Container -->
                <div class="relative w-full flex-1 bg-white min-h-[700px]">
                    <iframe id="gcalendarIframe" src="{{ $embedUrl }}" style="border: 0; width: 100%; height: 100%; min-height: 700px;" frameborder="0" scrolling="no" class="w-full h-full min-h-[700px]" title="Google Calendar Proyek">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- TAB 2: LOCAL SPRINT & ISSUES SUMMARY -->
        <div id="localCalendarView" class="hidden space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 mb-5 gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Daftar Proyek &amp; Iterasi Sprint</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ringkasan proyek aktif dalam workspace.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        Total Proyek: {{ $projects->count() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($projects as $proj)
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-mono font-bold text-xs border border-blue-200 dark:border-blue-800">
                                    {{ $proj->key }}
                                </span>
                                <span class="text-[11px] text-slate-500">{{ $proj->sprints->count() }} Sprint</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">{{ $proj->name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-3">
                                {{ $proj->description ?: 'Tidak ada deskripsi proyek.' }}
                            </p>
                        </div>
                        <div class="pt-2.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Anggota: {{ $proj->members->count() }}</span>
                            <a href="{{ route('projects.board', $proj) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                                Buka Board &rarr;
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Native Dialog: Setelan Google Calendar (PM Only) -->
    @if($isProjectManager)
    <dialog id="settingsModal" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Setelan Google Calendar Bersama</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Konfigurasikan ID kalender tim proyek Anda.</p>
            </div>
            <button type="button" onclick="closeModalDialog('settingsModal')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('calendar.settings.update') }}" class="p-5 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Google Calendar ID / Email Bersama *
                </label>
                <input type="text" name="calendar_id" value="{{ old('calendar_id', $calendarSettings['calendar_id'] ?? '') }}" placeholder="contoh: tim-proyek@group.calendar.google.com" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                    Dilihat di Google Calendar: Setelan dan Berbagi &rarr; Integrasikan Kalender &rarr; ID Kalender.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Zona Waktu</label>
                    <select name="timezone" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Asia/Jakarta" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura" {{ ($calendarSettings['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tampilan Awal</label>
                    <select name="view_mode" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="MONTH" {{ ($calendarSettings['view_mode'] ?? 'MONTH') === 'MONTH' ? 'selected' : '' }}>Bulanan (Month)</option>
                        <option value="WEEK" {{ ($calendarSettings['view_mode'] ?? '') === 'WEEK' ? 'selected' : '' }}>Mingguan (Week)</option>
                        <option value="AGENDA" {{ ($calendarSettings['view_mode'] ?? '') === 'AGENDA' ? 'selected' : '' }}>Agenda / List</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    URL Embed Kustom (Opsional)
                </label>
                <input type="url" name="custom_embed_url" value="{{ old('custom_embed_url', $calendarSettings['custom_embed_url'] ?? '') }}" placeholder="https://calendar.google.com/calendar/embed?src=..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModalDialog('settingsModal')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </dialog>
    @endif

    <!-- Native Dialog: Panduan Hak Akses -->
    <dialog id="guideModal" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Panduan Konfigurasi Kalender Bersama</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Aturan hak akses kalender bagi PM dan anggota tim.</p>
            </div>
            <button type="button" onclick="closeModalDialog('guideModal')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <div class="p-5 space-y-3 text-xs text-slate-600 dark:text-slate-300">
            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl">
                <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-1">1. Buat Kalender Baru di Google Calendar</h4>
                <p class="text-slate-500 dark:text-slate-400">Buat kalender khusus tim di Google Calendar, lalu tentukan zona waktu yang sesuai.</p>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl">
                <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-1">2. Beri Izin Editor kepada Project Manager</h4>
                <p class="text-slate-500 dark:text-slate-400">Tambahkan akun PM dengan izin "Make changes to events" agar dapat mengelola jadwal.</p>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl">
                <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-1">3. Beri Izin Read-Only kepada Anggota Tim</h4>
                <p class="text-slate-500 dark:text-slate-400">Tambahkan anggota tim dengan izin "See all event details" agar jadwal tetap aman.</p>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl">
                <h4 class="font-bold text-slate-900 dark:text-slate-100 mb-1">4. Salin ID Kalender ke Aplikasi</h4>
                <p class="text-slate-500 dark:text-slate-400">Salin Calendar ID pada menu Integrasikan Kalender dan simpan di halaman ini.</p>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="button" onclick="closeModalDialog('guideModal')" class="px-4 py-1.5 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 transition">
                    Tutup Panduan
                </button>
            </div>
        </div>
    </dialog>

    <script>
        function switchCalendarTab(tab) {
            const googleView = document.getElementById('googleCalendarView');
            const localView = document.getElementById('localCalendarView');
            const tabGoogleBtn = document.getElementById('tabGoogleBtn');
            const tabLocalBtn = document.getElementById('tabLocalBtn');

            if (tab === 'google') {
                if (googleView) googleView.classList.remove('hidden');
                if (localView) localView.classList.add('hidden');
                if (tabGoogleBtn) {
                    tabGoogleBtn.className = 'px-3 py-1 rounded-lg font-semibold transition bg-blue-600 text-white shadow-sm';
                }
                if (tabLocalBtn) {
                    tabLocalBtn.className = 'px-3 py-1 rounded-lg font-medium transition text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                }
            } else {
                if (googleView) googleView.classList.add('hidden');
                if (localView) localView.classList.remove('hidden');
                if (tabLocalBtn) {
                    tabLocalBtn.className = 'px-3 py-1 rounded-lg font-semibold transition bg-blue-600 text-white shadow-sm';
                }
                if (tabGoogleBtn) {
                    tabGoogleBtn.className = 'px-3 py-1 rounded-lg font-medium transition text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                }
            }
        }
    </script>
</x-app-layout>
