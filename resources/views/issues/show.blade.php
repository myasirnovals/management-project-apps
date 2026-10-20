<x-app-layout :project="$project">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.board', $project) }}" class="text-xs text-slate-400 hover:text-white transition">
                &larr; Kembali ke Board
            </a>
            <span class="text-slate-600">/</span>
            <span class="font-mono text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 text-xs">
                {{ $issue->issue_key }}
            </span>
            <span class="text-sm font-bold text-white">{{ $issue->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Main Issue Details Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-xl border border-indigo-500/20">
                        {{ $issue->issue_key }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $issue->type->badgeClasses() }}">
                        {{ $issue->type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $issue->status->badgeClasses() }}">
                        {{ $issue->status->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $issue->priority->badgeClasses() }}">
                        {{ $issue->priority->label() }}
                    </span>
                </div>

                <div class="text-xs text-slate-400">
                    Dibuat oleh <strong class="text-white">{{ $issue->reporter?->name ?? 'User' }}</strong> • {{ $issue->created_at->diffForHumans() }}
                </div>
            </div>

            <!-- Title & Description -->
            <div class="space-y-3">
                <h1 class="text-xl font-bold text-white">{{ $issue->title }}</h1>
                <div class="text-xs text-slate-300 leading-relaxed bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                    {!! nl2br(e($issue->description ?: 'Tidak ada deskripsi.')) !!}
                </div>
            </div>

            <!-- Meta Information Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-950/40 border border-slate-800/60 rounded-2xl text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Assignee</span>
                    <span class="text-white font-medium mt-0.5 block">
                        {{ $issue->assignee?->name ?? 'Unassigned' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Sprint</span>
                    <span class="text-white font-medium mt-0.5 block">
                        {{ $issue->sprint?->name ?? 'Backlog (No Sprint)' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Story Points</span>
                    <span class="text-white font-medium mt-0.5 block">
                        {{ $issue->story_points ?? '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Jatuh Tempo</span>
                    <span class="text-white font-medium mt-0.5 block">
                        {{ $issue->due_date ? $issue->due_date->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            <!-- Subtasks Section -->
            @if($issue->subtasks->isNotEmpty())
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                    Sub-tasks (Pekerjaan Turunan)
                </h3>
                <div class="space-y-2">
                    @foreach($issue->subtasks as $st)
                        <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-cyan-400 font-bold">{{ $st->issue_key }}</span>
                                <span class="text-slate-200">{{ $st->title }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300">{{ $st->status->label() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Issue Collaboration: Attachments & Comments -->
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
