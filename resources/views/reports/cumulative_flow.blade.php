<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-400 hover:text-white">&larr; Semua Laporan</a>
            <span class="text-slate-600">/</span>
            <span class="text-sm font-bold text-white">Cumulative Flow Diagram (CFD) (Modul VII.B.d)</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Handbook description banner -->
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
            <strong>Fungsi CFD:</strong> Cumulative Flow Diagram memperlihatkan berbagai status item pekerjaan untuk mendeteksi <em>bottleneck</em> (hambatan). Bila area warna pada bagan melebar secara vertikal dari waktu ke waktu, kolom tersebut mengindikasikan adanya penumpukan pekerjaan.
        </div>

        @php
            $totalIssues = max(1, array_sum($statusCounts));
            $pctToDo = round(($statusCounts['to_do'] / $totalIssues) * 100);
            $pctInProgress = round(($statusCounts['in_progress'] / $totalIssues) * 100);
            $pctInReview = round(($statusCounts['in_review'] / $totalIssues) * 100);
            $pctDone = round(($statusCounts['done'] / $totalIssues) * 100);
        @endphp

        <!-- Proportional Stacked Flow Bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white">Distribusi Status Alur Kerja Saat Ini</h3>
                <span class="text-xs text-slate-400">Total: <strong>{{ array_sum($statusCounts) }}</strong> Issues</span>
            </div>

            <!-- Visual Flow Bar (Orange for To Do, Teal for In Progress, Amber for Review, Purple/Green for Done - Gambar 7.4) -->
            <div>
                <div class="w-full h-8 rounded-2xl overflow-hidden flex shadow-inner bg-slate-950">
                    <div @style(["width: {$pctToDo}%"]) class="bg-amber-500 h-full flex items-center justify-center text-[10px] font-bold text-black font-mono transition-all duration-500" title="To Do: {{ $statusCounts['to_do'] }} issues">
                        @if($pctToDo > 8){{ $pctToDo }}%@endif
                    </div>
                    <div @style(["width: {$pctInProgress}%"]) class="bg-teal-500 h-full flex items-center justify-center text-[10px] font-bold text-black font-mono transition-all duration-500" title="In Progress: {{ $statusCounts['in_progress'] }} issues">
                        @if($pctInProgress > 8){{ $pctInProgress }}%@endif
                    </div>
                    <div @style(["width: {$pctInReview}%"]) class="bg-indigo-500 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="In Review: {{ $statusCounts['in_review'] }} issues">
                        @if($pctInReview > 8){{ $pctInReview }}%@endif
                    </div>
                    <div @style(["width: {$pctDone}%"]) class="bg-purple-600 h-full flex items-center justify-center text-[10px] font-bold text-white font-mono transition-all duration-500" title="Done: {{ $statusCounts['done'] }} issues">
                        @if($pctDone > 8){{ $pctDone }}%@endif
                    </div>
                </div>

                <!-- Legend & Count Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    <div class="p-4 bg-slate-950/60 rounded-2xl border border-amber-500/30">
                        <div class="flex items-center gap-2 text-amber-400 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span>To Do (Orange)</span>
                        </div>
                        <div class="text-2xl font-black text-white mt-2">{{ $statusCounts['to_do'] }} <span class="text-xs font-normal text-slate-400">issues</span></div>
                    </div>

                    <div class="p-4 bg-slate-950/60 rounded-2xl border border-teal-500/30">
                        <div class="flex items-center gap-2 text-teal-400 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                            <span>In Progress (Tosca)</span>
                        </div>
                        <div class="text-2xl font-black text-white mt-2">{{ $statusCounts['in_progress'] }} <span class="text-xs font-normal text-slate-400">issues</span></div>
                    </div>

                    <div class="p-4 bg-slate-950/60 rounded-2xl border border-indigo-500/30">
                        <div class="flex items-center gap-2 text-indigo-400 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                            <span>In Review</span>
                        </div>
                        <div class="text-2xl font-black text-white mt-2">{{ $statusCounts['in_review'] }} <span class="text-xs font-normal text-slate-400">issues</span></div>
                    </div>

                    <div class="p-4 bg-slate-950/60 rounded-2xl border border-purple-500/30">
                        <div class="flex items-center gap-2 text-purple-400 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                            <span>Done (Ungu)</span>
                        </div>
                        <div class="text-2xl font-black text-white mt-2">{{ $statusCounts['done'] }} <span class="text-xs font-normal text-slate-400">issues</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
