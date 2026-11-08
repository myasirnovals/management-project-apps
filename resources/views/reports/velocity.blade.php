<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xs">&larr; Semua Laporan</a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Velocity Report</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Average Velocity Stat Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rata-rata Kecepatan Tim (Average Velocity)</span>
                <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                    {{ $avgVelocity }} <span class="text-xs font-normal text-slate-500">Story Points / Sprint</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Data ini digunakan Scrum Master untuk memperkirakan kapasitas kerja yang realistis dalam perencanaan sprint mendatang.
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-slate-300 dark:bg-slate-700"></span>
                    <span class="text-slate-600 dark:text-slate-400">Komitmen Awal</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-emerald-500"></span>
                    <span class="text-slate-600 dark:text-slate-400">Selesai (Completed)</span>
                </div>
            </div>
        </div>

        <!-- Velocity Bar Chart -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Grafik Komitmen vs Hasil Selesai (7 Sprint Terakhir)</h3>

            @if(empty($velocityData))
                <div class="py-10 text-center text-slate-500 text-xs border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                    Belum ada data sprint yang tercatat. Selesaikan sprint untuk melihat statistik velocity tim.
                </div>
            @else
                <div class="space-y-5">
                    @foreach($velocityData as $v)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $v['name'] }} ({{ $v['status'] }})</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-slate-500">Komitmen: <strong class="text-slate-800 dark:text-slate-200">{{ $v['commitment'] }} SP</strong></span>
                                    <span class="text-emerald-600 dark:text-emerald-400">Selesai: <strong class="text-emerald-700 dark:text-emerald-300">{{ $v['completed'] }} SP</strong></span>
                                </div>
                            </div>

                            <!-- Dual Bar Representation -->
                            <div class="space-y-1">
                                <!-- Commitment Bar -->
                                <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-950 overflow-hidden flex">
                                    <div class="bg-slate-400 dark:bg-slate-700 h-full rounded-full transition-all duration-500" @style(['width: ' . min(100, $v['commitment'] * 5) . '%'])></div>
                                </div>
                                <!-- Completed Bar -->
                                <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-950 overflow-hidden flex">
                                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" @style(['width: ' . min(100, $v['completed'] * 5) . '%'])></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
