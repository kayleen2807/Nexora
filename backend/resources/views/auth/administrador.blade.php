<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</style>
</head>
<body>

<div class="panel-view" id="panelView">
  <div class="topbar">
    <div class="brand"><span class="logo-sm">N</span> Nexora <span class="sucursal" data-i18n="topbarTitulo">— Panel de Administrador</span></div>
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

      <div class="seccion" id="sec-sucursales">
        <h2 data-i18n="sucursalesHeading">Registro de sucursales</h2>
        <p class="desc" data-i18n="sucursalesDesc">Da de alta nuevas sucursales o edita las existentes.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombre">Nombre</label><input type="text" id="sucNombre" placeholder="Sucursal Sur"></div>
            <div class="campo"><label data-i18n="labelDireccion">Dirección</label><input type="text" id="sucDireccion" placeholder="Calle 123"></div>
            <button class="btn btn-primario" onclick="agregarSucursal()" data-i18n="btnAgregar">Agregar</button>
          </div>
        </div>
        <div class="card">
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thDireccion">Dirección</th><th></th><th></th></tr><tbody id="tablaSucursales"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-roles">
        <h2 data-i18n="rolesHeading">Asignar roles</h2>
        <p class="desc" data-i18n="rolesDesc">Cambia el rol y la sucursal asignada a cada usuario.</p>
        <div class="card">
          <table><tr><th data-i18n="thUsuario">Usuario</th><th data-i18n="thRol">Rol</th><th data-i18n="thSucursal">Sucursal</th><th></th></tr><tbody id="tablaRoles"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-usuarios">
        <h2 data-i18n="usuariosHeading">Usuarios</h2>
        <p class="desc" data-i18n="usuariosDesc">Crea, edita o elimina cuentas de acceso al sistema.</p>
        <div class="card">
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelNombreCompleto">Nombre completo</label><input type="text" id="usrNombre" data-i18n-placeholder="placeholderNombre" placeholder="Nombre"></div>
            <div class="campo"><label data-i18n="labelUsuario">Usuario</label><input type="text" id="usrUsuario" placeholder="usuario123"></div>
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
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thUsuario">Usuario</th><th data-i18n="thRol">Rol</th><th data-i18n="thSucursal">Sucursal</th><th></th></tr><tbody id="tablaUsuarios"></tbody></table>
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

    </div>
  </div>
</div>

<script>
  let sucursales = [
    { id: 1, nombre: 'Sucursal Centro', direccion: 'Av. Principal 123' },
    { id: 2, nombre: 'Sucursal Norte', direccion: 'Blvd. Norte 456' },
  ];

  let usuarios = [
    { id: 1, nombre: 'Administrador General', usuario: 'admin', rol: 'admin', sucursalId: null },
    { id: 2, nombre: 'Ana Gómez', usuario: 'ana.gerente', rol: 'gerente', sucursalId: 1 },
    { id: 3, nombre: 'Luis Pérez', usuario: 'luis.cajero', rol: 'cajero', sucursalId: 1 },
    { id: 4, nombre: 'María Ruiz', usuario: 'maria.cajero', rol: 'cajero', sucursalId: 2 },
  ];

  let proveedores = [
    { id: 1, nombre: 'Distribuidora del Pacífico', contacto: 'Jorge Ramírez', telefono: '312 123 4567' },
    { id: 2, nombre: 'Refrescos Colima', contacto: 'Sandra López', telefono: '312 765 4321' },
  ];

  const catalogoProductos = ['Refresco 600ml', 'Botana 150g', 'Agua 1L', 'Café americano', 'Sandwich jamón', 'Chicles'];
  let inventarioGlobal = {
    1: { 'Refresco 600ml': 12, 'Botana 150g': 8, 'Agua 1L': 20, 'Café americano': 15, 'Sandwich jamón': 6, 'Chicles': 30 },
    2: { 'Refresco 600ml': 20, 'Botana 150g': 15, 'Agua 1L': 40, 'Café americano': 10, 'Sandwich jamón': 4, 'Chicles': 18 },
  };

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      tagline: 'Panel de administrador', placeholderUsuario: 'Usuario', placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar', demoNote: 'Demo visual — cualquier dato entra',
      topbarTitulo: '— Panel de Administrador', cerrarSesion: 'Cerrar sesión',
      navSucursales: 'Sucursales', navRoles: 'Asignar roles', navUsuarios: 'Usuarios',
      navProveedores: 'Proveedores', navInventario: 'Inventario',
      sucursalesHeading: 'Registro de sucursales', sucursalesDesc: 'Da de alta nuevas sucursales o edita las existentes.',
      labelNombre: 'Nombre', labelDireccion: 'Dirección', btnAgregar: 'Agregar',
      thNombre: 'Nombre', thDireccion: 'Dirección', btnGuardar: 'Guardar', btnEliminar: 'Eliminar',
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
    },
    en: {
      tagline: 'Admin panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarTitulo: '— Admin Panel', cerrarSesion: 'Log out',
      navSucursales: 'Branches', navRoles: 'Assign roles', navUsuarios: 'Users',
      navProveedores: 'Suppliers', navInventario: 'Inventory',
      sucursalesHeading: 'Branch registry', sucursalesDesc: 'Add new branches or edit existing ones.',
      labelNombre: 'Name', labelDireccion: 'Address', btnAgregar: 'Add',
      thNombre: 'Name', thDireccion: 'Address', btnGuardar: 'Save', btnEliminar: 'Delete',
      alertNombreSucursal: 'Enter the branch name.', confirmEliminarSucursal: 'Delete this branch? Assigned users will be left without a branch.',
      rolesHeading: 'Assign roles', rolesDesc: 'Change the role and assigned branch for each user.',
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
    }
  };
  let idioma = 'es';
  function t(clave) { return textos[idioma][clave] || clave; }

  const secciones = [
    { id: 'sucursales', key: 'navSucursales' },
    { id: 'roles', key: 'navRoles' },
    { id: 'usuarios', key: 'navUsuarios' },
    { id: 'proveedores', key: 'navProveedores' },
    { id: 'inventario', key: 'navInventario' },
  ];
  let seccionActual = 'sucursales';

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
  mostrarSeccion('sucursales');

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
    if (id === 'sucursales') renderSucursales();
    if (id === 'roles') renderRoles();
    if (id === 'usuarios') renderUsuarios();
    if (id === 'proveedores') renderProveedores();
    if (id === 'inventario') renderInventarioGlobal();
  }

  function opcionesSucursal(seleccionId) {
    return sucursales.map(s => `<option value="${s.id}" ${s.id === seleccionId ? 'selected' : ''}>${s.nombre}</option>`).join('');
  }

  function textoRol(rol) { return rol === 'admin' ? t('rolAdmin') : rol === 'gerente' ? t('rolGerente') : t('rolCajero'); }

  /* ---------- SUCURSALES ---------- */
  function renderSucursales() {
    document.getElementById('tablaSucursales').innerHTML = sucursales.map(s => `
      <tr>
        <td><input value="${s.nombre}" id="sucN-${s.id}"></td>
        <td><input value="${s.direccion}" id="sucD-${s.id}"></td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarSucursal(${s.id})">${t('btnGuardar')}</button></td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarSucursal(${s.id})">${t('btnEliminar')}</button></td>
      </tr>`).join('');
  }

  function agregarSucursal() {
    const nombre = document.getElementById('sucNombre').value.trim();
    const direccion = document.getElementById('sucDireccion').value.trim();
    if (!nombre) { alert(t('alertNombreSucursal')); return; }
    const id = Math.max(0, ...sucursales.map(s => s.id)) + 1;
    sucursales.push({ id, nombre, direccion });
    inventarioGlobal[id] = Object.fromEntries(catalogoProductos.map(p => [p, 0]));
    document.getElementById('sucNombre').value = '';
    document.getElementById('sucDireccion').value = '';
    renderSucursales();
  }

  function guardarSucursal(id) {
    const s = sucursales.find(x => x.id === id);
    s.nombre = document.getElementById('sucN-' + id).value;
    s.direccion = document.getElementById('sucD-' + id).value;
    renderSucursales();
  }

  function eliminarSucursal(id) {
    if (!confirm(t('confirmEliminarSucursal'))) return;
    sucursales = sucursales.filter(s => s.id !== id);
    usuarios.forEach(u => { if (u.sucursalId === id) u.sucursalId = null; });
    delete inventarioGlobal[id];
    renderSucursales();
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

  function guardarRol(id) {
    const u = usuarios.find(x => x.id === id);
    u.rol = document.getElementById('rol-' + id).value;
    const sucVal = document.getElementById('sucRol-' + id).value;
    u.sucursalId = sucVal ? parseInt(sucVal) : null;
    renderRoles();
  }

  /* ---------- USUARIOS ---------- */
  function renderUsuarios() {
    document.getElementById('usrSucursal').innerHTML = `<option value="">${t('sinAsignar')}</option>${opcionesSucursal()}`;
    document.getElementById('tablaUsuarios').innerHTML = usuarios.map(u => {
      const sucursal = sucursales.find(s => s.id === u.sucursalId);
      const badgeClase = u.rol === 'admin' ? 'badge-admin' : u.rol === 'gerente' ? 'badge-gerente' : 'badge-cajero';
      return `
      <tr>
        <td>${u.nombre}</td>
        <td>${u.usuario}</td>
        <td><span class="badge ${badgeClase}">${textoRol(u.rol)}</span></td>
        <td>${sucursal ? sucursal.nombre : '—'}</td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarUsuario(${u.id})">${t('btnEliminar')}</button></td>
      </tr>`;
    }).join('');
  }

  function crearUsuario() {
    const nombre = document.getElementById('usrNombre').value.trim();
    const usuario = document.getElementById('usrUsuario').value.trim();
    const clave = document.getElementById('usrClave').value;
    const rol = document.getElementById('usrRol').value;
    const sucVal = document.getElementById('usrSucursal').value;

    if (!nombre || !usuario || !clave) { alert(t('alertCompletaUsuario')); return; }

    const id = Math.max(0, ...usuarios.map(u => u.id)) + 1;
    usuarios.push({ id, nombre, usuario, rol, sucursalId: sucVal ? parseInt(sucVal) : null });

    document.getElementById('usrNombre').value = '';
    document.getElementById('usrUsuario').value = '';
    document.getElementById('usrClave').value = '';
    renderUsuarios();
  }

  function eliminarUsuario(id) {
    if (!confirm(t('confirmEliminarUsuario'))) return;
    usuarios = usuarios.filter(u => u.id !== id);
    renderUsuarios();
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
    const sucId = parseInt(filtro.value) || sucursales[0]?.id;
    const inv = inventarioGlobal[sucId] || {};
    document.getElementById('tablaInventarioGlobal').innerHTML = Object.entries(inv).map(([nombre, cantidad]) => `
      <tr><td>${nombre}</td><td>${cantidad}</td></tr>`).join('') || `<tr><td colspan="2" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
  }
</script>

</body>
</html>