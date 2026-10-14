<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Set a new password') }} | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/password-flow.css') }}">
</head>
<body>
    <main class="recovery-page">
        <section class="recovery-shell" aria-label="{{ __('Set a new password') }}">
            <aside class="brand-panel">
                <div class="language-switcher">
                    @foreach (['es' => '🇪🇸', 'en' => '🇬🇧'] as $lang => $emoji)
                        <a href="{{ route('lang.switch', $lang) }}" class="{{ app()->getLocale() === $lang ? 'active' : '' }}">
                            {{ $emoji }} {{ strtoupper($lang) }}
                        </a>
                    @endforeach
                </div>

                <div class="brand-mark">
                    <div class="logo-badge">
                        <img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora - {{ __('Point of Sale System') }}">
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
                    <p class="form-kicker">{{ __('Access recovery') }}</p>
                    <h2>{{ __('Set a new password') }}</h2>
                    <p class="form-intro">{{ __('Choose a new password for your account.') }}</p>

                    @if ($errors->any())
                        <div class="recovery-message error" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="post" id="password-reset-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <label for="correo">{{ __('Email address') }}</label>
                        <input class="email-input" type="email" id="correo" name="correo" value="{{ old('correo', $correo) }}" autocomplete="email" required readonly>

                        <label for="password">{{ __('New password') }}</label>
                        <div class="password-field">
                            <input class="email-input" type="password" id="password" name="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="{{ __('Password requirements') }}" required>
                            <button type="button" class="password-toggle" data-target="password" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-label="{{ __('Show password') }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="password-hint">{{ __('Password requirements') }}</small>

                        <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <div class="password-field">
                            <input class="email-input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" data-target="password_confirmation" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-label="{{ __('Show password') }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <button type="submit">{{ __('Reset password') }}</button>
                    </form>

                    <a class="back-link" href="{{ route('password.request') }}">{{ __('Request a new link') }}</a>
                </div>
            </section>
        </section>
    </main>

    <script>
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
    </script>
</body>
</html>