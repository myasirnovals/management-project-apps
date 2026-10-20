<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Saran & Masukan Tim</span>
        </div>
    </x-slot>
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">Kotak Saran & Aspirasi</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>💡 Saran & Masukan Tim</span>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                    {{ $project->name }}
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Wadah aspirasi bagi seluruh anggota tim untuk mengusulkan fitur baru, perbaikan, atau saran pengembangan kepada Project Manager.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="document.getElementById('modal-new-feedback').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30 group">
                <svg class="w-4 h-4 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Beri Masukan Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Info -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-xl bg-indigo-950/60 border border-indigo-500/30 text-indigo-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400">Total Masukan</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-amber-400">Menunggu Review</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-indigo-400">Ditinjau / Direncanakan</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['planned'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-emerald-400">Telah Terealisasi</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['implemented'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('projects.feedback.index', $project) }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="filter-category" class="text-xs text-slate-400 font-medium">Kategori:</label>
                <select id="filter-category" name="category" onchange="this.form.submit()"
                    class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="all" {{ ($categoryFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="feature_request" {{ ($categoryFilter ?? '') === 'feature_request' ? 'selected' : '' }}>Permintaan Fitur</option>
                    <option value="improvement" {{ ($categoryFilter ?? '') === 'improvement' ? 'selected' : '' }}>Peningkatan Sistem</option>
                    <option value="bug_report" {{ ($categoryFilter ?? '') === 'bug_report' ? 'selected' : '' }}>Laporan Kendala</option>
                    <option value="general" {{ ($categoryFilter ?? '') === 'general' ? 'selected' : '' }}>Saran Umum</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="filter-status" class="text-xs text-slate-400 font-medium">Status:</label>
                <select id="filter-status" name="status" onchange="this.form.submit()"
                    class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="reviewed" {{ ($statusFilter ?? '') === 'reviewed' ? 'selected' : '' }}>Sedang Ditinjau</option>
                    <option value="planned" {{ ($statusFilter ?? '') === 'planned' ? 'selected' : '' }}>Direncanakan</option>
                    <option value="implemented" {{ ($statusFilter ?? '') === 'implemented' ? 'selected' : '' }}>Diimplementasikan</option>
                    <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            @if(!empty($categoryFilter) && $categoryFilter !== 'all' || !empty($statusFilter) && $statusFilter !== 'all')
                <a href="{{ route('projects.feedback.index', $project) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-400 font-medium">
            Menampilkan {{ $feedbacks->total() }} masukan
        </div>
    </div>

    <!-- Feedbacks List -->
    @if($feedbacks->isEmpty())
        <div class="py-16 text-center rounded-2xl bg-slate-900/50 border border-slate-800/80">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Belum Ada Saran atau Masukan</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-6">
                Jadilah yang pertama menyampaikan ide fitur atau masukan untuk pengembangan proyek ini kepada Project Manager.
            </p>
            <button type="button" onclick="document.getElementById('modal-new-feedback').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition">
                <span>Kirim Masukan Pertama</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($feedbacks as $fb)
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col justify-between hover:border-slate-700 transition shadow-sm space-y-4">
                    <!-- Top Author & Badges -->
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs ring-1 ring-indigo-500/30 shrink-0">
                                    {{ $fb->user?->initials() ?? '?' }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                        <span>{{ $fb->user?->name ?? 'Anggota Tim' }}</span>
                                        @if($fb->user)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">
                                                {{ $fb->user->role->label() }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $fb->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $fb->categoryBadgeClass() }}">
                                    {{ $fb->categoryLabel() }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $fb->statusBadgeClass() }}">
                                    {{ $fb->statusLabel() }}
                                </span>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <h4 class="text-sm font-bold text-white mb-1.5 leading-snug">
                            {{ $fb->title }}
                        </h4>
                        <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line bg-slate-950/40 p-3 rounded-xl border border-slate-800/80">
                            {{ $fb->description }}
                        </p>
                    </div>

                    <!-- Converted Issue Badge -->
                    @if($fb->convertedIssue)
                        <div class="p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/20 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                <span class="text-[11px] font-semibold text-purple-300 truncate">
                                    Dikonversi ke Backlog: <strong class="text-white">{{ $fb->convertedIssue->issue_key }}</strong> - {{ $fb->convertedIssue->title }}
                                </span>
                            </div>
                            <a href="{{ route('projects.backlog', $project) }}" class="text-[11px] font-bold text-purple-400 hover:text-purple-300 underline shrink-0">
                                Lihat di Backlog
                            </a>
                        </div>
                    @endif

                    <!-- PM Response (if any) -->
                    @if($fb->pm_response)
                        <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800 space-y-1.5">
                            <div class="flex items-center justify-between text-[11px]">
                                <div class="flex items-center gap-1.5 font-bold text-indigo-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <span>Tanggapan Project Manager ({{ $fb->responder?->name ?? 'PM' }})</span>
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $fb->responded_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-300 italic">
                                "{{ $fb->pm_response }}"
                            </p>
                        </div>
                    @endif

                    <!-- Action Controls -->
                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            @if($isProjectManager)
                                <!-- PM Edit Response Button -->
                                <button type="button"
                                    onclick="openResponseModal({{ $fb->id }}, '{{ addslashes($fb->title) }}', '{{ $fb->status }}', '{{ addslashes($fb->pm_response ?? '') }}')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold transition border border-slate-700 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ $fb->pm_response ? 'Ubah Tanggapan' : 'Tanggapi Masukan' }}</span>
                                </button>

                                <!-- Convert to Backlog Issue (if not converted) -->
                                @if(!$fb->converted_issue_id)
                                    <form method="POST" action="{{ route('projects.feedback.convert', [$project, $fb]) }}" onsubmit="return confirm('Konversi saran ini menjadi tiket Issue di Backlog proyek?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 text-[11px] font-semibold transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>Jadikan Issue</span>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        <!-- Delete Button -->
                        @if($isProjectManager || (Auth::id() === $fb->user_id && $fb->status === 'pending'))
                            <form method="POST" action="{{ route('projects.feedback.destroy', [$project, $fb]) }}" onsubmit="return confirm('Hapus saran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Masukan" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $feedbacks->links() }}
        </div>
    @endif
</div>

<!-- Modal: Kirim Masukan Baru -->
<div id="modal-new-feedback" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>💡 Sampaikan Saran & Masukan</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-new-feedback').classList.add('hidden')" class="p-1 text-slate-400 hover:text-white rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('projects.feedback.store', $project) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="fb-title" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Judul Saran / Usulan Fitur <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="fb-title" name="title" required
                    placeholder="Contoh: Tambahkan ekspor laporan ke format Excel atau integrasi Slack"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="fb-category" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Kategori Masukan <span class="text-rose-400">*</span>
                </label>
                <select id="fb-category" name="category" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="feature_request">Permintaan Fitur Baru (Feature Request)</option>
                    <option value="improvement">Peningkatan Sistem / Workflow (Improvement)</option>
                    <option value="bug_report">Laporan Kendala / Bug (Bug Report)</option>
                    <option value="general">Saran Umum / Diskusi Proyek (General)</option>
                </select>
            </div>

            <div>
                <label for="fb-description" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Uraian & Alasan Kebutuhan <span class="text-rose-400">*</span>
                </label>
                <textarea id="fb-description" name="description" rows="4" required
                    placeholder="Jelaskan kebutuhan Anda, manfaat bagi tim, atau situasi kendala yang dihadapi secara detail..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">
                    Saran ini akan diteruskan ke Project Manager untuk ditinjau kelayakannya masuk ke Sprint Backlog.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-new-feedback').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-md shadow-indigo-600/30">
                    Kirim Masukan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: PM Review & Response (Project Manager only) -->
@if($isProjectManager)
<div id="modal-pm-response" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>💬 Tanggapi Masukan Tim (PM Review)</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-pm-response').classList.add('hidden')" class="p-1 text-slate-400 hover:text-white rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="form-pm-response" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <span class="text-xs text-slate-400 block mb-1">Judul Masukan:</span>
                <p id="resp-feedback-title" class="text-sm font-bold text-white bg-slate-950 p-2.5 rounded-xl border border-slate-800"></p>
            </div>

            <div>
                <label for="resp-status" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Keputusan / Status Masukan <span class="text-rose-400">*</span>
                </label>
                <select id="resp-status" name="status" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="pending">Menunggu Review</option>
                    <option value="reviewed">Sedang Ditinjau (In Evaluation)</option>
                    <option value="planned">Disetujui / Direncanakan (Planned for Backlog)</option>
                    <option value="implemented">Telah Selesai (Implemented)</option>
                    <option value="rejected">Ditolak / Ditunda (Rejected / Out of Scope)</option>
                </select>
            </div>

            <div>
                <label for="resp-notes" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Catatan & Jawaban Project Manager
                </label>
                <textarea id="resp-notes" name="pm_response" rows="4"
                    placeholder="Tuliskan respon, alasan persetujuan/penolakan, atau rencana implementasi sprint kepada anggota tim..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-pm-response').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-md shadow-indigo-600/30">
                    Simpan Tanggapan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResponseModal(id, title, status, notes) {
        const form = document.getElementById('form-pm-response');
        form.action = `/projects/{{ $project->id }}/feedback/${id}`;
        document.getElementById('resp-feedback-title').textContent = title;
        document.getElementById('resp-status').value = status;
        document.getElementById('resp-notes').value = notes;
        document.getElementById('modal-pm-response').classList.remove('hidden');
    }
</script>
@endif

</x-app-layout>
