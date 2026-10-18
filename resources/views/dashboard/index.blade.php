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
                <h2 class="text-2xl font-black text-white tracking-tight sm:text-3xl">Selamat Datang Admin</h2>
                
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Proyek Baru</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-200 bg-slate-800/80 hover:bg-slate-800 hover:text-white border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-white">{{ $stats['total_projects'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Proyek Scrum &amp; Kanban aktif</div>
            </div>

            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sprint Sedang Berjalan</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-emerald-400">{{ $stats['active_sprints'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Iterasi sprint aktif</div>
            </div>

            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tugas Tim</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['completed_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue berhasil diselesaikan</div>
            </div>
        </div>
    </div>
</x-app-layout>
