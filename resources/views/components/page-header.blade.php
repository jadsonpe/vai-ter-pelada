@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-6 shadow-sm']) }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            @if($eyebrow)
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ $eyebrow }}</p>
            @endif
            <h1 class="{{ $eyebrow ? 'mt-2' : '' }} text-3xl font-bold text-slate-950">{{ $title }}</h1>
            @if($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-col gap-2 sm:flex-row">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
