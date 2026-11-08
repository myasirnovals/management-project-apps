@php
    $authUser = Auth::user();
    $isPm = $authUser->isProjectManager();
    $isAdminArea = request()->routeIs('admin.*', 'users.*', 'app.settings*', 'projects.create', 'projects.edit');
    $activeProject = ($project ?? $currentProject ?? null);
    if ($activeProject instanceof \App\Models\Project) {
        $activeProject->loadMissing(['components', 'sprints', 'versions', 'epics', 'members']);
    } else {
        $activeProject = null;
    }
    $navProjects = $navProjects ?? collect();
    $showTopbarCreateIssue = $activeProject
        && !$isAdminArea
        && $authUser->canMutateIssues()
        && request()->routeIs('projects.board', 'projects.kanban', 'projects.backlog');
    $brandMonogram = collect(explode(' ', $appBrandName ?? 'Capstone Project Manager'))
        ->filter()
        ->take(3)
        ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
        ->implode('') ?: 'CPM';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <script>
        try {
            var savedScheme = localStorage.getItem('color-scheme');
            if (savedScheme === 'light' || savedScheme === 'dark') {
                document.querySelector('meta[name="color-scheme"]').content = savedScheme;
            }
        } catch (e) {}
    </script>

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ $appBrandName ?? config('app.name', 'Capstone Project Manager') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased flex flex-col overflow-hidden">

    <!-- Global Topbar -->
    <header class="h-14 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-3 sm:px-5 flex items-center justify-between gap-2 shrink-0 z-20">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <button type="button" command="show-modal" commandfor="mobileSidebarDialog"
                class="lg:hidden inline-flex items-center justify-center min-h-11 min-w-11 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                aria-label="Buka menu navigasi">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ $isAdminArea && $isPm ? route('admin.index') : route('dashboard') }}" class="flex items-center gap-2 min-w-0 shrink-0">
                <span class="inline-flex items-center justify-center px-2 py-1 rounded-md bg-blue-700 text-white font-mono text-xs font-bold tracking-tight">
                    {{ $brandMonogram }}
                </span>
                <span class="hidden md:inline font-bold text-sm text-slate-900 dark:text-white truncate max-w-48">
                    {{ $appBrandName ?? 'Capstone Project Manager' }}
                </span>
            </a>

            <span class="hidden sm:block h-5 w-px bg-slate-200 dark:bg-slate-700" aria-hidden="true"></span>

            @if($isPm)
                <nav aria-label="Area Kerja" class="inline-flex items-center rounded-lg p-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <a href="{{ $activeProject && !$isAdminArea ? ($activeProject->type === 'kanban' ? route('projects.kanban', $activeProject) : route('projects.board', $activeProject)) : route('dashboard') }}"
                        class="px-2.5 py-1.5 min-h-9 inline-flex items-center rounded-md text-xs font-semibold transition-colors {{ !$isAdminArea ? 'bg-white dark:bg-slate-900 text-blue-800 dark:text-blue-200 shadow-2xs' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        Workspace Proyek
                    </a>
                    <a href="{{ route('admin.index') }}"
                        class="px-2.5 py-1.5 min-h-9 inline-flex items-center rounded-md text-xs font-semibold transition-colors {{ $isAdminArea ? 'bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-200 shadow-2xs' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        Panel Admin
                    </a>
                </nav>
            @else
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    Workspace Proyek
                </span>
            @endif

            @if(!$isAdminArea && $navProjects->isNotEmpty())
                <details class="relative hidden xl:block">
                    <summary class="list-none inline-flex items-center gap-1.5 px-2.5 py-1.5 min-h-9 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-medium text-slate-800 dark:text-slate-200 cursor-pointer select-none">
                        <span class="truncate max-w-44">
                            {{ $activeProject ? $activeProject->key . ' : ' . $activeProject->name : 'Pindah Proyek' }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="absolute left-0 mt-1.5 w-64 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg py-1.5 z-30">
                        <div class="px-3 py-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400">Pilih Proyek</div>
                        <div class="max-h-64 overflow-y-auto">
                            @foreach($navProjects as $p)
                                <a href="{{ $p->type === 'kanban' ? route('projects.kanban', $p) : route('projects.board', $p) }}"
                                    class="flex items-center gap-2 px-3 py-2 text-xs hover:bg-slate-100 dark:hover:bg-slate-700 {{ $activeProject && $activeProject->id === $p->id ? 'font-semibold text-blue-700 dark:text-blue-300 bg-blue-50/50 dark:bg-blue-950/40' : 'text-slate-800 dark:text-slate-200' }}">
                                    <span class="font-mono font-bold text-[11px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600">{{ $p->key }}</span>
                                    <span class="truncate">{{ $p->name }}</span>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-1 pt-1 border-t border-slate-200 dark:border-slate-700 px-2">
                            <a href="{{ route('dashboard') }}" class="block px-2 py-1.5 rounded-md text-xs font-medium text-blue-700 dark:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                Lihat Semua di Dashboard
                            </a>
                        </div>
                    </div>
                </details>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($showTopbarCreateIssue)
                <button type="button" command="show-modal" commandfor="createIssueModal"
                    class="btn-primary !py-1.5 !px-3 !text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Issue</span>
                </button>
            @endif

            <button type="button" onclick="toggleColorScheme()"
                class="inline-flex items-center justify-center min-h-11 min-w-11 sm:min-h-9 sm:min-w-9 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 cursor-pointer"
                title="Ubah tema terang atau gelap">
                <span class="dark:hidden inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="sr-only">Ubah ke tema gelap</span>
                </span>
                <span class="hidden dark:inline-flex items-center">
                    <svg class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sr-only">Ubah ke tema terang</span>
                </span>
            </button>

            <div class="hidden md:flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-700">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $authUser->role->badgeColor() }}">
                    Role: {{ $authUser->role->label() }}
                </span>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 text-xs font-bold shrink-0" title="{{ $authUser->name }}">
                    {{ $authUser->initials() }}
                </span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar dari sesi"
                    class="inline-flex items-center gap-1.5 min-h-11 sm:min-h-9 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-950/60 hover:text-rose-700 dark:hover:text-rose-200 border border-transparent hover:border-rose-200 dark:hover:border-rose-800 cursor-pointer transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Body Shell -->
    <div class="flex-1 flex min-h-0 overflow-hidden">
        <!-- Desktop Contextual Sidebar -->
        <aside class="hidden lg:flex lg:w-60 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex-col shrink-0">
            @include('layouts.partials.sidebar-nav', [
                'isAdminArea' => $isAdminArea,
                'isPm' => $isPm,
                'activeProject' => $activeProject,
                'navProjects' => $navProjects,
            ])
        </aside>

        <!-- Main Workspace Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            @if(isset($header) || isset($headerActions))
                <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    @isset($header)
                        <div class="min-w-0">
                            <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex flex-wrap items-center gap-2">
                                {{ $header }}
                            </h1>
                        </div>
                    @endisset

                    @isset($headerActions)
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            {{ $headerActions }}
                        </div>
                    @endisset
                </div>
            @endif

            <!-- Unified Flash Notifications -->
            <div class="px-4 sm:px-6 space-y-2 empty:hidden pt-4 empty:pt-0">
                @if(session('success'))
                    <div role="status" class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-sm flex items-center justify-between gap-3">
                        <span>{{ session('success') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-900 cursor-pointer" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div role="alert" class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-sm flex items-center justify-between gap-3">
                        <span>{{ session('error') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md hover:bg-rose-100 dark:hover:bg-rose-900 cursor-pointer" aria-label="Tutup peringatan">&times;</button>
                    </div>
                @endif

                @if(session('warning'))
                    <div role="alert" class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-sm flex items-center justify-between gap-3">
                        <span>{{ session('warning') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md hover:bg-amber-100 dark:hover:bg-amber-900 cursor-pointer" aria-label="Tutup peringatan">&times;</button>
                    </div>
                @endif

                @if(session('info'))
                    <div role="status" class="p-3 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-300 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-sm flex items-center justify-between gap-3">
                        <span>{{ session('info') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md hover:bg-blue-100 dark:hover:bg-blue-900 cursor-pointer" aria-label="Tutup informasi">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-sm">
                        <div class="font-semibold mb-1">Periksa kembali isian formulir:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Navigation Drawer (<dialog>) -->
    <dialog id="mobileSidebarDialog" class="dialog-drawer lg:hidden" closedby="any" aria-label="Menu Navigasi">
        <div class="flex flex-col h-full bg-white dark:bg-slate-900">
            <div class="h-14 px-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div class="min-w-0">
                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $authUser->name }}</div>
                    <div class="text-xs text-slate-600 dark:text-slate-300">Role: {{ $authUser->role->label() }}</div>
                </div>
                <button type="button" command="close" commandfor="mobileSidebarDialog"
                    class="min-h-11 min-w-11 inline-flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                    aria-label="Tutup menu navigasi">
                    &times;
                </button>
            </div>
            <div class="flex-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav', [
                    'isAdminArea' => $isAdminArea,
                    'isPm' => $isPm,
                    'activeProject' => $activeProject,
                    'navProjects' => $navProjects,
                ])
            </div>
        </div>
    </dialog>

    <!-- Contextual Create Issue Modal (<dialog>) -->
    @if($activeProject && $authUser->canMutateIssues())
        <dialog id="createIssueModal" closedby="any" aria-labelledby="createIssueModalTitle">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <div>
                        <h2 id="createIssueModalTitle" class="text-base font-bold text-slate-900 dark:text-white">Buat Issue Baru</h2>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                            Proyek: <span class="font-semibold">{{ $activeProject->name }}</span> (<span class="font-mono">{{ $activeProject->key }}</span>)
                        </p>
                    </div>
                    <button type="button" command="close" commandfor="createIssueModal"
                        class="min-h-10 min-w-10 inline-flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                        aria-label="Tutup dialog">
                        &times;
                    </button>
                </div>

                <form method="POST" action="{{ route('projects.issues.store', $activeProject) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_issue_type" class="form-label">Tipe Issue *</label>
                            <select id="create_issue_type" name="type" required class="form-select">
                                <option value="task" selected>Task (Pekerjaan Teknis)</option>
                                <option value="story">Story (Kebutuhan Pengguna)</option>
                                <option value="bug">Bug (Perbaikan Masalah)</option>
                                <option value="epic">Epic (Inisiatif Besar)</option>
                            </select>
                        </div>

                        <div>
                            <label for="create_issue_priority" class="form-label">Prioritas</label>
                            <select id="create_issue_priority" name="priority" class="form-select">
                                <option value="highest">Highest</option>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                                <option value="lowest">Lowest</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="create_issue_title" class="form-label">Judul / Ringkasan Issue *</label>
                        <input id="create_issue_title" type="text" name="title" required
                            placeholder="Contoh: Validasi stok barang saat kasir menyimpan transaksi"
                            class="form-input">
                    </div>

                    <div>
                        <label for="create_issue_desc" class="form-label">Deskripsi</label>
                        <textarea id="create_issue_desc" name="description" rows="3"
                            placeholder="Uraikan kebutuhan fungsional, langkah reproduksi, atau kriteria selesai..."
                            class="form-input"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_issue_assignee" class="form-label">Penerima Tugas (Assignee)</label>
                            <select id="create_issue_assignee" name="assignee_id" class="form-select">
                                <option value="">Belum Ditugaskan</option>
                                @foreach($activeProject->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create_issue_points" class="form-label">Story Points</label>
                            <input id="create_issue_points" type="number" name="story_points" min="0" max="100"
                                placeholder="Contoh: 1, 2, 3, 5, 8"
                                class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_issue_sprint" class="form-label">Tempatkan Pada</label>
                            <select id="create_issue_sprint" name="sprint_id" class="form-select">
                                <option value="">Backlog Proyek</option>
                                @foreach($activeProject->sprints as $sprint)
                                    @if($sprint->status->value !== 'completed')
                                        <option value="{{ $sprint->id }}">{{ $sprint->name }} ({{ $sprint->status->label() }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create_issue_due" class="form-label">Tenggat Waktu</label>
                            <input id="create_issue_due" type="date" name="due_date" class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="create_issue_epic" class="form-label">Induk Epic</label>
                            <select id="create_issue_epic" name="parent_id" class="form-select">
                                <option value="">Tanpa Epic</option>
                                @foreach($activeProject->epics as $epic)
                                    <option value="{{ $epic->id }}">{{ $epic->issue_key }} - {{ \Illuminate\Support\Str::limit($epic->title, 28) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create_issue_component" class="form-label">Komponen</label>
                            <select id="create_issue_component" name="component_id" class="form-select">
                                <option value="">Tanpa Komponen</option>
                                @foreach($activeProject->components as $component)
                                    <option value="{{ $component->id }}">{{ $component->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create_issue_version" class="form-label">Versi Rilis</label>
                            <select id="create_issue_version" name="version_id" class="form-select">
                                <option value="">Tanpa Versi</option>
                                @foreach($activeProject->versions as $ver)
                                    <option value="{{ $ver->id }}">{{ $ver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" command="close" commandfor="createIssueModal" class="btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary">
                            Simpan Issue
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif
</body>
</html>
