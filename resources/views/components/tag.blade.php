@props(['size' => 'base', 'href' => null])

@php
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'base' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ][$size] ?? $sizeClasses['base'];

    $classes = "inline-flex items-center bg-note-primary/10 text-note-primary rounded-full
                font-medium hover:bg-note-primary/20 transition-colors duration-200 {$sizeClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </span>
@endif
