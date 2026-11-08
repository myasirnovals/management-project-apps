import './bootstrap';

function getSystemScheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function getRenderedScheme() {
    const meta = document.querySelector('meta[name="color-scheme"]');
    const content = meta?.getAttribute('content') || 'light dark';
    if (content === 'dark' || content === 'light') {
        return content;
    }
    return getSystemScheme();
}

function applyMetaScheme(value) {
    const meta = document.querySelector('meta[name="color-scheme"]');
    if (meta) {
        meta.setAttribute('content', value || 'light dark');
    }
}

window.toggleColorScheme = function toggleColorScheme() {
    const current = getRenderedScheme();
    const target = current === 'dark' ? 'light' : 'dark';
    const system = getSystemScheme();

    try {
        if (target === system) {
            localStorage.removeItem('color-scheme');
            applyMetaScheme('light dark');
        } else {
            localStorage.setItem('color-scheme', target);
            applyMetaScheme(target);
        }
    } catch {
        applyMetaScheme(target);
    }
};

window.setColorSchemePreference = function setColorSchemePreference(pref) {
    try {
        if (pref === 'light' || pref === 'dark') {
            localStorage.setItem('color-scheme', pref);
            applyMetaScheme(pref);
        } else {
            localStorage.removeItem('color-scheme');
            applyMetaScheme('light dark');
        }
    } catch {
        applyMetaScheme(pref === 'light' || pref === 'dark' ? pref : 'light dark');
    }
};

window.addEventListener('storage', (event) => {
    if (event.key === 'color-scheme') {
        const val = event.newValue;
        applyMetaScheme(val === 'light' || val === 'dark' ? val : 'light dark');
    }
});

window.openModalDialog = function openModalDialog(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (typeof el.showModal === 'function') {
        if (!el.open) {
            el.showModal();
        }
    } else {
        el.classList.remove('hidden');
    }
};

window.closeModalDialog = function closeModalDialog(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (typeof el.close === 'function') {
        if (el.open) {
            el.close();
        }
    } else {
        el.classList.add('hidden');
    }
};

window.openModal = window.openModalDialog;
window.closeModal = window.closeModalDialog;
window.openCreateIssueModal = () => window.openModalDialog('createIssueModal');
window.closeCreateIssueModal = () => window.closeModalDialog('createIssueModal');

document.addEventListener('DOMContentLoaded', () => {
    if (!('closedBy' in HTMLDialogElement.prototype)) {
        document.querySelectorAll('dialog[closedby="any"]').forEach((dialog) => {
            dialog.addEventListener('click', (event) => {
                if (event.target !== dialog) return;
                const rect = dialog.getBoundingClientRect();
                const inside =
                    rect.top <= event.clientY &&
                    event.clientY <= rect.top + rect.height &&
                    rect.left <= event.clientX &&
                    event.clientX <= rect.left + rect.width;
                if (!inside) {
                    dialog.close();
                }
            });
        });
    }

    if (!('commandForElement' in HTMLButtonElement.prototype)) {
        document.addEventListener('click', (event) => {
            const btn = event.target.closest?.('button[commandfor]');
            if (!btn) return;
            const targetId = btn.getAttribute('commandfor');
            const command = btn.getAttribute('command');
            const target = targetId ? document.getElementById(targetId) : null;
            if (!target) return;

            if (command === 'show-modal' && typeof target.showModal === 'function') {
                event.preventDefault();
                if (!target.open) target.showModal();
            } else if ((command === 'close' || command === 'request-close') && typeof target.close === 'function') {
                event.preventDefault();
                if (target.open) target.close();
            }
        });
    }
});
