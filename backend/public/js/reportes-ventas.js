/*
 * Reportes de ventas (admin y gerente) — un solo archivo para los dos paneles.
 *
 *   const rv = NexoraReportesVentas.montar({
 *     contenedor: document.getElementById('rvAdmin'),
 *     url: '/administrador/reportes-ventas',
 *     sucursales: () => [{ id: 1, nombre: 'Centro' }],   // solo admin (el gerente no elige sucursal)
 *     dinero: n => '$1,234.50 MXN',                      // el formato del panel
 *     toast: (mensaje, tipo) => …,                       // mostrarToast del panel
 *   });
 *   rv.mostrar();   // al entrar a la sección (la primera vez carga Chart.js y pide el mes en curso)
 *
 * Las cifras vienen del servidor (ReporteVentasService): aquí solo se pintan. Chart.js se carga del CDN SOLO
 * cuando se abre la sección; si no carga (sin internet), las tablas se ven igual y se avisa.
 */
(function () {
  'use strict';

  const CHART_URL = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';

  const TEXTOS = {
    es: {
      periodo: 'Periodo', hoy: 'Hoy', ult7: 'Últimos 7 días', esteMes: 'Este mes', mesPasado: 'Mes pasado', ult30: 'Últimos 30 días',
      esteAnio: 'Este año', personalizado: 'Personalizado', desde: 'Desde', hasta: 'Hasta', sucursal: 'Sucursal', todas: 'Todas las sucursales',
      generar: 'Generar', exportar: 'Exportar CSV', imprimir: 'Imprimir / PDF', cargando: 'Cargando…',
      neto: 'Ventas netas', ventas: 'Número de ventas', ticket: 'Ticket promedio', devoluciones: 'Devoluciones y cancelaciones',
      porDia: 'Ventas por día', porMetodo: 'Por método de pago', porHora: 'Ventas por hora', topProductos: 'Productos más vendidos',
      porImporte: 'Por importe', porCantidad: 'Por cantidad', porCajero: 'Ventas por cajero',
      producto: 'Producto', cantidad: 'Cantidad', importe: 'Importe', cajero: 'Cajero', participacion: 'Participación',
      sinVentas: 'No hay ventas en este periodo.', sinGraficas: 'No se pudieron cargar las gráficas (¿sin internet?). Las tablas sí están completas.',
      errorCargar: 'No se pudo cargar el reporte.', fechasInvalidas: 'Elige la fecha "desde" y la fecha "hasta".',
      periodoTexto: 'Del {desde} al {hasta}', notaNeto: 'Cifras netas: ventas menos devoluciones y cancelaciones.', hora: 'Hora',
      csvResumen: 'Resumen', csvConcepto: 'Concepto', csvValor: 'Valor', csvDia: 'Día', csvMetodo: 'Método de pago', csvBruto: 'Ventas brutas',
    },
    en: {
      periodo: 'Period', hoy: 'Today', ult7: 'Last 7 days', esteMes: 'This month', mesPasado: 'Last month', ult30: 'Last 30 days',
      esteAnio: 'This year', personalizado: 'Custom', desde: 'From', hasta: 'To', sucursal: 'Branch', todas: 'All branches',
      generar: 'Generate', exportar: 'Export CSV', imprimir: 'Print / PDF', cargando: 'Loading…',
      neto: 'Net sales', ventas: 'Number of sales', ticket: 'Average ticket', devoluciones: 'Returns and cancellations',
      porDia: 'Sales per day', porMetodo: 'By payment method', porHora: 'Sales by hour', topProductos: 'Best-selling products',
      porImporte: 'By amount', porCantidad: 'By quantity', porCajero: 'Sales by cashier',
      producto: 'Product', cantidad: 'Quantity', importe: 'Amount', cajero: 'Cashier', participacion: 'Share',
      sinVentas: 'There are no sales in this period.', sinGraficas: 'The charts could not be loaded (no internet?). The tables are complete.',
      errorCargar: 'The report could not be loaded.', fechasInvalidas: 'Choose the "from" and "to" dates.',
      periodoTexto: 'From {desde} to {hasta}', notaNeto: 'Net figures: sales minus returns and cancellations.', hora: 'Hour',
      csvResumen: 'Summary', csvConcepto: 'Item', csvValor: 'Value', csvDia: 'Day', csvMetodo: 'Payment method', csvBruto: 'Gross sales',
    },
  };

  const ICONOS = {
    generar: '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/>',
    exportar: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/>',
    imprimir: '<path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
  };
  const icono = n => `<svg class="rv-ico" viewBox="0 0 24 24" aria-hidden="true">${ICONOS[n]}</svg>`;

  const CSS = `
  .rv-filtros { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; margin-bottom: 1rem; }
  .rv-filtros .rv-campo { display: flex; flex-direction: column; gap: 4px; }
  .rv-filtros label { font-size: 0.75rem; color: var(--muted); }
  .rv-filtros select, .rv-filtros input { height: 2.35rem; padding: 0 0.55rem; border: 1px solid var(--border); border-radius: 6px; font: inherit; font-size: 0.85rem; background: var(--card-bg); color: var(--text); }
  [data-theme="dark"] .rv-filtros input { color-scheme: dark; }
  .rv-acciones { display: flex; gap: 0.5rem; margin-left: auto; flex-wrap: wrap; }
  .rv-btn-sec { background: var(--card-bg); color: var(--text); border: 1px solid var(--border); }
  .rv-btn-sec:hover { border-color: var(--accent); color: var(--accent); }
  .rv-ico { width: 1.05em; height: 1.05em; vertical-align: -0.15em; margin-right: 6px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .rv-periodo { font-size: 0.8rem; color: var(--muted); margin: -0.25rem 0 1rem; }
  .rv-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
  .rv-kpi { background: var(--card-bg); border-radius: 12px; padding: 1rem 1.1rem; box-shadow: 0 1px 4px var(--shadow); }
  .rv-kpi .rv-valor { font-size: 1.45rem; font-weight: 700; color: var(--accent); margin-top: 4px; }
  .rv-kpi .rv-etiqueta { font-size: 0.78rem; color: var(--muted); }
  .rv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
  .rv-tarjeta { background: var(--card-bg); border-radius: 12px; padding: 1rem 1.1rem; box-shadow: 0 1px 4px var(--shadow); min-width: 0; }
  .rv-tarjeta h3 { font-size: 0.92rem; margin: 0 0 0.75rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; }
  .rv-lienzo { position: relative; height: 240px; }
  .rv-tarjeta table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
  .rv-tarjeta th, .rv-tarjeta td { text-align: left; padding: 0.45rem 0.4rem; border-bottom: 1px solid var(--border-light); }
  .rv-tarjeta th { font-size: 0.75rem; color: var(--muted); font-weight: 600; }
  .rv-tarjeta td.rv-num, .rv-tarjeta th.rv-num { text-align: right; white-space: nowrap; }
  .rv-seg { display: inline-flex; border: 1px solid var(--border); border-radius: 999px; overflow: hidden; }
  .rv-seg button { border: none; background: transparent; padding: 0.2rem 0.7rem; font: inherit; font-size: 0.75rem; cursor: pointer; color: var(--muted); }
  .rv-seg button.activo { background: var(--accent); color: #fff; }
  .rv-aviso { font-size: 0.8rem; color: var(--muted); padding: 0.75rem; text-align: center; }
  .rv-barra { height: 6px; border-radius: 3px; background: var(--accent); opacity: 0.75; }
  .rv-part { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--muted); }
  .rv-part .rv-pista { flex: 1; height: 6px; border-radius: 3px; background: var(--border-light); }
  .rv-cargando { opacity: 0.55; pointer-events: none; transition: opacity 0.15s; }
  @media print {
    body * { visibility: hidden !important; }
    .rv-raiz.rv-imprimiendo, .rv-raiz.rv-imprimiendo * { visibility: visible !important; }
    .rv-raiz.rv-imprimiendo { position: absolute; left: 0; top: 0; width: 100%; }
    .rv-raiz .rv-no-imprimir { display: none !important; }
    .rv-tarjeta, .rv-kpi { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }
    html, body, .panel-view, .layout, .content { height: auto !important; overflow: visible !important; }
  }`;

  let cssPuesto = false;
  let cargaChart = null;

  function cargarChart() {
    if (window.Chart) return Promise.resolve(window.Chart);
    if (!cargaChart) {
      cargaChart = new Promise((resolver, rechazar) => {
        const s = document.createElement('script');
        s.src = CHART_URL;
        s.onload = () => resolver(window.Chart);
        s.onerror = () => { cargaChart = null; rechazar(new Error('Chart.js')); };
        document.head.appendChild(s);
      });
    }
    return cargaChart;
  }

  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const fechaLocal = d => new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 10);

  function rangoDe(preset) {
    const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
    const d = new Date(hoy);
    switch (preset) {
      case 'hoy': return [hoy, hoy];
      case 'ult7': d.setDate(d.getDate() - 6); return [d, hoy];
      case 'ult30': d.setDate(d.getDate() - 29); return [d, hoy];
      case 'mesPasado': return [new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1), new Date(hoy.getFullYear(), hoy.getMonth(), 0)];
      case 'esteAnio': return [new Date(hoy.getFullYear(), 0, 1), hoy];
      default: return [new Date(hoy.getFullYear(), hoy.getMonth(), 1), hoy];   // este mes
    }
  }

  function montar(opc) {
    const idioma = () => (document.documentElement.lang || 'es').startsWith('en') ? 'en' : 'es';
    const t = k => TEXTOS[idioma()][k] ?? TEXTOS.es[k] ?? k;
    const dinero = opc.dinero || (n => '$' + Number(n).toFixed(2));
    const toast = opc.toast || (() => {});
    const raiz = opc.contenedor;
    const graficas = {};
    let datos = null, vista = 'importe', montado = false, pidiendo = 0;

    if (!cssPuesto) { const st = document.createElement('style'); st.textContent = CSS; document.head.appendChild(st); cssPuesto = true; }
    raiz.classList.add('rv-raiz');

    const fmtFecha = iso => {
      const [a, m, d] = iso.split('-').map(Number);
      return new Date(a, m - 1, d).toLocaleDateString(idioma() === 'en' ? 'en-US' : 'es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
    };
    const $ = sel => raiz.querySelector(sel);

    function esqueleto() {
      const presets = ['hoy', 'ult7', 'esteMes', 'mesPasado', 'ult30', 'esteAnio', 'personalizado'];
      const conSucursal = typeof opc.sucursales === 'function';
      raiz.innerHTML = `
        <div class="rv-filtros rv-no-imprimir">
          <div class="rv-campo"><label for="rvPreset">${t('periodo')}</label>
            <select id="rvPreset">${presets.map(p => `<option value="${p}" ${p === 'esteMes' ? 'selected' : ''}>${t(p)}</option>`).join('')}</select></div>
          <div class="rv-campo"><label for="rvDesde">${t('desde')}</label><input type="date" id="rvDesde"></div>
          <div class="rv-campo"><label for="rvHasta">${t('hasta')}</label><input type="date" id="rvHasta"></div>
          ${conSucursal ? `<div class="rv-campo"><label for="rvSucursal">${t('sucursal')}</label><select id="rvSucursal"></select></div>` : ''}
          <button type="button" class="btn btn-primario" id="rvGenerar">${icono('generar')}${t('generar')}</button>
          <div class="rv-acciones">
            <button type="button" class="btn rv-btn-sec" id="rvExportar">${icono('exportar')}${t('exportar')}</button>
            <button type="button" class="btn rv-btn-sec" id="rvImprimir">${icono('imprimir')}${t('imprimir')}</button>
          </div>
        </div>
        <p class="rv-periodo" id="rvPeriodo"></p>
        <div class="rv-kpis">
          ${['neto', 'ventas', 'ticket', 'devoluciones'].map(k => `<div class="rv-kpi"><div class="rv-etiqueta">${t(k)}</div><div class="rv-valor" id="rvKpi-${k}">—</div></div>`).join('')}
        </div>
        <p class="rv-aviso rv-no-imprimir" id="rvAvisoGraficas" hidden>${t('sinGraficas')}</p>
        <div class="rv-grid">
          <div class="rv-tarjeta" style="grid-column: 1 / -1;"><h3>${t('porDia')}</h3><div class="rv-lienzo"><canvas id="rvGrafDia"></canvas></div></div>
          <div class="rv-tarjeta"><h3>${t('porMetodo')}</h3><div class="rv-lienzo"><canvas id="rvGrafMetodo"></canvas></div></div>
          <div class="rv-tarjeta"><h3>${t('porHora')}</h3><div class="rv-lienzo"><canvas id="rvGrafHora"></canvas></div></div>
        </div>
        <div class="rv-grid">
          <div class="rv-tarjeta"><h3>${t('topProductos')}
            <span class="rv-seg rv-no-imprimir"><button type="button" data-vista="importe" class="activo">${t('porImporte')}</button><button type="button" data-vista="cantidad">${t('porCantidad')}</button></span></h3>
            <table><thead><tr><th>#</th><th>${t('producto')}</th><th class="rv-num">${t('cantidad')}</th><th class="rv-num">${t('importe')}</th></tr></thead><tbody id="rvTop"></tbody></table></div>
          <div class="rv-tarjeta"><h3>${t('porCajero')}</h3>
            <table><thead><tr><th>${t('cajero')}</th><th class="rv-num">${t('ventas')}</th><th class="rv-num">${t('neto')}</th><th style="width:25%">${t('participacion')}</th></tr></thead><tbody id="rvCajeros"></tbody></table></div>
        </div>
        <p class="rv-periodo">${t('notaNeto')}</p>`;

      const [d, h] = rangoDe('esteMes');
      $('#rvDesde').value = fechaLocal(d); $('#rvHasta').value = fechaLocal(h);
      $('#rvPreset').addEventListener('change', e => {
        if (e.target.value === 'personalizado') { $('#rvDesde').focus(); return; }
        const [a, b] = rangoDe(e.target.value);
        $('#rvDesde').value = fechaLocal(a); $('#rvHasta').value = fechaLocal(b);
        cargar();
      });
      ['#rvDesde', '#rvHasta'].forEach(s => $(s).addEventListener('change', () => { $('#rvPreset').value = 'personalizado'; }));
      if (conSucursal) $('#rvSucursal').addEventListener('change', cargar);
      $('#rvGenerar').addEventListener('click', cargar);
      $('#rvExportar').addEventListener('click', exportarCsv);
      $('#rvImprimir').addEventListener('click', imprimir);
      raiz.querySelectorAll('.rv-seg button').forEach(b => b.addEventListener('click', () => {
        vista = b.dataset.vista;
        raiz.querySelectorAll('.rv-seg button').forEach(x => x.classList.toggle('activo', x === b));
        pintarTop();
      }));
      // Si cambia el tema (claro/oscuro), las gráficas se vuelven a pintar con los colores nuevos
      new MutationObserver(() => { if (datos) pintarGraficas(); }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
      montado = true;
    }

    function llenarSucursales() {
      if (typeof opc.sucursales !== 'function') return;
      const sel = $('#rvSucursal'); const elegida = sel.value;
      sel.innerHTML = `<option value="">${t('todas')}</option>` + opc.sucursales().map(s => `<option value="${s.id}">${esc(s.nombre)}</option>`).join('');
      sel.value = elegida;
    }

    async function cargar() {
      const desde = $('#rvDesde').value, hasta = $('#rvHasta').value;
      if (!desde || !hasta) { toast(t('fechasInvalidas'), 'error'); return; }
      const params = new URLSearchParams({ desde, hasta });
      const suc = raiz.querySelector('#rvSucursal')?.value;
      if (suc) params.set('sucursal', suc);
      const mio = ++pidiendo;   // si se piden dos reportes seguidos, solo se pinta el último
      raiz.classList.add('rv-cargando');
      try {
        const resp = await fetch(opc.url + '?' + params, { headers: { Accept: 'application/json' } });
        const json = await resp.json().catch(() => null);
        if (!resp.ok) throw new Error(json?.message || t('errorCargar'));
        if (mio !== pidiendo) return;
        datos = json;
        pintar();
      } catch (e) {
        if (mio === pidiendo) toast(e.message || t('errorCargar'), 'error');
      } finally {
        if (mio === pidiendo) raiz.classList.remove('rv-cargando');
      }
    }

    function pintar() {
      const tot = datos.totales;
      $('#rvPeriodo').textContent = t('periodoTexto').replace('{desde}', fmtFecha(datos.desde)).replace('{hasta}', fmtFecha(datos.hasta));
      $('#rvKpi-neto').textContent = dinero(tot.neto);
      $('#rvKpi-ventas').textContent = tot.ventas.toLocaleString(idioma() === 'en' ? 'en-US' : 'es-MX');
      $('#rvKpi-ticket').textContent = dinero(tot.ticket_promedio);
      $('#rvKpi-devoluciones').textContent = dinero(tot.devoluciones);
      pintarTop();
      // Participación: el % es del total del periodo; la barra se mide contra quien más vendió (así se distinguen)
      const total = tot.neto || 1;
      const mayor = Math.max(...datos.por_cajero.map(c => c.neto), 1);
      $('#rvCajeros').innerHTML = datos.por_cajero.length
        ? datos.por_cajero.map(c => `<tr><td>${esc(c.cajero)}</td><td class="rv-num">${c.ventas}</td><td class="rv-num">${dinero(c.neto)}</td>
            <td><div class="rv-part"><div class="rv-pista"><div class="rv-barra" style="width:${Math.max(0, Math.min(100, c.neto / mayor * 100)).toFixed(1)}%"></div></div>${(c.neto / total * 100).toFixed(1)}%</div></td></tr>`).join('')
        : `<tr><td colspan="4" class="rv-aviso">${t('sinVentas')}</td></tr>`;
      pintarGraficas();
    }

    function pintarTop() {
      const lista = vista === 'cantidad' ? datos.top_cantidad : datos.top_importe;
      $('#rvTop').innerHTML = lista.length
        ? lista.map((p, i) => `<tr><td>${i + 1}</td><td>${esc(p.nombre)}</td><td class="rv-num">${p.cantidad}</td><td class="rv-num">${dinero(p.importe)}</td></tr>`).join('')
        : `<tr><td colspan="4" class="rv-aviso">${t('sinVentas')}</td></tr>`;
    }

    async function pintarGraficas() {
      let Chart;
      try { Chart = await cargarChart(); } catch (e) { $('#rvAvisoGraficas').hidden = false; return; }
      $('#rvAvisoGraficas').hidden = true;
      const css = getComputedStyle(document.documentElement);
      const color = n => css.getPropertyValue(n).trim();
      const acento = color('--accent') || '#ea580c', texto = color('--muted') || '#6b7280', rejilla = color('--border-light') || '#e5e7eb';
      Chart.defaults.color = texto;
      Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
      const ejes = { x: { grid: { color: rejilla } }, y: { grid: { color: rejilla }, beginAtZero: true, ticks: { callback: v => Number(v).toLocaleString(idioma() === 'en' ? 'en-US' : 'es-MX') } } };
      const tooltipDinero = { callbacks: { label: c => ' ' + dinero(c.parsed.y ?? c.parsed) } };
      const dibujar = (id, config) => { graficas[id]?.destroy(); graficas[id] = new Chart(raiz.querySelector('#' + id), config); };

      dibujar('rvGrafDia', {
        type: datos.por_dia.length > 45 ? 'line' : 'bar',
        data: { labels: datos.por_dia.map(d => fmtFecha(d.dia).replace(/ de \d{4}|, \d{4}| \d{4}/, '')),
          datasets: [{ data: datos.por_dia.map(d => d.neto), backgroundColor: acento, borderColor: acento, borderRadius: 4, fill: false, tension: 0.25, pointRadius: 2 }] },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: tooltipDinero }, scales: ejes },
      });
      dibujar('rvGrafMetodo', {
        type: 'doughnut',
        data: { labels: datos.por_metodo.map(m => m.metodo), datasets: [{ data: datos.por_metodo.map(m => m.neto), backgroundColor: [acento, '#0ea5e9', '#a855f7', '#22c55e'], borderWidth: 0 }] },
        options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: c => ' ' + c.label + ': ' + dinero(c.parsed) } } } },
      });
      dibujar('rvGrafHora', {
        type: 'bar',
        data: { labels: datos.por_hora.map(h => String(h.hora).padStart(2, '0') + ':00'), datasets: [{ data: datos.por_hora.map(h => h.neto), backgroundColor: acento, borderRadius: 3 }] },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: tooltipDinero }, scales: ejes },
      });
    }

    function exportarCsv() {
      if (!datos) return;
      const celda = v => { const s = String(v ?? ''); return /[",;\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s; };
      const n = v => Number(v).toFixed(2);
      const filas = [
        [t('csvResumen'), t('periodoTexto').replace('{desde}', datos.desde).replace('{hasta}', datos.hasta)],
        [t('csvConcepto'), t('csvValor')],
        [t('neto'), n(datos.totales.neto)], [t('csvBruto'), n(datos.totales.bruto)], [t('devoluciones'), n(datos.totales.devoluciones)],
        [t('ventas'), datos.totales.ventas], [t('ticket'), n(datos.totales.ticket_promedio)], [],
        [t('porDia')], [t('csvDia'), t('ventas'), t('neto')], ...datos.por_dia.map(d => [d.dia, d.ventas, n(d.neto)]), [],
        [t('porMetodo')], [t('csvMetodo'), t('ventas'), t('neto')], ...datos.por_metodo.map(m => [m.metodo, m.ventas, n(m.neto)]), [],
        [t('porCajero')], [t('cajero'), t('ventas'), t('neto')], ...datos.por_cajero.map(c => [c.cajero, c.ventas, n(c.neto)]), [],
        [t('porHora')], [t('hora'), t('ventas'), t('neto')], ...datos.por_hora.map(h => [String(h.hora).padStart(2, '0') + ':00', h.ventas, n(h.neto)]), [],
        [t('topProductos') + ' — ' + t('porImporte')], ['#', t('producto'), t('cantidad'), t('importe')], ...datos.top_importe.map((p, i) => [i + 1, p.nombre, p.cantidad, n(p.importe)]),
      ];
      // ﻿ (BOM) hace que Excel lea bien los acentos
      const blob = new Blob(['﻿' + filas.map(f => f.map(celda).join(',')).join('\r\n')], { type: 'text/csv;charset=utf-8' });
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = `reporte-ventas_${datos.desde}_${datos.hasta}.csv`;
      document.body.appendChild(a); a.click(); a.remove();
      setTimeout(() => URL.revokeObjectURL(a.href), 1000);
    }

    function imprimir() {
      raiz.classList.add('rv-imprimiendo');
      Object.values(graficas).forEach(g => g.resize());
      window.print();
      raiz.classList.remove('rv-imprimiendo');
    }

    return {
      /** Al entrar a la sección: la primera vez arma la pantalla y pide el mes en curso. */
      mostrar() {
        if (!montado) { esqueleto(); llenarSucursales(); cargar(); return; }
        llenarSucursales();
      },
      cargar,
      get datos() { return datos; },
    };
  }

  window.NexoraReportesVentas = { montar };
})();
