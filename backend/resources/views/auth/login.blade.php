<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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

                        <label for="correo">{{ __('Email') }}</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="{{ __('Email') }}" autocomplete="email" required autofocus>

                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" id="password" name="password" placeholder="{{ __('Password') }}" autocomplete="current-password" required>

                        <div class="form-options">
                            <label class="remember-option" for="remember">
                                <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                {{ __('Remember me') }}
                            </label>
                            <a href="#">{{ __('Forgot my password') }}</a>
                        </div>

                        <button type="submit">{{ __('Login') }}</button>
                    </form>
                </div>
            </section>
        </section>
    </main>
</body>
</html>