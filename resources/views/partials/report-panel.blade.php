@props([
    'title',
    'description',
    'action' => null,
    'reasons' => [],
    'dark' => false,
    'loginRedirect' => url()->current(),
])

@php
    $panelClass = $dark
        ? 'border-white/10 bg-white/[0.06] text-white shadow-xl shadow-slate-950/20'
        : 'border-red-100 bg-white text-slate-950 shadow-sm';
    $mutedClass = $dark ? 'text-slate-400' : 'text-slate-600';
    $fieldClass = 'border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 focus:border-red-300 focus:ring-red-300';
    $isOpen = $errors->has('reason') || $errors->has('description');
@endphp

<details @if($isOpen) open @endif {{ $attributes->merge(['class' => 'group rounded-lg border '.$panelClass]) }}>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-3 marker:hidden">
        <span class="flex min-w-0 items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $dark ? 'bg-red-400/15 text-red-100 ring-1 ring-red-300/20' : 'bg-red-50 text-red-700 ring-1 ring-red-100' }} transition group-hover:scale-105">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3 2.8 17.3A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.7l-7.5-13a2 2 0 0 0-3.4 0Z" />
                </svg>
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-black">{{ $title }}</span>
                <span class="block truncate text-xs {{ $mutedClass }}">Clique para abrir</span>
            </span>
        </span>
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $dark ? 'bg-white/5 text-slate-300' : 'bg-slate-50 text-slate-500' }} transition group-open:rotate-45 group-open:text-red-600" aria-hidden="true">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" d="M12 5v14M5 12h14" />
            </svg>
        </span>
    </summary>

    <div class="border-t {{ $dark ? 'border-white/10' : 'border-slate-100' }} p-5 pt-4">
        <p class="text-sm {{ $mutedClass }}">{{ $description }}</p>

    @auth
        <form method="POST" action="{{ $action }}" class="mt-4 space-y-3">
            @csrf

            <div>
                <label class="text-sm font-semibold {{ $dark ? 'text-slate-200' : 'text-slate-700' }}">Motivo</label>
                <select name="reason" required class="mt-1 w-full rounded-md text-sm {{ $fieldClass }}">
                    <option class="bg-white text-slate-900" value="">Selecione um motivo</option>
                    @foreach($reasons as $value => $label)
                        <option class="bg-white text-slate-900" value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
            </div>

            <div>
                <label class="text-sm font-semibold {{ $dark ? 'text-slate-200' : 'text-slate-700' }}">Explique o que aconteceu</label>
                <textarea name="description" rows="4" class="mt-1 w-full rounded-md text-sm {{ $fieldClass }}" placeholder="Descreva o problema com detalhes. Evite expor dados sensiveis de terceiros.">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs {{ $mutedClass }}">Opcional, mas ajuda a equipe a analisar com mais rapidez.</p>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <button class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                Enviar denúncia
            </button>
        </form>
    @else
        <div class="mt-4 rounded-md {{ $dark ? 'bg-slate-900/70 text-slate-300' : 'bg-slate-50 text-slate-700' }} p-4 text-sm">
            Apenas jogadores cadastrados podem enviar denuncias.
            <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('register', ['redirect' => $loginRedirect]) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                    Criar conta
                </a>
                <a href="{{ route('login', ['redirect' => $loginRedirect]) }}" class="inline-flex items-center justify-center rounded-md border {{ $dark ? 'border-white/10 text-slate-200 hover:bg-white/10' : 'border-slate-300 text-slate-700 hover:bg-slate-100' }} px-4 py-2 text-sm font-bold">
                    Entrar
                </a>
            </div>
        </div>
    @endauth
    </div>
</details>
