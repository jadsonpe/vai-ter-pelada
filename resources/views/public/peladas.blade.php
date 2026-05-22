<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Peladas abertas</h1>
                <p class="mt-2 text-slate-600">Encontre peladas ativas e rodadas programadas para os próximos 7 dias.</p>
            </div>
            <a href="{{ route('organizador.peladas.create') }}" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white sm:w-auto">Criar pelada</a>
        </div>

        <form method="GET" action="{{ route('peladas.index') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
            <div>
                <label for="cidade" class="text-sm font-medium text-slate-700">Cidade</label>
                <select id="cidade" name="cidade" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="">Todas</option>
                    @foreach($cidades as $cidade)
                        <option value="{{ $cidade }}" @selected(($filtros['cidade'] ?? '') === $cidade)>{{ $cidade }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bairro" class="text-sm font-medium text-slate-700">Bairro</label>
                <select id="bairro" name="bairro" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="">Todos</option>
                    @foreach($bairros as $bairro)
                        <option value="{{ $bairro }}" @selected(($filtros['bairro'] ?? '') === $bairro)>{{ $bairro }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="esporte_id" class="text-sm font-medium text-slate-700">Esporte</label>
                <select id="esporte_id" name="esporte_id" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="">Todos</option>
                    @foreach($esportes as $esporte)
                        <option value="{{ $esporte->id }}" @selected((string) ($filtros['esporte_id'] ?? '') === (string) $esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row md:items-end">
                <button class="w-full rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Filtrar</button>
                <a href="{{ route('peladas.index') }}" class="inline-flex w-full items-center justify-center rounded-md border border-slate-300 px-4 py-2 font-semibold text-slate-700 sm:w-auto">Limpar</a>
            </div>
        </form>

        <section class="mt-10">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Rodadas nos próximos 7 dias</h2>
                <span class="text-sm font-semibold text-slate-500">{{ $rodadas->count() }} encontradas</span>
            </div>

            <div class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white shadow-sm">
                @forelse($rodadas as $rodada)
                    @php($capacidade = $rodada->vagas_totais ?: $rodada->capacidade ?: $rodada->pelada->vagas_totais ?: $rodada->pelada->capacidade)
                    <a href="{{ route('peladas.show', $rodada->pelada) }}" class="grid gap-3 p-4 hover:bg-slate-50 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-slate-900">{{ $rodada->titulo }}</h3>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $rodada->pelada->esporte->nome }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $rodada->pelada->nome }} - {{ $rodada->pelada->local_nome ?: $rodada->pelada->local }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $rodada->pelada->bairro }} {{ $rodada->pelada->cidade ? '- '.$rodada->pelada->cidade : '' }}</p>
                        </div>
                        <div class="text-sm md:text-right">
                            <p class="font-semibold text-slate-900">{{ $rodada->data_hora->format('d/m/Y H:i') }}</p>
                            <p class="mt-1 text-slate-500">{{ $rodada->confirmados_count }}/{{ $capacidade }} confirmados</p>
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-600">Nenhuma rodada cadastrada para os próximos 7 dias com esses filtros.</p>
                @endforelse
            </div>
        </section>

        <section class="mt-10">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Todas as peladas ativas</h2>
                <span class="text-sm font-semibold text-slate-500">{{ $peladas->total() }} encontradas</span>
            </div>

            <div class="mt-4 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse($peladas as $pelada)
                    <a href="{{ route('peladas.show', $pelada) }}" class="flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm hover:border-emerald-300">
                        <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" />
                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-sm font-medium text-emerald-700">{{ $pelada->esporte->nome }}</p>
                            <h3 class="mt-1 text-xl font-semibold text-slate-900">{{ $pelada->nome }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $pelada->bairro }} {{ $pelada->cidade ? '- '.$pelada->cidade : '' }}</p>
                            <div class="mt-auto flex flex-wrap gap-2 pt-4 text-xs font-semibold">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">{{ $pelada->vagas_totais ?: $pelada->capacidade }} vagas</span>
                                @if($pelada->aceita_diarista)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">Aceita diarista</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-slate-600 md:col-span-2 lg:col-span-3">Nenhuma pelada ativa encontrada com esses filtros.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $peladas->links() }}</div>
        </section>
    </div>
</x-app-layout>
