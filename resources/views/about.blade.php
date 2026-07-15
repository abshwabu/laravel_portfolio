@extends('layouts.app')
@section('title')
    About
@endsection
@section('content')
    <main class="main-container about-section">
        <section id="profile" class="about-profile">
            <p class="eyebrow">$ cat about.me</p>
            <div class="profile-container">
                <div class="me-icon-container">
                    <img class="me-icon" src="{{ asset('storage/' . $user->hero_gif) }}" alt="{{ $user->name }}"
                        title="That is me" loading="lazy">
                </div>
                <div class="terminal-container">
                    <div class="terminal-header">
                        <div class="terminal-title">{{ $user->username }}.exe</div>
                        <div class="right-side-buttons">
                            <i class="fa-solid fa-window-minimize" aria-hidden="true"></i>
                            <i class="fa-solid fa-window-restore" aria-hidden="true"></i>
                            <i class="fa-solid fa-window-close" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="terminal-window">
                        <div class="statement">
                            <p class="input">
                                {{ $user->username }}.<span class="green">name</span>
                            </p>
                            <p class="return">
                                {{ $user->pronouns }}
                            </p>
                        </div>
                        <div class="statement">
                            <p class="input">
                                {{ $user->username }}.<span class="green">location</span>
                            </p>
                            <p class="return">
                                {{ $user->location }}
                            </p>
                        </div>
                        <div class="statement">
                            <p class="input">
                                {{ $user->username }}.<span class="green">languages</span>
                            </p>
                            <p class="return">
                                [ @foreach ($user->languages as $language)
                                    {{ $language }},
                                @endforeach ]
                            </p>
                        </div>
                        <div class="statement">
                            <p class="input">
                                {{ $user->username }}.<span class="green">hobies</span>
                            </p>
                            <p class="return">
                                [ @foreach ($user->hobbies as $language)
                                    {{ $language }},
                                @endforeach ]
                            </p>
                        </div>
                        <div class="statement">
                            <p class="input bottom"><span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="education-experience" class="about-history">
            <p class="eyebrow">$ history --education --experience</p>
            <div class="history-columns">
                <div id="education" class="education-experience-container">
                    <h3 class="reveal">Education</h3>
                    @foreach ($educations as $education)
                        <article class="education-experience-card reveal">
                            <h4 class="entry-title">{{ $education->education_degree }}</h4>
                            <p class="entry-meta">{{ $education->education_location }}</p>
                            <h5 class="entry-subheading">Achievements</h5>
                            <ul class="entry-list">
                                @php
                                    $achievements = explode(',', $education->achievements);
                                @endphp
                                @foreach ($achievements as $achievement)
                                    @if (trim($achievement))
                                        <li>{{ trim($achievement) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
                <div id="experience" class="education-experience-container experience-column">
                    <h3 class="reveal">Experience</h3>
                    @foreach ($experiences as $experience)
                        <article class="education-experience-card reveal">
                            <h4 class="entry-title">{{ $experience->role }}</h4>
                            <p class="entry-meta entry-date">{{ $experience->start_date }} - {{ $experience->end_date }}</p>
                            <p class="entry-meta">
                                <a class="entry-link" href="{{ $experience->company_url }}">{{ $experience->company }}</a>
                                <span class="entry-meta-separator">·</span>
                                {{ $experience->job_type }}
                            </p>
                            @foreach ($experience->tasks as $taskGroup)
                                <h5 class="entry-subheading">{{ $taskGroup->title }}</h5>
                                @php
                                    $tasks = explode('.', $taskGroup->tasks);
                                @endphp
                                <ul class="entry-list">
                                    @foreach ($tasks as $taskItem)
                                        @if (trim($taskItem))
                                            <li>{{ trim($taskItem) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endforeach
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="tech-stack" class="about-tech">
            <p class="eyebrow">$ ls ./stack</p>
            <h3 class="reveal">Tech Stack</h3>
            <div class="tech-stack-container">
                @foreach ($techstasks as $item)
                    <a href="{{ $item->url }}" class="tech-chip" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" loading="lazy">
                        <span>{{ $item->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section id="all-social-media" class="about-contact">
            <p class="eyebrow">$ contact --list</p>
            <h3 class="reveal">Contact</h3>
            <div class="contact-grid">
                <a href="{{ $user->linkedin_url }}" class="contact-link" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                    <span>LinkedIn</span>
                </a>
                <a href="{{ $user->github_url }}" class="contact-link" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-github" aria-hidden="true"></i>
                    <span>GitHub</span>
                </a>
                <a href="{{ $user->instagram_url }}" class="contact-link" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-telegram" aria-hidden="true"></i>
                    <span>Telegram</span>
                </a>
                <a href="mailto:{{ $user->email }}" class="contact-link">
                    <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                    <span>Email</span>
                </a>
            </div>
        </section>
    </main>
@endsection
