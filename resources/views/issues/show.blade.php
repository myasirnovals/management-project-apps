<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-2 flex-wrap text-xs sm:text-sm">
            <a href="{{ $project->type === 'kanban' ? route('projects.kanban', $project) : route('projects.board', $project) }}" class="text-blue-700 dark:text-blue-300 hover:underline">
                Board {{ $project->key }}
            </a>
            <span class="text-slate-400 dark:text-slate-600">/</span>
            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                {{ $issue->issue_key }}
            </span>
            <span class="font-bold text-slate-900 dark:text-white truncate max-w-md">{{ $issue->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="app-card p-5 sm:p-7 space-y-6">
            <!-- Header Badges -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2.5 py-1 rounded-md border border-blue-200 dark:border-blue-800">
                        {{ $issue->issue_key }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold border {{ $issue->type->badgeClasses() }}">
                        {{ $issue->type->label() }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $issue->status->badgeClasses() }}">
                        {{ $issue->status->label() }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $issue->priority->badgeClasses() }}">
                        {{ $issue->priority->label() }}
                    </span>
                </div>

                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Dibuat oleh <strong class="text-slate-800 dark:text-slate-200">{{ $issue->reporter?->name ?? 'User' }}</strong> • {{ $issue->created_at->diffForHumans() }}
                </div>
            </div>

            <!-- Title & Description -->
            <div class="space-y-3">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $issue->title }}</h1>
                <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    {!! nl2br(e($issue->description ?: 'Tidak ada deskripsi rinci untuk issue ini.')) !!}
                </div>
            </div>

            <!-- Meta Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase tracking-wider">Penerima Tugas</span>
                    <span class="text-slate-900 dark:text-white font-medium mt-0.5 block">
                        {{ $issue->assignee?->name ?? 'Belum Ditugaskan' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase tracking-wider">Sprint</span>
                    <span class="text-slate-900 dark:text-white font-medium mt-0.5 block">
                        {{ $issue->sprint?->name ?? 'Backlog' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase tracking-wider">Story Points</span>
                    <span class="text-slate-900 dark:text-white font-medium mt-0.5 block">
                        {{ $issue->story_points ?? '0' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-semibold uppercase tracking-wider">Jatuh Tempo</span>
                    <span class="text-slate-900 dark:text-white font-medium mt-0.5 block">
                        {{ $issue->due_date ? $issue->due_date->format('d M Y') : 'Tidak ditentukan' }}
                    </span>
                </div>
            </div>

            <!-- Subtasks Section -->
            @if($issue->subtasks->isNotEmpty())
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        Subtask Turunan
                    </h3>
                    <div class="space-y-2">
                        @foreach($issue->subtasks as $st)
                            <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-blue-700 dark:text-blue-300 font-bold">{{ $st->issue_key }}</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $st->title }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $st->status->label() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Collaboration (PRs, Attachments, Comments) -->
            @include('board.partials.issue-collaboration', ['prefix' => 'standalone'])
        </div>
    </div>

    @include('board.partials.issue-collaboration-scripts')

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const res = await fetch(`{{ route('projects.issues.show', [$project, $issue]) }}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                renderIssueCollaboration(data, 'standalone', '{{ $project->code }}');
            } catch (e) {
                console.error(e);
            }
        });
    </script>
</x-app-layout>
