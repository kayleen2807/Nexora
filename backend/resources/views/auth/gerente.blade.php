<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora - Panel de Gerente</title>
@include('partials.favicon')
<script src="{{ asset('js/theme.js') }}"></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f4f5f7; --text: #1f2933; --card-bg: #ffffff; --border: #d1d5db; --border-light: #eeeeee;
    --muted: #6b7280; --muted-light: #9ca3af; --topbar-bg: #1f2933; --topbar-text: #ffffff;
    --accent: #ea580c; --accent-hover: #c2410c; --accent-bg-light: #fff1e6; --sidebar-hover: #f4f5f7;
    --disabled-bg: #d1d5db; --danger-bg: #fee2e2; --danger-text: #dc2626; --danger-hover: #fecaca;
    --success-bg: #ffedd5; --success-text: #c2410c; --success-hover: #fed7aa;
    --badge-inactivo-bg: #f3f4f6; --badge-inactivo-text: #9ca3af;
    --badge-efectivo-bg: #fff3e0; --badge-efectivo-text: #e8631f;
    --badge-tarjeta-bg: #e0f2fe; --badge-tarjeta-text: #0369a1;
    --shadow: rgba(0,0,0,0.06); --login-bg: #fff3e8;
  }

  :root { --verde-bg: #dcfce7; --verde-text: #15803d; --verde-hover: #bbf7d0; }
  [data-theme="dark"] { --verde-bg: #143621; --verde-text: #4ade80; --verde-hover: #1c4a2c; }

  [data-theme="dark"] {
    --bg: #0f1420; --text: #e5e7eb; --card-bg: #1a2233; --border: #374151; --border-light: #2b3444;
    --muted: #9ca3af; --muted-light: #6b7280; --topbar-bg: #0a0e17; --topbar-text: #f3f4f6;
    --accent: #fb923c; --accent-hover: #fdba74; --accent-bg-light: #3a2210; --sidebar-hover: #232c3d;
    --disabled-bg: #374151; --danger-bg: #3f1d1d; --danger-text: #f87171; --danger-hover: #522525;
    --success-bg: #3b2410; --success-text: #fb923c; --success-hover: #4d2f14;
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
  .sidebar .ico { margin-right: 8px; }
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
  .fila-form input[type="file"] { min-width: 260px; max-width: 100%; padding: 0.28rem; color: var(--muted); cursor: pointer; }
  .fila-form input[type="file"]::file-selector-button { margin-right: 0.65rem; padding: 0.42rem 0.75rem; border: 0; border-radius: 5px; background: var(--accent); color: #fff; font: inherit; font-weight: 600; cursor: pointer; transition: background 0.2s ease; }
  .fila-form input[type="file"]::file-selector-button:hover { background: var(--accent-hover); }
  .selector-foto { display: inline-flex; align-items: center; gap: 0.45rem; width: fit-content; padding: 0.48rem 0.75rem; border-radius: 6px; background: var(--accent-bg-light); color: var(--accent-hover); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease, transform 0.2s ease; }
  .selector-foto:hover { background: var(--accent); color: #fff; transform: translateY(-1px); }
  .selector-foto input[type="file"] { display: none; }
  .icono-foto { width: 1.15rem; height: 1.15rem; flex: 0 0 auto; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

  .btn { padding: 0.55rem 1rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
  .btn-primario { background: var(--accent); color: #fff; }
  .btn-primario:hover { background: var(--accent-hover); }
  .btn-peligro { background: var(--danger-bg); color: var(--danger-text); }
  .btn-peligro:hover { background: var(--danger-hover); }
  .btn-exito { background: var(--verde-bg); color: var(--verde-text); }
  .btn-exito:hover { background: var(--verde-hover); }
  .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.78rem; }

  .badge { padding: 2px 8px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
  .badge-activo { background: var(--verde-bg); color: var(--verde-text); }
  .badge-completada { background: var(--success-bg); color: var(--success-text); }
  .ico { width: 1.15em; height: 1.15em; vertical-align: -0.2em; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
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

  .filtro-cat { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: var(--muted); }
  .filtro-cat select { padding: 0.35rem 0.5rem; border: 1px solid var(--border); border-radius: 5px; font-size: 0.82rem; background: var(--card-bg); color: var(--text); }
  td input.cat-input { width: 140px; }

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
  /* ---------- PAGINACIÓN DE CORTES (regla nueva; variables del tema) ---------- */
  .paginacion { display: flex; align-items: center; justify-content: flex-end; gap: 0.6rem; margin-top: 0.9rem; font-size: 0.82rem; color: var(--muted); }
  .paginacion .btn:disabled { opacity: 0.45; cursor: default; }
  /* ---------- CONFIGURACIÓN DE LA CUENTA ---------- */
  .cfg-grid { display: grid; gap: 1rem; max-width: 780px; margin-top: 1rem; }
  .cfg-card { background: var(--card-bg, #fff); border: 1px solid var(--border, #e5e7eb); border-radius: 12px; padding: 1.25rem 1.4rem; box-shadow: 0 1px 3px var(--shadow, rgba(0,0,0,0.06)); }
  .cfg-card h3 { margin: 0 0 0.2rem; font-size: 1rem; }
  .cfg-card .cfg-sub { margin: 0 0 1rem; font-size: 0.82rem; color: var(--muted, #6b7280); }
  .cfg-perfil { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.1rem; }
  .cfg-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; flex-shrink: 0; }
  .cfg-perfil b { display: block; font-size: 1rem; }
  .cfg-perfil span { color: var(--muted, #6b7280); font-size: 0.82rem; }
  .cfg-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.9rem; }
  .cfg-campo { display: flex; flex-direction: column; gap: 0.25rem; font-size: 0.78rem; color: var(--muted, #6b7280); }
  .cfg-campo input, .cfg-campo select { padding: 0.55rem 0.7rem; border: 1px solid var(--border, #d1d5db); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text, inherit); font-size: 0.9rem; font-family: inherit; }
  .cfg-campo input:disabled { opacity: 0.65; cursor: not-allowed; }
  .cfg-campo small { font-size: 0.72rem; }
  .cfg-acciones { margin-top: 1rem; display: flex; gap: 0.6rem; align-items: center; }
  .cfg-pass { position: relative; display: flex; }
  .cfg-pass input { flex: 1; padding-right: 2.4rem; }
  .cfg-ojo { position: absolute; right: 0.35rem; top: 50%; transform: translateY(-50%); background: transparent; border: 0; cursor: pointer; color: var(--muted, #6b7280); padding: 0.3rem; display: flex; }
  .cfg-ojo:hover { color: var(--accent); }
  .cfg-fuerza { height: 6px; border-radius: 99px; background: var(--border, #e5e7eb); overflow: hidden; margin-top: 0.2rem; }
  .cfg-fuerza i { display: block; height: 100%; width: 0; transition: width 0.2s, background 0.2s; }
  .cfg-fuerza-txt { font-size: 0.72rem; min-height: 1em; }
  .cfg-temas { display: flex; gap: 0.8rem; flex-wrap: wrap; }
  .cfg-tema { flex: 1; min-width: 150px; max-width: 220px; padding: 0.6rem; border: 2px solid var(--border, #d1d5db); border-radius: 12px; background: transparent; color: var(--text, inherit); cursor: pointer; text-align: left; font-family: inherit; }
  .cfg-tema:hover { border-color: var(--accent); }
  .cfg-tema.activo { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.18); }
  .cfg-prev { display: flex; height: 54px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(128,128,128,0.35); margin-bottom: 0.5rem; }
  .cfg-prev i { width: 28%; }
  .cfg-prev b { flex: 1; margin: 7px; border-radius: 5px; }
  .cfg-prev.claro { background: #f4f5f7; } .cfg-prev.claro i { background: #fff; } .cfg-prev.claro b { background: #fff; box-shadow: 0 0 0 1px #e5e7eb; }
  .cfg-prev.oscuro { background: #0f1420; } .cfg-prev.oscuro i { background: #1a2233; } .cfg-prev.oscuro b { background: #1a2233; box-shadow: 0 0 0 1px #2d3748; }
  .cfg-tema span { display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; }
  .cfg-tema .cfg-prev { align-items: stretch; gap: 0; }
  .cfg-tema .ico { width: 1.1em; height: 1.1em; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .cfg-bloque { margin-bottom: 1rem; }
  .cfg-bloque > label { display: block; font-size: 0.78rem; color: var(--muted, #6b7280); margin-bottom: 0.4rem; }
  .cfg-seg { display: inline-flex; border: 1px solid var(--border, #d1d5db); border-radius: 10px; overflow: hidden; }
  .cfg-seg a { padding: 0.5rem 1.1rem; color: var(--text, inherit); text-decoration: none; font-size: 0.88rem; font-weight: 600; }
  .cfg-seg a + a { border-left: 1px solid var(--border, #d1d5db); }
  .cfg-seg a.activo { background: var(--accent); color: #fff; }
  .cfg-fila { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.75rem 0; border-top: 1px solid var(--border, #e5e7eb); }
  .cfg-fila:first-of-type { border-top: 0; }
  .cfg-fila b { display: block; font-size: 0.9rem; font-weight: 600; }
  .cfg-fila small { color: var(--muted, #6b7280); font-size: 0.76rem; }
  .cfg-switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
  .cfg-switch input { opacity: 0; width: 100%; height: 100%; position: absolute; margin: 0; cursor: pointer; z-index: 1; }
  .cfg-switch span { position: absolute; inset: 0; background: var(--border, #cbd5e1); border-radius: 99px; transition: background 0.2s; }
  .cfg-switch span::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; transition: transform 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.3); }
  .cfg-switch input:checked + span { background: var(--accent); }
  .cfg-switch input:checked + span::after { transform: translateX(18px); }
  .cfg-info { display: flex; justify-content: space-between; gap: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--border, #e5e7eb); font-size: 0.85rem; }
  .cfg-info small { color: var(--muted, #6b7280); display: block; }
  .sidebar #btn-configuracion { margin-top: 0.6rem; border-top: 1px solid var(--border, #e5e7eb); }
  .cfg-escudo { width: 40px; height: 40px; border-radius: 50%; background: var(--border, #e5e7eb); color: var(--muted, #6b7280); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.2s, color 0.2s; }
  .cfg-escudo .ico { width: 1.3em; height: 1.3em; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .cfg-escudo.on { background: #dcfce7; color: #15803d; }
  [data-theme="dark"] .cfg-escudo.on { background: #143621; color: #4ade80; }
  .cfg-switch.verde input:checked + span { background: #16a34a; }
  .cfg-2fa-estado { transition: color 0.2s; }
  .cfg-2fa-estado.on { color: #15803d !important; font-weight: 600; }
  [data-theme="dark"] .cfg-2fa-estado.on { color: #4ade80 !important; }
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
    <div class="brand">Nexora <span class="sucursal">— <span data-i18n="topbarPanelGerente">Panel de Gerente</span> · {{ auth()->user()->sucursal->nombre ?? __('Sin sucursal asignada') }}</span></div>
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
          <div class="resumen-card"><div class="label" data-i18n="labelVentasHoy">Ventas de hoy</div><div class="valor" id="msVentasHoy">$0.00 MXN</div></div>
          <div class="resumen-card"><div class="label" data-i18n="labelVentasMes">Ventas del mes</div><div class="valor" id="msVentasMes">$0.00 MXN</div></div>
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
            <tr><th data-i18n="thFoto">Foto</th><th data-i18n="thProducto">Producto</th><th data-i18n="thStockActual">Stock actual</th><th data-i18n="thAjustar">Ajustar</th><th></th></tr>
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
            <tr><th data-i18n="thFecha">Fecha</th><th data-i18n="thFoto">Foto</th><th data-i18n="thProducto">Producto</th><th data-i18n="thCantidad">Cantidad</th><th data-i18n="thCostoUnit">Costo unit.</th><th data-i18n="thTotal">Total</th><th data-i18n="thProveedor">Proveedor</th></tr>
            <tbody id="tablaCompras"></tbody>
          </table>
        </div>
      </div>

      <div class="seccion" id="sec-corte">
        <h2 data-i18n="corteHeading">Cortes de caja</h2>
        <p class="desc" data-i18n="corteDesc">Historial de turnos de tus cajas: lo vendido contra el efectivo que contó cada cajero.</p>
        <!-- Totales de los turnos abiertos hoy (netos de devoluciones) -->
        <div class="resumen-grid">
          <div class="resumen-card"><div class="label" data-i18n="ventasEfectivo">Ventas en efectivo</div><div class="valor" id="resEfectivo">$0.00 MXN</div></div>
          <div class="resumen-card"><div class="label" data-i18n="ventasTarjeta">Ventas con tarjeta</div><div class="valor" id="resTarjeta">$0.00 MXN</div></div>
          <div class="resumen-card"><div class="label" data-i18n="totalDia">Total del día</div><div class="valor" id="resTotal">$0.00 MXN</div></div>
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
            <tr><th data-i18n="thCaja">Caja</th><th data-i18n="thEstado">Estado</th><th data-i18n="thEnTurno">En turno</th><th></th></tr>
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
            <div class="campo"><label data-i18n="labelCategoria">Categoría</label><input type="text" id="nuevoCategoria" list="listaCategorias" maxlength="40"><datalist id="listaCategorias"></datalist></div>
            <div class="campo"><label data-i18n="labelFotoProducto">Foto (opcional)</label><label class="selector-foto" for="nuevaFoto" title="Seleccionar foto" aria-label="Seleccionar foto"><svg class="icono-foto" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 8h.01"></path><path d="m3 16 5-5c.928-.893 2.072-.893 3 0l4 4"></path><path d="m14 14 1-1c.928-.893 2.072-.893 3 0l3 3"></path><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M19 2v4"></path><path d="M17 4h4"></path></svg><span>Foto</span><input type="file" id="nuevaFoto" accept="image/*"></label></div>
            <button class="btn btn-primario" onclick="darDeAlta()" data-i18n="btnDarAlta">Dar de alta</button>
          </div>
        </div>
        <div class="card">
          <div class="filtro-cat"><label for="filtroCategoria" data-i18n="filtroCategoria">Categoría</label><select id="filtroCategoria" onchange="renderProductos()"></select></div>
          <table>
            <tr>
              <th data-i18n="thFoto">Foto</th>
              <th data-i18n="thProducto">Producto</th>
              <th data-i18n="thCategoria">Categoría</th>
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

      <div class="seccion" id="sec-configuracion"></div>

      <div class="seccion" id="sec-reportes">
        <h2 data-i18n="repHeading">Reportes</h2>
        <p class="desc" data-i18n="repDesc">Avisa al administrador de una falla, un faltante, un problema con la caja o una sugerencia.</p>
        <div class="card">
          <h3 data-i18n="repNuevoHeading">Nuevo reporte</h3>
          <div class="fila-form">
            <div class="campo"><label for="repTipo" data-i18n="repLabelTipo">Tipo</label><select id="repTipo"></select></div>
            <div class="campo campo-ancho"><label for="repAsunto" data-i18n="repLabelAsunto">Asunto</label><input type="text" id="repAsunto" maxlength="120" data-i18n-placeholder="repPhAsunto"></div>
          </div>
          <div class="fila-form">
            <div class="campo campo-ancho"><label for="repDescripcion" data-i18n="repLabelDescripcion">Descripción</label><textarea id="repDescripcion" rows="3" maxlength="1000" data-i18n-placeholder="repPhDescripcion"></textarea></div>
          </div>
          <button class="btn btn-primario rep-btn" id="btnEnviarReporte" onclick="enviarReporte()"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg><span data-i18n="repBtnEnviar">Enviar reporte</span></button>
        </div>
        <div class="card">
          <h3 data-i18n="repListaHeading">Mis reportes</h3>
          <table>
            <tr><th data-i18n="repThFecha">Fecha</th><th data-i18n="repThAutor">Enviado por</th><th data-i18n="repThTipo">Tipo</th><th data-i18n="repThAsunto">Asunto</th><th data-i18n="repThEstado">Estado</th><th data-i18n="repThRespuesta">Respuesta</th></tr>
            <tbody id="tablaReportes"></tbody>
          </table>
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

  /* ---------- DATOS (declarados antes de cualquier función que los use) ---------- */
  let productos = [];
  let compras = [];
  let historial = [];
  let cajas = [];
  let cortes = [];
  let cortesHoy = { efectivo: 0, tarjeta: 0 };              // totales de los turnos de hoy (los calcula el servidor)
  let paginaCortes = { pagina: 1, paginas: 1, total: 0 };
  let miSucursal = null;
  let ventaAbierta = null;   // folio de la venta cuyo panel de devolución está abierto
  let detalleVenta = null;   // detalle (líneas + devoluciones) de esa venta
  let reportes = [];         // reportes de la sucursal (suyos y de sus cajeros)

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

  async function cargarCortes(pagina = paginaCortes.pagina) {
    try {
      const data = await api('/gerente/cortes?pagina=' + pagina);
      cortes = data.cortes;
      cortesHoy = data.hoy;
      paginaCortes = { pagina: data.pagina, paginas: data.paginas, total: data.total };
    }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  const dinero = n => '$' + Number(n).toFixed(2) + ' MXN';

  /* ---------- CATEGORÍAS (las escribe el gerente; cada negocio tiene las suyas) ---------- */
  const escCat = c => String(c).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  const claveCat = c => c.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

  // Limpia espacios y reutiliza la escritura que ya existe ("frutas" -> "Frutas"), para no duplicar categorías.
  function normalizarCategoria(texto) {
    const limpio = String(texto || '').trim().replace(/\s+/g, ' ');
    if (!limpio) return '';
    const existente = productos.map(p => p.categoria).find(c => c && claveCat(c) === claveCat(limpio));
    return existente || limpio;
  }

  function categoriasExistentes() {
    return [...new Set(productos.map(p => p.categoria).filter(Boolean))].sort((a, b) => a.localeCompare(b, idioma));
  }

  function llenarCategoriaControles() {
    const cats = categoriasExistentes();
    document.getElementById('listaCategorias').innerHTML = cats.map(c => `<option value="${escCat(c)}">`).join('');
    const filtro = document.getElementById('filtroCategoria');
    const previo = filtro.value;
    filtro.innerHTML = `<option value="">${t('todasCategorias')}</option>`
      + cats.map(c => `<option value="${escCat(c)}">${escCat(c)}</option>`).join('')
      + (productos.some(p => !p.categoria) ? `<option value="__sin__">${t('sinCategoria')}</option>` : '');
    filtro.value = [...filtro.options].some(o => o.value === previo) ? previo : '';
  }
  const ICONO_REFRESCAR = '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>';
  const ICONO_GUARDAR = '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>';
  const ICONO_POWER = '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.77.04"/></svg>';
  const ICONO_BAN = '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>';
  const ICONO_BASURA = '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>';

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
      labelProducto: 'Producto', labelCantidad: 'Cantidad', labelCostoUnitario: 'Costo unitario (MXN)', labelProveedor: 'Proveedor',
      placeholderNombre: 'Nombre', btnRegistrar: 'Registrar', comprasRegistradasHeading: 'Compras registradas',
      thFecha: 'Fecha', thCantidad: 'Cantidad', thCostoUnit: 'Costo unit.', thTotal: 'Total', thProveedor: 'Proveedor',
      sinCompras: 'Sin compras registradas',
      corteHeading: 'Cortes de caja', corteDesc: 'Historial de turnos de tus cajas: lo vendido contra el efectivo que contó cada cajero.',
      ventasEfectivo: 'Ventas en efectivo', ventasTarjeta: 'Ventas con tarjeta', totalDia: 'Total del día',
      fondoInicialLabel: 'Fondo inicial de caja', efectivoContadoLabel: 'Efectivo contado físicamente (MXN)',
      efectivoEsperado: 'Efectivo esperado en caja', sobrante: 'Sobrante', faltante: 'Faltante', cajaCuadrada: 'Caja cuadrada',
      cajasHeading: 'Registro de cajas', cajasDesc: 'Administra las cajas registradoras disponibles en esta sucursal.',
      cajaNombreLabel: 'Nombre de la caja', btnAgregarCaja: 'Agregar caja', thCaja: 'Caja', thEstado: 'Estado',
      badgeAbierta: 'Abierta', badgeCerrada: 'Cerrada', btnAbrir: 'Abrir', btnCerrar: 'Cerrar', btnEliminar: 'Eliminar', btnCancelar: 'Cancelar',
      sinCajas: 'Sin cajas registradas', alertNombreCaja: 'Ingresa el nombre de la caja.', confirmEliminarCaja: '¿Eliminar esta caja?',
      preciosHeading: 'Precios e IVA', preciosDesc: 'Modifica el precio base y el porcentaje de IVA de cada producto.',
      thPrecioBase: 'Precio base (MXN)', thIva: 'IVA %', thPrecioFinal: 'Precio final', btnGuardar: 'Guardar',
      productosHeading: 'Alta / baja de productos', productosDesc: 'Agrega productos, ajusta su precio e IVA o desactívalos. Los cambios solo aplican a tu sucursal.',
      labelNombre: 'Nombre', thCategoria: 'Categoría', labelCategoria: 'Categoría', filtroCategoria: 'Categoría', todasCategorias: 'Todas', sinCategoria: 'Sin categoría', labelStockInicial: 'Stock inicial', placeholderProducto: 'Producto',
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
      labelFotoProducto: 'Foto del producto', thFoto: 'Foto', fotoActualizada: 'Foto actualizada',
      navConfiguracion: 'Configuración',
      navReportes: 'Reportes', repHeading: 'Reportes al administrador', repNuevoHeading: 'Nuevo reporte', repListaHeading: 'Reportes de la sucursal',
      repDesc: 'Avisa al administrador de una falla, un faltante de producto, un problema con la caja o una sugerencia. Aquí también ves los reportes de tus cajeros.',
      repThFecha: 'Fecha', repThEstado: 'Estado',
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
      tagline: 'Manager panel', placeholderUsuario: 'Username', placeholderClave: 'Password',
      btnEntrar: 'Log in', demoNote: 'Visual demo — any data works',
      topbarSucursal: '— Manager Panel · Centro Branch', cerrarSesion: 'Log out',
      navInventario: 'Inventory', navCompras: 'Register purchases', navCorte: 'Cash counts',
      navCajas: 'Cash registers', navPrecios: 'Prices & tax', navProductos: 'Add / remove products',
      navHistorial: 'Sales history',
      invHeading: 'Inventory', invDesc: 'Check and adjust the stock at your branch.',
      thProducto: 'Product', thStockActual: 'Current stock', thAjustar: 'Adjust', btnAplicar: 'Apply',
      comprasHeading: 'Register purchases', comprasDesc: 'Log incoming stock received by the cashier or supplier.',
      labelProducto: 'Product', labelCantidad: 'Quantity', labelCostoUnitario: 'Unit cost (MXN)', labelProveedor: 'Supplier',
      placeholderNombre: 'Name', btnRegistrar: 'Register', comprasRegistradasHeading: 'Registered purchases',
      thFecha: 'Date', thCantidad: 'Quantity', thCostoUnit: 'Unit cost', thTotal: 'Total', thProveedor: 'Supplier',
      sinCompras: 'No purchases registered',
      corteHeading: 'Cash counts', corteDesc: 'Shift history for your registers: sales vs. the cash each cashier counted.',
      ventasEfectivo: 'Cash sales', ventasTarjeta: 'Card sales', totalDia: "Today's total",
      fondoInicialLabel: 'Starting cash fund', efectivoContadoLabel: 'Physical cash counted (MXN)',
      efectivoEsperado: 'Expected cash in register', sobrante: 'Overage', faltante: 'Shortage', cajaCuadrada: 'Register balanced',
      cajasHeading: 'Cash registers', cajasDesc: 'Manage the cash registers available at this branch.',
      cajaNombreLabel: 'Register name', btnAgregarCaja: 'Add register', thCaja: 'Register', thEstado: 'Status',
      badgeAbierta: 'Open', badgeCerrada: 'Closed', btnAbrir: 'Open', btnCerrar: 'Close', btnEliminar: 'Delete', btnCancelar: 'Cancel',
      sinCajas: 'No registers added', alertNombreCaja: 'Enter the register name.', confirmEliminarCaja: 'Delete this register?',
      preciosHeading: 'Prices & tax', preciosDesc: 'Edit the base price and tax rate for each product.',
      thPrecioBase: 'Base price (MXN)', thIva: 'Tax %', thPrecioFinal: 'Final price', btnGuardar: 'Save',
      productosHeading: 'Add / remove products', productosDesc: 'Add products, adjust their price and tax or deactivate them. Changes only apply to your branch.',
      labelNombre: 'Name', thCategoria: 'Category', labelCategoria: 'Category', filtroCategoria: 'Category', todasCategorias: 'All', sinCategoria: 'No category', labelStockInicial: 'Initial stock', placeholderProducto: 'Product',
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
      labelFotoProducto: 'Product photo', thFoto: 'Photo', fotoActualizada: 'Photo updated',
      navConfiguracion: 'Settings',
      navReportes: 'Reports', repHeading: 'Reports to the administrator', repNuevoHeading: 'New report', repListaHeading: 'Branch reports',
      repDesc: 'Let the administrator know about a failure, a missing product, a register problem or a suggestion. Your cashiers\' reports also appear here.',
      repThFecha: 'Date', repThEstado: 'Status',
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

  /* ---------- ICONOS SVG DEL MENÚ ---------- */
  const svgMenu = d => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${d}</svg>`;
  const ICONOS_MENU = {
    navMiSucursal: svgMenu('<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/>'),
    navInventario: svgMenu('<path d="M12 22v-9"/><path d="M15.17 2.21a1.67 1.67 0 0 1 1.63 0L21 4.57a1.93 1.93 0 0 1 0 3.36L8.82 14.79a1.655 1.655 0 0 1-1.64 0L3 12.43a1.93 1.93 0 0 1 0-3.36z"/><path d="M20 13v3.87a2.06 2.06 0 0 1-1.11 1.83l-6 3.08a1.93 1.93 0 0 1-1.78 0l-6-3.08A2.06 2.06 0 0 1 4 16.87V13"/><path d="M21 12.43a1.93 1.93 0 0 0 0-3.36L8.82 1.2a1.655 1.655 0 0 0-1.64 0L3 3.57a1.93 1.93 0 0 0 0 3.36l12.18 6.86a1.636 1.636 0 0 0 1.63 0z"/>'),
    navCompras: svgMenu('<path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4"/><path d="M4.5 15.5h15"/><path d="m5 11 4-7"/><path d="m9 11 1 9"/>'),
    navCajas: svgMenu('<rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/>'),
    navProductos: svgMenu('<path d="M16 16h6"/><path d="M19 13v6"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/>'),
    navHistorial: svgMenu('<circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>'),
    navCorte: svgMenu('<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>'),
    navConfiguracion: svgMenu('<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>'),
    navReportes: svgMenu('<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M12 7v2"/><path d="M12 13h.01"/>')
  };

  /* ================= CONFIGURACIÓN DE LA CUENTA ================= */
  const CFG_ETIQUETA = { es: 'Gerente', en: 'Manager' };
  const CFG_TEXTOS = {
  "es": {
    "navConfiguracion": "Configuración",
    "cfgTitulo": "Configuración",
    "cfgDesc": "Administra tu cuenta, la seguridad y la apariencia.",
    "perfilTitulo": "Perfil",
    "perfilSub": "Así te identifican en el sistema.",
    "lblNombre": "Nombre",
    "lblApellido": "Apellido",
    "lblCorreo": "Correo",
    "guardarCambios": "Guardar cambios",
    "perfilGuardado": "Perfil actualizado",
    "nombreObligatorio": "El nombre es obligatorio",
    "passTitulo": "Contraseña",
    "passSub": "Usa al menos 8 caracteres. Mezcla letras, números y símbolos para que sea más segura.",
    "lblPassActual": "Contraseña actual",
    "lblPassNueva": "Nueva contraseña",
    "lblPassConfirmar": "Confirmar nueva contraseña",
    "cambiarPass": "Cambiar contraseña",
    "passFalta": "Escribe tu contraseña actual",
    "passCorta": "La nueva contraseña debe tener al menos 8 caracteres",
    "passNoCoincide": "Las contraseñas no coinciden",
    "passIgual": "La nueva contraseña debe ser distinta a la actual",
    "passCambiada": "Contraseña actualizada",
    "fuerzaDebil": "Seguridad: débil",
    "fuerzaMedia": "Seguridad: media",
    "fuerzaFuerte": "Seguridad: fuerte",
    "verPass": "Mostrar u ocultar contraseña",
    "aparienciaTitulo": "Apariencia e idioma",
    "aparienciaSub": "El tema se aplica al instante en este dispositivo.",
    "lblTema": "Tema",
    "temaClaro": "Claro",
    "temaOscuro": "Oscuro",
    "lblIdioma": "Idioma",
    "lblMoneda": "Moneda",
    "monedaTxt": "Peso mexicano (MXN)",
    "monedaHint": "Todos los precios se muestran en pesos mexicanos.",
    "errorGuardar": "No se pudo guardar. Inténtalo de nuevo.",
    "dosPasosTitulo": "Verificación en dos pasos",
    "dosPasosSub": "Agrega una capa extra de seguridad al iniciar sesión.",
    "dosPasosActiva": "Activada",
    "dosPasosInactiva": "Desactivada",
    "dosPasosHint": "Te pedirá un código extra, además de tu contraseña.",
    "correoHint": "El correo es tu usuario; para cambiarlo pídelo al administrador."
  },
  "en": {
    "navConfiguracion": "Settings",
    "cfgTitulo": "Settings",
    "cfgDesc": "Manage your account, security and appearance.",
    "perfilTitulo": "Profile",
    "perfilSub": "This is how you are identified in the system.",
    "lblNombre": "First name",
    "lblApellido": "Last name",
    "lblCorreo": "Email",
    "guardarCambios": "Save changes",
    "perfilGuardado": "Profile updated",
    "nombreObligatorio": "First name is required",
    "passTitulo": "Password",
    "passSub": "Use at least 8 characters. Mix letters, numbers and symbols to make it stronger.",
    "lblPassActual": "Current password",
    "lblPassNueva": "New password",
    "lblPassConfirmar": "Confirm new password",
    "cambiarPass": "Change password",
    "passFalta": "Enter your current password",
    "passCorta": "The new password must be at least 8 characters long",
    "passNoCoincide": "The passwords do not match",
    "passIgual": "The new password must be different from the current one",
    "passCambiada": "Password updated",
    "fuerzaDebil": "Strength: weak",
    "fuerzaMedia": "Strength: medium",
    "fuerzaFuerte": "Strength: strong",
    "verPass": "Show or hide password",
    "aparienciaTitulo": "Appearance and language",
    "aparienciaSub": "The theme is applied instantly on this device.",
    "lblTema": "Theme",
    "temaClaro": "Light",
    "temaOscuro": "Dark",
    "lblIdioma": "Language",
    "lblMoneda": "Currency",
    "monedaTxt": "Mexican peso (MXN)",
    "monedaHint": "All prices are shown in Mexican pesos.",
    "errorGuardar": "Could not save. Please try again.",
    "dosPasosTitulo": "Two-step verification",
    "dosPasosSub": "Add an extra layer of security when you sign in.",
    "dosPasosActiva": "Enabled",
    "dosPasosInactiva": "Disabled",
    "dosPasosHint": "It will ask for an extra code, besides your password.",
    "correoHint": "Your email is your username; ask the administrator to change it."
  }
};
  const ct = k => (CFG_TEXTOS[idioma] || CFG_TEXTOS.es)[k] ?? k;
  const cfgIcono = d => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${d}</svg>`;
  const CFG_OJO = '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>';
  const CFG_OJO_NO = '<path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/>';
  const CFG_SOL = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>';
  const CFG_LUNA = '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>';

  /* --- perfil y contraseña (los guarda el servidor) --- */
  let cfgPerfil = null;

  async function cfgApi(url, metodo = 'GET', cuerpo = null) {
    const opciones = { method: metodo, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } };
    if (cuerpo !== null) { opciones.headers['Content-Type'] = 'application/json'; opciones.body = JSON.stringify(cuerpo); }
    const resp = await fetch(url, opciones);
    const data = resp.status === 204 ? null : await resp.json().catch(() => null);
    if (!resp.ok) {
      const primero = data && data.errors ? Object.values(data.errors)[0][0] : null;
      throw new Error(primero || (data && data.message) || ct('errorGuardar'));
    }
    return data;
  }

  async function cfgCargarPerfil() {
    try { cfgPerfil = await cfgApi('/configuracion/perfil'); }
    catch (e) { cfgPerfil = cfgPerfil || { nombre: '', apellido: '', correo: '', rol: '', sucursal: '' }; }
  }

  function cfgIniciales() {
    return (((cfgPerfil.nombre || '')[0] || '') + ((cfgPerfil.apellido || '')[0] || '')).toUpperCase() || '?';
  }

  function cfgRefrescarCabecera() {
    document.getElementById('cfgAvatar').textContent = cfgIniciales();
    document.getElementById('cfgNombreCompleto').textContent = (cfgPerfil.nombre + ' ' + cfgPerfil.apellido).trim();
  }

  async function cfgGuardarPerfil() {
    const nombre = document.getElementById('cfgNombre').value.trim();
    const apellido = document.getElementById('cfgApellido').value.trim();
    if (!nombre) { mostrarToast(ct('nombreObligatorio'), 'error'); return; }
    try {
      await cfgApi('/configuracion/perfil', 'PUT', { nombre, apellido });
      cfgPerfil = Object.assign({}, cfgPerfil, { nombre, apellido });
      cfgRefrescarCabecera();
      mostrarToast(ct('perfilGuardado'), 'exito');
    } catch (e) { mostrarToast(e.message, 'error'); }
  }

  function cfgVerPass(id, boton) {
    const campo = document.getElementById(id);
    const oculto = campo.type === 'password';
    campo.type = oculto ? 'text' : 'password';
    boton.innerHTML = cfgIcono(oculto ? CFG_OJO_NO : CFG_OJO);
  }

  function cfgFuerza() {
    const p = document.getElementById('cfgPassNueva').value;
    let puntos = 0;
    if (p.length >= 8) puntos++;
    if (p.length >= 12) puntos++;
    if (/[a-z]/.test(p) && /[A-Z]/.test(p)) puntos++;
    if (/\d/.test(p)) puntos++;
    if (/[^A-Za-z0-9]/.test(p)) puntos++;
    const nivel = !p ? 0 : puntos <= 1 ? 1 : puntos <= 3 ? 2 : 3;
    const barra = document.getElementById('cfgFuerzaBarra');
    barra.style.width = ['0%', '33%', '66%', '100%'][nivel];
    barra.style.background = ['transparent', '#dc2626', '#f59e0b', '#16a34a'][nivel];
    document.getElementById('cfgFuerzaTxt').textContent = nivel ? ct(['', 'fuerzaDebil', 'fuerzaMedia', 'fuerzaFuerte'][nivel]) : '';
  }

  async function cfgCambiarPass() {
    const actual = document.getElementById('cfgPassActual').value;
    const nueva = document.getElementById('cfgPassNueva').value;
    const confirmar = document.getElementById('cfgPassConfirmar').value;
    if (!actual) { mostrarToast(ct('passFalta'), 'error'); return; }
    if (nueva.length < 8) { mostrarToast(ct('passCorta'), 'error'); return; }
    if (nueva !== confirmar) { mostrarToast(ct('passNoCoincide'), 'error'); return; }
    if (nueva === actual) { mostrarToast(ct('passIgual'), 'error'); return; }
    try {
      await cfgApi('/configuracion/password', 'PUT', { password_actual: actual, password_nueva: nueva, password_nueva_confirmation: confirmar });
      ['cfgPassActual', 'cfgPassNueva', 'cfgPassConfirmar'].forEach(id => { document.getElementById(id).value = ''; });
      cfgFuerza();
      mostrarToast(ct('passCambiada'), 'exito');
    } catch (e) { mostrarToast(e.message, 'error'); }
  }

  /* --- tema e idioma --- */
  function cfgElegirTema(tema) {
    // Reutiliza toggleTema(), que ya guarda la preferencia como lo hace tu panel
    const actual = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    if (tema !== actual) toggleTema();
    cfgMarcarTema();
  }
  function cfgMarcarTema() {
    const oscuro = document.documentElement.getAttribute('data-theme') === 'dark';
    document.querySelectorAll('.cfg-tema').forEach(b => b.classList.toggle('activo', (b.dataset.tema === 'dark') === oscuro));
  }
  new MutationObserver(cfgMarcarTema).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

  /* --- verificación en dos pasos (por ahora solo visual) --- */
  let cfg2faActivo = false;
  const CFG_ESCUDO = '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>';

  function cfgToggle2fa(activo) {
    cfg2faActivo = activo;
    document.getElementById('cfgEscudo').classList.toggle('on', activo);
    const txt = document.getElementById('cfg2faEstado');
    txt.classList.toggle('on', activo);
    txt.textContent = ct(activo ? 'dosPasosActiva' : 'dosPasosInactiva');
  }

  /* --- pantalla --- */
  async function renderConfiguracion() {
    const cont = document.getElementById('sec-configuracion');
    if (!cfgPerfil) { cont.innerHTML = ''; await cfgCargarPerfil(); }
    const rolTexto = cfgPerfil.rol || CFG_ETIQUETA[idioma] || CFG_ETIQUETA.es;
    const campoPass = (id, etiqueta, extra = '') => `
      <div class="cfg-campo"><label for="${id}">${ct(etiqueta)}</label>
        <div class="cfg-pass"><input type="password" id="${id}" autocomplete="${id === 'cfgPassActual' ? 'current-password' : 'new-password'}" ${extra}>
          <button type="button" class="cfg-ojo" onclick="cfgVerPass('${id}', this)" title="${ct('verPass')}" aria-label="${ct('verPass')}">${cfgIcono(CFG_OJO)}</button></div></div>`;
    cont.innerHTML = `
      <h2>${ct('cfgTitulo')}</h2>
      <p class="desc">${ct('cfgDesc')}</p>
      <div class="cfg-grid">
        <div class="cfg-card">
          <h3>${ct('perfilTitulo')}</h3><p class="cfg-sub">${ct('perfilSub')}</p>
          <div class="cfg-perfil"><div class="cfg-avatar" id="cfgAvatar">${esc(cfgIniciales())}</div>
            <div><b id="cfgNombreCompleto">${esc((cfgPerfil.nombre + ' ' + cfgPerfil.apellido).trim())}</b><span>${esc(rolTexto)}${cfgPerfil.sucursal ? ' · ' + esc(cfgPerfil.sucursal) : ''}</span></div></div>
          <div class="cfg-form">
            <div class="cfg-campo"><label for="cfgNombre">${ct('lblNombre')}</label><input type="text" id="cfgNombre" maxlength="80" value="${esc(cfgPerfil.nombre)}"></div>
            <div class="cfg-campo"><label for="cfgApellido">${ct('lblApellido')}</label><input type="text" id="cfgApellido" maxlength="80" value="${esc(cfgPerfil.apellido)}"></div>
            <div class="cfg-campo"><label for="cfgCorreo">${ct('lblCorreo')}</label><input type="email" id="cfgCorreo" value="${esc(cfgPerfil.correo)}" disabled><small>${ct('correoHint')}</small></div>
          </div>
          <div class="cfg-acciones"><button class="btn btn-primario" onclick="cfgGuardarPerfil()">${ct('guardarCambios')}</button></div>
        </div>

        <div class="cfg-card">
          <h3>${ct('passTitulo')}</h3><p class="cfg-sub">${ct('passSub')}</p>
          <div class="cfg-form">
            ${campoPass('cfgPassActual', 'lblPassActual')}
            ${campoPass('cfgPassNueva', 'lblPassNueva', 'oninput="cfgFuerza()"')}
            ${campoPass('cfgPassConfirmar', 'lblPassConfirmar')}
            <div class="cfg-campo"><div class="cfg-fuerza"><i id="cfgFuerzaBarra"></i></div><span class="cfg-fuerza-txt" id="cfgFuerzaTxt"></span></div>
          </div>
          <div class="cfg-acciones"><button class="btn btn-primario" onclick="cfgCambiarPass()">${ct('cambiarPass')}</button></div>
        </div>

        <div class="cfg-card">
          <h3>${ct('dosPasosTitulo')}</h3><p class="cfg-sub">${ct('dosPasosSub')}</p>
          <div class="cfg-fila">
            <div style="display:flex; align-items:center; gap:0.8rem;">
              <span class="cfg-escudo ${cfg2faActivo ? 'on' : ''}" id="cfgEscudo">${cfgIcono(CFG_ESCUDO)}</span>
              <div><b>${ct('dosPasosTitulo')}</b><small class="cfg-2fa-estado ${cfg2faActivo ? 'on' : ''}" id="cfg2faEstado">${ct(cfg2faActivo ? 'dosPasosActiva' : 'dosPasosInactiva')}</small><small style="display:block">${ct('dosPasosHint')}</small></div>
            </div>
            <label class="cfg-switch verde"><input type="checkbox" ${cfg2faActivo ? 'checked' : ''} onchange="cfgToggle2fa(this.checked)" aria-label="${ct('dosPasosTitulo')}"><span></span></label>
          </div>
        </div>

        <div class="cfg-card">
          <h3>${ct('aparienciaTitulo')}</h3><p class="cfg-sub">${ct('aparienciaSub')}</p>
          <div class="cfg-bloque"><label>${ct('lblTema')}</label>
            <div class="cfg-temas">
              <button type="button" class="cfg-tema" data-tema="light" onclick="cfgElegirTema('light')"><span class="cfg-prev claro"><i></i><b></b></span><span>${cfgIcono(CFG_SOL)}${ct('temaClaro')}</span></button>
              <button type="button" class="cfg-tema" data-tema="dark" onclick="cfgElegirTema('dark')"><span class="cfg-prev oscuro"><i></i><b></b></span><span>${cfgIcono(CFG_LUNA)}${ct('temaOscuro')}</span></button>
            </div></div>
          <div class="cfg-bloque"><label>${ct('lblIdioma')}</label>
            <div class="cfg-seg">
              <a href="{{ route('lang.switch', 'es') }}" data-lang="es" class="${idioma === 'es' ? 'activo' : ''}">Español</a>
              <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="${idioma === 'en' ? 'activo' : ''}">English</a>
            </div></div>
          <div class="cfg-info"><div><b>${ct('lblMoneda')}</b><small>${ct('monedaHint')}</small></div><span>${ct('monedaTxt')}</span></div>
        </div>

      </div>`;
    cfgMarcarTema();
  }

  const secciones = [
    { id: 'mi-sucursal', key: 'navMiSucursal'},
    { id: 'inventario', key: 'navInventario' },
    { id: 'compras', key: 'navCompras' },
    { id: 'cajas', key: 'navCajas' },
    { id: 'productos', key: 'navProductos' },
    { id: 'historial', key: 'navHistorial' },
    // El cajero hace su corte; aquí el gerente consulta el historial de cortes
    { id: 'corte', key: 'navCorte' },
    { id: 'reportes', key: 'navReportes' },
    { id: 'configuracion', key: 'navConfiguracion' },
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
    try { localStorage.setItem('theme', nuevo); } catch (e) {}
  }

  (function inicializarTema() {
    let guardado = null;
    try { guardado = localStorage.getItem('theme'); } catch (e) {}
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
    document.getElementById('sidebar').innerHTML = secciones.map(s => `<button id="btn-${s.id}" onclick="mostrarSeccion('${s.id}')">${(ICONOS_MENU[s.key] || '') + t(s.key)}</button>`).join('');
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
    if (id === 'reportes') { renderReportes(); cargarReportes().then(renderReportes); }
    if (id === 'configuracion') renderConfiguracion();
  }

  /* ---------- REPORTES AL ADMINISTRADOR ---------- */
  const TIPOS_REPORTE = ['falla', 'faltante', 'caja', 'sugerencia', 'otro'];

  async function cargarReportes() {
    try { reportes = await api('/gerente/reportes'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  function renderReportes() {
    const sel = document.getElementById('repTipo');
    const previo = sel.value;
    sel.innerHTML = TIPOS_REPORTE.map(tp => `<option value="${tp}">${t('repTipo_' + tp)}</option>`).join('');
    if (previo) sel.value = previo;

    document.getElementById('tablaReportes').innerHTML = reportes.length
      ? reportes.map(r => `
        <tr>
          <td>${formatearFecha(r.fecha)}</td>
          <td>${r.mio ? t('repTu') : esc(r.autor ?? t('repUsuarioEliminado'))}${r.rol_autor && !r.mio ? `<div class="rep-desc">${t('repRol_' + r.rol_autor)}</div>` : ''}</td>
          <td>${t('repTipo_' + r.tipo)}</td>
          <td><strong>${esc(r.asunto)}</strong><div class="rep-desc">${esc(r.descripcion)}</div></td>
          <td><span class="badge rep-${r.estado}">${t('repEstado_' + r.estado)}</span></td>
          <td>${r.respuesta ? `${esc(r.respuesta)}<div class="rep-desc">${formatearFecha(r.fecha_respuesta)}</div>` : `<span class="rep-desc">${t('repSinRespuesta')}</span>`}</td>
        </tr>`).join('')
      : `<tr><td colspan="6" style="color:var(--muted);">${t('repSinReportes')}</td></tr>`;
  }

  async function enviarReporte() {
    const tipo = document.getElementById('repTipo').value;
    const asunto = document.getElementById('repAsunto').value.trim();
    const descripcion = document.getElementById('repDescripcion').value.trim();
    if (!asunto || !descripcion) { mostrarToast(t('repAlertCompleta'), 'error'); return; }

    const btn = document.getElementById('btnEnviarReporte');
    btn.disabled = true;   // evita mandar el mismo reporte dos veces con doble clic
    try {
      await api('/gerente/reportes', 'POST', { tipo, asunto, descripcion });
      document.getElementById('repAsunto').value = '';
      document.getElementById('repDescripcion').value = '';
      await cargarReportes();
      renderReportes();
      mostrarToast(t('repEnviado'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    } finally {
      btn.disabled = false;
    }
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
    document.getElementById('msVentasHoy').textContent = dinero(Number(miSucursal.ventas_hoy));
    document.getElementById('msVentasMes').textContent = dinero(Number(miSucursal.ventas_mes));
    document.getElementById('msDireccion').textContent = miSucursal.direccion || '—';
    document.getElementById('msContacto').textContent = miSucursal.contacto || '—';
    document.getElementById('msEstado').textContent = miSucursal.estado || '—';
  }

  /* ---------- INVENTARIO ---------- */
  function renderInventario() {
    document.getElementById('tablaInventario').innerHTML = productos.filter(p => p.activo).map(p => `
      <tr>
        <td>${p.foto
          ? `<img src="${esc(p.foto)}" alt="${esc(p.nombre)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">`
          : `<span style="color:var(--muted-light);font-size:0.75rem;">—</span>`}</td>
        <td>${esc(p.nombre)}</td>
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
    select.innerHTML = productos.filter(p => p.activo).map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join('');
    if (seleccionado) select.value = seleccionado; // no perder la selección al refrescar

    // El servidor ya las manda de la más reciente a la más antigua
    document.getElementById('tablaCompras').innerHTML = compras.map(c => `
      <tr>
        <td>${formatearFecha(c.fecha)}</td>
        <td>${c.foto ? `<img src="${esc(c.foto)}" alt="${esc(c.producto)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">` : `<span style="color:var(--muted-light);">—</span>`}</td>
        <td>${esc(c.producto)}</td><td>${c.cantidad}</td>
        <td>${dinero(c.costo)}</td><td>${dinero(c.total)}</td><td>${esc(c.proveedor || '—')}</td>
      </tr>`).join('') || `<tr><td colspan="7" style="color:var(--muted-light);">${t('sinCompras')}</td></tr>`;
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

  function renderCortes() {
    // Tarjetas de resumen: turnos abiertos HOY, netos de devoluciones (los calcula el servidor:
    // así no dependen de la página del historial que se esté viendo)
    const { efectivo, tarjeta } = cortesHoy;
    document.getElementById('resEfectivo').textContent = dinero(efectivo);
    document.getElementById('resTarjeta').textContent = dinero(tarjeta);
    document.getElementById('resTotal').textContent = dinero(efectivo + tarjeta);

    document.getElementById('tablaCortes').innerHTML = cortes.map(c => `
      <tr>
        <td>${esc(c.cajero ?? '—')}</td><td>${c.caja ?? '—'}</td>
        <td>${formatearFecha(c.apertura)}</td><td>${c.cierre ? formatearFecha(c.cierre) : '—'}</td>
        <td>${dinero(c.monto_inicial)}</td><td>${c.num_ventas} · ${dinero(c.total)}</td>
        <td>${dinero(c.efectivo_esperado)}</td><td>${c.efectivo_contado === null ? '—' : dinero(c.efectivo_contado)}</td>
        <td>${celdaDiferencia(c)}</td>
      </tr>`).join('') || `<tr><td colspan="9" style="color:var(--muted-light);">${t('sinCortes')}</td></tr>`;
    renderPaginacionCortes();
  }

  /* ---------- REGISTRO DE CAJAS ---------- */
  function renderCajas() {
    document.getElementById('tablaCajas').innerHTML = cajas.map(c => `
      <tr>
        <td>${t('thCaja')} ${c.numero}</td>
        <td><button class="btn btn-sm ${c.activa ? 'btn-exito' : 'btn-peligro'}" onclick="toggleCaja(${c.id})" title="${c.activa ? t('btnDesactivar') : t('btnActivar')}" aria-label="${c.activa ? t('btnDesactivar') : t('btnActivar')}">${ICONO_POWER}</button></td>
        <td>${c.en_turno ? `<span class="badge badge-tarjeta">${t('badgeAbierta')}</span> ${esc(c.cajero ?? '')}` : t('libre')}</td>
        <td><button class="btn btn-peligro btn-sm" onclick="eliminarCaja(${c.id})" title="${t('btnEliminar')}" aria-label="${t('btnEliminar')}">${ICONO_BASURA}</button></td>
      </tr>`).join('') || `<tr><td colspan="4" style="color:var(--muted-light);">${t('sinCajas')}</td></tr>`;
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
    llenarCategoriaControles();
    const filtroCat = document.getElementById('filtroCategoria').value;
    document.getElementById('tablaProductos').innerHTML = productos.filter(p => !filtroCat || (filtroCat === '__sin__' ? !p.categoria : p.categoria === filtroCat)).map(p => `
      <tr>
        <td>
          ${p.foto
            ? `<img src="${esc(p.foto)}" alt="${esc(p.nombre)}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">`
            : `<span style="color:var(--muted-light);font-size:0.75rem;">—</span>`}
        </td>
        <td>${esc(p.nombre)}</td>
        <td><input type="text" class="cat-input" id="cat-${p.id}" list="listaCategorias" maxlength="40" value="${escCat(p.categoria || '')}" placeholder="${t('sinCategoria')}"></td>
        <td><input type="number" id="precio-${p.id}" value="${p.precio}" step="0.01"></td>
        <td><input type="number" id="iva-${p.id}" value="${p.iva}" step="0.01"></td>
        <td id="final-${p.id}">${dinero((p.precio * (1 + p.iva / 100)))}</td>
        <td><button class="btn btn-sm ${p.activo ? 'btn-exito' : 'btn-peligro'}" onclick="toggleActivo(${p.id})" title="${p.activo ? t('btnDarBaja') : t('btnReactivar')}" aria-label="${p.activo ? t('btnDarBaja') : t('btnReactivar')}">${ICONO_POWER}</button></td>
        <td><button class="btn btn-primario btn-sm" onclick="guardarProducto(${p.id})" title="${t('btnGuardar')}" aria-label="${t('btnGuardar')}">${ICONO_GUARDAR}</button></td>
        <td>
          <label class="btn btn-sm" style="background:var(--accent-bg-light);color:var(--accent-hover);cursor:pointer;" title="${t('labelFotoProducto')}">
            <svg class="icono-foto" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 8h.01"></path><path d="m3 16 5-5c.928-.893 2.072-.893 3 0l4 4"></path><path d="m14 14 1-1c.928-.893 2.072-.893 3 0l3 3"></path><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M19 2v4"></path><path d="M17 4h4"></path></svg>
            <input type="file" accept="image/*" style="display:none" onchange="subirFoto(${p.id}, this)">
          </label>
        </td>
      </tr>`).join('');
  }

  async function subirFoto(id, inputEl) {
    if (!inputEl.files[0]) return;
    const formData = new FormData();
    formData.append('foto', inputEl.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    try {
      const resp = await fetch('/gerente/productos/' + id + '/foto', { method: 'POST', body: formData });
      const data = await resp.json();
      if (!resp.ok) throw new Error(data?.message || t('errorGenerico'));
      const p = productos.find(x => x.id === id);
      if (p) p.foto = data.foto;
      renderProductos();
      mostrarToast(t('fotoActualizada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  async function guardarProducto(id) {
    const precio = parseFloat(document.getElementById('precio-' + id).value);
    const iva = parseFloat(document.getElementById('iva-' + id).value);
    const categoria = normalizarCategoria(document.getElementById('cat-' + id).value);

    try {
      await api('/gerente/productos/' + id, 'PUT', { precio, iva, categoria });
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
      const categoria = normalizarCategoria(document.getElementById('nuevoCategoria').value);
      const producto = await api('/gerente/productos', 'POST', { nombre, precio, iva, stock, categoria });
      // Sube la foto si el gerente eligió una al crear el producto
      const fotoInput = document.getElementById('nuevaFoto');
      if (fotoInput.files[0]) {
        await subirFoto(producto.id_producto, fotoInput);
      }
      document.getElementById('nuevoNombre').value = '';
      document.getElementById('nuevoPrecio').value = '';
      document.getElementById('nuevoStock').value = '';
      document.getElementById('nuevoCategoria').value = '';
      fotoInput.value = '';
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
    return `<span class="badge badge-completada">${t('estadoCompletada')}</span>`;
  }

  function renderHistorial() {
    // El servidor ya las manda de la más reciente a la más antigua
    document.getElementById('tablaHistorial').innerHTML = historial.map(v => {
      const efectivo = v.metodo.toLowerCase() === 'efectivo';
      const abierta = ventaAbierta === v.folio;
      return `
      <tr>
        <td>#${v.folio}</td><td>${formatearFecha(v.fecha)}</td><td>${esc(v.cajero)}</td><td>${v.caja ?? '—'}</td>
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
        <td>${esc(i.producto)}</td><td>${i.cantidad}</td><td>${i.devuelto}</td><td>${dinero(i.precio_unitario)}</td>
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
          <td>${esc(d.producto ?? '—')} × ${d.cantidad}</td><td>${dinero(d.monto)}</td>
          <td>${esc(d.motivo)}</td><td>${esc(d.usuario ?? '')}</td>
        </tr>`).join('')}</table>` : '';

    const acciones = cancelada
      ? `<p class="desc">${t('canceladaPor')}: ${esc(v.motivo_cancelacion ?? '—')}</p>`
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