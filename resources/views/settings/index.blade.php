<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Pengaturan Aplikasi</span>
        </div>
    </x-slot>

    <div class="space-y-6 w-full max-w-5xl mx-auto">
        <!-- Top Info Header Card -->

        <form method="POST" action="{{ route('app.settings.update') }}" class="space-y-6">
            @csrf

            <!-- SECTION 1: GOOGLE CALENDAR INTEGRATION -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center font-bold text-sm shrink-0">
                            📅
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Integrasi Google Calendar Bersama</h3>
                            <p class="text-xs text-slate-400">Jadwal proyek yang ditampilkan di halaman Kalender Proyek.</p>
                        </div>
                    </div>
                    <a href="{{ route('calendar.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                        <span>Lihat Kalender Live</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Google Calendar ID / Email Kalender Bersama *
                        </label>
                        <input type="text" name="calendar_id"
                            value="{{ old('calendar_id', $settings['calendar_id'] ?? '') }}"
                            placeholder="contoh: tim-proyek@group.calendar.google.com atau email@gmail.com"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <p class="text-[11px] text-slate-400 mt-1">
                            ID Kalender dapat disalin dari Google Calendar: <em>Setelan dan Berbagi &rarr; Integrasikan Kalender &rarr; ID Kalender</em>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Zona Waktu Kalender</label>
                            <select name="calendar_timezone" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                                <option value="Asia/Jakarta" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Tampilan Awal</label>
                            <select name="calendar_view_mode" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                                <option value="MONTH" {{ ($settings['calendar_view_mode'] ?? 'MONTH') === 'MONTH' ? 'selected' : '' }}>Bulanan (Month View)</option>
                                <option value="WEEK" {{ ($settings['calendar_view_mode'] ?? '') === 'WEEK' ? 'selected' : '' }}>Mingguan (Week View)</option>
                                <option value="AGENDA" {{ ($settings['calendar_view_mode'] ?? '') === 'AGENDA' ? 'selected' : '' }}>Agenda / Daftar</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            URL Embed Kustom (Opsional)
                        </label>
                        <input type="url" name="custom_embed_url"
                            value="{{ old('custom_embed_url', $settings['custom_embed_url'] ?? '') }}"
                            placeholder="https://calendar.google.com/calendar/embed?src=..."
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika sudah menggunakan Google Calendar ID.</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: SMTP EMAIL & NOTIFICATIONS -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                            ✉️
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Konfigurasi Server Email (SMTP)</h3>
                            <p class="text-xs text-slate-400">Pengiriman notifikasi tugas baru, penugasan issue, dan pengingat deadline ke email anggota.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                        Mail Service
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Driver Mailer</label>
                            <select name="mail_mailer" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                                <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP Server</option>
                                <option value="log" {{ ($settings['mail_mailer'] ?? '') === 'log' ? 'selected' : '' }}>Log Lokal (Testing)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Host SMTP</label>
                            <input type="text" name="mail_host"
                                value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                                placeholder="smtp.gmail.com / mail.domain.com"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Port SMTP</label>
                            <input type="number" name="mail_port"
                                value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}"
                                placeholder="587 / 465"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Username / Email Akun</label>
                            <input type="text" name="mail_username"
                                value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                                placeholder="akun-smtp@gmail.com"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Password / App Password</label>
                            <input type="password" name="mail_password"
                                value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                                placeholder="••••••••••••"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Enkripsi</label>
                            <select name="mail_encryption" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                                <option value="none" {{ ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email Pengirim (From Address)</label>
                            <input type="email" name="mail_from_address"
                                value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                placeholder="noreply@project.local"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Pengirim (From Name)</label>
                            <input type="text" name="mail_from_name"
                                value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                                placeholder="AgilePM Studio"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: GENERAL WORKSPACE SETTINGS -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-sm shrink-0">
                            🏢
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Identitas Workspace & Sistem</h3>
                            <p class="text-xs text-slate-400">Pengaturan nama ruang kerja dan informasi studio aplikasi.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Aplikasi *</label>
                        <input type="text" name="app_name"
                            value="{{ old('app_name', $settings['app_name'] ?? 'AgilePM') }}"
                            required
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Organisasi / Studio</label>
                        <input type="text" name="organization_name"
                            value="{{ old('organization_name', $settings['organization_name'] ?? 'Pro Project Studio') }}"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Zona Waktu Default</label>
                        <select name="default_timezone" {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                            <option value="Asia/Jakarta" {{ ($settings['default_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" {{ ($settings['default_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" {{ ($settings['default_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Toolbar -->
            @if($isProjectManager)
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-3 rounded-2xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 hover:scale-[1.01] transition">
                        Simpan Semua Pengaturan
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
