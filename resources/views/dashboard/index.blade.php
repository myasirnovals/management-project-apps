<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Dashboard Workspace</span>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Welcome Banner Adaptif Sesuai Peran -->
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 p-8 shadow-xl">
            <div class="relative z-10 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ Auth::user()->role->badgeClass() }}">
                        {{ Auth::user()->role->label() }}
                    </span>
                    @if(Auth::user()->job_title)
                    <span class="text-xs text-slate-400 font-medium">
                        ({{ Auth::user()->job_title }})
                    </span>
                    @endif
                </div>

                <h2 class="text-2xl font-black text-white tracking-tight sm:text-3xl">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                    @if(Auth::user()->isProjectManager())
                        Kelola seluruh siklus proyek, iterasi sprint, anggota tim, serta pengaturan integrasi workspace Anda dari sini.
                    @elseif(Auth::user()->isProductOwner())
                        Pantau prioritas backlog, susun user story, kriteria kebutuhan bisnis, dan rencanakan milestone rilis proyek.
                    @else
                        Pantau issue dan tugas teknis yang ditugaskan kepada Anda, perbarui progres di papan Scrum atau Kanban, serta laporkan kendala.
                    @endif
                </p>

                @if(Auth::user()->isProjectManager())
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Proyek Baru</span>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Proyek</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-white">{{ $stats['total_projects'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Proyek yang dapat Anda akses</div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sprint Berjalan</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-emerald-400">{{ $stats['active_sprints'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Iterasi sprint aktif</div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tugas Saya</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['my_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Belum selesai (In Progress / To Do)</div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Selesai (Done)</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['completed_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue berhasil diselesaikan</div>
            </div>
        </div>

        <!-- Tugas yang Ditugaskan ke Saya (Khususnya sangat penting untuk Programmer & Team Technical) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Tugas yang Ditugaskan Kepada Saya</h2>
                        <p class="text-xs text-slate-400">Daftar pekerjaan aktif yang menjadi tanggung jawab Anda</p>
                    </div>
                </div>
                <span class="text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800">
                    {{ $myIssues->count() }} Tugas Aktif
                </span>
            </div>

            @if($myIssues->isEmpty())
                <div class="text-center py-8 text-xs text-slate-400">
                    Tidak ada tugas aktif yang sedang ditugaskan kepada Anda saat ini.
                </div>
            @else
                <div class="divide-y divide-slate-800/80">
                    @foreach($myIssues as $issue)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-800 text-slate-300 shrink-0">
                                {{ $issue->issue_key }}
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('projects.board', $issue->project) }}" class="text-sm font-semibold text-white hover:text-indigo-400 transition truncate block">
                                    {{ $issue->title }}
                                </a>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>{{ $issue->project->name }}</span>
                                    <span>•</span>
                                    <span class="capitalize">{{ $issue->type->label() }}</span>
                                    @if($issue->sprint)
                                        <span>•</span>
                                        <span class="text-indigo-400">{{ $issue->sprint->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $issue->status->badgeClass() }}">
                                {{ $issue->status->label() }}
                            </span>
                            <a href="{{ route('projects.board', $issue->project) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">
                                Buka Papan
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Project List -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Daftar Proyek</h2>
                        <p class="text-xs text-slate-400">Pilih proyek untuk langsung membuka papan kerja (Board)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800">
                        {{ $projects->count() }} Proyek
                    </span>
                </div>
            </div>

            @if($projects->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white">Belum Ada Proyek</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    @if(Auth::user()->isProjectManager())
                        Mulai dengan membuat proyek baru untuk mengelola tugas, sprint, dan backlog tim Anda.
                    @else
                        Anda belum ditambahkan ke proyek apa pun oleh Project Manager.
                    @endif
                </p>
                @if(Auth::user()->isProjectManager())
                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                    + Buat Proyek Pertama
                </a>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($projects as $p)
                <a href="{{ route('projects.board', $p) }}" class="group block bg-slate-950/60 hover:bg-slate-800/50 border border-slate-800 hover:border-indigo-500/50 rounded-2xl p-5 transition duration-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-xs font-mono shrink-0 group-hover:scale-105 transition">
                                    {{ $p->key }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white group-hover:text-indigo-400 transition flex items-center gap-1.5">
                                        <span>{{ $p->name }}</span>
                                    </h3>
                                    <span class="text-[10px] text-slate-500 font-medium">Dibuat {{ $p->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $p->type === 'scrum' ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' }}">
                                {{ $p->type === 'scrum' ? 'Scrum' : 'Kanban' }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-4">
                            {{ $p->description ?: 'Tidak ada deskripsi proyek.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                        <div class="flex items-center gap-3">
                            <span title="Issue Aktif" class="inline-flex items-center gap-1 text-slate-300">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="font-bold text-white">{{ $p->open_issues_count }}</span> aktif
                            </span>
                            <span title="Issue Selesai" class="inline-flex items-center gap-1 text-slate-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="font-bold text-white">{{ $p->done_issues_count }}</span> selesai
                            </span>
                        </div>

                        <span class="text-indigo-400 text-xs font-semibold group-hover:translate-x-1 transition inline-flex items-center gap-1">
                            Buka Board
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
