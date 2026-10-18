<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-400 hover:text-white">&larr; Semua Laporan</a>
                <span class="text-slate-600">/</span>
                <span class="text-sm font-bold text-white">Burn Up Report (Modul VII.B.a)</span>
            </div>

            @if($sprints->isNotEmpty())
                <form method="GET" action="{{ route('projects.reports.burnup', $project) }}">
                    <select name="sprint_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                        @foreach($sprints as $sp)
                            <option value="{{ $sp->id }}" {{ $sprint && $sprint->id === $sp->id ? 'selected' : '' }}>
                                {{ $sp->name }} ({{ $sp->status->label() }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
                <div class="text-slate-400 text-xs font-semibold uppercase">Total Cakupan (Total Scope)</div>
                <div class="text-2xl font-black text-white mt-1">{{ $totalStoryPoints }} <span class="text-xs font-normal text-slate-400">Story Points</span></div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
                <div class="text-slate-400 text-xs font-semibold uppercase">Pekerjaan Selesai (Completed Work)</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ $completedStoryPoints }} <span class="text-xs font-normal text-slate-400">Story Points</span></div>
            </div>
        </div>

        <!-- Burnup Chart Container (Modul VII Gambar 7.1) -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Grafik Kemajuan Selesai vs Total Cakupan</span>
                    <span class="text-xs font-normal text-slate-400">({{ $sprint ? $sprint->name : 'N/A' }})</span>
                </h3>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-amber-500"></span>
                        <span class="text-slate-400">Work Scope</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-emerald-500"></span>
                        <span class="text-slate-400">Completed Work</span>
                    </div>
                </div>
            </div>

            <!-- SVG Visual Burnup Chart -->
            <div class="p-4 bg-slate-950/70 border border-slate-800 rounded-2xl">
                @php
                    $pointsCount = count($timelineData);
                    $chartWidth = 800;
                    $chartHeight = 280;
                    $padding = 40;
                    $plotWidth = $chartWidth - ($padding * 2);
                    $plotHeight = $chartHeight - ($padding * 2);
                    $maxVal = max(1, $totalStoryPoints);

                    $scopePoints = [];
                    $completedPoints = [];
                    foreach ($timelineData as $idx => $t) {
                        $x = $padding + ($idx / max(1, $pointsCount - 1)) * $plotWidth;
                        $yScope = ($padding + $plotHeight) - (($t['scope'] / $maxVal) * $plotHeight);
                        $scopePoints[] = "{$x},{$yScope}";

                        if ($t['completed'] !== null) {
                            $yCompleted = ($padding + $plotHeight) - (($t['completed'] / $maxVal) * $plotHeight);
                            $completedPoints[] = "{$x},{$yCompleted}";
                        }
                    }
                    $scopeString = implode(' ', $scopePoints);
                    $completedString = implode(' ', $completedPoints);
                @endphp

                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-64 overflow-visible">
                    <!-- Grid Lines -->
                    @for($g = 0; $g <= 4; $g++)
                        @php $yG = $padding + ($g * ($plotHeight / 4)); @endphp
                        <line x1="{{ $padding }}" y1="{{ $yG }}" x2="{{ $chartWidth - $padding }}" y2="{{ $yG }}" stroke="#334155" stroke-dasharray="3,3" stroke-width="0.8" />
                        <text x="{{ $padding - 8 }}" y="{{ $yG + 4 }}" fill="#64748b" font-size="10" text-anchor="end" class="font-mono">
                            {{ round($maxVal - ($g * ($maxVal / 4))) }}
                        </text>
                    @endfor

                    <!-- Scope Line (Amber) -->
                    <polyline fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-dasharray="4,4" points="{{ $scopeString }}" />

                    <!-- Completed Work Line (Emerald Solid) -->
                    @if(!empty($completedString))
                        <polyline fill="none" stroke="#10b981" stroke-width="3" points="{{ $completedString }}" />
                    @endif

                    <!-- Data dots -->
                    @foreach($timelineData as $idx => $t)
                        @php
                            $x = $padding + ($idx / max(1, $pointsCount - 1)) * $plotWidth;
                        @endphp
                        @if($t['completed'] !== null)
                            @php
                                $yCompleted = ($padding + $plotHeight) - (($t['completed'] / $maxVal) * $plotHeight);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $yCompleted }}" r="4" fill="#10b981" class="ring-2 ring-slate-900" />
                        @endif
                        <text x="{{ $x }}" y="{{ $chartHeight - 12 }}" fill="#64748b" font-size="10" text-anchor="middle">
                            {{ $t['date'] }}
                        </text>
                    @endforeach
                </svg>
            </div>

            <div class="p-4 bg-slate-950/40 border border-slate-800 rounded-2xl text-xs text-slate-300 space-y-1.5">
                <strong class="font-bold text-slate-100 block">Penjelasan Burn Up Chart (Buku Modul Manajemen Proyek):</strong>
                <p>• <strong>Garis Oranye (Work Scope):</strong> Menunjukkan total cakupan sasaran sprint. Jika garis ini naik di tengah sprint, terjadi penambahan cakupan baru (*scope creep*).</p>
                <p>• <strong>Garis Hijau (Completed Work):</strong> Menunjukkan akumulasi pekerjaan yang telah selesai. Pada akhir sprint yang ideal, garis hijau akan bertemu dengan garis oranye.</p>
            </div>
        </div>
    </div>
</x-app-layout>
