<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Dashboard Workspace</span>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Welcome & Quick Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-900/60 via-purple-900/40 to-slate-900 border border-indigo-500/20 p-8 shadow-xl">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                    Role: {{ Auth::user()->role->label() }}
                </span>
                <h2 class="text-2xl font-black text-white tracking-tight sm:text-3xl">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                    Sistem manajemen proyek internal Anda sudah siap. Sebagai Project Manager / Scrum Master, Anda dapat membuat proyek baru, merencanakan sprint di Backlog, serta menambahkan rekan tim langsung melalui menu <strong>Kelola Tim</strong> tanpa form registrasi publik.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Proyek Baru</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-200 bg-slate-800/80 hover:bg-slate-800 hover:text-white border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Tambah Anggota Tim</span>
                    </a>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Proyek</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-white">{{ $stats['total_projects'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Proyek Scrum & Kanban aktif</div>
            </div>

            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sprint Sedang Berjalan</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-emerald-400">{{ $stats['active_sprints'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Iterasi sprint aktif</div>
            </div>

            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tugas Saya</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['my_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Belum selesai (In Progress / To Do)</div>
            </div>

            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Selesai (Done)</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['completed_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue berhasil diselesaikan</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Active Sprints & Project List -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Active Sprints Panel -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Sprint Aktif Saat Ini
                        </h3>
                    </div>

                    @if($activeSprints->isEmpty())
                        <div class="text-center py-10 bg-slate-950/40 rounded-2xl border border-dashed border-slate-800 text-slate-400 text-sm">
                            Belum ada sprint aktif yang sedang berlangsung. Buka menu <a href="{{ route('projects.index') }}" class="text-indigo-400 underline">Proyek</a> lalu mulai sprint di Backlog.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($activeSprints as $activeSprint)
                                <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                                    {{ $activeSprint->project->key }}
                                                </span>
                                                <h4 class="text-base font-bold text-white">{{ $activeSprint->name }}</h4>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-1 italic">
                                                Goal: {{ $activeSprint->goal ?: 'Tidak ada goal spesifik yang ditentukan.' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('projects.board', $activeSprint->project) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                                            <span>Buka Board</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-xs mb-1.5">
                                            <span class="text-slate-400">Progres Sprint</span>
                                            <span class="font-bold text-slate-200">{{ $activeSprint->completion_percentage }}% Selesai ({{ $activeSprint->completed_story_points }}/{{ $activeSprint->total_story_points }} SP)</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full transition-all duration-500" @style(["width: {$activeSprint->completion_percentage}%"])></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Projects Quick Overview -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-bold text-white">Daftar Proyek</h3>
                        <a href="{{ route('projects.create') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">
                            + Buat Proyek
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($projects as $p)
                            <div class="flex items-center justify-between p-4 bg-slate-950/40 border border-slate-800/80 rounded-2xl hover:border-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-bold text-sm text-indigo-400">
                                        {{ $p->key }}
                                    </div>
                                    <div>
                                        <a href="{{ route('projects.board', $p) }}" class="font-bold text-sm text-white hover:text-indigo-400 transition">
                                            {{ $p->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400">
                                            Tipe: <span class="capitalize">{{ $p->type }}</span> • {{ $p->members->count() }} Anggota Tim
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('projects.board', $p) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                                        Board
                                    </a>
                                    <a href="{{ route('projects.backlog', $p) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                                        Backlog
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: My Tasks -->
            <div class="space-y-8">
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-white">Tugas Ditugaskan ke Saya</h3>
                        <span class="text-xs font-bold text-slate-400 bg-slate-800 px-2 py-0.5 rounded-full">
                            {{ $myIssues->count() }}
                        </span>
                    </div>

                    @if($myIssues->isEmpty())
                        <div class="text-center py-8 text-slate-500 text-xs">
                            Tidak ada tugas yang menunggu pengerjaan Anda saat ini.
                        </div>
                    @else
                        <div class="divide-y divide-slate-800/80">
                            @foreach($myIssues as $task)
                                <div class="py-3.5 flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-mono font-semibold text-[11px] text-indigo-400">
                                            {{ $task->issue_key }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $task->status->badgeClasses() }}">
                                            {{ $task->status->label() }}
                                        </span>
                                    </div>
                                    <div class="text-xs font-medium text-slate-200">
                                        {{ $task->title }}
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                                        <span>{{ $task->project->name }}</span>
                                        <span class="font-bold text-{{ $task->priority->value === 'highest' || $task->priority->value === 'high' ? 'rose' : 'slate' }}-400">
                                            {{ $task->priority->label() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Handbook Quick Reference Tip -->
                <div class="p-5 rounded-3xl bg-indigo-950/40 border border-indigo-800/50 text-indigo-200 text-xs space-y-2">
                    <div class="font-bold text-sm text-indigo-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Pedoman Praktikum (Modul 2025)
                    </div>
                    <p class="leading-relaxed text-indigo-300/80">
                        Dalam kerangka kerja Scrum, setiap sprint dibatasi waktu (time-boxed) biasanya 2-3 minggu. Peran Project Manager / Scrum Master memastikan rintangan (impediments) dieliminasi dan Definition of Done tercapai.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
