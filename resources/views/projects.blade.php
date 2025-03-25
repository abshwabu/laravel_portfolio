@extends('layouts.app')
@section('title')
    Home
@endsection
@section('content')
    
        <main class="main-container project-section">
            <section class="projects">
                <div class="page-title">
                    <img src="./img/coding.svg" alt="Code Icon">
                    <h2>{{ $user->username }}.<span class="pink">projects</span></h2>
                </div>
                <p class="section-description">
                    See <a class="hyperlink" href="{{ $user->github_url }}">GitHub</a> profile for more details.
                </p>
                <div class="project-cards-container">
                    @foreach ($projects as $project)
                    <div class="card">
                        <img src="{{ asset('storage/' . $project->image) }}"
                            alt="Project Preview Screenshot" class="card-preview-img">
                        <div class="card-info">
                            <div class="title-and-links">
                                <span class="project-title">
                                    {{ $project->title }}
                                </span>
                                <div class="project-links">
                                    <a href="{{ $project->url }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="{{ $project->github_url }}">
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="project-skills">
                                @php
                                        $keywords = explode(',', $project->keyword);
                                    @endphp
                                    @foreach ($keywords as $keyword)
                                        <span>{{ $keyword }}</span>
                                    @endforeach
                            </div>
                            <p class="project-description">
                                {{ $project->description }}
                            </p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </section>
        </main>
    @endsection
