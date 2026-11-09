<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Accept your Nexora invitation') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/password-flow.css') }}">
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
    <main class="recovery-page">
        <section class="recovery-shell" aria-label="{{ __('User invitation') }}">
            <aside class="brand-panel">
                <div class="language-switcher">
                    @foreach (['es' => '🇪🇸', 'en' => '🇬🇧'] as $lang => $emoji)
                        <a href="{{ route('lang.switch', $lang) }}" class="{{ app()->getLocale() === $lang ? 'active' : '' }}">{{ $emoji }} {{ strtoupper($lang) }}</a>
                    @endforeach
                </div>
                <div class="brand-mark"><div class="logo-badge"><img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora"></div><div class="brand-wordmark"><p>NEXORA</p><span>{{ __('Point of Sale System') }}</span></div></div>
                <div class="panel-copy"><p class="eyebrow">{{ __('User invitation') }}</p><h1>{{ __('Welcome to Nexora.') }}</h1><p>{{ __('Complete your account setup securely.') }}</p></div>
            </aside>
            <section class="form-panel">
                <div class="form-card">
                    <p class="form-kicker">{{ __('Registration summary') }}</p>
                    <h2>{{ __('Accept your invitation') }}</h2>
                    <p class="form-intro">{{ __('You were registered by :admin. Set your password and accept the legal documents to activate your account.', ['admin' => $invitation->invitedBy->nombre . ' ' . $invitation->invitedBy->apellido]) }}</p>

                    <dl class="invitation-summary">
                        <dt>{{ __('Name') }}</dt><dd>{{ $invitation->user->nombre }} {{ $invitation->user->apellido }}</dd>
                        <dt>{{ __('Branch') }}</dt><dd>{{ $invitation->user->sucursal?->nombre ?? __('Not assigned') }}</dd>
                        <dt>{{ __('Role') }}</dt><dd>{{ ucfirst($invitation->user->rol?->nombre ?? __('User')) }}</dd>
                        <dt>{{ __('Registration date') }}</dt><dd>{{ $invitation->created_at?->format('Y-m-d') }}</dd>
                    </dl>

                    @if ($errors->any())
                        <div class="recovery-message error" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                    @endif

                    <form action="{{ route('invitation.accept', ['invitation' => $invitation->id, 'token' => $token]) }}" method="post">
                        @csrf
                        <label for="password">{{ __('New password') }}</label>
                        <div class="password-field"><input class="email-input" type="password" id="password" name="password" minlength="8" pattern="(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="{{ __('Password requirements') }}" required><button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="{{ __('Show password') }}"><i class="bi bi-eye"></i></button></div>
                        <small class="password-hint">{{ __('Password requirements') }}</small>
                        <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <div class="password-field"><input class="email-input" type="password" id="password_confirmation" name="password_confirmation" required><button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="{{ __('Show password') }}"><i class="bi bi-eye"></i></button></div>
                        <label class="legal-check"><input type="checkbox" name="acepta_terminos" value="1" required> {{ __('I accept the') }} <a href="{{ route('legal.terminos') }}" target="_blank" rel="noopener">{{ __('Terms and Conditions') }}</a></label>
                        <label class="legal-check"><input type="checkbox" name="acepta_privacidad" value="1" required> {{ __('I agree to the') }} <a href="{{ route('legal.privacidad') }}" target="_blank" rel="noopener">{{ __('Privacy Notice') }}</a></label>
                        <button type="submit">{{ __('Accept registration') }}</button>
                    </form>
                    <a class="back-link" href="{{ route('login') }}">{{ __('Back to login') }}</a>
                </div>
            </section>
        </section>
    </main>
    <script>
        function togglePassword(id, button) { const input = document.getElementById(id); const visible = input.type === 'text'; input.type = visible ? 'password' : 'text'; button.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash'; }
        document.getElementById('theme-toggle').addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    </script>
</body>
</html>