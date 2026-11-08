<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Dashboard Workspace</span>
        </div>
    </x-slot>

    @php
        $user = Auth::user();
        $isPm = $user->isProjectManager();
        $isDev = $user->isDeveloper();
        $isSm = $user->isScrumMaster();
        $isPo = $user->isProductOwner();
        $isViewer = $user->isViewer();
    @endphp

    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="app-card p-5 sm:p-6 bg-gradient-to-r from-blue-50/80 via-white to-slate-50 dark:from-slate-900 dark:via-slate-850 dark:to-slate-900 border-slate-200 dark:border-slate-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $user->role->badgeColor() }}">
                            Role: {{ $user->role->label() }}
                        </span>
                        @if($user->job_title)
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ $user->job_title }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Selamat Datang, {{ $user->name }}
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                        @if($isPm)
                            Kelola alokasi proyek, pantau kesehatan sprint tim, dan tanggapi masukan anggota tim.
                        @elseif($isDev)
                            Fokus menyelesaikan tugas sprint aktif yang ditugaskan kepada Anda dan laporkan progres terkini.
                        @elseif($isSm)
                            Pantau kelancaran iterasi sprint, minimalkan hambatan kolom review, dan kawal target tim.
                        @elseif($isPo)
                            Susun prioritas backlog produk, tentukan target versi rilis, dan kaji masukan tim.
                        @else
                            Pantau perkembangan pengerjaan proyek dan berikan masukan melalui Kotak Saran.
                        @endif
                    </p>
                </div>

                @if($isPm)
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('projects.create') }}" class="btn-primary !text-xs !py-2 !px-3.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buat Proyek Baru</span>
                        </a>
                        <a href="{{ route('admin.index') }}" class="btn-secondary !text-xs !py-2 !px-3.5">
                            <span>Buka Panel Admin</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="app-card p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Total Proyek</span>
                    <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono text-[11px]">
                        {{ $projects->count() }} Terdaftar
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total_projects'] }}</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Proyek yang dapat Anda akses</p>
            </div>

            @if($isDev)
                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Tugas Aktif Saya</span>
                        <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-mono text-[11px] font-bold">
                            Perlu Tindakan
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $stats['my_tasks_count'] }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Status To Do dan In Progress</p>
                </div>

                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Tugas Dalam Review</span>
                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono text-[11px]">
                            Review
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['my_in_review_tasks'] }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menunggu peninjauan kode</p>
                </div>

                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Tugas Selesai</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-mono text-[11px]">
                            Done
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $stats['my_completed_tasks'] }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tugas Anda yang telah tuntas</p>
                </div>
            @else
                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Sprint Sedang Berjalan</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-mono text-[11px]">
                            Aktif
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $stats['active_sprints'] }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Iterasi sprint aktif di workspace</p>
                </div>

                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Issue Terbuka</span>
                        <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-mono text-[11px]">
                            Dalam Proses
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $stats['open_issues_count'] }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Belum berstatus Done</p>
                </div>

                <div class="app-card p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Penyelesaian Proyek</span>
                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono text-[11px]">
                            Rasio
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $stats['workspace_completion_rate'] }}%</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $stats['completed_tasks_count'] }} dari seluruh issue selesai</p>
                </div>
            @endif
        </div>

        <!-- Section: Tugas Saya di Sprint Aktif (Developer & Users with tasks) -->
        @if($isDev || $myIssues->isNotEmpty())
            <div id="my-tasks" class="app-card">
                <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Tugas Saya di Sprint Aktif</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar issue yang ditugaskan kepada Anda dan perlu dituntaskan</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                        {{ $myIssues->count() }} Tugas
                    </span>
                </div>

                @if($myIssues->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-500 dark:text-slate-400">
                        Tidak ada tugas yang tertunda. Semua tugas Anda telah selesai atau belum ada issue yang ditugaskan kepada Anda.
                    </div>
                @else
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($myIssues as $issue)
                            <div class="p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-blue-400 dark:hover:border-blue-600 transition-colors flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded font-mono text-[11px] font-bold bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-blue-700 dark:text-blue-300">
                                                {{ $issue->issue_key }}
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded text-[11px] font-semibold border {{ $issue->type->badgeClasses() }}">
                                                {{ $issue->type->label() }}
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $issue->project->name }}
                                            </span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $issue->priority->badgeClasses() }}">
                                            {{ $issue->priority->label() }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white line-clamp-2">
                                        {{ $issue->title }}
                                    </h3>
                                </div>
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $issue->status->badgeClasses() }}">
                                        {{ $issue->status->label() }}
                                    </span>
                                    <a href="{{ $issue->project->type === 'kanban' ? route('projects.kanban', $issue->project) : route('projects.board', $issue->project) }}" class="font-semibold text-blue-700 dark:text-blue-300 hover:underline">
                                        Buka Board
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Section: Kotak Saran Tim Memerlukan Tindakan (Project Manager) -->
        @if($isPm && $pendingFeedbacks->isNotEmpty())
            <div id="feedbacks-section" class="app-card border-rose-300 dark:border-rose-900/60">
                <div class="p-4 sm:px-6 border-b border-rose-200 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-rose-900 dark:text-rose-200">Kotak Saran Tim Memerlukan Tindakan</h2>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Masukan anggota tim menunggu tinjauan dan tindak lanjut Project Manager</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-900 text-rose-800 dark:text-rose-200 self-start sm:self-auto">
                        {{ $pendingFeedbacks->count() }} Menunggu
                    </span>
                </div>

                <div class="p-4 sm:p-6 space-y-3">
                    @foreach($pendingFeedbacks as $fb)
                        <div class="p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $fb->categoryBadgeClass() }}">
                                        {{ $fb->categoryLabel() }}
                                    </span>
                                    <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300">
                                        {{ $fb->project ? $fb->project->key : 'Proyek' }}
                                    </span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $fb->title }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-1">
                                    {{ $fb->description }}
                                </p>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                    Dari {{ $fb->user ? $fb->user->name : 'Anonim' }} • {{ $fb->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ route('projects.feedback.index', $fb->project) }}" class="btn-primary !text-xs !py-1.5 !px-3">
                                    Tinjau Kotak Saran
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Project Grid -->
        <div class="app-card">
            <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Proyek Workspace</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pilih proyek untuk langsung membuka papan sprint atau kanban</p>
                </div>
                <span class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400">
                    {{ $projects->count() }} Proyek
                </span>
            </div>

            @if($projects->isEmpty())
                <div class="p-8 text-center text-sm text-slate-600 dark:text-slate-400">
                    Belum ada proyek yang dapat Anda akses saat ini.
                </div>
            @else
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($projects as $p)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-blue-400 dark:hover:border-blue-600 transition-colors flex flex-col justify-between gap-4">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded font-mono text-xs font-bold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800 shrink-0">
                                            {{ $p->key }}
                                        </span>
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                            <a href="{{ $p->type === 'kanban' ? route('projects.kanban', $p) : route('projects.board', $p) }}" class="hover:text-blue-700 dark:hover:text-blue-300">
                                                {{ $p->name }}
                                            </a>
                                        </h3>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $p->type === 'scrum' ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-200' : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200' }}">
                                        {{ strtoupper($p->type) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    {{ $p->description ?: 'Tidak ada deskripsi proyek.' }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                    <span><strong class="text-slate-900 dark:text-white">{{ $p->open_issues_count }}</strong> aktif</span>
                                    <span>•</span>
                                    <span><strong class="text-slate-900 dark:text-white">{{ $p->done_issues_count }}</strong> selesai</span>
                                </div>
                                <a href="{{ $p->type === 'kanban' ? route('projects.kanban', $p) : route('projects.board', $p) }}" class="btn-secondary !text-xs !py-1 !px-2.5">
                                    Buka Board
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
