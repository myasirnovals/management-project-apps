<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-400 hover:text-white">&larr; Semua Laporan</a>
            <span class="text-slate-600">/</span>
            <span class="text-sm font-bold text-white">Epic Report (Modul VII.B.f)</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
            <strong>Epic Report:</strong> Rangkuman banyak issues yang termasuk dalam setiap inisiatif Epic, memvisualisasikan proporsi pekerjaan yang telah selesai sesuai estimasi maupun yang belum terselesaikan.
        </div>

        <div class="space-y-6">
            @forelse($epics as $epic)
                @php
                    $total = $epic->epicIssues->count();
                    $done = $epic->epicIssues->where('status', \App\Enums\IssueStatus::DONE)->count();
                    $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                @endphp
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30 flex items-center justify-center font-bold text-xs">
                                ⚡
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-purple-400 font-bold text-xs">{{ $epic->issue_key }}</span>
                                    <h3 class="text-base font-bold text-white">{{ $epic->title }}</h3>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $epic->description ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-sm font-black text-purple-400">{{ $done }} / {{ $total }}</span>
                            <span class="text-xs text-slate-400 block font-medium">Issues Selesai ({{ $pct }}%)</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-2 rounded-full bg-slate-950 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-500" @style(["width: {$pct}%"])></div>
                    </div>

                    <!-- Child issues of this epic -->
                    <div class="space-y-2 mt-4">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Issues di Bawah Epic Ini:</h4>
                        <div class="space-y-1.5">
                            @forelse($epic->epicIssues as $child)
                                <div class="p-2.5 bg-slate-950/40 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-400">{{ $child->issue_key }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border {{ $child->type->badgeClasses() }}">{{ $child->type->label() }}</span>
                                        <span class="text-slate-200">{{ $child->title }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $child->status->badgeClasses() }}">{{ $child->status->label() }}</span>
                                </div>
                            @empty
                                <div class="text-slate-500 text-xs italic py-2">
                                    Belum ada story atau task yang dikaitkan ke Epic ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-slate-900 border border-slate-800 rounded-3xl text-slate-500 text-xs">
                    Belum ada Epic yang dibuat. Anda dapat membuatnya di menu Backlog.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
