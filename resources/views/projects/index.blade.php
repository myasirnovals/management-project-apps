<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                <span>Daftar Proyek</span>
                <span class="text-xs font-normal text-slate-400 bg-slate-800 px-2.5 py-1 rounded-full">
                    Total: {{ $projects->total() }} Proyek
                </span>
            </div>
            <a href="{{ route('projects.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Buat Proyek Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $p)
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 shadow-lg transition group">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600/20 to-purple-600/20 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-base font-mono">
                                {{ $p->key }}
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $p->type === 'scrum' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                {{ $p->type === 'scrum' ? 'Agile Scrum' : 'Kanban Flow' }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 transition">
                            <a href="{{ route('projects.board', $p) }}">{{ $p->name }}</a>
                        </h3>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                            {{ $p->description ?: 'Tidak ada deskripsi proyek.' }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-800 space-y-4">
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-slate-950/60 p-2 rounded-xl border border-slate-800/80">
                                <div class="font-bold text-slate-200">{{ $p->issues_count }}</div>
                                <div class="text-[10px] text-slate-500">Issues</div>
                            </div>
                            <div class="bg-slate-950/60 p-2 rounded-xl border border-slate-800/80">
                                <div class="font-bold text-slate-200">{{ $p->sprints_count }}</div>
                                <div class="text-[10px] text-slate-500">Sprint</div>
                            </div>
                            <div class="bg-slate-950/60 p-2 rounded-xl border border-slate-800/80">
                                <div class="font-bold text-slate-200">{{ $p->members->count() }}</div>
                                <div class="text-[10px] text-slate-500">Tim</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('projects.board', $p) }}" class="flex-1 py-2 rounded-xl text-center text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                                Buka Board
                            </a>
                            <a href="{{ route('projects.backlog', $p) }}" class="flex-1 py-2 rounded-xl text-center text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                                Backlog
                            </a>
                            <a href="{{ route('projects.edit', $p) }}" title="Pengaturan Proyek" class="p-2 rounded-xl text-slate-400 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4">
            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>
