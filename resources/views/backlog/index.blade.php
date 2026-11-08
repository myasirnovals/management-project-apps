@php
    $canManageSprints = Auth::user()->canManageSprints();
    $canManageBacklog = Auth::user()->canManageBacklog();
    $canMutate = Auth::user()->canMutateIssues();
@endphp

<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                {{ $project->key }}
            </span>
            <span class="text-base font-bold text-slate-900 dark:text-white">Perencanaan Backlog &amp; Sprint</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('projects.backlog.exportCsv', $project) }}" title="Ekspor daftar backlog ke format CSV"
            class="btn-secondary !text-xs !py-1.5 !px-3">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Ekspor CSV</span>
        </a>

        @if($canMutate)
            <button type="button" command="show-modal" commandfor="cleanUpModal" title="Bersihkan issue yang telah selesai"
                class="btn-secondary !text-xs !py-1.5 !px-3">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Clean Up</span>
            </button>
        @endif

        @if($canManageSprints)
            <button type="button" command="show-modal" commandfor="createSprintModal" class="btn-primary !text-xs !py-1.5 !px-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Sprint</span>
            </button>
        @endif
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        <!-- Epics Panel (Left Column) -->
        <div class="lg:col-span-1 app-card p-4 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-1.5">
                    <h2 class="font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-slate-200">Panel Epic</h2>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">({{ $epics->count() }})</span>
                </div>
                @if($canManageBacklog)
                    <button type="button" command="show-modal" commandfor="createEpicModal" class="text-xs font-semibold text-blue-700 dark:text-blue-300 hover:underline">
                        + Epic
                    </button>
                @endif
            </div>

            <div class="space-y-1.5 text-xs">
                <a href="{{ route('projects.backlog', $project) }}"
                    class="block px-3 py-2 rounded-lg font-medium transition-colors {{ empty($selectedEpicId) ? 'bg-blue-50 dark:bg-blue-950 text-blue-800 dark:text-blue-200 font-semibold border border-blue-200 dark:border-blue-800' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Semua Issue (Tanpa Filter)
                </a>

                @forelse($epics as $epic)
                    <a href="{{ route('projects.backlog', [$project, 'epic_id' => $epic->id]) }}"
                        class="block px-3 py-2 rounded-lg transition-colors border {{ $selectedEpicId == $epic->id ? 'bg-purple-50 dark:bg-purple-950 text-purple-900 dark:text-purple-200 font-semibold border-purple-300 dark:border-purple-800' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center justify-between gap-1">
                            <span class="font-mono text-[11px] font-bold text-purple-700 dark:text-purple-300">{{ $epic->issue_key }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 font-medium">
                                {{ $epic->epic_issues_count }} issue
                            </span>
                        </div>
                        <div class="mt-1 truncate font-medium">{{ $epic->title }}</div>
                    </a>
                @empty
                    <p class="text-xs text-slate-500 dark:text-slate-400 p-2">Belum ada inisiatif Epic.</p>
                @endforelse
            </div>
        </div>

        <!-- Sprints & Backlog List (Right 3 Columns) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Sprints Section -->
            @foreach($sprints as $sprint)
                <div class="app-card p-4 sm:p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $sprint->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $sprint->status->badgeClasses() }}">
                                    {{ $sprint->status->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 italic">
                                Sasaran: {{ $sprint->goal ?: 'Belum ada sasaran sprint.' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs text-slate-600 dark:text-slate-300 font-mono mr-1">
                                <strong>{{ $sprint->total_story_points }}</strong> Story Points
                            </span>

                            @if($sprint->status->value === 'planning' && $canManageSprints)
                                <button type="button"
                                    data-sprint-id="{{ $sprint->id }}"
                                    data-sprint-name="{{ $sprint->name }}"
                                    data-sprint-goal="{{ $sprint->goal }}"
                                    onclick="openStartSprintModal(this)"
                                    class="btn-primary !text-xs !py-1 !px-2.5">
                                    Mulai Sprint
                                </button>
                            @elseif($sprint->status->value === 'active')
                                <a href="{{ route('projects.board', $project) }}" class="btn-primary !text-xs !py-1 !px-2.5">
                                    Papan Aktif
                                </a>
                            @endif

                            @if($canManageSprints)
                                <form method="POST" action="{{ route('projects.sprints.destroy', [$project, $sprint]) }}" onsubmit="return confirm('Hapus sprint ini dan kembalikan seluruh issue ke Backlog?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/60" title="Hapus Sprint">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Sprint Issues -->
                    <div class="space-y-2">
                        @forelse($sprint->issues as $issue)
                            <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/60 flex items-center justify-between text-xs hover:border-slate-300 dark:hover:border-slate-600 transition-colors gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="px-1.5 py-0.2 rounded font-mono text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                        {{ $issue->type->label() }}
                                    </span>
                                    <span class="font-mono font-bold text-blue-700 dark:text-blue-300 text-xs shrink-0">{{ $issue->issue_key }}</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $issue->title }}</span>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>

                                    @if($issue->story_points)
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-mono text-[10px] font-bold flex items-center justify-center">
                                            {{ $issue->story_points }}
                                        </span>
                                    @endif

                                    @if($canMutate)
                                        <form method="POST" action="{{ route('projects.backlog.move', $project) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                                            <input type="hidden" name="sprint_id" value="">
                                            <button type="submit" class="text-xs text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white px-2 py-1 rounded hover:bg-slate-200 dark:hover:bg-slate-800" title="Keluarkan ke Backlog">
                                                Kembalikan ke Backlog
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-500 dark:text-slate-400 text-xs border border-dashed border-slate-200 dark:border-slate-700 rounded-lg">
                                Belum ada issue dalam sprint ini. Pindahkan issue dari daftar Backlog di bawah.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <!-- Backlog Section with Bulk Actions -->
            <div class="app-card p-4 sm:p-5 space-y-4">
                <form id="bulkForm" method="POST" action="{{ route('projects.backlog.bulk', $project) }}">
                    @csrf
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Backlog Proyek</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                                {{ $backlogIssues->count() }} Issue
                            </span>
                        </div>

                        @if($canMutate)
                            <div class="flex items-center gap-2 flex-wrap">
                                <label for="bulk_action_select" class="sr-only">Aksi Massal</label>
                                <select name="action" id="bulk_action_select" onchange="toggleBulkActionFields(this.value)" class="form-select !text-xs !py-1 !px-2.5 w-auto">
                                    <option value="">Pilih Aksi Massal</option>
                                    <option value="delete">Hapus Terpilih</option>
                                    <option value="change_status">Ubah Status</option>
                                    @if($sprints->isNotEmpty())
                                        <option value="move_sprint">Pindahkan ke Sprint</option>
                                    @endif
                                </select>

                                <!-- Contextual Select: Target Status -->
                                <div id="bulk_status_container" class="hidden">
                                    <select name="target_status" id="bulk_target_status" class="form-select !text-xs !py-1 !px-2.5 w-auto">
                                        <option value="to_do">To Do</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="in_review">In Review</option>
                                        <option value="done">Done</option>
                                    </select>
                                </div>

                                <!-- Contextual Select: Target Sprint -->
                                @if($sprints->isNotEmpty())
                                    <div id="bulk_sprint_container" class="hidden">
                                        <select name="target_sprint_id" id="bulk_target_sprint" class="form-select !text-xs !py-1 !px-2.5 w-auto">
                                            @foreach($sprints as $sp)
                                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <button type="submit" onclick="return confirmBulkAction()" class="btn-secondary !text-xs !py-1 !px-3">
                                    Terapkan
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Backlog List -->
                    <div class="space-y-2 mt-3">
                        @forelse($backlogIssues as $issue)
                            <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-between text-xs hover:border-slate-300 dark:hover:border-slate-600 transition-colors gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    @if($canMutate)
                                        <input type="checkbox" name="issue_ids[]" value="{{ $issue->id }}" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-700 focus:ring-blue-600">
                                    @endif
                                    <span class="px-1.5 py-0.2 rounded font-mono text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                        {{ $issue->type->label() }}
                                    </span>
                                    <span class="font-mono font-bold text-blue-700 dark:text-blue-300 text-xs shrink-0">{{ $issue->issue_key }}</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $issue->title }}</span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($issue->component)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $issue->component->name }}
                                        </span>
                                    @endif

                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-semibold {{ $issue->priority->badgeClasses() }}">
                                        {{ $issue->priority->label() }}
                                    </span>

                                    @if($canMutate && $sprints->isNotEmpty())
                                        <div class="flex items-center gap-1">
                                            @foreach($sprints as $sp)
                                                <button type="button"
                                                    data-issue-id="{{ $issue->id }}"
                                                    data-sprint-id="{{ $sp->id }}"
                                                    onclick="handleMoveSingleIssue(this)"
                                                    class="btn-secondary !text-[11px] !py-0.5 !px-2" title="Pindahkan ke {{ $sp->name }}">
                                                    Ke {{ $sp->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-500 dark:text-slate-400 text-xs border border-dashed border-slate-200 dark:border-slate-700 rounded-lg">
                                Backlog kosong. Tambahkan user story atau task baru di bawah ini.
                            </div>
                        @endforelse
                    </div>
                </form>

                <!-- Quick Inline Story Creation -->
                @if($canMutate)
                    <form method="POST" action="{{ route('projects.issues.store', $project) }}" class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="story">
                        <label for="quick_story_input" class="sr-only">Tambah Story Baru</label>
                        <input id="quick_story_input" type="text" name="title" required placeholder="Tambah user story baru ke Backlog..."
                            class="form-input flex-1 !text-xs">
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3 shrink-0">
                            Tambah ke Backlog
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal: Create Epic (<dialog>) -->
    @if($canManageBacklog)
        <dialog id="createEpicModal" closedby="any" aria-labelledby="create_epic_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="create_epic_title" class="text-base font-bold text-slate-900 dark:text-white">Buat Inisiatif Epic</h3>
                    <button type="button" command="close" commandfor="createEpicModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form method="POST" action="{{ route('projects.issues.store', $project) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="epic">

                    <div>
                        <label for="create_epic_name" class="form-label">Nama Epic *</label>
                        <input id="create_epic_name" type="text" name="title" required placeholder="Contoh: Modul Pembayaran Kasir &amp; Integrasi QRIS"
                            class="form-input">
                    </div>

                    <div>
                        <label for="create_epic_desc" class="form-label">Deskripsi &amp; Sasaran Epic</label>
                        <textarea id="create_epic_desc" name="description" rows="3" placeholder="Uraikan ruang lingkup dan batasan inisiatif besar ini..."
                            class="form-input"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="createEpicModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Simpan Epic</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif

    <!-- Modal: Create Sprint (<dialog>) -->
    @if($canManageSprints)
        <dialog id="createSprintModal" closedby="any" aria-labelledby="create_sprint_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="create_sprint_title" class="text-base font-bold text-slate-900 dark:text-white">Buat Sprint Baru</h3>
                    <button type="button" command="close" commandfor="createSprintModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form method="POST" action="{{ route('projects.sprints.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="create_sprint_name" class="form-label">Nama Sprint *</label>
                        <input id="create_sprint_name" type="text" name="name" required value="{{ $project->key }} Sprint {{ $sprints->count() + 1 }}"
                            class="form-input">
                    </div>

                    <div>
                        <label for="create_sprint_goal" class="form-label">Sasaran Sprint</label>
                        <textarea id="create_sprint_goal" name="goal" rows="2" placeholder="Target fungsional yang ingin dicapai dalam sprint ini..."
                            class="form-input"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_sprint_dur" class="form-label">Durasi</label>
                            <select id="create_sprint_dur" name="duration_weeks" class="form-select">
                                <option value="1">1 Minggu</option>
                                <option value="2" selected>2 Minggu (Standar)</option>
                                <option value="3">3 Minggu</option>
                                <option value="4">4 Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label for="create_sprint_start" class="form-label">Tanggal Mulai</label>
                            <input id="create_sprint_start" type="date" name="start_date" value="{{ date('Y-m-d') }}"
                                class="form-input">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="createSprintModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Simpan Sprint</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Modal: Start Sprint (<dialog>) -->
        <dialog id="startSprintModal" closedby="any" aria-labelledby="start_sprint_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="start_sprint_title" class="text-base font-bold text-slate-900 dark:text-white">Mulai Sprint</h3>
                    <button type="button" command="close" commandfor="startSprintModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <form id="startSprintForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="start_name" class="form-label">Nama Sprint *</label>
                        <input type="text" id="start_name" name="name" required class="form-input">
                    </div>

                    <div>
                        <label for="start_goal" class="form-label">Sasaran Sprint</label>
                        <textarea id="start_goal" name="goal" rows="2" class="form-input"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="start_start_date" class="form-label">Tanggal Mulai *</label>
                            <input type="date" id="start_start_date" name="start_date" required value="{{ date('Y-m-d') }}"
                                class="form-input">
                        </div>
                        <div>
                            <label for="start_end_date" class="form-label">Tanggal Berakhir *</label>
                            <input type="date" id="start_end_date" name="end_date" required value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                                class="form-input">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="startSprintModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">Mulai Sprint Sekarang</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif

    <!-- Modal: Clean Up Backlog (<dialog>) -->
    @if($canMutate)
        <dialog id="cleanUpModal" closedby="any" aria-labelledby="cleanup_dialog_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="cleanup_dialog_title" class="text-base font-bold text-slate-900 dark:text-white">Clean Up Backlog</h3>
                    <button type="button" command="close" commandfor="cleanUpModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                    Pembersihan backlog menjaga performa dan keterbacaan daftar issue tim. Aksi ini akan mengarsipkan atau menghapus issue yang telah berstatus <strong>Done</strong> di luar sprint aktif.
                </p>

                <form method="POST" action="{{ route('projects.backlog.cleanUp', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cleanup_days" class="form-label">Hapus issue selesai yang lebih dari (hari):</label>
                        <input id="cleanup_days" type="number" name="days" value="90" min="1" max="365" class="form-input">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">Jangka waktu default: 90 hari</span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="cleanUpModal" class="btn-secondary !text-xs !py-1.5 !px-3">Batal</button>
                        <button type="submit" class="btn-danger !text-xs !py-1.5 !px-3">Jalankan Clean Up</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif

    <script>
        function toggleBulkActionFields(action) {
            const statusContainer = document.getElementById('bulk_status_container');
            const sprintContainer = document.getElementById('bulk_sprint_container');

            if (statusContainer) {
                if (action === 'change_status') {
                    statusContainer.classList.remove('hidden');
                } else {
                    statusContainer.classList.add('hidden');
                }
            }

            if (sprintContainer) {
                if (action === 'move_sprint') {
                    sprintContainer.classList.remove('hidden');
                } else {
                    sprintContainer.classList.add('hidden');
                }
            }
        }

        function openStartSprintModal(el) {
            const sprintId = el.dataset.sprintId;
            const name = el.dataset.sprintName;
            const goal = el.dataset.sprintGoal || '';
            const form = document.getElementById('startSprintForm');
            if (form) form.action = `/projects/{{ $project->code }}/sprints/${sprintId}/start`;
            const nameEl = document.getElementById('start_name');
            if (nameEl) nameEl.value = name;
            const goalEl = document.getElementById('start_goal');
            if (goalEl) goalEl.value = goal;
            openModalDialog('startSprintModal');
        }

        function handleMoveSingleIssue(btn) {
            moveSingleIssueToSprint(btn.dataset.issueId, btn.dataset.sprintId);
        }

        async function moveSingleIssueToSprint(issueId, sprintId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/projects/{{ $project->code }}/backlog/move`, {
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
            return confirm(`Lanjutkan penerapan aksi massal untuk ${checked.length} issue terpilih?`);
        }
    </script>
</x-app-layout>
