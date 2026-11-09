<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora - Punto de Venta</title>
@include('partials.favicon')
<script src="{{ asset('js/theme.js') }}"></script>
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
    --accent: #ea580c;
    --accent-hover: #c2410c;
    --accent-bg-light: #fff1e6;
    --sidebar-hover: #f4f5f7;
    --oscuro-btn-bg: #1f2933;
    --oscuro-btn-hover: #111827;
    --disabled-bg: #d1d5db;
    --danger-bg: #fee2e2;
    --danger-text: #dc2626;
    --danger-hover: #fecaca;
    --success-bg: #ffedd5;
    --success-text: #c2410c;
    --success-hover: #fed7aa;
    --badge-inactivo-bg: #f3f4f6;
    --badge-inactivo-text: #9ca3af;
    --shadow: rgba(0,0,0,0.06);
    --login-bg: #fff3e8;
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
    --accent: #fb923c;
    --accent-hover: #fdba74;
    --accent-bg-light: #3a2210;
    --sidebar-hover: #232c3d;
    --oscuro-btn-bg: #374151;
    --oscuro-btn-hover: #4b5563;
    --disabled-bg: #374151;
    --danger-bg: #3f1d1d;
    --danger-text: #f87171;
    --danger-hover: #522525;
    --success-bg: #3b2410;
    --success-text: #fb923c;
    --success-hover: #4d2f14;
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
  .producto-card .foto-producto { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; margin-bottom: 6px; }
  .producto-card .foto-placeholder { width: 64px; height: 64px; background: var(--border-light); border-radius: 8px; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
  .chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0 0 1rem; }
  .chip { padding: 0.35rem 0.85rem; border: 1px solid var(--border, #d1d5db); border-radius: 999px; background: transparent; color: var(--text, inherit); font-size: 0.82rem; cursor: pointer; }
  .chip:hover { border-color: var(--accent); color: var(--accent); }
  .chip.activo { background: var(--accent); border-color: var(--accent); color: #fff; }
  .producto-card .cat { font-size: 0.66rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted-light, #9ca3af); margin-bottom: 0.15rem; }
  .producto-card .nombre { font-size: 0.9rem; font-weight: 600; margin-bottom: 4px; }
  .producto-card .precio { color: var(--accent); font-weight: 700; margin-bottom: 4px; }
  .producto-card .stock { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.6rem; }
  .producto-card button { width: 100%; padding: 0.5rem; background: var(--accent); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
  .producto-card button:hover { background: var(--accent-hover); }
  .producto-card button:disabled { background: var(--disabled-bg); cursor: not-allowed; }

  .carrito { background: var(--card-bg); border-radius: 12px; padding: 1.25rem; height: fit-content; }
  .carrito-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border-light); font-size: 0.85rem; }
  .carrito-item .quitar { color: var(--danger-text); cursor: pointer; margin-left: 8px; }
  .vacio { font-size: 0.85rem; color: var(--muted-light); padding: 0.5rem 0; }
  .total-box #total { margin-left: 0.4rem; }
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

  .btn-cobrar { width: 100%; margin-top: 1rem; padding: 0.85rem; background: #ea580c; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; }
  .btn-cobrar:hover { background: #c2410c; }
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

  /* ---------- CORTE DE CAJA (mismos estilos que el panel de Gerente) ---------- */
  .resumen-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
  .resumen-card { background: var(--card-bg); border-radius: 12px; padding: 1rem; box-shadow: 0 1px 4px var(--shadow); }
  .resumen-card .label { font-size: 0.78rem; color: var(--muted); margin-bottom: 4px; }
  .resumen-card .valor { font-size: 1.4rem; font-weight: 700; }
  .diferencia-row { display: flex; justify-content: space-between; padding: 0.6rem 0; font-size: 0.95rem; }
  .diferencia-row.sobrante { color: var(--success-text); font-weight: 700; }
  .diferencia-row.faltante { color: var(--danger-text); font-weight: 700; }
  .diferencia-row.cuadrado { color: var(--muted); font-weight: 700; }

  /* ---------- TOASTS (mismos estilos que el panel de Gerente) ---------- */
  #toastContainer { position: fixed; top: 1.25rem; right: 1.25rem; z-index: 1002; display: flex; flex-direction: column; gap: 0.6rem; }
  .toast { padding: 0.8rem 1.1rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; box-shadow: 0 4px 16px var(--shadow); min-width: 220px; animation: toastIn 0.25s ease-out; }
  .toast.exito { background: var(--success-bg); color: var(--success-text); }
  .toast.error { background: var(--danger-bg); color: var(--danger-text); }
  @keyframes toastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
  .toast.saliendo { animation: toastOut 0.2s ease-in forwards; }
  @keyframes toastOut { to { opacity: 0; transform: translateX(20px); } }

  /* ---------- MODAL DE CONFIRMACIÓN (mismos estilos que el panel de Gerente) ---------- */
  #modalOverlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); align-items: center; justify-content: center; z-index: 1001; }
  #modalOverlay.activo { display: flex; }
  .modal-box { background: var(--card-bg); color: var(--text); border-radius: 14px; padding: 1.5rem; width: 320px; box-shadow: 0 10px 40px var(--shadow); }
  .modal-box p { font-size: 0.9rem; margin-bottom: 1.25rem; }
  .modal-acciones { display: flex; justify-content: flex-end; gap: 0.6rem; }

  .ico { width: 1.15em; height: 1.15em; vertical-align: -0.2em; margin-right: 6px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

  .btn-solo-icono .ico { margin-right: 0; width: 1.5em; height: 1.5em; vertical-align: middle; }

  @media print {
    body * { visibility: hidden; }
    #ticketContenido, #ticketContenido * { visibility: visible; }
    .ticket-overlay { position: absolute; inset: auto; top: 0; left: 0; width: 100%; background: none; padding: 0; }
    .ticket { box-shadow: none; margin: 0 auto; width: 100%; max-width: 300px; }
    .ticket-botones { display: none; }
  }

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
  .cfg-campo input, .cfg-campo select, .cfg-fila select, .cfg-fila input[type="number"] { padding: 0.55rem 0.7rem; border: 1px solid var(--border, #d1d5db); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text, inherit); font-size: 0.9rem; font-family: inherit; }
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
  .cfg-fila input[type="number"] { width: 110px; }
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

<div class="pos-view" id="posView">
  <div class="topbar">
    <div class="brand">Nexora <span class="sucursal">— <span data-i18n="topbarPanelCajero">Punto de venta</span> · {{ auth()->user()->sucursal->nombre ?? __('Sin sucursal asignada') }}</span></div>
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
      <div class="seccion" id="sec-configuracion"></div>
      <div class="seccion" id="sec-vender">
        <!-- Sin turno abierto: primero se elige caja y fondo inicial -->
        <div class="card" id="aperturaBox" style="display:none">
          <h3 data-i18n="aperturaHeading">Abrir caja</h3>
          <p class="desc" data-i18n="aperturaDesc">Elige tu caja y cuenta el fondo inicial antes de empezar a vender.</p>
          <div class="fila-form">
            <div class="campo"><label data-i18n="labelCaja">Caja</label><select id="aperturaCaja"></select></div>
            <div class="campo"><label data-i18n="fondoInicialLabel">Fondo inicial de caja</label><input type="number" id="aperturaFondo" min="0" step="0.01" value="500"></div>
            <button class="btn btn-primario btn-solo-icono" onclick="abrirTurno()" data-i18n="btnAbrirCaja">Abrir caja</button>
          </div>
        </div>

        <div class="main" id="ventaBox" style="display:none">
          <div>
            <h2 data-i18n="productosHeading">Productos</h2>
            <p class="desc" id="turnoInfo"></p>
            <div class="fila-form">
              <div class="campo"><input type="search" id="buscarProducto" data-i18n-placeholder="placeholderBuscar" placeholder="Buscar producto..." oninput="renderProductos()"></div>
            </div>
            <div class="chips" id="chipsCategorias"></div>
            <div class="productos-grid" id="productosGrid"></div>
          </div>
          <div class="carrito">
            <h2 data-i18n="carritoHeading">Carrito</h2>
            <div id="carritoItems"></div>
            <div class="total-box"><span data-i18n="totalLabel">Total: $</span><span id="total">$0.00 MXN</span></div>

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

      <!-- ---------- CORTE DE CAJA ---------- -->
      <div class="seccion" id="sec-corte">
        <h2 data-i18n="corteHeading">Corte de caja</h2>
        <p class="desc" data-i18n="corteDesc">Compara lo vendido en tu turno contra el efectivo físico contado.</p>

        <p class="desc" id="corteSinTurno" style="display:none" data-i18n="corteSinTurno">No tienes un turno abierto.</p>

        <div id="corteContenido" style="display:none">
          <div class="resumen-grid">
            <div class="resumen-card"><div class="label" data-i18n="ventasEfectivo">Ventas en efectivo</div><div class="valor" id="resEfectivo">$0.00 MXN</div></div>
            <div class="resumen-card"><div class="label" data-i18n="ventasTarjeta">Ventas con tarjeta</div><div class="valor" id="resTarjeta">$0.00 MXN</div></div>
            <div class="resumen-card"><div class="label" data-i18n="totalTurno">Total del turno</div><div class="valor" id="resTotal">$0.00 MXN</div></div>
          </div>
          <div class="card">
            <table>
              <tr><td data-i18n="labelCaja">Caja</td><td id="corteCaja">—</td></tr>
              <tr><td data-i18n="labelApertura">Apertura</td><td id="corteApertura">—</td></tr>
              <tr><td data-i18n="fondoInicialLabel">Fondo inicial de caja</td><td id="corteFondo">$0.00 MXN</td></tr>
              <tr><td data-i18n="labelNumVentas">Ventas realizadas</td><td id="corteNumVentas">0</td></tr>
              <tr><td data-i18n="labelDevoluciones">Devoluciones / cancelaciones</td><td id="corteDevoluciones">$0.00 MXN</td></tr>
            </table>
          </div>
          <div class="card">
            <div class="fila-form">
              <div class="campo"><label data-i18n="efectivoContadoLabel">Efectivo contado físicamente</label><input type="number" id="efectivoContado" min="0" step="0.01" placeholder="0.00" oninput="calcularCorte()"></div>
              <button class="btn btn-peligro btn-solo-icono" onclick="cerrarTurno()" data-i18n="btnCerrarCaja">Cerrar caja y hacer corte</button>
            </div>
            <div id="diferenciaCorte"></div>
          </div>
        </div>
      </div>

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
            <tr><th data-i18n="repThFecha">Fecha</th><th data-i18n="repThTipo">Tipo</th><th data-i18n="repThAsunto">Asunto</th><th data-i18n="repThEstado">Estado</th><th data-i18n="repThRespuesta">Respuesta</th></tr>
            <tbody id="tablaReportes"></tbody>
          </table>
        </div>
      </div>

      <!-- ---------- REGISTRAR COMPRAS (oculta: es función del Gerente) ---------- -->
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

      <!-- ---------- PRODUCTOS (ALTA / BAJA) (oculta: es función del Gerente) ---------- -->
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
  /* Escapa texto que viene de la BD antes de meterlo en innerHTML (evita XSS: un nombre como
     <img onerror=...> se muestra como texto y no se ejecuta). function = se puede usar en todo el script. */
  function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
  }

  /* ---------- DATOS (declarados antes de cualquier función que los use) ---------- */
  let categoriaSel = '';     // '' = todas, '__sin__' = productos sin categoría
  let opcionesChips = [];
  let productos = [];        // { id, nombre, precio (con IVA), stock } de la sucursal
  let carrito = [];
  let compras = [];          // solo lo usa la sección oculta "Registrar compras"
  let metodoPago = 'efectivo';
  let metodosPago = {};      // { efectivo: id_metodo_pago, tarjeta: id_metodo_pago } desde la BD
  let turno = null;          // turno abierto (corte_caja) o null
  let cajasLibres = [];
  let resumenTurno = null;
  let cobrando = false;      // evita registrar la venta dos veces con doble clic
  let estadoCargado = false; // hasta que responda /cajero/estado no se muestra apertura ni venta
  let reportes = [];         // reportes que este cajero envió al administrador

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

  const dinero = n => '$' + Number(n).toFixed(2) + ' MXN';

  /* ---------- IDIOMA ---------- */
  const textos = {
    es: {
      navConfiguracion: 'Configuración',
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
      totalLabel: 'Total:',
      carritoVacio: 'Carrito vacío',
      metodoPagoHeading: 'Método de pago',
      btnEfectivo: 'Efectivo',
      btnTarjeta: 'Tarjeta',
      efectivoRecibidoLabel: 'Efectivo recibido (MXN)',
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
      labelCostoUnitario: 'Costo unitario (MXN)',
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
      topbarPanelCajero: 'Punto de venta', navCorte: 'Corte de caja',
      errorGenerico: 'Ocurrió un error. Intenta de nuevo.', errorCargar: 'No se pudo cargar la información del punto de venta.',
      placeholderBuscar: 'Buscar producto...', todasCategorias: 'Todas', sinCategoria: 'Sin categoría', sinResultados: 'Sin productos que coincidan.',
      aperturaHeading: 'Abrir caja', aperturaDesc: 'Elige tu caja y cuenta el fondo inicial antes de empezar a vender.',
      labelCaja: 'Caja', fondoInicialLabel: 'Fondo inicial de caja', btnAbrirCaja: 'Abrir caja',
      sinCajasLibres: 'No hay cajas disponibles. Pide al gerente que registre o libere una.',
      turnoAbierto: 'Caja abierta', cajaAbierta: 'Caja abierta. ¡Listo para vender!',
      faltanMetodos: 'Faltan los métodos de pago en la base de datos (ejecuta php artisan migrate).',
      ventaRegistrada: 'Venta registrada',
      corteHeading: 'Corte de caja', corteDesc: 'Compara lo vendido en tu turno contra el efectivo físico contado.',
      corteSinTurno: 'No tienes un turno abierto.', ventasEfectivo: 'Ventas en efectivo', ventasTarjeta: 'Ventas con tarjeta',
      totalTurno: 'Total del turno', labelApertura: 'Apertura', labelNumVentas: 'Ventas realizadas',
      labelDevoluciones: 'Devoluciones / cancelaciones',
      efectivoContadoLabel: 'Efectivo contado físicamente (MXN)', btnCerrarCaja: 'Cerrar caja y hacer corte',
      efectivoEsperado: 'Efectivo esperado en caja', sobrante: 'Sobrante', faltante: 'Faltante', cajaCuadrada: 'Caja cuadrada',
      alertEfectivoContado: 'Ingresa el efectivo contado.', confirmCerrarCaja: '¿Cerrar tu caja? Después ya no podrás vender en este turno.',
      btnCerrarConfirm: 'Cerrar caja', corteRealizado: 'Corte realizado. Caja cerrada.',
      ticketSubtotal: 'Subtotal', ticketIva: 'IVA', ticketCajero: 'Cajero',
      btnCancelar: 'Cancelar', btnEliminar: 'Eliminar',
      navReportes: 'Reportes', repHeading: 'Reportes al administrador', repNuevoHeading: 'Nuevo reporte', repListaHeading: 'Mis reportes',
      repDesc: 'Avisa al administrador de una falla, un faltante de producto, un problema con la caja o una sugerencia.',
      repThFecha: 'Fecha', repThEstado: 'Estado',
      repLabelTipo: 'Tipo', repLabelAsunto: 'Asunto', repLabelDescripcion: 'Descripción',
      repPhAsunto: 'Ej. La impresora no imprime tickets', repPhDescripcion: 'Cuéntale al administrador qué pasó, cuándo y en qué caja o producto…',
      repBtnEnviar: 'Enviar reporte', repThAutor: 'Enviado por', repThTipo: 'Tipo', repThAsunto: 'Asunto', repThRespuesta: 'Respuesta del administrador',
      repSinReportes: 'Aún no hay reportes.', repSinRespuesta: 'Sin respuesta todavía', repTu: 'Tú', repUsuarioEliminado: 'Usuario eliminado',
      repTipo_falla: 'Falla del sistema', repTipo_faltante: 'Faltante de producto', repTipo_caja: 'Problema con caja / dinero', repTipo_sugerencia: 'Sugerencia', repTipo_otro: 'Otro',
      repEstado_pendiente: 'Pendiente', repEstado_en_revision: 'En revisión', repEstado_resuelto: 'Resuelto',
      repRol_gerente: 'Gerente', repRol_cajero: 'Cajero',
      repEnviado: 'Reporte enviado al administrador', repAlertCompleta: 'Escribe el asunto y la descripción.',
    },
    en: {
      navConfiguracion: 'Settings',
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
      totalLabel: 'Total:',
      carritoVacio: 'Cart is empty',
      metodoPagoHeading: 'Payment method',
      btnEfectivo: 'Cash',
      btnTarjeta: 'Card',
      efectivoRecibidoLabel: 'Cash received (MXN)',
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
      labelCostoUnitario: 'Unit cost (MXN)',
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
      topbarPanelCajero: 'Point of sale', navCorte: 'Cash count',
      errorGenerico: 'Something went wrong. Please try again.', errorCargar: 'Could not load the point of sale information.',
      placeholderBuscar: 'Search product...', todasCategorias: 'All', sinCategoria: 'No category', sinResultados: 'No matching products.',
      aperturaHeading: 'Open register', aperturaDesc: 'Choose your register and count the starting cash before selling.',
      labelCaja: 'Register', fondoInicialLabel: 'Starting cash fund', btnAbrirCaja: 'Open register',
      sinCajasLibres: 'No registers available. Ask your manager to add or free one.',
      turnoAbierto: 'Register open', cajaAbierta: 'Register open. Ready to sell!',
      faltanMetodos: 'Payment methods are missing in the database (run php artisan migrate).',
      ventaRegistrada: 'Sale recorded',
      corteHeading: 'Cash count', corteDesc: 'Compare your shift sales against the physical cash counted.',
      corteSinTurno: 'You have no open shift.', ventasEfectivo: 'Cash sales', ventasTarjeta: 'Card sales',
      totalTurno: 'Shift total', labelApertura: 'Opened', labelNumVentas: 'Sales made',
      labelDevoluciones: 'Returns / cancellations',
      efectivoContadoLabel: 'Physical cash counted (MXN)', btnCerrarCaja: 'Close register and count',
      efectivoEsperado: 'Expected cash in register', sobrante: 'Overage', faltante: 'Shortage', cajaCuadrada: 'Register balanced',
      alertEfectivoContado: 'Enter the cash counted.', confirmCerrarCaja: 'Close your register? You will not be able to sell in this shift afterwards.',
      btnCerrarConfirm: 'Close register', corteRealizado: 'Cash count done. Register closed.',
      ticketSubtotal: 'Subtotal', ticketIva: 'Tax', ticketCajero: 'Cashier',
      btnCancelar: 'Cancel', btnEliminar: 'Delete',
      navReportes: 'Reports', repHeading: 'Reports to the administrator', repNuevoHeading: 'New report', repListaHeading: 'My reports',
      repDesc: 'Let the administrator know about a failure, a missing product, a register problem or a suggestion.',
      repThFecha: 'Date', repThEstado: 'Status',
      repLabelTipo: 'Type', repLabelAsunto: 'Subject', repLabelDescripcion: 'Description',
      repPhAsunto: 'E.g. The printer does not print receipts', repPhDescripcion: 'Tell the administrator what happened, when, and which register or product…',
      repBtnEnviar: 'Send report', repThAutor: 'Sent by', repThTipo: 'Type', repThAsunto: 'Subject', repThRespuesta: "Administrator's reply",
      repSinReportes: 'No reports yet.', repSinRespuesta: 'No reply yet', repTu: 'You', repUsuarioEliminado: 'Deleted user',
      repTipo_falla: 'System failure', repTipo_faltante: 'Missing product', repTipo_caja: 'Register / cash problem', repTipo_sugerencia: 'Suggestion', repTipo_otro: 'Other',
      repEstado_pendiente: 'Pending', repEstado_en_revision: 'In review', repEstado_resuelto: 'Resolved',
      repRol_gerente: 'Manager', repRol_cajero: 'Cashier',
      repEnviado: 'Report sent to the administrator', repAlertCompleta: 'Write the subject and the description.',
    }
  };
  let idioma = '{{ app()->getLocale() }}';
  function t(clave) { return textos[idioma][clave] || clave; }

  /* ---------- ICONOS SVG ---------- */
  const svg = d => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${d}</svg>`;
  const CARRITO = '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>';
  const ICONOS = {
    navConfiguracion: svg('<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>'),
    carritoHeading: svg(CARRITO),
    navVender: svg(CARRITO),
    navCorte: svg('<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>'),
    metodoPagoHeading: svg('<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>'),
    btnEfectivo: svg('<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>'),
    btnTarjeta: svg('<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>'),
    btnCerrarCaja: svg('<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.12 15.88"/><path d="M14.47 14.48 20 20"/><path d="M8.12 8.12 12 12"/>'),
    btnAbrirCaja: svg('<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/>'),
    agregarBtn: svg(CARRITO + '<path d="M13 6v5M10.5 8.5h5"/>'),
    navReportes: svg('<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M12 7v2"/><path d="M12 13h.01"/>'),
  };


  /* ================= CONFIGURACIÓN DE LA CUENTA ================= */
  const CFG_ROL = 'cajero';
  const CFG_ETIQUETA = { es: 'Cajero', en: 'Cashier' };
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
    "prefSub": "Se guardan en este dispositivo.",
    "prefGuardada": "Preferencia guardada",
    "errorGuardar": "No se pudo guardar. Inténtalo de nuevo.",
    "dosPasosTitulo": "Verificación en dos pasos",
    "dosPasosSub": "Agrega una capa extra de seguridad al iniciar sesión.",
    "dosPasosActiva": "Activada",
    "dosPasosInactiva": "Desactivada",
    "dosPasosHint": "Te pedirá un código extra, además de tu contraseña.",
    "prefTitulo": "Preferencias del cajero",
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
    "prefSub": "They are saved on this device.",
    "prefGuardada": "Preference saved",
    "errorGuardar": "Could not save. Please try again.",
    "dosPasosTitulo": "Two-step verification",
    "dosPasosSub": "Add an extra layer of security when you sign in.",
    "dosPasosActiva": "Enabled",
    "dosPasosInactiva": "Disabled",
    "dosPasosHint": "It will ask for an extra code, besides your password.",
    "prefTitulo": "Cashier preferences",
    "correoHint": "Your email is your username; ask the administrator to change it."
  }
};
  const ct = k => (CFG_TEXTOS[idioma] || CFG_TEXTOS.es)[k] ?? k;
  const CFG_PREFS = [];
  const CFG_CLAVE = 'nexora-pref-' + CFG_ROL;
  const cfgEsc = s => String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  const cfgIcono = d => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${d}</svg>`;
  const CFG_OJO = '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>';
  const CFG_OJO_NO = '<path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/>';
  const CFG_SOL = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>';
  const CFG_LUNA = '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>';

  /* --- preferencias del rol (se guardan en este dispositivo) --- */
  function cfgLeerPrefs() { try { return JSON.parse(localStorage.getItem(CFG_CLAVE)) || {}; } catch (e) { return {}; } }
  function cfgPref(id) {
    const guardadas = cfgLeerPrefs();
    return id in guardadas ? guardadas[id] : CFG_PREFS.find(p => p.id === id).defecto;
  }
  function cfgGuardarPref(id, valor) {
    const guardadas = cfgLeerPrefs();
    guardadas[id] = valor;
    try { localStorage.setItem(CFG_CLAVE, JSON.stringify(guardadas)); } catch (e) {}
    cfgAplicarPrefs();
    mostrarToast(ct('prefGuardada'), 'exito');
  }

  function cfgAplicarPrefs() {}
  function cfgIniciar() {}

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

  // En el panel real los enlaces de idioma llevan a la ruta de Laravel; si el enlace es "#" (demo) se cambia en la página.
  document.addEventListener('click', e => {
    const a = e.target.closest('a[data-lang]');
    if (a && a.getAttribute('href') === '#') { e.preventDefault(); idioma = a.dataset.lang; aplicarIdioma(); }
  });


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
  function cfgFilaPref(p) {
    let control = '';
    if (p.tipo === 'switch') {
      control = `<label class="cfg-switch"><input type="checkbox" ${cfgPref(p.id) ? 'checked' : ''} onchange="cfgGuardarPref('${p.id}', this.checked)"><span></span></label>`;
    } else if (p.tipo === 'select') {
      const actual = String(cfgPref(p.id));
      control = `<select onchange="cfgGuardarPref('${p.id}', this.value)">${p.opciones().map(o => `<option value="${cfgEsc(o.valor)}" ${String(o.valor) === actual ? 'selected' : ''}>${cfgEsc(o.texto)}</option>`).join('')}</select>`;
    } else {
      control = `<input type="number" min="${p.min ?? 0}" step="${p.step ?? 1}" value="${cfgEsc(cfgPref(p.id))}" onchange="cfgGuardarPref('${p.id}', parseFloat(this.value) || 0)">`;
    }
    return `<div class="cfg-fila"><div><b>${ct('pref_' + p.id)}</b><small>${ct('prefHint_' + p.id)}</small></div>${control}</div>`;
  }

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
          <div class="cfg-perfil"><div class="cfg-avatar" id="cfgAvatar">${cfgEsc(cfgIniciales())}</div>
            <div><b id="cfgNombreCompleto">${cfgEsc((cfgPerfil.nombre + ' ' + cfgPerfil.apellido).trim())}</b><span>${cfgEsc(rolTexto)}${cfgPerfil.sucursal ? ' · ' + cfgEsc(cfgPerfil.sucursal) : ''}</span></div></div>
          <div class="cfg-form">
            <div class="cfg-campo"><label for="cfgNombre">${ct('lblNombre')}</label><input type="text" id="cfgNombre" maxlength="80" value="${cfgEsc(cfgPerfil.nombre)}"></div>
            <div class="cfg-campo"><label for="cfgApellido">${ct('lblApellido')}</label><input type="text" id="cfgApellido" maxlength="80" value="${cfgEsc(cfgPerfil.apellido)}"></div>
            <div class="cfg-campo"><label for="cfgCorreo">${ct('lblCorreo')}</label><input type="email" id="cfgCorreo" value="${cfgEsc(cfgPerfil.correo)}" disabled><small>${ct('correoHint')}</small></div>
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

        ${CFG_PREFS.length ? `<div class="cfg-card">
          <h3>${ct('prefTitulo')}</h3><p class="cfg-sub">${ct('prefSub')}</p>
          ${CFG_PREFS.map(cfgFilaPref).join('')}
        </div>` : ''}
      </div>`;
    cfgMarcarTema();
  }

  const secciones = [
    { id: 'vender', key: 'navVender' },
    { id: 'corte', key: 'navCorte' },
    { id: 'reportes', key: 'navReportes' },
    // Compras y alta/baja de productos son funciones del Gerente; HTML/JS se conservan
    // { id: 'compras', key: 'navCompras' },
    // { id: 'productos', key: 'navProductos' },
  ];
  secciones.push({ id: 'configuracion', key: 'navConfiguracion' });
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
  // El idioma lo decide el servidor (sesión/cookie) y llega en `idioma`; NO se usa localStorage.
  // El cambio de idioma es el enlace del topbar (lang.switch), que recarga la página.
  function aplicarIdioma() {
    document.querySelectorAll('[data-i18n]').forEach(el => { const k = el.getAttribute('data-i18n'); if (k === 'btnCerrarCaja' || k === 'btnAbrirCaja') { el.innerHTML = ICONOS[k]; el.title = t(k); el.setAttribute('aria-label', t(k)); return; }
    el.innerHTML = (ICONOS[k] || '') + t(k); });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.getAttribute('data-i18n-placeholder')); });
    document.documentElement.lang = idioma;
    renderSidebar();
    mostrarSeccion(seccionActual, true);
  }

  aplicarIdioma();

  function cerrarSesion() {
    carrito = [];
    document.getElementById('logoutForm').submit();
  }

  cargarEstado();

  /* ---------- NAVEGACIÓN ---------- */
  function renderSidebar() {
    const sb = document.getElementById('sidebar');
    sb.innerHTML = secciones.map(s => `<button id="btn-${s.id}" onclick="mostrarSeccion('${s.id}')">${(ICONOS[s.key] || '') + t(s.key)}</button>`).join('');
    secciones.forEach(s => document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === seccionActual));
  }

  function mostrarSeccion(id, soloRefrescar) {
    seccionActual = id;
    if (id === 'configuracion') renderConfiguracion();
    secciones.forEach(s => {
      document.getElementById('sec-' + s.id).classList.toggle('activa', s.id === id);
      document.getElementById('btn-' + s.id).classList.toggle('activo', s.id === id);
    });
    if (id === 'vender') renderVender();
    if (id === 'corte') { renderCorte(); if (!soloRefrescar) cargarResumen(); }
    if (id === 'reportes') { renderReportes(); if (!soloRefrescar) cargarReportes().then(renderReportes); }
    if (id === 'compras') renderCompras();
    if (id === 'productos') renderProductosAdmin();
  }

  /* ---------- REPORTES AL ADMINISTRADOR ---------- */
  const TIPOS_REPORTE = ['falla', 'faltante', 'caja', 'sugerencia', 'otro'];

  async function cargarReportes() {
    try { reportes = await api('/cajero/reportes'); }
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
          <td>${t('repTipo_' + r.tipo)}</td>
          <td><strong>${esc(r.asunto)}</strong><div class="rep-desc">${esc(r.descripcion)}</div></td>
          <td><span class="badge rep-${r.estado}">${t('repEstado_' + r.estado)}</span></td>
          <td>${r.respuesta ? `${esc(r.respuesta)}<div class="rep-desc">${formatearFecha(r.fecha_respuesta)}</div>` : `<span class="rep-desc">${t('repSinRespuesta')}</span>`}</td>
        </tr>`).join('')
      : `<tr><td colspan="5" style="color:var(--muted);">${t('repSinReportes')}</td></tr>`;
  }

  async function enviarReporte() {
    const tipo = document.getElementById('repTipo').value;
    const asunto = document.getElementById('repAsunto').value.trim();
    const descripcion = document.getElementById('repDescripcion').value.trim();
    if (!asunto || !descripcion) { mostrarToast(t('repAlertCompleta'), 'error'); return; }

    const btn = document.getElementById('btnEnviarReporte');
    btn.disabled = true;   // evita mandar el mismo reporte dos veces con doble clic
    try {
      await api('/cajero/reportes', 'POST', { tipo, asunto, descripcion });
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

  /* ---------- TURNO (apertura de caja) ---------- */
  async function cargarEstado() {
    try {
      const estado = await api('/cajero/estado');
      turno = estado.turno;
      cajasLibres = estado.cajas;
      metodosPago = {};
      estado.metodos_pago.forEach(m => { metodosPago[m.nombre.toLowerCase()] = m.id; });
      if (turno) await cargarProductos();
    } catch (e) {
      mostrarToast(e.message || t('errorCargar'), 'error');
    }
    estadoCargado = true;
    mostrarSeccion(seccionActual);
  }

  async function cargarProductos() {
    try { productos = await api('/cajero/productos'); }
    catch (e) { mostrarToast(e.message, 'error'); }
  }

  /* Sin turno: formulario de apertura. Con turno: el punto de venta. */
  function renderVender() {
    document.getElementById('aperturaBox').style.display = estadoCargado && !turno ? 'block' : 'none';
    document.getElementById('ventaBox').style.display = estadoCargado && turno ? 'grid' : 'none';
    if (!estadoCargado) return;

    if (!turno) {
      const select = document.getElementById('aperturaCaja');
      select.innerHTML = cajasLibres.map(c => `<option value="${c.id}">${t('labelCaja')} ${c.numero}</option>`).join('')
        || `<option value="">${t('sinCajasLibres')}</option>`;
      return;
    }

    document.getElementById('turnoInfo').textContent =
      `${t('turnoAbierto')}: ${t('labelCaja')} ${turno.caja} · ${formatearFecha(turno.apertura)}`;
    renderProductos();
    renderCarrito();
  }

  async function abrirTurno() {
    const id_caja = parseInt(document.getElementById('aperturaCaja').value);
    const monto_inicial = parseFloat(document.getElementById('aperturaFondo').value);
    if (!id_caja) { mostrarToast(t('sinCajasLibres'), 'error'); return; }

    try {
      turno = await api('/cajero/turno/abrir', 'POST', { id_caja, monto_inicial: isNaN(monto_inicial) ? 0 : monto_inicial });
      await cargarProductos();
      renderVender();
      mostrarToast(t('cajaAbierta'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
      await cargarEstado(); // la caja pudo haber sido tomada por otro cajero
    }
  }

  /* ---------- VENDER ---------- */
  function elegirMetodo(metodo) {
    metodoPago = metodo;
    document.getElementById('btnEfectivo').classList.toggle('activo', metodo === 'efectivo');
    document.getElementById('btnTarjeta').classList.toggle('activo', metodo === 'tarjeta');
    document.getElementById('efectivoBox').style.display = metodo === 'efectivo' ? 'block' : 'none';
    renderCarrito();
  }

  const escCat = c => String(c).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

  // Botones de categoría: salen de los propios productos (cada negocio escribe las suyas).
  // Si ningún producto tiene categoría, no se muestran.
  function renderChipsCategorias() {
    const cont = document.getElementById('chipsCategorias');
    const cats = [...new Set(productos.map(p => p.categoria).filter(Boolean))].sort((a, b) => a.localeCompare(b, idioma));
    if (!cats.length) { categoriaSel = ''; cont.innerHTML = ''; return; }
    opcionesChips = [{ valor: '', texto: t('todasCategorias') }, ...cats.map(c => ({ valor: c, texto: c }))];
    if (productos.some(p => !p.categoria)) opcionesChips.push({ valor: '__sin__', texto: t('sinCategoria') });
    if (!opcionesChips.some(o => o.valor === categoriaSel)) categoriaSel = '';
    cont.innerHTML = opcionesChips.map((o, i) => `<button class="chip ${o.valor === categoriaSel ? 'activo' : ''}" onclick="seleccionarCategoria(${i})">${escCat(o.texto)}</button>`).join('');
  }

  function seleccionarCategoria(i) {
    categoriaSel = opcionesChips[i].valor;
    renderProductos();
  }

  function renderProductos() {
    const grid = document.getElementById('productosGrid');
    const busqueda = document.getElementById('buscarProducto').value.trim().toLowerCase();
    renderChipsCategorias();
    // El servidor ya manda solo productos activos con inventario en esta sucursal
    grid.innerHTML = productos.filter(p => p.nombre.toLowerCase().includes(busqueda)
      && (!categoriaSel || (categoriaSel === '__sin__' ? !p.categoria : p.categoria === categoriaSel))).map(p => {
      const enCarrito = carrito.find(i => i.id === p.id);
      const disponible = p.stock - (enCarrito ? enCarrito.cantidad : 0);
      const imgHtml = p.foto
        ? `<img class="foto-producto" src="${esc(p.foto)}" alt="${esc(p.nombre)}">`
        : `<div class="foto-placeholder">🛒</div>`;
      return `
        <div class="producto-card">
          ${imgHtml}
          ${p.categoria ? `<div class="cat">${escCat(p.categoria)}</div>` : ''}<div class="nombre">${esc(p.nombre)}</div>
          <div class="precio">${dinero(p.precio)}</div>
          <div class="stock">${t('stockLabel')}${disponible}</div>
          <button onclick="agregarAlCarrito(${p.id})" ${disponible <= 0 ? 'disabled' : ''}>
            ${disponible <= 0 ? t('sinStock') : ICONOS.agregarBtn + t('agregarBtn')}
          </button>
        </div>`;
    }).join('') || `<div class="vacio">${t('sinResultados')}</div>`;
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
            <span>${esc(i.nombre)} x${i.cantidad}</span>
            <span>${dinero((i.precio * i.cantidad))}<span class="quitar" onclick="quitarDelCarrito(${i.id})">✕</span></span>
          </div>`).join('')
      : `<div class="vacio">${t('carritoVacio')}</div>`;

    document.getElementById('total').textContent = dinero(total);

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
        cambioRow.innerHTML = `<span>${t('faltaLabel')}</span><span>${dinero(Math.abs(cambio))}</span>`;
        puedeCobrar = false;
      } else {
        cambioRow.className = 'cambio-row ok';
        cambioRow.innerHTML = `<span>${t('cambioLabel')}</span><span>${dinero(cambio)}</span>`;
      }
    } else {
      cambioRow.innerHTML = '';
    }

    document.getElementById('btnCobrar').disabled = !puedeCobrar || cobrando;
  }

  /*
   * Solo se mandan id_producto + cantidad. El servidor recalcula precios, valida stock,
   * guarda venta + detalle, descuenta inventario y devuelve los datos del ticket.
   */
  async function cobrar() {
    if (carrito.length === 0 || cobrando) return;

    const id_metodo_pago = metodosPago[metodoPago];
    if (!id_metodo_pago) { mostrarToast(t('faltanMetodos'), 'error'); return; }

    const recibido = metodoPago === 'efectivo' ? (parseFloat(document.getElementById('montoRecibido').value) || 0) : null;

    cobrando = true;
    document.getElementById('btnCobrar').disabled = true;
    try {
      const ticket = await api('/cajero/ventas', 'POST', {
        id_metodo_pago,
        items: carrito.map(i => ({ id_producto: i.id, cantidad: i.cantidad })),
        recibido,
      });

      mostrarTicket(ticket);
      carrito = [];
      document.getElementById('montoRecibido').value = '';
      elegirMetodo('efectivo');
      mostrarToast(t('ventaRegistrada'), 'exito');
    } catch (e) {
      mostrarToast(e.message, 'error');
    } finally {
      cobrando = false;
      await cargarProductos(); // stock real después de la venta (o del intento)
      renderProductos();
      renderCarrito();
    }
  }

  /* ---------- TICKET ---------- */
  function mostrarTicket(venta) {
    const filasItems = venta.items.map(i => `
      <tr>
        <td>${esc(i.nombre)}<br><span style="color:var(--muted);font-size:0.72rem;">${i.cantidad} x ${dinero(i.precio)}</span></td>
        <td style="text-align:right;">${dinero((i.precio * i.cantidad))}</td>
      </tr>`).join('');

    const filasPago = venta.metodo === 'efectivo'
      ? `<tr><td>${t('ticketRecibido')}</td><td style="text-align:right;">${dinero(venta.recibido)}</td></tr>
         <tr><td>${t('ticketCambio')}</td><td style="text-align:right;">${dinero(venta.cambio)}</td></tr>`
      : '';

    document.getElementById('ticketContenido').innerHTML = `
      <h2>Nexora</h2>
      <div class="ticket-sub">${esc(venta.sucursal)}<br>${t('ticketFolio')}${venta.folio}<br>${formatearFecha(venta.fecha)}<br>${t('ticketCajero')}: ${esc(venta.cajero)} · ${t('labelCaja')} ${venta.caja}</div>
      <div class="linea"></div>
      <table>${filasItems}</table>
      <div class="linea"></div>
      <table>
        <tr><td>${t('ticketSubtotal')}</td><td style="text-align:right;">${dinero(venta.subtotal)}</td></tr>
        <tr><td>${t('ticketIva')}</td><td style="text-align:right;">${dinero(venta.iva)}</td></tr>
        <tr class="fila-total"><td>${t('ticketTotal')}</td><td style="text-align:right;">${dinero(venta.total)}</td></tr>
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

  /* ---------- CORTE DE CAJA ---------- */
  async function cargarResumen() {
    if (!turno) { resumenTurno = null; renderCorte(); return; }
    try {
      resumenTurno = await api('/cajero/turno/resumen');
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
    renderCorte();
  }

  function renderCorte() {
    const hay = !!(turno && resumenTurno);
    document.getElementById('corteSinTurno').style.display = estadoCargado && !turno ? 'block' : 'none';
    document.getElementById('corteContenido').style.display = hay ? 'block' : 'none';
    if (!hay) return;

    // Netos: lo que el gerente devolvió/canceló de ventas de este turno ya no cuenta
    document.getElementById('resEfectivo').textContent = dinero(resumenTurno.ventas_efectivo - resumenTurno.devoluciones_efectivo);
    document.getElementById('resTarjeta').textContent = dinero(resumenTurno.ventas_tarjeta - resumenTurno.devoluciones_tarjeta);
    document.getElementById('corteDevoluciones').textContent = dinero(resumenTurno.devoluciones_efectivo + resumenTurno.devoluciones_tarjeta);
    document.getElementById('resTotal').textContent = dinero(resumenTurno.total);
    document.getElementById('corteCaja').textContent = resumenTurno.caja;
    document.getElementById('corteApertura').textContent = formatearFecha(resumenTurno.apertura);
    document.getElementById('corteFondo').textContent = dinero(resumenTurno.monto_inicial);
    document.getElementById('corteNumVentas').textContent = resumenTurno.num_ventas;
    calcularCorte();
  }

  /* Vista previa en vivo: fondo inicial + ventas en efectivo vs. lo contado */
  function calcularCorte() {
    if (!resumenTurno) return;
    const esperado = resumenTurno.efectivo_esperado;
    const contado = parseFloat(document.getElementById('efectivoContado').value);
    const div = document.getElementById('diferenciaCorte');
    const filaEsperado = `<div class="diferencia-row"><span>${t('efectivoEsperado')}</span><span>${dinero(esperado)}</span></div>`;

    if (isNaN(contado)) { div.innerHTML = filaEsperado; return; }

    const diferencia = Math.round((contado - esperado) * 100) / 100;
    let clase = 'cuadrado', texto = t('cajaCuadrada');
    if (diferencia > 0) { clase = 'sobrante'; texto = t('sobrante'); }
    if (diferencia < 0) { clase = 'faltante'; texto = t('faltante'); }
    div.innerHTML = filaEsperado + `<div class="diferencia-row ${clase}"><span>${texto}</span><span>${dinero(Math.abs(diferencia))}</span></div>`;
  }

  async function cerrarTurno() {
    const efectivo_contado = parseFloat(document.getElementById('efectivoContado').value);
    if (isNaN(efectivo_contado)) { mostrarToast(t('alertEfectivoContado'), 'error'); return; }
    if (!(await confirmar(t('confirmCerrarCaja'), t('btnCerrarConfirm')))) return;

    try {
      await api('/cajero/turno/cerrar', 'POST', { efectivo_contado });
      document.getElementById('efectivoContado').value = '';
      turno = null;
      resumenTurno = null;
      carrito = [];
      mostrarToast(t('corteRealizado'), 'exito');
      seccionActual = 'vender'; // regresa a "Vender", donde aparece la apertura de caja
      await cargarEstado();      // recarga las cajas libres
    } catch (e) {
      mostrarToast(e.message, 'error');
    }
  }

  /* ---------- REGISTRAR COMPRAS ---------- */
  function renderCompras() {
    const select = document.getElementById('compraProducto');
    select.innerHTML = productos.filter(p => p.activo).map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join('');
    document.getElementById('tablaCompras').innerHTML = compras.slice().reverse().map(c => `
      <tr>
        <td>${c.fecha}</td><td>${esc(c.nombre)}</td><td>${c.cantidad}</td>
        <td>${dinero(c.costo)}</td><td>${dinero((c.costo * c.cantidad))}</td><td>${esc(c.proveedor || '—')}</td>
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
        <td>${esc(p.nombre)}</td>
        <td>${dinero(p.precio)}</td>
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
  cfgIniciar();
</script>

</body>
</html>