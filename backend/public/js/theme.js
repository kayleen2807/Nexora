(function () {
    var KEY = 'theme';
    var TEXT_SIZE_KEY = 'text-size';
    var MIN_TEXT_SIZE = 100;
    var MAX_TEXT_SIZE = 130;
    var TEXT_SIZE_STEP = 5;
    var textStyleRecords = [];
    var textScaleRefreshQueued = false;

    // Aplica el tema guardado (o el del sistema) antes de pintar la página
    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) {}
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);

    function readTextSize() {
        var savedSize = null;
        try { savedSize = Number(localStorage.getItem(TEXT_SIZE_KEY)); } catch (e) {}
        if (!Number.isFinite(savedSize)) return MIN_TEXT_SIZE;
        return Math.min(MAX_TEXT_SIZE, Math.max(MIN_TEXT_SIZE, savedSize));
    }

    function restoreTextStyles() {
        textStyleRecords.forEach(function (record) {
            record.element.style.setProperty('font-size', record.inlineFontSize, record.inlineFontSizePriority);
            record.element.style.setProperty('line-height', record.inlineLineHeight, record.inlineLineHeightPriority);
        });
        textStyleRecords = [];
    }

    function captureTextStyles() {
        if (!document.body) return;
        restoreTextStyles();

        var elements = [document.body].concat(Array.prototype.slice.call(document.body.querySelectorAll('*')));
        elements.forEach(function (element) {
            if (element.closest('[data-text-size-widget]')) return;

            var computed = window.getComputedStyle(element);
            var fontSize = parseFloat(computed.fontSize);
            if (!Number.isFinite(fontSize)) return;

            var record = {
                element: element,
                fontSize: fontSize,
                lineHeight: computed.lineHeight,
                inlineFontSize: element.style.getPropertyValue('font-size'),
                inlineFontSizePriority: element.style.getPropertyPriority('font-size'),
                inlineLineHeight: element.style.getPropertyValue('line-height'),
                inlineLineHeightPriority: element.style.getPropertyPriority('line-height')
            };
            textStyleRecords.push(record);
        });
    }

    function renderTextScale(scale) {
        textStyleRecords.forEach(function (record) {
            record.element.style.setProperty('font-size', (record.fontSize * scale) + 'px', 'important');
            if (/^-?\d+(?:\.\d+)?px$/.test(record.lineHeight)) {
                record.element.style.setProperty('line-height', (parseFloat(record.lineHeight) * scale) + 'px', 'important');
            }
        });
    }

    function applyTextSize(size) {
        var clampedSize = Math.min(MAX_TEXT_SIZE, Math.max(MIN_TEXT_SIZE, size));
        if (document.body) renderTextScale(clampedSize / 100);
        try { localStorage.setItem(TEXT_SIZE_KEY, String(clampedSize)); } catch (e) {}
        return clampedSize;
    }

    function scheduleTextScaleRefresh() {
        if (textScaleRefreshQueued) return;
        textScaleRefreshQueued = true;
        window.requestAnimationFrame(function () {
            textScaleRefreshQueued = false;
            captureTextStyles();
            renderTextScale(readTextSize() / 100);
        });
    }

    // Mantiene la preferencia entre páginas y la aplica al terminar de cargar el DOM.
    var initialTextSize = readTextSize();

    // Conecta el botón de tema (login/registro/recuperación usan #theme-toggle; términos y privacidad usan #legal-theme)
    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('#theme-toggle, #legal-theme');
        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var root = document.documentElement;
                var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', next);
                try { localStorage.setItem(KEY, next); } catch (e) {}
            });
        });

        // Control flotante de tamaño del texto, disponible en todas las pantallas.
        if (!document.querySelector('[data-text-size-widget]')) {
            var isEnglish = (document.documentElement.lang || '').toLowerCase().startsWith('en');
            var labels = isEnglish
                ? { title: 'Text size', decrease: 'Decrease text size', increase: 'Increase text size', reset: 'Reset', open: 'Change text size' }
                : { title: 'Tamaño del texto', decrease: 'Disminuir tamaño del texto', increase: 'Aumentar tamaño del texto', reset: 'Restablecer', open: 'Cambiar tamaño del texto' };

            var style = document.createElement('style');
            style.textContent = [
                '[data-text-size-widget]{position:fixed;right:20px;bottom:20px;z-index:999;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;color:#1f2937}',
                '[data-text-size-widget] *{box-sizing:border-box}',
                '[data-text-size-toggle]{min-height:48px;padding:0 16px;border:1px solid #d1d5db;border-radius:999px;background:#fff;color:#1f2937;box-shadow:0 4px 16px rgba(0,0,0,.18);font:600 14px/1.2 system-ui,-apple-system,"Segoe UI",sans-serif;cursor:pointer;display:flex;align-items:center;gap:9px}',
                '[data-text-size-toggle] span:first-child{font-size:19px;font-weight:750;letter-spacing:-1px}',
                '[data-text-size-widget] button:focus-visible,[data-text-size-widget] input:focus-visible{outline:3px solid #f59e0b;outline-offset:3px}',
                '[data-text-size-panel]{position:absolute;right:0;bottom:58px;width:min(290px,calc(100vw - 32px));padding:16px;border:1px solid #d1d5db;border-radius:16px;background:#fff;box-shadow:0 8px 28px rgba(0,0,0,.2)}',
                '[data-text-size-panel][hidden]{display:none}',
                '[data-text-size-heading]{margin:0 0 14px;font-size:16px;font-weight:700;color:#1f2937}',
                '[data-text-size-controls]{display:flex;align-items:center;justify-content:space-between;gap:10px}',
                '[data-text-size-controls] button{width:44px;height:44px;border:1px solid #d1d5db;border-radius:10px;background:#f9fafb;color:#1f2937;font:700 22px/1 system-ui,sans-serif;cursor:pointer}',
                '[data-text-size-value]{min-width:54px;text-align:center;font-size:14px;font-weight:700;font-variant-numeric:tabular-nums}',
                '[data-text-size-range]{width:100%;margin:14px 0 8px;accent-color:#e87916;cursor:pointer}',
                '[data-text-size-reset]{display:block;margin:2px auto 0;padding:6px 10px;border:0;background:transparent;color:#92400e;font:600 13px system-ui,sans-serif;text-decoration:underline;cursor:pointer}',
                ':root[data-theme="dark"] [data-text-size-widget]{color:#f3f4f6}',
                ':root[data-theme="dark"] [data-text-size-toggle],:root[data-theme="dark"] [data-text-size-panel]{background:#1f2937;color:#f3f4f6;border-color:#4b5563}',
                ':root[data-theme="dark"] [data-text-size-heading]{color:#f3f4f6}',
                ':root[data-theme="dark"] [data-text-size-controls] button{background:#374151;color:#f3f4f6;border-color:#4b5563}',
                ':root[data-theme="dark"] [data-text-size-reset]{color:#fdba74}',
                '@media(max-width:480px){[data-text-size-widget]{right:12px;bottom:12px}[data-text-size-toggle]{min-height:46px;padding:0 13px}}',
                '@media(prefers-reduced-motion:reduce){[data-text-size-widget] *{scroll-behavior:auto!important;transition:none!important}}'
            ].join('');
            document.head.appendChild(style);

            var widget = document.createElement('div');
            widget.setAttribute('data-text-size-widget', '');

            var panel = document.createElement('section');
            panel.setAttribute('data-text-size-panel', '');
            panel.hidden = true;
            panel.setAttribute('aria-label', labels.title);

            var heading = document.createElement('h2');
            heading.setAttribute('data-text-size-heading', '');
            heading.textContent = labels.title;

            var controls = document.createElement('div');
            controls.setAttribute('data-text-size-controls', '');

            var decrease = document.createElement('button');
            decrease.type = 'button';
            decrease.textContent = 'A−';
            decrease.setAttribute('aria-label', labels.decrease);

            var value = document.createElement('output');
            value.setAttribute('data-text-size-value', '');
            value.setAttribute('aria-live', 'polite');

            var increase = document.createElement('button');
            increase.type = 'button';
            increase.textContent = 'A+';
            increase.setAttribute('aria-label', labels.increase);

            var range = document.createElement('input');
            range.type = 'range';
            range.min = String(MIN_TEXT_SIZE);
            range.max = String(MAX_TEXT_SIZE);
            range.step = String(TEXT_SIZE_STEP);
            range.setAttribute('data-text-size-range', '');
            range.setAttribute('aria-label', labels.title);

            var reset = document.createElement('button');
            reset.type = 'button';
            reset.setAttribute('data-text-size-reset', '');
            reset.textContent = labels.reset;

            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.setAttribute('data-text-size-toggle', '');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', labels.open);
            toggle.innerHTML = '<span aria-hidden="true">Aa</span><span>' + labels.title + '</span>';

            function updateControls(size) {
                var currentSize = applyTextSize(size);
                value.textContent = currentSize + '%';
                range.value = String(currentSize);
                decrease.disabled = currentSize <= MIN_TEXT_SIZE;
                increase.disabled = currentSize >= MAX_TEXT_SIZE;
                decrease.style.opacity = decrease.disabled ? '.5' : '1';
                increase.style.opacity = increase.disabled ? '.5' : '1';
            }

            function setOpen(open) {
                panel.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            controls.append(decrease, value, increase);
            panel.append(heading, controls, range, reset);
            widget.append(panel, toggle);
            // Se deja fuera del contenido ampliado para que el control conserve
            // su tamaño y permanezca fijo en la pantalla.
            document.documentElement.appendChild(widget);
            captureTextStyles();
            updateControls(initialTextSize);

            // Reaplica el ajuste a filas, mensajes y otros elementos que se
            // agreguen después de cargar la pantalla.
            if (window.MutationObserver) {
                var textScaleObserver = new MutationObserver(function (mutations) {
                    if (mutations.some(function (mutation) { return mutation.addedNodes.length > 0; })) {
                        scheduleTextScaleRefresh();
                    }
                });
                textScaleObserver.observe(document.body, { childList: true, subtree: true });
            }
            window.addEventListener('resize', scheduleTextScaleRefresh);

            toggle.addEventListener('click', function () {
                setOpen(panel.hidden);
            });
            decrease.addEventListener('click', function () {
                updateControls(Number(range.value) - TEXT_SIZE_STEP);
            });
            increase.addEventListener('click', function () {
                updateControls(Number(range.value) + TEXT_SIZE_STEP);
            });
            range.addEventListener('input', function () {
                updateControls(Number(range.value));
            });
            reset.addEventListener('click', function () {
                updateControls(MIN_TEXT_SIZE);
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !panel.hidden) {
                    setOpen(false);
                    toggle.focus();
                }
            });
            document.addEventListener('click', function (event) {
                if (!widget.contains(event.target) && !panel.hidden) setOpen(false);
            });
        }
    });
})();
