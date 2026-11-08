<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-blue-700 dark:text-blue-300 font-bold bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Laporan Metrik Agile &amp; Scrum</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($reports as $rep)
            @php
                $route = route('projects.reports.' . $rep['id'], $project);
            @endphp
            <a href="{{ $route }}"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex flex-col justify-between hover:border-blue-500/50 hover:shadow-md transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            {{ $rep['badge'] }}
                        </span>
                        <span class="text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">&rarr;</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition mb-1.5">
                        {{ $rep['title'] }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ $rep['description'] }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-semibold text-blue-600 dark:text-blue-400">
                    <span>Lihat Laporan</span>
                    <span>&rarr;</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
