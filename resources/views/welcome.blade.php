@extends('layouts.app')
@section('title')
    Home
@endsection
@section('content')
    <main class="main-container home-section">
        <section id="home" class="hero">
            <div class="hero-content">
                <p class="eyebrow">$ whoami</p>
                <h1 class="hero-title reveal">{{ $user->name }}</h1>
                <p class="hero-subline">{{ $user->title }}</p>
                <ul class="hero-socials">
                    <li class="social-link">
                        <a href="{{ $user->linkedin_url }}" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </li>
                    <li class="social-link">
                        <a href="{{ $user->github_url }}" aria-label="GitHub" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </li>
                    <li class="social-link">
                        <a href="{{ $user->instagram_url }}" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-telegram"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="hero-image-card">
                <img src="{{ asset('storage/' . $user->hero_gif) }}" alt="{{ $user->name }}">
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
                        data-modal-description="{{ $project->description }}"
                        data-modal-tags="{{ $project->keyword }}"
                        data-modal-url="{{ $project->url }}"
                        data-modal-github="{{ $project->github_url }}">
                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }} preview"
                            class="card-preview-img" loading="lazy">
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
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="{{ $project->github_url }}" aria-label="View {{ $project->title }} on GitHub"
                                    target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">
                                    <i class="fa-brands fa-github"></i>
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
            <img class="modal-image" src="" alt="">
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
