<!DOCTYPE html>
<html lang="en">
@php
    $user = App\Models\Setting::first();
@endphp

<head>
    <title>@yield('title') | {{ $user->name }} — {{ $user->title }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <meta charset="UTF-8">

    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <meta property="og:title" content="{{ $user->name }} — {{ $user->title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://abshewabu.dev" />
    <meta property="og:description" content="{{ $user->name }} — {{ $user->title }}. Portfolio, projects, and certificates." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="https://abshewabu.dev/storage/{{ $user->hero_gif }}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $user->name }} — {{ $user->title }}">
    <meta name="twitter:description" content="{{ $user->name }} — {{ $user->title }}. Portfolio, projects, and certificates.">
    <meta name="twitter:image" content="https://abshewabu.dev/storage/{{ $user->hero_gif }}">
    <meta name="twitter:site" content="{{ '@' . $user->username }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="copyright" content="{{ $user->name }}">
    <meta name="description" content="{{ $user->name }} — {{ $user->title }}. Portfolio, projects, and certificates.">
    <meta name="keywords"
        content="{{ $user->name }}, {{ $user->title }}, portfolio, developer, web developer, projects, certificates">
    <meta name="robots" content="index, follow" />

    <link rel="canonical" href="https://abshewabu.dev" />

    <link rel="preload" href="{{ asset('fonts/space-grotesk-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/inter-latin.woff2') }}" as="font" type="font/woff2" crossorigin>

    @if (request()->routeIs('index') && $user && $user->hero_gif)
        @php
            $heroBase = pathinfo($user->hero_gif, PATHINFO_FILENAME);
            $heroDir = file_exists(public_path("storage/{$heroBase}-400.avif")) ? 'storage' : (file_exists(public_path("img/{$heroBase}-400.avif")) ? 'img' : null);
        @endphp
        @if ($heroDir && file_exists(public_path("{$heroDir}/{$heroBase}-700.avif")))
            <link rel="preload" as="image" type="image/avif" fetchpriority="high"
                  imagesrcset="{{ asset("{$heroDir}/{$heroBase}-400.avif") }} 400w, {{ asset("{$heroDir}/{$heroBase}-700.avif") }} 700w"
                  imagesizes="(max-width: 640px) 352px, 450px">
        @elseif (file_exists(public_path("storage/{$user->hero_gif}")))
            <link rel="preload" as="image" href="{{ asset("storage/{$user->hero_gif}") }}" fetchpriority="high">
        @endif
    @endif

    <style>
        @include('partials.critical-css')
    </style>

    <link rel="stylesheet" href="{{ asset('css/footer.css?v=4') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('css/footer.css?v=4') }}"></noscript>

    @if (request()->routeIs('index'))
        <link rel="stylesheet" href="{{ asset('css/home.css?v=3') }}" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/home.css?v=3') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}">
        </noscript>
    @elseif (request()->routeIs('projects'))
        <link rel="stylesheet" href="{{ asset('css/projects.css?v=5') }}" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/projects.css?v=5') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}">
        </noscript>
    @elseif (request()->routeIs('about'))
        <link rel="stylesheet" href="{{ asset('css/about.css?v=3') }}" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/about.css?v=3') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}">
        </noscript>
    @elseif (request()->routeIs('photos'))
        <link rel="stylesheet" href="{{ asset('css/photos.css?v=2') }}" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/photos.css?v=2') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css?v=5') }}">
        </noscript>
    @endif
</head>

<body>
    <header class="header">
        <a href="/" class="header-logo">{{ '@' . $user->username }}</a>
        <nav class="nav">
            <div class="toggle"><x-app-icon name="bars" width="1.5rem" height="1.5rem" /></div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="{{ route('about') }}" @class(['is-current' => request()->routeIs('about')])>About</a></li>
                <li class="nav-item"><a href="{{ route('projects') }}" @class(['is-current' => request()->routeIs('projects')])>Projects</a></li>
                <li class="nav-item"><a href="{{ route('photos') }}" @class(['is-current' => request()->routeIs('photos')])>Certificate</a></li>
            </ul>
            <button type="button" id="theme-toggle" aria-label="Toggle dark mode" aria-pressed="false">
                <svg class="theme-icon theme-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                </svg>
                <svg class="theme-icon theme-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
            </button>
        </nav>
    </header>

    @yield('content')

    <footer class="footer-container">
        <p class="eyebrow footer-eyebrow">$ ./contact.sh</p>
        <div class="footer">
            <div class="footer-column">
                <a href="/" class="footer-logo">{{ '@' . $user->username }}</a>
                <div class="socials">
                    <ul>
                        <li class="social-link">
                            <a href="{{ $user->linkedin_url }}" aria-label="LinkedIn" target="_blank">
                                <x-app-icon name="linkedin" />
                            </a>
                        </li>
                        <li class="social-link">
                            <a href="{{ $user->github_url }}" aria-label="GitHub" target="_blank">
                                <x-app-icon name="github" />
                            </a>
                        </li>
                        <li class="social-link">
                            <a href="{{ $user->insta_url }}" aria-label="Instagram" target="_blank">
                                <x-app-icon name="instagram" />
                            </a>
                        </li>
                    </ul>
                </div>
                <span class="copyright"><x-app-icon name="copyright" width="0.875rem" height="0.875rem" /> {{ $user->name }} -
                    {{ now()->format('Y') }}</span>
            </div>
            <div class="footer-column">
                <a href="{{ route('about') }}" class="footer-button">
                    <img src="{{ asset('img/smile.svg') }}" alt="Smile Icon" width="18" height="18" loading="lazy" decoding="async">
                    About
                </a>
                <a href="{{ route('projects') }}" class="footer-button">
                    <img src="{{ asset('img/coding.svg') }}" alt="Code Icon" width="18" height="18" loading="lazy" decoding="async">
                    Projects
                </a>

            </div>
            <div class="footer-column">
                <a href="{{ route('photos') }}" class="footer-button">
                    <img src="{{ asset('img/camera.svg') }}" alt="Camera Icon" width="18" height="18" loading="lazy" decoding="async">
                    Certificates
                </a>
                <a href="mailto:{{ $user->email }}" class="footer-button">
                    <img src="{{ asset('img/email.svg') }}" alt="Email Icon" width="18" height="18" loading="lazy" decoding="async">
                    Contact
                </a>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/main.js') }}" defer></script>
</body>

</html>
