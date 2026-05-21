<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Patrocinadores</h1>
        <p class="mt-2 text-slate-600">Marcas que apoiam as peladas da comunidade.</p>

        <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($patrocinadores as $patrocinador)
                <a href="{{ $patrocinador->link ?: $patrocinador->site_url ?: '#' }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-emerald-300">
                    @if($patrocinador->logo ?: $patrocinador->logo_url)
                        <img src="{{ $patrocinador->logo ?: $patrocinador->logo_url }}" alt="{{ $patrocinador->nome }}" class="h-16 w-auto object-contain">
                    @endif
                    <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $patrocinador->nome }}</h2>
                    @if($patrocinador->telefone)
                        <p class="mt-1 text-sm text-slate-500">{{ $patrocinador->telefone }}</p>
                    @endif
                </a>
            @empty
                <p class="text-slate-600">Nenhum patrocinador ativo no momento.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
