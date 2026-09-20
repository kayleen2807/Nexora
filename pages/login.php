<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <main class="login-page">
        <section class="login-shell" aria-label="Inicio de sesión Nexora">
            <aside class="brand-panel">
                <div class="brand-mark" aria-label="Nexora">
                    <div class="logo-badge">
                        <img src="../assets/img/nexora-logo-cropped.png" alt="Nexora - Sistema de punto de venta">
                    </div>
                    <div class="brand-wordmark">
                        <p>NEXORA</p>
                        <span>Sistema de punto de venta</span>
                    </div>
                </div>

                <div class="panel-copy">
                    <p class="eyebrow">Gestión centralizada</p>
                    <h1>Punto de venta para crecer con orden.</h1>
                    <p>Administra ventas, inventario y sucursales desde una experiencia clara y rápida.</p>
                </div>

                <div class="stats-row" aria-label="Beneficios">
                    <span>Ventas</span>
                    <span>Inventario</span>
                    <span>Sucursales</span>
                </div>
            </aside>

            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">Acceso al sistema</p>
                    <h2>Iniciar sesión</h2>
                    <p class="form-intro">Ingresa tus credenciales para entrar al panel de Nexora.</p>

                    <form action="#" method="post">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Usuario" autocomplete="username" required>

                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" autocomplete="current-password" required>

                        <div class="form-options">
                            <label class="remember-option" for="remember">
                                <input type="checkbox" id="remember" name="remember">
                                Recordarme
                            </label>
                            <a href="#">Olvidé mi contraseña</a>
                        </div>

                        <button type="submit">Entrar</button>
                    </form>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
