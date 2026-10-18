<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <span class="text-slate-700">|</span>
                <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                    {{ $project->key }}
                </span>
                <span class="text-sm font-bold text-white">Backlog & Sprint Planning</span>
            </div>

            <!-- Toolbar Actions: Create Sprint, Clean Up, Export CSV -->
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.backlog.exportCsv', $project) }}" title="Export ke CSV (Modul VI)"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:text-white transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                <button type="button" onclick="openCleanUpModal()" title="Bersihkan issue lama (Modul VI.B)"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:text-white transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Clean Up</span>
                </button>

                <button type="button" onclick="openCreateSprintModal()"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Sprint</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        <!-- Left Column: Epics Panel (Modul II.A & VI) -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                    <span>⚡ Epics Panel</span>
                    <span class="text-[10px] text-slate-500 font-normal">({{ $epics->count() }})</span>
                </h3>
                <button type="button" onclick="openCreateEpicModal()" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                    + Epic
                </button>
            </div>

            <!-- Epic List Filters -->
            <div class="space-y-1.5 text-xs">
                <a href="{{ route('projects.backlog', $project) }}"
                    class="block p-2.5 rounded-xl font-medium transition {{ empty($selectedEpicId) ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    Semua Issues (Tanpa Filter)
                </a>

                @foreach($epics as $epic)
                    <a href="{{ route('projects.backlog', [$project, 'epic_id' => $epic->id]) }}"
                        class="p-2.5 rounded-xl block transition {{ $selectedEpicId == $epic->id ? 'bg-purple-600 text-white font-bold' : 'bg-slate-950/60 hover:bg-slate-800 text-slate-300' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] text-purple-300">{{ $epic->issue_key }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 bg-purple-900/60 rounded text-purple-200">
                                {{ $epic->epic_issues_count }} issues
                            </span>
                        </div>
                        <div class="font-semibold mt-1 truncate">{{ $epic->title }}</div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Right 3 Columns: Sprints & Backlog Containers -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Sprints Section (Modul III.B Gambar 3.4 - 3.7) -->
            @foreach($sprints as $sprint)
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white">{{ $sprint->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $sprint->status->badgeClasses() }}">
                                    {{ $sprint->status->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5 italic">
                                Goal: {{ $sprint->goal ?: 'Belum ada goal.' }}
                            </p>
                        </div>

                        <!-- Actions for Sprint -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 mr-2">
                                <strong class="text-white">{{ $sprint->total_story_points }}</strong> Story Points
                            </span>

                            @if($sprint->status->value === 'planning')
                                <button type="button"
                                    data-sprint-id="{{ $sprint->id }}"
                                    data-sprint-name="{{ $sprint->name }}"
                                    data-sprint-goal="{{ $sprint->goal }}"
                                    onclick="openStartSprintModal(this)"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-sm transition">
                                    Mulai Sprint &rarr;
                                </button>
                            @elseif($sprint->status->value === 'active')
                                <a href="{{ route('projects.board', $project) }}"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm transition">
                                    Buka Active Board
                                </a>
                            @endif

                            <form method="POST" action="{{ route('projects.sprints.destroy', [$project, $sprint]) }}" onsubmit="return confirm('Hapus sprint ini dan kembalikan issue ke Backlog?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-400 transition" title="Hapus Sprint">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sprint Issues List -->
                    <div class="space-y-2">
                        @forelse($sprint->issues as $issue)
                            <div class="p-3 bg-slate-950/70 border border-slate-800 rounded-2xl flex items-center justify-between text-xs hover:border-slate-700 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                        {{ $issue->type->label() }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-400 text-xs">{{ $issue->issue_key }}</span>
                                    <span class="font-medium text-slate-200 truncate">{{ $issue->title }}</span>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>

                                    @if($issue->story_points)
                                        <span class="w-5 h-5 rounded-full bg-slate-800 text-slate-300 text-[10px] font-bold flex items-center justify-center">
                                            {{ $issue->story_points }}
                                        </span>
                                    @endif

                                    <!-- Move Back to Backlog Button -->
                                    <form method="POST" action="{{ route('projects.backlog.move', $project) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                                        <input type="hidden" name="sprint_id" value="">
                                        <button type="submit" class="text-xs text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800" title="Keluarkan ke Backlog">
                                            &darr; Backlog
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl">
                                Belum ada issue dalam sprint ini. Tarik atau pindahkan issue dari Backlog di bawah ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <!-- Backlog Container (Modul VI) -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg space-y-4">
                <form id="bulkForm" method="POST" action="{{ route('projects.backlog.bulk', $project) }}">
                    @csrf
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-white">Backlog Proyek</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 text-slate-300">
                                {{ $backlogIssues->count() }} Issues
                            </span>
                        </div>

                        <!-- Bulk Actions Control (Modul VI.B Gambar 6.14 - 6.18) -->
                        <div class="flex items-center gap-2">
                            <select name="action" id="bulk_action_select" class="bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white focus:outline-none">
                                <option value="">-- Aksi Massal (Bulk) --</option>
                                <option value="delete">🗑️ Hapus yang Dipilih</option>
                                <option value="change_status">🔄 Ubah Status Massal</option>
                                <option value="move_sprint">📦 Pindahkan ke Sprint</option>
                            </select>

                            <button type="submit" onclick="return confirmBulkAction()" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 transition">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <!-- Backlog Issues List -->
                    <div class="space-y-2 mt-3">
                        @forelse($backlogIssues as $issue)
                            <div class="p-3 bg-slate-950/70 border border-slate-800 rounded-2xl flex items-center justify-between text-xs hover:border-slate-700 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <input type="checkbox" name="issue_ids[]" value="{{ $issue->id }}" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                        {{ $issue->type->label() }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-400 text-xs">{{ $issue->issue_key }}</span>
                                    <span class="font-medium text-slate-200 truncate">{{ $issue->title }}</span>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($issue->component)
                                        <span class="px-2 py-0.2 rounded text-[10px] bg-slate-800 text-slate-300">
                                            {{ $issue->component->name }}
                                        </span>
                                    @endif

                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $issue->priority->badgeClasses() }}">
                                        {{ $issue->priority->label() }}
                                    </span>

                                    <!-- Move to Sprint Dropdown -->
                                    @if($sprints->isNotEmpty())
                                        <div class="flex items-center gap-1">
                                            @foreach($sprints as $sp)
                                                <button type="button"
                                                    data-issue-id="{{ $issue->id }}"
                                                    data-sprint-id="{{ $sp->id }}"
                                                    onclick="handleMoveSingleIssue(this)"
                                                    class="px-2 py-1 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg text-[10px] font-semibold transition" title="Pindahkan ke {{ $sp->name }}">
                                                    &uarr; {{ $sp->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl">
                                Backlog kosong! Tekan tombol "+ Buat Issue" di atas untuk menambahkan user story atau task baru.
                            </div>
                        @endforelse
                    </div>
                </form>

                <!-- Quick Inline Backlog Creation (Modul II Gambar 2.16 & 2.19) -->
                <form method="POST" action="{{ route('projects.issues.store', $project) }}" class="mt-4 pt-3 border-t border-slate-800 flex gap-2">
                    @csrf
                    <input type="hidden" name="type" value="story">
                    <input type="text" name="title" required placeholder="+ What needs to be done? (Ketik tugas baru & tekan Enter)"
                        class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition">
                        Tambah ke Backlog
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Create Epic (Modul II.A Gambar 2.7 & 2.8) -->
    <div id="createEpicModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">⚡ Buat Epic Baru</h3>
                <button type="button" onclick="closeCreateEpicModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('projects.issues.store', $project) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="epic">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Epic *</label>
                    <input type="text" name="title" required placeholder="Contoh: Autentikasi Pengguna & Hak Akses"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi / Sasaran Epic</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan inisiatif besar ini..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCreateEpicModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-500">Buat Epic</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Create Sprint (Modul III.B Gambar 3.3 & 3.7) -->
    <div id="createSprintModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Buat Sprint Baru</h3>
                <button type="button" onclick="closeCreateSprintModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('projects.sprints.store', $project) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Sprint *</label>
                    <input type="text" name="name" required value="{{ $project->key }} Sprint {{ $sprints->count() + 1 }}"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Sprint Goal</label>
                    <textarea name="goal" rows="2" placeholder="Tujuan yang ingin dicapai dalam sprint ini..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Durasi</label>
                        <select name="duration_weeks" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            <option value="1">1 Minggu</option>
                            <option value="2" selected>2 Minggu (Rekomendasi)</option>
                            <option value="3">3 Minggu</option>
                            <option value="4">4 Minggu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCreateSprintModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500">Buat Sprint</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Start Sprint (Modul III.B Gambar 3.7) -->
    <div id="startSprintModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white" id="start_sprint_title">Mulai Sprint</h3>
                <button type="button" onclick="closeStartSprintModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form id="startSprintForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Sprint *</label>
                    <input type="text" id="start_name" name="name" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Sprint Goal</label>
                    <textarea id="start_goal" name="goal" rows="2" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Mulai *</label>
                        <input type="date" id="start_start_date" name="start_date" required value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Berakhir *</label>
                        <input type="date" id="start_end_date" name="end_date" required value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeStartSprintModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500">Mulai Sprint Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Clean Up Backlog (Modul VI.B Gambar 6.10 - 6.12) -->
    <div id="cleanUpModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Clean Up Backlog</h3>
                <button type="button" onclick="closeCleanUpModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                Pembersihan backlog secara berkala menjaga performa dan fokus tim. Fitur ini akan menghapus issue yang sudah berstatus <strong>Done</strong> yang berada di luar sprint aktif.
            </p>

            <form method="POST" action="{{ route('projects.backlog.cleanUp', $project) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Hapus issue selesai yang lebih dari (hari):</label>
                    <input type="number" name="days" value="90" min="1" max="365"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white">
                    <span class="text-[11px] text-slate-500 mt-1 block">Default modul: 90 hari (More Than Day)</span>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCleanUpModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500">Jalankan Clean Up</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateEpicModal() { document.getElementById('createEpicModal').classList.remove('hidden'); }
        function closeCreateEpicModal() { document.getElementById('createEpicModal').classList.add('hidden'); }
        function openCreateSprintModal() { document.getElementById('createSprintModal').classList.remove('hidden'); }
        function closeCreateSprintModal() { document.getElementById('createSprintModal').classList.add('hidden'); }
        function openCleanUpModal() { document.getElementById('cleanUpModal').classList.remove('hidden'); }
        function closeCleanUpModal() { document.getElementById('cleanUpModal').classList.add('hidden'); }

        function openStartSprintModal(el) {
            const sprintId = el.dataset.sprintId;
            const name = el.dataset.sprintName;
            const goal = el.dataset.sprintGoal || '';
            document.getElementById('startSprintForm').action = `/projects/{{ $project->id }}/sprints/${sprintId}/start`;
            document.getElementById('start_name').value = name;
            document.getElementById('start_goal').value = goal;
            document.getElementById('startSprintModal').classList.remove('hidden');
        }
        function closeStartSprintModal() { document.getElementById('startSprintModal').classList.add('hidden'); }

        function handleMoveSingleIssue(btn) {
            moveSingleIssueToSprint(btn.dataset.issueId, btn.dataset.sprintId);
        }

        async function moveSingleIssueToSprint(issueId, sprintId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/projects/{{ $project->id }}/backlog/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ issue_id: issueId, sprint_id: sprintId })
            });
            if (res.ok) {
                window.location.reload();
            } else {
                alert('Gagal memindahkan issue.');
            }
        }

        function confirmBulkAction() {
            const action = document.getElementById('bulk_action_select').value;
            if (!action) {
                alert('Pilih jenis aksi massal terlebih dahulu.');
                return false;
            }
            const checked = document.querySelectorAll('input[name="issue_ids[]"]:checked');
            if (checked.length === 0) {
                alert('Pilih minimal satu issue.');
                return false;
            }
            return confirm(`Lanjutkan ${action} untuk ${checked.length} issue terpilih?`);
        }
    </script>
</x-app-layout>
