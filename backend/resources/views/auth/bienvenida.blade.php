<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welcome') }} | Nexora</title>
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

    <main class="welcome-page" aria-label="Bienvenida Nexora">
        {{-- Barra superior: menú (Acerca de, Ayuda, Acceder) e idiomas --}}
        <header class="welcome-topbar">
            <nav class="welcome-nav" aria-label="{{ __('Main menu') }}">
                <a href="#acerca" class="welcome-nav__link">{{ __('About Nexora') }}</a>
                <a href="{{ route('ayuda') }}" class="welcome-nav__link">{{ __('Help') }}</a>

                <div class="nav-dropdown" id="access-menu">
                    <button type="button" class="nav-dropdown__toggle" aria-haspopup="true" aria-expanded="false" aria-controls="access-menu-list">
                        {{ __('Access') }} <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <ul class="nav-dropdown__list" id="access-menu-list" hidden>
                        <li>
                            <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> {{ __('Login') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}"><i class="bi bi-person-plus" aria-hidden="true"></i> {{ __('Register') }}</a>
                        </li>
                    </ul>
                </div>
            </nav>

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
        </header>

        {{-- Portada --}}
        <section class="welcome-hero">
            {{-- Columna izquierda: logo y, debajo, NEXORA + sistema de punto de venta + gestión centralizada --}}
            <div class="welcome-hero__brand" aria-label="Nexora">
                <div class="logo-badge">
                    <img src="{{ asset('img/nexora-logo-cropped.png') }}" alt="Nexora - Sistema de punto de venta">
                </div>
                <div class="brand-wordmark">
                    <p>NEXORA</p>
                    <span>{{ __('Point of Sale System') }}</span>
                </div>
                <p class="eyebrow">{{ __('Centralized Management') }}</p>
            </div>

            {{-- Columna derecha: título, descripción y etiquetas --}}
            <div class="welcome-hero__copy">
                <div class="panel-copy">
                    <h1>{{ __('POS to grow with organization.') }}</h1>
                    <p>{{ __('Manage sales, inventory and branches from a clear and fast experience.') }}</p>
                </div>

                <div class="welcome-features" aria-label="Beneficios">
                    <span><i class="bi bi-cart-check"></i> {{ __('Sales') }}</span>
                    <span><i class="bi bi-box-seam"></i> {{ __('Inventory') }}</span>
                    <span><i class="bi bi-shop"></i> {{ __('Branches') }}</span>
                </div>
            </div>
        </section>

        {{-- Sección "Acerca de Nexora" --}}
        <section class="about" id="acerca" aria-labelledby="about-title">
            <p class="form-kicker">{{ __('About us') }}</p>
            <h2 id="about-title">{{ __('About Nexora') }}</h2>
            <p class="about-lead">{{ __('Nexora is a point of sale system for businesses with several branches. It brings sales, inventory and branch management into one clear and fast experience.') }}</p>

            <div class="about-grid">
                <article class="about-card">
                    <i class="bi bi-cart-check" aria-hidden="true"></i>
                    <h3>{{ __('Sales') }}</h3>
                    <p>{{ __('Register sales quickly and keep a record of every transaction.') }}</p>
                </article>
                <article class="about-card">
                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                    <h3>{{ __('Inventory') }}</h3>
                    <p>{{ __('Keep track of your products and stock in every branch.') }}</p>
                </article>
                <article class="about-card">
                    <i class="bi bi-shop" aria-hidden="true"></i>
                    <h3>{{ __('Branches') }}</h3>
                    <p>{{ __('Manage all your branches from a single, centralized place.') }}</p>
                </article>
            </div>

            <p class="about-note">{{ __('Nexora is developed by a student team as a multi-branch point of sale project.') }}</p>

            <div class="about-help">
                <span>{{ __('Need help?') }}</span>
                <a href="{{ route('ayuda') }}">{{ __('Sign in to send us a message.') }}</a>
            </div>
        </section>
    </main>

    <script>
        // Menú desplegable de acceso: se abre con clic y se cierra al hacer clic fuera o con Escape.
        (function () {
            var menu = document.getElementById('access-menu');
            var toggle = menu.querySelector('.nav-dropdown__toggle');
            var list = document.getElementById('access-menu-list');

            function setOpen(open) {
                list.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                menu.classList.toggle('is-open', open);
            }

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                setOpen(list.hidden);
            });

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !list.hidden) {
                    setOpen(false);
                    toggle.focus();
                }
            });
        })();
    </script>
</body>
</html>