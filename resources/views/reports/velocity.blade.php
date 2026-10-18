<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.reports.index', $project) }}" class="text-slate-400 hover:text-white">&larr; Semua Laporan</a>
            <span class="text-slate-600">/</span>
            <span class="text-sm font-bold text-white">Velocity Report (Modul VII.B.e)</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <!-- Average Velocity Stat Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Kecepatan Tim (Average Velocity)</span>
                <div class="text-3xl font-black text-emerald-400 mt-1">
                    {{ $avgVelocity }} <span class="text-sm font-semibold text-slate-300">Story Points / Sprint</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Data ini digunakan Scrum Master untuk memperkirakan kapasitas kerja yang realistis dalam sprint planning mendatang.
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-slate-600"></span>
                    <span class="text-slate-300">Komitmen Awal (Estimated)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-emerald-500"></span>
                    <span class="text-slate-300">Selesai (Completed)</span>
                </div>
            </div>
        </div>

        <!-- Velocity Bar Chart -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <h3 class="text-sm font-bold text-white">Grafik Komitmen vs Hasil Selesai (7 Sprint Terakhir)</h3>

            @if(empty($velocityData))
                <div class="py-12 text-center text-slate-500 text-xs">
                    Belum ada data sprint yang tercatat. Buat dan selesaikan sprint untuk melihat statistik velocity.
                </div>
            @else
                <div class="space-y-6">
                    @foreach($velocityData as $v)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-200">{{ $v['name'] }} ({{ $v['status'] }})</span>
                                <div class="flex items-center gap-4">
                                    <span class="text-slate-400">Komitmen: <strong class="text-white">{{ $v['commitment'] }} SP</strong></span>
                                    <span class="text-emerald-400">Selesai: <strong class="text-emerald-300">{{ $v['completed'] }} SP</strong></span>
                                </div>
                            </div>

                            <!-- Dual Bar Representation -->
                            <div class="space-y-1">
                                <!-- Commitment Bar -->
                                <div class="w-full h-3 rounded-full bg-slate-950 overflow-hidden flex">
                                    <div class="bg-slate-600 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $v['commitment'] * 5) }}%"></div>
                                </div>
                                <!-- Completed Bar -->
                                <div class="w-full h-3 rounded-full bg-slate-950 overflow-hidden flex">
                                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $v['completed'] * 5) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
