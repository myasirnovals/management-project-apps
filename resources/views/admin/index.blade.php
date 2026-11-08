<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800 text-xs font-mono font-bold uppercase tracking-wider">
                Admin
            </span>
            <span>Panel Admin: Tata Kelola Sistem &amp; Proyek</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('projects.create') }}" class="btn-primary !py-1.5 !px-3 !text-xs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Buat Proyek Baru</span>
        </a>
        <a href="{{ route('users.index') }}" class="btn-secondary !py-1.5 !px-3 !text-xs">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Kelola Pengguna</span>
        </a>
        <a href="{{ route('app.settings') }}" class="btn-secondary !py-1.5 !px-3 !text-xs">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Pengaturan</span>
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Metric Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="app-card p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Total Proyek</span>
                    <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono text-[11px]">
                        {{ $adminStats['projects_active'] }} Aktif
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $adminStats['projects_total'] }}</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Seluruh proyek terdaftar</p>
            </div>

            <div class="app-card p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Pengguna Terdaftar</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-mono text-[11px]">
                        {{ $adminStats['users_active'] }} Aktif
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $adminStats['users_total'] }}</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">PM, PO, SM, Dev, &amp; Viewer</p>
            </div>

            <div class="app-card p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Kotak Saran Tim</span>
                    @if($adminStats['pending_feedback'] > 0)
                        <span class="px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-mono text-[11px] font-bold">
                            Perlu Tindakan
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono text-[11px]">
                            Tuntas
                        </span>
                    @endif
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $adminStats['pending_feedback'] }}</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Masukan belum ditanggapi</p>
            </div>

            <div class="app-card p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Integrasi GitHub</span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[11px]">
                        Webhook
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $adminStats['github_connected'] }}</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Proyek terhubung ke repositori</p>
            </div>
        </div>

        <!-- Governance Table: Proyek -->
        <div class="app-card">
            <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Tata Kelola Seluruh Proyek</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola anggota, pengaturan, dan pantau status setiap proyek</p>
                </div>
                <a href="{{ route('projects.create') }}" class="btn-primary !text-xs !py-1.5 !px-3 self-start sm:self-auto">
                    + Proyek Baru
                </a>
            </div>

            @if($projects->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Belum ada proyek yang terdaftar di sistem.</p>
                    <a href="{{ route('projects.create') }}" class="btn-primary mt-3 !text-xs">
                        Buat Proyek Pertama
                    </a>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Kunci &amp; Nama</th>
                                <th scope="col" class="px-4 py-3">Metode</th>
                                <th scope="col" class="px-4 py-3">Pemilik</th>
                                <th scope="col" class="px-4 py-3">Anggota</th>
                                <th scope="col" class="px-4 py-3">Issue Terbuka</th>
                                <th scope="col" class="px-4 py-3">GitHub</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi Manajemen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($projects as $p)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200 font-mono text-xs font-bold border border-blue-200 dark:border-blue-800 shrink-0">
                                                {{ $p->key }}
                                            </span>
                                            <div>
                                                <a href="{{ $p->type === 'kanban' ? route('projects.kanban', $p) : route('projects.board', $p) }}" class="font-semibold text-slate-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300">
                                                    {{ $p->name }}
                                                </a>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">/projects/{{ $p->code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $p->type === 'scrum' ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800' }}">
                                            {{ strtoupper($p->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300">
                                        {{ $p->owner ? $p->owner->name : 'Sistem' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300">
                                        {{ $p->members_count }} orang
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ $p->open_issues_count }}</span>
                                        <span class="text-slate-500 dark:text-slate-400">/ {{ $p->issues_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        @if($p->hasGithubIntegration())
                                            <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-300 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span>Terhubung</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('projects.edit', $p) }}" class="btn-secondary !text-xs !py-1 !px-2.5">
                                                Edit &amp; Anggota
                                            </a>
                                            <a href="{{ route('projects.github.index', $p) }}" class="btn-secondary !text-xs !py-1 !px-2.5">
                                                GitHub
                                            </a>
                                            <a href="{{ route('projects.feedback.index', $p) }}" class="btn-secondary !text-xs !py-1 !px-2.5" title="Kotak Saran">
                                                Saran ({{ $p->pending_feedback_count }})
                                            </a>
                                            <a href="{{ $p->type === 'kanban' ? route('projects.kanban', $p) : route('projects.board', $p) }}" class="btn-primary !text-xs !py-1 !px-2.5">
                                                Buka Board
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Moderation: Kotak Saran Tim Menunggu Tindakan PM -->
        <div class="app-card">
            <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Kotak Saran Tim Menunggu Tindakan</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tinjau masukan dan usulan fitur dari anggota tim lintas proyek</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pendingFeedbacks->isNotEmpty() ? 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                    {{ $pendingFeedbacks->count() }} Menunggu
                </span>
            </div>

            @if($pendingFeedbacks->isEmpty())
                <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                    Tidak ada saran anggota tim yang berstatus pending saat ini. Seluruh masukan telah ditinjau.
                </div>
            @else
                <div class="p-4 sm:p-6 space-y-3">
                    @foreach($pendingFeedbacks as $fb)
                        <div class="p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $fb->categoryBadgeClass() }}">
                                        {{ $fb->categoryLabel() }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-blue-700 dark:text-blue-300">
                                        {{ $fb->project ? $fb->project->key : 'Proyek' }}
                                    </span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $fb->title }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">
                                    {{ $fb->description }}
                                </p>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                    Diajukan oleh <span class="font-medium text-slate-700 dark:text-slate-300">{{ $fb->user ? $fb->user->name : 'Anonim' }}</span>
                                    pada {{ $fb->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ route('projects.feedback.index', $fb->project) }}" class="btn-primary !text-xs !py-1.5 !px-3">
                                    Tanggapi di Kotak Saran
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

