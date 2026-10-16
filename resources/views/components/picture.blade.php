@props([
    'src',
    'alt' => '',
    'class' => null,
    'width' => null,
    'height' => null,
    'widths' => [400, 800, 1200],
    'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'decoding' => 'async',
    'id' => null,
    'title' => null,
])

@php
    $cleanSrc = ltrim(parse_url($src, PHP_URL_PATH) ?? $src, '/');
    if (!str_starts_with($cleanSrc, 'storage/') && !str_starts_with($cleanSrc, 'img/')) {
        if (file_exists(public_path('storage/' . $cleanSrc))) {
            $cleanSrc = 'storage/' . $cleanSrc;
        } elseif (file_exists(public_path('img/' . $cleanSrc))) {
            $cleanSrc = 'img/' . $cleanSrc;
        } elseif (file_exists(public_path('img/project-preview/' . $cleanSrc))) {
            $cleanSrc = 'img/project-preview/' . $cleanSrc;
        }
    }

    $dir = pathinfo($cleanSrc, PATHINFO_DIRNAME);
    $name = pathinfo($cleanSrc, PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($cleanSrc, PATHINFO_EXTENSION));

    $avifCandidates = [];
    $webpCandidates = [];

    if ($ext !== 'svg') {
        foreach ($widths as $w) {
            $avifRel = "{$dir}/{$name}-{$w}.avif";
            if (file_exists(public_path($avifRel))) {
                $avifCandidates[] = asset($avifRel) . " {$w}w";
            }
            $webpRel = "{$dir}/{$name}-{$w}.webp";
            if (file_exists(public_path($webpRel))) {
                $webpCandidates[] = asset($webpRel) . " {$w}w";
            }
        }

        if (empty($avifCandidates) && file_exists(public_path("{$dir}/{$name}.avif"))) {
            $avifCandidates[] = asset("{$dir}/{$name}.avif");
        }
        if (empty($webpCandidates) && file_exists(public_path("{$dir}/{$name}.webp"))) {
            $webpCandidates[] = asset("{$dir}/{$name}.webp");
        }
    }

    $hasPictureSources = !empty($avifCandidates) || !empty($webpCandidates);
@endphp

@if ($hasPictureSources)
    <picture>
        @if (!empty($avifCandidates))
            <source type="image/avif" srcset="{{ implode(', ', $avifCandidates) }}" @if($sizes && count($avifCandidates) > 1) sizes="{{ $sizes }}" @endif>
        @endif
        @if (!empty($webpCandidates))
            <source type="image/webp" srcset="{{ implode(', ', $webpCandidates) }}" @if($sizes && count($webpCandidates) > 1) sizes="{{ $sizes }}" @endif>
        @endif
        <img src="{{ asset($cleanSrc) }}"
             alt="{{ $alt }}"
             @if($class) class="{{ $class }}" @endif
             @if($width) width="{{ $width }}" @endif
             @if($height) height="{{ $height }}" @endif
             @if($loading) loading="{{ $loading }}" @endif
             @if($decoding) decoding="{{ $decoding }}" @endif
             @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
             @if($id) id="{{ $id }}" @endif
             @if($title) title="{{ $title }}" @endif
             {{ $attributes }}>
    </picture>
@else
    <img src="{{ asset($cleanSrc) }}"
         alt="{{ $alt }}"
         @if($class) class="{{ $class }}" @endif
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
         @if($loading) loading="{{ $loading }}" @endif
         @if($decoding) decoding="{{ $decoding }}" @endif
         @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
         @if($id) id="{{ $id }}" @endif
         @if($title) title="{{ $title }}" @endif
         {{ $attributes }}>
@endif
