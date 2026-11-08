@php
    $navLinkBase = 'flex items-center gap-2.5 px-3 py-2 min-h-11 lg:min-h-9 rounded-lg text-sm font-medium transition-colors';
    $navLinkActive = 'bg-blue-50 dark:bg-blue-950/70 text-blue-800 dark:text-blue-200 font-semibold border border-blue-200 dark:border-blue-800';
    $navLinkIdle = 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent';
@endphp

<div class="flex flex-col h-full">
    @if($isAdminArea && $isPm)
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-amber-50/70 dark:bg-amber-950/30">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-200">
                Panel Admin Sistem
            </span>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Tata kelola proyek, pengguna, &amp; konfigurasi</p>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-5">
            <div>
                <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Administrasi</div>
                <nav class="space-y-1" aria-label="Menu Panel Admin">
                    <a href="{{ route('admin.index') }}" class="{{ $navLinkBase }} {{ request()->routeIs('admin.index') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Ringkasan Admin</span>
                    </a>

                    <a href="{{ route('projects.create') }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.create') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Proyek Baru</span>
                    </a>

                    <a href="{{ route('users.index') }}" class="{{ $navLinkBase }} {{ request()->routeIs('users.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Kelola Pengguna</span>
                    </a>

                    <a href="{{ route('app.settings') }}" class="{{ $navLinkBase }} {{ request()->routeIs('app.settings*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan Aplikasi</span>
                    </a>
                </nav>
            </div>

            @if($activeProject)
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                    <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Proyek Dipilih ({{ $activeProject->key }})
                    </div>
                    <nav class="space-y-1" aria-label="Konfigurasi Proyek">
                        <a href="{{ route('projects.edit', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.edit') ? $navLinkActive : $navLinkIdle }}">
                            <span>Pengaturan &amp; Anggota</span>
                        </a>
                        <a href="{{ route('projects.github.index', $activeProject) }}" class="{{ $navLinkBase }} {{ $navLinkIdle }}">
                            <span>Integrasi GitHub</span>
                        </a>
                        <a href="{{ $activeProject->type === 'kanban' ? route('projects.kanban', $activeProject) : route('projects.board', $activeProject) }}" class="{{ $navLinkBase }} {{ $navLinkIdle }}">
                            <span>Buka Board Proyek</span>
                        </a>
                    </nav>
                </div>
            @endif
        </div>
    @elseif($activeProject)
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-2 min-w-0">
                <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800 font-mono text-xs font-bold shrink-0">
                    {{ $activeProject->key }}
                </span>
                <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $activeProject->name }}</span>
            </div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                <span>Metode {{ strtoupper($activeProject->type) }}</span>
                <a href="{{ route('dashboard') }}" class="font-medium text-blue-700 dark:text-blue-300 hover:underline">Semua Proyek</a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-5">
            <div>
                <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Papan &amp; Jadwal</div>
                <nav class="space-y-1" aria-label="Eksekusi Proyek">
                    @if($activeProject->type === 'kanban')
                        <a href="{{ route('projects.kanban', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.kanban', 'projects.board', 'projects.issues.show') ? $navLinkActive : $navLinkIdle }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                            </svg>
                            <span>Kanban Board</span>
                        </a>
                    @else
                        <a href="{{ route('projects.board', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.board', 'projects.kanban', 'projects.issues.show') ? $navLinkActive : $navLinkIdle }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                            </svg>
                            <span>Scrum Board</span>
                        </a>
                    @endif

                    <a href="{{ route('projects.backlog', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.backlog*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                        <span>Backlog</span>
                    </a>

                    <a href="{{ route('projects.timeline', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.timeline*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Waktu Proyek</span>
                    </a>
                </nav>
            </div>

            <div>
                <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Rilis &amp; Evaluasi</div>
                <nav class="space-y-1" aria-label="Artefak Proyek">
                    <a href="{{ route('projects.components.index', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.components.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Komponen</span>
                    </a>

                    <a href="{{ route('projects.releases.index', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.releases.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Versi Rilis</span>
                    </a>

                    <a href="{{ route('projects.github.index', $activeProject) }}" class="{{ $navLinkBase }} justify-between {{ request()->routeIs('projects.github.*') ? $navLinkActive : $navLinkIdle }}">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                            </svg>
                            <span>Integrasi GitHub</span>
                        </span>
                        @if($activeProject->hasGithubIntegration())
                            <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400" title="Repositori Terhubung"></span>
                        @endif
                    </a>

                    <a href="{{ route('projects.reports.index', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.reports.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Laporan Agile</span>
                    </a>

                    <a href="{{ route('projects.feedback.index', $activeProject) }}" class="{{ $navLinkBase }} {{ request()->routeIs('projects.feedback.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span>Kotak Saran</span>
                    </a>
                </nav>
            </div>

            @if($isPm)
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('projects.edit', $activeProject) }}" class="{{ $navLinkBase }} {{ $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span>Pengaturan Proyek</span>
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-5">
            <div>
                <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Navigasi Utama</div>
                <nav class="space-y-1" aria-label="Workspace Utama">
                    <a href="{{ route('dashboard') }}" class="{{ $navLinkBase }} {{ request()->routeIs('dashboard') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard Workspace</span>
                    </a>

                    <a href="{{ route('calendar.index') }}" class="{{ $navLinkBase }} {{ request()->routeIs('calendar.*') ? $navLinkActive : $navLinkIdle }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kalender Proyek</span>
                    </a>
                </nav>
            </div>

            <div>
                <div class="px-3 mb-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Daftar Proyek</div>
                @if(isset($navProjects) && $navProjects->isNotEmpty())
                    <nav class="space-y-1" aria-label="Pintasan Proyek">
                        @foreach($navProjects as $navProj)
                            <a href="{{ $navProj->type === 'kanban' ? route('projects.kanban', $navProj) : route('projects.board', $navProj) }}"
                                class="{{ $navLinkBase }} {{ $navLinkIdle }}">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 font-mono text-[11px] font-semibold text-slate-800 dark:text-slate-200 shrink-0">
                                    {{ $navProj->key }}
                                </span>
                                <span class="truncate">{{ $navProj->name }}</span>
                            </a>
                        @endforeach
                    </nav>
                @else
                    <p class="px-3 py-2 text-xs text-slate-500 dark:text-slate-400">Belum ada proyek yang ditugaskan.</p>
                @endif
            </div>
        </div>
    @endif
</div>

