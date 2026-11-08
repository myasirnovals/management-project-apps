<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-blue-700 dark:text-blue-300 font-bold bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Integrasi GitHub</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Status & Action Header Banner -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-slate-800 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                Integrasi GitHub: {{ $project->name }}
                            </h2>
                            @if($project->hasGithubIntegration())
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Terhubung ({{ $project->github_repo }})</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Belum Terhubung</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Sinkronisasi pull request secara otomatis dengan kartu tugas pengerjaan proyek.
                        </p>
                    </div>
                </div>

                @if($project->hasGithubIntegration())
                    <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                        <a href="https://github.com/{{ $project->github_repo }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition">
                            <span>Buka Repositori</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        @if($isProjectManager)
                            <form method="POST" action="{{ route('projects.github.disconnect', $project) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin memutuskan integrasi GitHub untuk proyek ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 transition">
                                    Putus Hubungan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Left Column: Configuration or Member Instructions -->
            <div class="space-y-6">
                @if($isProjectManager)
                    <!-- Configuration Card (PM Only) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                Konfigurasi Repositori &amp; Otomatisasi
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Tautkan repositori GitHub ke proyek {{ $project->key }}.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('projects.github.update', $project) }}" class="space-y-4 text-xs">
                            @csrf
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Repositori GitHub *
                                </label>
                                <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 focus-within:ring-2 focus-within:ring-blue-500 transition">
                                    <span class="inline-flex items-center px-3 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono text-xs border-r border-slate-300 dark:border-slate-700 select-none">
                                        github.com/
                                    </span>
                                    <input type="text" name="github_repo"
                                        value="{{ old('github_repo', $project->github_repo) }}"
                                        placeholder="organisasi/nama-repo"
                                        required
                                        class="flex-1 bg-transparent px-3 py-2 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none font-mono">
                                </div>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
                                    Contoh: <code class="text-blue-600 dark:text-blue-400">tribuana/manajemen-toko</code>
                                </span>
                            </div>

                            <!-- Automations toggles -->
                            <div class="space-y-2 pt-2">
                                <span class="font-semibold text-slate-700 dark:text-slate-300 block">Aturan Otomatisasi Status Issue:</span>

                                <label class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer">
                                    <input type="checkbox" name="github_auto_move_pr" value="1"
                                        {{ old('github_auto_move_pr', $project->github_auto_move_pr ?? true) ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-slate-100">Pindah ke "In Review" saat PR dibuka</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Ketika developer membuka PR dengan kode tiket (contoh: [{{ $project->key }}-1]), issue otomatis beralih ke In Review.
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer">
                                    <input type="checkbox" name="github_auto_close_pr" value="1"
                                        {{ old('github_auto_close_pr', $project->github_auto_close_pr ?? true) ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-slate-100">Pindah ke "Done" saat PR dimerge</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Saat PR digabungkan ke branch utama di GitHub, status issue terkait langsung beralih ke Done.
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition">
                                    Simpan Konfigurasi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Webhook PR Simulator Card (PM Only) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                Simulator Webhook GitHub (Pengujian Event)
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Uji alur otomatisasi perubahan status tanpa perlu memicu webhook nyata dari GitHub.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('projects.github.simulate', $project) }}" class="space-y-4 text-xs">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Aksi Pull Request *</label>
                                    <select name="action" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="opened">PR Opened (Pindah ke In Review)</option>
                                        <option value="merged">PR Merged (Pindah ke Done)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor PR (Opsional)</label>
                                    <input type="number" name="pr_number" placeholder="Contoh: 42"
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Issue yang Ditargetkan</label>
                                <select name="issue_id" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih otomatis dari issue aktif</option>
                                    @foreach($projectIssues as $iss)
                                        <option value="{{ $iss->id }}">{{ $iss->issue_key }} - {{ $iss->title }} ({{ $iss->status->label() }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end pt-1">
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 transition">
                                    Simulasikan Webhook Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Read-Only Information Card (Developer / Non-PM) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                Informasi Repositori GitHub Proyek
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Status koneksi repositori dan panduan penamaan branch.
                            </p>
                        </div>
                        <div class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase">Repositori Terhubung:</span>
                                <span class="text-slate-900 dark:text-slate-100 font-mono font-bold text-sm">{{ $project->github_repo ?: 'Belum dihubungkan ke GitHub' }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 space-y-1.5">
                                <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase">Format Nama Branch &amp; PR:</span>
                                <p class="text-slate-700 dark:text-slate-300">Sertakan Issue Key pada branch atau judul PR Anda:</p>
                                <code class="block p-2 rounded bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-mono text-[11px]">feature/{{ strtolower($project->key) }}-1-implement-task</code>
                                <p class="text-slate-500 dark:text-slate-400 text-[11px]">Ketika PR dibuka, issue otomatis beralih ke <strong>In Review</strong>. Saat dimerge, otomatis beralih ke <strong>Done</strong>.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Webhook Setup & Guide -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
                <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                        URL Webhook &amp; Secret Key
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Salin URL dan Secret berikut ke pengaturan repositori GitHub Anda.
                    </p>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Payload URL -->
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Payload URL</label>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly id="webhookUrlInput" value="{{ $webhookUrl }}"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-200 font-mono select-all">
                            <button type="button" onclick="copyToClipboard('webhookUrlInput', 'URL Webhook berhasil disalin')"
                                class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 transition shrink-0">
                                Salin
                            </button>
                        </div>
                    </div>

                    @if($isProjectManager)
                        <!-- Webhook Secret (PM Only) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-semibold text-slate-700 dark:text-slate-300">Secret Token (HMAC SHA-256)</label>
                                @if($project->github_webhook_secret)
                                    <form method="POST" action="{{ route('projects.github.regenerateSecret', $project) }}" class="inline"
                                        onsubmit="return confirm('Regenerate secret akan mengubah kunci keamanan webhook. Lanjutkan?')">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-amber-600 dark:text-amber-400 hover:underline">
                                            Regenerate
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="webhookSecretInput"
                                    value="{{ $project->github_webhook_secret ?? 'Belum dibuat' }}"
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-200 font-mono select-all">
                                @if($project->github_webhook_secret)
                                    <button type="button" onclick="copyToClipboard('webhookSecretInput', 'Secret Webhook berhasil disalin')"
                                        class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 transition shrink-0">
                                        Salin
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Step Guide -->
                    <div class="pt-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Langkah Pemasangan di GitHub:
                        </h4>
                        <ol class="space-y-2 list-decimal list-inside text-slate-600 dark:text-slate-400">
                            <li>Buka repo GitHub &rarr; Settings &rarr; Webhooks &rarr; Add webhook.</li>
                            <li>Tempelkan Payload URL dan Secret Token di atas.</li>
                            <li>Pilih Content type: <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400">application/json</code>.</li>
                            <li>Pilih Let me select individual events &rarr; centang Pull requests &rarr; klik Add webhook.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pull Requests History Table -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                    Riwayat Pull Request Diterima ({{ $pullRequests->count() }})
                </h3>
            </div>

            @if($pullRequests->isEmpty())
                <div class="p-8 text-center bg-slate-50 dark:bg-slate-950/40 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl space-y-1">
                    <div class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Pull Request</div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Ketika developer membuka atau me-merge Pull Request di GitHub dengan format [{{ $project->key }}-1], riwayatnya akan tercatat di sini.
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-200 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                    @foreach($pullRequests as $pr)
                        <div class="p-3.5 bg-white dark:bg-slate-950/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ $pr->html_url }}" target="_blank"
                                        class="text-xs font-bold text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition truncate max-w-md">
                                        #{{ $pr->pr_number }} {{ $pr->title }}
                                    </a>

                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $pr->statusBadgeClasses() }}">
                                        {{ $pr->statusLabel() }}
                                    </span>

                                    @if($pr->issue)
                                        <span class="font-mono text-[10px] text-blue-700 dark:text-blue-300 font-bold bg-blue-100 dark:bg-blue-900/40 px-1.5 py-0.2 rounded border border-blue-200 dark:border-blue-800">
                                            {{ $pr->issue->issue_key }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-2">
                                    <span>Oleh <strong>{{ '@' . ($pr->user_username ?? 'developer') }}</strong></span>
                                    <span>•</span>
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                        {{ $pr->branch_head }} &rarr; {{ $pr->branch_base }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $pr->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <a href="{{ $pr->html_url }}" target="_blank"
                                class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold shrink-0">
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
                alert(message || 'Berhasil disalin');
            }).catch(() => {
                document.execCommand('copy');
                alert(message || 'Berhasil disalin');
            });
        }
    </script>
</x-app-layout>
