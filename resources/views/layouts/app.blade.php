<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Workspace' }} - {{ config('app.name', 'AgilePM') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased selection:bg-indigo-500 selection:text-white flex overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 select-none z-20">
        <!-- Brand & Logo -->
        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-600/30 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-white block">AgilePM</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest block font-semibold">Pro Project Studio</span>
                </div>
            </a>
        </div>

        <!-- Project Selector / Context -->
        @php
            $currentProject = isset($project) ? $project : null;
            $allProjectsList = \App\Models\Project::orderBy('name')->get();
        @endphp

        @if($currentProject)
            <div class="p-3 mx-3 my-3 bg-slate-950/60 rounded-xl border border-slate-800">
                <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Proyek Aktif</div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-6 h-6 rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $currentProject->key }}
                        </span>
                        <span class="text-xs font-semibold text-slate-200 truncate">{{ $currentProject->name }}</span>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center gap-1.5 text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 transition group">
                    <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        @endif

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto px-3 py-2 space-y-6">
            @if($currentProject)
                <!-- Project Planning Section -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Papan & Perencanaan
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('projects.board', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.board') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            <span>Scrum Board</span>
                        </a>

                        <a href="{{ route('projects.kanban', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.kanban') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            <span>Kanban Board</span>
                        </a>

                        <a href="{{ route('projects.backlog', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.backlog') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Backlog Management</span>
                        </a>
                    </nav>
                </div>

                <!-- Project Tracking Section -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Artefak & Pelaporan
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('projects.components.index', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.components.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Komponen</span>
                        </a>

                        <a href="{{ route('projects.releases.index', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.releases.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>Rilis & Versi</span>
                        </a>

                        <a href="{{ route('projects.reports.index', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.reports.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Laporan Agile</span>
                        </a>

                        <a href="{{ route('projects.edit', $currentProject) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('projects.edit') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pengaturan Proyek</span>
                        </a>
                    </nav>
                </div>
            @endif

            <!-- Global Workspace Section -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Workspace Utama
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola Tim & Anggota</span>
                    </a>

                    <a href="{{ route('calendar.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('calendar.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kalender Proyek</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- User Profile & Logout -->
        <div class="p-3 border-t border-slate-800 bg-slate-900/80">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs ring-2 ring-indigo-500/30 shrink-0">
                        {{ Auth::user()->initials() }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->role->label() }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Header Bar -->
        <header class="h-16 bg-slate-900 border-b border-slate-800 px-6 flex items-center justify-between shrink-0 z-10">
            <!-- Left: Breadcrumb / Title -->
            <div class="flex items-center gap-4">
                <h1 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                    {{ $header ?? 'Workspace' }}
                </h1>
            </div>

            <!-- Right: Actions & Quick Create Button -->
            <div class="flex items-center gap-3">
                @isset($headerActions)
                    {{ $headerActions }}
                @endisset

                @if($currentProject)
                    <!-- Fast "+ Buat Issue" Jira Style Button -->
                    <button type="button" onclick="openCreateIssueModal()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Issue</span>
                    </button>
                @endif
            </div>
        </header>

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300">&times;</button>
            </div>
        @endif

        <!-- Main Body Content -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Global Modal: Create Issue (Jira Navbar Style - Gambar 2.9) -->
    @if($currentProject)
    <div id="createIssueModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-white">Buat Issue Baru</h3>
                    <p class="text-xs text-slate-400">Proyek: <strong class="text-indigo-400">{{ $currentProject->name }} ({{ $currentProject->key }})</strong></p>
                </div>
                <button type="button" onclick="closeCreateIssueModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('projects.issues.store', $currentProject) }}" class="space-y-4">
                @csrf

                <!-- Issue Type -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Tipe Issue *</label>
                    <select name="type" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="story">📘 Story (User Requirement)</option>
                        <option value="task" selected>☑️ Task (Pekerjaan Teknis)</option>
                        <option value="bug">🐞 Bug (Masalah / Defect)</option>
                        <option value="epic">⚡ Epic (Wadah Inisiatif Besar)</option>
                    </select>
                </div>

                <!-- Title / Summary -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Summary / Judul Issue *</label>
                    <input type="text" name="title" required placeholder="Ringkasan tugas atau fitur yang ingin dibuat..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Deskripsi Lengkap</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan kebutuhan, kriteria pengujian, atau konteks..."
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Prioritas</label>
                        <select name="priority" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="highest">🔴 Highest</option>
                            <option value="high">🟠 High</option>
                            <option value="medium" selected>🟡 Medium</option>
                            <option value="low">🔵 Low</option>
                            <option value="lowest">⚪ Lowest</option>
                        </select>
                    </div>

                    <!-- Story Points (Estimasi) -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Story Points (Estimasi)</label>
                        <input type="number" name="story_points" min="0" max="100" placeholder="e.g. 1, 2, 3, 5, 8"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Assignee -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Penerima Tugas (Assignee)</label>
                        <select name="assignee_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Belum Ditugaskan (Unassigned) --</option>
                            @foreach($currentProject->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Component -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Komponen</label>
                        <select name="component_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Tanpa Komponen --</option>
                            @foreach($currentProject->components as $component)
                                <option value="{{ $component->id }}">{{ $component->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Target Sprint / Backlog -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Tempatkan Pada</label>
                        <select name="sprint_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Backlog Proyek</option>
                            @foreach($currentProject->sprints as $sprint)
                                <option value="{{ $sprint->id }}">{{ $sprint->name }} ({{ $sprint->status->label() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fix Version -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Fix Version (Rilis)</label>
                        <select name="version_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Tanpa Versi --</option>
                            @foreach($currentProject->versions as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->status->label() }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeCreateIssueModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                        Buat Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function openCreateIssueModal() {
            const modal = document.getElementById('createIssueModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeCreateIssueModal() {
            const modal = document.getElementById('createIssueModal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
</body>
</html>
