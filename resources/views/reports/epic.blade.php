<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xs">&larr; Semua Laporan</a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Epic Report</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 rounded-2xl p-4 text-xs text-blue-900 dark:text-blue-200">
            <strong>Epic Report:</strong> Rangkuman inisiatif Epic, memperlihatkan kemajuan pengerjaan story dan task yang berada di bawah setiap payung fitur besar.
        </div>

        <div class="space-y-4">
            @forelse($epics as $epic)
                @php
                    $total = $epic->epicIssues->count();
                    $done = $epic->epicIssues->where('status', \App\Enums\IssueStatus::DONE)->count();
                    $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center font-bold text-xs">
                                EPC
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-blue-700 dark:text-blue-300 font-bold text-xs">{{ $epic->issue_key }}</span>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $epic->title }}</h3>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $epic->description ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $done }} / {{ $total }}</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 block font-medium">Selesai ({{ $pct }}%)</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-950 overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-500" @style(["width: {$pct}%"])></div>
                    </div>

                    <!-- Child issues of this epic -->
                    <div class="space-y-2 mt-3">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Issues di Bawah Epic Ini:</h4>
                        <div class="space-y-1.5">
                            @forelse($epic->epicIssues as $child)
                                <div class="p-2.5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-bold text-slate-600 dark:text-slate-400 shrink-0">{{ $child->issue_key }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border shrink-0 {{ $child->type->badgeClasses() }}">{{ $child->type->label() }}</span>
                                        <span class="text-slate-800 dark:text-slate-200 truncate">{{ $child->title }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border shrink-0 {{ $child->status->badgeClasses() }}">{{ $child->status->label() }}</span>
                                </div>
                            @empty
                                <div class="text-slate-400 text-xs italic py-1">
                                    Belum ada story atau task yang dikaitkan ke Epic ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-500 text-xs">
                    Belum ada Epic yang dibuat. Anda dapat membuatnya di menu Backlog.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
