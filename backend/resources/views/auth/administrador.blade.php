<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora - Panel de Administrador</title>
@include('partials.favicon')
<script src="{{ asset('js/theme.js') }}"></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f4f5f7; --text: #1f2933; --card-bg: #ffffff; --border: #d1d5db; --border-light: #eeeeee;
    --muted: #6b7280; --muted-light: #9ca3af; --topbar-bg: #1f2933; --topbar-text: #ffffff;
    --accent: #ea580c; --accent-hover: #c2410c; --accent-bg-light: #fff1e6; --sidebar-hover: #f4f5f7;
    --danger-bg: #fee2e2; --danger-text: #dc2626; --danger-hover: #fecaca;
    --success-bg: #ffedd5; --success-text: #c2410c; --success-hover: #fed7aa;
    --warning-bg: #fef3c7; --warning-text: #b45309;
    --badge-admin-bg: #ede9fe; --badge-admin-text: #6d28d9;
    --badge-gerente-bg: #dbeafe; --badge-gerente-text: #1d4ed8;
    --badge-cajero-bg: #fff3e0; --badge-cajero-text: #e8631f;
    --shadow: rgba(0,0,0,0.06); --login-bg: #fff3e8;
  }

  [data-theme="dark"] {
    --bg: #0f1420; --text: #e5e7eb; --card-bg: #1a2233; --border: #374151; --border-light: #2b3444;
    --muted: #9ca3af; --muted-light: #6b7280; --topbar-bg: #0a0e17; --topbar-text: #f3f4f6;
    --accent: #fb923c; --accent-hover: #fdba74; --accent-bg-light: #3a2210; --sidebar-hover: #232c3d;
    --danger-bg: #3f1d1d; --danger-text: #f87171; --danger-hover: #522525;
    --success-bg: #3b2410; --success-text: #fb923c; --success-hover: #4d2f14;
    --warning-bg: #3a2e0f; --warning-text: #fbbf24;
    --badge-admin-bg: #2e1f4d; --badge-admin-text: #c4b5fd;
    --badge-gerente-bg: #1e2a4a; --badge-gerente-text: #93c5fd;
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
  .btn .ico { width: 1.15em; height: 1.15em; vertical-align: -0.2em; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .badge .ico { width: 1em; height: 1em; vertical-align: -0.15em; fill: none; stroke: currentColor; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }
  .ico-texto .ico { margin-right: 6px; }
  .sidebar .ico { width: 1.15em; height: 1.15em; vertical-align: -0.2em; margin-right: 8px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
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
  .badge-invitacion-aceptada { background: var(--success-bg); color: var(--success-text); }
  .badge-invitacion-pendiente { background: var(--warning-bg); color: var(--warning-text); }
  .badge-invitacion-vencida { background: var(--danger-bg); color: var(--danger-text); }
  .badge-invitacion-verificada { background: var(--badge-gerente-bg); color: var(--badge-gerente-text); }

  /* Formulario en cuadrícula (Crear usuarios): mismos tamaños y colores que .fila-form */
  .form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 0.9rem 1rem; }
  .form-grid .campo { display: flex; flex-direction: column; gap: 4px; }
  .form-grid label { font-size: 0.75rem; color: var(--muted); }
  .form-grid input, .form-grid select { width: 100%; height: 2.35rem; padding: 0 0.55rem; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 0.85rem; background: var(--card-bg); color: var(--text); }
  [data-theme="dark"] .form-grid input, [data-theme="dark"] .form-grid select { color-scheme: dark; } /* calendario y listas en claro sobre fondo oscuro */
  .form-grid select:disabled { opacity: 0.55; cursor: not-allowed; }
  .form-grid .ayuda { font-size: 0.72rem; color: var(--muted-light); line-height: 1.35; }
  .campo-clave { position: relative; }
  .campo-clave input { padding-right: 2.4rem; }
  .campo-clave button { position: absolute; right: 0.4rem; top: 50%; transform: translateY(-50%); display: flex; padding: 0.25rem; background: none; border: none; color: var(--muted); cursor: pointer; }
  .campo-clave button:hover { color: var(--accent); }
  .campo-clave .ico { width: 1.1em; height: 1.1em; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .form-separador { border: none; border-top: 1px solid var(--border-light); margin: 1.25rem 0; }
  .form-acciones { display: flex; justify-content: flex-end; margin-top: 1.25rem; }
  .form-acciones .ico { margin-right: 6px; }

  .filtro-sucursal { margin-bottom: 1rem; }
  .filtro-sucursal select { padding: 0.55rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.85rem; min-width: 200px; background: var(--card-bg); color: var(--text); }

  /* TOASTS */
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

/*  MODAL DE CONFIRMACIÓN */
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

  /* ---------- REPORTES (reglas nuevas; solo usan las variables del tema) ---------- */
  .fila-form .campo-ancho { flex: 1 1 280px; }
  .fila-form .campo-ancho input { width: 100%; }
  .fila-form textarea { width: 100%; min-height: 4.5rem; padding: 0.55rem; border: 1px solid var(--border); border-radius: 6px; font: inherit; font-size: 0.85rem; background: var(--card-bg); color: var(--text); resize: vertical; }
  .rep-btn .ico { width: 1.1em; height: 1.1em; vertical-align: -0.2em; margin-right: 6px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .rep-desc { font-size: 0.78rem; color: var(--muted); margin-top: 2px; white-space: pre-line; overflow-wrap: anywhere; }
  .rep-pendiente, .rep-en_revision, .rep-resuelto { white-space: nowrap; }
  .rep-pendiente { background: var(--danger-bg); color: var(--danger-text); }
  .rep-en_revision { background: var(--border-light); color: var(--muted); }
  .rep-resuelto { background: var(--success-bg); color: var(--success-text); }
  .filtros-rep { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
  .filtros-rep select, td select.rep-estado { padding: 0.45rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.82rem; background: var(--card-bg); color: var(--text); }
  td select.rep-estado { display: block; margin-top: 4px; }
  td textarea.rep-respuesta { width: 100%; min-width: 180px; min-height: 2.6rem; padding: 0.4rem; border: 1px solid var(--border); border-radius: 6px; font: inherit; font-size: 0.8rem; background: var(--card-bg); color: var(--text); resize: vertical; }
  #tablaReportes .btn { white-space: nowrap; }
  .contador-menu { margin-left: 6px; padding: 0 7px; border-radius: 20px; background: var(--danger-text); color: #fff; font-size: 0.7rem; font-weight: 700; }
  /* ---------- PAGINACIÓN DE CORTES (regla nueva; variables del tema) ---------- */
  .paginacion { display: flex; align-items: center; justify-content: flex-end; gap: 0.6rem; margin-top: 0.9rem; font-size: 0.82rem; color: var(--muted); }
  .paginacion .btn:disabled { opacity: 0.45; cursor: default; }
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
    <div class="brand">Nexora <span class="sucursal"><span data-i18n="topbarTitulo"> — Panel de Administrador</span> · <span id="topbarNegocio">{{ auth()->user()->negocio->nombre ?? '' }}</span></span></div>
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
            <div class="campo"><label data-i18n="labelContacto">Contacto</label><input type="tel" id="sucContacto" placeholder="3120000000" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"></div>
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

      <div class="seccion" id="sec-crear-usuarios">
        <h2 data-i18n="crearUsuariosHeading">Crear usuarios</h2>
        <p class="desc" data-i18n="crearUsuariosDesc">Registra una cuenta nueva y asígnale su rol y sucursal.</p>
        <!-- Mismos datos que register.blade.php (sin repetir contraseña) + rol y sucursal -->
        <div class="card">
          <h3 data-i18n="grupoDatosPersonales">Datos personales</h3>
          <div class="form-grid">
            <div class="campo"><label for="usrNombre" data-i18n="labelNombre">Nombre</label><input type="text" id="usrNombre" maxlength="150" autocomplete="off" data-i18n-placeholder="labelNombre" placeholder="Nombre"></div>
            <div class="campo"><label for="usrApellido" data-i18n="labelApellido">Apellido</label><input type="text" id="usrApellido" maxlength="150" autocomplete="off" data-i18n-placeholder="labelApellido" placeholder="Apellido"></div>
            <div class="campo"><label for="usrNacimiento" data-i18n="labelFechaNacimiento">Fecha de nacimiento</label><input type="date" id="usrNacimiento" max="{{ now()->subYears(18)->toDateString() }}"></div>
            <div class="campo"><label for="usrCorreo" data-i18n="labelCorreo">Correo</label><input type="email" id="usrCorreo" maxlength="150" autocomplete="off" placeholder="correo@nexora.com"></div>
          </div>

          <hr class="form-separador">

          <h3 data-i18n="grupoAcceso">Acceso al sistema</h3>
          <div class="form-grid">
            <div class="campo"><label for="usrRol" data-i18n="labelRol">Rol</label>
              <!-- Cada negocio tiene un solo administrador (su dueño): aquí solo se crean empleados -->
              <select id="usrRol">
                <option value="cajero" data-i18n="rolCajero">Cajero</option>
                <option value="gerente" data-i18n="rolGerente">Gerente</option>
              </select>
            </div>
            <div class="campo"><label for="usrSucursal" data-i18n="thSucursal">Sucursal</label><select id="usrSucursal"></select></div>
          </div>

          <div class="form-acciones">
            <button class="btn btn-primario" onclick="crearUsuario()"><span id="icoCrearUsuario"></span><span data-i18n="btnCrearUsuario">Crear usuario</span></button>
          </div>
        </div>
      </div>

      <div class="seccion" id="sec-usuarios">
        <h2 data-i18n="usuariosHeading">Usuarios</h2>
        <p class="desc" data-i18n="usuariosDesc">Consulta o elimina las cuentas de acceso al sistema.</p>
        <div class="card">
          <table><tr><th data-i18n="thNombre">Nombre</th><th data-i18n="thRol">Rol</th><th data-i18n="thSucursal">Sucursal</th><th data-i18n="thInvitacion">Invitación</th><th></th></tr><tbody id="tablaUsuarios"></tbody></table>
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
          <table><tr><th data-i18n="thFoto">Foto</th><th data-i18n="thProducto">Producto</th><th data-i18n="thStock">Stock</th></tr><tbody id="tablaInventarioGlobal"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-productos-admin">
        <h2 data-i18n="adminProductosHeading">Catálogo de productos</h2>
        <p class="desc" data-i18n="adminProductosDesc">Sube o reemplaza la foto de cada producto del catálogo global.</p>
        <div class="card">
          <table>
            <tr>
              <th data-i18n="thFoto">Foto</th>
              <th data-i18n="thProducto">Producto</th>
              <th data-i18n="thPrecio">Precio</th>
              <th data-i18n="thEstado">Estado</th>
              <th></th>
            </tr>
            <tbody id="tablaProductosAdmin"></tbody>
          </table>
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
          <table><tr><th data-i18n="thFecha">Fecha</th><th data-i18n="thCajero">Cajero</th><th data-i18n="thMetodoPago">Método de pago</th><th data-i18n="thTotal">Total</th><th data-i18n="thEstado">Estado</th></tr><tbody id="tablaVentas"></tbody></table>
        </div>
      </div>

      <div class="seccion" id="sec-cortes">
        <h2 data-i18n="cortesHeading">Cortes de caja por sucursal</h2>
        <p class="desc" data-i18n="cortesDesc">Consulta los turnos de caja de cualquier sucursal: lo vendido contra el efectivo contado.</p>
        <div class="filtro-sucursal">
          <select id="filtroSucursalCortes" onchange="irPaginaCortes(1)"></select>
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
          <div class="paginacion" id="paginacionCortes"></div>
        </div>
      </div>

      <div class="seccion" id="sec-reportes">
        <h2 data-i18n="repHeading">Reportes de tu equipo</h2>
        <p class="desc" data-i18n="repDesc">Reportes que te envían tus gerentes y cajeros. Cambia el estado y, si quieres, contéstales con una nota.</p>
        <div class="filtros-rep">
          <select id="repFiltroEstado" onchange="renderReportes()"></select>
          <select id="repFiltroTipo" onchange="renderReportes()"></select>
          <select id="repFiltroSucursal" onchange="renderReportes()"></select>
          <span class="rep-desc" id="repContador"></span>
        </div>
        <div class="card">
          <table>
            <tr><th data-i18n="repThFecha">Fecha</th><th data-i18n="repThSucursal">Sucursal</th><th data-i18n="repThAutor">Enviado por</th><th data-i18n="repThTipo">Tipo</th><th data-i18n="repThAsunto">Asunto</th><th data-i18n="repThEstado">Estado</th><th data-i18n="repThRespuesta">Respuesta</th><th></th></tr>
            <tbody id="tablaReportes"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-sistema">
        <h2 data-i18n="sistemaHeading">Información general del sistema</h2>
        <p class="desc" data-i18n="sistemaDesc">Resumen general de sucursales, usuarios, ventas e inventario.</p>
        <div class="card">
          <h3 data-i18n="miNegocioHeading">Mi negocio</h3>
          <div class="fila-form" style="margin-bottom:0;">
            <div class="campo"><label for="negocioNombre" data-i18n="labelNombreNegocio">Nombre del negocio</label><input type="text" id="negocioNombre" maxlength="150" style="min-width:260px;"></div>
            <button class="btn btn-primario" onclick="guardarNegocio()"><span id="icoGuardarNegocio" class="ico-texto"></span><span data-i18n="btnGuardar">Guardar</span></button>
          </div>
        </div>
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
  /* Escapa texto que viene de la BD antes de meterlo en innerHTML (evita XSS: un nombre como
     <img onerror=...> se muestra como texto y no se ejecuta). function = se puede usar en todo el script. */
  function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
  }

  let resumen = {};
  let negocio = null;

  async function cargarResumen() {
    const res = await fetch('/administrador/resumen');
    resumen = await res.json();
  }

  async function cargarNegocio() {
    const res = await fetch('/administrador/negocio', { headers: { 'Accept': 'application/json' } });
    if (res.ok) negocio = await res.json();
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

  let cortes = [];
  let paginaCortes = { pagina: 1, paginas: 1, total: 0 };
  let reportes = [];
  let reportesPendientes = 0;

  /* Cortes de la sucursal elegida, de 50 en 50: el filtro y la página los resuelve el servidor */
  async function cargarCortes(pagina = paginaCortes.pagina) {
    const filtro = document.getElementById('filtroSucursalCortes');
    if (!filtro.options.length) filtro.innerHTML = opcionesSucursal();
    const sucursal = filtro.value || sucursales[0]?.id_sucursal || '';
    const res = await fetch(`/administrador/cortes?sucursal=${sucursal}&pagina=${pagina}`, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;
    const data = await res.json();
    cortes = data.cortes;
    paginaCortes = { pagina: data.pagina, paginas: data.paginas, total: data.total };
  }

  /*  IDIOMA  */
  const textos = {
    es: {
      tagline: 'Panel de administrador', placeholderUsuario: 'Usuario', placeholderClave: 'Contraseña',
      btnEntrar: 'Entrar', demoNote: 'Demo visual — cualquier dato entra',
      topbarTitulo: '— Panel de Administrador', cerrarSesion: 'Cerrar sesión',
      navSucursales: 'Sucursales', navRoles: 'Asignar roles', navUsuarios: 'Usuarios',
      navProveedores: 'Proveedores', navInventario: 'Inventario',
      sucursalesHeading: 'Registro de sucursales', sucursalesDesc: 'Da de alta nuevas sucursales o edita las existentes.', thGerente: 'Gerente',
      labelNombre: 'Nombre', labelDireccion: 'Dirección', labelApellido: 'Apellido', labelCorreo: 'Correo', btnAgregar: 'Agregar',
      thNombre: 'Nombre', thDireccion: 'Dirección', thInvitacion: 'Invitación', estadoInvitacionAccepted: 'Aceptada', estadoInvitacionSent: 'Pendiente', estadoInvitacionViewed: 'Pendiente', estadoInvitacionExpired: 'Vencida', estadoInvitacionVerified: 'Verificada', estadoInvitacionPending: 'Pendiente', estadoInvitacionRevoked: 'Reemplazada', btnReenviarInvitacion: 'Reenviar invitación', invitacionReenviada: 'Invitación reenviada (vale 48 horas).', btnGuardar: 'Guardar', btnEliminar: 'Eliminar', btnCancelar: 'Cancelar',
      alertNombreSucursal: 'Ingresa el nombre de la sucursal.', alertContactoInvalido: 'El contacto debe tener exactamente 10 números.', errorGuardarSucursal: 'No se pudo guardar la sucursal.', errorActualizarSucursal: 'No se pudo actualizar la sucursal.', sucursalAgregada: 'Sucursal agregada', sucursalActualizada: 'Sucursal actualizada', confirmEliminarSucursal: '¿Eliminar esta sucursal? Los usuarios asignados quedarán sin sucursal.',
      rolesHeading: 'Asignar roles', rolesDesc: 'Cambia el rol y la sucursal asignada a cada usuario.',
      thUsuario: 'Usuario', thRol: 'Rol', thSucursal: 'Sucursal', sinAsignar: '— Sin asignar —',
      rolCajero: 'Cajero', rolGerente: 'Gerente', rolAdmin: 'Administrador',
      usuariosHeading: 'Usuarios', usuariosDesc: 'Consulta o elimina las cuentas de acceso al sistema.',
      labelNombreCompleto: 'Nombre completo', placeholderNombre: 'Nombre', labelUsuario: 'Usuario',
      labelContrasena: 'Contraseña', labelRol: 'Rol', btnCrearUsuario: 'Crear usuario',
      alertCompletaUsuario: 'Completa todos los campos.', confirmEliminarUsuario: '¿Eliminar este usuario?',
      navCrearUsuarios: 'Crear usuarios', crearUsuariosHeading: 'Crear usuarios',
      crearUsuariosDesc: 'Registra una cuenta nueva y asígnale su rol y sucursal.',
      labelFechaNacimiento: 'Fecha de nacimiento', grupoDatosPersonales: 'Datos personales', grupoAcceso: 'Acceso al sistema',
      ayudaContrasena: 'Mínimo 8 caracteres, una mayúscula, un número y por lo menos un carácter especial.',
      alertContrasenaInvalida: 'La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial.',
      alertMayorEdad: 'El usuario debe ser mayor de edad (18 años o más).', alertCorreoInvalido: 'El correo no tiene un formato válido.',
      usuarioCreado: 'Usuario creado', invitacionEnviada: 'Usuario creado e invitación enviada.', errorCrearUsuario: 'No se pudo crear el usuario.',
      mostrarContrasena: 'Mostrar contraseña', ocultarContrasena: 'Ocultar contraseña',
      errorGenerico: 'Ocurrió un error. Intenta de nuevo.',
      miNegocioHeading: 'Mi negocio', labelNombreNegocio: 'Nombre del negocio',
      alertNombreNegocio: 'Escribe el nombre de tu negocio.', negocioActualizado: 'Nombre del negocio actualizado',
      etiquetaTu: 'Tú', noEditarPropio: 'Eres el dueño del negocio: no puedes cambiar tu propio rol ni eliminar tu cuenta.',
      noEditarPropioCorto: 'Dueño del negocio',
      confirmCambioRol: '{nombre} pasará de {de} a {a}.',
      avisoAGerente: 'Dejará el punto de venta y administrará su sucursal (productos, inventario, compras y cajas).',
      avisoACajero: 'Dejará de administrar su sucursal y solo podrá vender.',
      avisoCambioSucursal: '{nombre} quedará asignado a: {sucursal}.',
      preguntaContinuar: '¿Deseas continuar?', btnConfirmarCambio: 'Cambiar rol',
      sinCambios: 'No hay cambios que guardar.', rolActualizado: 'Rol actualizado', errorRol: 'No se pudo actualizar el rol.',
      sucursalEliminada: 'Sucursal eliminada', errorEliminarSucursal: 'No se pudo eliminar la sucursal.',
      usuarioEliminado: 'Usuario eliminado', errorEliminarUsuario: 'No se pudo eliminar el usuario.',
      proveedoresHeading: 'Proveedores', proveedoresDesc: 'Administra el directorio de proveedores para las compras de mercancía.',
      labelNombreEmpresa: 'Nombre / empresa', labelContacto: 'Contacto', placeholderContacto: 'Nombre del contacto', labelTelefono: 'Teléfono',
      thProveedor: 'Proveedor', thContacto: 'Contacto', thTelefono: 'Teléfono',
      alertNombreProveedor: 'Ingresa el nombre del proveedor.', confirmEliminarProveedor: '¿Eliminar este proveedor?',
      inventarioHeading: 'Inventario por sucursal', inventarioDesc: 'Consulta las existencias de cualquier sucursal.',
      thProducto: 'Producto', thStock: 'Stock', sinDatos: 'Sin datos',
      navProductosAdmin: 'Catálogo / fotos',
      adminProductosHeading: 'Catálogo de productos', adminProductosDesc: 'Sube o reemplaza la foto de cada producto del catálogo global.',
      thFoto: 'Foto', thPrecio: 'Precio', fotoActualizada: 'Foto actualizada', labelFotoProducto: 'Foto del producto',
      navVentas: 'Ventas',
      ventasHeading: 'Ventas por sucursal', ventasDesc: 'Consulta las ventas registradas en cualquier sucursal.',
      thFecha: 'Fecha', thCajero: 'Cajero', thMetodoPago: 'Método de pago', thTotal: 'Total',
      estadoCompletada: 'Completada', estadoCancelada: 'Cancelada', estadoDevParcial: 'Con devolución',
      navCortes: 'Cortes de caja', cortesHeading: 'Cortes de caja por sucursal',
      cortesDesc: 'Consulta los turnos de caja de cualquier sucursal: lo vendido contra el efectivo contado.',
      thCaja: 'Caja', thApertura: 'Apertura', thCierre: 'Cierre', thFondo: 'Fondo', thVentas: 'Ventas',
      thEsperado: 'Esperado', thContado: 'Contado', thDiferencia: 'Diferencia',
      enTurno: 'En turno', sobrante: 'Sobrante', faltante: 'Faltante', cajaCuadrada: 'Cuadrada',
      navSistema: 'Información general',
      sistemaHeading: 'Información general del sistema', sistemaDesc: 'Resumen general de sucursales, usuarios, ventas e inventario.',
      statSucursales: 'Sucursales', statUsuarios: 'Usuarios totales', statGerentes: 'Gerentes', statCajeros: 'Cajeros',
      statProductos: 'Productos', statVentasHoy: 'Ventas de hoy', statVentasMes: 'Ventas del mes', statStockBajo: 'Stock bajo',
      labelEstado: 'Estado', thEstado: 'Estado', estadoActiva: 'Activa', estadoInactiva: 'Inactiva',
      navReportes: 'Reportes', repHeading: 'Reportes de tu equipo',
      repDesc: 'Reportes que te envían tus gerentes y cajeros. Cambia el estado y, si quieres, contéstales con una nota.',
      repTodosEstados: 'Todos los estados', repTodosTipos: 'Todos los tipos', repTodasSucursales: 'Todas las sucursales',
      repContador: ':n pendientes', repPendientesTitulo: 'Reportes pendientes', repThFecha: 'Fecha', repThSucursal: 'Sucursal', repThEstado: 'Estado',
      repPhRespuesta: 'Respuesta para quien lo envió (opcional)', repRespondido: 'Respondido el', repGuardado: 'Reporte actualizado',
      repLabelTipo: 'Tipo', repLabelAsunto: 'Asunto', repLabelDescripcion: 'Descripción',
      repPhAsunto: 'Ej. La impresora no imprime tickets', repPhDescripcion: 'Cuéntale al administrador qué pasó, cuándo y en qué caja o producto…',
      repBtnEnviar: 'Enviar reporte', repThAutor: 'Enviado por', repThTipo: 'Tipo', repThAsunto: 'Asunto', repThRespuesta: 'Respuesta del administrador',
      repSinReportes: 'Aún no hay reportes.', repSinRespuesta: 'Sin respuesta todavía', repTu: 'Tú', repUsuarioEliminado: 'Usuario eliminado',
      repTipo_falla: 'Falla del sistema', repTipo_faltante: 'Faltante de producto', repTipo_caja: 'Problema con caja / dinero', repTipo_sugerencia: 'Sugerencia', repTipo_otro: 'Otro',
      repEstado_pendiente: 'Pendiente', repEstado_en_revision: 'En revisión', repEstado_resuelto: 'Resuelto',
      repRol_gerente: 'Gerente', repRol_cajero: 'Cajero',
      repEnviado: 'Reporte enviado al administrador', repAlertCompleta: 'Escribe el asunto y la descripción.',
      btnAnterior: 'Anterior', btnSiguiente: 'Siguiente', paginaDe: 'Página :p de :t · :n cortes',
    },
    en: {
      tagline: 'Admin panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarTitulo: '— Admin Panel', cerrarSesion: 'Log out',
      navSucursales: 'Branches', navRoles: 'Assign roles', navUsuarios: 'Users',
      navProveedores: 'Suppliers', navInventario: 'Inventory',
      sucursalesHeading: 'Branch registry', sucursalesDesc: 'Add new branches or edit existing ones.',
      labelNombre: 'Name', labelDireccion: 'Address', labelApellido: 'Last name', labelCorreo: 'Email', btnAgregar: 'Add',
      thNombre: 'Name', thDireccion: 'Address', thInvitacion: 'Invitation', estadoInvitacionAccepted: 'Accepted', estadoInvitacionSent: 'Pending', estadoInvitacionViewed: 'Pending', estadoInvitacionExpired: 'Expired', estadoInvitacionVerified: 'Verified', estadoInvitacionPending: 'Pending', estadoInvitacionRevoked: 'Replaced', btnReenviarInvitacion: 'Resend invitation', invitacionReenviada: 'Invitation resent (valid for 48 hours).', btnGuardar: 'Save', btnEliminar: 'Delete', btnCancelar: 'Cancel',
      alertNombreSucursal: 'Enter the branch name.', alertContactoInvalido: 'Contact must contain exactly 10 digits.', errorGuardarSucursal: 'The branch could not be saved.', errorActualizarSucursal: 'The branch could not be updated.', sucursalAgregada: 'Branch added', sucursalActualizada: 'Branch updated', confirmEliminarSucursal: 'Delete this branch? Assigned users will be left without a branch.',
      rolesHeading: 'Assign roles', rolesDesc: 'Change the role and assigned branch for each user.', thGerente: 'Manager',
      thUsuario: 'User', thRol: 'Role', thSucursal: 'Branch', sinAsignar: '— Unassigned —',
      rolCajero: 'Cashier', rolGerente: 'Manager', rolAdmin: 'Administrator',
      usuariosHeading: 'Users', usuariosDesc: 'View or delete system access accounts.',
      labelNombreCompleto: 'Full name', placeholderNombre: 'Name', labelUsuario: 'Username',
      labelContrasena: 'Password', labelRol: 'Role', btnCrearUsuario: 'Create user',
      alertCompletaUsuario: 'Fill in all the fields.', confirmEliminarUsuario: 'Delete this user?',
      navCrearUsuarios: 'Create users', crearUsuariosHeading: 'Create users',
      crearUsuariosDesc: 'Register a new account and assign its role and branch.',
      labelFechaNacimiento: 'Date of birth', grupoDatosPersonales: 'Personal details', grupoAcceso: 'System access',
      ayudaContrasena: 'At least 8 characters, one uppercase letter, one number, and at least one special character.',
      alertContrasenaInvalida: 'The password needs at least 8 characters, one uppercase letter, one number, and one special character.',
      alertMayorEdad: 'The user must be an adult (18 or older).', alertCorreoInvalido: 'The email address is not valid.',
      usuarioCreado: 'User created', invitacionEnviada: 'User created and invitation sent.', errorCrearUsuario: 'The user could not be created.',
      mostrarContrasena: 'Show password', ocultarContrasena: 'Hide password',
      errorGenerico: 'Something went wrong. Please try again.',
      miNegocioHeading: 'My business', labelNombreNegocio: 'Business name',
      alertNombreNegocio: 'Enter your business name.', negocioActualizado: 'Business name updated',
      etiquetaTu: 'You', noEditarPropio: 'You own this business: you cannot change your own role or delete your account.',
      noEditarPropioCorto: 'Business owner',
      confirmCambioRol: '{nombre} will change from {de} to {a}.',
      avisoAGerente: 'They will leave the point of sale and manage their branch (products, inventory, purchases and registers).',
      avisoACajero: 'They will stop managing their branch and will only be able to sell.',
      avisoCambioSucursal: '{nombre} will be assigned to: {sucursal}.',
      preguntaContinuar: 'Do you want to continue?', btnConfirmarCambio: 'Change role',
      sinCambios: 'There are no changes to save.', rolActualizado: 'Role updated', errorRol: 'The role could not be updated.',
      sucursalEliminada: 'Branch deleted', errorEliminarSucursal: 'The branch could not be deleted.',
      usuarioEliminado: 'User deleted', errorEliminarUsuario: 'The user could not be deleted.',
      proveedoresHeading: 'Suppliers', proveedoresDesc: 'Manage the supplier directory for merchandise purchases.',
      labelNombreEmpresa: 'Name / company', labelContacto: 'Contact', placeholderContacto: 'Contact name', labelTelefono: 'Phone',
      thProveedor: 'Supplier', thContacto: 'Contact', thTelefono: 'Phone',
      alertNombreProveedor: 'Enter the supplier name.', confirmEliminarProveedor: 'Delete this supplier?',
      inventarioHeading: 'Inventory by branch', inventarioDesc: 'Check the stock at any branch.',
      thProducto: 'Product', thStock: 'Stock', sinDatos: 'No data',
      navProductosAdmin: 'Catalog / photos',
      adminProductosHeading: 'Product catalog', adminProductosDesc: 'Upload or replace the photo for each product in the global catalog.',
      thFoto: 'Photo', thPrecio: 'Price', fotoActualizada: 'Photo updated', labelFotoProducto: 'Product photo',
      navVentas: 'Sales',
      ventasHeading: 'Sales by branch', ventasDesc: 'Check the sales recorded at any branch.',
      thFecha: 'Date', thCajero: 'Cashier', thMetodoPago: 'Payment method', thTotal: 'Total',
      estadoCompletada: 'Completed', estadoCancelada: 'Cancelled', estadoDevParcial: 'With return',
      navCortes: 'Cash counts', cortesHeading: 'Cash counts by branch',
      cortesDesc: 'Check the register shifts at any branch: sales vs. the cash counted.',
      thCaja: 'Register', thApertura: 'Opened', thCierre: 'Closed', thFondo: 'Fund', thVentas: 'Sales',
      thEsperado: 'Expected', thContado: 'Counted', thDiferencia: 'Difference',
      enTurno: 'In shift', sobrante: 'Overage', faltante: 'Shortage', cajaCuadrada: 'Balanced',
      navSistema: 'Overview',
      sistemaHeading: 'System overview', sistemaDesc: 'General summary of branches, users, sales and inventory.',
      statSucursales: 'Branches', statUsuarios: 'Total users', statGerentes: 'Managers', statCajeros: 'Cashiers',
      statProductos: 'Products', statVentasHoy: "Today's sales", statVentasMes: 'Monthly sales', statStockBajo: 'Low stock',
      labelEstado: 'Status', thEstado: 'Status', estadoActiva: 'Active', estadoInactiva: 'Inactive',
      navReportes: 'Reports', repHeading: 'Reports from your team',
      repDesc: 'Reports sent by your managers and cashiers. Change the status and, if you want, reply with a note.',
      repTodosEstados: 'All statuses', repTodosTipos: 'All types', repTodasSucursales: 'All branches',
      repContador: ':n pending', repPendientesTitulo: 'Pending reports', repThFecha: 'Date', repThSucursal: 'Branch', repThEstado: 'Status',
      repPhRespuesta: 'Reply to the sender (optional)', repRespondido: 'Replied on', repGuardado: 'Report updated',
      repLabelTipo: 'Type', repLabelAsunto: 'Subject', repLabelDescripcion: 'Description',
      repPhAsunto: 'E.g. The printer does not print receipts', repPhDescripcion: 'Tell the administrator what happened, when, and which register or product…',
      repBtnEnviar: 'Send report', repThAutor: 'Sent by', repThTipo: 'Type', repThAsunto: 'Subject', repThRespuesta: "Administrator's reply",
      repSinReportes: 'No reports yet.', repSinRespuesta: 'No reply yet', repTu: 'You', repUsuarioEliminado: 'Deleted user',
      repTipo_falla: 'System failure', repTipo_faltante: 'Missing product', repTipo_caja: 'Register / cash problem', repTipo_sugerencia: 'Suggestion', repTipo_otro: 'Other',
      repEstado_pendiente: 'Pending', repEstado_en_revision: 'In review', repEstado_resuelto: 'Resolved',
      repRol_gerente: 'Manager', repRol_cajero: 'Cashier',
      repEnviado: 'Report sent to the administrator', repAlertCompleta: 'Write the subject and the description.',
      btnAnterior: 'Previous', btnSiguiente: 'Next', paginaDe: 'Page :p of :t · :n cash counts',
    }
  };
  let idioma = '{{ app()->getLocale() }}';
  function t(clave) { return textos[idioma][clave] || clave; }

  /* -------- ICONOS --------- */
  const svg = d => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${d}</svg>`;
  const CLIPBOARD_LIST = '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>';
  const ICONO_OJO = svg('<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>');
  const ICONO_OJO_TACHADO = svg('<path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/>');
  // Mismas reglas que el registro (RegisterController): 8+ caracteres, mayúscula, número y carácter especial
  const REGLA_CONTRASENA = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
  const ICONO_CANDADO = svg('<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>');
  const ICONO_GUARDAR = svg('<path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/>');
  const ICONO_CORREO = svg('<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>');
  const ICONO_BASURA = svg('<path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6"/><path d="M14 11v6"/>');
  const ICONOS_MENU = {
    navSistema: svg('<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'),
    navSucursales: svg('<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/>'),
    navRoles: svg('<path d="M2 21a8 8 0 0 1 10.434-7.62"/><circle cx="10" cy="8" r="5"/><circle cx="18" cy="18" r="3"/><path d="m19.5 14.3-.4.9"/><path d="m16.9 20.8-.4.9"/><path d="m21.7 19.5-.9-.4"/><path d="m15.2 16.9-.9-.4"/><path d="m21.7 16.5-.9.4"/><path d="m15.2 19.1-.9.4"/><path d="m19.5 21.7-.4-.9"/><path d="m16.9 15.2-.4-.9"/>'),
    navCrearUsuarios: svg('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>'),
    navUsuarios: svg('<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'),
    navInventario: svg('<path d="M12 22v-9"/><path d="M15.17 2.21a1.67 1.67 0 0 1 1.63 0L21 4.57a1.93 1.93 0 0 1 0 3.36L8.82 14.79a1.655 1.655 0 0 1-1.64 0L3 12.43a1.93 1.93 0 0 1 0-3.36z"/><path d="M20 13v3.87a2.06 2.06 0 0 1-1.11 1.83l-6 3.08a1.93 1.93 0 0 1-1.78 0l-6-3.08A2.06 2.06 0 0 1 4 16.87V13"/><path d="M21 12.43a1.93 1.93 0 0 0 0-3.36L8.82 1.2a1.655 1.655 0 0 0-1.64 0L3 3.57a1.93 1.93 0 0 0 0 3.36l12.18 6.86a1.636 1.636 0 0 0 1.63 0z"/>'),
    navProductosAdmin: svg('<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>'),
    navVentas: svg('<circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>'),
    navCortes: svg(CLIPBOARD_LIST),
    navReportes: svg('<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M12 7v2"/><path d="M12 13h.01"/>'),
  };

  const secciones = [
    { id: 'sistema', key: 'navSistema' },
    { id: 'sucursales', key: 'navSucursales' },
    { id: 'roles', key: 'navRoles' },
    { id: 'crear-usuarios', key: 'navCrearUsuarios' },
    { id: 'usuarios', key: 'navUsuarios' },
    //{ id: 'proveedores', key: 'navProveedores' }, //pendiente en la bd y conexion
    { id: 'inventario', key: 'navInventario' },
    { id: 'productos-admin', key: 'navProductosAdmin' },
    { id: 'ventas', key: 'navVentas'},
    { id: 'cortes', key: 'navCortes' },
    { id: 'reportes', key: 'navReportes' },
  ];

  let seccionActual = 'sistema';

  /* TEMA  */
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
    try { localStorage.setItem('theme', nuevo); } catch (e) {}
  }

  (function inicializarTema() {
    let guardado = null;
    try { guardado = localStorage.getItem('theme'); } catch (e) {}
    if (guardado === 'dark' || guardado === 'light') aplicarTema(guardado);
    else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) aplicarTema('dark');
    else aplicarTema('light');
  })();

  /* IDIOMA*/

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

  let productosAdmin = [];

  async function cargarProductosAdmin() {
    try {
      const res = await fetch('/administrador/productos', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      });
      productosAdmin = await res.json();
    } catch (e) {}
  }

  function renderProductosAdmin() {
    document.getElementById('tablaProductosAdmin').innerHTML = productosAdmin.map(p => `
      <tr>
        <td>${p.foto
          ? `<img src="${esc(p.foto)}" alt="${esc(p.nombre)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">`
          : `<span style="color:var(--muted-light);font-size:0.75rem;">—</span>`}
        </td>
        <td>${esc(p.nombre)}</td>
        <td>$${p.precio.toFixed(2)}</td>
        <td><span class="badge ${p.activo ? 'badge-gerente' : 'badge-cajero'}">${p.activo ? t('estadoActiva') : t('estadoInactiva')}</span></td>
        <td>
          <label class="btn btn-sm" style="background:var(--accent-bg-light);color:var(--accent-hover);cursor:pointer;" title="${t('labelFotoProducto')}">
            📷 <input type="file" accept="image/*" style="display:none" onchange="subirFotoAdmin(${p.id}, this)">
          </label>
        </td>
      </tr>`).join('') || `<tr><td colspan="5" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
  }

  async function subirFotoAdmin(id, inputEl) {
    if (!inputEl.files[0]) return;
    const formData = new FormData();
    formData.append('foto', inputEl.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    try {
      const resp = await fetch('/administrador/productos/' + id + '/foto', { method: 'POST', body: formData });
      const data = await resp.json();
      if (!resp.ok) throw new Error(data?.message || t('errorGenerico') || 'Error');
      const p = productosAdmin.find(x => x.id === id);
      if (p) p.foto = data.foto;
      renderProductosAdmin();
      mostrarToast(t('fotoActualizada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  renderSidebar();
  (async () => {
    await cargarUsuarios();
    await cargarSucursales();
    await cargarInventario();
    await cargarProductosAdmin();
    await cargarVentas();
    await cargarCortes();
    await cargarResumen();
    await cargarNegocio();
    await cargarReportes();
    mostrarSeccion('sistema');
  })();

  function renderSidebar() {
    document.getElementById('sidebar').innerHTML = secciones.map(s => `<button id="btn-${s.id}" onclick="mostrarSeccion('${s.id}')">${(ICONOS_MENU[s.key] || '') + t(s.key)}</button>`).join('');
    secciones.forEach(s => document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === seccionActual));
    pintarContadorReportes();
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
    if (id === 'crear-usuarios') renderCrearUsuario();
    if (id === 'usuarios') renderUsuarios();
    //if (id === 'proveedores') renderProveedores();
    if (id === 'inventario') renderInventarioGlobal();
    if (id === 'productos-admin') { renderProductosAdmin(); cargarProductosAdmin().then(renderProductosAdmin); }
    if (id === 'ventas') renderVentas();
    if (id === 'cortes') { renderCortes(); cargarCortes().then(renderCortes); }
    if (id === 'reportes') { renderReportes(); cargarReportes().then(renderReportes); }
  }

  function opcionesSucursal(seleccionId) {
    return sucursales.map(s => `<option value="${s.id_sucursal}" ${s.id_sucursal === seleccionId ? 'selected' : ''}>${esc(s.nombre)}</option>`).join('');
  }

  function textoRol(rol) { return rol === 'admin' ? t('rolAdmin') : rol === 'gerente' ? t('rolGerente') : t('rolCajero'); }

  /* -Consulta general del sistema (dashboard)  */
  function renderResumen() {
    document.getElementById('statSucursales').textContent = resumen.sucursales ?? 0;
    document.getElementById('statUsuarios').textContent = resumen.usuarios?.total ?? 0;
    document.getElementById('statGerentes').textContent = resumen.usuarios?.gerentes ?? 0;
    document.getElementById('statCajeros').textContent = resumen.usuarios?.cajeros ?? 0;
    document.getElementById('statProductos').textContent = resumen.productos ?? 0;
    document.getElementById('statVentasHoy').textContent = `$${Number(resumen.ventas_hoy ?? 0).toFixed(2)}`;
    document.getElementById('statVentasMes').textContent = `$${Number(resumen.ventas_mes ?? 0).toFixed(2)}`;
    document.getElementById('statStockBajo').textContent = resumen.stock_bajo ?? 0;

    document.getElementById('icoGuardarNegocio').innerHTML = ICONO_GUARDAR;
    const input = document.getElementById('negocioNombre');
    if (negocio && document.activeElement !== input) input.value = negocio.nombre;
  }

  /* MI NEGOCIO (nombre editable) */
  async function guardarNegocio() {
    const nombre = document.getElementById('negocioNombre').value.trim();
    if (!nombre) { mostrarToast(t('alertNombreNegocio'), 'error'); return; }

    const res = await fetch('/administrador/negocio', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ nombre }),
    });
    const data = await res.json().catch(() => null);
    if (!res.ok) { mostrarToast(data?.message ?? t('errorGenerico'), 'error'); return; }

    negocio = data;
    document.getElementById('topbarNegocio').textContent = data.nombre;
    mostrarToast(t('negocioActualizado'), 'exito');
  }

  /*  SUCURSALES  */
  function renderSucursales() {
    document.getElementById('tablaSucursales').innerHTML = sucursales.map(s => `
      <tr>
        <td><input value="${esc(s.nombre)}" id="sucN-${s.id_sucursal}"></td>
        <td><input value="${esc(s.direccion)}" id="sucD-${s.id_sucursal}"></td>
        <td><input type="tel" value="${esc(s.contacto ?? '')}" id="sucC-${s.id_sucursal}" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"></td>
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
    if (contacto && !/^\d{10}$/.test(contacto)) { mostrarToast(t('alertContactoInvalido'), 'error'); return; }

    const res = await fetch('/administrador/sucursales', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ nombre, direccion, contacto, estado }),
    });

    if (!res.ok) {
      const error = await res.json().catch(() => ({}));
      mostrarToast(error?.errors?.contacto?.[0] || t('errorGuardarSucursal'), 'error');
      return;
    }

    document.getElementById('sucNombre').value = '';
    document.getElementById('sucDireccion').value = '';
    document.getElementById('sucContacto').value = '';
    await cargarSucursales();
    mostrarToast(t('sucursalAgregada'), 'exito');
  }

  async function guardarSucursal(id) {
    const nombre = document.getElementById('sucN-' + id).value;
    const direccion = document.getElementById('sucD-' + id).value;
    const contacto = document.getElementById('sucC-' + id).value;
    const estado = document.getElementById('sucE-' + id).value;
    const gerVal = document.getElementById('sucGer-' + id).value;
    if (contacto.trim() && !/^\d{10}$/.test(contacto.trim())) { mostrarToast(t('alertContactoInvalido'), 'error'); return; }

    const res = await fetch('/administrador/sucursales/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ nombre, direccion, contacto, estado, id_gerente: gerVal ? parseInt(gerVal) : null }),
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      mostrarToast(data?.errors?.contacto?.[0] || data?.errors?.id_gerente?.[0] || data?.message || t('errorActualizarSucursal'), 'error');
      return;
    }
    await cargarSucursales();
    await cargarUsuarios();
    mostrarToast(t('sucursalActualizada'), 'exito');
    // El servidor avisa si el gerente anterior quedó sin sucursal
    if (data?.aviso) mostrarToast(data.aviso, 'exito');
  }

  async function eliminarSucursal(id) {
    const ok = await confirmar(t('confirmEliminarSucursal'));
    if (!ok) return;

    const res = await fetch('/administrador/sucursales/' + id, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? t('errorEliminarSucursal'), 'error');
      return;
    }
    mostrarToast(t('sucursalEliminada'), 'exito');
    await cargarSucursales();
  }

  /* ROLES  */
  /* Etiqueta "Tú" con candado para la cuenta del propio administrador */
  function etiquetaTu() {
    return `<span class="badge badge-admin" title="${t('noEditarPropio')}">${ICONO_CANDADO} ${t('etiquetaTu')}</span>`;
  }

  function renderRoles() {
    document.getElementById('tablaRoles').innerHTML = usuarios.map(u => {
      // El dueño (administrador) no se edita aquí: ni su rol ni su sucursal
      if (u.esYo || u.rol === 'admin') {
        return `
        <tr>
          <td>${esc(u.nombre)} ${u.esYo ? etiquetaTu() : ''}</td>
          <td><span class="badge badge-admin">${t('rolAdmin')}</span></td>
          <td>—</td>
          <td><span style="color:var(--muted-light);font-size:0.78rem;">${t('noEditarPropioCorto')}</span></td>
        </tr>`;
      }
      return `
      <tr>
        <td>${esc(u.nombre)}</td>
        <td>
          <select id="rol-${u.id}">
            <option value="cajero" ${u.rol === 'cajero' ? 'selected' : ''}>${t('rolCajero')}</option>
            <option value="gerente" ${u.rol === 'gerente' ? 'selected' : ''}>${t('rolGerente')}</option>
          </select>
        </td>
        <td>
          <select id="sucRol-${u.id}"><option value="">${t('sinAsignar')}</option>${opcionesSucursal(u.sucursalId)}</select>
        </td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarRol(${u.id})">${t('btnGuardar')}</button></td>
      </tr>`;
    }).join('');
  }

  /* Explica en palabras qué implica el cambio, para confirmarlo antes de guardar */
  function mensajeCambioRol(u, rol, idSucursal) {
    const nombreSucursal = id => sucursales.find(s => s.id_sucursal === id)?.nombre ?? t('sinAsignar');
    const partes = [];
    if (rol !== u.rol) {
      partes.push(t('confirmCambioRol').replace('{nombre}', u.nombre).replace('{de}', textoRol(u.rol)).replace('{a}', textoRol(rol)));
      partes.push(rol === 'gerente' ? t('avisoAGerente') : t('avisoACajero'));
    }
    if (idSucursal !== (u.sucursalId ?? null)) {
      partes.push(t('avisoCambioSucursal').replace('{nombre}', u.nombre).replace('{sucursal}', nombreSucursal(idSucursal)));
    }
    return partes.join(' ');
  }

  async function guardarRol(id) {
    const u = usuarios.find(x => x.id === id);
    const rol = document.getElementById('rol-' + id).value;
    const sucVal = document.getElementById('sucRol-' + id).value;
    const idSucursal = sucVal ? parseInt(sucVal) : null;

    const mensaje = mensajeCambioRol(u, rol, idSucursal);
    if (!mensaje) { mostrarToast(t('sinCambios'), 'error'); return; }
    if (!(await confirmar(mensaje + ' ' + t('preguntaContinuar'), t('btnConfirmarCambio')))) {
      renderRoles(); // regresa los selectores a como estaban
      return;
    }

    const res = await fetch('/administrador/usuarios/' + id + '/rol', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ rol, id_sucursal: idSucursal }),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? t('errorRol'), 'error');
      renderRoles();
      return;
    }
    mostrarToast(t('rolActualizado'), 'exito');
    await cargarUsuarios();
    await cargarSucursales(); // el gerente de alguna sucursal pudo cambiar
  }

  /*  USUARIOS  */
  function renderUsuarios() {
    document.getElementById('tablaUsuarios').innerHTML = usuarios.map(u => {
      const sucursal = sucursales.find(s => s.id_sucursal === u.sucursalId);
      const badgeClase = u.rol === 'admin' ? 'badge-admin' : u.rol === 'gerente' ? 'badge-gerente' : 'badge-cajero';
      // El dueño no puede borrarse a sí mismo: en su fila no hay botón de eliminar
      const estadoInvitacion = u.invitacionEstado || 'pending';
      // Reenviar solo a empleados que aún no activan su cuenta (el servidor lo vuelve a validar)
      const puedeReenviar = !u.esYo && u.rol !== 'admin' && !['accepted', 'verified'].includes(estadoInvitacion);
      const reenviar = puedeReenviar
        ? `<button class="btn btn-primario btn-sm" onclick="reenviarInvitacion(${u.id})" title="${t('btnReenviarInvitacion')}" aria-label="${t('btnReenviarInvitacion')}">${ICONO_CORREO}</button> `
        : '';
      const accion = u.esYo || u.rol === 'admin'
        ? ''
        : `${reenviar}<button class="btn btn-peligro btn-sm" onclick="eliminarUsuario(${u.id})" title="${t('btnEliminar')}" aria-label="${t('btnEliminar')}">${ICONO_BASURA}</button>`;
      const estadoClase = estadoInvitacion === 'accepted' ? 'badge-invitacion-aceptada' : estadoInvitacion === 'expired' ? 'badge-invitacion-vencida' : estadoInvitacion === 'verified' ? 'badge-invitacion-verificada' : 'badge-invitacion-pendiente';
      const estadoTexto = t('estadoInvitacion' + estadoInvitacion.charAt(0).toUpperCase() + estadoInvitacion.slice(1));
      return `
      <tr>
        <td>${esc(u.nombre)} ${u.esYo ? etiquetaTu() : ''}</td>
        <td><span class="badge ${badgeClase}">${textoRol(u.rol)}</span></td>
        <td>${sucursal ? esc(sucursal.nombre) : '—'}</td>
        <td><span class="badge ${estadoClase}">${estadoTexto}</span></td>
        <td style="white-space:nowrap">${accion}</td>
      </tr>`;
    }).join('');
  }

  /*  CREAR USUARIOS  */
  /* Llena el selector de sucursales sin perder la elección */
  function renderCrearUsuario() {
    const select = document.getElementById('usrSucursal');
    const elegida = select.value;
    select.innerHTML = `<option value="">${t('sinAsignar')}</option>${opcionesSucursal()}`;
    select.value = elegida;

    document.getElementById('icoCrearUsuario').innerHTML = ICONOS_MENU.navCrearUsuarios;
  }

  function esMayorDeEdad(fechaIso) {
    const limite = new Date();
    limite.setFullYear(limite.getFullYear() - 18);
    return new Date(fechaIso + 'T00:00:00') <= limite;
  }

  async function crearUsuario() {
    const nombre = document.getElementById('usrNombre').value.trim();
    const apellido = document.getElementById('usrApellido').value.trim();
    const fecha_nacimiento = document.getElementById('usrNacimiento').value;
    const correo = document.getElementById('usrCorreo').value.trim().toLowerCase();
    const rol = document.getElementById('usrRol').value;
    const sucVal = document.getElementById('usrSucursal').value;

    // Validación rápida en el navegador; el servidor vuelve a validar todo
    if (!nombre || !apellido || !fecha_nacimiento || !correo) { mostrarToast(t('alertCompletaUsuario'), 'error'); return; }
    if (!document.getElementById('usrCorreo').checkValidity()) { mostrarToast(t('alertCorreoInvalido'), 'error'); return; }
    if (!esMayorDeEdad(fecha_nacimiento)) { mostrarToast(t('alertMayorEdad'), 'error'); return; }

    const res = await fetch('/administrador/usuarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      // Sin contraseña: la persona la crea desde el correo de invitación
      body: JSON.stringify({
        nombre, apellido, fecha_nacimiento, correo, rol,
        id_sucursal: sucVal ? parseInt(sucVal) : null,
      }),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? t('errorCrearUsuario'), 'error');
      return;
    }

    ['usrNombre', 'usrApellido', 'usrNacimiento', 'usrCorreo'].forEach(id => { document.getElementById(id).value = ''; });
    renderCrearUsuario();
    mostrarToast(t('invitacionEnviada'), 'exito');
    await cargarUsuarios();
  }

  async function reenviarInvitacion(id) {
    const res = await fetch('/administrador/usuarios/' + id + '/invitacion', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
    const data = await res.json().catch(() => null);
    if (!res.ok) { mostrarToast(data?.message ?? t('errorGenerico'), 'error'); return; }
    mostrarToast(t('invitacionReenviada'), 'exito');
    await cargarUsuarios();
  }

  async function eliminarUsuario(id) {
    const ok = await confirmar(t('confirmEliminarUsuario'));
    if (!ok) return;

    const res = await fetch('/administrador/usuarios/' + id, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });

    if (!res.ok) {
      const err = await res.json().catch(() => null);
      mostrarToast(err?.message ?? t('errorEliminarUsuario'), 'error');
      return;
    }
    mostrarToast(t('usuarioEliminado'), 'exito');
    await cargarUsuarios();
  }

  /* -PROVEEDORES  */
  function renderProveedores() {
    document.getElementById('tablaProveedores').innerHTML = proveedores.map(p => `
      <tr>
        <td><input value="${esc(p.nombre)}" id="provN-${p.id}"></td>
        <td><input value="${esc(p.contacto)}" id="provC-${p.id}"></td>
        <td><input value="${esc(p.telefono)}" id="provT-${p.id}"></td>
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

  /* INVENTARIO GLOBAL  */
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
            <td>${i.foto ? `<img src="${esc(i.foto)}" alt="${esc(i.producto)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">` : `<span style="color:var(--muted-light);">—</span>`}</td>
            <td>${esc(i.producto)}</td>
            <td style="${bajo ? 'color:var(--danger-text); font-weight:600;' : ''}">${i.existencias}${bajo ? ' ⚠️' : ''}</td>
          </tr>`;
        }).join('')
      : `<tr><td colspan="3" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
  }

  /* Sekeccion de gerente  */
  function opcionesGerente(seleccionId) {
    const gerentes = usuarios.filter(u => u.rol === 'gerente');
    const opciones = gerentes.map(g => `<option value="${g.id}" ${g.id === seleccionId ? 'selected' : ''}>${esc(g.nombre)}</option>`).join('');
    return `<option value="">${t('sinAsignar')}</option>${opciones}`;
  }

  /* -Consultar ventas  */
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
          <td>${esc(v.cajero)}</td>
          <td>${esc(v.metodo_pago)}</td>
          <td>$${Number(v.total).toFixed(2)}</td>
          <td>${v.estado === 'cancelada' ? t('estadoCancelada') : (v.devuelto > 0 ? t('estadoDevParcial') : t('estadoCompletada'))}</td>
        </tr>`).join('')
      : `<tr><td colspan="5" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
  }

  /*  REPORTES de gerentes y cajeros (bandeja del dueño)  */
  const TIPOS_REPORTE = ['falla', 'faltante', 'caja', 'sugerencia', 'otro'];
  const ESTADOS_REPORTE = ['pendiente', 'en_revision', 'resuelto'];
  const fechaReporte = iso => iso ? new Date(iso).toLocaleString(idioma === 'es' ? 'es-MX' : 'en-US') : '—';

  async function cargarReportes() {
    const res = await fetch('/administrador/reportes', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;
    const data = await res.json();
    reportes = data.reportes;
    reportesPendientes = data.pendientes;
    pintarContadorReportes();
  }

  /* Globo rojo con el número de pendientes junto a "Reportes" en el menú */
  function pintarContadorReportes() {
    const btn = document.getElementById('btn-reportes');
    if (!btn) return;
    btn.querySelector('.contador-menu')?.remove();
    if (reportesPendientes > 0) btn.insertAdjacentHTML('beforeend', `<span class="contador-menu" title="${t('repPendientesTitulo')}">${reportesPendientes}</span>`);
  }

  /* Rellena un <select> de filtro conservando lo que estaba elegido */
  function llenarFiltroReportes(id, opciones) {
    const sel = document.getElementById(id);
    const previo = sel.value;
    sel.innerHTML = opciones.map(([valor, texto]) => `<option value="${valor}">${esc(texto)}</option>`).join('');
    sel.value = [...sel.options].some(o => o.value === previo) ? previo : '';
  }

  function renderReportes() {
    llenarFiltroReportes('repFiltroEstado', [['', t('repTodosEstados')], ...ESTADOS_REPORTE.map(e => [e, t('repEstado_' + e)])]);
    llenarFiltroReportes('repFiltroTipo', [['', t('repTodosTipos')], ...TIPOS_REPORTE.map(tp => [tp, t('repTipo_' + tp)])]);
    llenarFiltroReportes('repFiltroSucursal', [['', t('repTodasSucursales')], ...sucursales.map(s => [String(s.id_sucursal), s.nombre])]);
    const fEstado = document.getElementById('repFiltroEstado').value;
    const fTipo = document.getElementById('repFiltroTipo').value;
    const fSucursal = document.getElementById('repFiltroSucursal').value;
    document.getElementById('repContador').textContent = t('repContador').replace(':n', reportesPendientes);

    const items = reportes.filter(r => (!fEstado || r.estado === fEstado) && (!fTipo || r.tipo === fTipo) && (!fSucursal || String(r.id_sucursal) === fSucursal));
    document.getElementById('tablaReportes').innerHTML = items.length
      ? items.map(r => `
        <tr>
          <td>${fechaReporte(r.fecha)}</td>
          <td>${esc(r.sucursal ?? '—')}</td>
          <td>${esc(r.autor ?? t('repUsuarioEliminado'))}${r.rol_autor ? `<div class="rep-desc">${t('repRol_' + r.rol_autor)}</div>` : ''}</td>
          <td>${t('repTipo_' + r.tipo)}</td>
          <td><strong>${esc(r.asunto)}</strong><div class="rep-desc">${esc(r.descripcion)}</div></td>
          <td>
            <span class="badge rep-${r.estado}">${t('repEstado_' + r.estado)}</span>
            <select class="rep-estado" id="repEstado-${r.id}">${ESTADOS_REPORTE.map(e => `<option value="${e}" ${e === r.estado ? 'selected' : ''}>${t('repEstado_' + e)}</option>`).join('')}</select>
          </td>
          <td>
            <textarea class="rep-respuesta" id="repRespuesta-${r.id}" maxlength="500" placeholder="${t('repPhRespuesta')}">${esc(r.respuesta ?? '')}</textarea>
            ${r.fecha_respuesta ? `<div class="rep-desc">${t('repRespondido')} ${fechaReporte(r.fecha_respuesta)}</div>` : ''}
          </td>
          <td><button class="btn btn-primario btn-sm" onclick="guardarReporte(${r.id})"><span class="ico-texto">${ICONO_GUARDAR}</span>${t('btnGuardar')}</button></td>
        </tr>`).join('')
      : `<tr><td colspan="8" style="color:var(--muted-light);">${t('repSinReportes')}</td></tr>`;
  }

  async function guardarReporte(id) {
    const estado = document.getElementById('repEstado-' + id).value;
    const respuesta = document.getElementById('repRespuesta-' + id).value.trim();
    const res = await fetch('/administrador/reportes/' + id, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ estado, respuesta }),
    });
    const data = await res.json().catch(() => null);
    if (!res.ok) { mostrarToast(data?.message ?? t('errorGenerico'), 'error'); return; }
    await cargarReportes();
    renderReportes();
    mostrarToast(t('repGuardado'), 'exito');
  }

  /* Controles "Anterior / Página X de Y / Siguiente" del historial de cortes (50 por página) */
  function renderPaginacionCortes() {
    const { pagina, paginas, total } = paginaCortes;
    document.getElementById('paginacionCortes').innerHTML = total <= 0 ? '' : `
      <button class="btn btn-primario btn-sm" onclick="irPaginaCortes(${pagina - 1})" ${pagina <= 1 ? 'disabled' : ''}>‹ ${t('btnAnterior')}</button>
      <span>${t('paginaDe').replace(':p', pagina).replace(':t', paginas).replace(':n', total)}</span>
      <button class="btn btn-primario btn-sm" onclick="irPaginaCortes(${pagina + 1})" ${pagina >= paginas ? 'disabled' : ''}>${t('btnSiguiente')} ›</button>`;
  }

  function irPaginaCortes(n) {
    cargarCortes(n).then(renderCortes);
  }

  /*  Consultar cortes de caja (solo lectura)  */
  function renderCortes() {
    const filtro = document.getElementById('filtroSucursalCortes');
    if (!filtro.options.length) {
      filtro.innerHTML = opcionesSucursal();
    }
    const items = cortes;   // ya vienen filtrados por sucursal y paginados desde el servidor
    const dinero = n => '$' + Number(n).toFixed(2);

    const diferencia = c => {
      if (c.diferencia === null) return t('enTurno');
      if (c.diferencia > 0) return `<span style="color:var(--success-text); font-weight:600;">${t('sobrante')} ${dinero(c.diferencia)}</span>`;
      if (c.diferencia < 0) return `<span style="color:var(--danger-text); font-weight:600;">${t('faltante')} ${dinero(-c.diferencia)}</span>`;
      return t('cajaCuadrada');
    };

    document.getElementById('tablaCortes').innerHTML = items.length
      ? items.map(c => `
        <tr>
          <td>${esc(c.cajero ?? '—')}</td><td>${c.caja ?? '—'}</td>
          <td>${new Date(c.apertura).toLocaleString()}</td><td>${c.cierre ? new Date(c.cierre).toLocaleString() : '—'}</td>
          <td>${dinero(c.monto_inicial)}</td><td>${c.num_ventas} · ${dinero(c.total)}</td>
          <td>${dinero(c.efectivo_esperado)}</td><td>${c.efectivo_contado === null ? '—' : dinero(c.efectivo_contado)}</td>
          <td>${diferencia(c)}</td>
        </tr>`).join('')
      : `<tr><td colspan="9" style="color:var(--muted-light);">${t('sinDatos')}</td></tr>`;
    renderPaginacionCortes();
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