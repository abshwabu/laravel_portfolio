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
                    <article class="card project-card reveal" data-tags="{{ $projectTags }}">
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
                                    target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="{{ $project->github_url }}" aria-label="View {{ $project->title }} on GitHub"
                                    target="_blank" rel="noopener noreferrer">
                                    <i class="fa-brands fa-github"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
@endsection
