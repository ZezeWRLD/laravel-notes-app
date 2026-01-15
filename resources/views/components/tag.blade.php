@props(['size' => 'base', 'href' => null])

@php
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'base' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ][$size] ?? $sizeClasses['base'];

    $bgColor = $tag->color ?? '#3b82f6';
    $classes = "inline-flex items-center rounded-full font-medium hover:opacity-80 transition-opacity duration-200 {$sizeClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'style' => "background-color: {$bgColor}20; color: {$bgColor}"]) }}>
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes, 'style' => "background-color: {$bgColor}20; color: {$bgColor}"]) }}>
        {{ $slot }}
    </span>
@endif
