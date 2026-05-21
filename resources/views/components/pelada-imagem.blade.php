@props([
    'src' => null,
    'alt' => '',
    'variant' => 'card',
    'empty' => 'Sem imagem',
])

@php
    $wrapper = match ($variant) {
        'preview' => 'aspect-[16/9] w-full max-w-sm overflow-hidden rounded-lg border border-slate-200 bg-slate-100',
        'thumb' => 'aspect-[16/9] w-16 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100',
        default => 'h-48 w-full overflow-hidden bg-slate-100 sm:h-52',
    };
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="block h-full w-full object-cover object-center">
    @else
        <div class="flex h-full w-full items-center justify-center px-2 text-center text-xs text-slate-400">{{ $empty }}</div>
    @endif
</div>
