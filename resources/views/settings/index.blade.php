<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Pengaturan Aplikasi</span>
        </div>
    </x-slot>

    <div class="space-y-6 w-full max-w-5xl mx-auto">
        <form method="POST" action="{{ route('app.settings.update') }}" class="space-y-6">
            @csrf

            <!-- SECTION 1: GOOGLE CALENDAR INTEGRATION -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-200 dark:border-blue-800">
                            CAL
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Integrasi Google Calendar Bersama</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Jadwal proyek yang disinkronkan di halaman Kalender Proyek.</p>
                        </div>
                    </div>
                    <a href="{{ route('calendar.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold flex items-center gap-1">
                        <span>Lihat Kalender Live</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Google Calendar ID / Email Kalender Bersama *
                        </label>
                        <input type="text" name="calendar_id"
                            value="{{ old('calendar_id', $settings['calendar_id'] ?? '') }}"
                            placeholder="contoh: tim-proyek@group.calendar.google.com atau email@gmail.com"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                            ID Kalender dapat disalin dari Google Calendar: <em>Setelan dan Berbagi &rarr; Integrasikan Kalender &rarr; ID Kalender</em>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Zona Waktu Kalender</label>
                            <select name="calendar_timezone" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                                <option value="Asia/Jakarta" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura" {{ ($settings['calendar_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tampilan Awal</label>
                            <select name="calendar_view_mode" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                                <option value="MONTH" {{ ($settings['calendar_view_mode'] ?? 'MONTH') === 'MONTH' ? 'selected' : '' }}>Bulanan (Month View)</option>
                                <option value="WEEK" {{ ($settings['calendar_view_mode'] ?? '') === 'WEEK' ? 'selected' : '' }}>Mingguan (Week View)</option>
                                <option value="AGENDA" {{ ($settings['calendar_view_mode'] ?? '') === 'AGENDA' ? 'selected' : '' }}>Agenda / Daftar</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            URL Embed Kustom (Opsional)
                        </label>
                        <input type="url" name="custom_embed_url"
                            value="{{ old('custom_embed_url', $settings['custom_embed_url'] ?? '') }}"
                            placeholder="https://calendar.google.com/calendar/embed?src=..."
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Kosongkan jika sudah menggunakan Google Calendar ID.</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: SMTP EMAIL -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200 dark:border-amber-800">
                            MAIL
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Konfigurasi Server Email (SMTP)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Notifikasi penugasan issue dan pengingat deadline ke email anggota.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Driver Mailer</label>
                            <select name="mail_mailer" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                                <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP Server</option>
                                <option value="log" {{ ($settings['mail_mailer'] ?? '') === 'log' ? 'selected' : '' }}>Log Lokal (Testing)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Host SMTP</label>
                            <input type="text" name="mail_host"
                                value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                                placeholder="smtp.gmail.com / mail.domain.com"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Port SMTP</label>
                            <input type="number" name="mail_port"
                                value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}"
                                placeholder="587 / 465"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Username / Akun</label>
                            <input type="text" name="mail_username"
                                value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                                placeholder="akun-smtp@gmail.com"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Password / App Password</label>
                            <input type="password" name="mail_password"
                                value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                                placeholder="••••••••••••"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Enkripsi</label>
                            <select name="mail_encryption" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                                <option value="none" {{ ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Pengirim (From Address)</label>
                            <input type="email" name="mail_from_address"
                                value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                placeholder="noreply@project.local"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pengirim (From Name)</label>
                            <input type="text" name="mail_from_name"
                                value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                                placeholder="Capstone Project Studio"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: WHATSAPP GATEWAY -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200 dark:border-emerald-800">
                            WA
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Integrasi WhatsApp Gateway &amp; Otomasi Pengingat</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pengiriman pengingat tenggat waktu dan tugas overdue ke nomor WhatsApp anggota tim.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl">
                        <div>
                            <label class="block font-bold text-slate-900 dark:text-slate-100 mb-0.5">Aktifkan Pengiriman WhatsApp Gateway Otomatis</label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Bila dinonaktifkan, PM tetap dapat menggunakan fitur 1-Klik kontak WhatsApp manual.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="whatsapp_gateway_enabled" value="0">
                            <input type="checkbox" name="whatsapp_gateway_enabled" value="1"
                                {{ !empty($settings['whatsapp_gateway_enabled']) ? 'checked' : '' }}
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Provider Gateway</label>
                            <select name="whatsapp_gateway_provider" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                                <option value="fonnte" {{ ($settings['whatsapp_gateway_provider'] ?? 'fonnte') === 'fonnte' ? 'selected' : '' }}>Fonnte (fonnte.com)</option>
                                <option value="custom_webhook" {{ ($settings['whatsapp_gateway_provider'] ?? '') === 'custom_webhook' ? 'selected' : '' }}>Custom Webhook / Self-Hosted Gateway</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">API Token / Device Key</label>
                            <input type="password" name="whatsapp_api_token"
                                value="{{ old('whatsapp_api_token', $settings['whatsapp_api_token'] ?? '') }}"
                                placeholder="Masukkan token API gateway"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Webhook URL (Khusus provider Custom Webhook)</label>
                        <input type="url" name="whatsapp_webhook_url"
                            value="{{ old('whatsapp_webhook_url', $settings['whatsapp_webhook_url'] ?? '') }}"
                            placeholder="https://api.gateway-anda.com/send"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: GENERAL WORKSPACE SETTINGS -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-200 dark:border-blue-800">
                            APP
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Identitas Workspace &amp; Sistem</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pengaturan nama ruang kerja dan informasi instansi.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aplikasi *</label>
                        <input type="text" name="app_name"
                            value="{{ old('app_name', $settings['app_name'] ?? 'Capstone Project Manager') }}"
                            required
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Organisasi / Koperasi</label>
                        <input type="text" name="organization_name"
                            value="{{ old('organization_name', $settings['organization_name'] ?? 'Koperasi Tribuana IV Pusdiklatpassus Kopassus') }}"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Zona Waktu Default</label>
                        <select name="default_timezone" {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                            <option value="Asia/Jakarta" {{ ($settings['default_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" {{ ($settings['default_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" {{ ($settings['default_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: TRIGGER PENGINGAT DEADLINE (MANUAL TEST) -->
            @if($isProjectManager)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">
                        Uji Pengecekan Tenggat Waktu &amp; Kirim Pengingat
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Jalankan proses pengecekan tugas secara instan (tugas mendekati tenggat &amp; overdue). Notifikasi dikirim via Email atau WhatsApp jika aktif.
                    </p>
                </div>
                <button type="submit" formaction="{{ route('app.settings.triggerReminders') }}"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800 transition shrink-0">
                    Uji Pengingat Sekarang
                </button>
            </div>
            @endif

            <!-- Bottom Action Toolbar -->
            @if($isProjectManager)
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                    Simpan Semua Pengaturan
                </button>
            </div>
            @endif
        </form>
    </div>
</x-app-layout>
