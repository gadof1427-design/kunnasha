/*
 * Kunnasha front-end behaviour (vanilla JS, no dependencies).
 *
 * Everything is driven by data-* attributes in the Blade views, using event
 * delegation, so it also works for cards appended by "load more".
 */

const MAX_FALLBACK = 155;

// Texts in the page's language, from lang/{ar,en}/js.php (see Layout/app.blade.php).
const I18N = (window.kunnasha && window.kunnasha.i18n) || {};
const t = (key, replace = {}) =>
    Object.entries(replace).reduce((text, [name, value]) => text.replace(`:${name}`, value), I18N[key] || key);

/* ------------------------------------------------------------------ */
/* Toast                                                               */
/* ------------------------------------------------------------------ */

let toastRoot = null;

function toast(message, kind = 'ok') {
    if (!toastRoot) {
        toastRoot = document.createElement('div');
        toastRoot.setAttribute('role', 'status');
        toastRoot.setAttribute('aria-live', 'polite');
        toastRoot.className =
            'pointer-events-none fixed inset-x-0 bottom-24 z-[60] flex flex-col items-center gap-2 px-4';
        document.body.appendChild(toastRoot);
    }

    const el = document.createElement('div');
    el.className =
        'kn-toast rounded-full px-5 py-2.5 text-sm font-semibold shadow-lg ' +
        (kind === 'error' ? 'bg-red-700 text-white' : 'bg-[#292929] text-[#fff8cf]');
    el.textContent = message;
    toastRoot.appendChild(el);

    setTimeout(() => {
        el.classList.add('kn-toast-out');
        setTimeout(() => el.remove(), 260);
    }, 2400);
}

/* ------------------------------------------------------------------ */
/* Clipboard                                                           */
/* ------------------------------------------------------------------ */

async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (_) {
        // Fallback for http:// pages and older browsers.
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.cssText = 'position:fixed;top:0;opacity:0;pointer-events:none';
        document.body.appendChild(ta);
        ta.select();
        let ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (_) {
            ok = false;
        }
        ta.remove();
        return ok;
    }
}

function readQuote(card) {
    const d = card.dataset;
    return {
        id: d.quoteId,
        text: d.quoteText || '',
        author: d.quoteAuthor || '',
        by: d.quoteBy || '',
        url: d.quoteUrl || location.href,
        bg: d.quoteBg || '#FFFFFF',
        fg: d.quoteFg || '#292929',
        accent: d.quoteAccent || '#7c0221',
    };
}

function shareText(q) {
    return `«${q.text}»` + (q.author ? `\n— ${q.author}` : '') + `\n\nكُنّاشة\n${q.url}`;
}

/* ------------------------------------------------------------------ */
/* Share a quote as an image (canvas, brand fonts, no libraries)       */
/* ------------------------------------------------------------------ */

const FONT_HEAD = '"Aref Ruqaa", "Noto Naskh Arabic", serif';
const FONT_BODY = '"Amiri", "Noto Naskh Arabic", serif';

function wrapLines(ctx, text, maxWidth) {
    const lines = [];

    text.split(/\r?\n/).forEach((paragraph) => {
        const words = paragraph.split(/\s+/).filter(Boolean);

        if (!words.length) {
            lines.push('');
            return;
        }

        let line = '';
        words.forEach((word) => {
            const test = line ? `${line} ${word}` : word;
            if (!line || ctx.measureText(test).width <= maxWidth) {
                line = test;
            } else {
                lines.push(line);
                line = word;
            }
        });
        lines.push(line);
    });

    return lines;
}

function roundedRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
}

function withAlpha(ctx, alpha, fn) {
    ctx.save();
    ctx.globalAlpha = alpha;
    fn();
    ctx.restore();
}

async function renderQuoteImage(q) {
    // Make sure the Arabic glyphs of the brand fonts are loaded before drawing.
    try {
        await Promise.all([
            document.fonts.load(`700 80px "Aref Ruqaa"`, q.text + q.author + 'كُنّاشة'),
            document.fonts.load(`400 48px "Amiri"`, q.text + q.author),
        ]);
    } catch (_) {
        /* fonts are optional: fall back to system serif */
    }

    const W = 1080;
    const H = 1350; // 4:5, the best portrait ratio for Instagram / WhatsApp status
    const PAD = 110;

    const canvas = document.createElement('canvas');
    canvas.width = W;
    canvas.height = H;
    const ctx = canvas.getContext('2d');

    ctx.direction = 'rtl';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'alphabetic';

    // Background + inner frame
    ctx.fillStyle = q.bg;
    ctx.fillRect(0, 0, W, H);

    withAlpha(ctx, 0.28, () => {
        ctx.strokeStyle = q.accent;
        ctx.lineWidth = 3;
        roundedRect(ctx, 46, 46, W - 92, H - 92, 30);
        ctx.stroke();
    });

    // Opening mark
    ctx.fillStyle = q.accent;
    ctx.font = `700 170px ${FONT_HEAD}`;
    ctx.fillText('«', W / 2, 270);

    // Quote text: shrink until it fits the text box
    const boxTop = 330;
    const boxBottom = 930;
    const maxW = W - PAD * 2;

    let size = 78;
    let lines = [];
    let lineHeight = 0;

    for (; size >= 26; size -= 4) {
        ctx.font = `400 ${size}px ${FONT_BODY}`;
        lines = wrapLines(ctx, q.text, maxW);
        lineHeight = size * 1.9;

        const fitsHeight = lines.length * lineHeight <= boxBottom - boxTop;
        const fitsWidth = lines.every((l) => ctx.measureText(l).width <= maxW);
        if (fitsHeight && fitsWidth) break;
    }

    ctx.fillStyle = q.fg;
    const blockHeight = lines.length * lineHeight;
    const firstBaseline = boxTop + (boxBottom - boxTop - blockHeight) / 2 + size * 1.2;
    lines.forEach((line, i) => ctx.fillText(line, W / 2, firstBaseline + i * lineHeight));

    // Closing mark
    ctx.fillStyle = q.accent;
    ctx.font = `700 120px ${FONT_HEAD}`;
    ctx.fillText('»', W / 2, 1040);

    // Author
    if (q.author) {
        ctx.font = `700 46px ${FONT_BODY}`;
        let author = `— ${q.author}`;
        while (author.length > 4 && ctx.measureText(author).width > maxW) {
            author = author.slice(0, -2) + '…';
        }
        ctx.fillStyle = q.accent;
        ctx.fillText(author, W / 2, 1120);
    }

    // Footer: wordmark + site
    withAlpha(ctx, 0.35, () => {
        ctx.fillStyle = q.fg;
        ctx.fillRect(W / 2 - 60, 1160, 120, 2);
    });
    withAlpha(ctx, 0.95, () => {
        ctx.fillStyle = q.fg;
        ctx.font = `700 54px ${FONT_HEAD}`;
        ctx.fillText('كُنّاشة', W / 2, 1236);
    });
    withAlpha(ctx, 0.7, () => {
        ctx.fillStyle = q.fg;
        ctx.direction = 'ltr';
        ctx.font = `500 26px ui-sans-serif, system-ui, sans-serif`;
        ctx.fillText(location.host, W / 2, 1282);
    });

    return new Promise((resolve, reject) =>
        canvas.toBlob((blob) => (blob ? resolve(blob) : reject(new Error('toBlob'))), 'image/png')
    );
}

function downloadBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 4000);
}

async function shareQuoteImage(card, button) {
    const q = readQuote(card);

    button.disabled = true;
    button.setAttribute('aria-busy', 'true');

    try {
        const blob = await renderQuoteImage(q);
        const file = new File([blob], `kunnasha-${q.id}.png`, { type: 'image/png' });

        // On phones, open the native share sheet (WhatsApp, Instagram, ...).
        const isTouch = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
        if (isTouch && navigator.canShare && navigator.canShare({ files: [file] })) {
            try {
                await navigator.share({ files: [file], title: 'كُنّاشة' });
                return;
            } catch (err) {
                if (err && err.name === 'AbortError') return; // user closed the sheet
            }
        }

        downloadBlob(blob, file.name);
        toast(t('image_saved'));
    } catch (_) {
        toast(t('image_failed'), 'error');
    } finally {
        button.disabled = false;
        button.removeAttribute('aria-busy');
    }
}

/* ------------------------------------------------------------------ */
/* Card actions (copy, link, image, edit)                              */
/* ------------------------------------------------------------------ */

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;

    const card = button.closest('[data-quote-card]');
    if (!card) return;

    switch (button.dataset.action) {
        case 'copy': {
            const ok = await copyText(shareText(readQuote(card)));
            toast(ok ? t('copied') : t('copy_failed'), ok ? 'ok' : 'error');
            break;
        }
        case 'copy-link': {
            const ok = await copyText(readQuote(card).url);
            toast(ok ? t('link_copied') : t('copy_failed'), ok ? 'ok' : 'error');
            break;
        }
        case 'share-image':
            await shareQuoteImage(card, button);
            break;
        case 'toggle-edit': {
            const panel = card.querySelector('[data-edit-panel]');
            if (!panel) break;
            const open = panel.classList.toggle('hidden') === false;
            card.querySelectorAll('[data-action="toggle-edit"][aria-expanded]').forEach((b) =>
                b.setAttribute('aria-expanded', open ? 'true' : 'false')
            );
            if (open) {
                const field = panel.querySelector('textarea');
                updateCounter(field);
                updatePreview(panel.querySelector('[data-preview-root]'));
                field && field.focus();
            }
            break;
        }
    }
});

/* ------------------------------------------------------------------ */
/* Confirm dialogs + double-submit protection                          */
/* ------------------------------------------------------------------ */

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
        return;
    }

    if (form.hasAttribute('data-once') && !event.defaultPrevented) {
        const submit = form.querySelector('[type="submit"]');
        if (submit) {
            submit.dataset.label = submit.textContent;
            // Disable on the next tick so the form value is still submitted.
            setTimeout(() => {
                submit.disabled = true;
                submit.textContent = t('saving');
            }, 0);
        }
    }
});

// Coming back with the browser's back button must not leave buttons disabled.
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;
    document.querySelectorAll('form[data-once] [type="submit"]').forEach((b) => {
        b.disabled = false;
        if (b.dataset.label) b.textContent = b.dataset.label;
    });
});

/* ------------------------------------------------------------------ */
/* Character counter                                                   */
/* ------------------------------------------------------------------ */

function charCount(value) {
    return Array.from(value).length; // code points, like PHP's mb_strlen
}

function updateCounter(field) {
    if (!field || !field.dataset.counter) return;

    const out = document.getElementById(field.dataset.counter);
    if (!out) return;

    const max = parseInt(field.getAttribute('maxlength'), 10) || MAX_FALLBACK;
    const len = charCount(field.value);
    const left = max - len;

    out.textContent = `${len} / ${max}`;
    out.classList.toggle('text-[#707070]', left > 20);
    out.classList.toggle('text-[#b6233e]', left <= 20 && left > 0);
    out.classList.toggle('font-bold', left <= 20);
    out.classList.toggle('text-red-700', left <= 0);
}

document.addEventListener('input', (event) => {
    if (event.target.matches && event.target.matches('[data-counter]')) {
        updateCounter(event.target);
    }
});

/* ------------------------------------------------------------------ */
/* Live preview (composer + edit panels)                               */
/* ------------------------------------------------------------------ */

// Any [data-preview-root] (see components/quote-fields.blade.php) mirrors its
// text, author and colour into its [data-preview] card while the user types.
function updatePreview(root) {
    const preview = root && root.querySelector('[data-preview]');
    if (!preview) return;

    const checked = root.querySelector('[data-color-radio]:checked');
    const textEl = preview.querySelector('[data-preview-text]');
    const authorEl = preview.querySelector('[data-preview-author]');

    if (checked) {
        preview.style.backgroundColor = checked.dataset.bg;
        preview.style.color = checked.dataset.fg;
        preview.style.setProperty('--kn-accent', checked.dataset.accent);
        authorEl.style.color = checked.dataset.accent;
    }

    const input = root.querySelector('[data-preview-input]');
    const text = input ? input.value.trim() : '';
    textEl.textContent = text || t('preview_placeholder');
    textEl.style.opacity = text ? '1' : '0.55';

    const authorInput = root.querySelector('[data-preview-author-input]');
    const author = authorInput ? authorInput.value.trim() : '';
    authorEl.textContent = author ? `— ${author}` : '';
}

['input', 'change'].forEach((type) =>
    document.addEventListener(type, (event) => {
        const root = event.target.closest && event.target.closest('[data-preview-root]');
        if (root) updatePreview(root);
    })
);

document.querySelectorAll('[data-preview-root]').forEach(updatePreview);

/* ------------------------------------------------------------------ */
/* Composer modal                                                      */
/* ------------------------------------------------------------------ */

const modal = document.querySelector('[data-composer-modal]');
let lastTrigger = null;

function openComposer(trigger) {
    if (!modal) return;
    lastTrigger = trigger || null;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    const input = modal.querySelector('[data-preview-input]');
    updateCounter(input);
    updatePreview(modal.querySelector('[data-preview-root]'));
    setTimeout(() => input.focus(), 30);
}

function closeComposer() {
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    if (lastTrigger && document.contains(lastTrigger)) lastTrigger.focus();
}

if (modal) {
    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-composer]');
        if (opener) {
            event.preventDefault();
            openComposer(opener);
            return;
        }

        if (event.target.closest('[data-close-composer]') || event.target === modal) {
            closeComposer();
        }
    });

    // Esc closes; Tab stays inside the dialog.
    document.addEventListener('keydown', (event) => {
        if (modal.classList.contains('hidden')) return;

        if (event.key === 'Escape') {
            closeComposer();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusable = Array.from(
            modal.querySelectorAll('button, [href], input:not([class~="sr-only"]), textarea, select, [tabindex]:not([tabindex="-1"])')
        ).filter((el) => !el.disabled && el.offsetParent !== null);

        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    // Server re-opened it because of a validation error: lock scroll + sync state.
    if (!modal.classList.contains('hidden')) {
        document.body.classList.add('overflow-hidden');
        const input = modal.querySelector('[data-preview-input]');
        updateCounter(input);
        input.focus();
    }
}

// Counters for edit panels that start open (after a validation error).
document.querySelectorAll('[data-edit-panel]:not(.hidden) textarea[data-counter]').forEach(updateCounter);

/* ------------------------------------------------------------------ */
/* "Load more" (falls back to a normal link without JS)                */
/* ------------------------------------------------------------------ */

document.addEventListener('click', async (event) => {
    const link = event.target.closest('[data-load-more]');
    if (!link) return;

    event.preventDefault();
    if (link.dataset.loading) return;

    const grid = document.querySelector('[data-quotes-grid]');
    if (!grid) {
        window.location.href = link.href;
        return;
    }

    const label = link.textContent;
    link.dataset.loading = '1';
    link.setAttribute('aria-busy', 'true');
    link.textContent = t('loading');

    try {
        const response = await fetch(link.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
        const incoming = doc.querySelector('[data-quotes-grid]');
        if (!incoming) throw new Error('unexpected response');

        Array.from(incoming.children).forEach((node, i) => {
            node.style.setProperty('--i', String(i));
            grid.appendChild(document.importNode(node, true));
        });

        // The "previous page" link no longer makes sense once more is loaded.
        document.querySelectorAll('[data-prev-page]').forEach((el) => el.remove());

        const next = doc.querySelector('[data-load-more]');
        if (next) {
            link.href = next.href;
            link.textContent = label;
            link.removeAttribute('aria-busy');
            delete link.dataset.loading;
        } else {
            const nav = link.closest('[data-pagination]');
            (nav || link).remove();
        }
    } catch (_) {
        window.location.href = link.href; // let the browser load the next page normally
    }
});

/* ------------------------------------------------------------------ */
/* Profile photo: show the chosen picture before saving                */
/* ------------------------------------------------------------------ */

document.addEventListener('change', (event) => {
    const input = event.target.closest && event.target.closest('[data-photo-input]');
    const file = input && input.files && input.files[0];
    if (!file) return;

    const form = input.closest('form');
    const name = form.querySelector('[data-photo-name]');
    if (name) name.textContent = t('photo_chosen', { name: file.name });

    const avatar = form.querySelector('[data-photo-preview] > span');
    if (avatar && file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = '';
        img.className = 'h-full w-full object-cover';
        avatar.replaceChildren(img);
    }
});
