<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Forgot your password?') }} | Nexora</title>
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
        <section class="recovery-shell" aria-label="{{ __('Access recovery') }}">
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
                    <h2>{{ __('Forgot your password?') }}</h2>
                    <p class="form-intro">{{ __('Enter your email address and we will help you reset your password.') }}</p>

                    @if (session('status'))
                        <div class="recovery-message success" role="status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="recovery-message error" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="post">
                        @csrf
                        <label for="correo">{{ __('Email address') }}</label>
                        <input class="email-input" type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="{{ __('Email address') }}" autocomplete="email" required autofocus>
                        <button type="submit">{{ __('Send reset link') }}</button>
                    </form>

                    <a class="back-link" href="{{ route('login') }}">
                        <span aria-hidden="true">←</span>
                        {{ __('Back to login') }}
                    </a>
                </div>
            </section>
        </section>
    </main>
     <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    </script>
</body>
</html>