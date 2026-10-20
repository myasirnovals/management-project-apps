<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Integrasi GitHub & Pull Request</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Status & Overview Hero Banner -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950/40 border border-slate-800 rounded-3xl p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $project->hasGithubIntegration() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ $project->hasGithubIntegration() ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }}"></span>
                        <span>{{ $project->hasGithubIntegration() ? 'Terhubung dengan GitHub' : 'Belum Terhubung' }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                        </svg>
                        <span>Integrasi GitHub Proyek (Per-Repository)</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                        Hubungkan proyek <strong>{{ $project->name }}</strong> dengan repository GitHub spesifik. Terima pembaruan Pull Request secara real-time, tautkan otomatis ke kartu tugas, dan pindahkan status pengerjaan saat kode di-review atau di-merge.
                    </p>
                </div>

                @if($project->hasGithubIntegration())
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <a href="https://github.com/{{ $project->github_repo }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                            <span>Buka di GitHub</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('projects.github.disconnect', $project) }}"
                            onsubmit="return confirm('Apakah Anda yakin ingin memutuskan integrasi GitHub untuk proyek ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition">
                                Putus Hubungan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Settings & Setup Guide -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Configuration Form -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span>⚙️</span>
                            <span>Konfigurasi Repositori & Otomatisasi</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Tentukan repositori GitHub yang diasosiasikan secara eksklusif dengan proyek ini.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('projects.github.update', $project) }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Nama Repositori GitHub *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-xs font-mono">
                                    github.com/
                                </div>
                                <input type="text" name="github_repo"
                                    value="{{ old('github_repo', $project->github_repo) }}"
                                    placeholder="username/nama-repo (contoh: myasirnovals/management-project-apps)"
                                    required
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-28 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">
                                Format: <code class="text-indigo-300">owner/nama-repo</code> atau URL lengkap repository GitHub Anda.
                            </span>
                        </div>

                        <!-- Automations toggles -->
                        <div class="space-y-3 pt-2">
                            <span class="text-xs font-semibold text-slate-300 block">Aturan Otomatisasi Status Tiket:</span>

                            <label class="flex items-start gap-3 p-3.5 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition">
                                <input type="checkbox" name="github_auto_move_pr" value="1"
                                    {{ old('github_auto_move_pr', $project->github_auto_move_pr ?? true) ? 'checked' : '' }}
                                    class="mt-0.5 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-bold text-white">Otomatis pindah ke "In Review" saat Pull Request dibuat</div>
                                    <div class="text-[11px] text-slate-400">
                                        Ketika developer membuka PR dengan menyertakan kode tiket (contoh: <code class="text-indigo-400">[{{ $project->key }}-1]</code>), tiket otomatis beralih dari <strong>To Do</strong> ke <strong>In Review</strong>.
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3.5 bg-slate-950/60 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition">
                                <input type="checkbox" name="github_auto_close_pr" value="1"
                                    {{ old('github_auto_close_pr', $project->github_auto_close_pr ?? true) ? 'checked' : '' }}
                                    class="mt-0.5 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-bold text-white">Otomatis selesaikan tiket ("Done") saat Pull Request di-merge</div>
                                    <div class="text-[11px] text-slate-400">
                                        Saat kode PR berhasil di-merge ke branch utama di GitHub, status tiket terkait langsung berubah menjadi <strong>Done</strong>.
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                                Simpan Konfigurasi GitHub
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Webhook Details & Setup Guide -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span>🔗</span>
                            <span>URL Webhook & Secret Key Proyek</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Gunakan informasi ini di pengaturan repository GitHub Anda (<span class="text-slate-300">Settings &rarr; Webhooks</span>).
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Payload URL -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Payload URL</label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="webhookUrlInput" value="{{ $webhookUrl }}"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 font-mono select-all">
                                <button type="button" onclick="copyToClipboard('webhookUrlInput', 'URL Webhook disalin!')"
                                    class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-xs font-semibold text-white transition shrink-0">
                                    Salin
                                </button>
                            </div>
                        </div>

                        <!-- Webhook Secret -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-semibold text-slate-300">Secret Token (HMAC SHA-256)</label>
                                @if($project->github_webhook_secret)
                                    <form method="POST" action="{{ route('projects.github.regenerateSecret', $project) }}" class="inline"
                                        onsubmit="return confirm('Regenerate secret akan mengubah kunci keamanan webhook. Lanjutkan?')">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-amber-400 hover:underline">
                                            Regenerate Secret
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="webhookSecretInput"
                                    value="{{ $project->github_webhook_secret ?? 'Belum dibuat (akan otomatis dibuat saat Anda menyimpan repositori di atas)' }}"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 font-mono select-all">
                                @if($project->github_webhook_secret)
                                    <button type="button" onclick="copyToClipboard('webhookSecretInput', 'Secret Webhook disalin!')"
                                        class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-xs font-semibold text-white transition shrink-0">
                                        Salin
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- 4 Step Guide -->
                        <div class="mt-6 p-4 bg-slate-950/60 border border-slate-800 rounded-2xl space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-400">Cara Pasang di GitHub (4 Langkah Singkat):</h4>
                            <ol class="text-xs text-slate-300 space-y-2 list-decimal list-inside leading-relaxed">
                                <li>Buka repository GitHub Anda &rarr; <strong>Settings</strong> &rarr; <strong>Webhooks</strong> &rarr; Klik tombol <strong>Add webhook</strong>.</li>
                                <li>Tempelkan <strong>Payload URL</strong> dan <strong>Secret</strong> di atas.</li>
                                <li>Pada pilihan <strong>Content type</strong>, pilih <code class="bg-slate-900 px-1 py-0.5 rounded text-indigo-300">application/json</code>.</li>
                                <li>Pilih opsi <em>"Let me select individual events"</em> lalu beri centang pada <strong>Pull requests</strong>. Simpan webhook!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Webhook Simulator & Quick Stats -->
            <div class="space-y-8">
                <!-- Test Simulator (No ngrok needed!) -->
                <div class="bg-gradient-to-br from-indigo-950/30 via-slate-900 to-slate-900 border border-indigo-500/20 rounded-3xl p-6 sm:p-7 space-y-5">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 uppercase tracking-wider mb-2">
                            ⚡ Instant Tester
                        </div>
                        <h3 class="text-base font-bold text-white">Simulasi Webhook PR</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Uji coba alur integrasi tanpa perlu menunggu PR riil dari GitHub atau memasang tunnel local.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('projects.github.simulate', $project) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Target Tiket Proyek</label>
                            <select name="issue_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @forelse($projectIssues as $iss)
                                    <option value="{{ $iss->id }}">{{ $iss->issue_key }} - {{ Str::limit($iss->title, 28) }} ({{ $iss->status->label() }})</option>
                                @empty
                                    <option value="">(Belum ada tiket di proyek ini)</option>
                                @endforelse
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Aksi Simulasi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="p-2.5 bg-slate-950/80 border border-slate-800 rounded-xl flex items-center gap-2 cursor-pointer hover:border-indigo-500/50 transition">
                                    <input type="radio" name="action" value="opened" checked class="text-indigo-600 bg-slate-900 border-slate-700 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-200 font-semibold">PR Dibuka</span>
                                </label>
                                <label class="p-2.5 bg-slate-950/80 border border-slate-800 rounded-xl flex items-center gap-2 cursor-pointer hover:border-indigo-500/50 transition">
                                    <input type="radio" name="action" value="merged" class="text-indigo-600 bg-slate-900 border-slate-700 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-200 font-semibold">PR Di-Merge</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Jalankan Simulasi Webhook</span>
                        </button>
                    </form>
                </div>

                <!-- Integration Stats -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ringkasan Aktivitas</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] text-slate-400">Total PR Masuk</div>
                            <div class="text-xl font-black text-white mt-0.5">{{ $pullRequests->count() }}</div>
                        </div>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] text-slate-400">PR Merged</div>
                            <div class="text-xl font-black text-purple-400 mt-0.5">{{ $pullRequests->where('is_merged', true)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pull Requests History Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>📥</span>
                        <span>Riwayat Pull Request Diterima ({{ $pullRequests->count() }})</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Daftar pull request yang dikirimkan oleh para developer dari GitHub ke proyek ini.
                    </p>
                </div>
            </div>

            @if($pullRequests->isEmpty())
                <div class="p-12 text-center bg-slate-950/40 border border-dashed border-slate-800 rounded-2xl space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-800 flex items-center justify-center text-2xl text-slate-400">
                        📦
                    </div>
                    <div class="text-sm font-bold text-slate-300">Belum Ada Pull Request</div>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Ketika developer membuka atau me-merge Pull Request di GitHub dengan mencantumkan kode tiket proyek (misal: <code>[{{ $project->key }}-1]</code>), datanya akan tampil secara otomatis di sini.
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden">
                    @foreach($pullRequests as $pr)
                        <div class="p-4 bg-slate-950/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-850 transition">
                            <div class="flex items-start sm:items-center gap-3">
                                @if($pr->user_avatar)
                                    <img src="{{ $pr->user_avatar }}" alt="{{ $pr->user_username }}"
                                        class="w-9 h-9 rounded-full border border-slate-700 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-white shrink-0">
                                        {{ substr($pr->user_username ?? 'D', 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ $pr->html_url }}" target="_blank"
                                            class="text-xs font-bold text-white hover:text-indigo-300 transition">
                                            #{{ $pr->pr_number }} {{ $pr->title }}
                                        </a>

                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $pr->statusBadgeClasses() }}">
                                            {{ $pr->statusLabel() }}
                                        </span>

                                        @if($pr->issue)
                                            <span class="font-mono text-[10px] text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">
                                                {{ $pr->issue->issue_key }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-400 mt-1 flex flex-wrap items-center gap-2">
                                        <span>Oleh <strong>@{{ $pr->user_username ?? 'developer' }}</strong></span>
                                        <span>•</span>
                                        <span class="font-mono text-[10px] text-slate-400 bg-slate-900 px-1.5 py-0.5 rounded">
                                            {{ $pr->branch_head }} &rarr; {{ $pr->branch_base }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $pr->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ $pr->html_url }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs text-indigo-400 hover:text-indigo-300 font-semibold px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 transition self-start sm:self-center shrink-0">
                                <span>Lihat di GitHub</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(elementId, message) {
            const input = document.getElementById(elementId);
            if (!input) return;
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                alert(message || 'Berhasil disalin ke clipboard!');
            }).catch(() => {
                document.execCommand('copy');
                alert(message || 'Berhasil disalin ke clipboard!');
            });
        }
    </script>
</x-app-layout>
