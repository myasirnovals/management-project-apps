@php
    $canMutate = Auth::user()->canMutateIssues();
    $canManageSprints = Auth::user()->canManageSprints();
    $dialogPrefix = $prefix ?? 'board';
@endphp

<dialog id="issueDetailModal" class="dialog-wide" closedby="any" aria-labelledby="detail_key">
    <div class="p-5 sm:p-6 max-h-[88vh] overflow-y-auto">
        <!-- Dialog Header -->
        <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
            <div class="flex items-center gap-2 flex-wrap">
                <span id="detail_key" class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800"></span>
                <span id="detail_type_badge" class="px-2 py-0.5 rounded text-[11px] font-semibold border"></span>
                <span id="detail_status_badge" class="px-2 py-0.5 rounded text-[11px] font-medium border"></span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($canManageSprints)
                    <div id="wa_action_group" class="hidden items-center gap-1.5">
                        <div class="relative inline-block text-left" id="wa_dropdown_container">
                            <button type="button" onclick="toggleWaDropdown(event)"
                                class="btn-secondary !text-xs !py-1 !px-2.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                                <span>Ingatkan via WA</span>
                            </button>
                            <div id="wa_dropdown_menu" class="hidden absolute right-0 mt-1 w-64 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 p-2 text-xs space-y-1">
                                <div class="px-2 py-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    Kirim Pengingat ke <span id="wa_target_name" class="font-bold text-slate-900 dark:text-white"></span>
                                </div>
                                <a id="wa_link_reminder" href="#" target="_blank" class="block px-2.5 py-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                    <span class="font-semibold block">Pengingat Jatuh Tempo</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Pengingat batas waktu pengerjaan</span>
                                </a>
                                <a id="wa_link_overdue" href="#" target="_blank" class="block px-2.5 py-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                    <span class="font-semibold block">Tanya Kendala / Tertunda</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Konfirmasi hambatan teknis</span>
                                </a>
                                <a id="wa_link_unresponsive" href="#" target="_blank" class="block px-2.5 py-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                    <span class="font-semibold block">Follow-up Respon</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Minta tanggapan pembaruan tiket</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <button type="button" command="close" commandfor="issueDetailModal"
                    class="min-h-9 min-w-9 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                    aria-label="Tutup dialog">
                    &times;
                </button>
            </div>
        </div>

        <!-- Issue Edit Form -->
        <form id="editIssueForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="detail_title" class="form-label">Judul / Ringkasan Issue *</label>
                <input type="text" id="detail_title" name="title" required {{ !$canMutate ? 'disabled' : '' }}
                    class="form-input">
            </div>

            <div>
                <label for="detail_description" class="form-label">Deskripsi</label>
                <textarea id="detail_description" name="description" rows="3" {{ !$canMutate ? 'disabled' : '' }}
                    class="form-input"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 p-3.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl">
                <div>
                    <label for="detail_status" class="form-label">Status</label>
                    <select id="detail_status" name="status" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="to_do">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="in_review">In Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>

                <div>
                    <label for="detail_priority" class="form-label">Prioritas</label>
                    <select id="detail_priority" name="priority" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="highest">Highest</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        <option value="lowest">Lowest</option>
                    </select>
                </div>

                <div>
                    <label for="detail_type" class="form-label">Tipe</label>
                    <select id="detail_type" name="type" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="story">Story</option>
                        <option value="task">Task</option>
                        <option value="bug">Bug</option>
                        <option value="epic">Epic</option>
                        <option value="subtask">Subtask</option>
                    </select>
                </div>

                <div>
                    <label for="detail_story_points" class="form-label">Story Points</label>
                    <input type="number" id="detail_story_points" name="story_points" min="0" max="100" {{ !$canMutate ? 'disabled' : '' }}
                        class="form-input">
                </div>

                <div>
                    <label for="detail_assignee_id" class="form-label">Penerima Tugas</label>
                    <select id="detail_assignee_id" name="assignee_id" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="">Belum Ditugaskan</option>
                        @foreach($project->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="detail_component_id" class="form-label">Komponen</label>
                    <select id="detail_component_id" name="component_id" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="">Tanpa Komponen</option>
                        @foreach($project->components as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="detail_version_id" class="form-label">Versi Rilis</label>
                    <select id="detail_version_id" name="version_id" {{ !$canMutate ? 'disabled' : '' }} class="form-select">
                        <option value="">Tanpa Versi</option>
                        @foreach($project->versions as $ver)
                            <option value="{{ $ver->id }}">{{ $ver->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="detail_due_date" class="form-label">Tenggat Waktu</label>
                    <input type="date" id="detail_due_date" name="due_date" {{ !$canMutate ? 'disabled' : '' }} class="form-input">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700">
                @if($canMutate)
                    <button type="button" id="deleteIssueBtn" onclick="deleteCurrentIssue()" class="btn-danger !text-xs !py-1.5 !px-3">
                        Hapus Issue
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" command="close" commandfor="issueDetailModal" class="btn-secondary !text-xs !py-1.5 !px-3">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary !text-xs !py-1.5 !px-3">
                            Simpan Perubahan
                        </button>
                    </div>
                @else
                    <span class="text-xs text-slate-500 dark:text-slate-400 italic">Mode hanya lihat (Viewer)</span>
                    <button type="button" command="close" commandfor="issueDetailModal" class="btn-secondary !text-xs !py-1.5 !px-3">
                        Tutup
                    </button>
                @endif
            </div>
        </form>

        <!-- Subtasks Section -->
        <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-700 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Subtask Turunan
            </h3>

            <div id="subtasks_container" class="space-y-2">
                <!-- Populated dynamically via JS -->
            </div>

            @if($canMutate)
                <form id="createSubtaskForm" method="POST" class="flex gap-2 pt-2">
                    @csrf
                    <input type="text" id="subtask_title_input" name="title" required placeholder="Tambah subtask baru..."
                        class="form-input flex-1 !text-xs">
                    <button type="submit" class="btn-secondary !text-xs !py-1.5 !px-3 shrink-0">
                        + Subtask
                    </button>
                </form>
            @endif
        </div>

        <!-- Issue Collaboration: Attachments & Comments -->
        @include('board.partials.issue-collaboration', ['prefix' => $dialogPrefix])
    </div>
</dialog>

