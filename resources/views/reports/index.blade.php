<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Dashboard</span>
            </a>
            <span class="text-slate-700">|</span>
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Laporan Agile & Scrum (Modul VII)</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="bg-indigo-950/20 border border-indigo-500/20 rounded-2xl p-4 text-xs text-indigo-200">
            <strong>Laporan Metrik Kinerja Tim:</strong> Laporan membantu melacak dan menganalisis produktivitas tim di seluruh sprint dan rilis, mengidentifikasi hambatan (bottleneck), serta memproyeksikan perkiraan penyelesaian kerja secara realistis.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reports as $rep)
                @php
                    $route = route('projects.reports.' . $rep['id'], $project);
                @endphp
                <a href="{{ $route }}"
                    class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-indigo-500/50 hover:shadow-xl transition group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                {{ $rep['badge'] }}
                            </span>
                            <span class="text-slate-500 group-hover:text-indigo-400 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white group-hover:text-indigo-300 transition mb-2">
                            {{ $rep['title'] }}
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ $rep['description'] }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-semibold text-indigo-400 group-hover:underline">
                        <span>Buka Laporan</span>
                        <span>&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
