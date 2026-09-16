{{-- Branded confirm/alert — ganti window.confirm / alert --}}
<div id="pesbar-confirm-root" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-news-ink/50" data-pesbar-confirm-backdrop></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div role="dialog"
             aria-modal="true"
             aria-labelledby="pesbar-confirm-title"
             class="w-full max-w-md bg-white border-2 border-news-ink shadow-none">
            <div class="px-5 py-4 border-b border-news-line">
                <h3 id="pesbar-confirm-title" class="font-display text-lg font-bold text-news-ink">Konfirmasi</h3>
            </div>
            <div class="px-5 py-4">
                <p id="pesbar-confirm-message" class="text-sm text-news-muted leading-relaxed whitespace-pre-line"></p>
            </div>
            <div class="px-5 py-4 border-t border-news-line flex flex-wrap justify-end gap-2">
                <button type="button"
                        id="pesbar-confirm-cancel"
                        class="px-4 py-2 text-sm font-semibold border border-news-line text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
                    Batal
                </button>
                <button type="button"
                        id="pesbar-confirm-ok"
                        class="px-4 py-2 text-sm font-semibold bg-news-ink text-white hover:bg-news-accent transition-colors">
                    Ya, lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<div id="pesbar-toast-root" class="fixed top-4 right-4 z-[110] flex flex-col gap-2 pointer-events-none max-w-sm w-[calc(100%-2rem)]"></div>

<script>
(function () {
    if (window.pesbarConfirm) return;

    var root = document.getElementById('pesbar-confirm-root');
    var msgEl = document.getElementById('pesbar-confirm-message');
    var titleEl = document.getElementById('pesbar-confirm-title');
    var okBtn = document.getElementById('pesbar-confirm-ok');
    var cancelBtn = document.getElementById('pesbar-confirm-cancel');
    var backdrop = root ? root.querySelector('[data-pesbar-confirm-backdrop]') : null;
    var resolveFn = null;
    var mode = 'confirm';

    function close(result) {
        if (!root) return;
        root.classList.add('hidden');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        if (resolveFn) {
            var fn = resolveFn;
            resolveFn = null;
            fn(result);
        }
    }

    function open(message, options) {
        options = options || {};
        mode = options.mode || 'confirm';
        if (!root) {
            return Promise.resolve(mode === 'alert' ? true : window.confirm(message));
        }
        return new Promise(function (resolve) {
            resolveFn = resolve;
            titleEl.textContent = options.title || (mode === 'alert' ? 'Info' : 'Konfirmasi');
            msgEl.textContent = message || '';
            okBtn.textContent = options.okText || (mode === 'alert' ? 'Tutup' : 'Ya, lanjutkan');
            cancelBtn.classList.toggle('hidden', mode === 'alert');
            if (options.danger) {
                okBtn.className = 'px-4 py-2 text-sm font-semibold bg-news-accent text-white hover:bg-news-ink transition-colors';
            } else {
                okBtn.className = 'px-4 py-2 text-sm font-semibold bg-news-ink text-white hover:bg-news-accent transition-colors';
            }
            root.classList.remove('hidden');
            root.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            okBtn.focus();
        });
    }

    window.pesbarConfirm = function (message, options) {
        return open(message, Object.assign({ mode: 'confirm' }, options || {}));
    };

    window.pesbarAlert = function (message, options) {
        return open(message, Object.assign({ mode: 'alert', title: 'Info' }, options || {}));
    };

    window.pesbarToast = function (message, type) {
        var host = document.getElementById('pesbar-toast-root');
        if (!host) {
            window.pesbarAlert(message);
            return;
        }
        var el = document.createElement('div');
        var isError = type === 'error';
        el.className = 'pointer-events-auto bg-white border border-news-line border-l-4 p-3 shadow-sm animate-slide-down ' +
            (isError ? 'border-l-news-accent' : 'border-l-news-ink');
        el.innerHTML = '<p class="text-sm font-semibold text-news-ink">' + (isError ? 'Error' : 'Berhasil') + '</p>' +
            '<p class="text-sm text-news-muted mt-0.5"></p>';
        el.querySelectorAll('p')[1].textContent = message;
        host.appendChild(el);
        setTimeout(function () {
            el.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-6px)';
            setTimeout(function () { el.remove(); }, 350);
        }, 3500);
    };

    window.pesbarConfirmSubmit = function (form, message, options) {
        if (!form) return;
        window.pesbarConfirm(message, Object.assign({ danger: true }, options || {})).then(function (ok) {
            if (ok) HTMLFormElement.prototype.submit.call(form);
        });
    };

    window.pesbarConfirmForm = function (event, message, options) {
        event.preventDefault();
        var form = event.target;
        window.pesbarConfirm(message, Object.assign({ danger: true }, options || {})).then(function (ok) {
            if (ok) HTMLFormElement.prototype.submit.call(form);
        });
        return false;
    };

    if (okBtn) {
        okBtn.addEventListener('click', function () { close(true); });
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () { close(false); });
    }
    if (backdrop) {
        backdrop.addEventListener('click', function () { close(mode === 'alert'); });
    }
    document.addEventListener('keydown', function (e) {
        if (!root || root.classList.contains('hidden')) return;
        if (e.key === 'Escape') {
            e.preventDefault();
            close(mode === 'alert');
        }
    });
})();
</script>
