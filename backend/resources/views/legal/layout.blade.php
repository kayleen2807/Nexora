<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Nexora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        /* Mismas variables que public/css/login.css */
        :root {
            color-scheme: light;
            --orange: #ef8e19;
            --orange-dark: #cf7110;
            --accent: var(--orange-dark);
 
            --brand-bg: linear-gradient(135deg, #f1f4f8 0%, #e2e7ee 100%);
            --brand-stripe: rgba(255, 255, 255, 0.55);
            --card-bg: #ffffff;
            --card-border: #e3e8ee;
            --card-shadow: 0 16px 38px rgba(20, 36, 58, 0.08);
 
            --title: #14243a;
            --text: #3f4b5d;
            --muted: #6b7686;
            --line: #d9e0e8;
            --pill-bg: rgba(20, 36, 58, 0.08);
            --pill-text: #14243a;
 
            --toggle-bg: #ffffff;
            --toggle-border: #d9e0e8;
        }
        :root[data-theme="dark"] {
            color-scheme: dark;
            --accent: var(--orange);
 
            --brand-bg: linear-gradient(135deg, #20252c 0%, #15181d 100%);
            --brand-stripe: rgba(255, 255, 255, 0.04);
            --card-bg: #232830;
            --card-border: #323843;
            --card-shadow: 0 16px 38px rgba(0, 0, 0, 0.35);
 
            --title: #ffffff;
            --text: #d6dbe3;
            --muted: #9ba5b3;
            --line: #343b46;
            --pill-bg: rgba(255, 255, 255, 0.1);
            --pill-text: #ffffff;
 
            --toggle-bg: #1f242b;
            --toggle-border: rgba(255, 255, 255, 0.18);
        }
 
        * { box-sizing: border-box; }
 
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Montserrat', system-ui, sans-serif;
            color: var(--text);
            line-height: 1.7;
            background:
                repeating-linear-gradient(115deg, transparent 0 140px, var(--brand-stripe) 140px 280px),
                var(--brand-bg);
            background-attachment: fixed;
        }
 
        .legal-wrap { max-width: 860px; margin: 0 auto; padding: 32px 20px 64px; }
        .legal-top {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
        }
        .legal-back { color: var(--accent); text-decoration: none; font-weight: 700; font-size: .92rem; }
        .legal-back:hover { text-decoration: underline; }
        .legal-tools { display: flex; align-items: center; gap: 10px; }
 
        /* Selector de idioma estilo login */
        .legal-lang { display: flex; gap: 8px; }
        .legal-lang a {
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--pill-bg);
            color: var(--pill-text);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            opacity: .6;
            transition: opacity .2s ease;
        }
        .legal-lang a:hover { opacity: 1; }
        .legal-lang a.active { opacity: 1; }
 
        /* Botón de tema estilo login */
        .legal-theme {
            width: 46px; height: 46px; padding: 0;
            display: grid; place-items: center;
            border: 1px solid var(--toggle-border);
            border-radius: 50%;
            background: var(--toggle-bg);
            font-size: 22px; line-height: 1;
            cursor: pointer;
            transition: transform 160ms ease, border-color 160ms ease;
        }
        .legal-theme:hover { transform: scale(1.06); border-color: var(--orange); }
        .legal-theme:focus-visible { outline: 2px solid var(--orange); outline-offset: 3px; }
        .legal-theme i {
            display: block;
            background: linear-gradient(135deg, #ffd45a, #f58a1f);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .legal-theme .icon-sun { display: none; }
        :root[data-theme="dark"] .legal-theme .icon-moon { display: none; }
        :root[data-theme="dark"] .legal-theme .icon-sun { display: block; }
 
        .legal-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: var(--card-shadow);
        }
        .legal-card h1 { margin: 0 0 4px; font-size: 1.7rem; color: var(--title); }
        .legal-updated { color: var(--muted); font-size: .85rem; margin: 0 0 24px; }
        .legal-card h2 { font-size: 1.1rem; margin: 28px 0 8px; color: var(--accent); }
        .legal-card p, .legal-card li { font-size: .95rem; }
        .legal-card ul, .legal-card ol { padding-left: 22px; }
        .legal-card a { color: var(--accent); font-weight: 700; }
        .legal-card strong { color: var(--title); }
        .legal-note {
            border-left: 4px solid var(--orange);
            background: color-mix(in srgb, var(--orange) 10%, transparent);
            padding: 12px 16px; border-radius: 8px; font-size: .9rem; margin: 16px 0;
        }
        @media (max-width: 600px) { .legal-card { padding: 24px 18px; } }
    </style>
</head>
<body>
    <div class="legal-wrap">
        <div class="legal-top">
            <a class="legal-back" href="{{ route('register') }}">
                <i class="bi bi-arrow-left"></i>
                {{ app()->getLocale() === 'es' ? 'Volver al registro' : 'Back to registration' }}
            </a>
            <div class="legal-tools">
                <div class="legal-lang">
                    @foreach (['es' => '🇪🇸', 'en' => '🇬🇧'] as $lang => $emoji)
                        <a href="{{ route('lang.switch', $lang) }}" class="{{ app()->getLocale() === $lang ? 'active' : '' }}">{{ $emoji }} {{ strtoupper($lang) }}</a>
                    @endforeach
                </div>
                <button type="button" class="legal-theme" id="legal-theme" aria-label="{{ app()->getLocale() === 'es' ? 'Cambiar tema' : 'Change theme' }}">
                    <i class="bi bi-moon-fill icon-moon"></i>
                    <i class="bi bi-sun-fill icon-sun"></i>
                </button>
            </div>
        </div>
 
        <article class="legal-card">
            @yield('content')
        </article>
    </div>
 
    <script>
        document.getElementById('legal-theme').addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    </script>
</body>
</html>
 