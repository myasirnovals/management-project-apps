<!-- Issue Collaboration: Attachments & Comments Section -->
<div class="mt-8 pt-6 border-t border-slate-800 space-y-6">
    <!-- SECTION 1: LAMPIRAN & BUKTI PENGERJAAN (ATTACHMENTS) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm">📎</span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                    Lampiran & Bukti Pengerjaan
                </h4>
                <span id="{{ $prefix }}_attachments_count" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400">
                    0
                </span>
            </div>
            <label for="{{ $prefix }}_file_input"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-indigo-300 bg-indigo-600/10 hover:bg-indigo-600/20 border border-indigo-500/20 cursor-pointer transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Unggah Berkas / Screenshot</span>
            </label>
            <input type="file" id="{{ $prefix }}_file_input" class="hidden"
                onchange="handleAttachmentUpload(event, '{{ $prefix }}')">
        </div>

        <p class="text-[11px] text-slate-500">
            Unggah screenshot kendala, dokumen analisis, atau file pendukung tugas (gambar, PDF, dokumen Word, atau ZIP hingga 25 MB).
        </p>

        <!-- Loading indicator -->
        <div id="{{ $prefix }}_upload_loading" class="hidden p-3 bg-indigo-950/40 border border-indigo-500/30 rounded-xl text-xs text-indigo-300 flex items-center gap-2 animate-pulse">
            <svg class="w-4 h-4 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Sedang mengunggah berkas... Mohon tunggu.</span>
        </div>

        <!-- Attachments Container -->
        <div id="{{ $prefix }}_attachments_container" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- SECTION 2: CATATAN PROGRES & DISKUSI (COMMENTS) -->
    <div class="space-y-4 pt-6 border-t border-slate-800/80">
        <div class="flex items-center gap-2">
            <span class="text-sm">💬</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                Catatan Progres & Diskusi
            </h4>
            <span id="{{ $prefix }}_comments_count" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400">
                0
            </span>
        </div>

        <!-- New Comment Input Box -->
        <form onsubmit="handleCommentSubmit(event, '{{ $prefix }}')" class="space-y-2">
            <textarea id="{{ $prefix }}_comment_input" rows="2" required
                placeholder="Tulis catatan progres pengerjaan, tautan PR/Google Drive, atau pertanyaan di sini..."
                class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition resize-none"></textarea>
            <div class="flex items-center justify-between">
                <span class="text-[10px] text-slate-500">Tekan tombol kirim untuk mencatat progres ke tiket ini.</span>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    <span>Kirim Catatan</span>
                </button>
            </div>
        </form>

        <!-- Comments Stream Container -->
        <div id="{{ $prefix }}_comments_container" class="space-y-3 pt-2">
            <!-- Populated via JS -->
        </div>
    </div>
</div>
