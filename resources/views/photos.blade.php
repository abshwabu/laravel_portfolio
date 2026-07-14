@extends('layouts.app')
@section('title')
    Photos
@endsection
@section('content')
    <h1>Certificates & Photos</h1>

    <div class="photo-cards-container">
        @foreach ($photos as $photo)
            <div class="card">
                <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}" class="card-preview-img"
                    loading="lazy">
                <div class="project-card-info">
                    <h3 class="project-title">{{ $photo->title }}</h3>
                </div>
            </div>
        @endforeach
    </div>
@endsection
