<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora - Panel de Gerente</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f4f5f7; --text: #1f2933; --card-bg: #ffffff; --border: #d1d5db; --border-light: #eeeeee;
    --muted: #6b7280; --muted-light: #9ca3af; --topbar-bg: #1f2933; --topbar-text: #ffffff;
    --accent: #0d9488; --accent-hover: #0b7d73; --accent-bg-light: #e6fffb; --sidebar-hover: #f4f5f7;
    --disabled-bg: #d1d5db; --danger-bg: #fee2e2; --danger-text: #dc2626; --danger-hover: #fecaca;
    --success-bg: #dcfce7; --success-text: #16a34a; --success-hover: #bbf7d0;
    --badge-inactivo-bg: #f3f4f6; --badge-inactivo-text: #9ca3af;
    --badge-efectivo-bg: #fff3e0; --badge-efectivo-text: #e8631f;
    --badge-tarjeta-bg: #e0f2fe; --badge-tarjeta-text: #0369a1;
    --shadow: rgba(0,0,0,0.06); --login-bg: #e0f2f1;
  }

  [data-theme="dark"] {
    --bg: #0f1420; --text: #e5e7eb; --card-bg: #1a2233; --border: #374151; --border-light: #2b3444;
    --muted: #9ca3af; --muted-light: #6b7280; --topbar-bg: #0a0e17; --topbar-text: #f3f4f6;
    --accent: #14b8a6; --accent-hover: #2dd4bf; --accent-bg-light: #113a36; --sidebar-hover: #232c3d;
    --disabled-bg: #374151; --danger-bg: #3f1d1d; --danger-text: #f87171; --danger-hover: #522525;
    --success-bg: #143621; --success-text: #4ade80; --success-hover: #1c4a2c;
    --badge-inactivo-bg: #2b3444; --badge-inactivo-text: #9ca3af;
    --badge-efectivo-bg: #3a2a1a; --badge-efectivo-text: #fdba74;
    --badge-tarjeta-bg: #0c2a3d; --badge-tarjeta-text: #7dd3fc;
    --shadow: rgba(0,0,0,0.4); --login-bg: #0a0e17;
  }

  body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); transition: background 0.2s, color 0.2s; }

  .login-container { display: flex; align-items: center; justify-content: center; height: 100vh; background: var(--login-bg); position: relative; }
  .login-box { background: var(--card-bg); padding: 2.5rem; border-radius: 16px; box-shadow: 0 8px 30px var(--shadow); width: 320px; text-align: center; }
  .logo { display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 14px; background: var(--accent); color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem; }
  .login-box h1 { font-size: 1.3rem; margin-bottom: 0.25rem; }
  .login-box .tagline { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem; }
  .login-box input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; background: var(--card-bg); color: var(--text); }
  .login-box button { width: 100%; padding: 0.75rem; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; }
  .login-box button:hover { background: var(--accent-hover); }
  .demo-note { font-size: 0.75rem; color: var(--muted-light); margin-top: 1rem; }

  .esquina-superior { position: absolute; top: 1.25rem; right: 1.25rem; display: flex; gap: 0.5rem; }
  .tema-toggle, .idioma-toggle { width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border); background: var(--card-bg); cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; }
  .idioma-toggle { font-size: 0.72rem; font-weight: 700; color: var(--text); }
  .topbar .esquina-superior { position: static; }
  .topbar .tema-toggle, .topbar .idioma-toggle { width: 30px; height: 30px; background: transparent; border: 1px solid rgba(255,255,255,0.25); color: var(--topbar-text); }

  .panel-view { display: block; }
  .topbar { background: var(--topbar-bg); color: var(--topbar-text); padding: 0.85rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
  .topbar .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.05rem; }
  .topbar .brand .logo-sm { width: 28px; height: 28px; border-radius: 7px; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }
  .topbar .sucursal { font-weight: 400; font-size: 0.8rem; color: #cbd2d9; margin-left: 8px; }
  .topbar-derecha { display: flex; align-items: center; gap: 10px; }
  .topbar a { color: var(--topbar-text); text-decoration: none; font-size: 0.85rem; cursor: pointer; }

  .layout { display: grid; grid-template-columns: 220px 1fr; min-height: calc(100vh - 56px); }
  .sidebar { background: var(--card-bg); border-right: 1px solid var(--border-light); padding: 1rem 0; }
  .sidebar button { display: block; width: 100%; text-align: left; padding: 0.7rem 1.25rem; background: none; border: none; font-size: 0.88rem; color: var(--muted); cursor: pointer; border-left: 3px solid transparent; }
  .sidebar button:hover { background: var(--sidebar-hover); }
  .sidebar button.activo { background: var(--accent-bg-light); border-left-color: var(--accent); color: var(--accent-hover); font-weight: 600; }

  .content { padding: 1.5rem; max-width: 1000px; }
  .content h2 { font-size: 1.2rem; margin-bottom: 0.3rem; }
  .content .desc { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }
  .seccion { display: none; }
  .seccion.activa { display: block; }

  .card { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 4px var(--shadow); }
  .card h3 { font-size: 0.95rem; margin-bottom: 1rem; }

  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 0.55rem 0.6rem; border-bottom: 1px solid var(--border-light); text-align: left; font-size: 0.85rem; }
  th { color: var(--muted); font-weight: 600; font-size: 0.78rem; text-transform: uppercase; }
  td input { width: 80px; padding: 0.35rem; border: 1px solid var(--border); border-radius: 5px; font-size: 0.82rem; background: var(--card-bg); color: var(--text); }

  .fila-form { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: end; }
  .fila-form .campo { display: flex; flex-direction: column; gap: 4px; }
  .fila-form label { font-size: 0.75rem; color: var(--muted); }
  .fila-form input, .fila-form select { padding: 0.55rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.85rem; min-width: 130px; background: var(--card-bg); color: var(--text); }

  .btn { padding: 0.55rem 1rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
  .btn-primario { background: var(--accent); color: #fff; }
  .btn-primario:hover { background: var(--accent-hover); }
  .btn-peligro { background: var(--danger-bg); color: var(--danger-text); }
  .btn-peligro:hover { background: var(--danger-hover); }
  .btn-exito { background: var(--success-bg); color: var(--success-text); }
  .btn-exito:hover { background: var(--success-hover); }
  .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.78rem; }

  .badge { padding: 2px 8px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
  .badge-activo { background: var(--success-bg); color: var(--success-text); }
  .badge-inactivo { background: var(--badge-inactivo-bg); color: var(--badge-inactivo-text); }
  .badge-efectivo { background: var(--badge-efectivo-bg); color: var(--badge-efectivo-text); }
  .badge-tarjeta { background: var(--badge-tarjeta-bg); color: var(--badge-tarjeta-text); }

  .resumen-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
  .resumen-card { background: var(--card-bg); border-radius: 12px; padding: 1rem; box-shadow: 0 1px 4px var(--shadow); }
  .resumen-card .label { font-size: 0.78rem; color: var(--muted); margin-bottom: 4px; }
  .resumen-card .valor { font-size: 1.4rem; font-weight: 700; }

  .diferencia-row { display: flex; justify-content: space-between; padding: 0.6rem 0; font-size: 0.95rem; }
  .diferencia-row.sobrante { color: var(--success-text); font-weight: 700; }
  .diferencia-row.faltante { color: var(--danger-text); font-weight: 700; }
  .diferencia-row.cuadrado { color: var(--muted); font-weight: 700; }
    /* ---------- TOASTS ---------- */
  #toastContainer {
    position: fixed; top: 1.25rem; right: 1.25rem; z-index: 1000;
    display: flex; flex-direction: column; gap: 0.6rem;
  }
  .toast {
    padding: 0.8rem 1.1rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
    box-shadow: 0 4px 16px var(--shadow); min-width: 220px;
    animation: toastIn 0.25s ease-out;
  }
  .toast.exito { background: var(--success-bg); color: var(--success-text); }
  .toast.error { background: var(--danger-bg); color: var(--danger-text); }
  @keyframes toastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
  .toast.saliendo { animation: toastOut 0.2s ease-in forwards; }
  @keyframes toastOut { to { opacity: 0; transform: translateX(20px); } }

  /* ---------- MODAL DE CONFIRMACIÓN ---------- */
  #modalOverlay {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45);
    align-items: center; justify-content: center; z-index: 1001;
  }
  #modalOverlay.activo { display: flex; }
  .modal-box {
    background: var(--card-bg); color: var(--text); border-radius: 14px; padding: 1.5rem;
    width: 320px; box-shadow: 0 10px 40px var(--shadow);
  }
  .modal-box p { font-size: 0.9rem; margin-bottom: 1.25rem; }
  .modal-acciones { display: flex; justify-content: flex-end; gap: 0.6rem; }

</style>
</head>
<body>
<form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none">
    @csrf
</form>
<div id="toastContainer"></div>

<div id="modalOverlay">
  <div class="modal-box">
    <p id="modalMensaje"></p>
    <div class="modal-acciones">
      <button class="btn" onclick="cerrarModal(false)" data-i18n="btnCancelar">Cancelar</button>
      <button class="btn btn-peligro" id="modalConfirmarBtn" onclick="cerrarModal(true)" data-i18n="btnEliminar">Eliminar</button>
    </div>
  </div>
</div>
<div class="panel-view" id="panelView">
  <div class="topbar">
    <div class="brand"><span class="logo-sm">N</span> Nexora <span class="sucursal">— <span data-i18n="topbarPanelGerente">Panel de Gerente</span> · {{ auth()->user()->sucursal->nombre ?? __('Sin sucursal asignada') }}</span></div>
    <div class="topbar-derecha">
      <a onclick="cerrarSesion()" data-i18n="cerrarSesion">Cerrar sesión</a>
      <div class="esquina-superior">
        @php $otroIdioma = app()->getLocale() === 'es' ? 'en' : 'es'; @endphp
        <a class="idioma-toggle" id="idiomaTogglePanel" href="{{ route('lang.switch', $otroIdioma) }}" title="Change language">{{ strtoupper($otroIdioma) }}</a>
        <button class="tema-toggle" id="temaTogglePanel" onclick="toggleTema()" title="Cambiar tema">🌙</button>
      </div>
    </div>
  </div>
  <div class="layout">
    <div class="sidebar" id="sidebar"></div>
    <div class="content">
      <div class="seccion" id="sec-mi-sucursal">
        <h2 data-i18n="miSucursalHeading">Mi sucursal</h2>
        <p class="desc" data-i18n="miSucursalDesc">Información general de tu sucursal.</p>

        <div class="resumen-grid">
          <div class="resumen-card"><div class="label" data-i18n="labelEmpleados">Empleados</div><div class="valor" id="msEmpleados">0</div></div>
          <div class="resumen-card"><div class="label" data-i18n="labelProductosInv">Productos en inventario</div><div class="valor" id="msProductos">0</div></div>
          <div class="resumen-card"><div class="label" data-i18n="labelStockBajo">Stock bajo</div><div class="valor" id="msStockBajo">0</div></div>
        </div>
        <div class="resumen-grid">
          <div class="resumen-card"><div class="label" data-i18n="labelVentasHoy">Ventas de hoy</div><div class="valor" id="msVentasHoy">$0.00</div></div>
          <div class="resumen-card"><div class="label" data-i18n="labelVentasMes">Ventas del mes</div><div class="valor" id="msVentasMes">$0.00</div></div>
        </div>

        <div class="card">
          <h3 data-i18n="labelDatosSucursal">Datos de la sucursal</h3>
          <table>
            <tr><td data-i18n="labelDireccion">Dirección</td><td id="msDireccion">—</td></tr>
            <tr><td data-i18n="labelContacto">Contacto</td><td id="msContacto">—</td></tr>
            <tr><td data-i18n="labelEstado">Estado</td><td id="msEstado">—</td></tr>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-inventario">
        <h2 data-i18n="invHeading">Inventario</h2>
        <p class="desc" data-i18n="invDesc">Consulta y ajusta las existencias de tu sucursal.</p>
        <div class="card">
          <table>
            <tr><th data-i18n="thProducto">Producto</th><th data-i18n="thStockActual">Stock actual</th><th data-i18n="thAjustar">Ajustar</th><th></th></tr>
            <tbody id="tablaInventario"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-compras">
        <h2 data-i18n="comprasHeading">Registrar compras</h2>
        <p class="desc" data-i18n="comprasDesc">Registra las entradas de mercancía recibidas por el cajero o proveedor.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelProducto">Producto</label><select id="compraProducto"></select></div>
            <div class="campo"><label data-i18n="labelCantidad">Cantidad</label><input type="number" id="compraCantidad" min="1" placeholder="0"></div>
            <div class="campo"><label data-i18n="labelCostoUnitario">Costo unitario</label><input type="number" id="compraCosto" min="0" step="0.01" placeholder="0.00"></div>
            <div class="campo"><label data-i18n="labelProveedor">Proveedor</label><input type="text" id="compraProveedor" data-i18n-placeholder="placeholderNombre" placeholder="Nombre"></div>
            <button class="btn btn-primario" onclick="registrarCompra()" data-i18n="btnRegistrar">Registrar</button>
          </div>
        </div>
        <div class="card">
          <h3 data-i18n="comprasRegistradasHeading">Compras registradas</h3>
          <table>
            <tr><th data-i18n="thFecha">Fecha</th><th data-i18n="thProducto">Producto</th><th data-i18n="thCantidad">Cantidad</th><th data-i18n="thCostoUnit">Costo unit.</th><th data-i18n="thTotal">Total</th><th data-i18n="thProveedor">Proveedor</th></tr>
            <tbody id="tablaCompras"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-corte">
        <h2 data-i18n="corteHeading">Cortes de caja</h2>
        <p class="desc" data-i18n="corteDesc">Historial de turnos de tus cajas: lo vendido contra el efectivo que contó cada cajero.</p>
        <!-- Totales de los turnos abiertos hoy (netos de devoluciones) -->
        <div class="resumen-grid">
          <div class="resumen-card"><div class="label" data-i18n="ventasEfectivo">Ventas en efectivo</div><div class="valor" id="resEfectivo">$0.00</div></div>
          <div class="resumen-card"><div class="label" data-i18n="ventasTarjeta">Ventas con tarjeta</div><div class="valor" id="resTarjeta">$0.00</div></div>
          <div class="resumen-card"><div class="label" data-i18n="totalDia">Total del día</div><div class="valor" id="resTotal">$0.00</div></div>
        </div>
        <div class="card">
          <table>
            <tr>
              <th data-i18n="thCajero">Cajero</th><th data-i18n="thCaja">Caja</th><th data-i18n="thApertura">Apertura</th><th data-i18n="thCierre">Cierre</th>
              <th data-i18n="thFondo">Fondo</th><th data-i18n="thVentas">Ventas</th><th data-i18n="thEsperado">Esperado</th>
              <th data-i18n="thContado">Contado</th><th data-i18n="thDiferencia">Diferencia</th>
            </tr>
            <tbody id="tablaCortes"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-cajas">
        <h2 data-i18n="cajasHeading">Registro de cajas</h2>
        <p class="desc" data-i18n="cajasDesc">Administra las cajas registradoras disponibles en esta sucursal.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="cajaNumeroLabel">Número de caja</label><input type="number" id="cajaNumero" min="1" placeholder="3"></div>
            <button class="btn btn-primario" onclick="agregarCaja()" data-i18n="btnAgregarCaja">Agregar caja</button>
          </div>
        </div>
        <div class="card">
          <table>
            <tr><th data-i18n="thCaja">Caja</th><th data-i18n="thEstado">Estado</th><th data-i18n="thEnTurno">En turno</th><th></th><th></th></tr>
            <tbody id="tablaCajas"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-productos">
        <h2 data-i18n="productosHeading">Productos</h2>
        <p class="desc" data-i18n="productosDesc">Agrega productos, ajusta su precio e IVA, o actívalos/desactívalos.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombre">Nombre</label><input type="text" id="nuevoNombre" data-i18n-placeholder="placeholderProducto" placeholder="Producto"></div>
            <div class="campo"><label data-i18n="thPrecioBase">Precio base</label><input type="number" id="nuevoPrecio" min="0" step="0.01" placeholder="0.00"></div>
            <div class="campo"><label data-i18n="thIva">IVA %</label><input type="number" id="nuevoIva" min="0" step="0.01" value="16"></div>
            <div class="campo"><label data-i18n="labelStockInicial">Stock inicial</label><input type="number" id="nuevoStock" min="0" placeholder="0"></div>
            <button class="btn btn-primario" onclick="darDeAlta()" data-i18n="btnDarAlta">Dar de alta</button>
          </div>
        </div>
        <div class="card">
          <table>
            <tr>
              <th data-i18n="thProducto">Producto</th>
              <th data-i18n="thPrecioBase">Precio base</th>
              <th data-i18n="thIva">IVA %</th>
              <th data-i18n="thPrecioFinal">Precio final</th>
              <th data-i18n="thEstado">Estado</th>
              <th></th>
              <th></th>
            </tr>
            <tbody id="tablaProductos"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-historial">
        <h2 data-i18n="historialHeading">Historial de ventas</h2>
        <p class="desc" data-i18n="historialDesc">Todas las ventas registradas en esta sucursal. Desde aquí puedes devolver piezas o cancelar una venta.</p>
        <div class="card">
          <table>
            <tr><th data-i18n="thFolio">Folio</th><th data-i18n="thFecha">Fecha</th><th data-i18n="thCajero">Cajero</th><th data-i18n="thCaja">Caja</th><th data-i18n="thMetodo">Método</th><th data-i18n="thTotal">Total</th><th data-i18n="thEstado">Estado</th><th></th></tr>
            <tbody id="tablaHistorial"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  /* ---------- DATOS (declarados antes de cualquier función que los use) ---------- */
  let productos = [];
  let compras = [];
  let historial = [];
  let cajas = [];
  let cortes = [];
  let miSucursal = null;
  let ventaAbierta = null;   // folio de la venta cuyo panel de devolución está abierto
  let detalleVenta = null;   // detalle (líneas + devoluciones) de esa venta

  /*
   * fetch con CSRF + JSON. Devuelve el cuerpo ya parseado (o null en 204).
   * Si el servidor responde error, lanza un Error con su `message` para mostrarlo en un toast.
   */
  async function api(url, metodo = 'GET', cuerpo = null) {
    const opciones = {
      method: metodo,
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    };
    if (cuerpo !== null) {
      opciones.headers['Content-Type'] = 'application/json';
      opciones.body = JSON.stringify(cuerpo);
    }
    const resp = await fetch(url, opciones);
    const data = resp.status === 204 ? null : await resp.json().catch(() => null);
    if (!resp.ok) throw new Error(data?.message || t('errorGenerico'));
    return data;
  }

  function formatearFecha(iso) {
    return iso ? new Date(iso).toLocaleString(idioma === 'es' ? 'es-MX' : 'en-US') : '—';
  }

  async function cargarProductos() {
    try { productos = await api('/gerente/productos'); }
    catch (e) { mostrarToast(t('errorCargarProductos'), 'error'); }
  }

  async function cargarCompras() {
    try { compras = await api('/gerente/compras'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  async function cargarCajas() {
    try { cajas = await api('/gerente/cajas'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  async function cargarHistorial() {
    try { historial = await api('/gerente/historial'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  async function cargarCortes() {
    try { cortes = await api('/gerente/cortes'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  const dinero = n => '$' + Number(n).toFixed(2);

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      tagline: 'Panel de gerente', placeholderUsuario: 'Usuario', placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar', demoNote: 'Demo visual — cualquier dato entra',
      topbarSucursal: '— Panel de Gerente · Sucursal Centro', cerrarSesion: 'Cerrar sesión',
      navInventario: 'Inventario', navCompras: 'Registrar compras', navCorte: 'Cortes de caja',
      navCajas: 'Registro de cajas', navPrecios: 'Precios e IVA', navProductos: 'Alta / baja productos',
      navHistorial: 'Historial de ventas',
      invHeading: 'Inventario', invDesc: 'Consulta y ajusta las existencias de tu sucursal.',
      thProducto: 'Producto', thStockActual: 'Stock actual', thAjustar: 'Ajustar', btnAplicar: 'Aplicar',
      comprasHeading: 'Registrar compras', comprasDesc: 'Registra las entradas de mercancía recibidas por el cajero o proveedor.',
      labelProducto: 'Producto', labelCantidad: 'Cantidad', labelCostoUnitario: 'Costo unitario', labelProveedor: 'Proveedor',
      placeholderNombre: 'Nombre', btnRegistrar: 'Registrar', comprasRegistradasHeading: 'Compras registradas',
      thFecha: 'Fecha', thCantidad: 'Cantidad', thCostoUnit: 'Costo unit.', thTotal: 'Total', thProveedor: 'Proveedor',
      sinCompras: 'Sin compras registradas',
      corteHeading: 'Cortes de caja', corteDesc: 'Historial de turnos de tus cajas: lo vendido contra el efectivo que contó cada cajero.',
      ventasEfectivo: 'Ventas en efectivo', ventasTarjeta: 'Ventas con tarjeta', totalDia: 'Total del día',
      fondoInicialLabel: 'Fondo inicial de caja', efectivoContadoLabel: 'Efectivo contado físicamente',
      efectivoEsperado: 'Efectivo esperado en caja', sobrante: 'Sobrante', faltante: 'Faltante', cajaCuadrada: 'Caja cuadrada',
      cajasHeading: 'Registro de cajas', cajasDesc: 'Administra las cajas registradoras disponibles en esta sucursal.',
      cajaNombreLabel: 'Nombre de la caja', btnAgregarCaja: 'Agregar caja', thCaja: 'Caja', thEstado: 'Estado',
      badgeAbierta: 'Abierta', badgeCerrada: 'Cerrada', btnAbrir: 'Abrir', btnCerrar: 'Cerrar', btnEliminar: 'Eliminar', btnCancelar: 'Cancelar',
      sinCajas: 'Sin cajas registradas', alertNombreCaja: 'Ingresa el nombre de la caja.', confirmEliminarCaja: '¿Eliminar esta caja?',
      preciosHeading: 'Precios e IVA', preciosDesc: 'Modifica el precio base y el porcentaje de IVA de cada producto.',
      thPrecioBase: 'Precio base', thIva: 'IVA %', thPrecioFinal: 'Precio final', btnGuardar: 'Guardar',
      productosHeading: 'Alta / baja de productos', productosDesc: 'Agrega productos, ajusta su precio e IVA o desactívalos. Los cambios solo aplican a tu sucursal.',
      labelNombre: 'Nombre', labelStockInicial: 'Stock inicial', placeholderProducto: 'Producto',
      btnDarAlta: 'Dar de alta', thPrecio: 'Precio', badgeActivo: 'Activo', badgeInactivo: 'Inactivo',
      btnDarBaja: 'Dar de baja', btnReactivar: 'Reactivar', alertCompletaProducto: 'Completa al menos nombre y precio.',
      historialHeading: 'Historial de ventas', historialDesc: 'Todas las ventas registradas en esta sucursal. Desde aquí puedes devolver piezas o cancelar una venta.',
      thCajero: 'Cajero', thMetodo: 'Método', badgeEfectivo: 'Efectivo', badgeTarjeta: 'Tarjeta',
      alertCompletaCompra: 'Completa producto, cantidad y costo.',
      navMiSucursal: 'Mi sucursal', topbarPanelGerente: 'Panel de Gerente',
      miSucursalHeading: 'Mi sucursal', miSucursalDesc: 'Información general de tu sucursal.',
      labelEmpleados: 'Empleados', labelProductosInv: 'Productos en inventario', labelStockBajo: 'Stock bajo',
      labelVentasHoy: 'Ventas de hoy', labelVentasMes: 'Ventas del mes', labelDatosSucursal: 'Datos de la sucursal',
      labelDireccion: 'Dirección', labelContacto: 'Contacto', labelEstado: 'Estado',
      errorCargarSucursal: 'No se pudo cargar la información de tu sucursal.',
      labelEstado: 'Estado', thEstado: 'Estado', estadoActiva: 'Activa', estadoInactiva: 'Inactiva',
      errorCargarProductos: 'No se pudieron cargar los productos.',
      errorGenerico: 'Ocurrió un error. Intenta de nuevo.',
      cajaNumeroLabel: 'Número de caja', alertNumeroCaja: 'Ingresa el número de la caja.',
      thEnTurno: 'En turno', thFolio: 'Folio', libre: 'Libre',
      btnActivar: 'Activar', btnDesactivar: 'Desactivar',
      cajaAgregada: 'Caja agregada', cajaEliminada: 'Caja eliminada',
      productoActualizado: 'Producto actualizado', productoAgregado: 'Producto agregado',
      stockActualizado: 'Stock actualizado', compraRegistrada: 'Compra registrada; inventario actualizado',
      sinVentas: 'Sin ventas registradas', sinProductos: 'Sin productos',
      thApertura: 'Apertura', thCierre: 'Cierre', thFondo: 'Fondo', thVentas: 'Ventas', thEsperado: 'Esperado',
      thContado: 'Contado', thDiferencia: 'Diferencia', sinCortes: 'Sin cortes registrados', enTurno: 'En turno',
      estadoCompletada: 'Completada', estadoCancelada: 'Cancelada', estadoDevParcial: 'Con devolución',
      btnGestionar: 'Devolver / cancelar', btnOcultar: 'Ocultar',
      thVendidos: 'Vendidos', thDevueltos: 'Devueltos', thPrecioUnit: 'Precio unit.', thDevolver: 'Devolver',
      labelMotivo: 'Motivo', placeholderMotivo: 'Ej. producto dañado',
      btnDevolverSel: 'Devolver piezas', btnCancelarVenta: 'Cancelar venta completa',
      confirmCancelarVenta: '¿Cancelar toda la venta? Se reembolsa lo pendiente y el stock regresa al inventario.',
      btnConfirmarCancelacion: 'Cancelar venta', alertMotivo: 'Escribe el motivo.', alertPiezas: 'Indica cuántas piezas se devuelven.',
      devolucionRegistrada: 'Devolución registrada; stock actualizado', ventaCancelada: 'Venta cancelada; stock actualizado',
      historialDevoluciones: 'Devoluciones registradas', tipoDevolucion: 'Devolución', tipoCancelacion: 'Cancelación',
      devueltoLabel: 'devuelto', canceladaPor: 'Motivo de cancelación',
    },
    en: {
      tagline: 'Manager panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarSucursal: '— Manager Panel · Centro Branch', cerrarSesion: 'Log out',
      navInventario: 'Inventory', navCompras: 'Register purchases', navCorte: 'Cash counts',
      navCajas: 'Cash registers', navPrecios: 'Prices & tax', navProductos: 'Add / remove products',
      navHistorial: 'Sales history',
      invHeading: 'Inventory', invDesc: 'Check and adjust the stock at your branch.',
      thProducto: 'Product', thStockActual: 'Current stock', thAjustar: 'Adjust', btnAplicar: 'Apply',
      comprasHeading: 'Register purchases', comprasDesc: 'Log incoming stock received by the cashier or supplier.',
      labelProducto: 'Product', labelCantidad: 'Quantity', labelCostoUnitario: 'Unit cost', labelProveedor: 'Supplier',
      placeholderNombre: 'Name', btnRegistrar: 'Register', comprasRegistradasHeading: 'Registered purchases',
      thFecha: 'Date', thCantidad: 'Quantity', thCostoUnit: 'Unit cost', thTotal: 'Total', thProveedor: 'Supplier',
      sinCompras: 'No purchases registered',
      corteHeading: 'Cash counts', corteDesc: 'Shift history for your registers: sales vs. the cash each cashier counted.',
      ventasEfectivo: 'Cash sales', ventasTarjeta: 'Card sales', totalDia: "Today's total",
      fondoInicialLabel: 'Starting cash fund', efectivoContadoLabel: 'Physical cash counted',
      efectivoEsperado: 'Expected cash in register', sobrante: 'Overage', faltante: 'Shortage', cajaCuadrada: 'Register balanced',
      cajasHeading: 'Cash registers', cajasDesc: 'Manage the cash registers available at this branch.',
      cajaNombreLabel: 'Register name', btnAgregarCaja: 'Add register', thCaja: 'Register', thEstado: 'Status',
      badgeAbierta: 'Open', badgeCerrada: 'Closed', btnAbrir: 'Open', btnCerrar: 'Close', btnEliminar: 'Delete', btnCancelar: 'Cancel',
      sinCajas: 'No registers added', alertNombreCaja: 'Enter the register name.', confirmEliminarCaja: 'Delete this register?',
      preciosHeading: 'Prices & tax', preciosDesc: 'Edit the base price and tax rate for each product.',
      thPrecioBase: 'Base price', thIva: 'Tax %', thPrecioFinal: 'Final price', btnGuardar: 'Save',
      productosHeading: 'Add / remove products', productosDesc: 'Add products, adjust their price and tax or deactivate them. Changes only apply to your branch.',
      labelNombre: 'Name', labelStockInicial: 'Initial stock', placeholderProducto: 'Product',
      btnDarAlta: 'Add product', thPrecio: 'Price', badgeActivo: 'Active', badgeInactivo: 'Inactive',
      btnDarBaja: 'Deactivate', btnReactivar: 'Reactivate', alertCompletaProducto: 'Fill in at least name and price.',
      historialHeading: 'Sales history', historialDesc: 'All sales recorded at this branch. From here you can return items or cancel a sale.',
      thCajero: 'Cashier', thMetodo: 'Method', badgeEfectivo: 'Cash', badgeTarjeta: 'Card',
      alertCompletaCompra: 'Fill in product, quantity, and cost.',
      navMiSucursal: 'My branch', topbarPanelGerente: 'Manager Panel',
      miSucursalHeading: 'My branch', miSucursalDesc: 'General information about your branch.',
      labelEmpleados: 'Employees', labelProductosInv: 'Products in inventory', labelStockBajo: 'Low stock',
      labelVentasHoy: "Today's sales", labelVentasMes: "This month's sales", labelDatosSucursal: 'Branch info',
      labelDireccion: 'Address', labelContacto: 'Contact', labelEstado: 'Status',
      errorCargarSucursal: 'Could not load your branch information.',
      labelEstado: 'Status', thEstado: 'Status', estadoActiva: 'Active', estadoInactiva: 'Inactive',
      errorCargarProductos: 'Could not load the products.',
      errorGenerico: 'Something went wrong. Please try again.',
      cajaNumeroLabel: 'Register number', alertNumeroCaja: 'Enter the register number.',
      thEnTurno: 'In shift', thFolio: 'Receipt #', libre: 'Free',
      btnActivar: 'Activate', btnDesactivar: 'Deactivate',
      cajaAgregada: 'Register added', cajaEliminada: 'Register deleted',
      productoActualizado: 'Product updated', productoAgregado: 'Product added',
      stockActualizado: 'Stock updated', compraRegistrada: 'Purchase registered; inventory updated',
      sinVentas: 'No sales recorded', sinProductos: 'No products',
      thApertura: 'Opened', thCierre: 'Closed', thFondo: 'Fund', thVentas: 'Sales', thEsperado: 'Expected',
      thContado: 'Counted', thDiferencia: 'Difference', sinCortes: 'No cash counts recorded', enTurno: 'In shift',
      estadoCompletada: 'Completed', estadoCancelada: 'Cancelled', estadoDevParcial: 'With return',
      btnGestionar: 'Return / cancel', btnOcultar: 'Hide',
      thVendidos: 'Sold', thDevueltos: 'Returned', thPrecioUnit: 'Unit price', thDevolver: 'Return',
      labelMotivo: 'Reason', placeholderMotivo: 'e.g. damaged product',
      btnDevolverSel: 'Return items', btnCancelarVenta: 'Cancel whole sale',
      confirmCancelarVenta: 'Cancel the whole sale? The pending amount is refunded and stock goes back to inventory.',
      btnConfirmarCancelacion: 'Cancel sale', alertMotivo: 'Enter the reason.', alertPiezas: 'Enter how many items are returned.',
      devolucionRegistrada: 'Return recorded; stock updated', ventaCancelada: 'Sale cancelled; stock updated',
      historialDevoluciones: 'Recorded returns', tipoDevolucion: 'Return', tipoCancelacion: 'Cancellation',
      devueltoLabel: 'returned', canceladaPor: 'Cancellation reason',
    }
  };
  let idioma = '{{ app()->getLocale() }}';
  function t(clave) { return textos[idioma][clave] || clave; }

  const secciones = [
    { id: 'mi-sucursal', key: 'navMiSucursal'},
    { id: 'inventario', key: 'navInventario' },
    { id: 'compras', key: 'navCompras' },
    { id: 'cajas', key: 'navCajas' },
    { id: 'productos', key: 'navProductos' },
    { id: 'historial', key: 'navHistorial' },
    // El cajero hace su corte; aquí el gerente consulta el historial de cortes
    { id: 'corte', key: 'navCorte' },
  ];
  let seccionActual = 'mi-sucursal';

  /* ---------- TEMA CLARO / OSCURO ---------- */
  function aplicarTema(tema) {
    document.documentElement.setAttribute('data-theme', tema);
    const icono = tema === 'dark' ? '☀️' : '🌙';
    const bLogin = document.getElementById('temaToggleLogin');
    const bPanel = document.getElementById('temaTogglePanel');
    if (bLogin) bLogin.textContent = icono;
    if (bPanel) bPanel.textContent = icono;
  }

  function toggleTema() {
    const actual = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    const nuevo = actual === 'dark' ? 'light' : 'dark';
    aplicarTema(nuevo);
    try { localStorage.setItem('nexora-tema', nuevo); } catch (e) {}
  }

  (function inicializarTema() {
    let guardado = null;
    try { guardado = localStorage.getItem('nexora-tema'); } catch (e) {}
    if (guardado === 'dark' || guardado === 'light') aplicarTema(guardado);
    else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) aplicarTema('dark');
    else aplicarTema('light');
  })();

  /* ---------- IDIOMA ---------- */
  function aplicarIdioma() {
    document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.getAttribute('data-i18n')); });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.getAttribute('data-i18n-placeholder')); });
    document.documentElement.lang = idioma;
    renderSidebar();
    mostrarSeccion(seccionActual);
  }

  aplicarIdioma();

  function cerrarSesion() {
    document.getElementById('logoutForm').submit();
  }

  function renderSidebar() {
    document.getElementById('sidebar').innerHTML = secciones.map(s => `<button id="btn-${s.id}" onclick="mostrarSeccion('${s.id}')">${t(s.key)}</button>`).join('');
    secciones.forEach(s => document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === seccionActual));
  }

  function mostrarSeccion(id) {
    seccionActual = id;
    secciones.forEach(s => {
      document.getElementById('sec-' + s.id).classList.toggle('activa', s.id === id);
      document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === id);
    });
    // Se pinta de inmediato con lo que ya hay y luego se refresca con datos del servidor
    if (id === 'mi-sucursal') { renderMiSucursal(); cargarMiSucursal().then(renderMiSucursal); }
    if (id === 'inventario') { renderInventario(); cargarProductos().then(renderInventario); }
    if (id === 'compras') { renderCompras(); Promise.all([cargarProductos(), cargarCompras()]).then(renderCompras); }
    if (id === 'cajas') { renderCajas(); cargarCajas().then(renderCajas); }
    if (id === 'productos') { renderProductos(); cargarProductos().then(renderProductos); }
    if (id === 'historial') { renderHistorial(); cargarHistorial().then(renderHistorial); }
    if (id === 'corte') { renderCortes(); cargarCortes().then(renderCortes); }
  }

  /* ---------- MI SUCURSAL ---------- */

  async function cargarMiSucursal() {
    try { miSucursal = await api('/gerente/mi-sucursal'); }
    catch (e) { mostrarToast(e.message || t('errorCargarSucursal'), 'error'); }
  }

  function renderMiSucursal() {
    if (!miSucursal) return;
    document.getElementById('msEmpleados').textContent = miSucursal.empleados;
    document.getElementById('msProductos').textContent = miSucursal.productos;
    document.getElementById('msStockBajo').textContent = miSucursal.stock_bajo;
    document.getElementById('msVentasHoy').textContent = '$' + Number(miSucursal.ventas_hoy).toFixed(2);
    document.getElementById('msVentasMes').textContent = '$' + Number(miSucursal.ventas_mes).toFixed(2);
    document.getElementById('msDireccion').textContent = miSucursal.direccion || '—';
    document.getElementById('msContacto').textContent = miSucursal.contacto || '—';
    document.getElementById('msEstado').textContent = miSucursal.estado || '—';
  }

  /* ---------- INVENTARIO ---------- */
  function renderInventario() {
    document.getElementById('tablaInventario').innerHTML = productos.filter(p => p.activo).map(p => `
      <tr>
        <td>${p.nombre}</td>
        <td>${p.stock}</td>
        <td><input type="number" id="ajuste-${p.id}" placeholder="+/-"></td>
        <td><button class="btn btn-primario btn-sm" onclick="ajustarStock(${p.id})">${t('btnAplicar')}</button></td>
      </tr>`).join('');
  }

  async function ajustarStock(id) {
    const input = document.getElementById('ajuste-' + id);
    const ajuste = parseInt(input.value);
    if (!ajuste) return;

    try {
      const r = await api('/gerente/inventario/' + id, 'PUT', { ajuste });
      productos.find(x => x.id === id).stock = r.stock;
      input.value = '';
      renderInventario();
      mostrarToast(t('stockActualizado'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  /* ---------- COMPRAS ---------- */
  function renderCompras() {
    const select = document.getElementById('compraProducto');
    const seleccionado = select.value;
    select.innerHTML = productos.filter(p => p.activo).map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
    if (seleccionado) select.value = seleccionado; // no perder la selección al refrescar

    // El servidor ya las manda de la más reciente a la más antigua
    document.getElementById('tablaCompras').innerHTML = compras.map(c => `
      <tr>
        <td>${formatearFecha(c.fecha)}</td><td>${c.producto}</td><td>${c.cantidad}</td>
        <td>$${c.costo.toFixed(2)}</td><td>$${c.total.toFixed(2)}</td><td>${c.proveedor || '—'}</td>
      </tr>`).join('') || `<tr><td colspan="6" style="color:var(--muted-light);">${t('sinCompras')}</td></tr>`;
  }

  async function registrarCompra() {
    const id_producto = parseInt(document.getElementById('compraProducto').value);
    const cantidad = parseInt(document.getElementById('compraCantidad').value);
    const costo_unitario = parseFloat(document.getElementById('compraCosto').value);
    const proveedor = document.getElementById('compraProveedor').value.trim() || null;
    if (!id_producto || !cantidad || isNaN(costo_unitario)) { mostrarToast(t('alertCompletaCompra'), 'error'); return; }

    try {
      await api('/gerente/compras', 'POST', { id_producto, cantidad, costo_unitario, proveedor });
      document.getElementById('compraCantidad').value = '';
      document.getElementById('compraCosto').value = '';
      document.getElementById('compraProveedor').value = '';
      await Promise.all([cargarProductos(), cargarCompras()]);
      renderCompras();
      mostrarToast(t('compraRegistrada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  /* ---------- CORTES DE CAJA (historial) ---------- */
  // Sobrante / faltante / cuadrada, con los colores de .diferencia-row del diseño original
  function celdaDiferencia(c) {
    if (c.diferencia === null) return `<span class="badge badge-tarjeta">${t('enTurno')}</span>`;
    let clase = 'cuadrado', texto = t('cajaCuadrada');
    if (c.diferencia > 0) { clase = 'sobrante'; texto = t('sobrante'); }
    if (c.diferencia < 0) { clase = 'faltante'; texto = t('faltante'); }
    return `<span class="diferencia-row ${clase}" style="padding:0;display:inline">${texto} ${dinero(Math.abs(c.diferencia))}</span>`;
  }

  function renderCortes() {
    // Tarjetas de resumen: turnos abiertos HOY, netos de devoluciones
    const hoy = new Date().toDateString();
    const deHoy = cortes.filter(c => new Date(c.apertura).toDateString() === hoy);
    const efectivo = deHoy.reduce((s, c) => s + c.ventas_efectivo - c.devoluciones_efectivo, 0);
    const tarjeta = deHoy.reduce((s, c) => s + c.ventas_tarjeta - c.devoluciones_tarjeta, 0);
    document.getElementById('resEfectivo').textContent = dinero(efectivo);
    document.getElementById('resTarjeta').textContent = dinero(tarjeta);
    document.getElementById('resTotal').textContent = dinero(efectivo + tarjeta);

    document.getElementById('tablaCortes').innerHTML = cortes.map(c => `
      <tr>
        <td>${c.cajero ?? '—'}</td><td>${c.caja ?? '—'}</td>
        <td>${formatearFecha(c.apertura)}</td><td>${c.cierre ? formatearFecha(c.cierre) : '—'}</td>
        <td>${dinero(c.monto_inicial)}</td><td>${c.num_ventas} · ${dinero(c.total)}</td>
        <td>${dinero(c.efectivo_esperado)}</td><td>${c.efectivo_contado === null ? '—' : dinero(c.efectivo_contado)}</td>
        <td>${celdaDiferencia(c)}</td>
      </tr>`).join('') || `<tr><td colspan="9" style="color:var(--muted-light);">${t('sinCortes')}</td></tr>`;
  }

  /* ---------- REGISTRO DE CAJAS ---------- */
  function renderCajas() {
    document.getElementById('tablaCajas').innerHTML = cajas.map(c => `
      <tr>
        <td>${t('thCaja')} ${c.numero}</td>
        <td><span class="badge ${c.activa ? 'badge-activo' : 'badge-inactivo'}">${c.activa ? t('estadoActiva') : t('estadoInactiva')}</span></td>
        <td>${c.en_turno ? `<span class="badge badge-tarjeta">${t('badgeAbierta')}</span> ${c.cajero ?? ''}` : t('libre')}</td>
        <td><button class="btn btn-sm ${c.activa ? 'btn-peligro' : 'btn-exito'}" onclick="toggleCaja(${c.id})">${c.activa ? t('btnDesactivar') : t('btnActivar')}</button></td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarCaja(${c.id})">${t('btnEliminar')}</button></td>
      </tr>`).join('') || `<tr><td colspan="5" style="color:var(--muted-light);">${t('sinCajas')}</td></tr>`;
  }

  async function agregarCaja() {
    const numero = parseInt(document.getElementById('cajaNumero').value);
    if (!numero) { mostrarToast(t('alertNumeroCaja'), 'error'); return; }

    try {
      await api('/gerente/cajas', 'POST', { numero });
      document.getElementById('cajaNumero').value = '';
      await cargarCajas();
      renderCajas();
      mostrarToast(t('cajaAgregada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function toggleCaja(id) {
    try {
      await api('/gerente/cajas/' + id + '/toggle', 'PUT');
      await cargarCajas();
      renderCajas();
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function eliminarCaja(id) {
    if (!(await confirmar(t('confirmEliminarCaja')))) return;

    try {
      await api('/gerente/cajas/' + id, 'DELETE');
      await cargarCajas();
      renderCajas();
      mostrarToast(t('cajaEliminada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

    /* ---------- PRODUCTOS ---------- */
  function renderProductos() {
    document.getElementById('tablaProductos').innerHTML = productos.map(p => `
      <tr>
        <td>${p.nombre}</td>
        <td><input type="number" id="precio-${p.id}" value="${p.precio}" step="0.01"></td>
        <td><input type="number" id="iva-${p.id}" value="${p.iva}" step="0.01"></td>
        <td id="final-${p.id}">$${(p.precio * (1 + p.iva / 100)).toFixed(2)}</td>
        <td><span class="badge ${p.activo ? 'badge-activo' : 'badge-inactivo'}">${p.activo ? t('badgeActivo') : t('badgeInactivo')}</span></td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarProducto(${p.id})">${t('btnGuardar')}</button></td>
        <td><button class="btn btn-sm ${p.activo ? 'btn-peligro' : 'btn-exito'}" onclick="toggleActivo(${p.id})">${p.activo ? t('btnDarBaja') : t('btnReactivar')}</button></td>
      </tr>`).join('');
  }

  async function guardarProducto(id) {
    const precio = parseFloat(document.getElementById('precio-' + id).value);
    const iva = parseFloat(document.getElementById('iva-' + id).value);

    try {
      await api('/gerente/productos/' + id, 'PUT', { precio, iva });
      await cargarProductos();
      renderProductos();
      mostrarToast(t('productoActualizado'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function toggleActivo(id) {
    try {
      await api('/gerente/productos/' + id + '/toggle', 'PUT');
      await cargarProductos();
      renderProductos();
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function darDeAlta() {
    const nombre = document.getElementById('nuevoNombre').value.trim();
    const precio = parseFloat(document.getElementById('nuevoPrecio').value);
    const iva = parseFloat(document.getElementById('nuevoIva').value) || 0;
    const stock = parseInt(document.getElementById('nuevoStock').value) || 0;
    if (!nombre || !precio) { mostrarToast(t('alertCompletaProducto'), 'error'); return; }

    try {
      await api('/gerente/productos', 'POST', { nombre, precio, iva, stock });
      document.getElementById('nuevoNombre').value = '';
      document.getElementById('nuevoPrecio').value = '';
      document.getElementById('nuevoStock').value = '';
      await cargarProductos();
      renderProductos();
      mostrarToast(t('productoAgregado'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  /* ---------- HISTORIAL ---------- */
  function badgeEstadoVenta(v) {
    if (v.estado === 'cancelada') return `<span class="badge badge-inactivo">${t('estadoCancelada')}</span>`;
    if (v.devuelto > 0) return `<span class="badge badge-efectivo">${t('estadoDevParcial')}</span>`;
    return `<span class="badge badge-activo">${t('estadoCompletada')}</span>`;
  }

  function renderHistorial() {
    // El servidor ya las manda de la más reciente a la más antigua
    document.getElementById('tablaHistorial').innerHTML = historial.map(v => {
      const efectivo = v.metodo.toLowerCase() === 'efectivo';
      const abierta = ventaAbierta === v.folio;
      return `
      <tr>
        <td>#${v.folio}</td><td>${formatearFecha(v.fecha)}</td><td>${v.cajero}</td><td>${v.caja ?? '—'}</td>
        <td><span class="badge ${efectivo ? 'badge-efectivo' : 'badge-tarjeta'}">${efectivo ? t('badgeEfectivo') : t('badgeTarjeta')}</span></td>
        <td>${dinero(v.neto)}${v.devuelto > 0 ? `<br><span style="color:var(--muted);font-size:0.72rem;">${dinero(v.total)} − ${dinero(v.devuelto)} ${t('devueltoLabel')}</span>` : ''}</td>
        <td>${badgeEstadoVenta(v)}</td>
        <td><button class="btn btn-sm" onclick="gestionarVenta(${v.folio})">${abierta ? t('btnOcultar') : t('btnGestionar')}</button></td>
      </tr>
      ${abierta && detalleVenta ? `<tr><td colspan="8">${panelDevolucion()}</td></tr>` : ''}`;
    }).join('') || `<tr><td colspan="8" style="color:var(--muted-light);">${t('sinVentas')}</td></tr>`;
  }

  /* Panel bajo la venta: piezas a devolver por línea, motivo y botones */
  function panelDevolucion() {
    const v = detalleVenta;
    const cancelada = v.estado === 'cancelada';
    const pendiente = v.items.some(i => i.disponible > 0);

    const filas = v.items.map(i => `
      <tr>
        <td>${i.producto}</td><td>${i.cantidad}</td><td>${i.devuelto}</td><td>${dinero(i.precio_unitario)}</td>
        <td>${!cancelada && i.disponible > 0
          ? `<input type="number" id="dev-${i.id_detalle}" min="0" max="${i.disponible}" placeholder="0">`
          : '—'}</td>
      </tr>`).join('');

    const historialDev = v.devoluciones.length ? `
      <h3 style="margin-top:1rem;">${t('historialDevoluciones')}</h3>
      <table>${v.devoluciones.map(d => `
        <tr>
          <td>${formatearFecha(d.fecha)}</td>
          <td>${d.tipo === 'cancelacion' ? t('tipoCancelacion') : t('tipoDevolucion')}</td>
          <td>${d.producto ?? '—'} × ${d.cantidad}</td><td>${dinero(d.monto)}</td>
          <td>${d.motivo}</td><td>${d.usuario ?? ''}</td>
        </tr>`).join('')}</table>` : '';

    const acciones = cancelada
      ? `<p class="desc">${t('canceladaPor')}: ${v.motivo_cancelacion ?? '—'}</p>`
      : `<div class="fila-form">
          <div class="campo"><label>${t('labelMotivo')}</label><input type="text" id="devMotivo" maxlength="255" placeholder="${t('placeholderMotivo')}"></div>
          ${pendiente ? `<button class="btn btn-primario" onclick="devolverPiezas(${v.folio})">${t('btnDevolverSel')}</button>` : ''}
          <button class="btn btn-peligro" onclick="cancelarVenta(${v.folio})">${t('btnCancelarVenta')}</button>
        </div>`;

    return `
      <table>
        <tr><th>${t('thProducto')}</th><th>${t('thVendidos')}</th><th>${t('thDevueltos')}</th><th>${t('thPrecioUnit')}</th><th>${t('thDevolver')}</th></tr>
        ${filas}
      </table>
      <div style="margin-top:0.75rem;">${acciones}</div>
      ${historialDev}`;
  }

  async function gestionarVenta(folio) {
    if (ventaAbierta === folio) { ventaAbierta = null; detalleVenta = null; renderHistorial(); return; }
    try {
      detalleVenta = await api('/gerente/ventas/' + folio);
      ventaAbierta = folio;
      renderHistorial();
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function devolverPiezas(folio) {
    const motivo = document.getElementById('devMotivo').value.trim();
    const items = detalleVenta.items
      .map(i => ({ id_detalle: i.id_detalle, cantidad: parseInt(document.getElementById('dev-' + i.id_detalle)?.value) || 0 }))
      .filter(i => i.cantidad > 0);
    if (!items.length) { mostrarToast(t('alertPiezas'), 'error'); return; }
    if (!motivo) { mostrarToast(t('alertMotivo'), 'error'); return; }

    try {
      detalleVenta = await api(`/gerente/ventas/${folio}/devolucion`, 'POST', { items, motivo });
      await cargarHistorial();
      renderHistorial();
      mostrarToast(t('devolucionRegistrada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function cancelarVenta(folio) {
    const motivo = document.getElementById('devMotivo').value.trim();
    if (!motivo) { mostrarToast(t('alertMotivo'), 'error'); return; }
    if (!(await confirmar(t('confirmCancelarVenta'), t('btnConfirmarCancelacion')))) return;

    try {
      detalleVenta = await api(`/gerente/ventas/${folio}/cancelar`, 'POST', { motivo });
      await cargarHistorial();
      renderHistorial();
      mostrarToast(t('ventaCancelada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  function mostrarToast(mensaje, tipo = 'exito') {
    const cont = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.textContent = mensaje;
    cont.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('saliendo');
      setTimeout(() => toast.remove(), 200);
    }, 3000);
  }

  let _resolverModal = null;
  function confirmar(mensaje, textoBoton = t('btnEliminar')) {
    document.getElementById('modalMensaje').textContent = mensaje;
    document.getElementById('modalConfirmarBtn').textContent = textoBoton;
    document.getElementById('modalOverlay').classList.add('activo');
    return new Promise(resolve => { _resolverModal = resolve; });
  }
  function cerrarModal(resultado) {
    document.getElementById('modalOverlay').classList.remove('activo');
    if (_resolverModal) _resolverModal(resultado);
  }

</script>

</body>
</html>