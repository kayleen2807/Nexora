/*
 * Requisitos de contraseña en vivo (registro, invitación, restablecer contraseña y Configuración).
 *
 * Mismas reglas que el servidor (RegisterController, PasswordResetController, UserInvitationController
 * y ConfiguracionController): mínimo 8 caracteres, una mayúscula, un número y un carácter especial.
 * Este archivo solo AYUDA a verlo; quien decide siempre es el servidor.
 *
 * Uso automático en una página: debajo del campo de contraseña
 *     <div data-requisitos-de="password"></div>
 * y debajo del campo de confirmación
 *     <div data-coincide-con="password" data-confirmacion="password_confirmation"></div>
 * Uso manual (paneles, donde el formulario se pinta con JS):
 *     NexoraClave.montar({ clave: 'idCampo', confirmacion: 'idCampo2', requisitos: elDiv, coincide: elDiv2 })
 */
(function () {
    var TEXTOS = {
        es: {
            largo: 'Mínimo 8 caracteres', mayuscula: 'Una letra mayúscula', numero: 'Un número',
            especial: 'Un carácter especial (!@#$%…)', coinciden: 'Las contraseñas coinciden',
            debil: 'Débil', media: 'Media', fuerte: 'Fuerte', fuerza: 'Seguridad',
        },
        en: {
            largo: 'At least 8 characters', mayuscula: 'One uppercase letter', numero: 'One number',
            especial: 'One special character (!@#$%…)', coinciden: 'Passwords match',
            debil: 'Weak', media: 'Medium', fuerte: 'Strong', fuerza: 'Strength',
        },
    };

    var REGLAS = [
        ['largo', function (v) { return v.length >= 8; }],
        ['mayuscula', function (v) { return /[A-Z]/.test(v); }],
        ['numero', function (v) { return /\d/.test(v); }],
        ['especial', function (v) { return /[^A-Za-z0-9]/.test(v); }],
    ];

    // Estilos propios (una sola vez por página); usan las variables del tema si existen
    var CSS = '' +
        '.nx-req{margin:4px 0 10px;font-size:12.5px;line-height:1.35;font-family:inherit}' +
        '.nx-req-barra{height:6px;border-radius:99px;background:rgba(128,128,128,.22);overflow:hidden}' +
        '.nx-req-barra i{display:block;height:100%;width:0;border-radius:99px;transition:width .3s ease,background .3s ease}' +
        '.nx-req-nivel{display:flex;justify-content:space-between;margin:4px 0 6px;font-size:11.5px;opacity:.85}' +
        '.nx-req ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:3px 12px}' +
        '.nx-req li{display:flex;align-items:center;gap:7px;color:var(--muted,#6b7280);transition:color .25s ease}' +
        '.nx-req li b{flex:0 0 auto;width:16px;height:16px;border-radius:50%;border:1.5px solid currentColor;display:grid;place-items:center;font-size:10px;line-height:1;transition:all .25s ease}' +
        '.nx-req li b::after{content:"";width:7px;height:4px;border-left:2px solid #fff;border-bottom:2px solid #fff;transform:rotate(-45deg) scale(0);margin-top:-2px;transition:transform .25s cubic-bezier(.3,1.6,.5,1)}' +
        '.nx-req li.ok{color:#16a34a}' +
        '.nx-req li.ok b{background:#16a34a;border-color:#16a34a;animation:nxPop .35s ease}' +
        '.nx-req li.ok b::after{transform:rotate(-45deg) scale(1)}' +
        '.nx-req li.mal{color:#dc2626}' +
        '[data-theme="dark"] .nx-req li.ok{color:#4ade80}[data-theme="dark"] .nx-req li.ok b{background:#22c55e;border-color:#22c55e}' +
        '[data-theme="dark"] .nx-req li.mal{color:#f87171}' +
        '.nx-req.coincide ul{grid-template-columns:1fr}' +
        '@keyframes nxPop{0%{transform:scale(.6)}60%{transform:scale(1.25)}100%{transform:scale(1)}}' +
        '@media (prefers-reduced-motion:reduce){.nx-req *{transition:none!important;animation:none!important}}';

    function estilos() {
        if (document.getElementById('nx-req-css')) return;
        var s = document.createElement('style');
        s.id = 'nx-req-css';
        s.textContent = CSS;
        document.head.appendChild(s);
    }

    function textos() {
        var lang = (document.documentElement.lang || 'es').slice(0, 2);
        return TEXTOS[lang] || TEXTOS.es;
    }

    function item(clave, t) {
        return '<li data-regla="' + clave + '"><b aria-hidden="true"></b><span>' + t[clave] + '</span></li>';
    }

    /* Monta los requisitos para un campo (y, si se da, el "coinciden" para su confirmación). */
    function montar(op) {
        estilos();
        var campo = typeof op.clave === 'string' ? document.getElementById(op.clave) : op.clave;
        var confirmacion = typeof op.confirmacion === 'string' ? document.getElementById(op.confirmacion) : op.confirmacion;
        if (!campo || !op.requisitos) return null;
        var t = textos();

        op.requisitos.classList.add('nx-req');
        op.requisitos.setAttribute('aria-live', 'polite');
        op.requisitos.innerHTML =
            '<div class="nx-req-barra"><i></i></div>' +
            '<div class="nx-req-nivel"><span>' + t.fuerza + '</span><span class="nx-req-txt"></span></div>' +
            '<ul>' + REGLAS.map(function (r) { return item(r[0], t); }).join('') + '</ul>';
        if (op.coincide) {
            op.coincide.classList.add('nx-req', 'coincide');
            op.coincide.innerHTML = '<ul>' + item('coinciden', t) + '</ul>';
        }

        function actualizar() {
            var v = campo.value || '';
            var cumplidas = 0;
            REGLAS.forEach(function (r) {
                var ok = r[1](v);
                if (ok) cumplidas++;
                var li = op.requisitos.querySelector('[data-regla="' + r[0] + '"]');
                li.classList.toggle('ok', ok);
                li.classList.toggle('mal', !ok && v.length > 0);
            });

            // Barra de seguridad (igual que la de Configuración): 12+ caracteres suma un punto extra
            var puntos = cumplidas + (v.length >= 12 ? 1 : 0);
            var nivel = !v ? 0 : puntos <= 2 ? 1 : puntos <= 4 ? 2 : 3;
            var barra = op.requisitos.querySelector('.nx-req-barra i');
            barra.style.width = ['0%', '33%', '66%', '100%'][nivel];
            barra.style.background = ['transparent', '#dc2626', '#f59e0b', '#16a34a'][nivel];
            op.requisitos.querySelector('.nx-req-txt').textContent = nivel ? [null, t.debil, t.media, t.fuerte][nivel] : '';

            if (op.coincide && confirmacion) {
                var c = confirmacion.value || '';
                var li2 = op.coincide.querySelector('li');
                var iguales = c.length > 0 && c === v;
                li2.classList.toggle('ok', iguales);
                li2.classList.toggle('mal', c.length > 0 && !iguales);
            }
        }

        campo.addEventListener('input', actualizar);
        if (confirmacion) confirmacion.addEventListener('input', actualizar);
        actualizar();

        return {
            actualizar: actualizar,
            cumple: function () {
                var v = campo.value || '';
                return REGLAS.every(function (r) { return r[1](v); }) && (!confirmacion || confirmacion.value === v);
            },
        };
    }

    window.NexoraClave = { montar: montar };

    // Montaje automático en las páginas (registro, invitación, restablecer contraseña)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-requisitos-de]').forEach(function (div) {
            var id = div.getAttribute('data-requisitos-de');
            var coincide = document.querySelector('[data-coincide-con="' + id + '"]');
            montar({
                clave: id,
                confirmacion: coincide ? coincide.getAttribute('data-confirmacion') : null,
                requisitos: div,
                coincide: coincide,
            });
        });
    });
})();
