<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-blue-700 dark:text-blue-300 font-bold bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Kotak Saran &amp; Masukan Tim</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <button type="button" onclick="openModalDialog('modalNewFeedback')"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Beri Masukan Baru</span>
        </button>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Metric Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Masukan</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $stats['total'] }}</p>
                </div>
                <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-xs">ALL</span>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Menunggu Review</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $stats['pending'] }}</p>
                </div>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">REV</span>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Direncanakan</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $stats['planned'] }}</p>
                </div>
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">PLN</span>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Terealisasi</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $stats['implemented'] }}</p>
                </div>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">DONE</span>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 shadow-sm">
            <form method="GET" action="{{ route('projects.feedback.index', $project) }}" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="filter-category" class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kategori:</label>
                    <select id="filter-category" name="category" onchange="this.form.submit()"
                        class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ ($categoryFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="feature_request" {{ ($categoryFilter ?? '') === 'feature_request' ? 'selected' : '' }}>Permintaan Fitur</option>
                        <option value="improvement" {{ ($categoryFilter ?? '') === 'improvement' ? 'selected' : '' }}>Peningkatan Sistem</option>
                        <option value="bug_report" {{ ($categoryFilter ?? '') === 'bug_report' ? 'selected' : '' }}>Laporan Kendala</option>
                        <option value="general" {{ ($categoryFilter ?? '') === 'general' ? 'selected' : '' }}>Saran Umum</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label for="filter-status" class="text-xs text-slate-500 dark:text-slate-400 font-medium">Status:</label>
                    <select id="filter-status" name="status" onchange="this.form.submit()"
                        class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="reviewed" {{ ($statusFilter ?? '') === 'reviewed' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="planned" {{ ($statusFilter ?? '') === 'planned' ? 'selected' : '' }}>Direncanakan</option>
                        <option value="implemented" {{ ($statusFilter ?? '') === 'implemented' ? 'selected' : '' }}>Diimplementasikan</option>
                        <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                @if((!empty($categoryFilter) && $categoryFilter !== 'all') || (!empty($statusFilter) && $statusFilter !== 'all'))
                    <a href="{{ route('projects.feedback.index', $project) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        Reset Filter
                    </a>
                @endif
            </form>

            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Total: {{ $feedbacks->total() }} masukan
            </div>
        </div>

        <!-- Feedbacks List -->
        @if($feedbacks->isEmpty())
            <div class="text-center rounded-2xl bg-white dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-800 p-12">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">Belum Ada Saran atau Masukan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
                    Sampaikan ide fitur baru atau usulan perbaikan kepada Project Manager melalui tombol di bawah.
                </p>
                <button type="button" onclick="openModalDialog('modalNewFeedback')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    <span>Kirim Masukan Pertama</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($feedbacks as $fb)
                    <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm space-y-3">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center text-xs font-bold border border-slate-200 dark:border-slate-700 shrink-0">
                                        {{ $fb->user?->initials() ?? '?' }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                            <span>{{ $fb->user?->name ?? 'Anggota Tim' }}</span>
                                            @if($fb->user)
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
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
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $fb->categoryBadgeClass() }}">
                                        {{ $fb->categoryLabel() }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $fb->statusBadgeClass() }}">
                                        {{ $fb->statusLabel() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Title & Description -->
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mb-1">
                                {{ $fb->title }}
                            </h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-slate-50 dark:bg-slate-950/40 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                {{ $fb->description }}
                            </p>
                        </div>

                        <!-- Converted Issue Badge -->
                        @if($fb->convertedIssue)
                            <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 flex items-center justify-between text-xs">
                                <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-300 truncate">
                                    Dikonversi ke Backlog: <strong>{{ $fb->convertedIssue->issue_key }}</strong>
                                </span>
                                <a href="{{ route('projects.backlog', $project) }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline shrink-0">
                                    Buka Backlog
                                </a>
                            </div>
                        @endif

                        <!-- PM Response -->
                        @if($fb->pm_response)
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">Respon PM ({{ $fb->responder?->name ?? 'PM' }})</span>
                                    <span class="text-[10px] text-slate-400">{{ $fb->responded_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 italic">
                                    "{{ $fb->pm_response }}"
                                </p>
                            </div>
                        @endif

                        <!-- Action Controls -->
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                @if($isProjectManager)
                                    <!-- PM Edit Response Button -->
                                    <button type="button"
                                        data-id="{{ $fb->id }}"
                                        data-title="{{ $fb->title }}"
                                        data-status="{{ $fb->status }}"
                                        data-notes="{{ $fb->pm_response ?? '' }}"
                                        onclick="openResponseModal(this)"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-[11px] font-semibold transition border border-slate-200 dark:border-slate-700">
                                        {{ $fb->pm_response ? 'Ubah Tanggapan' : 'Tanggapi Masukan' }}
                                    </button>

                                    <!-- Convert to Backlog Issue -->
                                    @if(!$fb->converted_issue_id)
                                        <form method="POST" action="{{ route('projects.feedback.convert', [$project, $fb]) }}" onsubmit="return confirm('Konversi saran ini menjadi tiket Issue di Backlog proyek?')">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-[11px] font-semibold transition">
                                                Jadikan Issue
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
                                    <button type="submit" title="Hapus Masukan" class="p-1 text-slate-400 hover:text-rose-500 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    <!-- Native Dialog: Kirim Masukan Baru -->
    <dialog id="modalNewFeedback" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                Sampaikan Saran &amp; Masukan
            </h3>
            <button type="button" onclick="closeModalDialog('modalNewFeedback')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('projects.feedback.store', $project) }}" class="p-5 space-y-4 text-xs">
            @csrf
            <div>
                <label for="fb-title" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Judul Saran / Usulan Fitur <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="fb-title" name="title" required
                    placeholder="Contoh: Tambahkan ekspor laporan ke format Excel"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="fb-category" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Kategori Masukan <span class="text-rose-500">*</span>
                </label>
                <select id="fb-category" name="category" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="feature_request">Permintaan Fitur Baru (Feature Request)</option>
                    <option value="improvement">Peningkatan Sistem / Workflow (Improvement)</option>
                    <option value="bug_report">Laporan Kendala / Bug (Bug Report)</option>
                    <option value="general">Saran Umum / Diskusi Proyek (General)</option>
                </select>
            </div>

            <div>
                <label for="fb-description" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Uraian &amp; Alasan Kebutuhan <span class="text-rose-500">*</span>
                </label>
                <textarea id="fb-description" name="description" rows="4" required
                    placeholder="Jelaskan kebutuhan Anda atau situasi kendala yang dihadapi secara detail..."
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalDialog('modalNewFeedback')"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    Kirim Masukan
                </button>
            </div>
        </form>
    </dialog>

    <!-- Native Dialog: PM Response -->
    @if($isProjectManager)
    <dialog id="modalPmResponse" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                Tanggapi Masukan Tim (PM Review)
            </h3>
            <button type="button" onclick="closeModalDialog('modalPmResponse')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <form id="form-pm-response" method="POST" action="" class="p-5 space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Judul Masukan:</span>
                <p id="resp-feedback-title" class="text-xs font-bold text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800"></p>
            </div>

            <div>
                <label for="resp-status" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Keputusan / Status Masukan <span class="text-rose-500">*</span>
                </label>
                <select id="resp-status" name="status" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending">Menunggu Review</option>
                    <option value="reviewed">Sedang Ditinjau</option>
                    <option value="planned">Disetujui / Direncanakan (Planned for Backlog)</option>
                    <option value="implemented">Telah Selesai (Implemented)</option>
                    <option value="rejected">Ditolak / Ditunda (Rejected)</option>
                </select>
            </div>

            <div>
                <label for="resp-notes" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Catatan &amp; Jawaban Project Manager
                </label>
                <textarea id="resp-notes" name="pm_response" rows="4"
                    placeholder="Tuliskan respon atau rencana implementasi kepada anggota tim..."
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalDialog('modalPmResponse')"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    Simpan Tanggapan
                </button>
            </div>
        </form>
    </dialog>

    <script>
        function openResponseModal(target, title, status, notes) {
            let id, fbTitle, fbStatus, fbNotes;
            if (target instanceof HTMLElement) {
                id = target.getAttribute('data-id');
                fbTitle = target.getAttribute('data-title');
                fbStatus = target.getAttribute('data-status');
                fbNotes = target.getAttribute('data-notes') || '';
            } else {
                id = target;
                fbTitle = title;
                fbStatus = status;
                fbNotes = notes || '';
            }

            const form = document.getElementById('form-pm-response');
            form.action = `/projects/{{ $project->id }}/feedback/${id}`;
            document.getElementById('resp-feedback-title').textContent = fbTitle;
            document.getElementById('resp-status').value = fbStatus;
            document.getElementById('resp-notes').value = fbNotes;
            openModalDialog('modalPmResponse');
        }
    </script>
    @endif
</x-app-layout>
