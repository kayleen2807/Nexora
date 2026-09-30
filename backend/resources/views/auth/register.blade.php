<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <main class="login-page">
        <section class="login-shell" aria-label="Registro Nexora">
            <aside class="brand-panel">
                <div class="brand-mark" aria-label="Nexora">
                    <div class="logo-badge">
                        <img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora - Sistema de punto de venta">
                    </div>
                    <div class="brand-wordmark">
                        <p>NEXORA</p>
                        <span>Sistema de punto de venta</span>
                    </div>
                </div>

                <div class="panel-copy">
                    <p class="eyebrow">Gestión centralizada</p>
                    <h1>Crea tu cuenta para empezar.</h1>
                    <p>Registra tus datos para acceder al panel de Nexora según tu rol.</p>
                </div>
            </aside>

            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">Nuevo en Nexora</p>
                    <h2>Registrarme</h2>
                    <p class="form-intro">Completa el formulario para crear tu cuenta.</p>

                    @if ($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div id="password-mismatch" class="form-error" role="alert">
                        Las contraseñas no coinciden.
                    </div>

                    <form action="{{ route('register') }}" method="post" id="register-form">
                        @csrf

                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" autocomplete="given-name" required autofocus>

                        <label for="apellido">Primer apellido</label>
                        <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Primer apellido" autocomplete="family-name" required>

                        <label for="fecha_nacimiento">Fecha de nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" max="{{ now()->subYears(18)->toDateString() }}" required>

                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="correo@ejemplo.com" autocomplete="email" required>

                        <label for="password">Contraseña</label>
                        <div class="password-field">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Contraseña"
                                autocomplete="new-password"
                                minlength="8"
                                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                                title="Mínimo 8 caracteres, una mayúscula, un número y por lo menos un carácter especial"
                                required
                            >
                            <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="password-hint">Mínimo 8 caracteres, una mayúscula, un número y por lo menos un carácter especial.</small>

                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="password-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar contraseña" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <button type="submit">Registrar</button>
                    </form>

                    <p class="form-footer">
                        ¿Ya tienes cuenta?
                        <a class="link-login" href="{{ route('login') }}">Inicia sesión</a>
                    </p>
                </div>
            </section>
        </section>
    </main>

    <script>
        // Alterna type="password"/"text" y el icono del ojito para cada campo marcado.
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.target);
                var icon = button.querySelector('i');
                var isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !isHidden);
                icon.classList.toggle('bi-eye-slash', isHidden);
                button.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });

        // Valida en el cliente que ambas contraseñas coincidan antes de enviar el formulario.
        document.getElementById('register-form').addEventListener('submit', function (event) {
            var password = document.getElementById('password').value;
            var confirmation = document.getElementById('password_confirmation').value;
            var mismatchAlert = document.getElementById('password-mismatch');

            if (password !== confirmation) {
                event.preventDefault();
                mismatchAlert.classList.add('is-visible');
            } else {
                mismatchAlert.classList.remove('is-visible');
            }
        });
    </script>
</body>
</html>
