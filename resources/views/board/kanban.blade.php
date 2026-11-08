<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                {{ $project->key }}
            </span>
            <span class="text-base font-bold text-slate-900 dark:text-white">Kanban Board</span>
        </div>
    </x-slot>

    <div class="space-y-4 max-w-full">
        <!-- Toolbar: Search & Info -->
        <div class="app-card p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
            <form method="GET" action="{{ route('projects.kanban', $project) }}" class="relative w-64 sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau judul issue..."
                    class="form-input !text-xs !py-1.5 !pl-8 !pr-3">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>

            <div class="text-slate-600 dark:text-slate-400 text-xs">
                Total <strong class="text-slate-900 dark:text-white">{{ $issues->count() }}</strong> issue pada alur kerja Kanban
            </div>
        </div>

        <!-- 4 Columns Kanban Grid -->
        <div class="board-scroller">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start min-w-[72rem] xl:min-w-0">
                @foreach($columns as $colKey => $col)
                    <div class="app-card p-3.5 flex flex-col min-h-96">
                        <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $colKey === 'to_do' ? 'bg-slate-400' : ($colKey === 'in_progress' ? 'bg-blue-500' : ($colKey === 'in_review' ? 'bg-amber-500' : 'bg-emerald-500')) }}"></span>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                    {{ $col['title'] }}
                                </h3>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $col['issues']->count() }}
                            </span>
                        </div>

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

                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                                        {{ $issue->title }}
                                    </h4>

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
    </div>

    <!-- Shared Issue Detail Dialog -->
    @include('board.partials.issue-detail-dialog', ['prefix' => 'kanban'])

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

                renderIssueCollaboration(data, 'kanban', '{{ $project->code }}');
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
