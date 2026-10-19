<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-bold text-white">Waktu Proyek</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ $createEventUrl }}" target="_blank" rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition"
            title="Buat Jadwal Proyek di Google Calendar">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Buat Jadwal</span>
        </a>
    </x-slot>

    <style>
        @media (min-width: 768px) {
            .timeline-metrics-grid {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 1rem !important;
            }
            .timeline-breakdown-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
        }
    </style>

    <div class="space-y-6 w-full max-w-[1600px] mx-auto">
        <!-- 3 Milestone & Timeline Quick Metric Cards (3 Kolom x 1 Baris) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 timeline-metrics-grid">
            <!-- Active Sprint Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sprint Aktif</div>
                    @if($activeSprint)
                        <div class="text-sm font-bold text-white mt-1">{{ $activeSprint->name }}</div>
                        <div class="text-xs text-emerald-400 mt-0.5 flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $activeSprint->start_date ? $activeSprint->start_date->format('d M') : '-' }} s/d {{ $activeSprint->end_date ? $activeSprint->end_date->format('d M Y') : '-' }}
                        </div>
                    @else
                        <div class="text-sm font-semibold text-slate-400 mt-1">Tidak ada sprint aktif</div>
                        <div class="text-xs text-slate-500 mt-0.5">Mulai sprint melalui Backlog Management</div>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold text-sm shrink-0">
                    ⚡
                </div>
            </div>

            <!-- Total Sprints Milestone Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siklus Sprint</div>
                    <div class="text-lg font-bold text-white mt-1">{{ $sprints->count() }} Sprint</div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        {{ $sprints->where('status', \App\Enums\SprintStatus::COMPLETED)->count() }} Selesai • {{ $sprints->where('status', \App\Enums\SprintStatus::PLANNING)->count() }} Rencana
                    </div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center font-bold text-sm shrink-0">
                    🔄
                </div>
            </div>

            <!-- Issue Deadlines Metric Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tenggat Waktu Issue</div>
                    <div class="text-lg font-bold text-white mt-1">{{ $issuesWithDue->count() }} Terjadwal</div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        <span class="text-amber-400 font-semibold">{{ $upcomingIssues->count() }} Mendatang</span> • <span class="text-emerald-400 font-semibold">{{ $doneIssues->count() }} Selesai</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center font-bold text-sm shrink-0">
                    🎯
                </div>
            </div>
        </div>

        <!-- GOOGLE CALENDAR EMBED VIEW (FOCUSED FOR THIS PROJECT) -->
        <div class="space-y-6">
            <!-- Main Embed Frame (Full Width & Spacious Height) -->
            <div class="w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col relative min-h-[820px] lg:min-h-[880px]">
                <!-- Google Calendar Top Status Strip -->
                <div class="bg-slate-900 border-b border-slate-800 px-5 py-2.5 flex items-center justify-between text-xs text-slate-300">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-medium text-white">Google Calendar Live View — Proyek {{ $project->name }}</span>
                        @if(!empty($calendarSettings['calendar_id']))
                        <span class="text-slate-500 font-mono text-[11px] truncate max-w-[320px]">({{ $calendarSettings['calendar_id'] }})</span>
                        @endif
                    </div>

                    <!-- View Mode Selector -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-slate-950 rounded-xl p-0.5 border border-slate-800 text-[11px]">
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'MONTH']) }}"
                                class="px-2.5 py-1 rounded-lg font-semibold transition {{ $viewMode === 'MONTH' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Bulan
                            </a>
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'WEEK']) }}"
                                class="px-2.5 py-1 rounded-lg font-semibold transition {{ $viewMode === 'WEEK' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Minggu
                            </a>
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'AGENDA']) }}"
                                class="px-2.5 py-1 rounded-lg font-semibold transition {{ $viewMode === 'AGENDA' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Agenda
                            </a>
                        </div>

                        <a href="{{ $openCalendarUrl }}" target="_blank" class="text-indigo-400 hover:underline flex items-center gap-1 font-semibold text-[11px]">
                            Layar Penuh
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Iframe Container -->
                <div class="relative w-full flex-1 bg-white min-h-[800px] lg:min-h-[860px]">
                    <iframe id="gcalendarIframe" src="{{ $embedUrl }}" style="border: 0; width: 100%; height: 100%; min-height: 860px;" frameborder="0" scrolling="no" class="w-full h-full min-h-[800px] lg:min-h-[860px]" title="Google Calendar Waktu Proyek {{ $project->name }}">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- BREAKDOWN SECTION: SPRINT MILESTONES & ISSUE DEADLINES (2 Kolom x 1 Baris) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 timeline-breakdown-grid">
            <!-- SPRINT MILESTONES CARD -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">🚩</span>
                        <div>
                            <h3 class="text-sm font-bold text-white">Milestone &amp; Siklus Sprint Proyek</h3>
                            <p class="text-[11px] text-slate-400">Jadwal dan periode iterasi sprint di proyek {{ $project->name }}.</p>
                        </div>
                    </div>
                    <a href="{{ route('projects.backlog', $project) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                        Kelola Backlog →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($sprints as $sprint)
                    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-3 hover:border-slate-700 transition">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-xs font-bold text-white truncate">{{ $sprint->name }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $sprint->status->value === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($sprint->status->value === 'completed' ? 'bg-slate-800 text-slate-400 border border-slate-700' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20') }}">
                                    {{ $sprint->status->label() }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                @if($sprint->start_date && $sprint->end_date)
                                    <span>{{ $sprint->start_date->format('d M Y') }} — {{ $sprint->end_date->format('d M Y') }}</span>
                                    <span class="text-slate-500">({{ $sprint->start_date->diffInDays($sprint->end_date) }} hari)</span>
                                @else
                                    <span class="text-slate-500 italic">Tanggal belum ditentukan</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-mono font-semibold text-slate-300">{{ $sprint->issues->count() }} issues</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-xs text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                        Belum ada sprint yang dibuat di proyek ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- UPCOMING ISSUE DEADLINES CARD -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">⏳</span>
                        <div>
                            <h3 class="text-sm font-bold text-white">Tenggat Waktu Tugas (Issue Deadlines)</h3>
                            <p class="text-[11px] text-slate-400">Daftar tugas dengan batas waktu yang perlu diselesaikan.</p>
                        </div>
                    </div>
                    <a href="{{ route('projects.board', $project) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                        Lihat Board →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($issuesWithDue->take(5) as $issue)
                    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-3 hover:border-slate-700 transition">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-[10px] font-bold text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20 shrink-0">
                                    {{ $issue->issue_key }}
                                </span>
                                <h4 class="text-xs font-semibold text-white truncate">{{ $issue->title }}</h4>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                <span>Batas: <strong class="text-slate-300">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</strong></span>
                                <span class="text-slate-600">•</span>
                                <span class="{{ \Carbon\Carbon::parse($issue->due_date)->isPast() && $issue->status->value !== 'done' ? 'text-rose-400 font-semibold' : 'text-slate-400' }}">
                                    {{ \Carbon\Carbon::parse($issue->due_date)->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $issue->status->value === 'done' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-300' }}">
                                {{ $issue->status->label() }}
                            </span>
                            @if($issue->assignee)
                                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-[10px] font-bold text-white" title="{{ $issue->assignee->name }}">
                                    {{ $issue->assignee->initials() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-xs text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                        Belum ada issue dengan tenggat waktu (due date) di proyek ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: PANDUAN HAK AKSES -->
    <div id="guideModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">
                        💡
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Panduan Hak Akses Kalender Proyek</h3>
                        <p class="text-[11px] text-slate-400">Aturan integrasi Google Calendar di tingkat proyek.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('guideModal')" class="text-slate-400 hover:text-white text-xl leading-none">
                    &times;
                </button>
            </div>

            <div class="space-y-3 text-xs text-slate-300 leading-relaxed">
                <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                    <strong class="text-white block mb-1">1. Hak Akses Project Manager:</strong>
                    Project Manager dapat membuat jadwal kegiatan proyek baru ke Google Calendar langsung dengan tombol <em>+ Buat Jadwal Proyek</em>. Judul acara secara otomatis ditandai kode proyek <strong>[{{ $project->key }}]</strong>.
                </div>

                <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                    <strong class="text-white block mb-1">2. Hak Akses Developer / Member:</strong>
                    Anggota tim memiliki akses langsung untuk melihat jadwal (*read-only*) secara langsung melalui live view kalender ini atau aplikasi Google Calendar.
                </div>

                <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                    <strong class="text-white block mb-1">3. Sinkronisasi Bersama:</strong>
                    Setiap jadwal yang ditambahkan di Google Calendar dengan ID bersama akan langsung tampil bagi seluruh anggota proyek.
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end">
                <button type="button" onclick="closeModal('guideModal')" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 transition">
                    Tutup Panduan
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        }

        window.addEventListener('click', function(e) {
            const guide = document.getElementById('guideModal');
            if (e.target === guide) closeModal('guideModal');
        });
    </script>
</x-app-layout>
