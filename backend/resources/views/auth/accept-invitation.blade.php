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
    <script src="{{ asset('js/requisitos-contrasena.js') }}"></script>
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
                    @foreach (['es' => '🇪🇸', 'en' => '🇺🇸'] as $lang => $emoji)
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
                        {{-- Requisitos en vivo con barra de seguridad (public/js/requisitos-contrasena.js) --}}
                        <div data-requisitos-de="password"></div>
                        <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <div class="password-field"><input class="email-input" type="password" id="password_confirmation" name="password_confirmation" required><button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="{{ __('Show password') }}"><i class="bi bi-eye"></i></button></div>
                        <div data-coincide-con="password" data-confirmacion="password_confirmation"></div>

                        {{-- Igual que en el registro: las casillas se habilitan solo después de abrir su enlace --}}
                        <label class="legal-check {{ old('acepta_terminos') ? '' : 'is-locked' }}" for="acepta_terminos"><input type="checkbox" id="acepta_terminos" name="acepta_terminos" value="1" {{ old('acepta_terminos') ? 'checked' : 'disabled' }} required> <span>{!! __('I accept the :link', ['link' => '<a href="' . e(route('legal.terminos')) . '" target="_blank" rel="noopener" data-requires="acepta_terminos">' . e(__('Terms and Conditions')) . '</a>']) !!}</span></label>
                        <label class="legal-check {{ old('acepta_privacidad') ? '' : 'is-locked' }}" for="acepta_privacidad"><input type="checkbox" id="acepta_privacidad" name="acepta_privacidad" value="1" {{ old('acepta_privacidad') ? 'checked' : 'disabled' }} required> <span>{!! __('I agree to the :link', ['link' => '<a href="' . e(route('legal.privacidad')) . '" target="_blank" rel="noopener" data-requires="acepta_privacidad">' . e(__('Privacy Notice')) . '</a>']) !!}</span></label>
                        <small id="legal-hint" class="legal-hint">{{ __('Open and read both links to be able to accept them.') }}</small>
                        <button type="submit" id="invitation-submit" disabled><i class="bi bi-check2-circle" aria-hidden="true"></i> {{ __('Accept registration') }}</button>
                    </form>
                    <a class="back-link" href="{{ route('login') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> {{ __('Back to login') }}</a>
                </div>
            </section>
        </section>
    </main>
    <script>
        function togglePassword(id, button) { const input = document.getElementById(id); const visible = input.type === 'text'; input.type = visible ? 'password' : 'text'; button.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash'; }

        // Igual que register.blade.php: el botón se habilita con el formulario válido, las contraseñas iguales
        // y las dos casillas marcadas; cada casilla se desbloquea al abrir su enlace (se recuerda en la pestaña).
        (function () {
            var form = document.querySelector('form');
            var terms = document.getElementById('acepta_terminos');
            var privacy = document.getElementById('acepta_privacidad');
            var submitBtn = document.getElementById('invitation-submit');
            var legalHint = document.getElementById('legal-hint');
            var password = document.getElementById('password');
            var confirmation = document.getElementById('password_confirmation');

            function safeGet(key) { try { return sessionStorage.getItem(key); } catch (e) { return null; } }
            function safeSet(key, value) { try { sessionStorage.setItem(key, value); } catch (e) {} }

            function updateState() {
                var legalOk = terms.checked && privacy.checked;
                submitBtn.disabled = !(legalOk && form.checkValidity() && password.value === confirmation.value);
            }

            function refreshLegalHint() {
                legalHint.style.display = form.querySelectorAll('.legal-check input:disabled').length ? 'block' : 'none';
            }

            document.querySelectorAll('a[data-requires]').forEach(function (link) {
                var checkbox = document.getElementById(link.dataset.requires);
                var row = checkbox.closest('.legal-check');
                var key = 'leido_' + link.dataset.requires;

                function unlock() {
                    checkbox.disabled = false;
                    row.classList.remove('is-locked');
                    row.removeAttribute('title');
                    safeSet(key, '1');
                    refreshLegalHint();
                    updateState();
                }

                if (safeGet(key) || checkbox.checked) {
                    unlock();
                } else {
                    row.title = @json(__('First open and read the link'));
                }
                link.addEventListener('click', unlock);
                link.addEventListener('auxclick', unlock); // clic con la rueda del mouse
            });

            form.addEventListener('input', updateState);
            form.addEventListener('change', updateState);
            window.addEventListener('pageshow', updateState);
            refreshLegalHint();
            updateState();
        })();
        document.getElementById('theme-toggle').addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    </script>
</body>
</html>