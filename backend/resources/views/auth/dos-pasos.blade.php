<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Two-step verification') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/password-flow.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body>
    {{-- Mismo diseño que verify-email.blade.php; theme.js conecta el botón #theme-toggle --}}
    <button type="button" class="theme-toggle" id="theme-toggle" aria-label="{{ __('Change theme') }}">
        <i class="bi bi-sun-fill icon-sun"></i>
        <i class="bi bi-moon-fill icon-moon"></i>
    </button>
    <main class="recovery-page">
        <section class="recovery-shell" aria-label="{{ __('Two-step verification') }}">
            <aside class="brand-panel">
                <div class="language-switcher">
                    @foreach (['es' => '🇪🇸', 'en' => '🇺🇸'] as $lang => $emoji)
                        <a href="{{ route('lang.switch', $lang) }}" class="{{ app()->getLocale() === $lang ? 'active' : '' }}">{{ $emoji }} {{ strtoupper($lang) }}</a>
                    @endforeach
                </div>
                <div class="brand-mark">
                    <div class="logo-badge"><img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora"></div>
                    <div class="brand-wordmark"><p>NEXORA</p><span>{{ __('Point of Sale System') }}</span></div>
                </div>
                <div class="panel-copy">
                    <p class="eyebrow">{{ __('Security') }}</p>
                    <h1>{{ __('Protect your account.') }}</h1>
                    <p>{{ __('Your account has two-step verification: besides your password, we ask for a code sent to your email.') }}</p>
                </div>
            </aside>
            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">{{ __('Two-step verification') }}</p>
                    <h2>{{ __('Check your email') }}</h2>
                    <p class="form-intro">{{ __('Enter the 6-digit code we sent to :email.', ['email' => $correo]) }}</p>

                    @if (session('status'))
                        <div class="recovery-message success" role="status">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="recovery-message error" role="alert">
                            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('dos-pasos.verificar') }}" method="post">
                        @csrf
                        <label for="codigo">{{ __('Verification code') }}</label>
                        <input class="email-input verification-code" type="text" id="codigo" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
                        <button type="submit"><i class="bi bi-shield-check" aria-hidden="true"></i> {{ __('Continue') }}</button>
                    </form>

                    <form class="resend-form" action="{{ route('dos-pasos.reenviar') }}" method="post">
                        @csrf
                        <button type="submit" class="link-button"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> {{ __('Send a new code') }}</button>
                    </form>
                    <a class="back-link" href="{{ route('login') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> {{ __('Back to login') }}</a>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
