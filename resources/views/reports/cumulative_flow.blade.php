<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xs">&larr; Semua Laporan</a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Cumulative Flow Diagram (CFD)</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Function banner -->
        <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 rounded-2xl p-4 text-xs text-blue-900 dark:text-blue-200">
            <strong>Fungsi CFD:</strong> Cumulative Flow Diagram memperlihatkan distribusi status item pekerjaan untuk mendeteksi potensi penumpukan pekerjaan (bottleneck).
        </div>

        @php
            $totalIssues = max(1, array_sum($statusCounts));
            $pctToDo = round(($statusCounts['to_do'] / $totalIssues) * 100);
            $pctInProgress = round(($statusCounts['in_progress'] / $totalIssues) * 100);
            $pctInReview = round(($statusCounts['in_review'] / $totalIssues) * 100);
            $pctDone = round(($statusCounts['done'] / $totalIssues) * 100);
        @endphp

        <!-- Proportional Stacked Flow Bar -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Distribusi Status Alur Kerja Saat Ini</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Total: <strong>{{ array_sum($statusCounts) }}</strong> Issues</span>
            </div>

            <!-- Visual Flow Bar -->
            <div>
                <div class="w-full h-7 rounded-xl overflow-hidden flex bg-slate-100 dark:bg-slate-950">
                    <div @style(["width: {$pctToDo}%"]) class="bg-amber-500 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="To Do: {{ $statusCounts['to_do'] }} issues">
                        @if($pctToDo > 8){{ $pctToDo }}%@endif
                    </div>
                    <div @style(["width: {$pctInProgress}%"]) class="bg-sky-500 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="In Progress: {{ $statusCounts['in_progress'] }} issues">
                        @if($pctInProgress > 8){{ $pctInProgress }}%@endif
                    </div>
                    <div @style(["width: {$pctInReview}%"]) class="bg-indigo-500 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="In Review: {{ $statusCounts['in_review'] }} issues">
                        @if($pctInReview > 8){{ $pctInReview }}%@endif
                    </div>
                    <div @style(["width: {$pctDone}%"]) class="bg-emerald-600 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="Done: {{ $statusCounts['done'] }} issues">
                        @if($pctDone > 8){{ $pctDone }}%@endif
                    </div>
                </div>

                <!-- Legend & Count Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-amber-200 dark:border-amber-500/20">
                        <div class="flex items-center gap-1.5 text-amber-700 dark:text-amber-400 text-xs font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>To Do</span>
                        </div>
                        <div class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">{{ $statusCounts['to_do'] }} <span class="text-xs font-normal text-slate-500">issues</span></div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-sky-200 dark:border-sky-500/20">
                        <div class="flex items-center gap-1.5 text-sky-700 dark:text-sky-400 text-xs font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            <span>In Progress</span>
                        </div>
                        <div class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">{{ $statusCounts['in_progress'] }} <span class="text-xs font-normal text-slate-500">issues</span></div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-indigo-200 dark:border-indigo-500/20">
                        <div class="flex items-center gap-1.5 text-indigo-700 dark:text-indigo-400 text-xs font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <span>In Review</span>
                        </div>
                        <div class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">{{ $statusCounts['in_review'] }} <span class="text-xs font-normal text-slate-500">issues</span></div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-emerald-200 dark:border-emerald-500/20">
                        <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span>Done</span>
                        </div>
                        <div class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">{{ $statusCounts['done'] }} <span class="text-xs font-normal text-slate-500">issues</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
