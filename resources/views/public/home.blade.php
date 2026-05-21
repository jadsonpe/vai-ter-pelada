<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        @include('shared.status')
    </div>

    <section class="bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[1.2fr_.8fr] lg:px-8">
            <div>
                <x-application-logo class="mb-6 h-24 w-auto sm:h-32" />
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Encontre. Organize. Jogue.</p>
                <h1 class="mt-3 text-4xl font-bold sm:text-5xl">Vai Ter Pelada</h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-300">Crie peladas recorrentes, priorize mensalistas, libere vagas para diaristas, acompanhe fila de espera e sorteie times com quem confirmou presença.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-5 py-3 font-semibold text-slate-950 hover:bg-emerald-400">Ver peladas</a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md border border-white/30 px-5 py-3 font-semibold text-white hover:bg-white/10">Criar conta</a>
                    @endguest
                </div>
            </div>
            <div class="grid gap-3">
                @forelse($banners as $banner)
                    <a href="{{ $banner->link_url ?: '#' }}" class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <p class="text-sm text-emerald-200">{{ $banner->posicao }}</p>
                        <h2 class="mt-1 text-xl font-semibold">{{ $banner->titulo }}</h2>
                    </a>
                @empty
                    <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <p class="text-sm text-emerald-200">Banner</p>
                        <h2 class="mt-1 text-xl font-semibold">Espaco para campanhas e comunicados</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-900">Peladas em destaque</h2>
            <a class="text-sm font-semibold text-emerald-700" href="{{ route('peladas.index') }}">Ver todas</a>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($peladas as $pelada)
                <a href="{{ route('peladas.show', $pelada) }}" class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm hover:border-emerald-300">
                    <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" />
                    <div class="p-5">
                        <p class="text-sm font-medium text-emerald-700">{{ $pelada->esporte->nome }}</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900">{{ $pelada->nome }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>
                        <p class="mt-4 text-sm text-slate-500">Organizador: {{ $pelada->organizador->name }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        @if($patrocinadores->isNotEmpty())
            <div class="mt-10 border-t border-slate-200 pt-6">
                <h2 class="text-lg font-semibold text-slate-900">Patrocinadores</h2>
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach($patrocinadores as $patrocinador)
                        <a class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-700" href="{{ $patrocinador->link ?: $patrocinador->site_url ?: '#' }}">{{ $patrocinador->nome }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-app-layout>
