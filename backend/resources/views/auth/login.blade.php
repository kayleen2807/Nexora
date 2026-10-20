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
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>
     <button type="button" class="theme-toggle" id="theme-toggle" aria-label="{{ __('Change theme') }}">
        <i class="bi bi-sun-fill icon-sun"></i>
        <i class="bi bi-moon-fill icon-moon"></i>
    </button>
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

                    @if (session('status'))
                        <div class="login-status" role="status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="post">
                        @csrf

                        <label for="correo">{{ __('Email') }}</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="{{ __('Email') }}" autocomplete="email" required autofocus>

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
                            <a href="{{ route('password.request') }}">{{ __('Forgot my password') }}</a>
                        </div>

                        <button type="submit">{{ __('Login') }}</button>
                    </form>

                    <div class="google-divider"><span>{{ __('or') }}</span></div>

                    <a href="{{ route('google.redirect') }}" class="btn-google">
                        <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        {{ __('Continue with Google') }}
                    </a>

                    <p class="form-footer">
                        {{ __("Don't have an account?") }}
                        <a class="link-register" href="{{ route('register') }}">{{ __('Register') }}</a>
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
         document.getElementById('theme-toggle').addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    </script>
</body>
</html>