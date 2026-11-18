/*
 * "¿Dónde hay?" (gerente y cajero) — como en farmacias: en qué OTRAS sucursales del negocio hay un producto.
 *
 *   NexoraDisponibilidad.abrir({ url: '/cajero/productos/12/disponibilidad', producto: 'Tenis Running' });
 *
 * El servidor (Producto::disponibilidad) solo devuelve sucursales activas del mismo negocio que tienen el
 * producto activo, con dirección, teléfono y piezas. Aquí solo se pinta una ventana (CSS propio con las
 * variables del tema del panel; textos es/en según <html lang>). Se cierra con Esc, con la X o tocando afuera.
 */
(function () {
  'use strict';

  const TEXTOS = {
    es: {
      titulo: '¿Dónde hay "{producto}"?', cargando: 'Buscando en tus otras sucursales…', cerrar: 'Cerrar',
      vacio: 'Ninguna otra sucursal lo tiene disponible.', error: 'No se pudo consultar la disponibilidad.',
      piezas: n => (n === 1 ? '1 pieza' : `${n.toLocaleString('es-MX')} piezas`), agotado: 'Agotado', telefono: 'Tel.',
      nota: 'Existencias en este momento; pueden cambiar si esa sucursal vende.',
    },
    en: {
      titulo: 'Where is "{producto}" available?', cargando: 'Checking your other branches…', cerrar: 'Close',
      vacio: 'No other branch has it available.', error: 'Availability could not be checked.',
      piezas: n => (n === 1 ? '1 unit' : `${n.toLocaleString('en-US')} units`), agotado: 'Out of stock', telefono: 'Phone',
      nota: 'Stock right now; it may change if that branch sells.',
    },
  };
  const idioma = (document.documentElement.lang || 'es').slice(0, 2) === 'en' ? 'en' : 'es';
  const t = clave => TEXTOS[idioma][clave];

  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  const CSS = `
  .disp-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
  .disp-caja { background: var(--card-bg); color: var(--text); border: 1px solid var(--border); border-radius: 12px; width: 100%; max-width: 460px; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
  .disp-cabeza { display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem 1.1rem 0.6rem; }
  .disp-cabeza h3 { flex: 1; font-size: 1rem; margin: 0; line-height: 1.35; overflow-wrap: anywhere; }
  .disp-cerrar { background: none; border: 1px solid var(--border); color: var(--text); border-radius: 8px; width: 30px; height: 30px; cursor: pointer; font-size: 1.1rem; line-height: 1; flex-shrink: 0; }
  .disp-cerrar:hover { border-color: var(--accent); color: var(--accent); }
  .disp-cuerpo { padding: 0 1.1rem 1rem; overflow-y: auto; }
  .disp-lista { list-style: none; margin: 0; padding: 0; }
  .disp-lista li { display: flex; gap: 0.75rem; align-items: center; padding: 0.7rem 0; border-top: 1px solid var(--border); }
  .disp-lista li:first-child { border-top: none; }
  .disp-datos { flex: 1; min-width: 0; }
  .disp-sucursal { font-weight: 600; font-size: 0.92rem; }
  .disp-linea { font-size: 0.8rem; color: var(--muted); overflow-wrap: anywhere; }
  .disp-linea a { color: var(--accent); text-decoration: none; }
  .disp-piezas { white-space: nowrap; font-size: 0.8rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: var(--accent-bg-light, rgba(234,88,12,0.12)); color: var(--accent); }
  .disp-piezas.agotado { background: var(--danger-bg, rgba(220,38,38,0.12)); color: var(--danger-text); }
  .disp-mensaje { font-size: 0.9rem; color: var(--muted); padding: 0.5rem 0 0.25rem; }
  .disp-nota { font-size: 0.72rem; color: var(--muted); margin-top: 0.6rem; }`;

  function ponerCss() {
    if (document.getElementById('disp-css')) return;
    const estilo = document.createElement('style');
    estilo.id = 'disp-css';
    estilo.textContent = CSS;
    document.head.appendChild(estilo);
  }

  let abierta = null;   // { overlay, alTeclear, regresarA }

  function cerrar() {
    if (!abierta) return;
    document.removeEventListener('keydown', abierta.alTeclear);
    abierta.overlay.remove();
    abierta.regresarA?.focus?.();
    abierta = null;
  }

  function pintarLista(cuerpo, sucursales) {
    if (!sucursales.length) {
      cuerpo.innerHTML = `<p class="disp-mensaje">${esc(t('vacio'))}</p>`;
      return;
    }
    cuerpo.innerHTML = `<ul class="disp-lista">${sucursales.map(s => `
      <li>
        <div class="disp-datos">
          <div class="disp-sucursal">${esc(s.sucursal)}</div>
          ${s.direccion ? `<div class="disp-linea">${esc(s.direccion)}</div>` : ''}
          ${s.contacto ? `<div class="disp-linea">${esc(t('telefono'))} <a href="tel:${esc(s.contacto)}">${esc(s.contacto)}</a></div>` : ''}
        </div>
        <span class="disp-piezas ${s.existencias > 0 ? '' : 'agotado'}">${esc(s.existencias > 0 ? t('piezas')(s.existencias) : t('agotado'))}</span>
      </li>`).join('')}</ul>
      <p class="disp-nota">${esc(t('nota'))}</p>`;
  }

  async function abrir({ url, producto }) {
    cerrar();
    ponerCss();

    const overlay = document.createElement('div');
    overlay.className = 'disp-overlay';
    overlay.innerHTML = `
      <div class="disp-caja" role="dialog" aria-modal="true" aria-labelledby="disp-titulo">
        <div class="disp-cabeza">
          <h3 id="disp-titulo">${esc(t('titulo').replace('{producto}', producto))}</h3>
          <button type="button" class="disp-cerrar" aria-label="${esc(t('cerrar'))}" title="${esc(t('cerrar'))}">&times;</button>
        </div>
        <div class="disp-cuerpo" aria-live="polite"><p class="disp-mensaje">${esc(t('cargando'))}</p></div>
      </div>`;
    const alTeclear = e => { if (e.key === 'Escape') cerrar(); };
    abierta = { overlay, alTeclear, regresarA: document.activeElement };

    overlay.addEventListener('click', e => { if (e.target === overlay) cerrar(); });
    overlay.querySelector('.disp-cerrar').addEventListener('click', cerrar);
    document.addEventListener('keydown', alTeclear);
    document.body.appendChild(overlay);
    overlay.querySelector('.disp-cerrar').focus();

    const cuerpo = overlay.querySelector('.disp-cuerpo');
    try {
      const resp = await fetch(url, { headers: { Accept: 'application/json' } });
      const datos = await resp.json().catch(() => null);
      if (!resp.ok || !Array.isArray(datos)) throw new Error(datos?.message || t('error'));
      if (abierta?.overlay === overlay) pintarLista(cuerpo, datos);
    } catch (e) {
      if (abierta?.overlay === overlay) cuerpo.innerHTML = `<p class="disp-mensaje">${esc(e.message || t('error'))}</p>`;
    }
  }

  window.NexoraDisponibilidad = { abrir, cerrar };
})();
