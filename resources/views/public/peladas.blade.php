<x-app-layout>
    @php
        $sportIcon = fn (string $name): string => match (strtolower($name)) {
            'futebol', 'futebol society' => 'futebol_flat.png',
            'society' => 'society_flat.png',
            'basquete', 'basket' => 'basquete_flat.png',
            'vôlei', 'volei' => 'volei_flat.png',
            default => 'ui_player.png',
        };

        $formatBRL = fn ($value): string => $value !== null && $value !== '' ? 'R$ '.number_format($value, 2, ',', '.') : '-';
    @endphp
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-lg sm:px-8">
            <p class="text-sm uppercase tracking-[0.3em] text-emerald-400">Peladas</p>
            <h1 class="mt-4 text-4xl font-semibold tracking-tight">Encontre a pelada certa</h1>
            <p class="mt-4 max-w-2xl text-slate-300">Filtre por esporte, cidade, bairro e faixa de preço para achar peladas e rodadas com o melhor custo-benefício.</p>

            <div class="mt-8 grid gap-3 sm:grid-cols-2">
                <div class="rounded-3xl bg-white/5 p-4">
                    <p class="text-sm text-slate-400">Peladas ativas</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ $peladas->total() }}</p>
                </div>
                <div class="rounded-3xl bg-white/5 p-4">
                    <p class="text-sm text-slate-400">Rodadas nos próximos 7 dias</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ $rodadas->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Filtro rápido por esporte (select) -->
        <div class="mt-8">
            <form method="GET" action="{{ route('peladas.index') }}" class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-700">Esporte:</span>

                @foreach(request()->except('esporte_id') as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $item)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $item }}" />
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                    @endif
                @endforeach

                @php
                    $esportesList = $esportes ?? App\Models\Esporte::where('ativo', true)->orderBy('nome')->get();
                @endphp
                <select name="esporte_id" onchange="this.form.submit()" class="mt-0 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
                    <option value="" @if(!request('esporte_id')) selected @endif>Todos</option>
                    @foreach($esportesList as $esporte)
                        <option value="{{ $esporte->id }}" @selected((string)request('esporte_id') === (string)$esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Painel expansível de filtros -->
        <div class="mt-8">
            <button type="button" id="filter-toggle" class="flex w-full items-center justify-between rounded-2xl bg-emerald-600 px-6 py-3 text-white font-semibold transition hover:bg-emerald-500">
                <span>Mais filtros</span>
                <svg id="filter-icon" class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button>

            <div id="filter-panel" class="hidden mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Filtros avançados</p>
                        <p class="mt-1 text-sm text-slate-500">Refine sua busca em tempo real.</p>
                    </div>
                    <a href="{{ route('peladas.index') }}" class="text-sm font-semibold text-emerald-600">Limpar todos</a>
                </div>

                <form method="GET" action="{{ route('peladas.index') }}" class="space-y-4">
                    <input type="hidden" name="esporte_id" value="{{ request('esporte_id') }}" />

                    <div>
                        <label for="q" class="text-sm font-medium text-slate-700">Buscar por nome, local ou bairro</label>
                        <input id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Digite para buscar..." class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900" autocomplete="off" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="cidade" class="text-sm font-medium text-slate-700">Cidade</label>
                            <select id="cidade" name="cidade" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
                                <option value="">Todas</option>
                                @foreach($cidades as $cidade)
                                    <option value="{{ $cidade }}" @selected(($filtros['cidade'] ?? '') === $cidade)>{{ $cidade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="bairro" class="text-sm font-medium text-slate-700">Bairro</label>
                            <select id="bairro" name="bairro" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
                                <option value="">Todos</option>
                                @foreach($bairros as $bairro)
                                    <option value="{{ $bairro }}" @selected(($filtros['bairro'] ?? '') === $bairro)>{{ $bairro }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="price_type" class="text-sm font-medium text-slate-700">Tipo de preço</label>
                            <select id="price_type" name="price_type" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
                                <option value="both" @selected(($filtros['price_type'] ?? 'both') === 'both')>Ambos</option>
                                <option value="mensalista" @selected(($filtros['price_type'] ?? '') === 'mensalista')>Mensalista</option>
                                <option value="diarista" @selected(($filtros['price_type'] ?? '') === 'diarista')>Diarista</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort" class="text-sm font-medium text-slate-700">Ordenar</label>
                            <select id="sort" name="sort" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900">
                                <option value="">Padrão</option>
                                <option value="price_asc" @selected(($filtros['sort'] ?? '') === 'price_asc')>Preço: menor primeiro</option>
                                <option value="price_desc" @selected(($filtros['sort'] ?? '') === 'price_desc')>Preço: maior primeiro</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="price_min" class="text-sm font-medium text-slate-700">Preço mínimo</label>
                            <input id="price_min" name="price_min" type="number" step="0.01" value="{{ $filtros['price_min'] ?? '' }}" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900" />
                        </div>
                        <div>
                            <label for="price_max" class="text-sm font-medium text-slate-700">Preço máximo</label>
                            <input id="price_max" name="price_max" type="number" step="0.01" value="{{ $filtros['price_max'] ?? '' }}" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900" />
                        </div>
                    </div>

                    <button class="mt-4 w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">Aplicar filtros</button>
                </form>
            </div>
        </div>

        @php
            $activeFilters = collect($filtros)
                ->filter(fn($value, $key) => $value !== null && $value !== '')
                ->mapWithKeys(function ($value, $key) {
                    return match ($key) {
                        'q' => ['Busca' => $value],
                        'cidade' => ['Cidade' => $value],
                        'bairro' => ['Bairro' => $value],
                        'esporte_id' => ['Esporte' => optional(App\Models\Esporte::find($value))->nome ?: 'Selecionado'],
                        'price_type' => ['Tipo' => $value === 'both' ? 'Ambos' : ucfirst($value)],
                        'price_min' => ['Preço mínimo' => "R$ {$value}"],
                        'price_max' => ['Preço máximo' => "R$ {$value}"],
                        'sort' => ['Ordenação' => $value === 'price_asc' ? 'Menor preço' : 'Maior preço'],
                        default => [$key => $value],
                    };
                });
        @endphp

        @if($activeFilters->isNotEmpty())
            <div class="mt-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-700">Filtros aplicados</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($activeFilters as $label => $value)
                        <span class="rounded-full bg-white px-3 py-1 text-sm font-medium text-slate-700 shadow-sm">{{ $label }}: {{ $value }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <script>
            document.getElementById('filter-toggle').addEventListener('click', function() {
                const panel = document.getElementById('filter-panel');
                const icon = document.getElementById('filter-icon');
                panel.classList.toggle('hidden');
                icon.style.transform = panel.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        </script>

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
                    <a href="{{ route('peladas.show', $rodada->pelada) }}" class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                        <x-pelada-imagem variant="card" :src="$rodada->pelada->imagemUrl()" :alt="$rodada->pelada->nome" class="h-32 sm:h-28" />
                        <div class="flex flex-col gap-3 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">
                                    <img src="{{ asset('assets/img/icons/'.$sportIcon($rodada->pelada->esporte->nome)) }}" alt="{{ $rodada->pelada->esporte->nome }}" class="h-4 w-4" />
                                    {{ $rodada->pelada->esporte->nome }}
                                </span>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ ucfirst($rodada->status) }}</span>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $rodada->pelada->nome }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $rodada->titulo }}</p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 text-sm">
                                <div>
                                    <p class="text-slate-500">Local</p>
                                    <p class="mt-0.5 font-semibold text-slate-900">{{ $rodada->pelada->local_nome ?: $rodada->pelada->local }}</p>
                                    <p class="text-slate-500">{{ $rodada->pelada->bairro }}{{ $rodada->pelada->cidade ? ' · '.$rodada->pelada->cidade : '' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Data e hora</p>
                                    <p class="mt-0.5 font-semibold text-slate-900">{{ $rodada->data_hora->format('d/m H:i') }}</p>
                                    <p class="mt-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 inline-block">{{ $rodada->confirmados_count }}/{{ $capacidade }}</p>
                                </div>
                            </div>

                            <div class="mt-2 rounded-full bg-slate-100 h-2 overflow-hidden">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $ocupacao }}%"></div>
                            </div>
                        </div>
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
                        <div class="flex flex-1 flex-col gap-3 p-5">
                            <div class="flex items-center justify-between gap-4">
                                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                    <img src="{{ asset('assets/img/icons/'.$sportIcon($pelada->esporte->nome)) }}" alt="{{ $pelada->esporte->nome }}" class="h-4 w-4" />
                                    {{ $pelada->esporte->nome }}
                                </p>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $pelada->vagas_totais ?: $pelada->capacidade }} vagas</span>
                            </div>

                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $pelada->nome }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>
                                <p class="text-sm text-slate-500">{{ $pelada->bairro }}{{ $pelada->cidade ? ' · '.$pelada->cidade : '' }}</p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Mensalista</p>
                                    <p class="mt-2 text-base font-semibold text-slate-900">{{ $formatBRL($pelada->valor_mensalista) }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Diarista</p>
                                    <p class="mt-2 text-base font-semibold text-slate-900">{{ $formatBRL($pelada->valor_diarista) }}</p>
                                </div>
                            </div>

                            <div class="mt-auto flex flex-wrap gap-2 pt-3 text-xs font-semibold">
                                @if($pelada->aceita_diarista)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">Aceita diarista</span>
                                @endif
                                @if($pelada->requer_aprovacao)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">Requer aprovação</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">Entrada imediata</span>
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
