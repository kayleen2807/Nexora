<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <main class="login-page">
        <section class="login-shell" aria-label="Inicio de sesión Nexora">
            <aside class="brand-panel">
                @php
                    $idiomas = [
                        'es' => '🇪🇸',
                        'en' => '🇬🇧',
                    ];
                @endphp
                <div class="language-switcher">
                    @foreach ($idiomas as $lang => $emoji)
                        <a href="{{ route('lang.switch', $lang) }}"
                        class="{{ app()->getLocale() === $lang ? 'active' : '' }}">
                            {{ $emoji }} {{ strtoupper($lang) }}
                        </a>
                    @endforeach
                </div>
                <div class="brand-mark" aria-label="Nexora">
                    <div class="logo-badge">
                        <img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora - Sistema de punto de venta">
                    </div>
                    <div class="brand-wordmark">
                        <p>NEXORA</p>
                        <span>{{ __('Point of Sale System') }}</span>
                    </div>
                </div>

                <div class="panel-copy">
                    <p class="eyebrow">{{ __('Centralized Management') }}</p>
                    <h1>{{ __('POS to grow with organization.') }}</h1>
                    <p>{{ __('Manage sales, inventory and branches from a clear and fast experience.') }}</p>
                </div>

                <div class="stats-row" aria-label="Beneficios">
                    <span>{{ __('Sales') }}</span>
                    <span>{{ __('Inventory') }}</span>
                    <span>{{ __('Branches') }}</span>
                </div>
            </aside>

            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">{{ __('System Access') }}</p>
                    <h2>{{ __('Login') }}</h2>
                    <p class="form-intro">{{ __('Enter your credentials to access the Nexora panel.') }}</p>

                    @if ($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="post">
                        @csrf

                        <label for="usuario">{{ __('User') }}</label>
                        <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="{{ __('User') }}" autocomplete="username" required autofocus>

                        <label for="password">{{ __('Password') }}</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="{{ __('Password') }}" autocomplete="current-password" required>
                            <button type="button" class="password-toggle" data-target="password" aria-label="{{ __('Show password') }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <div class="form-options">
                            <label class="remember-option" for="remember">
                                <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                {{ __('Remember me') }}
                            </label>
                            <a href="/password.html">{{ __('Forgot my password') }}</a>
                        </div>

                        <button type="submit">{{ __('Login') }}</button>
                    </form>

                    <p class="form-footer">
                        ¿No tienes cuenta?
                        <a class="link-register" href="{{ route('register') }}">Registrarme</a>
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
    </script>
</body>
</html>