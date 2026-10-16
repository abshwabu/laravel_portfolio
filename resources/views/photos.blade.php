@extends('layouts.app')
@section('title')
    Certificates
@endsection
@section('content')
    <main class="main-container photos-section">
        <section class="photos">
            <p class="eyebrow">$ cat certificates.log</p>
            <h1 class="reveal page-heading">Certificates</h1>
            <div class="photo-cards-container">
                @foreach ($photos as $photo)
                    @if (! empty($photo->url))
                        <a href="{{ $photo->url }}" class="photo-card-link" target="_blank" rel="noopener noreferrer"
                            aria-label="View {{ $photo->title }}">
                            <article class="card reveal">
                                <x-picture
                                    :src="'storage/' . $photo->image"
                                    :alt="$photo->title"
                                    class="card-preview-img"
                                    width="400"
                                    height="225"
                                    :widths="[400, 800, 1200]"
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <div class="photo-card-info">
                                    <h2 class="photo-title">{{ $photo->title }}</h2>
                                </div>
                            </article>
                        </a>
                    @else
                        <article class="card photo-card-static reveal">
                            <x-picture
                                :src="'storage/' . $photo->image"
                                :alt="$photo->title"
                                class="card-preview-img"
                                width="400"
                                height="225"
                                :widths="[400, 800, 1200]"
                                sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px"
                                loading="lazy"
                                decoding="async"
                            />
                            <div class="photo-card-info">
                                <h2 class="photo-title">{{ $photo->title }}</h2>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    </main>
@endsection
