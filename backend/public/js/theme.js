(function () {
    var KEY = 'theme';

    // Aplica el tema guardado (o el del sistema) antes de pintar la página
    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) {}
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);

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
    });
})();