<!-- Issue Collaboration: PRs, Attachments, & Comments -->
<div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-700 space-y-6">
    <!-- Pull Requests (GitHub) -->
    <div id="{{ $prefix }}_github_section" class="hidden space-y-2.5 pb-5 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                    Pull Requests (GitHub)
                </h3>
            </div>
            <span id="{{ $prefix }}_prs_count" class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-200 border border-purple-200 dark:border-purple-800 font-mono">
                0
            </span>
        </div>

        <div id="{{ $prefix }}_prs_container" class="space-y-2">
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- Lampiran & Bukti Pengerjaan -->
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                    Lampiran &amp; Bukti Pengerjaan
                </h3>
                <span id="{{ $prefix }}_attachments_count" class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                    0
                </span>
            </div>

            @if(Auth::user()->canMutateIssues())
                <label for="{{ $prefix }}_file_input" class="btn-secondary !text-xs !py-1 !px-2.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Unggah Berkas</span>
                </label>
                <input type="file" id="{{ $prefix }}_file_input" class="hidden" onchange="handleAttachmentUpload(event, '{{ $prefix }}')">
            @endif
        </div>

        <p class="text-[11px] text-slate-500 dark:text-slate-400">
            Unggah berkas screenshot, dokumen spesifikasi, atau artefak pengerjaan tugas (gambar, PDF, dokumen Word, ZIP hingga 25 MB).
        </p>

        <div id="{{ $prefix }}_upload_loading" class="hidden p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-lg text-xs text-blue-800 dark:text-blue-200 flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Sedang mengunggah berkas, mohon tunggu...</span>
        </div>

        <div id="{{ $prefix }}_attachments_container" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- Catatan Progres & Diskusi -->
    <div class="space-y-3 pt-5 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Catatan Progres &amp; Diskusi
            </h3>
            <span id="{{ $prefix }}_comments_count" class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                0
            </span>
        </div>

        @if(Auth::user()->canMutateIssues())
            <form onsubmit="handleCommentSubmit(event, '{{ $prefix }}')" class="space-y-2">
                <textarea id="{{ $prefix }}_comment_input" rows="2" required
                    placeholder="Tulis catatan progres pengerjaan, link pull request, atau kendala teknis..."
                    class="form-input text-xs"></textarea>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Tekan tombol untuk menyimpan catatan ke riwayat issue.</span>
                    <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">
                        Kirim Catatan
                    </button>
                </div>
            </form>
        @else
            <p class="text-xs text-slate-500 dark:text-slate-400 italic">Peran Viewer hanya memiliki hak akses baca pada diskusi ini.</p>
        @endif

        <div id="{{ $prefix }}_comments_container" class="space-y-2.5 pt-1">
            <!-- Populated via JS -->
        </div>
    </div>
</div>
