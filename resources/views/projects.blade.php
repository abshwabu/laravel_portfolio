@extends('layouts.app')
@section('title')
    Projects
@endsection
@section('content')
    @php
        $keywordMap = [];
        foreach ($projects as $project) {
            foreach (explode(',', $project->keyword) as $keyword) {
                $trimmed = trim($keyword);
                if ($trimmed === '') {
                    continue;
                }
                $key = strtolower($trimmed);
                if (! isset($keywordMap[$key])) {
                    $keywordMap[$key] = $trimmed;
                }
            }
        }
        $uniqueKeywords = array_values($keywordMap);
        sort($uniqueKeywords, SORT_NATURAL | SORT_FLAG_CASE);
    @endphp

    <main class="main-container project-section">
        <section class="projects">
            <p class="eyebrow">$ ls ~/projects --all</p>
            <h2 class="reveal">Projects</h2>
            <p class="section-description">
                See <a class="hyperlink" href="{{ $user->github_url }}">GitHub</a> profile for more details.
            </p>

            <div class="project-filter" id="project-filter">
                <button type="button" class="filter-pill is-active" data-tag="all">All</button>
                @foreach ($uniqueKeywords as $keyword)
                    <button type="button" class="filter-pill"
                        data-tag="{{ strtolower($keyword) }}">{{ $keyword }}</button>
                @endforeach
            </div>

            <div class="project-cards-container">
                @foreach ($projects as $project)
                    @php
                        $projectTags = collect(explode(',', $project->keyword))
                            ->map(fn ($keyword) => strtolower(trim($keyword)))
                            ->filter()
                            ->implode(',');
                    @endphp
                    <article class="card project-card reveal" role="button" tabindex="0"
                        aria-label="View details for {{ $project->title }}"
                        data-tags="{{ $projectTags }}"
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
