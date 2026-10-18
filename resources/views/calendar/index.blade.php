<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Kalender Proyek</span>
        </div>
    </x-slot>

    @php
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthLabel = $monthNames[$monthDate->month] . ' ' . $monthDate->year;
    @endphp

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Top Toolbar: Month Navigator & Project Filter -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <!-- Month Navigator -->
            <div class="flex items-center gap-3">
                <a href="{{ route('calendar.index', ['month' => $prevMonth, 'project_id' => $selectedProjectId]) }}"
                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                    title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="flex items-center gap-2">
                    <span class="text-base font-bold text-white tracking-tight min-w-[150px] text-center">
                        {{ $monthLabel }}
                    </span>
                    <a href="{{ route('calendar.index', ['month' => now()->format('Y-m'), 'project_id' => $selectedProjectId]) }}"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                        Bulan Ini
                    </a>
                </div>

                <a href="{{ route('calendar.index', ['month' => $nextMonth, 'project_id' => $selectedProjectId]) }}"
                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                    title="Bulan Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Project Filter -->
            <form method="GET" action="{{ route('calendar.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <label for="projectFilter" class="text-xs font-medium text-slate-400 shrink-0">Filter Proyek:</label>
                <select id="projectFilter" name="project_id" onchange="this.form.submit()"
                    class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-56">
                    <option value="">Semua Proyek ({{ $projects->count() }})</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedProjectId === (string)$p->id ? 'selected' : '' }}>
                            [{{ $p->key }}] {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Calendar Grid (3 Columns on Large Screens) -->
            <div class="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                <!-- Day of week header -->
                <div class="grid grid-cols-7 bg-slate-950/80 border-b border-slate-800 text-center py-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div class="text-indigo-400">Sab</div>
                    <div class="text-rose-400">Min</div>
                </div>

                <!-- Days cells grid -->
                <div class="grid grid-cols-7 divide-x divide-y divide-slate-800/80 bg-slate-900/60">
                    @foreach($period as $date)
                        @php
                            $dateStr = $date->toDateString();
                            $isCurrentMonth = $date->month === $monthDate->month;
                            $isToday = $date->isToday();
                            $dayEvents = $eventsByDate[$dateStr] ?? ['sprints' => [], 'issues' => []];
                            $sprintsOnDay = $dayEvents['sprints'];
                            $issuesOnDay = $dayEvents['issues'];
                        @endphp
                        <div class="min-h-[110px] p-2 flex flex-col justify-between transition {{ $isCurrentMonth ? 'bg-slate-900/40' : 'bg-slate-950/50 opacity-40' }} {{ $isToday ? 'ring-1 ring-inset ring-indigo-500/50 bg-indigo-950/10' : '' }}">
                            <!-- Date Number & Today badge -->
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold {{ $isToday ? 'w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center font-mono shadow-sm' : ($isCurrentMonth ? 'text-slate-200' : 'text-slate-600') }}">
                                    {{ $date->day }}
                                </span>
                                @if($isToday)
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20">Hari Ini</span>
                                @endif
                            </div>

                            <!-- Events List for the day -->
                            <div class="space-y-1 overflow-y-auto max-h-[85px] pr-0.5 scrollbar-thin">
                                <!-- Sprints active -->
                                @foreach($sprintsOnDay as $sprint)
                                    <a href="{{ route('projects.board', $sprint->project) }}"
                                        title="Sprint: {{ $sprint->name }} (Proyek: {{ $sprint->project->name }})"
                                        class="block px-1.5 py-1 rounded-md text-[10px] font-semibold bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-200 truncate transition">
                                        <span class="text-indigo-400 font-mono">[{{ $sprint->project->key }}]</span> {{ $sprint->name }}
                                    </a>
                                @endforeach

                                <!-- Issues due -->
                                @foreach($issuesOnDay as $issue)
                                    <a href="{{ route('projects.board', $issue->project) }}"
                                        title="Tenggat Issue: {{ $issue->issue_key }} - {{ $issue->title }}"
                                        class="block px-1.5 py-1 rounded-md text-[10px] font-medium bg-amber-950/70 hover:bg-amber-900/80 border border-amber-500/30 text-amber-200 truncate transition">
                                        <span class="text-amber-400 font-mono">⏰ {{ $issue->issue_key }}</span> {{ $issue->title }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="mt-1"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar / Widgets (1 Column) -->
            <div class="space-y-5">
                <!-- Legend Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Legenda Kalender</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded bg-indigo-600/30 border border-indigo-500/50 shrink-0"></span>
                            <span class="text-slate-300">Sprint Iterasi Berjalan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded bg-amber-600/30 border border-amber-500/50 shrink-0"></span>
                            <span class="text-slate-300">Tenggat Waktu Tugas (Due Date)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 ring-2 ring-indigo-500/20 shrink-0"></span>
                            <span class="text-slate-300">Penanda Hari Ini</span>
                        </div>
                    </div>
                </div>

                <!-- Active Sprints Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Sprint Aktif</h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 bg-slate-950 px-2 py-0.5 rounded-full border border-slate-800">
                            {{ $activeSprints->count() }}
                        </span>
                    </div>

                    @if($activeSprints->isEmpty())
                        <p class="text-xs text-slate-500 py-3 text-center">Tidak ada sprint yang sedang berjalan saat ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($activeSprints as $asp)
                                <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold text-white">{{ $asp->name }}</span>
                                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            {{ $asp->project->key }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $asp->project->name }}</p>
                                    @if($asp->start_date && $asp->end_date)
                                        <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between">
                                            <span>{{ $asp->start_date->format('d M') }} - {{ $asp->end_date->format('d M Y') }}</span>
                                            <a href="{{ route('projects.board', $asp->project) }}" class="text-indigo-400 hover:underline font-semibold">Buka Board &rarr;</a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Upcoming Deadlines Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-amber-400">⏰</span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Tenggat Terdekat</h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 bg-slate-950 px-2 py-0.5 rounded-full border border-slate-800">
                            {{ $upcomingIssues->count() }}
                        </span>
                    </div>

                    @if($upcomingIssues->isEmpty())
                        <p class="text-xs text-slate-500 py-3 text-center">Tidak ada tenggat tugas dalam waktu dekat.</p>
                    @else
                        <div class="space-y-2.5">
                            @foreach($upcomingIssues as $uIssue)
                                <a href="{{ route('projects.board', $uIssue->project) }}" class="block bg-slate-950/60 hover:bg-slate-800/50 border border-slate-800/80 rounded-xl p-2.5 transition group">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-mono text-xs font-bold text-amber-400">{{ $uIssue->issue_key }}</span>
                                        <span class="text-[10px] text-slate-500">{{ Carbon\Carbon::parse($uIssue->due_date)->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-200 group-hover:text-indigo-400 transition truncate">{{ $uIssue->title }}</p>
                                    <span class="text-[10px] text-slate-500 block mt-1">{{ $uIssue->project->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
