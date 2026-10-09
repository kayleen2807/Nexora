<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <main class="login-page">
        <section class="login-shell" aria-label="{{ __('User registration') }}">
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
            </aside>

            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">{{ __('New to Nexora') }}</p>
                    <h2>{{ __('Register') }}</h2>
                    <p class="form-intro">{{ __('Complete the form to create your account.') }}</p>

                    @if ($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div id="password-mismatch" class="form-error" role="alert">
                        {{ __('Passwords do not match.') }}
                    </div>

                    <form action="{{ route('register') }}" method="post" id="register-form">
                        @csrf

                        <label for="nombre">{{ __('Name') }}</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="{{ __('Name') }}" autocomplete="given-name" required autofocus>

                        <label for="apellido">{{ __('First surname') }}</label>
                        <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="{{ __('First surname') }}" autocomplete="family-name" required>

                        <label for="fecha_nacimiento">{{ __('Date of birth') }}</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" max="{{ now()->subYears(18)->toDateString() }}" required>

                        <label for="correo">{{ __('Email address') }}</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="{{ __('Email address') }}" autocomplete="email" required>

                        <label for="password">{{ __('Password') }}</label>
                        <div class="password-field">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="{{ __('Password') }}"
                                autocomplete="new-password"
                                minlength="8"
                                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                                title="{{ __('Password requirements') }}"
                                required
                            >
                            <button type="button" class="password-toggle" data-target="password" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-label="{{ __('Show password') }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="password-hint">{{ __('Password requirements') }}</small>

                        <label for="password_confirmation">{{ __('Confirm password') }}</label>
                        <div class="password-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm password') }}" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" data-target="password_confirmation" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-label="{{ __('Show password') }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <button type="submit">{{ __('Register') }}</button>
                    </form>

                    <p class="form-footer">
                        {{ __('Already have an account?') }}
                        <a class="link-login" href="{{ route('login') }}">{{ __('Login') }}</a>
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
                button.setAttribute('aria-label', isHidden ? button.dataset.hideLabel : button.dataset.showLabel);
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
