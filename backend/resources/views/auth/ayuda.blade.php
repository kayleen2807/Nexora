<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Help') }} | Nexora</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bienvenida.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body>
    <button type="button" class="theme-toggle" id="theme-toggle" aria-label="{{ __('Change theme') }}">
        <i class="bi bi-sun-fill icon-sun"></i>
        <i class="bi bi-moon-fill icon-moon"></i>
    </button>
    <main class="login-page">
        <section class="login-shell" aria-label="{{ __('Help') }}">
            <aside class="brand-panel">
                @php
                    $idiomas = [
                        'es' => '🇪🇸',
                        'en' => '🇺🇸',
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
                    <p class="form-kicker">{{ __('Help') }}</p>
                    <h2>{{ __('How can we help you?') }}</h2>
                    <p class="form-intro">{{ __('Write your message and we will reply to your email.') }}</p>

                    @if (session('status'))
                        <div class="form-success" role="status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $correoUsuario = $usuario->correo ?? $usuario->email ?? null;
                    @endphp
                    @if ($correoUsuario)
                        <p class="help-reply">{{ __('We will reply to') }}: <strong>{{ $correoUsuario }}</strong></p>
                    @endif

                    <form action="{{ route('ayuda.enviar') }}" method="post">
                        @csrf

                        <label for="asunto">{{ __('Subject') }}</label>
                        <input type="text" id="asunto" name="asunto" value="{{ old('asunto') }}" placeholder="{{ __('Subject') }}" maxlength="150" required autofocus>

                        <label for="mensaje">{{ __('Message') }}</label>
                        <textarea id="mensaje" name="mensaje" class="help-textarea" placeholder="{{ __('Message') }}" minlength="10" maxlength="2000" required>{{ old('mensaje') }}</textarea>

                        <button type="submit"><i class="bi bi-send" aria-hidden="true"></i> {{ __('Send message') }}</button>
                    </form>

                    <a class="help-back" href="{{ url('/') }}">{{ __('Back to welcome page') }}</a>
                </div>
            </section>
        </section>
    </main>
</body>
</html>