@font-face {
    font-family: 'Space Grotesk';
    font-style: normal;
    font-weight: 500 700;
    font-display: swap;
    src: url('{{ asset('fonts/space-grotesk-latin.woff2') }}') format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074-2079, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

@font-face {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 400 700;
    font-display: swap;
    src: url('{{ asset('fonts/inter-latin.woff2') }}') format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074-2079, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

@font-face {
    font-family: 'IBM Plex Mono';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('{{ asset('fonts/ibm-plex-mono-400-latin.woff2') }}') format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074-2079, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

@font-face {
    font-family: 'IBM Plex Mono';
    font-style: normal;
    font-weight: 500;
    font-display: swap;
    src: url('{{ asset('fonts/ibm-plex-mono-500-latin.woff2') }}') format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074-2079, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

:root {
    --paper: #FAFAF7;
    --ink: #14151A;
    --muted: #6B6D76;
    --line: #E7E5DF;
    --accent: #1F5C4C;
    --accent-soft: #E7EFEC;
    --gold: #C99A44;
    --surface: #FFFFFF;

    --font-display: 'Space Grotesk', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-mono: 'IBM Plex Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

    --text-eyebrow: 0.8rem;
    --letter-spacing-eyebrow: 0.08em;
    --text-body: 1rem;
    --line-height-body: 1.6;
    --text-h3: 1.5rem;
    --text-h2: 2.25rem;
    --text-h1: clamp(2.5rem, 5vw, 4.5rem);

    --max-width: 1100px;
    --section-padding: clamp(4rem, 8vw, 7rem);
}

[data-theme="dark"] {
    --paper: #14151A;
    --ink: #F5F4F0;
    --muted: #9A9CA6;
    --line: #2A2C33;
    --accent: #4FA98C;
    --accent-soft: #1B2E29;
    --gold: #D8AE5E;
    --surface: #1B1C22;
}

html[data-theme="light"] {
    color-scheme: light;
}

html[data-theme="dark"] {
    color-scheme: dark;
}

body,
header,
.footer-container,
.card,
button,
.filter-pill,
.cta-button,
.footer-button,
.footer-logo,
.header-logo,
.nav-menu {
    transition: background-color 200ms ease, color 200ms ease, border-color 200ms ease;
}

*,
*::before,
*::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
    overflow-x: clip;
}

body {
    overflow-x: clip;
    font-family: var(--font-body);
    font-size: var(--text-body);
    font-weight: 400;
    line-height: var(--line-height-body);
    color: var(--ink);
    background-color: var(--paper);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

h1,
h2,
h3 {
    font-family: var(--font-display);
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
}

h1 {
    font-size: var(--text-h1);
}

h2 {
    font-size: var(--text-h2);
}

h3 {
    font-size: var(--text-h3);
}

a,
span {
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
}

a {
    text-decoration: none;
}

ul {
    list-style-type: none;
}

img {
    max-width: 100%;
    height: auto;
    display: block;
}

.eyebrow {
    font-family: var(--font-mono);
    font-size: var(--text-eyebrow);
    font-weight: 400;
    letter-spacing: var(--letter-spacing-eyebrow);
    text-transform: uppercase;
    color: var(--muted);
}

.main-container {
    margin: 0 auto;
    padding: 0 clamp(1.25rem, 4vw, 2rem);
    max-width: var(--max-width);
    width: 100%;
}

section {
    padding: var(--section-padding) 0;
}

.icon {
    display: inline-block;
    vertical-align: -0.15em;
    flex-shrink: 0;
}

.social-link a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
}

.social-link a i,
.social-link a svg,
.social-link a .icon {
    color: var(--muted);
    transition: color 0.2s ease;
}

.social-link a:hover i,
.social-link a:hover svg,
.social-link a:hover .icon,
.social-link a i:hover,
.social-link a svg:hover {
    color: var(--accent);
}

:focus {
    outline: none;
}

a:focus-visible,
button:focus-visible,
[role="button"]:focus-visible,
.toggle:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: no-preference) {
    .reveal {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 400ms ease, transform 400ms ease;
    }

    .reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    html {
        scroll-behavior: auto;
    }

    .reveal {
        opacity: 1;
        transform: none;
    }
}

header {
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    height: 4rem;
    padding: 0 max(clamp(1.25rem, 4vw, 2rem), calc((100% - var(--max-width)) / 2));
    background-color: var(--paper);
    border-bottom: 1px solid var(--line);
}

.header-logo {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--ink);
    padding: 0.5rem 0;
    transition: color 0.2s ease;
    min-width: 0;
    max-width: 70%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.header-logo:hover {
    color: var(--accent);
}

.nav {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

#theme-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2.25rem;
    height: 2.25rem;
    padding: 0;
    border: 1px solid var(--line);
    border-radius: 999px;
    background-color: var(--surface);
    color: var(--ink);
    cursor: pointer;
    transition: background-color 200ms ease, color 200ms ease, border-color 200ms ease;
}

#theme-toggle .theme-icon {
    width: 1.125rem;
    height: 1.125rem;
}

#theme-toggle .theme-icon-sun {
    display: none;
}

#theme-toggle .theme-icon-moon {
    display: block;
}

html[data-theme="dark"] #theme-toggle .theme-icon-sun {
    display: block;
}

html[data-theme="dark"] #theme-toggle .theme-icon-moon {
    display: none;
}

.nav-menu {
    list-style-type: none;
}

.nav-item {
    text-align: center;
}

.nav-item a {
    display: block;
    font-family: var(--font-body);
    font-size: 0.9375rem;
    font-weight: 500;
    letter-spacing: 0.01em;
    color: var(--muted);
    padding: 0.75rem 1rem;
    position: relative;
    transition: color 0.2s ease;
}

.nav-item a:hover,
.nav-item a:focus {
    color: var(--ink);
}

.nav-item a.is-current {
    color: var(--ink);
    font-weight: 600;
}

.nav-item a.is-current::after {
    content: '';
    position: absolute;
    left: 1rem;
    right: 1rem;
    bottom: 0.35rem;
    height: 2px;
    background-color: var(--gold);
    border-radius: 1px;
}

.toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    cursor: pointer;
    color: var(--ink);
}

.toggle i,
.toggle svg,
.toggle .icon {
    font-size: 1.5rem;
}

@media only screen and (max-width: 767px) {
    .nav-item {
        display: none;
    }

    .nav-menu {
        position: fixed;
        top: 4rem;
        left: 0;
        right: 0;
        width: 100%;
        flex-direction: column;
        align-items: stretch;
        padding: 0.75rem 0;
        background-color: var(--paper);
        border-bottom: 1px solid var(--line);
        box-shadow: 0 4px 12px color-mix(in srgb, var(--ink) 4%, transparent);
        z-index: 99;
    }

    .nav-menu.active {
        display: flex;
    }

    .nav-menu.active .nav-item {
        display: block;
    }

    .nav-menu.active .nav-item a {
        padding: 0.75rem 1.5rem;
    }

    .nav-menu.active .nav-item a.is-current::after {
        left: 1.5rem;
        right: 1.5rem;
    }
}

@media only screen and (min-width: 768px) {
    .toggle {
        display: none;
    }

    .nav-menu {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.25rem;
    }
}

.home-section .hero {
    min-height: auto;
    padding-top: clamp(2rem, 6vw, 4rem);
    padding-bottom: var(--section-padding);
    display: grid;
    gap: clamp(2rem, 5vw, 3.5rem);
    align-items: center;
}

.home-section .hero-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}

.home-section .hero-title {
    margin-top: 0.75rem;
    overflow-wrap: anywhere;
}

.home-section .hero-subline {
    font-family: var(--font-body);
    font-size: clamp(1rem, 2.5vw, 1.125rem);
    line-height: var(--line-height-body);
    color: var(--muted);
    margin-top: 0.75rem;
    max-width: 32rem;
}

.home-section .hero-socials {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.25rem;
    margin-top: 1.5rem;
}

.home-section .hero-socials a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
}

.home-section .hero-socials a i,
.home-section .hero-socials a svg,
.home-section .hero-socials a .icon {
    font-size: 1.25rem;
}

.home-section .hero-image-card {
    width: 100%;
    max-width: 22rem;
    margin: 0 auto;
    border: 1px solid var(--line);
    border-radius: 0.75rem;
    overflow: hidden;
    background-color: var(--surface);
}

.home-section .hero-image-card img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

@media only screen and (min-width: 768px) {
    .home-section .hero {
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
    }

    .home-section .hero-image-card {
        margin: 0 0 0 auto;
        max-width: none;
    }
}

@media only screen and (max-width: 359px) {
    .home-section .hero-socials a {
        width: 2.25rem;
        height: 2.25rem;
    }
}
