<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Arenas</h1>
        <p class="mt-2 text-slate-600">Locais onde já existem peladas cadastradas.</p>

        <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($arenas as $arena)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $arena->local_nome }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $arena->bairro }} {{ $arena->cidade ? '- '.$arena->cidade : '' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $arena->endereco }}</p>
                    @if($mapsUrl = $arena->mapsUrl())
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                            Ver no mapa
                        </a>
                    @endif
                </article>
            @empty
                <p class="text-slate-600">Nenhuma arena cadastrada ainda.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
