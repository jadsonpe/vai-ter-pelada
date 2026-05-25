@props([
    'user',
    'size' => 'md',
])

@php
    $classes = match ($size) {
        'xs' => 'h-8 w-8 text-xs',
        'sm' => 'h-10 w-10 text-sm',
        'lg' => 'h-16 w-16 text-2xl',
        'xl' => 'h-24 w-24 text-3xl',
        default => 'h-12 w-12 text-base',
    };

    $src = $user?->avatarUrl();
@endphp

<div {{ $attributes->merge(['class' => "{$classes} shrink-0 overflow-hidden rounded-full border border-emerald-200 bg-emerald-100 text-emerald-900 shadow-sm"]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
    @else
        <div class="flex h-full w-full items-center justify-center font-bold">
            {{ $user?->initials() ?: 'J' }}
        </div>
    @endif
</div>
