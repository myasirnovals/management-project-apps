<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Laporan Agile & Scrum</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto">
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