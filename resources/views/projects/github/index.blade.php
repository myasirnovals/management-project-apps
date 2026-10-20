<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Integrasi GitHub & Pull Request</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Sleek Status & Action Header Banner -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-950 border border-slate-700/60 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">
                                Integrasi GitHub: {{ $project->name }}
                            </h2>
                            @if($project->hasGithubIntegration())
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Terhubung ({{ $project->github_repo }})</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                    <span>Belum Terhubung</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Sinkronisasi pull request secara otomatis dengan kartu tugas pengerjaan proyek ini.
                        </p>
                    </div>
                </div>

                @if($project->hasGithubIntegration())
                    <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                        <a href="https://github.com/{{ $project->github_repo }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                            <span>Buka Repositori</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        @if($isProjectManager ?? false)
                            <form method="POST" action="{{ route('projects.github.disconnect', $project) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin memutuskan integrasi GitHub untuk proyek ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition">
                                    Putus Hubungan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Balanced 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Left Column: Configuration & Activity Stats -->
            <div class="space-y-6">
                @if($isProjectManager ?? false)
                    <!-- Configuration Card (Project Manager Only) -->
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-5 shadow-sm">
                        <div class="border-b border-slate-800 pb-4">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>⚙️</span>
                                <span>Konfigurasi Repositori & Otomatisasi</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                Tentukan repositori GitHub yang ditautkan ke proyek <span class="text-indigo-400 font-semibold">{{ $project->key }}</span>.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('projects.github.update', $project) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                    Nama Repositori GitHub *
                                </label>
                                <!-- Clean Input Group (Solves overlapping placeholder bug) -->
                                <div class="flex rounded-xl overflow-hidden border border-slate-700 bg-slate-950 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition">
                                    <span class="inline-flex items-center px-3.5 bg-slate-900 text-slate-400 text-xs font-mono border-r border-slate-800 select-none">
                                        github.com/
                                    </span>
                                    <input type="text" name="github_repo"
                                        value="{{ old('github_repo', $project->github_repo) }}"
                                        placeholder="username/nama-repo"
                                        required
                                        class="flex-1 bg-transparent px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono">
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1.5 block">
                                    Contoh: <code class="text-indigo-300">myasirnovals/management-project-apps</code>
                                </span>
                            </div>

                            <!-- Automations toggles -->
                            <div class="space-y-2.5 pt-2">
                                <span class="text-xs font-semibold text-slate-300 block">Aturan Otomatisasi Status Tiket:</span>

                                <label class="flex items-start gap-3 p-3 bg-slate-950/70 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition">
                                    <input type="checkbox" name="github_auto_move_pr" value="1"
                                        {{ old('github_auto_move_pr', $project->github_auto_move_pr ?? true) ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-xs font-bold text-white">Otomatis pindah ke "In Review" saat PR dibuat</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Ketika developer membuka PR dengan kode tiket (contoh: <code class="text-indigo-400">[{{ $project->key }}-1]</code>), tiket otomatis beralih ke <strong>In Review</strong>.
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 bg-slate-950/70 border border-slate-800 rounded-xl cursor-pointer hover:border-slate-700 transition">
                                    <input type="checkbox" name="github_auto_close_pr" value="1"
                                        {{ old('github_auto_close_pr', $project->github_auto_close_pr ?? true) ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-xs font-bold text-white">Otomatis selesaikan tiket ("Done") saat PR di-merge</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Saat kode PR digabungkan ke branch utama di GitHub, status tiket terkait langsung beralih ke <strong>Done</strong>.
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                                    Simpan Konfigurasi
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Read-Only Information Card (For Developers / Non-PM) -->
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-4 shadow-sm">
                        <div class="border-b border-slate-800 pb-4">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>ℹ️</span>
                                <span>Informasi Repositori GitHub Proyek</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                Status koneksi repositori dan aturan otomatisasi sinkronisasi Pull Request.
                            </p>
                        </div>
                        <div class="space-y-3.5 text-xs text-slate-300">
                            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800">
                                <span class="text-slate-400 block text-[11px] font-semibold uppercase">Repositori Terhubung:</span>
                                <span class="text-white font-mono font-bold text-sm">{{ $project->github_repo ?: 'Belum dihubungkan ke GitHub' }}</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2">
                                <span class="text-slate-400 block text-[11px] font-semibold uppercase">Panduan Penamaan Branch & PR:</span>
                                <p class="text-slate-300">Sertakan Issue Key pada branch atau judul PR Anda:</p>
                                <code class="block p-2 rounded bg-slate-900 text-indigo-400 font-mono text-[11px]">feature/{{ strtolower($project->key) }}-1-implement-task</code>
                                <p class="text-slate-400 text-[11px]">Ketika PR dibuka, tiket otomatis berpindah ke <strong class="text-indigo-300">In Review</strong>. Saat dimerge, otomatis berpindah ke <strong class="text-emerald-300">Done</strong>.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-indigo-950/30 border border-indigo-500/20 text-indigo-300 text-[11px] flex items-center gap-2.5">
                                <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Konfigurasi repositori dan webhook dikelola secara eksklusif oleh Project Manager.</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Activity Summary Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <span>📊</span>
                            <span>Ringkasan Aktivitas Pull Request</span>
                        </h3>
                        <span class="text-[11px] text-slate-400">Total: {{ $pullRequests->count() }} PR</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3.5 bg-slate-950/70 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] text-slate-400 font-medium">Total PR Masuk</div>
                            <div class="text-xl font-black text-white mt-1">{{ $pullRequests->count() }}</div>
                        </div>

                        <div class="p-3.5 bg-slate-950/70 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] text-emerald-400 font-medium">PR Open</div>
                            <div class="text-xl font-black text-emerald-400 mt-1">{{ $pullRequests->where('state', 'open')->where('is_merged', false)->count() }}</div>
                        </div>

                        <div class="p-3.5 bg-slate-950/70 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] text-purple-400 font-medium">PR Merged</div>
                            <div class="text-xl font-black text-purple-400 mt-1">{{ $pullRequests->where('is_merged', true)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Webhook Setup & Guide -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-5 shadow-sm">
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>🔗</span>
                        <span>URL Webhook & Secret Key</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Salin URL dan Secret berikut ke pengaturan repository GitHub Anda (<span class="text-slate-300">Settings &rarr; Webhooks</span>).
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Payload URL -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Payload URL</label>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly id="webhookUrlInput" value="{{ $webhookUrl }}"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono select-all">
                            <button type="button" onclick="copyToClipboard('webhookUrlInput', 'URL Webhook berhasil disalin!')"
                                class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-xs font-semibold text-white transition shrink-0 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>

                    @if($isProjectManager ?? false)
                        <!-- Webhook Secret (PM Only) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-semibold text-slate-300">Secret Token (HMAC SHA-256)</label>
                                @if($project->github_webhook_secret)
                                    <form method="POST" action="{{ route('projects.github.regenerateSecret', $project) }}" class="inline"
                                        onsubmit="return confirm('Regenerate secret akan mengubah kunci keamanan webhook. Lanjutkan?')">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-amber-400 hover:underline">
                                            Regenerate
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="webhookSecretInput"
                                    value="{{ $project->github_webhook_secret ?? 'Belum dibuat (akan otomatis dibuat saat Anda menyimpan repositori)' }}"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono select-all">
                                @if($project->github_webhook_secret)
                                    <button type="button" onclick="copyToClipboard('webhookSecretInput', 'Secret Webhook berhasil disalin!')"
                                        class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-xs font-semibold text-white transition shrink-0 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span>Salin</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- 4 Step Guide with Clean UI -->
                    <div class="pt-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-2.5">
                            Panduan Pasang di GitHub (4 Langkah):
                        </h4>
                        <div class="space-y-2">
                            <div class="p-3 bg-slate-950/70 border border-slate-800/80 rounded-xl flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                                <div class="text-xs text-slate-300">
                                    Buka repo GitHub &rarr; <strong>Settings</strong> &rarr; <strong>Webhooks</strong> &rarr; klik <strong>Add webhook</strong>.
                                </div>
                            </div>

                            <div class="p-3 bg-slate-950/70 border border-slate-800/80 rounded-xl flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                                <div class="text-xs text-slate-300">
                                    Tempelkan <strong>Payload URL</strong> dan <strong>Secret</strong> di atas.
                                </div>
                            </div>

                            <div class="p-3 bg-slate-950/70 border border-slate-800/80 rounded-xl flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                                <div class="text-xs text-slate-300">
                                    Pilih Content type: <code class="px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-indigo-300">application/json</code>.
                                </div>
                            </div>

                            <div class="p-3 bg-slate-950/70 border border-slate-800/80 rounded-xl flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">4</span>
                                <div class="text-xs text-slate-300">
                                    Pilih <em>"Let me select individual events"</em> &rarr; centang <strong>Pull requests</strong> &rarr; klik <strong>Add webhook</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full-Width Pull Requests History Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>📥</span>
                        <span>Riwayat Pull Request Diterima ({{ $pullRequests->count() }})</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Daftar pull request yang dikirimkan oleh para developer dari GitHub ke proyek ini.
                    </p>
                </div>
            </div>

            @if($pullRequests->isEmpty())
                <div class="p-10 text-center bg-slate-950/40 border border-dashed border-slate-800 rounded-2xl space-y-2">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-slate-800 flex items-center justify-center text-lg text-slate-400">
                        📦
                    </div>
                    <div class="text-xs font-bold text-slate-300">Belum Ada Pull Request</div>
                    <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                        Ketika developer membuka atau me-merge Pull Request di GitHub dengan kode tiket proyek (misal: <code>[{{ $project->key }}-1]</code>), datanya akan tampil di sini.
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden">
                    @foreach($pullRequests as $pr)
                        <div class="p-4 bg-slate-950/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-850 transition">
                            <div class="flex items-start sm:items-center gap-3 min-w-0">
                                @if($pr->user_avatar)
                                    <img src="{{ $pr->user_avatar }}" alt="{{ $pr->user_username }}"
                                        class="w-8 h-8 rounded-full border border-slate-700 shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-white shrink-0">
                                        {{ substr($pr->user_username ?? 'D', 0, 1) }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ $pr->html_url }}" target="_blank"
                                            class="text-xs font-bold text-white hover:text-indigo-300 transition truncate max-w-md">
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
                                        <span>Oleh <strong>{{ '@' . ($pr->user_username ?? 'developer') }}</strong></span>
                                        <span>•</span>
                                        <span class="font-mono text-[10px] text-slate-400 bg-slate-900 px-1.5 py-0.5 rounded border border-slate-800">
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
