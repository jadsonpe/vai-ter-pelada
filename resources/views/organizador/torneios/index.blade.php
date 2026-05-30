<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.index') }}" class="text-sm font-semibold text-emerald-700">Voltar para peladas</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Torneios - {{ $pelada->nome }}</h1>
                <p class="mt-1 text-sm text-slate-600">Crie torneios de futebol, society ou futsal vinculados a esta pelada.</p>
            </div>
            <a href="{{ route('organizador.peladas.torneios.create', $pelada) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Novo torneio</a>
        </div>

        <div class="mt-6 divide-y divide-slate-100 overflow-hidden rounded-lg border border-slate-200 bg-white">
            @forelse($pelada->torneios as $torneio)
                <article class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-950">{{ $torneio->nome }}</h2>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $torneio->formatoLabel() }}</span>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ ucfirst($torneio->status) }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $torneio->data_torneio->format('d/m/Y') }} - {{ $torneio->quantidade_times }} times - {{ $torneio->jogadores_por_time }} por time</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm font-semibold">
                        <a class="text-emerald-700" href="{{ route('organizador.torneios.show', $torneio) }}">Gerenciar</a>
                        <a class="text-emerald-700" href="{{ route('organizador.torneios.edit', $torneio) }}">Editar</a>
                        <a class="text-emerald-700" href="{{ route('torneios.public.show', $torneio) }}" target="_blank" rel="noopener noreferrer">Página pública</a>
                    </div>
                </article>
            @empty
                <div class="p-8 text-center">
                    <p class="font-semibold text-slate-900">Nenhum torneio criado ainda.</p>
                    <p class="mt-1 text-sm text-slate-600">Comece criando um torneio para sortear times e gerar a tabela.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
