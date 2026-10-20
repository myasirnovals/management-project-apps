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
                                placeholder="Capstone Project Studio"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: WHATSAPP GATEWAY & AUTOMATION -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm shrink-0">
                            💬
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Integrasi WhatsApp Gateway & Otomasi Pengingat</h3>
                            <p class="text-xs text-slate-400">Pengiriman pengingat tenggat waktu, tugas tertunda/overdue, dan pesan follow-up otomatis ke WhatsApp anggota tim.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">
                        WhatsApp Integration
                    </span>
                </div>

                <div class="space-y-4">
                    <!-- Toggle Enabled -->
                    <div class="flex items-center justify-between p-4 bg-slate-950/60 border border-slate-800 rounded-2xl">
                        <div>
                            <label class="block text-xs font-bold text-white mb-0.5">Aktifkan Pengiriman WhatsApp Gateway Otomatis</label>
                            <p class="text-[11px] text-slate-400">Jika dinonaktifkan, PM tetap dapat menggunakan fitur 1-Klik <em>"Ingatkan via WA"</em> secara instan tanpa perlu token gateway.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="whatsapp_gateway_enabled" value="0">
                            <input type="checkbox" name="whatsapp_gateway_enabled" value="1"
                                {{ !empty($settings['whatsapp_gateway_enabled']) ? 'checked' : '' }}
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Provider Gateway</label>
                            <select name="whatsapp_gateway_provider" {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                                <option value="fonnte" {{ ($settings['whatsapp_gateway_provider'] ?? 'fonnte') === 'fonnte' ? 'selected' : '' }}>Fonnte (Rekomendasi Indonesia - fonnte.com)</option>
                                <option value="custom_webhook" {{ ($settings['whatsapp_gateway_provider'] ?? '') === 'custom_webhook' ? 'selected' : '' }}>Custom Webhook / Self-Hosted Gateway</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">API Token / Device Key</label>
                            <input type="password" name="whatsapp_api_token"
                                value="{{ old('whatsapp_api_token', $settings['whatsapp_api_token'] ?? '') }}"
                                placeholder="Masukkan token API gateway Anda"
                                {{ !$isProjectManager ? 'disabled' : '' }}
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Webhook URL (Opsional, khusus provider Custom Webhook)</label>
                        <input type="url" name="whatsapp_webhook_url"
                            value="{{ old('whatsapp_webhook_url', $settings['whatsapp_webhook_url'] ?? '') }}"
                            placeholder="https://api.gateway-anda.com/send"
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: GENERAL WORKSPACE SETTINGS -->
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
                            value="{{ old('app_name', $settings['app_name'] ?? 'Capstone Project Manager') }}"
                            required
                            {{ !$isProjectManager ? 'disabled' : '' }}
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nama Organisasi / Studio</label>
                        <input type="text" name="organization_name"
                            value="{{ old('organization_name', $settings['organization_name'] ?? 'Capstone Project Studio') }}"
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

            <!-- SECTION 5: TRIGGER PENGINGAT DEADLINE (MANUAL TEST) -->
            @if($isProjectManager)
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>🚀</span>
                        <span>Uji Pengecekan Tenggat Waktu & Kirim Pengingat</span>
                    </h4>
                    <p class="text-xs text-slate-400 mt-1">
                        Jalankan proses pengecekan tugas secara instan (tugas mendekati tenggat & overdue). Notifikasi akan dikirim via Email dan WhatsApp Gateway jika aktif.
                    </p>
                </div>
                <button type="submit" formaction="{{ route('app.settings.triggerReminders') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 transition shrink-0 flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                    <span>Uji Pengingat Sekarang</span>
                </button>
            </div>
            @endif

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