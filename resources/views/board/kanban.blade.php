<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Kanban Board</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('projects.backlog', $project) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
            Buka Backlog
        </a>
    </x-slot>

    <div class="space-y-4 max-w-full">
        <!-- Search bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 flex items-center justify-between gap-3 text-xs">
            <form method="GET" action="{{ route('projects.kanban', $project) }}" class="relative w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari issue atau kata kunci..."
                    class="w-full pl-8 pr-3 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
            <div class="text-slate-400 text-xs">
                Total {{ $issues->count() }} Issues di Kanban Board
            </div>
        </div>

        <!-- 4 Columns Kanban Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">
            @foreach($columns as $colKey => $col)
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-4 flex flex-col min-h-[500px]">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $colKey === 'to_do' ? 'bg-slate-400' : ($colKey === 'in_progress' ? 'bg-blue-400' : ($colKey === 'in_review' ? 'bg-amber-400' : 'bg-emerald-400')) }}"></span>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-200">{{ $col['title'] }}</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 text-slate-300">
                        {{ $col['issues']->count() }}
                    </span>
                </div>

                <div class="space-y-3 flex-1 overflow-y-auto"
                    data-status="{{ $col['status']->value }}"
                    ondragover="handleDragOver(event)"
                    ondrop="handleDrop(event, this)">
                    @forelse($col['issues'] as $issue)
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4 shadow-sm hover:border-indigo-500/50 hover:shadow-md transition cursor-pointer group"
                        draggable="true"
                        ondragstart="handleDragStart(event, this)"
                        data-issue-id="{{ $issue->id }}"
                        onclick="openKanbanIssueDetail(this.dataset.issueId)">

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

                        <h4 class="text-xs font-semibold text-slate-200 line-clamp-2 leading-snug mb-2 group-hover:text-white transition">
                            {{ $issue->title }}
                        </h4>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-800/60" onclick="event.stopPropagation()">
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

                            @if($issue->assignee)
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center ring-1 ring-slate-700" title="{{ $issue->assignee->name }}">
                                {{ $issue->assignee->initials() }}
                            </div>
                            @endif
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

    <!-- Edit Issue Drawer for Kanban -->
    <div id="kanbanDetailModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-end p-0">
        <div class="bg-slate-900 border-l border-slate-800 w-full max-w-xl h-full overflow-y-auto p-6 sm:p-8 flex flex-col justify-between shadow-2xl">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <span id="kanban_detail_key" class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20"></span>
                    <button type="button" onclick="closeKanbanDetailModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <form id="kanbanEditForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Judul / Summary</label>
                        <input type="text" id="kanban_title" name="title" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Deskripsi</label>
                        <textarea id="kanban_description" name="description" rows="3" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Status</label>
                            <select id="kanban_status" name="status" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                                <option value="to_do">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="in_review">In Review</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Prioritas</label>
                            <select id="kanban_priority" name="priority" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                                <option value="highest">Highest</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                                <option value="lowest">Lowest</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Tipe</label>
                            <select id="kanban_type" name="type" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                                <option value="story">Story</option>
                                <option value="task">Task</option>
                                <option value="bug">Bug</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Assignee</label>
                            <select id="kanban_assignee" name="assignee_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-800">
                        <button type="button" onclick="closeKanbanDetailModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function openKanbanIssueDetail(id) {
            const modal = document.getElementById('kanbanDetailModal');
            const res = await fetch(`/projects/{{ $project->code }}/issues/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            const issue = data.issue;

            document.getElementById('kanban_detail_key').innerText = issue.issue_key;
            document.getElementById('kanban_title').value = issue.title;
            document.getElementById('kanban_description').value = issue.description || '';
            document.getElementById('kanban_status').value = issue.status;
            document.getElementById('kanban_priority').value = issue.priority;
            document.getElementById('kanban_type').value = issue.type;
            document.getElementById('kanban_assignee').value = issue.assignee_id || '';
            document.getElementById('kanbanEditForm').action = `/projects/{{ $project->code }}/issues/${issue.id}`;

            modal.classList.remove('hidden');
        }

        function closeKanbanDetailModal() {
            document.getElementById('kanbanDetailModal').classList.add('hidden');
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