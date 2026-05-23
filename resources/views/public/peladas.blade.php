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
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Peladas agendadas para os próximos 7 dias</h2>
                    <p class="mt-1 text-sm text-slate-500">Ache uma perto de você e solicite participação.</p>
                </div>
                <span class="text-sm font-semibold text-slate-500">{{ $rodadas->total() }} encontradas</span>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($rodadas as $rodada)
                    @php($capacidade = $rodada->vagas_totais ?: $rodada->capacidade ?: $rodada->pelada->vagas_totais ?: $rodada->pelada->capacidade)
                    @php($ocupacao = $capacidade ? min(100, round(($rodada->confirmados_count / $capacidade) * 100)) : 0)
                    <a href="{{ route('peladas.show', $rodada->pelada) }}" class="group block overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">{{ $rodada->pelada->esporte->nome }}</span>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ ucfirst($rodada->status) }}</span>
                        </div>

                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $rodada->pelada->nome }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $rodada->titulo }}</p>
                        <p class="mt-3 text-sm text-slate-500">{{ $rodada->pelada->local_nome ?: $rodada->pelada->local }}</p>
                        <p class="text-sm text-slate-500">{{ $rodada->pelada->bairro }}{{ $rodada->pelada->cidade ? ' · '.$rodada->pelada->cidade : '' }}</p>

                        <div class="mt-5 flex items-center justify-between gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $rodada->data_hora->format('d/m/Y') }}</p>
                                <p class="text-slate-500">{{ $rodada->data_hora->format('H:i') }}</p>
                            </div>
                            <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $rodada->confirmados_count }}/{{ $capacidade }} confirmados</div>
                        </div>

                        <div class="mt-4 rounded-full bg-slate-100 h-2.5 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $ocupacao }}%"></div>
                        </div>
                        <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-500">Ocupação {{ $ocupacao }}%</p>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-600">Nenhuma rodada cadastrada para os próximos 7 dias com esses filtros.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $rodadas->links() }}</div>
        </section>

        <section class="mt-10">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Todas as peladas ativas</h2>
                    <p class="mt-1 text-sm text-slate-500">Clique nas peladas e solicite participação ao organizador.</p>
                </div>
                <span class="text-sm font-semibold text-slate-500">{{ $peladas->total() }} encontradas</span>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($peladas as $pelada)
                    <a href="{{ route('peladas.show', $pelada) }}" class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                        <x-pelada-imagem variant="card" :src="$pelada->imagemUrl()" :alt="$pelada->nome" class="h-28 sm:h-24" />
                        <div class="flex flex-1 flex-col gap-2 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ $pelada->esporte->nome }}</p>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $pelada->nome }}</h3>
                            <p class="text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>
                            <p class="text-sm text-slate-500">{{ $pelada->bairro }}{{ $pelada->cidade ? ' · '.$pelada->cidade : '' }}</p>
                            <div class="mt-auto flex flex-wrap gap-2 pt-3 text-xs font-semibold">
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
