<script>
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

    function autoLinkUrls(text) {
        const escaped = escapeHtml(text);
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return escaped.replace(urlRegex, function(url) {
            return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-blue-700 dark:text-blue-400 hover:underline font-medium break-all">${url}</a>`;
        }).replace(/\n/g, '<br>');
    }

    function renderIssueCollaboration(data, prefix, projectCode) {
        window['currentIssueId_' + prefix] = data.issue.id;
        window['currentProjectCode_' + prefix] = projectCode;

        // Render GitHub Pull Requests
        const prs = data.pull_requests || [];
        const githubSection = document.getElementById(prefix + '_github_section');
        const prsContainer = document.getElementById(prefix + '_prs_container');
        const prsCount = document.getElementById(prefix + '_prs_count');

        if (githubSection && prsContainer) {
            if (prs.length > 0) {
                githubSection.classList.remove('hidden');
                if (prsCount) prsCount.innerText = prs.length;
                prsContainer.innerHTML = '';
                prs.forEach(pr => {
                    const prCard = document.createElement('div');
                    prCard.className = 'p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-between gap-3 text-xs';
                    prCard.innerHTML = `
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${pr.status_badge}">
                                ${escapeHtml(pr.status_label)}
                            </span>
                            <div class="min-w-0">
                                <a href="${escapeHtml(pr.html_url)}" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition truncate block">
                                    #${pr.pr_number} ${escapeHtml(pr.title)}
                                </a>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                    <span>oleh <strong>@${escapeHtml(pr.user_username || 'developer')}</strong></span>
                                    <span>•</span>
                                    <span class="font-mono px-1 rounded bg-slate-100 dark:bg-slate-800">${escapeHtml(pr.branch_head || '')} &rarr; ${escapeHtml(pr.branch_base || '')}</span>
                                </div>
                            </div>
                        </div>
                        <a href="${escapeHtml(pr.html_url)}" target="_blank" class="btn-secondary !text-xs !py-1 !px-2.5 shrink-0">
                            <span>GitHub</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    `;
                    prsContainer.appendChild(prCard);
                });
            } else {
                githubSection.classList.add('hidden');
            }
        }

        // Render Attachments
        const attachments = data.attachments || [];
        const attContainer = document.getElementById(prefix + '_attachments_container');
        const attCount = document.getElementById(prefix + '_attachments_count');
        if (attCount) attCount.innerText = attachments.length;

        if (attContainer) {
            attContainer.innerHTML = '';
            if (attachments.length === 0) {
                attContainer.innerHTML = `
                    <div class="col-span-full py-4 px-3 text-center text-slate-500 dark:text-slate-400 text-xs border border-dashed border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50/50 dark:bg-slate-900/40">
                        Belum ada lampiran berkas pada issue ini.
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
                    <div class="py-4 px-3 text-center text-slate-500 dark:text-slate-400 text-xs border border-dashed border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50/50 dark:bg-slate-900/40">
                        Belum ada catatan progres atau diskusi pada issue ini.
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
        card.className = 'p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-between gap-3 text-xs';

        let previewHtml = '';
        if (att.is_image) {
            previewHtml = `
                <a href="${att.url}" target="_blank" class="w-10 h-10 rounded-md overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 block hover:opacity-90" title="Buka gambar penuh">
                    <img src="${att.url}" alt="${escapeHtml(att.filename)}" class="w-full h-full object-cover">
                </a>
            `;
        } else {
            previewHtml = `
                <div class="w-10 h-10 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            `;
        }

        const deleteBtnHtml = att.can_delete ? `
            <button type="button" onclick="handleAttachmentDelete('${prefix}', ${att.id})" title="Hapus berkas"
                class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/60 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        ` : '';

        card.innerHTML = `
            <div class="flex items-center gap-3 min-w-0">
                ${previewHtml}
                <div class="min-w-0">
                    <a href="${att.download_url}" target="_blank" class="font-semibold text-slate-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 truncate block" title="${escapeHtml(att.filename)}">
                        ${escapeHtml(att.filename)}
                    </a>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
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
                    class="min-h-8 min-w-8 inline-flex items-center justify-center rounded-md text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        item.className = 'p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg space-y-1.5 text-xs';

        const deleteBtnHtml = com.can_delete ? `
            <button type="button" onclick="handleCommentDelete('${prefix}', ${com.id})" title="Hapus catatan"
                class="min-h-7 min-w-7 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/60 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        ` : '';

        item.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 text-[10px] font-bold">
                        ${escapeHtml(com.user_initials)}
                    </span>
                    <span class="font-bold text-slate-900 dark:text-white">${escapeHtml(com.user_name)}</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 capitalize">${escapeHtml(com.user_role)}</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">• ${com.created_at_human}</span>
                </div>
                ${deleteBtnHtml}
            </div>
            <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-xs pl-8">
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
                event.target.value = '';
                const attContainer = document.getElementById(prefix + '_attachments_container');
                const attCount = document.getElementById(prefix + '_attachments_count');
                
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
                alert(data.message || 'Gagal mengunggah berkas.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            if (loading) loading.classList.add('hidden');
        }
    }

    async function handleAttachmentDelete(prefix, attId) {
        if (!confirm('Hapus berkas lampiran ini?')) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/attachments/${attId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const el = document.getElementById(`att_item_${prefix}_${attId}`);
                if (el) el.remove();

                const attCount = document.getElementById(prefix + '_attachments_count');
                if (attCount) {
                    attCount.innerText = Math.max(0, parseInt(attCount.innerText || '1') - 1);
                }
            } else {
                alert(data.message || 'Gagal menghapus berkas.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat menghapus lampiran.');
        }
    }

    async function handleCommentSubmit(event, prefix) {
        event.preventDefault();
        const input = document.getElementById(prefix + '_comment_input');
        const content = input ? input.value.trim() : '';
        if (!content) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
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

                if (comContainer && comContainer.children.length === 1 && comContainer.children[0].textContent.includes('Belum ada catatan')) {
                    comContainer.innerHTML = '';
                }

                if (comContainer) {
                    comContainer.prepend(createCommentElement(data.comment, prefix));
                }
                if (comCount) {
                    comCount.innerText = parseInt(comCount.innerText || '0') + 1;
                }
            } else {
                alert(data.message || 'Gagal mengirim catatan.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        }
    }

    async function handleCommentDelete(prefix, comId) {
        if (!confirm('Hapus catatan diskusi ini?')) return;

        const issueId = window['currentIssueId_' + prefix];
        const projectCode = window['currentProjectCode_' + prefix];
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch(`/projects/${projectCode}/issues/${issueId}/comments/${comId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const el = document.getElementById(`com_item_${prefix}_${comId}`);
                if (el) el.remove();

                const comCount = document.getElementById(prefix + '_comments_count');
                if (comCount) {
                    comCount.innerText = Math.max(0, parseInt(comCount.innerText || '1') - 1);
                }
            } else {
                alert(data.message || 'Gagal menghapus komentar.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat menghapus komentar.');
        }
    }
</script>
