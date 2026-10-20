<script>
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Auto-link URLs in plain text safely
    function autoLinkUrls(text) {
        const escaped = escapeHtml(text);
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return escaped.replace(urlRegex, function(url) {
            return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-indigo-400 hover:text-indigo-300 underline font-medium break-all">${url}</a>`;
        }).replace(/\n/g, '<br>');
    }

    function renderIssueCollaboration(data, prefix, projectCode) {
        window['currentIssueId_' + prefix] = data.issue.id;
        window['currentProjectCode_' + prefix] = projectCode;

        // Render Attachments
        const attachments = data.attachments || [];
        const attContainer = document.getElementById(prefix + '_attachments_container');
        const attCount = document.getElementById(prefix + '_attachments_count');
        if (attCount) attCount.innerText = attachments.length;

        if (attContainer) {
            attContainer.innerHTML = '';
            if (attachments.length === 0) {
                attContainer.innerHTML = `
                    <div class="col-span-full py-4 px-3 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                        Belum ada lampiran. Unggah screenshot hasil pengerjaan atau berkas analisis di atas.
                    </div>
                `;
            } else {
                attachments.forEach(att => {
                    attContainer.appendChild(createAttachmentElement(att, prefix));
                });
            }
        }

        // Render Comments
        const comments = data.comments || [];
        const comContainer = document.getElementById(prefix + '_comments_container');
        const comCount = document.getElementById(prefix + '_comments_count');
        if (comCount) comCount.innerText = comments.length;

        if (comContainer) {
            comContainer.innerHTML = '';
            if (comments.length === 0) {
                comContainer.innerHTML = `
                    <div class="py-4 px-3 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                        Belum ada catatan progres atau diskusi pada tiket ini.
                    </div>
                `;
            } else {
                comments.forEach(com => {
                    comContainer.appendChild(createCommentElement(com, prefix));
                });
            }
        }
    }

    function createAttachmentElement(att, prefix) {
        const card = document.createElement('div');
        card.id = `att_item_${prefix}_${att.id}`;
        card.className = 'group p-3 bg-slate-950/90 border border-slate-800 rounded-2xl flex items-center justify-between gap-3 text-xs hover:border-slate-700 transition shadow-sm';

        let previewHtml = '';
        if (att.is_image) {
            previewHtml = `
                <a href="${att.url}" target="_blank" class="w-10 h-10 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shrink-0 block hover:opacity-90 transition" title="Buka gambar penuh">
                    <img src="${att.url}" alt="${escapeHtml(att.filename)}" class="w-full h-full object-cover">
                </a>
            `;
        } else {
            previewHtml = `
                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-indigo-400 font-bold flex items-center justify-center shrink-0 text-base">
                    📄
                </div>
            `;
        }

        const deleteBtnHtml = att.can_delete ? `
            <button type="button" onclick="handleAttachmentDelete('${prefix}', ${att.id})" title="Hapus berkas"
                class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 flex items-center justify-center transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        ` : '';

        card.innerHTML = `
            <div class="flex items-center gap-3 min-w-0">
                ${previewHtml}
                <div class="min-w-0">
                    <a href="${att.download_url}" target="_blank" class="font-semibold text-slate-200 hover:text-indigo-400 truncate block transition" title="${escapeHtml(att.filename)}">
                        ${escapeHtml(att.filename)}
                    </a>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                        <span class="font-mono">${att.formatted_size}</span>
                        <span>•</span>
                        <span>${escapeHtml(att.user_name)}</span>
                        <span>•</span>
                        <span>${att.created_at_human}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <a href="${att.download_url}" target="_blank" title="Unduh berkas"
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 flex items-center justify-center transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
                ${deleteBtnHtml}
            </div>
        `;

        return card;
    }

    function createCommentElement(com, prefix) {
        const item = document.createElement('div');
        item.id = `com_item_${prefix}_${com.id}`;
        item.className = 'p-3.5 bg-slate-950/80 border border-slate-800 rounded-2xl space-y-2 text-xs shadow-sm';

        const deleteBtnHtml = com.can_delete ? `
            <button type="button" onclick="handleCommentDelete('${prefix}', ${com.id})" title="Hapus catatan"
                class="text-slate-500 hover:text-rose-400 p-1 rounded hover:bg-rose-500/10 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        ` : '';

        item.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center ring-1 ring-slate-700">
                        ${escapeHtml(com.user_initials)}
                    </div>
                    <span class="font-bold text-slate-200">${escapeHtml(com.user_name)}</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-800 text-slate-400 capitalize">${escapeHtml(com.user_role)}</span>
                    <span class="text-[10px] text-slate-500">• ${com.created_at_human}</span>
                </div>
                ${deleteBtnHtml}
            </div>
            <div class="text-slate-300 leading-relaxed text-xs pl-8">
                ${autoLinkUrls(com.content)}
            </div>
        `;

        return item;
    }

    async function handleAttachmentUpload(event, prefix) {
        const file = event.target.files[0];
        if (!file) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        if (!issueId || !projectCode) return;

        const loading = document.getElementById(prefix + '_upload_loading');
        if (loading) loading.classList.remove('hidden');

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/attachments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                event.target.value = ''; // reset input
                const attContainer = document.getElementById(prefix + '_attachments_container');
                const attCount = document.getElementById(prefix + '_attachments_count');
                
                // Remove empty state if present
                if (attContainer && attContainer.children.length === 1 && attContainer.children[0].classList.contains('col-span-full')) {
                    attContainer.innerHTML = '';
                }

                if (attContainer) {
                    attContainer.prepend(createAttachmentElement(data.attachment, prefix));
                }
                if (attCount) {
                    attCount.innerText = parseInt(attCount.innerText || '0') + 1;
                }
            } else {
                alert(data.message || 'Gagal mengunggah berkas. Pastikan ukuran file tidak melebihi 25 MB.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat mengunggah berkas.');
        } finally {
            if (loading) loading.classList.add('hidden');
        }
    }

    async function handleAttachmentDelete(prefix, attachmentId) {
        if (!confirm('Apakah Anda yakin ingin menghapus berkas lampiran ini?')) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        if (!issueId || !projectCode) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/attachments/${attachmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const item = document.getElementById(`att_item_${prefix}_${attachmentId}`);
                if (item) item.remove();
                
                const attCount = document.getElementById(prefix + '_attachments_count');
                if (attCount) {
                    const current = Math.max(0, parseInt(attCount.innerText || '1') - 1);
                    attCount.innerText = current;

                    const attContainer = document.getElementById(prefix + '_attachments_container');
                    if (current === 0 && attContainer) {
                        attContainer.innerHTML = `
                            <div class="col-span-full py-4 px-3 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                Belum ada lampiran. Unggah screenshot hasil pengerjaan atau berkas analisis di atas.
                            </div>
                        `;
                    }
                }
            } else {
                const data = await res.json();
                alert(data.message || 'Gagal menghapus berkas.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
        }
    }

    async function handleCommentSubmit(event, prefix) {
        event.preventDefault();

        const input = document.getElementById(prefix + '_comment_input');
        if (!input || !input.value.trim()) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        if (!issueId || !projectCode) return;

        const content = input.value.trim();
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ content: content })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                input.value = '';
                const comContainer = document.getElementById(prefix + '_comments_container');
                const comCount = document.getElementById(prefix + '_comments_count');

                if (comContainer && comContainer.children.length === 1 && comContainer.children[0].classList.contains('py-4')) {
                    comContainer.innerHTML = '';
                }

                if (comContainer) {
                    comContainer.prepend(createCommentElement(data.comment, prefix));
                }
                if (comCount) {
                    comCount.innerText = parseInt(comCount.innerText || '0') + 1;
                }
            } else {
                alert(data.message || 'Gagal menambahkan komentar.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menambahkan komentar.');
        }
    }

    async function handleCommentDelete(prefix, commentId) {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan komentar ini?')) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        if (!issueId || !projectCode) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const item = document.getElementById(`com_item_${prefix}_${commentId}`);
                if (item) item.remove();

                const comCount = document.getElementById(prefix + '_comments_count');
                if (comCount) {
                    const current = Math.max(0, parseInt(comCount.innerText || '1') - 1);
                    comCount.innerText = current;

                    const comContainer = document.getElementById(prefix + '_comments_container');
                    if (current === 0 && comContainer) {
                        comContainer.innerHTML = `
                            <div class="py-4 px-3 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                Belum ada catatan progres atau diskusi pada tiket ini.
                            </div>
                        `;
                    }
                }
            } else {
                const data = await res.json();
                alert(data.message || 'Gagal menghapus komentar.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
        }
    }
</script>
