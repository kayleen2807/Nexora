<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora - Panel de Administrador</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f4f5f7; --text: #1f2933; --card-bg: #ffffff; --border: #d1d5db; --border-light: #eeeeee;
    --muted: #6b7280; --muted-light: #9ca3af; --topbar-bg: #1f2933; --topbar-text: #ffffff;
    --accent: #0d9488; --accent-hover: #0b7d73; --accent-bg-light: #e6fffb; --sidebar-hover: #f4f5f7;
    --danger-bg: #fee2e2; --danger-text: #dc2626; --danger-hover: #fecaca;
    --success-bg: #dcfce7; --success-text: #16a34a; --success-hover: #bbf7d0;
    --badge-admin-bg: #ede9fe; --badge-admin-text: #6d28d9;
    --badge-gerente-bg: #ccfbf1; --badge-gerente-text: #0d9488;
    --badge-cajero-bg: #fff3e0; --badge-cajero-text: #e8631f;
    --shadow: rgba(0,0,0,0.06); --login-bg: #e0f2f1;
  }

  [data-theme="dark"] {
    --bg: #0f1420; --text: #e5e7eb; --card-bg: #1a2233; --border: #374151; --border-light: #2b3444;
    --muted: #9ca3af; --muted-light: #6b7280; --topbar-bg: #0a0e17; --topbar-text: #f3f4f6;
    --accent: #14b8a6; --accent-hover: #2dd4bf; --accent-bg-light: #113a36; --sidebar-hover: #232c3d;
    --danger-bg: #3f1d1d; --danger-text: #f87171; --danger-hover: #522525;
    --success-bg: #143621; --success-text: #4ade80; --success-hover: #1c4a2c;
    --badge-admin-bg: #2e1f4d; --badge-admin-text: #c4b5fd;
    --badge-gerente-bg: #113a36; --badge-gerente-text: #5eead4;
    --badge-cajero-bg: #3a2a1a; --badge-cajero-text: #fdba74;
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

  .content { padding: 1.5rem; max-width: 1050px; }
  .content h2 { font-size: 1.2rem; margin-bottom: 0.3rem; }
  .content .desc { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }
  .seccion { display: none; }
  .seccion.activa { display: block; }

  .card { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 4px var(--shadow); }
  .card h3 { font-size: 0.95rem; margin-bottom: 1rem; }

  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 0.55rem 0.6rem; border-bottom: 1px solid var(--border-light); text-align: left; font-size: 0.85rem; }
  th { color: var(--muted); font-weight: 600; font-size: 0.78rem; text-transform: uppercase; }
  td input, td select { padding: 0.35rem; border: 1px solid var(--border); border-radius: 5px; font-size: 0.82rem; background: var(--card-bg); color: var(--text); }

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
  .badge-admin { background: var(--badge-admin-bg); color: var(--badge-admin-text); }
  .badge-gerente { background: var(--badge-gerente-bg); color: var(--badge-gerente-text); }
  .badge-cajero { background: var(--badge-cajero-bg); color: var(--badge-cajero-text); }

  .filtro-sucursal { margin-bottom: 1rem; }
  .filtro-sucursal select { padding: 0.55rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.85rem; min-width: 200px; background: var(--card-bg); color: var(--text); }

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

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.25rem; }
.stat-card { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 4px var(--shadow); text-align: center; }
.stat-card .valor { font-size: 1.6rem; font-weight: 700; color: var(--accent); }
.stat-card .etiqueta { font-size: 0.78rem; color: var(--muted); margin-top: 4px; }
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
<div class="panel-view" id="panelView">
  <div class="topbar">
    <div class="brand"><span class="logo-sm">N</span> Nexora <span class="sucursal" data-i18n="topbarTitulo"> — Panel de Administrador</span></div>
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

      <div class="seccion" id="sec-sucursales">
        <h2 data-i18n="sucursalesHeading">Registro de sucursales</h2>
        <p class="desc" data-i18n="sucursalesDesc">Da de alta nuevas sucursales o edita las existentes.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombre">Nombre</label><input type="text" id="sucNombre" placeholder="Sucursal Sur"></div>
            <div class="campo"><label data-i18n="labelDireccion">Dirección</label><input type="text" id="sucDireccion" placeholder="Calle 123"></div>
            <div class="campo"><label data-i18n="labelContacto">Contacto</label><input type="text" id="sucContacto" placeholder="312 000 0000"></div>
            <div class="campo"><label data-i18n="labelEstado">Estado</label>
              <select id="sucEstado">
                <option value="activa" data-i18n="estadoActiva">Activa</option>
                <option value="inactiva" data-i18n="estadoInactiva">Inactiva</option>
              </select>
            </div>
            <button class="btn btn-primario" onclick="agregarSucursal()" data-i18n="btnAgregar">Agregar</button>
          </div>
        </div>
        <div class="card">
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thDireccion">Dirección</th><th data-i18n="thContacto">Contacto</th><th data-i18n="thEstado">Estado</th><th data-i18n="thGerente">Gerente</th><th></th><th></th></tr><tbody id="tablaSucursales"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-roles">
        <h2 data-i18n="rolesHeading">Asignar roles</h2>
        <p class="desc" data-i18n="rolesDesc">Cambia el rol y la sucursal asignada a cada usuario.</p>
        <div class="card">
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thRol">Rol</th><th data-i18n="thSucursal">Sucursal</th><th></th></tr><tbody id="tablaRoles"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-usuarios">
        <h2 data-i18n="usuariosHeading">Usuarios</h2>
        <p class="desc" data-i18n="usuariosDesc">Crea, edita o elimina cuentas de acceso al sistema.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombre">Nombre</label><input type="text" id="usrNombre" placeholder="Nombre"></div>
            <div class="campo"><label data-i18n="labelApellido">Apellido</label><input type="text" id="usrApellido" placeholder="Apellido"></div>
            <div class="campo"><label data-i18n="labelCorreo">Correo</label><input type="email" id="usrCorreo" placeholder="correo@nexora.com"></div>
            <div class="campo"><label data-i18n="labelContrasena">Contraseña</label><input type="password" id="usrClave" placeholder="••••••"></div>
            <div class="campo"><label data-i18n="labelRol">Rol</label>
              <select id="usrRol">
                <option value="cajero" data-i18n="rolCajero">Cajero</option>
                <option value="gerente" data-i18n="rolGerente">Gerente</option>
                <option value="admin" data-i18n="rolAdmin">Administrador</option>
              </select>
            </div>
            <div class="campo"><label data-i18n="thSucursal">Sucursal</label><select id="usrSucursal"></select></div>
            <button class="btn btn-primario" onclick="crearUsuario()" data-i18n="btnCrearUsuario">Crear usuario</button>
          </div>
        </div>
        <div class="card">
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thRol">Rol</th><th data-i18n="thSucursal">Sucursal</th><th></th></tr><tbody id="tablaUsuarios"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-proveedores">
        <h2 data-i18n="proveedoresHeading">Proveedores</h2>
        <p class="desc" data-i18n="proveedoresDesc">Administra el directorio de proveedores para las compras de mercancía.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombreEmpresa">Nombre / empresa</label><input type="text" id="provNombre" placeholder="Distribuidora XYZ"></div>
            <div class="campo"><label data-i18n="labelContacto">Contacto</label><input type="text" id="provContacto" data-i18n-placeholder="placeholderContacto" placeholder="Nombre del contacto"></div>
            <div class="campo"><label data-i18n="labelTelefono">Teléfono</label><input type="text" id="provTelefono" placeholder="312 000 0000"></div>
            <button class="btn btn-primario" onclick="agregarProveedor()" data-i18n="btnAgregar">Agregar</button>
          </div>
        </div>
        <div class="card">
          <table><tr><th data-i18n="thProveedor">Proveedor</th><th data-i18n="thContacto">Contacto</th><th data-i18n="thTelefono">Teléfono</th><th></th></tr><tbody id="tablaProveedores"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-inventario">
        <h2 data-i18n="inventarioHeading">Inventario por sucursal</h2>
        <p class="desc" data-i18n="inventarioDesc">Consulta las existencias de cualquier sucursal.</p>
        <div class="filtro-sucursal">
          <select id="filtroSucursal" onchange="renderInventarioGlobal()"></select>
        </div>
        <div class="card">
          <table><tr><th data-i18n="thProducto">Producto</th><th data-i18n="thStock">Stock</th></tr><tbody id="tablaInventarioGlobal"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-ventas">
        <h2 data-i18n="ventasHeading">Ventas por sucursal</h2>
        <p class="desc" data-i18n="ventasDesc">Consulta las ventas registradas en cualquier sucursal.</p>
        <div class="filtro-sucursal">
          <select id="filtroSucursalVentas" onchange="renderVentas()"></select>
        </div>
        <p class="desc">Total en esta sucursal: <strong id="totalVentasSucursal">$0.00</strong></p>
        <div class="card">
          <table><tr><th data-i18n="thFecha">Fecha</th><th data-i18n="thCajero">Cajero</th><th data-i18n="thMetodoPago">Método de pago</th><th data-i18n="thTotal">Total</th></tr><tbody id="tablaVentas"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-sistema">
        <h2 data-i18n="sistemaHeading">Información general del sistema</h2>
        <p class="desc" data-i18n="sistemaDesc">Resumen general de sucursales, usuarios, ventas e inventario.</p>
        <div class="stats-grid">
          <div class="stat-card"><div class="valor" id="statSucursales">0</div><div class="etiqueta" data-i18n="statSucursales">Sucursales</div></div>
          <div class="stat-card"><div class="valor" id="statUsuarios">0</div><div class="etiqueta" data-i18n="statUsuarios">Usuarios totales</div></div>
          <div class="stat-card"><div class="valor" id="statGerentes">0</div><div class="etiqueta" data-i18n="statGerentes">Gerentes</div></div>
          <div class="stat-card"><div class="valor" id="statCajeros">0</div><div class="etiqueta" data-i18n="statCajeros">Cajeros</div></div>
          <div class="stat-card"><div class="valor" id="statProductos">0</div><div class="etiqueta" data-i18n="statProductos">Productos</div></div>
          <div class="stat-card"><div class="valor" id="statVentasHoy">$0.00</div><div class="etiqueta" data-i18n="statVentasHoy">Ventas de hoy</div></div>
          <div class="stat-card"><div class="valor" id="statVentasMes">$0.00</div><div class="etiqueta" data-i18n="statVentasMes">Ventas del mes</div></div>
          <div class="stat-card"><div class="valor" id="statStockBajo">0</div><div class="etiqueta" data-i18n="statStockBajo">Stock bajo</div></div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  let resumen = {};

  async function cargarResumen() {
    const res = await fetch('/administrador/resumen');
    resumen = await res.json();
  }

  let sucursales = [];
  
  async function cargarSucursales() {
    const res = await fetch('/administrador/sucursales');
    sucursales = await res.json();
    renderSucursales();
  }

  let usuarios = [];

  async function cargarUsuarios(){
    const res = await fetch('/administrador/usuarios');
    usuarios = await res.json();
    renderRoles();
    renderUsuarios();
  }

  let proveedores = [
    { id: 1, nombre: 'Distribuidora del Pacífico', contacto: 'Jorge Ramírez', telefono: '312 123 4567' },
    { id: 2, nombre: 'Refrescos Colima', contacto: 'Sandra López', telefono: '312 765 4321' },
  ];

  let inventario = [];

  async function cargarInventario(){
    const res = await fetch('/administrador/inventario');
    inventario = await res.json();
  }

  let ventas = [];

  async function cargarVentas() {
    const res = await fetch('/administrador/ventas');
    ventas = await res.json();
  }

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      tagline: 'Panel de administrador', placeholderUsuario: 'Usuario', placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar', demoNote: 'Demo visual — cualquier dato entra',
      topbarTitulo: '— Panel de Administrador', cerrarSesion: 'Cerrar sesión',
      navSucursales: 'Sucursales', navRoles: 'Asignar roles', navUsuarios: 'Usuarios',
      navProveedores: 'Proveedores', navInventario: 'Inventario',
      sucursalesHeading: 'Registro de sucursales', sucursalesDesc: 'Da de alta nuevas sucursales o edita las existentes.', thGerente: 'Gerente',
      labelNombre: 'Nombre', labelDireccion: 'Dirección', labelApellido: 'Apellido', labelCorreo: 'Correo', btnAgregar: 'Agregar',
      thNombre: 'Nombre', thDireccion: 'Dirección', btnGuardar: 'Guardar', btnEliminar: 'Eliminar', btnCancelar: 'Cancelar',
      alertNombreSucursal: 'Ingresa el nombre de la sucursal.', confirmEliminarSucursal: '¿Eliminar esta sucursal? Los usuarios asignados quedarán sin sucursal.',
      rolesHeading: 'Asignar roles', rolesDesc: 'Cambia el rol y la sucursal asignada a cada usuario.',
      thUsuario: 'Usuario', thRol: 'Rol', thSucursal: 'Sucursal', sinAsignar: '— Sin asignar —',
      rolCajero: 'Cajero', rolGerente: 'Gerente', rolAdmin: 'Administrador',
      usuariosHeading: 'Usuarios', usuariosDesc: 'Crea, edita o elimina cuentas de acceso al sistema.',
      labelNombreCompleto: 'Nombre completo', placeholderNombre: 'Nombre', labelUsuario: 'Usuario',
      labelContrasena: 'Contraseña', labelRol: 'Rol', btnCrearUsuario: 'Crear usuario',
      alertCompletaUsuario: 'Completa nombre, usuario y contraseña.', confirmEliminarUsuario: '¿Eliminar este usuario?',
      proveedoresHeading: 'Proveedores', proveedoresDesc: 'Administra el directorio de proveedores para las compras de mercancía.',
      labelNombreEmpresa: 'Nombre / empresa', labelContacto: 'Contacto', placeholderContacto: 'Nombre del contacto', labelTelefono: 'Teléfono',
      thProveedor: 'Proveedor', thContacto: 'Contacto', thTelefono: 'Teléfono',
      alertNombreProveedor: 'Ingresa el nombre del proveedor.', confirmEliminarProveedor: '¿Eliminar este proveedor?',
      inventarioHeading: 'Inventario por sucursal', inventarioDesc: 'Consulta las existencias de cualquier sucursal.',
      thProducto: 'Producto', thStock: 'Stock', sinDatos: 'Sin datos',
      navVentas: 'Ventas',
      ventasHeading: 'Ventas por sucursal', ventasDesc: 'Consulta las ventas registradas en cualquier sucursal.',
      thFecha: 'Fecha', thCajero: 'Cajero', thMetodoPago: 'Método de pago', thTotal: 'Total',
      navSistema: 'Información general',
      sistemaHeading: 'Información general del sistema', sistemaDesc: 'Resumen general de sucursales, usuarios, ventas e inventario.',
      statSucursales: 'Sucursales', statUsuarios: 'Usuarios totales', statGerentes: 'Gerentes', statCajeros: 'Cajeros',
      statProductos: 'Productos', statVentasHoy: 'Ventas de hoy', statVentasMes: 'Ventas del mes', statStockBajo: 'Stock bajo',
      labelEstado: 'Estado', thEstado: 'Estado', estadoActiva: 'Activa', estadoInactiva: 'Inactiva',
    },
    en: {
      tagline: 'Admin panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarTitulo: '— Admin Panel', cerrarSesion: 'Log out',
      navSucursales: 'Branches', navRoles: 'Assign roles', navUsuarios: 'Users',
      navProveedores: 'Suppliers', navInventario: 'Inventory',
      sucursalesHeading: 'Branch registry', sucursalesDesc: 'Add new branches or edit existing ones.',
      labelNombre: 'Name', labelDireccion: 'Address', labelApellido: 'Last name', labelCorreo: 'Email', btnAgregar: 'Add',
      thNombre: 'Name', thDireccion: 'Address', btnGuardar: 'Save', btnEliminar: 'Delete', btnGuardar: 'Cancel',
      alertNombreSucursal: 'Enter the branch name.', confirmEliminarSucursal: 'Delete this branch? Assigned users will be left without a branch.',
      rolesHeading: 'Assign roles', rolesDesc: 'Change the role and assigned branch for each user.', thGerente: 'Manager',
      thUsuario: 'User', thRol: 'Role', thSucursal: 'Branch', sinAsignar: '— Unassigned —',
      rolCajero: 'Cashier', rolGerente: 'Manager', rolAdmin: 'Administrator',
      usuariosHeading: 'Users', usuariosDesc: 'Create, edit, or delete system access accounts.',
      labelNombreCompleto: 'Full name', placeholderNombre: 'Name', labelUsuario: 'Username',
      labelContrasena: 'Password', labelRol: 'Role', btnCrearUsuario: 'Create user',
      alertCompletaUsuario: 'Fill in name, username, and password.', confirmEliminarUsuario: 'Delete this user?',
      proveedoresHeading: 'Suppliers', proveedoresDesc: 'Manage the supplier directory for merchandise purchases.',
      labelNombreEmpresa: 'Name / company', labelContacto: 'Contact', placeholderContacto: 'Contact name', labelTelefono: 'Phone',
      thProveedor: 'Supplier', thContacto: 'Contact', thTelefono: 'Phone',
      alertNombreProveedor: 'Enter the supplier name.', confirmEliminarProveedor: 'Delete this supplier?',
      inventarioHeading: 'Inventory by branch', inventarioDesc: 'Check the stock at any branch.',
      thProducto: 'Product', thStock: 'Stock', sinDatos: 'No data',
      navVentas: 'Sales',
      ventasHeading: 'Sales by branch', ventasDesc: 'Check the sales recorded at any branch.',
      thFecha: 'Date', thCajero: 'Cashier', thMetodoPago: 'Payment method', thTotal: 'Total',
      navSistema: 'Overview',
      sistemaHeading: 'System overview', sistemaDesc: 'General summary of branches, users, sales and inventory.',
      statSucursales: 'Branches', statUsuarios: 'Total users', statGerentes: 'Managers', statCajeros: 'Cashiers',
      statProductos: 'Products', statVentasHoy: "Today's sales", statVentasMes: 'Monthly sales', statStockBajo: 'Low stock',
      labelEstado: 'Status', thEstado: 'Status', estadoActiva: 'Active', estadoInactiva: 'Inactive',
    }
  };
  let idioma = '{{ app()->getLocale() }}';
  function t(clave) { return textos[idioma][clave] || clave; }

  const secciones = [
    { id: 'sistema', key: 'navSistema' },
    { id: 'sucursales', key: 'navSucursales' },
    { id: 'roles', key: 'navRoles' },
    { id: 'usuarios', key: 'navUsuarios' },
    //{ id: 'proveedores', key: 'navProveedores' }, //pendiente en la bd y conexion
    { id: 'inventario', key: 'navInventario' },
    { id: 'ventas', key: 'navVentas'},
  ];

  let seccionActual = 'sistema';

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
    aplicarIdioma();
  })();

  function cerrarSesion() {
    document.getElementById('logoutForm').submit();
  }

  renderSidebar();
  (async () => {
    await cargarUsuarios();
    await cargarSucursales();
    await cargarInventario();
    await cargarVentas();
    await cargarResumen();
    mostrarSeccion('sistema');
  })();

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
    if(id === 'sistema') renderResumen();
    if (id === 'sucursales') renderSucursales();
    if (id === 'roles') renderRoles();
    if (id === 'usuarios') renderUsuarios();
    //if (id === 'proveedores') renderProveedores();
    if (id === 'inventario') renderInventarioGlobal();
    if (id === 'ventas') renderVentas();
  }

  function opcionesSucursal(seleccionId) {
    return sucursales.map(s => `<option value="${s.id_sucursal}" ${s.id_sucursal === seleccionId ? 'selected' : ''}>${s.nombre}</option>`).join('');
  }

  function textoRol(rol) { return rol === 'admin' ? t('rolAdmin') : rol === 'gerente' ? t('rolGerente') : t('rolCajero'); }

  /* ---------- Consulta general del sistema (dashboard) ---------- */
  function renderResumen() {
    document.getElementById('statSucursales').textContent = resumen.sucursales ?? 0;
    document.getElementById('statUsuarios').textContent = resumen.usuarios?.total ?? 0;
    document.getElementById('statGerentes').textContent = resumen.usuarios?.gerentes ?? 0;
    document.getElementById('statCajeros').textContent = resumen.usuarios?.cajeros ?? 0;
    document.getElementById('statProductos').textContent = resumen.productos ?? 0;
    document.getElementById('statVentasHoy').textContent = `$${Number(resumen.ventas_hoy ?? 0).toFixed(2)}`;
    document.getElementById('statVentasMes').textContent = `$${Number(resumen.ventas_mes ?? 0).toFixed(2)}`;
    document.getElementById('statStockBajo').textContent = resumen.stock_bajo ?? 0;
  }

  /* ---------- SUCURSALES ---------- */
  function renderSucursales() {
    document.getElementById('tablaSucursales').innerHTML = sucursales.map(s => `
      <tr>
        <td><input value="${s.nombre}" id="sucN-${s.id_sucursal}"></td>
        <td><input value="${s.direccion}" id="sucD-${s.id_sucursal}"></td>
        <td><input value="${s.contacto ?? ''}" id="sucC-${s.id_sucursal}"></td>
        <td>
          <select id="sucE-${s.id_sucursal}">
            <option value="activa" ${s.estado === 'activa' ? 'selected' : ''}>${t('estadoActiva')}</option>
            <option value="inactiva" ${s.estado === 'inactiva' ? 'selected' : ''}>${t('estadoInactiva')}</option>
          </select>
        </td>
        <td><select id="sucGer-${s.id_sucursal}">${opcionesGerente(s.id_gerente)}</select></td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarSucursal(${s.id_sucursal})">${t('btnGuardar')}</button></td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarSucursal(${s.id_sucursal})">${t('btnEliminar')}</button></td>
      </tr>`).join('');
  }

  async function agregarSucursal() {
    const nombre = document.getElementById('sucNombre').value.trim();
    const direccion = document.getElementById('sucDireccion').value.trim();
    const contacto = document.getElementById('sucContacto').value.trim();
    const estado = document.getElementById('sucEstado').value;
    if (!nombre) { mostrarToast(t('alertNombreSucursal'), 'error'); return; }

    const res = await fetch('/administrador/sucursales', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ nombre, direccion, contacto, estado }),
    });

    if (!res.ok) { mostrarToast('No se pudo guardar la sucursal.', 'error'); return; }

    document.getElementById('sucNombre').value = '';
    document.getElementById('sucDireccion').value = '';
    document.getElementById('sucContacto').value = '';
    await cargarSucursales();
    mostrarToast('Sucursal agregada', 'exito');
  }

  async function guardarSucursal(id) {
    const nombre = document.getElementById('sucN-' + id).value;
    const direccion = document.getElementById('sucD-' + id).value;
    const contacto = document.getElementById('sucC-' + id).value;
    const estado = document.getElementById('sucE-' + id).value;
    const gerVal = document.getElementById('sucGer-' + id).value;

    const res = await fetch('/administrador/sucursales/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ nombre, direccion, contacto, estado, id_gerente: gerVal ? parseInt(gerVal) : null }),
    });

    if (!res.ok) { mostrarToast('No se pudo actualizar la sucursal.', 'error'); return; }
    await cargarSucursales();
    await cargarUsuarios();
    mostrarToast('Sucursal actualizada', 'exito');
  }

  async function eliminarSucursal(id) {
    const ok = await confirmar(t('confirmEliminarSucursal'));
    if (!ok) return;

    const res = await fetch('/administrador/sucursales/' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });

    if (!res.ok) { mostrarToast('No se pudo eliminar (puede tener usuarios asignados).','error'); return; }
    mostrarToast('Sucursal eliminada', 'exito');
    await cargarSucursales();
  }

  /* ---------- ROLES ---------- */
  function renderRoles() {
    document.getElementById('tablaRoles').innerHTML = usuarios.map(u => `
      <tr>
        <td>${u.nombre}</td>
        <td>
          <select id="rol-${u.id}">
            <option value="cajero" ${u.rol === 'cajero' ? 'selected' : ''}>${t('rolCajero')}</option>
            <option value="gerente" ${u.rol === 'gerente' ? 'selected' : ''}>${t('rolGerente')}</option>
            <option value="admin" ${u.rol === 'admin' ? 'selected' : ''}>${t('rolAdmin')}</option>
          </select>
        </td>
        <td>
          <select id="sucRol-${u.id}"><option value="">${t('sinAsignar')}</option>${opcionesSucursal(u.sucursalId)}</select>
        </td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarRol(${u.id})">${t('btnGuardar')}</button></td>
      </tr>`).join('');
  }

  async function guardarRol(id) {
    const rol = document.getElementById('rol-' + id).value;
    const sucVal = document.getElementById('sucRol-' + id).value;

    const res = await fetch('/administrador/usuarios/' + id + '/rol', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ rol, id_sucursal: sucVal ? parseInt(sucVal) : null }),
    });

    if (!res.ok) { mostrarToast('No se pudo actualizar el rol.', 'error'); return; }
    mostrarToast('Rol actualizado', 'exito');
    await cargarUsuarios();
  }

  /* ---------- USUARIOS ---------- */
  function renderUsuarios() {
    document.getElementById('usrSucursal').innerHTML = `<option value="">${t('sinAsignar')}</option>${opcionesSucursal()}`;
    document.getElementById('tablaUsuarios').innerHTML = usuarios.map(u => {
      const sucursal = sucursales.find(s => s.id_sucursal === u.sucursalId);
      const badgeClase = u.rol === 'admin' ? 'badge-admin' : u.rol === 'gerente' ? 'badge-gerente' : 'badge-cajero';
      return `
      <tr>
        <td>${u.nombre}</td>
        <td><span class="badge ${badgeClase}">${textoRol(u.rol)}</span></td>
        <td>${sucursal ? sucursal.nombre : '—'}</td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarUsuario(${u.id})">${t('btnEliminar')}</button></td>
      </tr>`;
    }).join('');
  }

  async function crearUsuario() {
    const nombre = document.getElementById('usrNombre').value.trim();
    const apellido = document.getElementById('usrApellido').value.trim();
    const correo = document.getElementById('usrCorreo').value.trim();
    const contrasena = document.getElementById('usrClave').value;
    const rol = document.getElementById('usrRol').value;
    const sucVal = document.getElementById('usrSucursal').value;

    if (!nombre || !apellido || !correo || !contrasena) { 
      mostrarToast(t('alertCompletaUsuario'), 'error'); 
      return; 
    }

    const res = await fetch('/administrador/usuarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ nombre, apellido, correo, contrasena, rol, id_sucursal: sucVal ? parseInt(sucVal) : null }),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? 'No se pudo crear el usuario.', 'error');
      return;
    }

    document.getElementById('usrNombre').value = '';
    document.getElementById('usrApellido').value = '';
    document.getElementById('usrCorreo').value = '';
    document.getElementById('usrClave').value = '';
    mostrarToast('Usuario creado', 'exito');
    await cargarUsuarios();
  }

  async function eliminarUsuario(id) {
    const ok = await confirmar(t('confirmEliminarUsuario'));
    if (!ok) return;

    const res = await fetch('/administrador/usuarios/' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? 'No se pudo eliminar el usuario.', 'error');
      return;
    }
    mostrarToast('Usuario eliminado', 'exito');
    await cargarUsuarios();
  }

  /* ---------- PROVEEDORES ---------- */
  function renderProveedores() {
    document.getElementById('tablaProveedores').innerHTML = proveedores.map(p => `
      <tr>
        <td><input value="${p.nombre}" id="provN-${p.id}"></td>
        <td><input value="${p.contacto}" id="provC-${p.id}"></td>
        <td><input value="${p.telefono}" id="provT-${p.id}"></td>
        <td>
          <button class="btn btn-primario btn-sm" onclick="guardarProveedor(${p.id})">${t('btnGuardar')}</button>
          <button class="btn btn-peligro btn-sm" onclick="eliminarProveedor(${p.id})">${t('btnEliminar')}</button>
        </td>
      </tr>`).join('');
  }

  function agregarProveedor() {
    const nombre = document.getElementById('provNombre').value.trim();
    const contacto = document.getElementById('provContacto').value.trim();
    const telefono = document.getElementById('provTelefono').value.trim();
    if (!nombre) { alert(t('alertNombreProveedor')); return; }
    const id = Math.max(0, ...proveedores.map(p => p.id)) + 1;
    proveedores.push({ id, nombre, contacto, telefono });
    document.getElementById('provNombre').value = '';
    document.getElementById('provContacto').value = '';
    document.getElementById('provTelefono').value = '';
    renderProveedores();
  }

  function guardarProveedor(id) {
    const p = proveedores.find(x => x.id === id);
    p.nombre = document.getElementById('provN-' + id).value;
    p.contacto = document.getElementById('provC-' + id).value;
    p.telefono = document.getElementById('provT-' + id).value;
    renderProveedores();
  }

  function eliminarProveedor(id) {
    if (!confirm(t('confirmEliminarProveedor'))) return;
    proveedores = proveedores.filter(p => p.id !== id);
    renderProveedores();
  }

  /* ---------- INVENTARIO GLOBAL ---------- */
  function renderInventarioGlobal() {
    const filtro = document.getElementById('filtroSucursal');
    if (!filtro.options.length) {
      filtro.innerHTML = opcionesSucursal();
    }
    const sucId = parseInt(filtro.value) || sucursales[0]?.id_sucursal;
    const items = inventario.filter(i => i.id_sucursal === sucId);

    document.getElementById('tablaInventarioGlobal').innerHTML = items.length
      ? items.map(i => {
          const bajo = i.existencias <= i.stock_minimo;
          return `<tr>
            <td>${i.producto}</td>
            <td style="${bajo ? 'color:var(--danger-text); font-weight:600;' : ''}">${i.existencias}${bajo ? ' ⚠️' : ''}</td>
          </tr>`;
        }).join('')
      : `<tr><td colspan="2" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
  }

  /* ---------- Sekeccion de gerente ---------- */
  function opcionesGerente(seleccionId) {
    const gerentes = usuarios.filter(u => u.rol === 'gerente');
    const opciones = gerentes.map(g => `<option value="${g.id}" ${g.id === seleccionId ? 'selected' : ''}>${g.nombre}</option>`).join('');
    return `<option value="">${t('sinAsignar')}</option>${opciones}`;
  }

  /* ---------- Consultar ventas ---------- */
  function renderVentas() {
    const filtro = document.getElementById('filtroSucursalVentas');
    if (!filtro.options.length) {
      filtro.innerHTML = opcionesSucursal();
    }
    const sucId = parseInt(filtro.value) || sucursales[0]?.id_sucursal;
    const items = ventas.filter(v => v.id_sucursal === sucId);
    const totalSucursal = items.reduce((acc, v) => acc + Number(v.total), 0);

    document.getElementById('totalVentasSucursal').textContent = `$${totalSucursal.toFixed(2)}`;

    document.getElementById('tablaVentas').innerHTML = items.length
      ? items.map(v => `
        <tr>
          <td>${new Date(v.fecha).toLocaleString()}</td>
          <td>${v.cajero}</td>
          <td>${v.metodo_pago}</td>
          <td>$${Number(v.total).toFixed(2)}</td>
        </tr>`).join('')
      : `<tr><td colspan="4" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
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