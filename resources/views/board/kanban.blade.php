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
                                @if($issue->pullRequests && $issue->pullRequests->isNotEmpty())
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20" title="{{ $issue->pullRequests->count() }} Pull Request terhubung">
                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                                    </svg>
                                    <span>PR #{{ $issue->pullRequests->first()->pr_number }}</span>
                                </span>
                                @endif
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
                            <div class="flex items-center gap-1.5">
                                @if($issue->assignee->hasWhatsApp())
                                <a href="{{ $issue->assignee->whatsappUrl(app(\App\Services\WhatsAppService::class)->buildMessage($issue->assignee, $issue, 'reminder')) }}"
                                    target="_blank" onclick="event.stopPropagation()"
                                    class="w-5 h-5 rounded-full bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-white flex items-center justify-center transition border border-emerald-500/30"
                                    title="Kirim Pengingat WhatsApp ke {{ $issue->assignee->name }}">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                </a>
                                @endif
                                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center ring-1 ring-slate-700" title="{{ $issue->assignee->name }}">
                                    {{ $issue->assignee->initials() }}
                                </div>
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

    <!-- Edit Issue Pop-up Modal for Kanban -->
    <div id="kanbanDetailModal" onclick="if(event.target === this) closeKanbanDetailModal()"
        class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl xl:max-w-3xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl transition-all duration-200">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <span id="kanban_detail_key" class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20"></span>
                    <div class="flex items-center gap-3">
                        <div id="kanban_wa_group" class="hidden items-center gap-2">
                            <div class="relative inline-block text-left" id="kanban_wa_container">
                                <button type="button" onclick="toggleKanbanWaDropdown(event)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm shadow-emerald-600/30 transition">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                    <span>Ingatkan via WA</span>
                                    <svg class="w-3 h-3 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="kanban_wa_menu" class="hidden absolute right-0 mt-2 w-64 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-30 p-2 text-xs space-y-1">
                                    <div class="px-2.5 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                                        Hubungi <span id="kanban_wa_target_name" class="text-white"></span>
                                    </div>
                                    <a id="kanban_wa_reminder" href="#" target="_blank"
                                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-slate-200 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                        <span>⏰</span>
                                        <div>
                                            <div class="font-bold">Pengingat Jatuh Tempo</div>
                                            <div class="text-[10px] text-slate-400">Tenggat waktu & status</div>
                                        </div>
                                    </a>
                                    <a id="kanban_wa_overdue" href="#" target="_blank"
                                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-slate-200 hover:bg-amber-500/10 hover:text-amber-400 transition">
                                        <span>⚠️</span>
                                        <div>
                                            <div class="font-bold">Tanya Kendala / Tertunda</div>
                                            <div class="text-[10px] text-slate-400">Kenapa belum dikerjakan</div>
                                        </div>
                                    </a>
                                    <a id="kanban_wa_unresponsive" href="#" target="_blank"
                                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-slate-200 hover:bg-rose-500/10 hover:text-rose-400 transition">
                                        <span>❓</span>
                                        <div>
                                            <div class="font-bold">Follow-up Respon Tim</div>
                                            <div class="text-[10px] text-slate-400">Belum menjawab pesan/update</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="closeKanbanDetailModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-400 hover:text-white transition text-xl leading-none"
                            title="Tutup Modal">&times;</button>
                    </div>
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

                <!-- Issue Collaboration: Attachments & Comments (Deliverables & Progress) -->
                @include('board.partials.issue-collaboration', ['prefix' => 'kanban'])
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

            // WhatsApp actions setup
            const waGroup = document.getElementById('kanban_wa_group');
            const waMenu = document.getElementById('kanban_wa_menu');
            if (waMenu) waMenu.classList.add('hidden');

            if (data.whatsapp && data.whatsapp.has_phone) {
                waGroup.classList.remove('hidden');
                waGroup.classList.add('flex');

                document.getElementById('kanban_wa_target_name').innerText = data.whatsapp.assignee_name || 'Assignee';
                document.getElementById('kanban_wa_reminder').href = data.whatsapp.reminder_url;
                document.getElementById('kanban_wa_overdue').href = data.whatsapp.overdue_url;
                document.getElementById('kanban_wa_unresponsive').href = data.whatsapp.unresponsive_url;
            } else {
                waGroup.classList.add('hidden');
                waGroup.classList.remove('flex');
            }

            // Issue Collaboration: render attachments and comments
            renderIssueCollaboration(data, 'kanban', '{{ $project->code }}');

            modal.classList.remove('hidden');
        }

        function toggleKanbanWaDropdown(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('kanban_wa_menu');
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('kanban_wa_container');
            const menu = document.getElementById('kanban_wa_menu');
            if (menu && !menu.classList.contains('hidden') && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        function closeKanbanDetailModal() {
            document.getElementById('kanbanDetailModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeKanbanDetailModal();
            }
        });

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