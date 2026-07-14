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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/main.css?v=2') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/header.css?v=2') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/home.css?v=2') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/projects.css?v=4') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/footer.css?v=2') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/about.css?v=2') }}" type="text/css">
</head>

<body>
    <header class="header">
        <a href="/" class="header-logo">{{ '@' . $user->username }}</a>
        <nav class="nav">
            <div class="toggle"><i class="fas fa-bars"></i></div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a href="{{ route('projects') }}">Projects</a></li>
                <li class="nav-item"><a href="{{ route('photos') }}">Certificate</a></li>
            </ul>
        </nav>
    </header>

    @yield('content')

    <footer class="footer-container">
        <div class="footer">
            <div class="footer-column">
                <a href="/" class="footer-logo">{{ '@' . $user->username }}</a>
                <div class="socials">
                    <ul>
                        <li class="social-link">
                            <a href="{{ $user->linkedin_url }}" aria-label="LinkedIn" target="_blank">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        </li>
                        <li class="social-link">
                            <a href="{{ $user->github_url }}" aria-label="GitHub" target="_blank">
                                <i class="fa-brands fa-github"></i>
                            </a>
                        </li>
                        <li class="social-link">
                            <a href="{{ $user->insta_url }}" aria-label="Instagram" target="_blank">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <span class="copyright"><i class="fa-regular fa-copyright"></i> {{ $user->name }} -
                    {{ now()->format('Y') }}</span>
            </div>
            <div class="footer-column">
                <a href="{{ route('about') }}" class="footer-button">
                    <img src="{{ asset('img/smile.svg') }}" alt="Smile Icon" loading="lazy">
                    About
                </a>
                <a href="{{ route('projects') }}" class="footer-button">
                    <img src="{{ asset('img/coding.svg') }}" alt="Code Icon" loading="lazy">
                    Projects
                </a>

            </div>
            <div class="footer-column">
                <a href="{{ route('photos') }}" class="footer-button">
                    <img src="{{ asset('img/camera.svg') }}" alt="Camera Icon" loading="lazy">
                    Certificates
                </a>
                <a href="mailto:{{ $user->email }}" class="footer-button">
                    <img src="{{ asset('img/email.svg') }}" alt="Email Icon" loading="lazy">
                    Contact
                </a>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>
