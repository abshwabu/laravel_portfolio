@extends('layouts.app')
@section('title')
    Photos
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>Certificates & Photos</h1>
            
            <div class="photo-cards-container">
                @foreach($photos as $photo)
                <div class="card">
                    <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}" class="card-preview-img">
                    <div class="project-card-info">
                        <h3 class="project-title">{{ $photo->title }}</h3>
                        <p class="project-description">{{ $photo->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
