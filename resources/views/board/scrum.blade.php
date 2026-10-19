<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">{{ $currentSprint ? $currentSprint->name : 'Sprint Board (Backlog View)' }}</span>
            @if($currentSprint && $currentSprint->status->value === 'active')
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                Aktif
            </span>
            @if($currentSprint->end_date)
            <span class="text-[11px] text-slate-400">
                • Berakhir: {{ \Carbon\Carbon::parse($currentSprint->end_date)->diffForHumans() }}
            </span>
            @endif
            @endif
        </div>
    </x-slot>

    @if($currentSprint && $currentSprint->status->value === 'active' && Auth::user()->isProjectManager())
    <x-slot name="headerActions">
        <button type="button" onclick="openCompleteSprintModal()"
            class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/30 transition">
            Selesaikan Sprint
        </button>
    </x-slot>
    @endif

    <div class="space-y-4 max-w-full">
        <!-- Filter & Control Toolbar (Modul IV.D Quick Filters & Swimlanes) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
            <!-- Left: Search & Quick Filters -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Search Input -->
                <form method="GET" action="{{ route('projects.board', $project) }}" class="relative">
                    <input type="hidden" name="swimlane" value="{{ $swimlaneMode }}">
                    @if($quickFilter)<input type="hidden" name="filter" value="{{ $quickFilter }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari issue atau kode..."
                        class="pl-8 pr-3 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-44 sm:w-56">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>

                <span class="text-slate-600">|</span>

                <!-- Quick Filters Buttons (Modul IV.D) -->
                <a href="{{ route('projects.board', [$project, 'swimlane' => $swimlaneMode]) }}"
                    class="px-3 py-1.5 rounded-xl font-medium transition {{ empty($quickFilter) ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    Semua
                </a>

                <a href="{{ route('projects.board', [$project, 'filter' => 'only_my_issues', 'swimlane' => $swimlaneMode]) }}"
                    class="px-3 py-1.5 rounded-xl font-medium transition {{ $quickFilter === 'only_my_issues' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    Only My Issues
                </a>

                <a href="{{ route('projects.board', [$project, 'filter' => 'recently_updated', 'swimlane' => $swimlaneMode]) }}"
                    class="px-3 py-1.5 rounded-xl font-medium transition {{ $quickFilter === 'recently_updated' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    Recently Updated
                </a>

                <a href="{{ route('projects.board', [$project, 'filter' => 'bugs_only', 'swimlane' => $swimlaneMode]) }}"
                    class="px-3 py-1.5 rounded-xl font-medium transition {{ $quickFilter === 'bugs_only' ? 'bg-rose-600 text-white' : 'bg-slate-950 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    🐞 Bugs Saja
                </a>
            </div>

            <!-- Right: Swimlane selector & Sprint Switcher -->
            <div class="flex items-center gap-3">
                <!-- Swimlane Selector (Modul IV.C Gambar 4.20) -->
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 font-semibold">Swimlane:</span>
                    <select onchange="window.location.href = this.value" class="bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white focus:outline-none">
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'none']) }}" {{ $swimlaneMode === 'none' ? 'selected' : '' }}>None</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'stories']) }}" {{ $swimlaneMode === 'stories' ? 'selected' : '' }}>Stories</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'assignee']) }}" {{ $swimlaneMode === 'assignee' ? 'selected' : '' }}>Assignees</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'epic']) }}" {{ $swimlaneMode === 'epic' ? 'selected' : '' }}>Epics</option>
                    </select>
                </div>

                <!-- Sprint Selector -->
                @if($allSprints->count() > 1)
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 font-semibold">Sprint:</span>
                    <select onchange="window.location.href = this.value" class="bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white focus:outline-none">
                        @foreach($allSprints as $sp)
                        <option value="{{ route('projects.board', [$project, 'sprint_id' => $sp->id, 'swimlane' => $swimlaneMode, 'filter' => $quickFilter]) }}"
                            {{ $currentSprint && $currentSprint->id === $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ $sp->status->label() }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        @if(!$currentSprint)
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-5 text-center">
            <h4 class="text-sm font-bold text-indigo-300">Belum Ada Sprint Aktif</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                Untuk mulai menggunakan papan aktif Scrum, buat sprint baru dan masukkan issue dari halaman Backlog, kemudian klik "Mulai Sprint".
            </p>
            <a href="{{ route('projects.backlog', $project) }}" class="inline-block mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition">
                Ke Halaman Backlog &rarr;
            </a>
        </div>
        @endif

        <!-- Scrum Board Grid (4 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">
            @foreach($columns as $colKey => $col)
            @php
            $colIssuesCount = $col['issues']->count();
            $isOverLimit = $col['max'] && $colIssuesCount > $col['max'];
            @endphp
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-4 flex flex-col min-h-[500px]">
                <!-- Column Header with Constraints (Modul IV.B Gambar 4.18) -->
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $colKey === 'to_do' ? 'bg-slate-400' : ($colKey === 'in_progress' ? 'bg-blue-400' : ($colKey === 'in_review' ? 'bg-amber-400' : 'bg-emerald-400')) }}"></span>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-200">{{ $col['title'] }}</h3>
                    </div>

                    <!-- WIP Constraints Badge -->
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $isOverLimit ? 'bg-rose-500 text-white' : 'bg-slate-800 text-slate-300' }}">
                            {{ $colIssuesCount }}
                            @if($col['max'])
                            / <span class="text-slate-400">{{ $col['max'] }}</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Column Cards Container -->
                <div class="space-y-3 flex-1 overflow-y-auto"
                    data-status="{{ $col['status']->value }}"
                    ondragover="handleDragOver(event)"
                    ondrop="handleDrop(event, this)">
                    @forelse($col['issues'] as $issue)
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4 shadow-sm hover:border-indigo-500/50 hover:shadow-md transition cursor-pointer group"
                        draggable="true"
                        ondragstart="handleDragStart(event, this)"
                        data-issue-id="{{ $issue->id }}"
                        onclick="openIssueDetailModal(this.dataset.issueId)">

                        <!-- Card Header: Key & Priority -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                    {{ $issue->type->label() }}
                                </span>
                                <span class="font-mono text-xs font-bold text-slate-400 group-hover:text-indigo-400 transition">
                                    {{ $issue->issue_key }}
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $issue->priority->badgeClasses() }}">
                                {{ $issue->priority->label() }}
                            </span>
                        </div>

                        <!-- Card Title -->
                        <h4 class="text-xs font-semibold text-slate-200 line-clamp-2 leading-snug mb-2 group-hover:text-white transition">
                            {{ $issue->title }}
                        </h4>

                        <!-- Card Meta: Component & Epic -->
                        <div class="flex flex-wrap items-center gap-1.5 mb-3">
                            @if($issue->component)
                            <span class="px-2 py-0.2 rounded text-[10px] bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $issue->component->name }}
                            </span>
                            @endif
                            @if($issue->parent && $issue->parent->type->value === 'epic')
                            <span class="px-2 py-0.2 rounded text-[10px] bg-purple-950 text-purple-300 border border-purple-800">
                                ⚡ {{ $issue->parent->title }}
                            </span>
                            @endif
                        </div>

                        <!-- Card Footer: Quick Move Dropdown & Assignee -->
                        <div class="flex items-center justify-between pt-2 border-t border-slate-800/60" onclick="event.stopPropagation()">
                            <!-- Quick Move Select -->
                            <form method="POST" action="{{ route('projects.board.status', $project) }}" class="inline">
                                @csrf
                                <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                                <select name="status" onchange="this.form.submit()"
                                    class="bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-[10px] font-medium text-slate-300 focus:outline-none hover:border-indigo-500">
                                    <option value="to_do" {{ $issue->status->value === 'to_do' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ $issue->status->value === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="in_review" {{ $issue->status->value === 'in_review' ? 'selected' : '' }}>In Review</option>
                                    <option value="done" {{ $issue->status->value === 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </form>

                            <!-- Story Points & Assignee Avatar -->
                            <div class="flex items-center gap-2">
                                @if($issue->story_points !== null)
                                <span class="w-5 h-5 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-bold flex items-center justify-center" title="Story Points">
                                    {{ $issue->story_points }}
                                </span>
                                @endif
                                @if($issue->assignee)
                                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center ring-1 ring-slate-700" title="{{ $issue->assignee->name }}">
                                    {{ $issue->assignee->initials() }}
                                </div>
                                @else
                                <div class="w-6 h-6 rounded-full bg-slate-800 border border-dashed border-slate-700 text-slate-500 text-[10px] flex items-center justify-center" title="Unassigned">
                                    ?
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="h-32 border border-dashed border-slate-800 rounded-2xl flex items-center justify-center text-slate-600 text-xs">
                        Tidak ada kartu
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Complete Sprint Modal (Modul III.B) -->
    @if($currentSprint && $currentSprint->status->value === 'active')
    <div id="completeSprintModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl p-6">
            <h3 class="text-base font-bold text-white mb-2">Selesaikan {{ $currentSprint->name }}?</h3>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                Sprint ini memiliki <strong>{{ $currentSprint->issues->where('status', \App\Enums\IssueStatus::DONE)->count() }}</strong> issue selesai dan <strong>{{ $currentSprint->issues->where('status', '!=', \App\Enums\IssueStatus::DONE)->count() }}</strong> issue yang belum selesai.
            </p>

            <form method="POST" action="{{ route('projects.sprints.complete', [$project, $currentSprint]) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pindahkan issue yang belum selesai ke:</label>
                    <select name="incomplete_action" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <option value="backlog">Backlog Proyek</option>
                        @foreach($project->sprints->where('status', \App\Enums\SprintStatus::PLANNING) as $planSprint)
                        <option value="{{ $planSprint->id }}">{{ $planSprint->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeCompleteSprintModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500">Selesaikan Sprint</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Issue Detail & Edit Modal Drawer (Modul IV.G Gambar 4.45 - 4.48) -->
    <div id="issueDetailModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-end p-0">
        <div class="bg-slate-900 border-l border-slate-800 w-full max-w-2xl h-full overflow-y-auto p-6 sm:p-8 flex flex-col justify-between shadow-2xl animate-in slide-in-from-right duration-200">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <div class="flex items-center gap-2">
                        <span id="detail_key" class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20"></span>
                        <span id="detail_type_badge" class="px-2 py-0.5 rounded text-[10px] font-bold border"></span>
                    </div>
                    <button type="button" onclick="closeIssueDetailModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form id="editIssueForm" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Judul / Summary</label>
                        <input type="text" id="detail_title" name="title" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Deskripsi</label>
                        <textarea id="detail_description" name="description" rows="3"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Details Fields Grid (Modul IV.G General Fields) -->
                    <div class="grid grid-cols-2 gap-4 p-4 bg-slate-950/60 border border-slate-800 rounded-2xl">
                        <!-- Status -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status</label>
                            <select id="detail_status" name="status" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                                <option value="to_do">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="in_review">In Review</option>
                                <option value="done">Done</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Prioritas</label>
                            <select id="detail_priority" name="priority" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                                <option value="highest">🔴 Highest</option>
                                <option value="high">🟠 High</option>
                                <option value="medium">🟡 Medium</option>
                                <option value="low">🔵 Low</option>
                                <option value="lowest">⚪ Lowest</option>
                            </select>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Tipe</label>
                            <select id="detail_type" name="type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                                <option value="story">Story</option>
                                <option value="task">Task</option>
                                <option value="bug">Bug</option>
                                <option value="epic">Epic</option>
                                <option value="subtask">Sub-task</option>
                            </select>
                        </div>

                        <!-- Story Points -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Story Points</label>
                            <input type="number" id="detail_story_points" name="story_points" min="0" max="100"
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                        </div>

                        <!-- Assignee -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Assignee</label>
                            <select id="detail_assignee_id" name="assignee_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Component -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Komponen</label>
                            <select id="detail_component_id" name="component_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white">
                                <option value="">-- Tidak ada --</option>
                                @foreach($project->components as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                        <button type="button" id="deleteIssueBtn" onclick="deleteCurrentIssue()"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition">
                            Hapus Issue
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="closeIssueDetailModal()" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>

                <!-- Subtasks Section (Modul II.C Gambar 2.20 & 2.21) -->
                <div class="mt-8 pt-6 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">Sub-tasks (Pekerjaan Turunan)</h4>

                    <div id="subtasks_container" class="space-y-2 mb-4">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <form id="createSubtaskForm" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" id="subtask_title_input" name="title" required placeholder="Tambah sub-task baru..."
                            class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition">
                            + Subtask
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentOpenIssueId = null;

        function openCompleteSprintModal() {
            document.getElementById('completeSprintModal').classList.remove('hidden');
        }

        function closeCompleteSprintModal() {
            document.getElementById('completeSprintModal').classList.add('hidden');
        }

        async function openIssueDetailModal(issueId) {
            currentOpenIssueId = issueId;
            const modal = document.getElementById('issueDetailModal');

            try {
                const response = await fetch(`/projects/{{ $project->code }}/issues/${issueId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                const issue = data.issue;

                document.getElementById('detail_key').innerText = issue.issue_key;
                document.getElementById('detail_title').value = issue.title;
                document.getElementById('detail_description').value = issue.description || '';
                document.getElementById('detail_status').value = issue.status;
                document.getElementById('detail_priority').value = issue.priority;
                document.getElementById('detail_type').value = issue.type;
                document.getElementById('detail_story_points').value = issue.story_points || '';
                document.getElementById('detail_assignee_id').value = issue.assignee_id || '';
                document.getElementById('detail_component_id').value = issue.component_id || '';

                const badge = document.getElementById('detail_type_badge');
                badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold border ' + data.type_badge;
                badge.innerText = issue.type;

                document.getElementById('editIssueForm').action = `/projects/{{ $project->code }}/issues/${issue.id}`;
                document.getElementById('createSubtaskForm').action = `/projects/{{ $project->code }}/issues/${issue.id}/subtasks`;

                // Render Subtasks
                const subContainer = document.getElementById('subtasks_container');
                subContainer.innerHTML = '';
                if (issue.subtasks && issue.subtasks.length > 0) {
                    issue.subtasks.forEach(st => {
                        const div = document.createElement('div');
                        div.className = 'p-2.5 bg-slate-950/80 border border-slate-800 rounded-xl flex items-center justify-between text-xs';
                        div.innerHTML = `
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-cyan-400 font-bold">${st.issue_key}</span>
                                <span class="text-slate-200">${st.title}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300">${st.status}</span>
                        `;
                        subContainer.appendChild(div);
                    });
                } else {
                    subContainer.innerHTML = '<div class="text-slate-500 text-xs italic">Belum ada subtask untuk issue ini.</div>';
                }

                modal.classList.remove('hidden');
            } catch (err) {
                console.error(err);
                alert('Gagal mengambil detail issue.');
            }
        }

        function closeIssueDetailModal() {
            document.getElementById('issueDetailModal').classList.add('hidden');
        }

        async function deleteCurrentIssue() {
            if (!currentOpenIssueId) return;
            if (!confirm('Apakah Anda yakin ingin menghapus issue ini?')) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await fetch(`/projects/{{ $project->code }}/issues/${currentOpenIssueId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus issue.');
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        function handleDragStart(e, el) {
            e.dataTransfer.setData('text/plain', el.dataset.issueId);
        }

        function handleDragOver(e) {
            e.preventDefault();
        }

        async function handleDrop(e, colEl) {
            e.preventDefault();
            const issueId = e.dataTransfer.getData('text/plain');
            const targetStatus = colEl.dataset.status;
            if (!issueId || !targetStatus) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await fetch(`/projects/{{ $project->code }}/board/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        issue_id: issueId,
                        status: targetStatus
                    })
                });
                if (res.ok) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        }
    </script>
</x-app-layout>