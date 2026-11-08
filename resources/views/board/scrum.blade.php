<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                {{ $project->key }}
            </span>
            <span class="text-base font-bold text-slate-900 dark:text-white">
                {{ $currentSprint ? $currentSprint->name : 'Papan Scrum (Backlog)' }}
            </span>
            @if($currentSprint && $currentSprint->status->value === 'active')
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    Aktif
                </span>
                @if($currentSprint->end_date)
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        • Berakhir {{ \Carbon\Carbon::parse($currentSprint->end_date)->diffForHumans() }}
                    </span>
                @endif
            @endif
        </div>
    </x-slot>

    @if($currentSprint && $currentSprint->status->value === 'active' && Auth::user()->canManageSprints())
        <x-slot name="headerActions">
            <button type="button" command="show-modal" commandfor="completeSprintModal" class="btn-primary !text-xs !py-1.5 !px-3">
                Selesaikan Sprint
            </button>
        </x-slot>
    @endif

    <div class="space-y-4 max-w-full">
        <!-- Toolbar: Filters & Swimlanes -->
        <div class="app-card p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('projects.board', $project) }}" class="relative">
                    <input type="hidden" name="swimlane" value="{{ $swimlaneMode }}">
                    @if($quickFilter)<input type="hidden" name="filter" value="{{ $quickFilter }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau judul issue..."
                        class="form-input !text-xs !py-1.5 !pl-8 !pr-3 w-48 sm:w-60">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>

                <div class="inline-flex items-center rounded-lg p-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <a href="{{ route('projects.board', [$project, 'swimlane' => $swimlaneMode]) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition-colors {{ empty($quickFilter) ? 'bg-white dark:bg-slate-900 text-blue-800 dark:text-blue-200 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Semua
                    </a>
                    <a href="{{ route('projects.board', [$project, 'filter' => 'only_my_issues', 'swimlane' => $swimlaneMode]) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition-colors {{ $quickFilter === 'only_my_issues' ? 'bg-white dark:bg-slate-900 text-blue-800 dark:text-blue-200 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Tugas Saya
                    </a>
                    <a href="{{ route('projects.board', [$project, 'filter' => 'recently_updated', 'swimlane' => $swimlaneMode]) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition-colors {{ $quickFilter === 'recently_updated' ? 'bg-white dark:bg-slate-900 text-blue-800 dark:text-blue-200 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Baru Diperbarui
                    </a>
                    <a href="{{ route('projects.board', [$project, 'filter' => 'bugs_only', 'swimlane' => $swimlaneMode]) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition-colors {{ $quickFilter === 'bugs_only' ? 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 shadow-2xs font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Bug
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <label for="swimlane_selector" class="text-slate-600 dark:text-slate-400 font-semibold text-xs">Swimlane:</label>
                    <select id="swimlane_selector" onchange="window.location.href = this.value" class="form-select !text-xs !py-1 !px-2 w-auto">
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'none']) }}" {{ $swimlaneMode === 'none' ? 'selected' : '' }}>None</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'stories']) }}" {{ $swimlaneMode === 'stories' ? 'selected' : '' }}>Stories</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'assignee']) }}" {{ $swimlaneMode === 'assignee' ? 'selected' : '' }}>Assignees</option>
                        <option value="{{ route('projects.board', [$project, 'filter' => $quickFilter, 'swimlane' => 'epic']) }}" {{ $swimlaneMode === 'epic' ? 'selected' : '' }}>Epics</option>
                    </select>
                </div>

                @if($allSprints->count() > 1)
                    <div class="flex items-center gap-1.5">
                        <label for="sprint_selector" class="text-slate-600 dark:text-slate-400 font-semibold text-xs">Sprint:</label>
                        <select id="sprint_selector" onchange="window.location.href = this.value" class="form-select !text-xs !py-1 !px-2 w-auto">
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
            <div class="app-card p-6 text-center bg-blue-50/40 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Sprint yang Sedang Aktif</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 max-w-md mx-auto">
                    Papan Scrum menampilkan issue dari sprint aktif. Buka halaman Backlog untuk merencanakan dan memulai sprint baru.
                </p>
                <a href="{{ route('projects.backlog', $project) }}" class="btn-primary mt-3 !text-xs">
                    Buka Perencanaan Backlog
                </a>
            </div>
        @endif

        <!-- Board Scroller Container -->
        <div class="board-scroller space-y-6">
            @foreach($swimlanes as $lane)
                @php
                    $laneIssuesCount = collect($lane['columns'])->sum(fn ($c) => $c['issues']->count());
                @endphp

                <div class="space-y-3">
                    @if($swimlaneMode !== 'none')
                        <div class="flex items-center gap-2 px-1">
                            <span class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                {{ $lane['title'] }}
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {{ $laneIssuesCount }} issue
                            </span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start min-w-[72rem] xl:min-w-0">
                        @foreach($lane['columns'] as $colKey => $col)
                            @php
                                $colCount = $col['issues']->count();
                                $isOverLimit = $col['max'] && $colCount > $col['max'];
                            @endphp

                            <div class="app-card p-3.5 flex flex-col min-h-96">
                                <!-- Column Header -->
                                <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $colKey === 'to_do' ? 'bg-slate-400' : ($colKey === 'in_progress' ? 'bg-blue-500' : ($colKey === 'in_review' ? 'bg-amber-500' : 'bg-emerald-500')) }}"></span>
                                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                            {{ $col['title'] }}
                                        </h4>
                                    </div>

                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold {{ $isOverLimit ? 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                        {{ $colCount }}
                                        @if($col['max'])
                                            / <span class="text-slate-500 dark:text-slate-400">{{ $col['max'] }}</span>
                                        @endif
                                    </span>
                                </div>

                                <!-- Dropzone & Cards -->
                                <div class="space-y-2.5 flex-1 overflow-y-auto"
                                    data-status="{{ $col['status']->value }}"
                                    ondragover="handleDragOver(event)"
                                    ondrop="handleDrop(event, this)">
                                    @forelse($col['issues'] as $issue)
                                        <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-blue-400 dark:hover:border-blue-600 transition-colors shadow-2xs cursor-pointer group"
                                            draggable="{{ Auth::user()->canMutateIssues() ? 'true' : 'false' }}"
                                            ondragstart="handleDragStart(event, this)"
                                            data-issue-id="{{ $issue->id }}"
                                            onclick="openBoardIssueDetail({{ $issue->id }})">

                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-1.5 py-0.2 rounded font-mono text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-blue-700 dark:text-blue-300 border border-slate-300 dark:border-slate-700">
                                                        {{ $issue->issue_key }}
                                                    </span>
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold border {{ $issue->type->badgeClasses() }}">
                                                        {{ $issue->type->label() }}
                                                    </span>
                                                    @if($issue->pullRequests && $issue->pullRequests->isNotEmpty())
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800" title="Pull Request GitHub">
                                                            PR #{{ $issue->pullRequests->first()->pr_number }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-semibold {{ $issue->priority->badgeClasses() }}">
                                                    {{ $issue->priority->label() }}
                                                </span>
                                            </div>

                                            <h5 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                                                {{ $issue->title }}
                                            </h5>

                                            @if($issue->component || ($issue->parent && $issue->parent->type->value === 'epic'))
                                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                                    @if($issue->component)
                                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                            {{ $issue->component->name }}
                                                        </span>
                                                    @endif
                                                    @if($issue->parent && $issue->parent->type->value === 'epic')
                                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                            {{ $issue->parent->title }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100 dark:border-slate-800" onclick="event.stopPropagation()">
                                                @if(Auth::user()->canMutateIssues())
                                                    <form method="POST" action="{{ route('projects.board.status', $project) }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                                                        <select name="status" onchange="this.form.submit()" class="form-select !text-[11px] !py-0.5 !px-2 w-auto">
                                                            <option value="to_do" {{ $issue->status->value === 'to_do' ? 'selected' : '' }}>To Do</option>
                                                            <option value="in_progress" {{ $issue->status->value === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="in_review" {{ $issue->status->value === 'in_review' ? 'selected' : '' }}>In Review</option>
                                                            <option value="done" {{ $issue->status->value === 'done' ? 'selected' : '' }}>Done</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium border {{ $issue->status->badgeClasses() }}">
                                                        {{ $issue->status->label() }}
                                                    </span>
                                                @endif

                                                <div class="flex items-center gap-1.5">
                                                    @if($issue->story_points !== null)
                                                        <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[10px] font-bold flex items-center justify-center" title="Story Points">
                                                            {{ $issue->story_points }}
                                                        </span>
                                                    @endif
                                                    @if($issue->assignee)
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 text-[10px] font-bold shrink-0" title="{{ $issue->assignee->name }}">
                                                            {{ $issue->assignee->initials() }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-dashed border-slate-300 dark:border-slate-600 text-slate-400 text-[10px]" title="Belum ditugaskan">
                                                            ?
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="h-28 border border-dashed border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-400 text-xs">
                                            Tidak ada issue
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Complete Sprint Modal (<dialog>) -->
    @if($currentSprint && $currentSprint->status->value === 'active' && Auth::user()->canManageSprints())
        <dialog id="completeSprintModal" closedby="any" aria-labelledby="complete_sprint_title">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <h3 id="complete_sprint_title" class="text-base font-bold text-slate-900 dark:text-white">
                        Selesaikan {{ $currentSprint->name }}?
                    </h3>
                    <button type="button" command="close" commandfor="completeSprintModal"
                        class="min-h-8 min-w-8 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                        aria-label="Tutup dialog">&times;</button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                    Sprint ini memiliki <strong>{{ $currentSprint->issues->where('status', \App\Enums\IssueStatus::DONE)->count() }}</strong> issue selesai dan <strong>{{ $currentSprint->issues->where('status', '!=', \App\Enums\IssueStatus::DONE)->count() }}</strong> issue yang belum selesai.
                </p>

                <form method="POST" action="{{ route('projects.sprints.complete', [$project, $currentSprint]) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="complete_incomplete_action" class="form-label">Pindahkan issue yang belum selesai ke:</label>
                        <select id="complete_incomplete_action" name="incomplete_action" class="form-select">
                            <option value="backlog">Backlog Proyek</option>
                            @foreach($project->sprints->where('status', \App\Enums\SprintStatus::PLANNING) as $planSprint)
                                <option value="{{ $planSprint->id }}">{{ $planSprint->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="completeSprintModal" class="btn-secondary !text-xs !py-1.5 !px-3">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">
                            Selesaikan Sprint
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif

    <!-- Shared Issue Detail Dialog -->
    @include('board.partials.issue-detail-dialog', ['prefix' => 'scrum'])

    <script>
        let currentOpenIssueId = null;

        async function openBoardIssueDetail(issueId) {
            currentOpenIssueId = issueId;
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
                if (document.getElementById('detail_version_id')) {
                    document.getElementById('detail_version_id').value = issue.version_id || '';
                }
                if (document.getElementById('detail_due_date')) {
                    document.getElementById('detail_due_date').value = issue.due_date ? issue.due_date.substring(0, 10) : '';
                }

                const badge = document.getElementById('detail_type_badge');
                if (badge) {
                    badge.className = 'px-2 py-0.5 rounded text-[11px] font-semibold border ' + data.type_badge;
                    badge.innerText = issue.type;
                }
                const statusBadge = document.getElementById('detail_status_badge');
                if (statusBadge) {
                    statusBadge.className = 'px-2 py-0.5 rounded text-[11px] font-medium border ' + data.status_badge;
                    statusBadge.innerText = issue.status;
                }

                const editForm = document.getElementById('editIssueForm');
                if (editForm) editForm.action = `/projects/{{ $project->code }}/issues/${issue.id}`;
                const subForm = document.getElementById('createSubtaskForm');
                if (subForm) subForm.action = `/projects/{{ $project->code }}/issues/${issue.id}/subtasks`;

                // Render Subtasks
                const subContainer = document.getElementById('subtasks_container');
                if (subContainer) {
                    subContainer.innerHTML = '';
                    if (issue.subtasks && issue.subtasks.length > 0) {
                        issue.subtasks.forEach(st => {
                            const div = document.createElement('div');
                            div.className = 'p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-between text-xs';
                            div.innerHTML = `
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-mono font-bold text-blue-700 dark:text-blue-300">${escapeHtml(st.issue_key)}</span>
                                    <span class="truncate text-slate-800 dark:text-slate-200">${escapeHtml(st.title)}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0">${escapeHtml(st.status)}</span>
                            `;
                            subContainer.appendChild(div);
                        });
                    } else {
                        subContainer.innerHTML = '<div class="text-slate-500 dark:text-slate-400 text-xs italic">Belum ada subtask untuk issue ini.</div>';
                    }
                }

                // WhatsApp follow-up actions (PM / SM)
                const waGroup = document.getElementById('wa_action_group');
                const waMenu = document.getElementById('wa_dropdown_menu');
                if (waMenu) waMenu.classList.add('hidden');

                if (waGroup && data.whatsapp && data.whatsapp.has_phone) {
                    waGroup.classList.remove('hidden');
                    waGroup.classList.add('flex');
                    document.getElementById('wa_target_name').innerText = data.whatsapp.assignee_name || 'Assignee';
                    document.getElementById('wa_link_reminder').href = data.whatsapp.reminder_url;
                    document.getElementById('wa_link_overdue').href = data.whatsapp.overdue_url;
                    document.getElementById('wa_link_unresponsive').href = data.whatsapp.unresponsive_url;
                } else if (waGroup) {
                    waGroup.classList.add('hidden');
                    waGroup.classList.remove('flex');
                }

                renderIssueCollaboration(data, 'scrum', '{{ $project->code }}');
                openModalDialog('issueDetailModal');
            } catch (err) {
                console.error(err);
                alert('Gagal mengambil detail issue.');
            }
        }

        function toggleWaDropdown(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('wa_dropdown_menu');
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('wa_dropdown_container');
            const menu = document.getElementById('wa_dropdown_menu');
            if (menu && !menu.classList.contains('hidden') && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

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

    @include('board.partials.issue-collaboration-scripts')
</x-app-layout>
