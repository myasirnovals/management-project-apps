<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xs">&larr; Semua Laporan</a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Burn Down Report</span>
        </div>
    </x-slot>

    @if($sprints->isNotEmpty())
        <x-slot name="headerActions">
            <form method="GET" action="{{ route('projects.reports.burndown', $project) }}">
                <select name="sprint_id" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($sprints as $sp)
                        <option value="{{ $sp->id }}" {{ $sprint && $sprint->id === $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ $sp->status->label() }})
                        </option>
                    @endforeach
                </select>
            </form>
        </x-slot>
    @endif

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                <div class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Komitmen Sprint</div>
                <div class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $totalStoryPoints }} <span class="text-xs font-normal text-slate-500">Story Points</span></div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                <div class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">Diselesaikan (Done)</div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $completedStoryPoints }} <span class="text-xs font-normal text-slate-500">Story Points</span></div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                <div class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">Tersisa (Remaining)</div>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $remainingStoryPoints }} <span class="text-xs font-normal text-slate-500">Story Points</span></div>
            </div>
        </div>

        <!-- Burndown Chart Container -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <span>Grafik Sisa Pekerjaan vs Waktu</span>
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $sprint ? $sprint->name : 'N/A' }})</span>
                </h3>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-blue-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Target Ideal</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-rose-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Aktual Tersisa</span>
                    </div>
                </div>
            </div>

            <!-- SVG Visual Burndown Chart -->
            <div class="p-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl">
                @php
                    $pointsCount = count($timelineData);
                    $chartWidth = 800;
                    $chartHeight = 280;
                    $padding = 40;
                    $plotWidth = $chartWidth - ($padding * 2);
                    $plotHeight = $chartHeight - ($padding * 2);
                    $maxVal = max(1, $totalStoryPoints);

                    $idealPoints = [];
                    $actualPoints = [];
                    foreach ($timelineData as $idx => $t) {
                        $x = $padding + ($idx / max(1, $pointsCount - 1)) * $plotWidth;
                        $yIdeal = ($padding + $plotHeight) - (($t['ideal'] / $maxVal) * $plotHeight);
                        $idealPoints[] = "{$x},{$yIdeal}";

                        if ($t['actual'] !== null) {
                            $yActual = ($padding + $plotHeight) - (($t['actual'] / $maxVal) * $plotHeight);
                            $actualPoints[] = "{$x},{$yActual}";
                        }
                    }
                    $idealString = implode(' ', $idealPoints);
                    $actualString = implode(' ', $actualPoints);
                @endphp

                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-64 overflow-visible">
                    <!-- Grid Lines -->
                    @for($g = 0; $g <= 4; $g++)
                        @php $yG = $padding + ($g * ($plotHeight / 4)); @endphp
                        <line x1="{{ $padding }}" y1="{{ $yG }}" x2="{{ $chartWidth - $padding }}" y2="{{ $yG }}" stroke="#cbd5e1" class="dark:stroke-slate-800" stroke-dasharray="3,3" stroke-width="0.8" />
                        <text x="{{ $padding - 8 }}" y="{{ $yG + 4 }}" fill="#94a3b8" font-size="10" text-anchor="end" class="font-mono">
                            {{ round($maxVal - ($g * ($maxVal / 4))) }}
                        </text>
                    @endfor

                    <!-- Ideal Line (Blue Dashed) -->
                    <polyline fill="none" stroke="#3b82f6" stroke-dasharray="4,4" stroke-width="2" points="{{ $idealString }}" />

                    <!-- Actual Line (Rose Solid) -->
                    @if(!empty($actualString))
                        <polyline fill="none" stroke="#f43f5e" stroke-width="2.5" points="{{ $actualString }}" />
                    @endif

                    <!-- Data dots -->
                    @foreach($timelineData as $idx => $t)
                        @php
                            $x = $padding + ($idx / max(1, $pointsCount - 1)) * $plotWidth;
                        @endphp
                        @if($t['actual'] !== null)
                            @php
                                $yActual = ($padding + $plotHeight) - (($t['actual'] / $maxVal) * $plotHeight);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $yActual }}" r="3.5" fill="#f43f5e" class="ring-2 ring-white dark:ring-slate-900" />
                        @endif
                        <text x="{{ $x }}" y="{{ $chartHeight - 12 }}" fill="#94a3b8" font-size="10" text-anchor="middle">
                            {{ $t['date'] }}
                        </text>
                    @endforeach
                </svg>
            </div>

            <!-- Interpretation Guide -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-600 dark:text-slate-300 space-y-1">
                <strong class="font-bold text-slate-900 dark:text-slate-100 block">Cara Membaca Burndown Chart:</strong>
                <p>• <strong>Garis Biru Putus-putus:</strong> Target ideal sisa tugas, memperlihatkan laju kecepatan penyelesaian pekerjaan harian yang diharapkan.</p>
                <p>• <strong>Garis Merah:</strong> Realisasi sisa tugas aktual. Bila garis merah di atas garis ideal, progres tim mengalami keterlambatan. Bila di bawah garis ideal, progres berjalan lebih cepat dari rencana.</p>
            </div>
        </div>

        <!-- Sprint Issues List Table -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Daftar Issue dalam Sprint Ini</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-2.5">Key</th>
                            <th class="px-4 py-2.5">Summary</th>
                            <th class="px-4 py-2.5">Tipe</th>
                            <th class="px-4 py-2.5">Prioritas</th>
                            <th class="px-4 py-2.5">Assignee</th>
                            <th class="px-4 py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($issues as $issue)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-2.5 font-mono font-bold text-blue-700 dark:text-blue-300">{{ $issue->issue_key }}</td>
                                <td class="px-4 py-2.5 font-medium text-slate-900 dark:text-slate-200">{{ $issue->title }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $issue->type->badgeClasses() }}">
                                        {{ $issue->type->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $issue->priority->badgeClasses() }}">
                                        {{ $issue->priority->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-600 dark:text-slate-300">
                                    {{ $issue->assignee ? $issue->assignee->name : 'Unassigned' }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">Tidak ada data issue dalam sprint ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
