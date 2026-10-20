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

    <!-- Issue Detail & Edit Modal Pop-up (Modul IV.G Gambar 4.45 - 4.48) -->
    <div id="issueDetailModal" onclick="if(event.target === this) closeIssueDetailModal()"
        class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl xl:max-w-3xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl transition-all duration-200">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <div class="flex items-center gap-2">
                        <span id="detail_key" class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20"></span>
                        <span id="detail_type_badge" class="px-2 py-0.5 rounded text-[10px] font-bold border"></span>
                    </div>

                    <!-- Right: WhatsApp Actions & Close -->
                    <div class="flex items-center gap-3">
                        <div id="wa_action_group" class="hidden items-center gap-2">
                            <div class="relative inline-block text-left" id="wa_dropdown_container">
                                <button type="button" onclick="toggleWaDropdown(event)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm shadow-emerald-600/30 transition">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                    <span>Ingatkan via WA</span>
                                    <svg class="w-3 h-3 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="wa_dropdown_menu" class="hidden absolute right-0 mt-2 w-64 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-30 p-2 text-xs space-y-1">
                                    <div class="px-2.5 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                                        Hubungi <span id="wa_target_name" class="text-white"></span>
                                    </div>
                                    <a id="wa_link_reminder" href="#" target="_blank"
                                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-slate-200 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                        <span>⏰</span>
                                        <div>
                                            <div class="font-bold">Pengingat Jatuh Tempo</div>
                                            <div class="text-[10px] text-slate-400">Tenggat waktu & update status</div>
                                        </div>
                                    </a>
                                    <a id="wa_link_overdue" href="#" target="_blank"
                                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-slate-200 hover:bg-amber-500/10 hover:text-amber-400 transition">
                                        <span>⚠️</span>
                                        <div>
                                            <div class="font-bold">Tanya Kendala / Tertunda</div>
                                            <div class="text-[10px] text-slate-400">Kenapa belum dikerjakan</div>
                                        </div>
                                    </a>
                                    <a id="wa_link_unresponsive" href="#" target="_blank"
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
                        <span id="wa_no_phone_hint" class="hidden text-[11px] text-slate-500 italic">No WA belum diisi</span>
                        <button type="button" onclick="closeIssueDetailModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-400 hover:text-white transition text-xl leading-none"
                            title="Tutup Modal">&times;</button>
                    </div>
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

                <!-- Issue Collaboration: Attachments & Comments (Deliverables & Progress) -->
                @include('board.partials.issue-collaboration', ['prefix' => 'scrum'])
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

                // WhatsApp actions setup
                const waGroup = document.getElementById('wa_action_group');
                const waHint = document.getElementById('wa_no_phone_hint');
                const waMenu = document.getElementById('wa_dropdown_menu');
                if (waMenu) waMenu.classList.add('hidden');

                if (data.whatsapp && data.whatsapp.has_phone) {
                    waGroup.classList.remove('hidden');
                    waGroup.classList.add('flex');
                    waHint.classList.add('hidden');

                    document.getElementById('wa_target_name').innerText = data.whatsapp.assignee_name || 'Assignee';
                    document.getElementById('wa_link_reminder').href = data.whatsapp.reminder_url;
                    document.getElementById('wa_link_overdue').href = data.whatsapp.overdue_url;
                    document.getElementById('wa_link_unresponsive').href = data.whatsapp.unresponsive_url;
                } else {
                    waGroup.classList.add('hidden');
                    waGroup.classList.remove('flex');
                    if (issue.assignee_id) {
                        waHint.classList.remove('hidden');
                    } else {
                        waHint.classList.add('hidden');
                    }
                }

                // Issue Collaboration: render attachments and comments
                renderIssueCollaboration(data, 'scrum', '{{ $project->code }}');

                modal.classList.remove('hidden');
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

        function closeIssueDetailModal() {
            document.getElementById('issueDetailModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeIssueDetailModal();
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