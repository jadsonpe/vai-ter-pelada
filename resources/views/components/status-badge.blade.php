@props([
    'variant' => 'slate',
])

@php
    $classes = match ($variant) {
        'green', 'active', 'ativo', 'ativa' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'red', 'danger', 'bloqueado', 'encerrada' => 'bg-red-50 text-red-800 ring-red-200',
        'amber', 'warning', 'pendente', 'pausada' => 'bg-amber-50 text-amber-800 ring-amber-200',
        default => 'bg-slate-100 text-slate-700 ring-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {$classes}"]) }}>
    {{ $slot }}
</span>
