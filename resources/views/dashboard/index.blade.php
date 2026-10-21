<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>Dashboard Workspace</span>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    @php
        $user = Auth::user();
        $isPm = $user->isProjectManager();
        $isDev = $user->isDeveloper();
        $isSm = $user->isScrumMaster();
        $isPo = $user->isProductOwner();
        $isViewer = $user->isViewer();

        // Theme palette & contextual motto per role
        $roleTheme = match($user->role->value) {
            'project_manager' => [
                'banner_border' => 'border-purple-500/30',
                'banner_gradient' => 'from-purple-950/60 via-indigo-950/40 to-slate-900',
                'badge' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                'glow' => 'bg-purple-500/10',
                'motto' => 'Pantau tata kelola proyek, kesehatan sprint tim, alokasi tugas, serta tanggapi saran & masukan dari anggota tim.',
            ],
            'developer' => [
                'banner_border' => 'border-emerald-500/30',
                'banner_gradient' => 'from-emerald-950/60 via-teal-950/40 to-slate-900',
                'badge' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                'glow' => 'bg-emerald-500/10',
                'motto' => 'Fokus menyelesaikan tugas sprint aktif yang ditugaskan kepada Anda. Pantau issue Anda dan sampaikan ide kapan saja.',
            ],
            'scrum_master' => [
                'banner_border' => 'border-sky-500/30',
                'banner_gradient' => 'from-sky-950/60 via-blue-950/40 to-slate-900',
                'badge' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
                'glow' => 'bg-sky-500/10',
                'motto' => 'Pantau kesehatan sprint berjalan, minimalkan bottleneck di kolom review, dan kawal kecepatan iterasi tim.',
            ],
            'product_owner' => [
                'banner_border' => 'border-amber-500/30',
                'banner_gradient' => 'from-amber-950/60 via-orange-950/40 to-slate-900',
                'badge' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                'glow' => 'bg-amber-500/10',
                'motto' => 'Pantau kesiapan backlog, target rilis versi, dan kaji masukan tim untuk diadaptasi menjadi user story.',
            ],
            default => [
                'banner_border' => 'border-slate-700/60',
                'banner_gradient' => 'from-slate-900 via-slate-950 to-slate-900',
                'badge' => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                'glow' => 'bg-slate-500/10',
                'motto' => 'Pantau status pengerjaan proyek dan milestone rilis, serta berikan masukan konstruktif melalui Kotak Saran.',
            ],
        };
    @endphp

    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Welcome & Quick Banner (Role-Adaptive) -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r {{ $roleTheme['banner_gradient'] }} border {{ $roleTheme['banner_border'] }} p-6 sm:p-8 shadow-xl">
            <div class="relative z-10 max-w-3xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $roleTheme['badge'] }} border">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        Role: {{ $user->role->label() }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">
                        {{ $user->job_title ?: 'Anggota Tim' }}
                    </span>
                </div>

                <h2 class="text-2xl font-black text-white tracking-tight sm:text-3xl">
                    Selamat Datang, {{ $user->name }}
                </h2>

                <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
                    {{ $roleTheme['motto'] }}
                </p>

                <!-- Contextual Quick Actions per Role -->
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if($isPm)
                        {{-- PM Quick Actions --}}
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Buat Proyek Baru</span>
                        </a>

                        @if($stats['pending_feedback_count'] > 0)
                        <a href="#feedbacks-section" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-amber-300 bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 transition">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>Tinjau Kotak Saran ({{ $stats['pending_feedback_count'] }})</span>
                        </a>
                        @endif

                        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Kelola Pengguna</span>
                        </a>

                        <a href="{{ route('app.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pengaturan Aplikasi</span>
                        </a>

                    @elseif($isDev)
                        {{-- Developer Quick Actions --}}
                        <a href="#my-tasks" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/30 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>Tugas Saya Hari Ini ({{ $stats['my_tasks_count'] }})</span>
                        </a>

                        @if($firstProject)
                        <a href="{{ route('projects.board', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            <span>Buka Board ({{ $firstProject->key }})</span>
                        </a>

                        <a href="{{ route('projects.feedback.index', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 transition">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <span>Kirim Saran ke PM</span>
                        </a>
                        @endif

                    @elseif($isSm)
                        {{-- Scrum Master Quick Actions --}}
                        @if($firstProject)
                        <a href="{{ route('projects.board', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/30 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Papan Sprint Aktif</span>
                        </a>

                        <a href="{{ route('projects.reports.velocity', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Laporan Velocity</span>
                        </a>
                        @endif

                        <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 transition">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Kalender Proyek</span>
                        </a>

                    @elseif($isPo)
                        {{-- Product Owner Quick Actions --}}
                        @if($firstProject)
                        <a href="{{ route('projects.backlog', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-md shadow-amber-600/30 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Kelola Backlog Produk</span>
                        </a>

                        <a href="{{ route('projects.releases.index', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>Versi & Rilis</span>
                        </a>

                        <a href="{{ route('projects.feedback.index', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 transition">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <span>Kotak Saran Tim</span>
                        </a>
                        @endif

                    @else
                        {{-- Viewer Quick Actions --}}
                        @if($firstProject)
                        <a href="{{ route('projects.board', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            <span>Jelajahi Papan Proyek</span>
                        </a>

                        <a href="{{ route('projects.feedback.index', $firstProject) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 transition">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <span>Beri Saran ke PM</span>
                        </a>
                        @endif

                        <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Kalender Proyek</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Ambient Glow Effect -->
            <div class="absolute -right-16 -bottom-16 w-72 h-72 {{ $roleTheme['glow'] }} rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- Metric Stat Cards (Role-Adaptive) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Card 1: Total Proyek (Universal) --}}
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-slate-700 transition">
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

            {{-- Card 2: Adaptive per role --}}
            @if($isDev)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Tugas Aktif Saya</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['my_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Perlu diselesaikan (To Do &amp; In Progress)</div>
            </div>
            @elseif($isPo)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-blue-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Item di Backlog</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-blue-400">{{ $stats['backlog_issues_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Belum dimasukkan ke dalam sprint</div>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-emerald-500/40 transition">
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
            @endif

            {{-- Card 3: Adaptive per role --}}
            @if($isDev)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-sky-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-400">Tugas Dalam Review</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-sky-400">{{ $stats['my_in_review_tasks'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Menunggu tinjauan kode &amp; pengujian</div>
            </div>
            @elseif($isSm)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Bottleneck / Review</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['in_review_issues_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Issue di kolom In Review</div>
            </div>
            @elseif($isPo)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-purple-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Versi Rilis Aktif</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['active_releases_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Target versi yang belum dirilis</div>
            </div>
            @elseif($isPm)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Issue Terbuka Tim</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['open_issues_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue tim belum berstatus Done</div>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-purple-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Issue Selesai (Done)</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['completed_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue berhasil diselesaikan</div>
            </div>
            @endif

            {{-- Card 4: Adaptive per role --}}
            @if($isPm)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-rose-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-400">Saran Butuh Respon</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-rose-400">{{ $stats['pending_feedback_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Masukan tim menunggu review PM</div>
            </div>
            @elseif($isDev)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-emerald-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Tugas Saya Selesai</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-emerald-400">{{ $stats['my_completed_tasks'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Tugas Anda yang berstatus Done</div>
            </div>
            @elseif($isSm)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-purple-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Total Selesai (Done)</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-purple-400">{{ $stats['completed_tasks_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Total issue berhasil diselesaikan</div>
            </div>
            @elseif($isPo)
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Saran Fitur Baru</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-amber-400">{{ $stats['pending_feedback_count'] }}</div>
                <div class="mt-1 text-xs text-slate-400">Ide baru yang siap dikaji jadi backlog</div>
            </div>
            @else
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-5 shadow-sm hover:border-indigo-500/40 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Progres Workspace</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-3xl font-black text-indigo-400">{{ $stats['workspace_completion_rate'] }}%</div>
                <div class="mt-1 text-xs text-slate-400">Rasio issue terselesaikan</div>
            </div>
            @endif
        </div>

        {{-- Section: Tugas Saya di Sprint Aktif (Khusus Developer atau User dengan Assigned Issues) --}}
        @if($isDev || $myIssues->isNotEmpty())
        <div id="my-tasks" class="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-sm scroll-mt-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Tugas Saya di Sprint Aktif</h2>
                        <p class="text-xs text-slate-400">Daftar issue yang ditugaskan kepada Anda dan perlu dituntaskan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800 font-mono">
                        {{ $myIssues->count() }} Tugas Tertunda
                    </span>
                </div>
            </div>

            @if($myIssues->isEmpty())
            <div class="text-center py-10 px-4 rounded-xl bg-slate-950/40 border border-slate-800/50">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white">Tidak Ada Tugas yang Tertunda</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Luar biasa! Semua tugas Anda telah selesai atau belum ada issue sprint yang ditugaskan kepada Anda saat ini.
                </p>
                @if($firstProject)
                <a href="{{ route('projects.board', $firstProject) }}" class="inline-flex items-center gap-2 mt-4 px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 transition">
                    <span>Lihat Papan Kerja Proyek</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @foreach($myIssues as $issue)
                <div class="bg-slate-950/60 hover:bg-slate-800/40 border border-slate-800/80 hover:border-slate-700 rounded-xl p-4 transition group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-indigo-400 border border-slate-700">
                                    {{ $issue->issue_key }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $issue->type->badgeClasses() }}">
                                    {{ $issue->type->label() }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    • {{ $issue->project->name }}
                                </span>
                            </div>

                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $issue->priority->badgeClasses() }}">
                                {{ $issue->priority->label() }}
                            </span>
                        </div>

                        <h4 class="text-sm font-semibold text-white group-hover:text-indigo-400 transition line-clamp-2 mb-3">
                            {{ $issue->title }}
                        </h4>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium border {{ $issue->status->badgeClasses() }}">
                                {{ $issue->status->label() }}
                            </span>
                            @if($issue->sprint)
                            <span class="text-[10px] text-slate-400 hidden sm:inline" title="Sprint">
                                🏃 {{ $issue->sprint->name }}
                            </span>
                            @endif
                        </div>

                        <a href="{{ route('projects.board', $issue->project) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold inline-flex items-center gap-1 text-[11px] transition">
                            <span>Buka di Board</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Section: Kotak Saran Tim Memerlukan Tindakan (Khusus Project Manager) --}}
        @if($isPm)
        <div id="feedbacks-section" class="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-sm scroll-mt-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Kotak Saran Tim Memerlukan Tindakan</h2>
                        <p class="text-xs text-slate-400">Masukan fitur dan saran dari anggota tim yang menunggu keputusan PM</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20 font-bold">
                        {{ $pendingFeedbacks->count() }} Menunggu
                    </span>
                </div>
            </div>

            @if($pendingFeedbacks->isEmpty())
            <div class="text-center py-8 px-4 rounded-xl bg-slate-950/40 border border-slate-800/50">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xs font-semibold text-white">Semua Saran Telah Ditinjau</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada saran atau permintaan fitur yang menggantung saat ini.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($pendingFeedbacks as $fb)
                <div class="bg-slate-950/60 hover:bg-slate-800/40 border border-slate-800/80 rounded-xl p-4 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/15 text-indigo-300 border border-indigo-500/20 font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $fb->user ? $fb->user->initials() : 'U' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $fb->categoryBadgeClass() }}">
                                    {{ $fb->categoryLabel() }}
                                </span>
                                <span class="text-xs font-bold text-white">
                                    {{ $fb->title }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    di <span class="text-slate-300 font-semibold">{{ $fb->project->name }}</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">
                                {{ $fb->description }}
                            </p>
                            <span class="text-[10px] text-slate-500">
                                Dari <strong class="text-slate-400">{{ $fb->user ? $fb->user->name : 'Anonim' }}</strong> • {{ $fb->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ route('projects.feedback.index', $fb->project) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                            <span>Tinjau &amp; Tanggapi</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Section: Status Saran & Masukan Anda (Untuk Non-PM yang Pernah Mengirimkan Masukan) --}}
        @if(!$isPm && $myFeedbacks->isNotEmpty())
        <div class="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Status Masukan &amp; Saran Anda</h2>
                        <p class="text-xs text-slate-400">Pantau perkembangan ide atau perbaikan yang telah Anda sampaikan ke PM</p>
                    </div>
                </div>
                <span class="text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800">
                    {{ $myFeedbacks->count() }} Saran Terkirim
                </span>
            </div>

            <div class="space-y-3">
                @foreach($myFeedbacks as $mf)
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $mf->categoryBadgeClass() }}">
                                {{ $mf->categoryLabel() }}
                            </span>
                            <span class="text-xs font-bold text-white">
                                {{ $mf->title }}
                            </span>
                            <span class="text-[10px] text-slate-400">
                                • Proyek {{ $mf->project->name }}
                            </span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $mf->statusBadgeClass() }}">
                            {{ $mf->statusLabel() }}
                        </span>
                    </div>

                    @if($mf->pm_response)
                    <div class="mt-2.5 p-3 rounded-lg bg-indigo-950/30 border border-indigo-500/20 text-xs">
                        <span class="font-bold text-indigo-300 block mb-0.5">Tanggapan Project Manager:</span>
                        <p class="text-slate-300 italic">{{ $mf->pm_response }}</p>
                    </div>
                    @endif

                    <div class="mt-2 text-[10px] text-slate-500 flex items-center justify-between">
                        <span>Dikirim {{ $mf->created_at->diffForHumans() }}</span>
                        <a href="{{ route('projects.feedback.index', $mf->project) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                            Buka Kotak Saran Proyek &rarr;
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Project List (Workspace Projects) -->
        <div id="project-list" class="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
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
                    <span class="text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800 font-mono">
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
                @if($isPm)
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Mulai dengan membuat proyek baru untuk mengelola tugas, sprint, dan backlog tim Anda.</p>
                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                    + Buat Proyek Pertama
                </a>
                @else
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Belum ada proyek yang terdaftar di workspace ini. Silakan hubungi Project Manager untuk menginisiasi proyek baru.</p>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($projects as $p)
                <div class="group bg-slate-950/60 hover:bg-slate-800/50 border border-slate-800/80 hover:border-indigo-500/50 rounded-2xl p-5 transition duration-200 shadow-sm hover:shadow-indigo-500/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600/20 to-purple-600/20 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-xs font-mono shrink-0 group-hover:scale-105 transition">
                                    {{ $p->key }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white group-hover:text-indigo-400 transition flex items-center gap-1.5">
                                        <a href="{{ route('projects.board', $p) }}">{{ $p->name }}</a>
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

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
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

                        <div class="flex items-center gap-2">
                            <a href="{{ route('projects.feedback.index', $p) }}" title="Kotak Saran Proyek" class="p-1 rounded-lg text-slate-400 hover:text-indigo-300 hover:bg-slate-800 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('projects.board', $p) }}" class="text-indigo-400 text-xs font-semibold group-hover:translate-x-0.5 transition inline-flex items-center gap-1">
                                Buka Board
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>