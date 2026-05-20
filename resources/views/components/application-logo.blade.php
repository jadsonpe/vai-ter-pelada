@props(['alt' => 'Vai Ter Pelada'])

@php($logoPath = public_path('assets/img/logo/vai-ter-pelada-logo-transparente.png'))

@if(file_exists($logoPath))
    <img src="{{ asset('assets/img/logo/vai-ter-pelada-logo-transparente.png') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'h-12 w-auto']) }}>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center font-extrabold uppercase tracking-wide text-emerald-700']) }}>
        Vai Ter Pelada
    </span>
@endif
