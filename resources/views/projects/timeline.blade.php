<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="font-mono text-blue-700 dark:text-blue-300 font-bold bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800 text-xs">
                {{ $project->key }}
            </span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Waktu Proyek</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <div class="flex items-center gap-2">
            <button type="button" onclick="openModalDialog('timelineGuideModal')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition">
                <span>Panduan Jadwal</span>
            </button>
            @if($isProjectManager)
            <a href="{{ $createEventUrl }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition"
                title="Buat Jadwal Proyek di Google Calendar">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Jadwal</span>
            </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6 w-full max-w-[1600px] mx-auto">
        <!-- 3 Milestone & Timeline Quick Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Active Sprint Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sprint Aktif</div>
                    @if($activeSprint)
                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $activeSprint->name }}</div>
                        <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5 flex items-center gap-1.5 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeSprint->start_date ? $activeSprint->start_date->format('d M') : '-' }} s/d {{ $activeSprint->end_date ? $activeSprint->end_date->format('d M Y') : '-' }}
                        </div>
                    @else
                        <div class="text-sm font-semibold text-slate-500 dark:text-slate-400 mt-1">Tidak ada sprint aktif</div>
                        <div class="text-xs text-slate-400 mt-0.5">Mulai sprint melalui Backlog</div>
                    @endif
                </div>
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-200 dark:border-blue-800">
                    ACT
                </span>
            </div>

            <!-- Total Sprints Milestone Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Siklus Sprint</div>
                    <div class="text-base font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $sprints->count() }} Sprint</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $sprints->where('status', \App\Enums\SprintStatus::COMPLETED)->count() }} Selesai • {{ $sprints->where('status', \App\Enums\SprintStatus::PLANNING)->count() }} Rencana
                    </div>
                </div>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200 dark:border-emerald-800">
                    CYC
                </span>
            </div>

            <!-- Issue Deadlines Metric Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tenggat Waktu Issue</div>
                    <div class="text-base font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $issuesWithDue->count() }} Terjadwal</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $upcomingIssues->count() }} Mendatang</span> • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $doneIssues->count() }} Selesai</span>
                    </div>
                </div>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200 dark:border-amber-800">
                    DUE
                </span>
            </div>
        </div>

        <!-- GOOGLE CALENDAR EMBED VIEW -->
        <div class="space-y-6">
            <div class="w-full bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col relative min-h-[720px]">
                <!-- Top Strip -->
                <div class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 px-4 py-2.5 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">Google Calendar Live View : Proyek {{ $project->name }}</span>
                        @if(!empty($calendarSettings['calendar_id']))
                            <span class="text-slate-500 font-mono text-[11px] truncate max-w-[320px]">({{ $calendarSettings['calendar_id'] }})</span>
                        @endif
                    </div>

                    <!-- View Mode Selector -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-slate-100 dark:bg-slate-900 rounded-lg p-0.5 border border-slate-200 dark:border-slate-800 text-xs">
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'MONTH']) }}"
                                class="px-2.5 py-1 rounded-md font-semibold transition {{ $viewMode === 'MONTH' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                Bulan
                            </a>
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'WEEK']) }}"
                                class="px-2.5 py-1 rounded-md font-semibold transition {{ $viewMode === 'WEEK' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                Minggu
                            </a>
                            <a href="{{ route('projects.timeline', [$project, 'mode' => 'AGENDA']) }}"
                                class="px-2.5 py-1 rounded-md font-semibold transition {{ $viewMode === 'AGENDA' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                Agenda
                            </a>
                        </div>

                        <a href="{{ $openCalendarUrl }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 font-semibold text-xs">
                            Layar Penuh
                        </a>
                    </div>
                </div>

                <!-- Iframe Container -->
                <div class="relative w-full flex-1 bg-white min-h-[700px]">
                    <iframe id="gcalendarIframe" src="{{ $embedUrl }}" style="border: 0; width: 100%; height: 100%; min-height: 700px;" frameborder="0" scrolling="no" class="w-full h-full min-h-[700px]" title="Google Calendar Waktu Proyek {{ $project->name }}">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- BREAKDOWN SECTION: SPRINT MILESTONES & ISSUE DEADLINES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- SPRINT MILESTONES CARD -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">Milestone &amp; Siklus Sprint</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Jadwal dan periode iterasi sprint di proyek {{ $project->name }}.</p>
                    </div>
                    <a href="{{ route('projects.backlog', $project) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                        Kelola Backlog &rarr;
                    </a>
                </div>

                <div class="space-y-2">
                    @forelse($sprints as $sprint)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $sprint->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold shrink-0 {{ $sprint->status->value === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800' : ($sprint->status->value === 'completed' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200') }}">
                                    {{ $sprint->status->label() }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                @if($sprint->start_date && $sprint->end_date)
                                    <span>{{ $sprint->start_date->format('d M Y') }} s/d {{ $sprint->end_date->format('d M Y') }}</span>
                                    <span class="text-slate-400">({{ $sprint->start_date->diffInDays($sprint->end_date) }} hari)</span>
                                @else
                                    <span class="italic">Tanggal belum ditentukan</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $sprint->issues->count() }} issues</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        Belum ada sprint yang dibuat di proyek ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- UPCOMING ISSUE DEADLINES CARD -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">Tenggat Waktu Tugas</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Daftar tugas dengan batas waktu yang perlu diselesaikan.</p>
                    </div>
                    <a href="{{ route('projects.board', $project) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                        Lihat Board &rarr;
                    </a>
                </div>

                <div class="space-y-2">
                    @forelse($issuesWithDue->take(5) as $issue)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="font-mono text-[10px] font-bold text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/40 px-1.5 py-0.2 rounded border border-blue-200 dark:border-blue-800 shrink-0">
                                    {{ $issue->issue_key }}
                                </span>
                                <h4 class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $issue->title }}</h4>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                <span>Batas: <strong class="text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</strong></span>
                                <span>•</span>
                                <span class="{{ \Carbon\Carbon::parse($issue->due_date)->isPast() && $issue->status->value !== 'done' ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ \Carbon\Carbon::parse($issue->due_date)->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $issue->status->value === 'done' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $issue->status->label() }}
                            </span>
                            @if($issue->assignee)
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px] font-bold" title="{{ $issue->assignee->name }}">
                                    {{ $issue->assignee->initials() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        Belum ada issue dengan tenggat waktu di proyek ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Native Dialog: Panduan Jadwal -->
    <dialog id="timelineGuideModal" class="native-modal rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-0 overflow-hidden" closedby="any">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Panduan Jadwal Kalender Proyek</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Aturan integrasi Google Calendar di tingkat proyek.</p>
            </div>
            <button type="button" onclick="closeModalDialog('timelineGuideModal')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
        </div>

        <div class="p-5 space-y-3 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200 dark:border-slate-800">
                <strong class="text-slate-900 dark:text-slate-100 block mb-1">1. Hak Akses Project Manager:</strong>
                Project Manager dapat membuat jadwal kegiatan proyek baru ke Google Calendar langsung dengan tombol <em>+ Buat Jadwal</em>. Judul acara secara otomatis ditandai kode proyek <strong>[{{ $project->key }}]</strong>.
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200 dark:border-slate-800">
                <strong class="text-slate-900 dark:text-slate-100 block mb-1">2. Hak Akses Developer / Member:</strong>
                Anggota tim memiliki akses untuk melihat jadwal (read-only) secara langsung melalui live view kalender ini atau aplikasi Google Calendar.
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200 dark:border-slate-800">
                <strong class="text-slate-900 dark:text-slate-100 block mb-1">3. Sinkronisasi Bersama:</strong>
                Setiap jadwal yang ditambahkan di Google Calendar dengan ID bersama akan langsung tampil bagi seluruh anggota proyek.
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModalDialog('timelineGuideModal')" class="px-4 py-1.5 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 transition">
                    Tutup Panduan
                </button>
            </div>
        </div>
    </dialog>
</x-app-layout>
