<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</style>
</head>
<body>

<div class="panel-view" id="panelView">
  <div class="topbar">
    <div class="brand"><span class="logo-sm">N</span> Nexora <span class="sucursal" data-i18n="topbarSucursal">— Panel de Gerente · Sucursal Centro</span></div>
    <div class="topbar-derecha">
      <a onclick="cerrarSesion()" data-i18n="cerrarSesion">Cerrar sesión</a>
      <div class="esquina-superior">
        <button class="idioma-toggle" id="idiomaTogglePanel" onclick="toggleIdioma()" title="Change language">ES</button>
        <button class="tema-toggle" id="temaTogglePanel" onclick="toggleTema()" title="Cambiar tema">🌙</button>
      </div>
    </div>
  </div>
  <div class="layout">
    <div class="sidebar" id="sidebar"></div>
    <div class="content">

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
        <h2 data-i18n="corteHeading">Corte de caja</h2>
        <p class="desc" data-i18n="corteDesc">Compara lo vendido hoy contra el efectivo físico contado.</p>
        <div class="resumen-grid">
          <div class="resumen-card"><div class="label" data-i18n="ventasEfectivo">Ventas en efectivo</div><div class="valor" id="resEfectivo">$0.00</div></div>
          <div class="resumen-card"><div class="label" data-i18n="ventasTarjeta">Ventas con tarjeta</div><div class="valor" id="resTarjeta">$0.00</div></div>
          <div class="resumen-card"><div class="label" data-i18n="totalDia">Total del día</div><div class="valor" id="resTotal">$0.00</div></div>
        </div>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="fondoInicialLabel">Fondo inicial de caja</label><input type="number" id="fondoInicial" min="0" step="0.01" value="500" oninput="calcularCorte()"></div>
            <div class="campo"><label data-i18n="efectivoContadoLabel">Efectivo contado físicamente</label><input type="number" id="efectivoContado" min="0" step="0.01" placeholder="0.00" oninput="calcularCorte()"></div>
          </div>
          <div id="diferenciaCorte"></div>
        </div>
      </div>

      <div class="seccion" id="sec-cajas">
        <h2 data-i18n="cajasHeading">Registro de cajas</h2>
        <p class="desc" data-i18n="cajasDesc">Administra las cajas registradoras disponibles en esta sucursal.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="cajaNombreLabel">Nombre de la caja</label><input type="text" id="cajaNombre" placeholder="Caja 3"></div>
            <button class="btn btn-primario" onclick="agregarCaja()" data-i18n="btnAgregarCaja">Agregar caja</button>
          </div>
        </div>
        <div class="card">
          <table>
            <tr><th data-i18n="thCaja">Caja</th><th data-i18n="thEstado">Estado</th><th></th><th></th></tr>
            <tbody id="tablaCajas"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-precios">
        <h2 data-i18n="preciosHeading">Precios e IVA</h2>
        <p class="desc" data-i18n="preciosDesc">Modifica el precio base y el porcentaje de IVA de cada producto.</p>
        <div class="card">
          <table>
            <tr><th data-i18n="thProducto">Producto</th><th data-i18n="thPrecioBase">Precio base</th><th data-i18n="thIva">IVA %</th><th data-i18n="thPrecioFinal">Precio final</th><th></th></tr>
            <tbody id="tablaPrecios"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-productos">
        <h2 data-i18n="productosHeading">Alta / baja de productos</h2>
        <p class="desc" data-i18n="productosDesc">Agrega nuevos productos al catálogo o desactiva los que ya no se venden.</p>
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
            <tr><th data-i18n="thProducto">Producto</th><th data-i18n="thPrecio">Precio</th><th data-i18n="thEstado">Estado</th><th></th></tr>
            <tbody id="tablaProductos"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-historial">
        <h2 data-i18n="historialHeading">Historial de ventas</h2>
        <p class="desc" data-i18n="historialDesc">Todas las ventas registradas en esta sucursal.</p>
        <div class="card">
          <table>
            <tr><th data-i18n="thFecha">Fecha</th><th data-i18n="thCajero">Cajero</th><th data-i18n="thMetodo">Método</th><th data-i18n="thTotal">Total</th></tr>
            <tbody id="tablaHistorial"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  let productos = [
    { id: 1, nombre: 'Refresco 600ml', precio: 18, iva: 16, stock: 12, activo: true },
    { id: 2, nombre: 'Botana 150g', precio: 25.5, iva: 16, stock: 8, activo: true },
    { id: 3, nombre: 'Agua 1L', precio: 12, iva: 16, stock: 20, activo: true },
    { id: 4, nombre: 'Café americano', precio: 32, iva: 16, stock: 15, activo: true },
    { id: 5, nombre: 'Sandwich jamón', precio: 45, iva: 16, stock: 6, activo: true },
    { id: 6, nombre: 'Chicles', precio: 8, iva: 0, stock: 30, activo: true },
  ];
  let compras = [];
  let historial = [
    { fecha: '25/08/2026 09:14', cajero: 'Cajero 1', metodo: 'efectivo', total: 55.5 },
    { fecha: '25/08/2026 10:02', cajero: 'Cajero 1', metodo: 'tarjeta', total: 90.0 },
    { fecha: '25/08/2026 11:47', cajero: 'Cajero 1', metodo: 'efectivo', total: 32.0 },
    { fecha: '25/08/2026 13:20', cajero: 'Cajero 1', metodo: 'tarjeta', total: 130.5 },
  ];
  let cajas = [
    { id: 1, nombre: 'Caja 1', abierta: true },
    { id: 2, nombre: 'Caja 2', abierta: false },
  ];

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      tagline: 'Panel de gerente', placeholderUsuario: 'Usuario', placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar', demoNote: 'Demo visual — cualquier dato entra',
      topbarSucursal: '— Panel de Gerente · Sucursal Centro', cerrarSesion: 'Cerrar sesión',
      navInventario: 'Inventario', navCompras: 'Registrar compras', navCorte: 'Corte de caja',
      navCajas: 'Registro de cajas', navPrecios: 'Precios e IVA', navProductos: 'Alta / baja productos',
      navHistorial: 'Historial de ventas',
      invHeading: 'Inventario', invDesc: 'Consulta y ajusta las existencias de tu sucursal.',
      thProducto: 'Producto', thStockActual: 'Stock actual', thAjustar: 'Ajustar', btnAplicar: 'Aplicar',
      comprasHeading: 'Registrar compras', comprasDesc: 'Registra las entradas de mercancía recibidas por el cajero o proveedor.',
      labelProducto: 'Producto', labelCantidad: 'Cantidad', labelCostoUnitario: 'Costo unitario', labelProveedor: 'Proveedor',
      placeholderNombre: 'Nombre', btnRegistrar: 'Registrar', comprasRegistradasHeading: 'Compras registradas',
      thFecha: 'Fecha', thCantidad: 'Cantidad', thCostoUnit: 'Costo unit.', thTotal: 'Total', thProveedor: 'Proveedor',
      sinCompras: 'Sin compras registradas',
      corteHeading: 'Corte de caja', corteDesc: 'Compara lo vendido hoy contra el efectivo físico contado.',
      ventasEfectivo: 'Ventas en efectivo', ventasTarjeta: 'Ventas con tarjeta', totalDia: 'Total del día',
      fondoInicialLabel: 'Fondo inicial de caja', efectivoContadoLabel: 'Efectivo contado físicamente',
      efectivoEsperado: 'Efectivo esperado en caja', sobrante: 'Sobrante', faltante: 'Faltante', cajaCuadrada: 'Caja cuadrada',
      cajasHeading: 'Registro de cajas', cajasDesc: 'Administra las cajas registradoras disponibles en esta sucursal.',
      cajaNombreLabel: 'Nombre de la caja', btnAgregarCaja: 'Agregar caja', thCaja: 'Caja', thEstado: 'Estado',
      badgeAbierta: 'Abierta', badgeCerrada: 'Cerrada', btnAbrir: 'Abrir', btnCerrar: 'Cerrar', btnEliminar: 'Eliminar',
      sinCajas: 'Sin cajas registradas', alertNombreCaja: 'Ingresa el nombre de la caja.', confirmEliminarCaja: '¿Eliminar esta caja?',
      preciosHeading: 'Precios e IVA', preciosDesc: 'Modifica el precio base y el porcentaje de IVA de cada producto.',
      thPrecioBase: 'Precio base', thIva: 'IVA %', thPrecioFinal: 'Precio final', btnGuardar: 'Guardar',
      productosHeading: 'Alta / baja de productos', productosDesc: 'Agrega nuevos productos al catálogo o desactiva los que ya no se venden.',
      labelNombre: 'Nombre', labelStockInicial: 'Stock inicial', placeholderProducto: 'Producto',
      btnDarAlta: 'Dar de alta', thPrecio: 'Precio', badgeActivo: 'Activo', badgeInactivo: 'Inactivo',
      btnDarBaja: 'Dar de baja', btnReactivar: 'Reactivar', alertCompletaProducto: 'Completa al menos nombre y precio.',
      historialHeading: 'Historial de ventas', historialDesc: 'Todas las ventas registradas en esta sucursal.',
      thCajero: 'Cajero', thMetodo: 'Método', badgeEfectivo: 'Efectivo', badgeTarjeta: 'Tarjeta',
      alertCompletaCompra: 'Completa producto, cantidad y costo.',
    },
    en: {
      tagline: 'Manager panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarSucursal: '— Manager Panel · Centro Branch', cerrarSesion: 'Log out',
      navInventario: 'Inventory', navCompras: 'Register purchases', navCorte: 'Cash count',
      navCajas: 'Cash registers', navPrecios: 'Prices & tax', navProductos: 'Add / remove products',
      navHistorial: 'Sales history',
      invHeading: 'Inventory', invDesc: 'Check and adjust the stock at your branch.',
      thProducto: 'Product', thStockActual: 'Current stock', thAjustar: 'Adjust', btnAplicar: 'Apply',
      comprasHeading: 'Register purchases', comprasDesc: 'Log incoming stock received by the cashier or supplier.',
      labelProducto: 'Product', labelCantidad: 'Quantity', labelCostoUnitario: 'Unit cost', labelProveedor: 'Supplier',
      placeholderNombre: 'Name', btnRegistrar: 'Register', comprasRegistradasHeading: 'Registered purchases',
      thFecha: 'Date', thCantidad: 'Quantity', thCostoUnit: 'Unit cost', thTotal: 'Total', thProveedor: 'Supplier',
      sinCompras: 'No purchases registered',
      corteHeading: 'Cash count', corteDesc: "Compare today's sales against the physical cash counted.",
      ventasEfectivo: 'Cash sales', ventasTarjeta: 'Card sales', totalDia: "Today's total",
      fondoInicialLabel: 'Starting cash fund', efectivoContadoLabel: 'Physical cash counted',
      efectivoEsperado: 'Expected cash in register', sobrante: 'Overage', faltante: 'Shortage', cajaCuadrada: 'Register balanced',
      cajasHeading: 'Cash registers', cajasDesc: 'Manage the cash registers available at this branch.',
      cajaNombreLabel: 'Register name', btnAgregarCaja: 'Add register', thCaja: 'Register', thEstado: 'Status',
      badgeAbierta: 'Open', badgeCerrada: 'Closed', btnAbrir: 'Open', btnCerrar: 'Close', btnEliminar: 'Delete',
      sinCajas: 'No registers added', alertNombreCaja: 'Enter the register name.', confirmEliminarCaja: 'Delete this register?',
      preciosHeading: 'Prices & tax', preciosDesc: 'Edit the base price and tax rate for each product.',
      thPrecioBase: 'Base price', thIva: 'Tax %', thPrecioFinal: 'Final price', btnGuardar: 'Save',
      productosHeading: 'Add / remove products', productosDesc: 'Add new products to the catalog or deactivate ones no longer sold.',
      labelNombre: 'Name', labelStockInicial: 'Initial stock', placeholderProducto: 'Product',
      btnDarAlta: 'Add product', thPrecio: 'Price', badgeActivo: 'Active', badgeInactivo: 'Inactive',
      btnDarBaja: 'Deactivate', btnReactivar: 'Reactivate', alertCompletaProducto: 'Fill in at least name and price.',
      historialHeading: 'Sales history', historialDesc: 'All sales recorded at this branch.',
      thCajero: 'Cashier', thMetodo: 'Method', badgeEfectivo: 'Cash', badgeTarjeta: 'Card',
      alertCompletaCompra: 'Fill in product, quantity, and cost.',
    }
  };
  let idioma = 'es';
  function t(clave) { return textos[idioma][clave] || clave; }

  const secciones = [
    { id: 'inventario', key: 'navInventario' },
    { id: 'compras', key: 'navCompras' },
    { id: 'corte', key: 'navCorte' },
    { id: 'cajas', key: 'navCajas' },
    { id: 'precios', key: 'navPrecios' },
    { id: 'productos', key: 'navProductos' },
    { id: 'historial', key: 'navHistorial' },
  ];
  let seccionActual = 'inventario';

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
    const bPanel = document.getElementById('idiomaTogglePanel');
    if (bLogin) bLogin.textContent = etiqueta;
    if (bPanel) bPanel.textContent = etiqueta;
    document.documentElement.lang = idioma;
    renderSidebar();
    mostrarSeccion(seccionActual);
  }

  (function inicializarIdioma() {
    let guardado = null;
    try { guardado = localStorage.getItem('nexora-idioma'); } catch (e) {}
    idioma = (guardado === 'en' || guardado === 'es') ? guardado : 'es';
    aplicarIdioma();
  })();

  function cerrarSesion() {
    // TODO: conectar con la ruta real de logout de Laravel (auth)
  }

  renderSidebar();
  mostrarSeccion('inventario');

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
    if (id === 'inventario') renderInventario();
    if (id === 'compras') renderCompras();
    if (id === 'corte') renderCorte();
    if (id === 'cajas') renderCajas();
    if (id === 'precios') renderPrecios();
    if (id === 'productos') renderProductosAdmin();
    if (id === 'historial') renderHistorial();
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

  function ajustarStock(id) {
    const input = document.getElementById('ajuste-' + id);
    const valor = parseInt(input.value);
    if (!valor) return;
    const p = productos.find(x => x.id === id);
    p.stock = Math.max(0, p.stock + valor);
    input.value = '';
    renderInventario();
  }

  /* ---------- COMPRAS ---------- */
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

  /* ---------- CORTE DE CAJA ---------- */
  function renderCorte() {
    const efectivo = historial.filter(v => v.metodo === 'efectivo').reduce((s, v) => s + v.total, 0);
    const tarjeta = historial.filter(v => v.metodo === 'tarjeta').reduce((s, v) => s + v.total, 0);
    document.getElementById('resEfectivo').textContent = '$' + efectivo.toFixed(2);
    document.getElementById('resTarjeta').textContent = '$' + tarjeta.toFixed(2);
    document.getElementById('resTotal').textContent = '$' + (efectivo + tarjeta).toFixed(2);
    calcularCorte();
  }

  function calcularCorte() {
    const efectivoVentas = historial.filter(v => v.metodo === 'efectivo').reduce((s, v) => s + v.total, 0);
    const fondo = parseFloat(document.getElementById('fondoInicial').value) || 0;
    const contado = parseFloat(document.getElementById('efectivoContado').value);
    const esperado = fondo + efectivoVentas;
    const div = document.getElementById('diferenciaCorte');

    if (isNaN(contado)) {
      div.innerHTML = `<div class="diferencia-row"><span>${t('efectivoEsperado')}</span><span>$${esperado.toFixed(2)}</span></div>`;
      return;
    }
    const diferencia = contado - esperado;
    let clase = 'cuadrado', texto = t('cajaCuadrada');
    if (diferencia > 0) { clase = 'sobrante'; texto = t('sobrante'); }
    if (diferencia < 0) { clase = 'faltante'; texto = t('faltante'); }

    div.innerHTML = `
      <div class="diferencia-row"><span>${t('efectivoEsperado')}</span><span>$${esperado.toFixed(2)}</span></div>
      <div class="diferencia-row ${clase}"><span>${texto}</span><span>$${Math.abs(diferencia).toFixed(2)}</span></div>`;
  }

  /* ---------- REGISTRO DE CAJAS ---------- */
  function renderCajas() {
    document.getElementById('tablaCajas').innerHTML = cajas.map(c => `
      <tr>
        <td>${c.nombre}</td>
        <td><span class="badge ${c.abierta ? 'badge-activo' : 'badge-inactivo'}">${c.abierta ? t('badgeAbierta') : t('badgeCerrada')}</span></td>
        <td><button class="btn btn-sm ${c.abierta ? 'btn-peligro' : 'btn-exito'}" onclick="toggleCaja(${c.id})">${c.abierta ? t('btnCerrar') : t('btnAbrir')}</button></td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarCaja(${c.id})">${t('btnEliminar')}</button></td>
      </tr>`).join('') || `<tr><td colspan="4" style="color:var(--muted-light);">${t('sinCajas')}</td></tr>`;
  }

  function agregarCaja() {
    const nombre = document.getElementById('cajaNombre').value.trim();
    if (!nombre) { alert(t('alertNombreCaja')); return; }
    const id = Math.max(0, ...cajas.map(c => c.id)) + 1;
    cajas.push({ id, nombre, abierta: true });
    document.getElementById('cajaNombre').value = '';
    renderCajas();
  }

  function toggleCaja(id) {
    const c = cajas.find(x => x.id === id);
    c.abierta = !c.abierta;
    renderCajas();
  }

  function eliminarCaja(id) {
    if (!confirm(t('confirmEliminarCaja'))) return;
    cajas = cajas.filter(c => c.id !== id);
    renderCajas();
  }

  /* ---------- PRECIOS E IVA ---------- */
  function renderPrecios() {
    document.getElementById('tablaPrecios').innerHTML = productos.filter(p => p.activo).map(p => `
      <tr>
        <td>${p.nombre}</td>
        <td><input type="number" id="precio-${p.id}" value="${p.precio}" step="0.01"></td>
        <td><input type="number" id="iva-${p.id}" value="${p.iva}" step="0.01"></td>
        <td id="final-${p.id}">$${(p.precio * (1 + p.iva / 100)).toFixed(2)}</td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarPrecio(${p.id})">${t('btnGuardar')}</button></td>
      </tr>`).join('');
  }

  function guardarPrecio(id) {
    const p = productos.find(x => x.id === id);
    p.precio = parseFloat(document.getElementById('precio-' + id).value) || p.precio;
    p.iva = parseFloat(document.getElementById('iva-' + id).value) ?? p.iva;
    document.getElementById('final-' + id).textContent = '$' + (p.precio * (1 + p.iva / 100)).toFixed(2);
  }

  /* ---------- ALTA / BAJA PRODUCTOS ---------- */
  function renderProductosAdmin() {
    document.getElementById('tablaProductos').innerHTML = productos.map(p => `
      <tr>
        <td>${p.nombre}</td>
        <td>$${p.precio.toFixed(2)}</td>
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
    const iva = parseFloat(document.getElementById('nuevoIva').value) || 0;
    const stock = parseInt(document.getElementById('nuevoStock').value) || 0;
    if (!nombre || !precio) { alert(t('alertCompletaProducto')); return; }

    const nuevoId = Math.max(...productos.map(p => p.id)) + 1;
    productos.push({ id: nuevoId, nombre, precio, iva, stock, activo: true });

    document.getElementById('nuevoNombre').value = '';
    document.getElementById('nuevoPrecio').value = '';
    document.getElementById('nuevoStock').value = '';
    renderProductosAdmin();
  }

  /* ---------- HISTORIAL ---------- */
  function renderHistorial() {
    document.getElementById('tablaHistorial').innerHTML = historial.slice().reverse().map(v => `
      <tr>
        <td>${v.fecha}</td><td>${v.cajero}</td>
        <td><span class="badge ${v.metodo === 'efectivo' ? 'badge-efectivo' : 'badge-tarjeta'}">${v.metodo === 'efectivo' ? t('badgeEfectivo') : t('badgeTarjeta')}</span></td>
        <td>$${v.total.toFixed(2)}</td>
      </tr>`).join('');
  }
</script>

</body>
</html>