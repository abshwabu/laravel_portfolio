@extends('layouts.app')
@section('title')
    About us
@endsection
@section('content')


    <main class="main-container about-section">
        <section id="profile">
            <div class="page-title">
                <img src="./img/smile.svg" alt="Smile Icon">
                <h2>{{ $user->username }}.<span class="pink">profile</span></h2>
            </div>
            <p class="section-description">
                Hello
            </p>
            <div class="profile-container">
                <div class="me-icon-container">
                    <img class="me-icon" src="{{ asset('storage/' . $user->hero_gif) }}" alt="{{ $user->name }}"
                        title="That is me">
                </div>
                <div class="terminal-container">
                    <div class="terminal-header">
                        <div id="terminal-title">{{ $user->username }}.exe</div>
                        <div class="right-side-buttons">
                            <i class="fa-solid fa-window-minimize"></i>
                            <i class="fa-solid fa-window-restore"></i>
                            <i class="fa-solid fa-window-close"></i>
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
        <section id="education-experience">
            <div id="education" class="education-experience-container">
                <h3>Education</h3>
                @foreach ($educations as $education)
                    <div class="education-experience-card">
                        <div class="card-info">
                            <h4 class="green">{{ $education->education_degree }}</h4>
                            <p>{{ $education->education_location }}</p>
                        </div>
                        <div class="card-description">
                            <h5>Achievements</h5>
                            @php
                                $achievements = explode(',', $education->achievements);
                            @endphp
                            @foreach ($achievements as $achievement)
                                <p>{{ $achievement }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="experience" class="education-experience-container">
                <h3>Experience</h3>
                @foreach ($experiences as $experience)
                    <div class="education-experience-card">
                        <div class="card-info">
                            <h4 class="green">{{ $experience->role }}</h4>
                            <p class="date">{{ $experience->start_date }} - {{ $experience->end_date }}</p>
                            <p><a href="{{ $experience->company_url }}">{{ $experience->company }}</a><br>
                                {{ $experience->job_type }}</p>
                        </div>
                        <div class="card-description">
                            @foreach ($experience->tasks as $task)
                                <h5>{{ $task->title }}</h5>
                                @php
                                $tasks = explode('.', $task->tasks);
                            @endphp
                            @foreach ($tasks as $task)
                                <p>{{ $task }}</p>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="tech-stack">
            <h3>Tech Stack</h3>
            <div class="tech-stack-container">
                @foreach ($techstasks as $item)
                        <div class="skill">
                            <img style="width: 35px;height: 35px; object-fit: scale-down; margin-right: 5px;"
                                src="{{ asset('storage/' . $item->image) }}"
                                alt="{{ $item->name }}"><a href="{{ $item->url }}">{{ $item->name }}</a>
                        </div>
                @endforeach
            </div>
        </section>
        <section id="all-social-media">
            <h3>Contact</h3>
            <p class="section-description">
                Here are all the places you can find me on the
                internet.
            </p>
            <div class="social-media-list">
                <a href="{{ $user->linkedin_url }}" class="social-media-item" target="_blank">
                    <i class="fa-brands fa-linkedin-in"></i>
                    LinkedIn
                </a>
                <a href="{{ $user->github_url }}" class="social-media-item" target="_blank">
                    <i class="fa-brands fa-github"></i>
                    GitHub
                </a>
                <a href="{{ $user->instagram_url }}" class="social-media-item" target="_blank">
                    <i class="fa-brands fa-telegram"></i>
                    Telegram
                </a>
                <a href="mailto:{{ $user->email }}" class="social-media-item" >
                    <i class="fa-solid fa-envelope"></i>
                    Email
                </a>
            </div>
        </section>
    </main>
@endsection
