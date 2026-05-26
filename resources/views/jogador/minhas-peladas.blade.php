<x-app-layout>
    @php
        $statusClasses = [
            'ativo' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            'pendente' => 'bg-amber-50 text-amber-800 ring-amber-200',
            'bloqueado' => 'bg-red-50 text-red-800 ring-red-200',
            'saiu' => 'bg-slate-100 text-slate-700 ring-slate-200',
            'inativo' => 'bg-slate-100 text-slate-700 ring-slate-200',
            'aprovada' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            'recusada' => 'bg-red-50 text-red-800 ring-red-200',
        ];
        $tipoLabels = [
            'mensalista' => 'Mensalista',
            'diarista' => 'Diarista',
            'entrar_pelada' => 'Entrada na pelada',
            'virar_mensalista' => 'Virar mensalista',
        ];
        $totalJogosProximos = $membros->sum(fn ($membro) => $membro->pelada->jogos->count());
        $convitesPendentes = $convites->where('status', 'pendente')->count();
        $solicitacoesPendentes = $solicitacoes->where('status', 'pendente')->count();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Area do jogador</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-950">Minhas peladas</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                        Acompanhe suas peladas, confirme presença nas próximas rodadas e responda convites recebidos.
                    </p>
                </div>
                <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Encontrar peladas
                </a>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-500">Peladas na lista</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $membros->count() }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-500">Rodadas próximas</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $totalJogosProximos }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-500">Convites pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $convitesPendentes }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-500">Solicitações pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $solicitacoesPendentes }}</p>
                </div>
            </div>
        </div>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-950">Minhas participações</h2>
                    <p class="mt-1 text-sm text-slate-600">Filtre suas peladas por nome, local, tipo ou status.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('jogador.peladas.minhas') }}" class="mt-5 grid gap-3 lg:grid-cols-[minmax(0,1fr)_160px_160px_auto]">
                <label class="text-sm font-medium text-slate-700">
                    Buscar
                    <input name="q" value="{{ $filtros['q'] ?? '' }}" class="mt-1 w-full rounded-md border-slate-300 text-sm" placeholder="Nome, bairro, cidade ou local">
                </label>
                <label class="text-sm font-medium text-slate-700">
                    Tipo
                    <select name="tipo" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                        <option value="">Todos</option>
                        <option value="mensalista" @selected(($filtros['tipo'] ?? '') === 'mensalista')>Mensalista</option>
                        <option value="diarista" @selected(($filtros['tipo'] ?? '') === 'diarista')>Diarista</option>
                    </select>
                </label>
                <label class="text-sm font-medium text-slate-700">
                    Status
                    <select name="status" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                        <option value="">Todos</option>
                        @foreach(['ativo', 'pendente', 'bloqueado', 'saiu', 'inativo'] as $status)
                            <option value="{{ $status }}" @selected(($filtros['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end gap-2">
                    <button class="h-10 rounded-md bg-slate-900 px-4 text-sm font-semibold text-white">Filtrar</button>
                    <a href="{{ route('jogador.peladas.minhas') }}" class="inline-flex h-10 items-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Limpar</a>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
                @forelse($membros as $membro)
                    <article class="border-b border-slate-200 bg-white p-4 last:border-b-0">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$membro->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                        {{ ucfirst($membro->status) }}
                                    </span>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                                        {{ $tipoLabels[$membro->tipo] ?? ucfirst($membro->tipo) }}
                                    </span>
                                    @if($membro->pelada->esporte)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $membro->pelada->esporte->nome }}
                                        </span>
                                    @endif
                                    <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-semibold text-white">
                                        {{ $membro->pelada->categoriaLabel() }}
                                    </span>
                                </div>
                                <h3 class="mt-3 text-lg font-bold text-slate-950">{{ $membro->pelada->nome }}</h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $membro->pelada->local_nome ?: $membro->pelada->local }}
                                    @if($membro->pelada->bairro || $membro->pelada->cidade)
                                        <span class="text-slate-400">-</span>
                                        {{ collect([$membro->pelada->bairro, $membro->pelada->cidade])->filter()->implode(', ') }}
                                    @endif
                                </p>
                                @if($membro->data_entrada)
                                    <p class="mt-1 text-xs text-slate-500">Entrada em {{ $membro->data_entrada->format('d/m/Y') }}</p>
                                @endif
                                @if($membro->pelada->data_fundacao)
                                    <p class="mt-1 text-xs text-slate-500">Pelada fundada em {{ $membro->pelada->data_fundacao->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <a href="{{ route('peladas.show', $membro->pelada) }}" class="inline-flex items-center justify-center rounded-md border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                Abrir pelada
                            </a>
                        </div>

                        <div class="mt-4 rounded-lg bg-slate-50 p-4">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h4 class="font-semibold text-slate-900">Próximas rodadas</h4>
                                <span class="text-xs font-medium text-slate-500">{{ $membro->pelada->jogos->count() }} encontrada(s)</span>
                            </div>
                            <div class="mt-3 divide-y divide-slate-200">
                                @forelse($membro->pelada->jogos as $jogo)
                                    @php($participacao = $jogo->participantes->first())
                                    <div class="flex flex-col gap-3 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $jogo->titulo }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                            @if($participacao && in_array($participacao->status, ['confirmado', 'fila'], true))
                                                <span class="inline-flex items-center justify-center rounded-md px-3 py-2 text-xs font-semibold {{ $participacao->status === 'confirmado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $participacao->status === 'confirmado' ? 'Confirmado' : 'Fila #'.$participacao->posicao_fila }}
                                                </span>
                                                <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}" class="w-full sm:w-auto">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-white sm:w-auto">Cancelar</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}" class="w-full sm:w-auto">
                                                    @csrf
                                                    <button class="w-full rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 sm:w-auto">Confirmar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="py-2 text-sm text-slate-500">Nenhuma rodada aberta nos próximos dias.</p>
                                @endforelse
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="p-8 text-center">
                        <p class="font-semibold text-slate-900">Nenhuma pelada encontrada.</p>
                        <p class="mt-1 text-sm text-slate-600">Ajuste os filtros ou explore novas peladas para participar.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">Convites recebidos</h2>
                        <p class="mt-1 text-sm text-slate-600">Convites enviados por organizadores para você aceitar ou recusar.</p>
                    </div>
                    <form method="GET" action="{{ route('jogador.peladas.minhas') }}" class="flex gap-2">
                        @foreach(request()->except('convite_status', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="convite_status" class="h-10 rounded-md border-slate-300 text-sm" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(['pendente', 'aprovada', 'recusada'] as $status)
                                <option value="{{ $status }}" @selected(($filtros['convite_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($convites as $convite)
                        @php($tipoConvite = str_replace('convite_', '', $convite->tipo_solicitacao ?? ''))
                        <div class="py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$convite->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                            {{ ucfirst($convite->status) }}
                                        </span>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                        Convite para {{ $tipoLabels[$tipoConvite] ?? $tipoConvite }}
                                    </span>
                                    <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-semibold text-white">
                                        {{ $convite->pelada->categoriaLabel() }}
                                    </span>
                                    </div>
                                    <p class="mt-3 font-semibold text-slate-950">{{ $convite->pelada->nome }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Recebido em {{ $convite->created_at->format('d/m/Y H:i') }}</p>
                                    @if($convite->mensagem)
                                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $convite->mensagem }}</p>
                                    @endif
                                </div>
                                @if($convite->status === 'pendente')
                                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                                        <form method="POST" action="{{ route('jogador.solicitacoes.aceitar-convite', $convite) }}" class="w-full sm:w-auto">
                                            @csrf
                                            @method('PATCH')
                                            <button class="w-full rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 sm:w-auto">Aceitar</button>
                                        </form>
                                        <form method="POST" action="{{ route('jogador.solicitacoes.recusar-convite', $convite) }}" class="w-full sm:w-auto">
                                            @csrf
                                            @method('PATCH')
                                            <button class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto">Recusar</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="font-semibold text-slate-900">Nenhum convite encontrado.</p>
                            <p class="mt-1 text-sm text-slate-600">Quando um organizador te convidar, o convite aparece aqui.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">Solicitações enviadas</h2>
                        <p class="mt-1 text-sm text-slate-600">Pedidos que você enviou para entrar ou virar mensalista.</p>
                    </div>
                    <form method="GET" action="{{ route('jogador.peladas.minhas') }}" class="flex gap-2">
                        @foreach(request()->except('solicitacao_status', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="solicitacao_status" class="h-10 rounded-md border-slate-300 text-sm" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach(['pendente', 'aprovada', 'recusada'] as $status)
                                <option value="{{ $status }}" @selected(($filtros['solicitacao_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($solicitacoes as $solicitacao)
                        @php($tipoSolicitacao = $solicitacao->tipo_solicitacao ?: $solicitacao->tipo)
                        <div class="py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$solicitacao->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                            {{ ucfirst($solicitacao->status) }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $tipoLabels[$tipoSolicitacao] ?? str_replace('_', ' ', ucfirst($tipoSolicitacao)) }}
                                        </span>
                                        <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-semibold text-white">
                                            {{ $solicitacao->pelada->categoriaLabel() }}
                                        </span>
                                    </div>
                                    <p class="mt-3 font-semibold text-slate-950">{{ $solicitacao->pelada->nome }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Enviada em {{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
                                    @if($solicitacao->mensagem)
                                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $solicitacao->mensagem }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('peladas.show', $solicitacao->pelada) }}" class="inline-flex shrink-0 items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Ver pelada
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="font-semibold text-slate-900">Nenhuma solicitação encontrada.</p>
                            <p class="mt-1 text-sm text-slate-600">Os pedidos feitos por você aparecem aqui.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
