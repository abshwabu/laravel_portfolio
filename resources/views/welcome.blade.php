@extends('layouts.app')
@section('title')
    Home
@endsection
@section('content')
    <main class="main-container home-section">
        <section id="home" class="hero">
            <div class="hero-content">
                <p class="eyebrow">$ whoami</p>
                <h1 class="hero-title">{{ $user->name }}</h1>
                <p class="hero-subline">{{ $user->title }}</p>
                <ul class="hero-socials">
                    <li class="social-link">
                        <a href="{{ $user->linkedin_url }}" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                            <x-app-icon name="linkedin" />
                        </a>
                    </li>
                    <li class="social-link">
                        <a href="{{ $user->github_url }}" aria-label="GitHub" target="_blank" rel="noopener noreferrer">
                            <x-app-icon name="github" />
                        </a>
                    </li>
                    <li class="social-link">
                        <a href="{{ $user->instagram_url }}" aria-label="Telegram" target="_blank" rel="noopener noreferrer">
                            <x-app-icon name="telegram" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="hero-image-card">
                <x-picture
                    :src="'storage/' . $user->hero_gif"
                    :alt="$user->name"
                    width="700"
                    height="700"
                    :widths="[400, 700]"
                    sizes="(max-width: 640px) 352px, 450px"
                    loading="eager"
                    fetchpriority="high"
                    decoding="sync"
                />
            </div>
        </section>

        <section class="home-projects" id="select-projects">
            <p class="eyebrow">$ ls ./select-projects</p>
            <h2 class="reveal">Select Projects</h2>
            <p class="section-description">
                Here are some personal projects I have worked on.
                You can find more on <a class="hyperlink" href="{{ $user->github_url }}">GitHub</a>.
            </p>
            <div class="project-cards-container">
                @foreach ($projects as $project)
                    <article class="card project-card reveal" role="button" tabindex="0"
                        aria-label="View details for {{ $project->title }}"
                        data-modal-title="{{ $project->title }}"
                        data-modal-image="{{ $project->image }}"
                        data-modal-images="{{ json_encode($project->all_images_data) }}"
                        data-modal-description="{{ $project->description }}"
                        data-modal-tags="{{ $project->keyword }}"
                        data-modal-url="{{ $project->url }}"
                        data-modal-github="{{ $project->github_url }}">
                        <x-picture
                            :src="'storage/' . $project->image"
                            :alt="$project->title . ' preview'"
                            class="card-preview-img"
                            width="400"
                            height="225"
                            :widths="[400, 800, 1200]"
                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px"
                            loading="lazy"
                            decoding="async"
                        />
                        <div class="project-card-info">
                            <h3 class="project-title">{{ $project->title }}</h3>
                            <div class="project-skills">
                                @foreach (explode(',', $project->keyword) as $keyword)
                                    <span>{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                            <p class="project-description">{{ $project->description }}</p>
                            <div class="project-links">
                                <a href="{{ $project->url }}" aria-label="Visit {{ $project->title }} live site"
                                    target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">
                                    <x-app-icon name="external-link" width="1rem" height="1rem" />
                                </a>
                                <a href="{{ $project->github_url }}" aria-label="View {{ $project->title }} on GitHub"
                                    target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">
                                    <x-app-icon name="github" width="1rem" height="1rem" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="cta-container">
                <a href="/projects" class="cta-button">Explore More</a>
            </div>
        </section>
    </main>

    <div class="modal-overlay" id="project-modal" hidden>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <button type="button" class="modal-close" aria-label="Close">&times;</button>
            <div class="modal-gallery" id="modal-gallery">
                <div class="modal-gallery-viewport">
                    <picture class="modal-picture">
                        <source class="modal-source-avif" type="image/avif" srcset="">
                        <source class="modal-source-webp" type="image/webp" srcset="">
                        <img class="modal-image" src="" alt="Project preview" width="800" height="450" loading="lazy" decoding="async">
                    </picture>
                    <button type="button" class="gallery-nav gallery-prev" aria-label="Previous image" hidden>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button type="button" class="gallery-nav gallery-next" aria-label="Next image" hidden>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <span class="gallery-counter" hidden></span>
                </div>
                <div class="modal-thumbnails" role="tablist" aria-label="Project images" hidden></div>
            </div>
            <h3 class="modal-title" id="modal-title"></h3>
            <div class="modal-tags"></div>
            <p class="modal-description"></p>
            <div class="modal-actions">
                <a href="#" class="modal-button modal-button-live" target="_blank" rel="noopener noreferrer">View Live</a>
                <a href="#" class="modal-button modal-button-github" target="_blank" rel="noopener noreferrer">View Code</a>
            </div>
        </div>
    </div>
@endsection
