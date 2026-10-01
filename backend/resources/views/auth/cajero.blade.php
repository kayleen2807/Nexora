<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nexora - Punto de Venta</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f4f5f7;
    --text: #1f2933;
    --card-bg: #ffffff;
    --border: #d1d5db;
    --border-light: #eeeeee;
    --muted: #6b7280;
    --muted-light: #9ca3af;
    --topbar-bg: #1f2933;
    --topbar-text: #ffffff;
    --accent: #0d9488;
    --accent-hover: #0b7d73;
    --accent-bg-light: #e6fffb;
    --sidebar-hover: #f4f5f7;
    --oscuro-btn-bg: #1f2933;
    --oscuro-btn-hover: #111827;
    --disabled-bg: #d1d5db;
    --danger-bg: #fee2e2;
    --danger-text: #dc2626;
    --danger-hover: #fecaca;
    --success-bg: #dcfce7;
    --success-text: #16a34a;
    --success-hover: #bbf7d0;
    --badge-inactivo-bg: #f3f4f6;
    --badge-inactivo-text: #9ca3af;
    --shadow: rgba(0,0,0,0.06);
    --login-bg: #e0f2f1;
  }

  [data-theme="dark"] {
    --bg: #0f1420;
    --text: #e5e7eb;
    --card-bg: #1a2233;
    --border: #374151;
    --border-light: #2b3444;
    --muted: #9ca3af;
    --muted-light: #6b7280;
    --topbar-bg: #0a0e17;
    --topbar-text: #f3f4f6;
    --accent: #14b8a6;
    --accent-hover: #2dd4bf;
    --accent-bg-light: #113a36;
    --sidebar-hover: #232c3d;
    --oscuro-btn-bg: #374151;
    --oscuro-btn-hover: #4b5563;
    --disabled-bg: #374151;
    --danger-bg: #3f1d1d;
    --danger-text: #f87171;
    --danger-hover: #522525;
    --success-bg: #143621;
    --success-text: #4ade80;
    --success-hover: #1c4a2c;
    --badge-inactivo-bg: #2b3444;
    --badge-inactivo-text: #9ca3af;
    --shadow: rgba(0,0,0,0.4);
    --login-bg: #0a0e17;
  }

  body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); transition: background 0.2s, color 0.2s; }

  /* ---------- LOGIN ---------- */
  .login-container { display: flex; align-items: center; justify-content: center; height: 100vh; background: var(--login-bg); position: relative; }
  .login-box { background: var(--card-bg); padding: 2.5rem; border-radius: 16px; box-shadow: 0 8px 30px var(--shadow); width: 320px; text-align: center; }
  .logo { display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 14px; background: var(--accent); color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem; }
  .login-box h1 { font-size: 1.3rem; margin-bottom: 0.25rem; }
  .login-box .tagline { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem; }
  .login-box input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; background: var(--card-bg); color: var(--text); }
  .login-box button { width: 100%; padding: 0.75rem; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.15s; }
  .login-box button:hover { background: var(--accent-hover); }
  .demo-note { font-size: 0.75rem; color: var(--muted-light); margin-top: 1rem; }

  .esquina-superior { position: absolute; top: 1.25rem; right: 1.25rem; display: flex; gap: 0.5rem; }
  .tema-toggle, .idioma-toggle {
    width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border); background: var(--card-bg);
    cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;
  }
  .idioma-toggle { font-size: 0.75rem; font-weight: 700; color: var(--text); }
  .topbar .esquina-superior { position: static; }
  .topbar .tema-toggle, .topbar .idioma-toggle {
    width: 32px; height: 32px; background: transparent; border: 1px solid rgba(255,255,255,0.25); color: var(--topbar-text);
  }

  /* ---------- POS ---------- */
  .pos-view { display: block; min-height: 100vh; }
  .topbar { background: var(--topbar-bg); color: var(--topbar-text); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
  .topbar .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.1rem; }
  .topbar .brand .logo-sm { width: 30px; height: 30px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
  .topbar .sucursal { font-weight: 400; font-size: 0.85rem; color: #cbd2d9; margin-left: 8px; }
  .topbar-derecha { display: flex; align-items: center; gap: 10px; }
  .topbar a { color: var(--topbar-text); text-decoration: none; font-size: 0.9rem; cursor: pointer; }

  .layout { display: grid; grid-template-columns: 200px 1fr; min-height: calc(100vh - 60px); }
  .sidebar { background: var(--card-bg); border-right: 1px solid var(--border-light); padding: 1rem 0; }
  .sidebar button { display: block; width: 100%; text-align: left; padding: 0.7rem 1.25rem; background: none; border: none; font-size: 0.88rem; color: var(--muted); cursor: pointer; border-left: 3px solid transparent; }
  .sidebar button:hover { background: var(--sidebar-hover); }
  .sidebar button.activo { background: var(--accent-bg-light); border-left-color: var(--accent); color: var(--accent-hover); font-weight: 600; }

  .content { padding: 1.5rem; }
  .content h2 { font-size: 1.2rem; margin-bottom: 0.3rem; }
  .content .desc { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }
  .seccion { display: none; }
  .seccion.activa { display: block; }

  .card { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 4px var(--shadow); }
  .card h3 { font-size: 0.95rem; margin-bottom: 1rem; }

  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 0.55rem 0.6rem; border-bottom: 1px solid var(--border-light); text-align: left; font-size: 0.85rem; }
  th { color: var(--muted); font-weight: 600; font-size: 0.78rem; text-transform: uppercase; }

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

  /* ---------- SECCIÓN VENDER ---------- */
  .main { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; max-width: 1100px; margin: 0 auto; }
  .main h2 { font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
  .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }
  .producto-card { background: var(--card-bg); border-radius: 12px; padding: 1rem; box-shadow: 0 1px 4px var(--shadow); text-align: center; }
  .producto-card .nombre { font-size: 0.9rem; font-weight: 600; margin-bottom: 4px; }
  .producto-card .precio { color: var(--accent); font-weight: 700; margin-bottom: 4px; }
  .producto-card .stock { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.6rem; }
  .producto-card button { width: 100%; padding: 0.5rem; background: var(--oscuro-btn-bg); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; }
  .producto-card button:hover { background: var(--oscuro-btn-hover); }
  .producto-card button:disabled { background: var(--disabled-bg); cursor: not-allowed; }

  .carrito { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; height: fit-content; }
  .carrito-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border-light); font-size: 0.85rem; }
  .carrito-item .quitar { color: var(--danger-text); cursor: pointer; margin-left: 8px; }
  .vacio { font-size: 0.85rem; color: var(--muted-light); padding: 0.5rem 0; }
  .total-box { margin-top: 1rem; font-size: 1.3rem; font-weight: 700; text-align: right; color: var(--text); }

  .pago-section { margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-light); }
  .pago-section h3 { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.6rem; color: var(--muted); }
  .metodos-pago { display: flex; gap: 0.5rem; margin-bottom: 0.75rem; }
  .metodo-btn { flex: 1; padding: 0.6rem; border: 1.5px solid var(--border); border-radius: 8px; background: var(--card-bg); cursor: pointer; font-size: 0.85rem; color: var(--muted); text-align: center; }
  .metodo-btn.activo { border-color: var(--accent); background: var(--accent-bg-light); color: var(--accent-hover); font-weight: 600; }
  .efectivo-box { margin-top: 0.5rem; }
  .efectivo-box label { font-size: 0.8rem; color: var(--muted); display: block; margin-bottom: 4px; }
  .efectivo-box input { width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; margin-bottom: 0.5rem; background: var(--card-bg); color: var(--text); }
  .cambio-row { display: flex; justify-content: space-between; font-size: 0.95rem; padding: 0.4rem 0; }
  .cambio-row.insuficiente { color: var(--danger-text); font-weight: 600; }
  .cambio-row.ok { color: var(--success-text); font-weight: 600; }

  .btn-cobrar { width: 100%; margin-top: 1rem; padding: 0.85rem; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; }
  .btn-cobrar:hover { background: #128a3e; }
  .btn-cobrar:disabled { background: var(--disabled-bg); cursor: not-allowed; }

  /* ---------- TICKET ---------- */
  .ticket-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); align-items: center; justify-content: center; z-index: 1000; padding: 1rem; }
  .ticket-overlay.activo { display: flex; }
  .ticket { background: var(--card-bg); color: var(--text); width: 300px; max-height: 90vh; overflow-y: auto; border-radius: 12px; padding: 1.5rem; font-family: 'Courier New', monospace; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
  .ticket h2 { text-align: center; font-size: 1.05rem; margin-bottom: 0.2rem; }
  .ticket-sub { text-align: center; font-size: 0.72rem; color: var(--muted); line-height: 1.4; }
  .ticket .linea { border-top: 1px dashed var(--border); margin: 0.75rem 0; }
  .ticket table { width: 100%; font-size: 0.8rem; }
  .ticket table td { padding: 0.25rem 0; border: none; }
  .ticket .fila-total td { font-weight: 700; font-size: 0.95rem; }
  .ticket-botones { display: flex; gap: 0.5rem; margin-top: 1.25rem; }
  .ticket-botones button { flex: 1; padding: 0.6rem; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
  .btn-cerrar-ticket { background: var(--oscuro-btn-bg); color: #fff; }
  .btn-cerrar-ticket:hover { background: var(--oscuro-btn-hover); }
  .btn-imprimir { background: var(--accent); color: #fff; }
  .btn-imprimir:hover { background: var(--accent-hover); }

  @media print {
    body * { visibility: hidden; }
    #ticketContenido, #ticketContenido * { visibility: visible; }
    .ticket-overlay { position: absolute; inset: auto; top: 0; left: 0; width: 100%; background: none; padding: 0; }
    .ticket { box-shadow: none; margin: 0 auto; width: 100%; max-width: 300px; }
    .ticket-botones { display: none; }
  }
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
      <button class="btn btn-peligro" onclick="cerrarModal(true)" data-i18n="btnEliminar">Eliminar</button>
    </div>
  </div>
</div>

<div class="pos-view" id="posView">
  <div class="topbar">
    <div class="brand"><span class="logo-sm">N</span> Nexora <span class="sucursal" data-i18n="sucursalCentro">— Sucursal Centro</span></div>
    <div class="topbar-derecha">
      <a onclick="cerrarSesion()" data-i18n="cerrarSesion">Cerrar sesión</a>
      <div class="esquina-superior">
        @php $otroIdioma = app()->getLocale() === 'es' ? 'en' : 'es'; @endphp
        <a class="idioma-toggle" id="idiomaTogglePanel" href="{{ route('lang.switch', $otroIdioma) }}" title="Change language">{{ strtoupper($otroIdioma) }}</a>
        <button class="tema-toggle" id="temaTogglePos" onclick="toggleTema()" title="Cambiar tema">🌙</button>
      </div>
    </div>
  </div>

  <div class="layout">
    <div class="sidebar" id="sidebar"></div>
    <div class="content">

      <!-- ---------- VENDER ---------- -->
      <div class="seccion" id="sec-vender">
        <div class="main">
          <div>
            <h2 data-i18n="productosHeading">Productos</h2>
            <div class="productos-grid" id="productosGrid"></div>
          </div>
          <div class="carrito">
            <h2 data-i18n="carritoHeading">Carrito</h2>
            <div id="carritoItems"></div>
            <div class="total-box"><span data-i18n="totalLabel">Total: $</span><span id="total">0.00</span></div>

            <div class="pago-section">
              <h3 data-i18n="metodoPagoHeading">Método de pago</h3>
              <div class="metodos-pago">
                <button type="button" class="metodo-btn activo" id="btnEfectivo" onclick="elegirMetodo('efectivo')" data-i18n="btnEfectivo">Efectivo</button>
                <button type="button" class="metodo-btn" id="btnTarjeta" onclick="elegirMetodo('tarjeta')" data-i18n="btnTarjeta">Tarjeta</button>
              </div>

              <div class="efectivo-box" id="efectivoBox">
                <label for="montoRecibido" data-i18n="efectivoRecibidoLabel">Efectivo recibido</label>
                <input type="number" id="montoRecibido" min="0" step="0.01" placeholder="0.00" oninput="renderCarrito()">
                <div class="cambio-row" id="cambioRow"></div>
              </div>
            </div>

            <button class="btn-cobrar" id="btnCobrar" onclick="cobrar()" disabled data-i18n="btnCobrar">Cobrar</button>
          </div>
        </div>
      </div>

      <!-- ---------- REGISTRAR COMPRAS ---------- -->
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

      <!-- ---------- PRODUCTOS (ALTA / BAJA) ---------- -->
      <div class="seccion" id="sec-productos">
        <h2 data-i18n="productosSecHeading">Productos</h2>
        <p class="desc" data-i18n="productosSecDesc">Registra productos nuevos o da de baja los que ya no se venden.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombre">Nombre</label><input type="text" id="nuevoNombre" data-i18n-placeholder="placeholderProducto" placeholder="Nombre del producto"></div>
            <div class="campo"><label data-i18n="labelPrecio">Precio</label><input type="number" id="nuevoPrecio" min="0" step="0.01" placeholder="0.00"></div>
            <div class="campo"><label data-i18n="labelStockInicial">Stock inicial</label><input type="number" id="nuevoStock" min="0" placeholder="0"></div>
            <button class="btn btn-primario" onclick="darDeAlta()" data-i18n="btnAgregarProducto">Agregar producto</button>
          </div>
        </div>
        <div class="card">
          <table>
            <tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thPrecio">Precio</th><th data-i18n="thStock">Stock</th><th data-i18n="thEstado">Estado</th><th></th></tr>
            <tbody id="tablaProductos"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ---------- TICKET DE VENTA ---------- -->
<div class="ticket-overlay" id="ticketOverlay">
  <div class="ticket">
    <div id="ticketContenido"></div>
    <div class="ticket-botones">
      <button class="btn-cerrar-ticket" onclick="cerrarTicket()" data-i18n="btnNuevaVenta">Nueva venta</button>
      <button class="btn-imprimir" onclick="imprimirTicket()" data-i18n="btnImprimir">Imprimir</button>
    </div>
  </div>
</div>

<script>
  const productos = [
    { id: 1, nombre: 'Refresco 600ml', precio: 18, stock: 12, activo: true },
    { id: 2, nombre: 'Botana 150g', precio: 25.5, stock: 8, activo: true },
    { id: 3, nombre: 'Agua 1L', precio: 12, stock: 20, activo: true },
    { id: 4, nombre: 'Café americano', precio: 32, stock: 15, activo: true },
    { id: 5, nombre: 'Sandwich jamón', precio: 45, stock: 6, activo: true },
    { id: 6, nombre: 'Chicles', precio: 8, stock: 30, activo: true },
  ];
  let carrito = [];
  let compras = [];
  let metodoPago = 'efectivo';
  let numeroTicket = 1000;

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      tagline: 'Punto de venta multi-sucursal',
      placeholderUsuario: 'Usuario',
      placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar',
      demoNote: 'Demo visual — cualquier dato entra',
      sucursalCentro: '— Sucursal Centro',
      cerrarSesion: 'Cerrar sesión',
      navVender: 'Vender',
      navCompras: 'Registrar compras',
      navProductos: 'Productos',
      productosHeading: 'Productos',
      carritoHeading: 'Carrito',
      totalLabel: 'Total: $',
      carritoVacio: 'Carrito vacío',
      metodoPagoHeading: 'Método de pago',
      btnEfectivo: 'Efectivo',
      btnTarjeta: 'Tarjeta',
      efectivoRecibidoLabel: 'Efectivo recibido',
      btnCobrar: 'Cobrar',
      cambioLabel: 'Cambio',
      faltaLabel: 'Falta',
      sinStock: 'Sin stock',
      agregarBtn: 'Agregar',
      stockLabel: 'Stock: ',
      comprasHeading: 'Registrar compras',
      comprasDesc: 'Registra las entradas de mercancía recibidas por el cajero o proveedor.',
      labelProducto: 'Producto',
      labelCantidad: 'Cantidad',
      labelCostoUnitario: 'Costo unitario',
      labelProveedor: 'Proveedor',
      placeholderNombre: 'Nombre',
      btnRegistrar: 'Registrar',
      comprasRegistradasHeading: 'Compras registradas',
      thFecha: 'Fecha', thProducto: 'Producto', thCantidad: 'Cantidad', thCostoUnit: 'Costo unit.', thTotal: 'Total', thProveedor: 'Proveedor',
      sinCompras: 'Sin compras registradas',
      productosSecHeading: 'Productos',
      productosSecDesc: 'Registra productos nuevos o da de baja los que ya no se venden.',
      labelNombre: 'Nombre', labelPrecio: 'Precio', labelStockInicial: 'Stock inicial',
      placeholderProducto: 'Nombre del producto',
      btnAgregarProducto: 'Agregar producto',
      thNombre: 'Nombre', thPrecio: 'Precio', thStock: 'Stock', thEstado: 'Estado',
      badgeActivo: 'Activo', badgeInactivo: 'Inactivo',
      btnDarBaja: 'Dar de baja', btnReactivar: 'Reactivar',
      ticketSucursal: 'Sucursal Centro', ticketFolio: 'Ticket #', ticketTotal: 'Total',
      ticketMetodoPago: 'Método de pago', ticketEfectivo: 'Efectivo', ticketTarjeta: 'Tarjeta',
      ticketRecibido: 'Recibido', ticketCambio: 'Cambio', ticketGracias: '¡Gracias por su compra!',
      btnNuevaVenta: 'Nueva venta', btnImprimir: 'Imprimir',
      alertCompletaCompra: 'Completa producto, cantidad y costo.',
      alertCompletaProducto: 'Completa al menos nombre y precio.',
    },
    en: {
      tagline: 'Multi-branch point of sale',
      placeholderUsuario: 'Username',
      placeholderClave: 'Password',
      btnEntrar: 'Log in',
      demoNote: 'Visual demo — any data works',
      sucursalCentro: '— Centro Branch',
      cerrarSesion: 'Log out',
      navVender: 'Sell',
      navCompras: 'Register purchases',
      navProductos: 'Products',
      productosHeading: 'Products',
      carritoHeading: 'Cart',
      totalLabel: 'Total: $',
      carritoVacio: 'Cart is empty',
      metodoPagoHeading: 'Payment method',
      btnEfectivo: 'Cash',
      btnTarjeta: 'Card',
      efectivoRecibidoLabel: 'Cash received',
      btnCobrar: 'Charge',
      cambioLabel: 'Change',
      faltaLabel: 'Missing',
      sinStock: 'Out of stock',
      agregarBtn: 'Add',
      stockLabel: 'Stock: ',
      comprasHeading: 'Register purchases',
      comprasDesc: 'Log incoming stock received by the cashier or supplier.',
      labelProducto: 'Product',
      labelCantidad: 'Quantity',
      labelCostoUnitario: 'Unit cost',
      labelProveedor: 'Supplier',
      placeholderNombre: 'Name',
      btnRegistrar: 'Register',
      comprasRegistradasHeading: 'Registered purchases',
      thFecha: 'Date', thProducto: 'Product', thCantidad: 'Quantity', thCostoUnit: 'Unit cost', thTotal: 'Total', thProveedor: 'Supplier',
      sinCompras: 'No purchases registered',
      productosSecHeading: 'Products',
      productosSecDesc: 'Register new products or deactivate ones no longer sold.',
      labelNombre: 'Name', labelPrecio: 'Price', labelStockInicial: 'Initial stock',
      placeholderProducto: 'Product name',
      btnAgregarProducto: 'Add product',
      thNombre: 'Name', thPrecio: 'Price', thStock: 'Stock', thEstado: 'Status',
      badgeActivo: 'Active', badgeInactivo: 'Inactive',
      btnDarBaja: 'Deactivate', btnReactivar: 'Reactivate',
      ticketSucursal: 'Centro Branch', ticketFolio: 'Receipt #', ticketTotal: 'Total',
      ticketMetodoPago: 'Payment method', ticketEfectivo: 'Cash', ticketTarjeta: 'Card',
      ticketRecibido: 'Received', ticketCambio: 'Change', ticketGracias: 'Thank you for your purchase!',
      btnNuevaVenta: 'New sale', btnImprimir: 'Print',
      alertCompletaCompra: 'Fill in product, quantity, and cost.',
      alertCompletaProducto: 'Fill in at least name and price.',
    }
  };
  let idioma = '{{ app()->getLocale() }}';
  function t(clave) { return textos[idioma][clave] || clave; }

  const secciones = [
    { id: 'vender', key: 'navVender' },
    { id: 'compras', key: 'navCompras' },
    { id: 'productos', key: 'navProductos' },
  ];
  let seccionActual = 'vender';

  /* ---------- TEMA CLARO / OSCURO ---------- */
  function aplicarTema(tema) {
    document.documentElement.setAttribute('data-theme', tema);
    const icono = tema === 'dark' ? '☀️' : '🌙';
    const btnLogin = document.getElementById('temaToggleLogin');
    const btnPos = document.getElementById('temaTogglePos');
    if (btnLogin) btnLogin.textContent = icono;
    if (btnPos) btnPos.textContent = icono;
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

  /* ---------- IDIOMA: cambio y aplicación ---------- */
  function toggleIdioma() {
    idioma = idioma === 'es' ? 'en' : 'es';
    try { localStorage.setItem('nexora-idioma', idioma); } catch (e) {}
    aplicarIdioma();
  }

  function aplicarIdioma() {
    document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.getAttribute('data-i18n')); });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.getAttribute('data-i18n-placeholder')); });
    const etiqueta = idioma === 'es' ? 'EN' : 'ES';
    const bLogin = document.getElementById('idiomaToggleLogin');
    const bPos = document.getElementById('idiomaTogglePos');
    if (bLogin) bLogin.textContent = etiqueta;
    if (bPos) bPos.textContent = etiqueta;
    document.documentElement.lang = idioma;
    renderSidebar();
    mostrarSeccion(seccionActual, true);
  }

  (function inicializarIdioma() {
    let guardado = null;
    try { guardado = localStorage.getItem('nexora-idioma'); } catch (e) {}
    idioma = (guardado === 'en' || guardado === 'es') ? guardado : 'es';
    aplicarIdioma();
  })();

  function cerrarSesion() {
    carrito = [];
    document.getElementById('logoutForm').submit();
  }

  renderSidebar();
  mostrarSeccion('vender');

  /* ---------- NAVEGACIÓN ---------- */
  function renderSidebar() {
    const sb = document.getElementById('sidebar');
    sb.innerHTML = secciones.map(s => `<button id="btn-${s.id}" onclick="mostrarSeccion('${s.id}')">${t(s.key)}</button>`).join('');
    secciones.forEach(s => document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === seccionActual));
  }

  function mostrarSeccion(id, soloRefrescar) {
    seccionActual = id;
    secciones.forEach(s => {
      document.getElementById('sec-' + s.id).classList.toggle('activa', s.id === id);
      document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === id);
    });
    if (id === 'vender') { renderProductos(); renderCarrito(); }
    if (id === 'compras') renderCompras();
    if (id === 'productos') renderProductosAdmin();
  }

  /* ---------- VENDER ---------- */
  function elegirMetodo(metodo) {
    metodoPago = metodo;
    document.getElementById('btnEfectivo').classList.toggle('activo', metodo === 'efectivo');
    document.getElementById('btnTarjeta').classList.toggle('activo', metodo === 'tarjeta');
    document.getElementById('efectivoBox').style.display = metodo === 'efectivo' ? 'block' : 'none';
    renderCarrito();
  }

  function renderProductos() {
    const grid = document.getElementById('productosGrid');
    grid.innerHTML = productos.filter(p => p.activo).map(p => {
      const enCarrito = carrito.find(i => i.id === p.id);
      const disponible = p.stock - (enCarrito ? enCarrito.cantidad : 0);
      return `
        <div class="producto-card">
          <div class="nombre">${p.nombre}</div>
          <div class="precio">$${p.precio.toFixed(2)}</div>
          <div class="stock">${t('stockLabel')}${disponible}</div>
          <button onclick="agregarAlCarrito(${p.id})" ${disponible <= 0 ? 'disabled' : ''}>
            ${disponible <= 0 ? t('sinStock') : t('agregarBtn')}
          </button>
        </div>`;
    }).join('');
  }

  function agregarAlCarrito(id) {
    const p = productos.find(x => x.id === id);
    const existente = carrito.find(i => i.id === id);
    const enCarritoActual = existente ? existente.cantidad : 0;
    if (enCarritoActual + 1 > p.stock) return;
    if (existente) existente.cantidad++;
    else carrito.push({ ...p, cantidad: 1 });
    renderProductos();
    renderCarrito();
  }

  function quitarDelCarrito(id) {
    carrito = carrito.filter(i => i.id !== id);
    renderProductos();
    renderCarrito();
  }

  function renderCarrito() {
    const cont = document.getElementById('carritoItems');
    const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);

    cont.innerHTML = carrito.length
      ? carrito.map(i => `
          <div class="carrito-item">
            <span>${i.nombre} x${i.cantidad}</span>
            <span>$${(i.precio * i.cantidad).toFixed(2)}<span class="quitar" onclick="quitarDelCarrito(${i.id})">✕</span></span>
          </div>`).join('')
      : `<div class="vacio">${t('carritoVacio')}</div>`;

    document.getElementById('total').textContent = total.toFixed(2);

    const cambioRow = document.getElementById('cambioRow');
    let puedeCobrar = carrito.length > 0;

    if (metodoPago === 'efectivo') {
      const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
      const cambio = recibido - total;
      if (carrito.length === 0) {
        cambioRow.innerHTML = '';
      } else if (recibido === 0) {
        cambioRow.innerHTML = '';
        puedeCobrar = false;
      } else if (cambio < 0) {
        cambioRow.className = 'cambio-row insuficiente';
        cambioRow.innerHTML = `<span>${t('faltaLabel')}</span><span>$${Math.abs(cambio).toFixed(2)}</span>`;
        puedeCobrar = false;
      } else {
        cambioRow.className = 'cambio-row ok';
        cambioRow.innerHTML = `<span>${t('cambioLabel')}</span><span>$${cambio.toFixed(2)}</span>`;
      }
    } else {
      cambioRow.innerHTML = '';
    }

    document.getElementById('btnCobrar').disabled = !puedeCobrar;
  }

  function cobrar() {
    if (carrito.length === 0) return;
    const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
    const recibido = metodoPago === 'efectivo' ? (parseFloat(document.getElementById('montoRecibido').value) || 0) : null;
    const cambio = metodoPago === 'efectivo' ? recibido - total : null;

    numeroTicket++;
    const ventaTicket = {
      folio: numeroTicket,
      fecha: new Date().toLocaleString(idioma === 'es' ? 'es-MX' : 'en-US'),
      items: carrito.map(i => ({ nombre: i.nombre, cantidad: i.cantidad, precio: i.precio })),
      total, metodo: metodoPago, recibido, cambio,
    };

    carrito.forEach(item => {
      const p = productos.find(x => x.id === item.id);
      p.stock -= item.cantidad;
    });

    mostrarTicket(ventaTicket);
    carrito = [];
    document.getElementById('montoRecibido').value = '';
    elegirMetodo('efectivo');
    renderProductos();
    renderCarrito();
  }

  /* ---------- TICKET ---------- */
  function mostrarTicket(venta) {
    const filasItems = venta.items.map(i => `
      <tr>
        <td>${i.nombre}<br><span style="color:var(--muted);font-size:0.72rem;">${i.cantidad} x $${i.precio.toFixed(2)}</span></td>
        <td style="text-align:right;">$${(i.precio * i.cantidad).toFixed(2)}</td>
      </tr>`).join('');

    const filasPago = venta.metodo === 'efectivo'
      ? `<tr><td>${t('ticketRecibido')}</td><td style="text-align:right;">$${venta.recibido.toFixed(2)}</td></tr>
         <tr><td>${t('ticketCambio')}</td><td style="text-align:right;">$${venta.cambio.toFixed(2)}</td></tr>`
      : '';

    document.getElementById('ticketContenido').innerHTML = `
      <h2>Nexora</h2>
      <div class="ticket-sub">${t('ticketSucursal')}<br>${t('ticketFolio')}${venta.folio}<br>${venta.fecha}</div>
      <div class="linea"></div>
      <table>${filasItems}</table>
      <div class="linea"></div>
      <table>
        <tr class="fila-total"><td>${t('ticketTotal')}</td><td style="text-align:right;">$${venta.total.toFixed(2)}</td></tr>
        <tr><td>${t('ticketMetodoPago')}</td><td style="text-align:right;">${venta.metodo === 'efectivo' ? t('ticketEfectivo') : t('ticketTarjeta')}</td></tr>
        ${filasPago}
      </table>
      <div class="linea"></div>
      <div class="ticket-sub">${t('ticketGracias')}</div>
    `;
    document.getElementById('ticketOverlay').classList.add('activo');
  }

  function cerrarTicket() { document.getElementById('ticketOverlay').classList.remove('activo'); }
  function imprimirTicket() { window.print(); }

  /* ---------- REGISTRAR COMPRAS ---------- */
  function renderCompras() {
    const select = document.getElementById('compraProducto');
    select.innerHTML = productos.filter(p => p.activo).map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
    document.getElementById('tablaCompras').innerHTML = compras.slice().reverse().map(c => `
      <tr>
        <td>${c.fecha}</td><td>${c.nombre}</td><td>${c.cantidad}</td>
        <td>$${c.costo.toFixed(2)}</td><td>$${(c.costo * c.cantidad).toFixed(2)}</td><td>${c.proveedor || '—'}</td>
      </tr>`).join('') || `<tr><td colspan="6" style="color:var(--muted-light);">${t('sinCompras')}</td></tr>`;
  }

  function registrarCompra() {
    const id = parseInt(document.getElementById('compraProducto').value);
    const cantidad = parseInt(document.getElementById('compraCantidad').value);
    const costo = parseFloat(document.getElementById('compraCosto').value);
    const proveedor = document.getElementById('compraProveedor').value;
    if (!id || !cantidad || !costo) { alert(t('alertCompletaCompra')); return; }

    const p = productos.find(x => x.id === id);
    p.stock += cantidad;
    compras.push({ fecha: new Date().toLocaleString(idioma === 'es' ? 'es-MX' : 'en-US'), nombre: p.nombre, cantidad, costo, proveedor });

    document.getElementById('compraCantidad').value = '';
    document.getElementById('compraCosto').value = '';
    document.getElementById('compraProveedor').value = '';
    renderCompras();
  }

  /* ---------- ALTA / BAJA DE PRODUCTOS ---------- */
  function renderProductosAdmin() {
    document.getElementById('tablaProductos').innerHTML = productos.map(p => `
      <tr>
        <td>${p.nombre}</td>
        <td>$${p.precio.toFixed(2)}</td>
        <td>${p.stock}</td>
        <td><span class="badge ${p.activo ? 'badge-activo' : 'badge-inactivo'}">${p.activo ? t('badgeActivo') : t('badgeInactivo')}</span></td>
        <td><button class="btn btn-sm ${p.activo ? 'btn-peligro' : 'btn-exito'}" onclick="toggleActivo(${p.id})">${p.activo ? t('btnDarBaja') : t('btnReactivar')}</button></td>
      </tr>`).join('');
  }

  function toggleActivo(id) {
    const p = productos.find(x => x.id === id);
    p.activo = !p.activo;
    renderProductosAdmin();
  }

  function darDeAlta() {
    const nombre = document.getElementById('nuevoNombre').value.trim();
    const precio = parseFloat(document.getElementById('nuevoPrecio').value);
    const stock = parseInt(document.getElementById('nuevoStock').value) || 0;
    if (!nombre || !precio) { alert(t('alertCompletaProducto')); return; }

    const nuevoId = Math.max(0, ...productos.map(p => p.id)) + 1;
    productos.push({ id: nuevoId, nombre, precio, stock, activo: true });

    document.getElementById('nuevoNombre').value = '';
    document.getElementById('nuevoPrecio').value = '';
    document.getElementById('nuevoStock').value = '';
    renderProductosAdmin();
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
  function confirmar(mensaje) {
    document.getElementById('modalMensaje').textContent = mensaje;
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