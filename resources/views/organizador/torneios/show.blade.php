<x-app-layout>
    @php
        $cardIcon = fn (string $color) => match ($color) {
            'amarelo' => '<span class="inline-block h-4 w-3 rounded-sm bg-yellow-400 align-middle ring-1 ring-yellow-500/40"></span>',
            'vermelho' => '<span class="inline-block h-4 w-3 rounded-sm bg-red-600 align-middle ring-1 ring-red-700/40"></span>',
            'azul' => '<span class="inline-block h-4 w-3 rounded-sm bg-blue-600 align-middle ring-1 ring-blue-700/40"></span>',
            default => '',
        };
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.torneios.index', $pelada) }}" class="text-sm font-semibold text-emerald-700">Voltar para torneios</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $torneio->nome }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $pelada->nome }} - {{ $torneio->formatoLabel() }} - {{ $torneio->data_torneio->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('torneios.public.show', $torneio) }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Página pública</a>
                <a href="https://wa.me/?text={{ urlencode('Acompanhe o torneio '.$torneio->nome.': '.route('torneios.public.show', $torneio)) }}" target="_blank" rel="noopener noreferrer" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Compartilhar</a>
            </div>
        </div>

        @if($torneioEncerrado)
            <div class="mt-6 rounded-lg border border-slate-300 bg-slate-100 p-4 text-sm font-semibold text-slate-700">
                Final realizada. Times, jogadores e súmulas deste torneio estão bloqueados para preservar o historico.
            </div>
        @endif

        @if($torneio->imagemUrl() || collect($torneio->mural_fotos ?: [])->isNotEmpty())
            <section class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                @if($torneio->imagemUrl())
                    <img src="{{ $torneio->imagemUrl() }}" alt="Imagem do torneio {{ $torneio->nome }}" class="h-56 w-full object-cover md:h-72">
                @endif

                @if(collect($torneio->mural_fotos ?: [])->isNotEmpty())
                    <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($torneio->muralFotosUrls() as $fotoUrl)
                            <img src="{{ $fotoUrl }}" alt="Foto do mural do torneio" class="h-36 w-full rounded-lg object-cover">
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500">Participantes</p>
                <p class="mt-2 text-2xl font-bold">{{ $torneio->participantes->where('status', 'ativo')->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500">Times</p>
                <p class="mt-2 text-2xl font-bold">{{ $torneio->times->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500">Jogos</p>
                <p class="mt-2 text-2xl font-bold">{{ $torneio->jogos->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500">Status</p>
                <p class="mt-2 text-2xl font-bold">{{ ucfirst($torneio->status) }}</p>
            </div>
        </div>

        @php
            $ordemFases = [
                'pontos_corridos' => 10,
                'grupo' => 10,
                'mata_mata' => 20,
                'oitavas' => 30,
                'quartas' => 40,
                'semifinal' => 50,
                'terceiro_lugar' => 60,
                'final' => 70,
            ];

            $labelFase = function ($jogo) {
                return match ($jogo->fase) {
                    'pontos_corridos', 'grupo' => 'Rodada '.$jogo->rodada,
                    'mata_mata' => 'Mata-mata',
                    'oitavas' => 'Oitavas de final',
                    'quartas' => 'Quartas de final',
                    'semifinal' => 'Semifinal',
                    'terceiro_lugar' => 'Terceiro lugar',
                    'final' => 'Final',
                    default => ucfirst(str_replace('_', ' ', $jogo->fase)),
                };
            };

            $jogosOrdenados = $torneio->jogos->sortBy(fn ($jogo) => sprintf(
                '%03d-%03d-%03d',
                $ordemFases[$jogo->fase] ?? 999,
                $jogo->rodada,
                $jogo->ordem
            ));
        @endphp

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
            <section class="space-y-4">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Adicionar participantes</h2>
                <p class="mt-1 text-sm text-slate-600">Selecione membros da pelada ou cole nomes avulsos, um por linha.</p>
                @if($torneioEncerrado)
                    <p class="mt-4 rounded-md bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-600">Participantes bloqueados apos a final.</p>
                @else
                    <form method="POST" action="{{ route('organizador.torneios.participantes.store', $torneio) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="membros-torneio" class="text-sm font-medium text-slate-700">Membros da pelada</label>
                            <select id="membros-torneio" name="membros[]" multiple size="10" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                                @foreach($membrosDisponiveis->groupBy('tipo') as $tipo => $membrosPorTipo)
                                    <optgroup label="{{ ucfirst($tipo) }}">
                                        @foreach($membrosPorTipo->sortBy(fn ($membro) => $membro->nomeExibicao()) as $membro)
                                            <option value="{{ $membro->id }}">{{ $membro->nomeExibicao() }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">No computador, use Ctrl ou Shift para selecionar varios. No celular, toque nos nomes desejados.</p>
                        </div>
                        <div>
                            <label for="nomes-manuais" class="text-sm font-medium text-slate-700">Participantes manuais</label>
                            <textarea id="nomes-manuais" name="nomes_manuais" rows="4" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Joao da Silva&#10;Carlos Pereira&#10;Rafael Santos"></textarea>
                        </div>
                        <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700">Adicionar selecionados</button>
                    </form>
                @endif
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="font-bold text-slate-950">Participantes do torneio</h3>
                            <p class="text-sm text-slate-600">Configure goleiros e cabecas de chave em uma unica lista.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-slate-500">{{ $torneio->participantes->count() }} jogador(es)</span>
                            <button
                                type="button"
                                class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                data-toggle-target="participantes-torneio-lista"
                                aria-controls="participantes-torneio-lista"
                                aria-expanded="false"
                                data-label-open="Ver"
                                data-label-close="Ocultar"
                            >
                                Ver
                            </button>
                        </div>
                    </div>

                    <form id="participantes-torneio-lista" method="POST" action="{{ route('organizador.torneios.participantes.update-many', $torneio) }}" class="mt-4 hidden">
                        @csrf
                        @method('PATCH')

                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <div class="hidden grid-cols-[minmax(0,1fr)_170px_130px_80px] gap-3 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-500 md:grid">
                                <span>Jogador</span>
                                <span>Funcao</span>
                                <span>Status</span>
                                <span class="text-right">Acao</span>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @forelse($torneio->participantes->sortBy(fn ($p) => $p->nomeExibicao()) as $participante)
                                    @php
                                        $perfilSorteio = match (true) {
                                            $participante->goleiro && $participante->cabeca_chave => 'goleiro_cabeca',
                                            $participante->goleiro => 'goleiro',
                                            $participante->cabeca_chave => 'cabeca',
                                            default => 'normal',
                                        };
                                    @endphp
                                    <div class="grid gap-3 px-3 py-3 md:grid-cols-[minmax(0,1fr)_170px_130px_80px] md:items-center">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-950">{{ $participante->nomeExibicao() }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ ucfirst($participante->tipo) }}
                                                @if($participante->timeJogador)
                                                    - {{ $participante->timeJogador->time->nome }}
                                                @endif
                                            </p>
                                        </div>

                                        <label class="text-xs font-semibold text-slate-500 md:text-sm md:font-medium md:text-slate-700">
                                            <span class="md:hidden">Funcao</span>
                                            <select name="participantes[{{ $participante->id }}][perfil]" class="mt-1 w-full rounded-md border-slate-300 text-sm md:mt-0" @disabled($torneioEncerrado)>
                                                <option value="normal" @selected($perfilSorteio === 'normal')>Normal</option>
                                                <option value="goleiro" @selected($perfilSorteio === 'goleiro')>Goleiro</option>
                                                <option value="cabeca" @selected($perfilSorteio === 'cabeca')>Cabeca de chave</option>
                                                <option value="goleiro_cabeca" @selected($perfilSorteio === 'goleiro_cabeca')>Goleiro + cabeca</option>
                                            </select>
                                        </label>

                                        <label class="text-xs font-semibold text-slate-500 md:text-sm md:font-medium md:text-slate-700">
                                            <span class="md:hidden">Status</span>
                                            <select name="participantes[{{ $participante->id }}][status]" class="mt-1 w-full rounded-md border-slate-300 text-sm md:mt-0" @disabled($torneioEncerrado)>
                                                <option value="ativo" @selected($participante->status === 'ativo')>Ativo</option>
                                                <option value="removido" @selected($participante->status === 'removido')>Removido</option>
                                            </select>
                                        </label>

                                        <div class="flex justify-end">
                                            @unless($torneioEncerrado)
                                            <button
                                                type="submit"
                                                form="remover-participante-{{ $participante->id }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-red-200 text-red-600 hover:bg-red-50"
                                                title="Remover participante"
                                                aria-label="Remover {{ $participante->nomeExibicao() }}"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18" />
                                                    <path d="M8 6V4h8v2" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                    <path d="M10 11v5" />
                                                    <path d="M14 11v5" />
                                                </svg>
                                                <span class="sr-only">Remover participante</span>
                                            </button>
                                            @else
                                                <span class="rounded-md bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">Bloqueado</span>
                                            @endunless
                                        </div>
                                    </div>
                                @empty
                                    <p class="p-4 text-sm text-slate-500">Nenhum participante adicionado ainda.</p>
                                @endforelse
                            </div>
                        </div>

                        @if($torneio->participantes->isNotEmpty() && ! $torneioEncerrado)
                            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-slate-500">As funcoes ajudam a equilibrar o sorteio antes da primeira montagem dos times.</p>
                                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Salvar configuracoes</button>
                            </div>
                        @endif
                    </form>

                    @foreach($torneio->participantes as $participante)
                        <form id="remover-participante-{{ $participante->id }}" method="POST" action="{{ route('organizador.torneios.participantes.destroy', $participante) }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>

                <div class="mt-5 hidden max-h-[520px] space-y-2 overflow-y-auto pr-1">
                    @foreach($torneio->participantes->sortBy(fn ($p) => $p->nomeExibicao()) as $participante)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $participante->nomeExibicao() }}</p>
                                    <p class="text-xs text-slate-500">{{ ucfirst($participante->tipo) }} {{ $participante->timeJogador ? '- '.$participante->timeJogador->time->nome : '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('organizador.torneios.participantes.destroy', $participante) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-red-200 text-red-600 hover:bg-red-50" title="Remover participante" aria-label="Remover participante">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4h8v2" />
                                            <path d="M19 6l-1 14H6L5 6" />
                                            <path d="M10 11v5" />
                                            <path d="M14 11v5" />
                                        </svg>
                                        <span class="sr-only">Remover participante</span>
                                    </button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('organizador.torneios.participantes.update', $participante) }}" class="mt-3 grid gap-2 text-xs sm:grid-cols-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="goleiro" value="0">
                                <input type="hidden" name="cabeca_chave" value="0">
                                <input type="hidden" name="status" value="{{ $participante->status }}">
                                <label class="flex items-center gap-2"><input type="checkbox" name="goleiro" value="1" @checked($participante->goleiro)> Goleiro</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="cabeca_chave" value="1" @checked($participante->cabeca_chave)> Cabeça</label>
                                <button class="rounded-md border border-slate-300 px-2 py-1 font-semibold text-slate-700">Salvar</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-950">Times sorteados</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $torneio->jogadores_por_time }} jogadores por time. Restantes ficam fora do sorteio.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($torneio->times->isEmpty() && ! $torneioEncerrado)
                                <form method="POST" action="{{ route('organizador.torneios.times.sortear', $torneio) }}" data-loading-submit data-loading-message="Sorteando times...">
                                    @csrf
                                    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Sortear times</button>
                                </form>
                            @elseif($torneio->times->isEmpty())
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Sorteio bloqueado</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Sorteio fechado</span>
                                <button
                                    type="button"
                                    class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                    data-toggle-target="times-sorteados-lista"
                                    aria-controls="times-sorteados-lista"
                                    aria-expanded="false"
                                    data-label-open="Ver"
                                    data-label-close="Ocultar"
                                >
                                    Ver
                                </button>
                            @endif
                        </div>
                    </div>

                    <div id="times-sorteados-lista" class="{{ $torneio->times->isEmpty() ? '' : 'hidden' }}">
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach($torneio->times->sortBy('ordem') as $time)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    @if($torneioEncerrado)
                                        <h3 class="font-bold text-slate-950">{{ $time->nome }}</h3>
                                    @else
                                        <form method="POST" action="{{ route('organizador.torneios.times.update', $time) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input name="nome" value="{{ $time->nome }}" class="min-w-0 flex-1 rounded-md border-slate-300 text-sm">
                                            <button class="rounded-md bg-white px-3 text-xs font-semibold text-emerald-700 ring-1 ring-slate-200">OK</button>
                                        </form>
                                    @endif
                                    <ul class="mt-3 space-y-1 text-sm text-slate-700">
                                        @foreach($time->jogadores->sortBy('ordem') as $jogador)
                                            <li>{{ $jogador->participante->nomeExibicao() }}</li>
                                        @endforeach
                                    </ul>
                                    @unless($torneioEncerrado)
                                        <form method="POST" action="{{ route('organizador.torneios.times.jogadores.store', $time) }}" class="mt-3 space-y-2 rounded-md border border-dashed border-slate-300 bg-white p-3">
                                            @csrf
                                            <select name="torneio_participante_id" class="w-full rounded-md border-slate-300 text-sm">
                                                <option value="">Adicionar jogador restante</option>
                                                @foreach($restantes->sortBy(fn ($p) => $p->nomeExibicao()) as $participanteRestante)
                                                    <option value="{{ $participanteRestante->id }}">{{ $participanteRestante->nomeExibicao() }}</option>
                                                @endforeach
                                            </select>
                                            <div class="flex flex-col gap-2 sm:flex-row">
                                                <input name="nome_manual" class="min-w-0 flex-1 rounded-md border-slate-300 text-sm" placeholder="Ou digite um nome manual">
                                                <button class="rounded-md border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">Adicionar</button>
                                            </div>
                                        </form>
                                    @endunless
                                </div>
                            @endforeach
                        </div>

                        @if($restantes->isNotEmpty())
                            <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-900">
                                <p class="font-semibold">Jogadores restantes</p>
                                <p class="mt-1">{{ $restantes->map(fn ($p) => $p->nomeExibicao())->implode(', ') }}</p>
                            </div>
                        @endif
                    </div>

                    @unless($torneioEncerrado)
                        <form method="POST" action="{{ route('organizador.torneios.jogos.gerar', $torneio) }}" class="mt-4" data-loading-submit data-loading-message="Gerando tabela de jogos...">
                            @csrf
                            <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700">Gerar jogos do torneio</button>
                        </form>
                    @else
                        <p class="mt-4 rounded-md bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-600">Tabela bloqueada apos a final.</p>
                    @endunless
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-950">Classificação</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="p-2">Time</th><th>P</th><th>J</th><th>V</th><th>E</th><th>D</th><th>GP</th><th>GC</th><th>SG</th>
                                    <th>{!! $cardIcon('amarelo') !!}</th>
                                    <th>{!! $cardIcon('vermelho') !!}</th>
                                    <th>{!! $cardIcon('azul') !!}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($classificacao as $row)
                                    <tr><td class="p-2 font-semibold">{{ $row['time']->nome }}</td><td>{{ $row['pontos'] }}</td><td>{{ $row['jogos'] }}</td><td>{{ $row['vitorias'] }}</td><td>{{ $row['empates'] }}</td><td>{{ $row['derrotas'] }}</td><td>{{ $row['gols_pro'] }}</td><td>{{ $row['gols_contra'] }}</td><td>{{ $row['saldo'] }}</td><td>{{ $row['amarelos'] }}</td><td>{{ $row['vermelhos'] }}</td><td>{{ $row['azuis'] ?? 0 }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold text-slate-950">Jogos e súmulas</h2>
            <div class="mt-4 space-y-4">
                @forelse($jogosOrdenados as $jogo)
                    @php
                        $timeAJogadores = $jogo->timeA?->jogadores ?? collect();
                        $timeBJogadores = $jogo->timeB?->jogadores ?? collect();
                        $jogadoresDoJogo = $timeAJogadores->merge($timeBJogadores);
                        $golsCasaRegistrados = $jogo->gols
                            ->where('torneio_time_id', $jogo->time_a_id)
                            ->flatMap(fn ($gol) => array_fill(0, (int) $gol->quantidade, $gol->torneio_participante_id))
                            ->values();
                        $golsVisitanteRegistrados = $jogo->gols
                            ->where('torneio_time_id', $jogo->time_b_id)
                            ->flatMap(fn ($gol) => array_fill(0, (int) $gol->quantidade, $gol->torneio_participante_id))
                            ->values();
                        $jogadoresCasaJson = $timeAJogadores->map(fn ($jogador) => [
                            'id' => $jogador->participante->id,
                            'name' => $jogador->participante->nomeExibicao(),
                            'cards' => $jogo->cartoes
                                ->where('torneio_participante_id', $jogador->participante->id)
                                ->pluck('tipo')
                                ->values(),
                        ])->values();
                        $jogadoresVisitanteJson = $timeBJogadores->map(fn ($jogador) => [
                            'id' => $jogador->participante->id,
                            'name' => $jogador->participante->nomeExibicao(),
                            'cards' => $jogo->cartoes
                                ->where('torneio_participante_id', $jogador->participante->id)
                                ->pluck('tipo')
                                ->values(),
                        ])->values();
                        $jogoFinalizado = $jogo->status === 'finalizado';
                    @endphp
                    <article class="rounded-lg border p-4 {{ $jogoFinalizado ? 'border-slate-300 bg-slate-100/80' : 'border-emerald-100 bg-white' }}">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide {{ $jogoFinalizado ? 'text-slate-500' : 'text-emerald-700' }}">{{ $labelFase($jogo) }} - Jogo {{ $jogo->ordem }}</p>
                                <h3 class="mt-1 font-bold {{ $jogoFinalizado ? 'text-slate-700' : 'text-slate-950' }}">{{ $jogo->timeA?->nome ?: 'A definir' }} x {{ $jogo->timeB?->nome ?: 'A definir' }}</h3>
                                @if($jogo->status === 'finalizado')
                                    <p class="mt-1 text-sm font-semibold text-slate-700">{{ $jogo->gols_a }} x {{ $jogo->gols_b }} @if($jogo->vencedor) - vencedor: {{ $jogo->vencedor->nome }} @endif</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <span class="inline-flex justify-center rounded-full px-3 py-1 text-xs font-semibold {{ $jogoFinalizado ? 'bg-slate-700 text-white' : 'bg-amber-50 text-amber-800' }}">{{ $jogoFinalizado ? 'sumula salva' : $jogo->status }} @if($jogo->wo) - W.O. @endif</span>
                                @if($torneioEncerrado)
                                    <button
                                        type="button"
                                        class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                        data-toggle-target="sumula-jogo-{{ $jogo->id }}"
                                        aria-controls="sumula-jogo-{{ $jogo->id }}"
                                        aria-expanded="false"
                                        data-label-open="Ver sumula"
                                        data-label-close="Ocultar sumula"
                                    >
                                        Ver sumula
                                    </button>
                                @else
                                <button
                                    type="button"
                                    class="rounded-md border px-3 py-2 text-sm font-semibold {{ $jogoFinalizado ? 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}"
                                    data-toggle-target="sumula-jogo-{{ $jogo->id }}"
                                    aria-controls="sumula-jogo-{{ $jogo->id }}"
                                    aria-expanded="false"
                                    data-label-open="Lançar súmula"
                                    data-label-close="Ocultar súmula"
                                >
                                    Lançar súmula
                                </button>
                                @endif
                            </div>
                        </div>

                        @unless($torneioEncerrado)
                        <form
                            id="sumula-jogo-{{ $jogo->id }}"
                            method="POST"
                            action="{{ route('organizador.torneios.jogos.resultado', $jogo) }}"
                            class="mt-4 hidden space-y-4 rounded-lg bg-slate-50 p-4"
                            data-sumula-form
                            data-home-team-id="{{ $jogo->time_a_id }}"
                            data-away-team-id="{{ $jogo->time_b_id }}"
                            data-home-team-name="{{ $jogo->timeA?->nome ?: 'Time casa' }}"
                            data-away-team-name="{{ $jogo->timeB?->nome ?: 'Time visitante' }}"
                            data-home-goals='@json($golsCasaRegistrados)'
                            data-away-goals='@json($golsVisitanteRegistrados)'
                            data-home-players='@json($jogadoresCasaJson)'
                            data-away-players='@json($jogadoresVisitanteJson)'
                        >
                            @csrf
                            @method('PATCH')
                            <input type="number" name="gols_a" min="0" value="{{ old('gols_a', $jogo->gols_a) }}" class="rounded-md border-slate-300" placeholder="Gols A">
                            <input type="number" name="gols_b" min="0" value="{{ old('gols_b', $jogo->gols_b) }}" class="rounded-md border-slate-300" placeholder="Gols B">
                            <select name="vencedor_id" class="rounded-md border-slate-300">
                                <option value="">Vencedor se empate</option>
                                @foreach([$jogo->timeA, $jogo->timeB] as $timeOpcao)
                                    @if($timeOpcao)<option value="{{ $timeOpcao->id }}" @selected($jogo->vencedor_id === $timeOpcao->id)>{{ $timeOpcao->nome }}</option>@endif
                                @endforeach
                            </select>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="decidido_penaltis" value="1" @checked($jogo->decidido_penaltis)> Pênaltis</label>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="wo" value="1" @checked($jogo->wo)> W.O.</label>
                            <select name="wo_vencedor_id" class="rounded-md border-slate-300">
                                <option value="">Vencedor W.O.</option>
                                @foreach([$jogo->timeA, $jogo->timeB] as $timeOpcao)
                                    @if($timeOpcao)<option value="{{ $timeOpcao->id }}" @selected($jogo->wo_vencedor_id === $timeOpcao->id)>{{ $timeOpcao->nome }}</option>@endif
                                @endforeach
                            </select>

                            <textarea name="observacao" rows="2" class="lg:col-span-6 rounded-md border-slate-300" placeholder="Observações da partida">{{ old('observacao', $jogo->observacao) }}</textarea>

                            <div class="lg:col-span-3 rounded-lg bg-slate-50 p-3">
                                <p class="text-sm font-bold text-slate-900">Gols</p>
                                @for($i = 0; $i < 5; $i++)
                                    <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_1fr_90px]">
                                        <select name="gols[{{ $i }}][participante_id]" class="rounded-md border-slate-300 text-sm"><option value="">Jogador</option>@foreach($jogadoresDoJogo as $jogador)<option value="{{ $jogador->participante->id }}">{{ $jogador->participante->nomeExibicao() }}</option>@endforeach</select>
                                        <select name="gols[{{ $i }}][time_id]" class="rounded-md border-slate-300 text-sm"><option value="">Time</option>@foreach([$jogo->timeA, $jogo->timeB] as $timeOpcao)@if($timeOpcao)<option value="{{ $timeOpcao->id }}">{{ $timeOpcao->nome }}</option>@endif @endforeach</select>
                                        <input type="number" name="gols[{{ $i }}][quantidade]" min="1" class="rounded-md border-slate-300 text-sm" placeholder="Qtd">
                                    </div>
                                @endfor
                            </div>

                            <div class="lg:col-span-3 rounded-lg bg-slate-50 p-3">
                                <p class="text-sm font-bold text-slate-900">Cartões</p>
                                @for($i = 0; $i < 5; $i++)
                                    <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_1fr_110px_80px]">
                                        <select name="cartoes[{{ $i }}][participante_id]" class="rounded-md border-slate-300 text-sm"><option value="">Jogador</option>@foreach($jogadoresDoJogo as $jogador)<option value="{{ $jogador->participante->id }}">{{ $jogador->participante->nomeExibicao() }}</option>@endforeach</select>
                                        <select name="cartoes[{{ $i }}][time_id]" class="rounded-md border-slate-300 text-sm"><option value="">Time</option>@foreach([$jogo->timeA, $jogo->timeB] as $timeOpcao)@if($timeOpcao)<option value="{{ $timeOpcao->id }}">{{ $timeOpcao->nome }}</option>@endif @endforeach</select>
                                        <select name="cartoes[{{ $i }}][tipo]" class="rounded-md border-slate-300 text-sm"><option value="">Tipo</option><option value="amarelo">Amarelo</option><option value="vermelho">Vermelho</option></select>
                                        <input type="number" name="cartoes[{{ $i }}][quantidade]" min="1" class="rounded-md border-slate-300 text-sm" placeholder="Qtd">
                                    </div>
                                @endfor
                            </div>

                            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white lg:col-span-6">Salvar súmula</button>
                        </form>
                        @endunless

                        @if($torneioEncerrado)
                            <div id="sumula-jogo-{{ $jogo->id }}" class="mt-4 hidden space-y-4 rounded-lg border border-slate-200 bg-white p-4">
                                <div class="grid gap-3 md:grid-cols-3">
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Placar</p>
                                        <p class="mt-1 text-lg font-black text-slate-950">{{ $jogo->gols_a ?? 0 }} x {{ $jogo->gols_b ?? 0 }}</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vencedor</p>
                                        <p class="mt-1 font-bold text-slate-950">{{ $jogo->vencedor?->nome ?: 'Nao informado' }}</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Decisao</p>
                                        <p class="mt-1 font-bold text-slate-950">
                                            @if($jogo->wo)
                                                W.O.
                                            @elseif($jogo->decidido_penaltis)
                                                Penaltis
                                            @else
                                                Tempo normal
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-4 lg:grid-cols-2">
                                    <div class="rounded-lg border border-slate-200 p-4">
                                        <h4 class="font-bold text-slate-950">Gols</h4>
                                        <div class="mt-3 space-y-2 text-sm">
                                            @forelse($jogo->gols->groupBy('torneio_time_id') as $timeId => $golsDoTime)
                                                <div class="rounded-md bg-slate-50 p-3">
                                                    <p class="font-bold text-slate-800">
                                                        {{ (int) $timeId === (int) $jogo->time_a_id ? ($jogo->timeA?->nome ?: 'Time casa') : ($jogo->timeB?->nome ?: 'Time visitante') }}
                                                    </p>
                                                    <ul class="mt-2 space-y-1 text-slate-700">
                                                        @foreach($golsDoTime as $gol)
                                                            <li>{{ $gol->participante?->nomeExibicao() ?: 'Jogador' }} - {{ $gol->quantidade }} gol(s)</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @empty
                                                <p class="rounded-md bg-slate-50 p-3 text-slate-500">Nenhum gol registrado para este jogo.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="rounded-lg border border-slate-200 p-4">
                                        <h4 class="font-bold text-slate-950">Cartoes</h4>
                                        <div class="mt-3 space-y-2 text-sm">
                                            @forelse($jogo->cartoes->groupBy('torneio_participante_id') as $cartoesDoJogador)
                                                @php($primeiroCartao = $cartoesDoJogador->first())
                                                <div class="rounded-md bg-slate-50 p-3">
                                                    <p class="font-bold text-slate-800">{{ $primeiroCartao->participante?->nomeExibicao() ?: 'Jogador' }}</p>
                                                    <p class="mt-1 flex flex-wrap gap-2 text-slate-700">
                                                        @foreach($cartoesDoJogador as $cartao)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-white px-2 py-1 text-xs font-bold ring-1 ring-slate-200">
                                                                {!! $cardIcon($cartao->tipo) !!} {{ $cartao->quantidade }}
                                                            </span>
                                                        @endforeach
                                                    </p>
                                                </div>
                                            @empty
                                                <p class="rounded-md bg-slate-50 p-3 text-slate-500">Nenhum cartao registrado para este jogo.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                @if($jogo->observacao)
                                    <div class="rounded-lg border border-slate-200 p-4">
                                        <h4 class="font-bold text-slate-950">Observacoes</h4>
                                        <p class="mt-2 text-sm text-slate-700">{{ $jogo->observacao }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Nenhum jogo gerado ainda.</p>
                @endforelse
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Artilharia</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($artilharia as $row)
                        <div class="flex items-center justify-between py-2 text-sm"><span>{{ $row['participante']->nomeExibicao() }} <span class="text-slate-500">({{ $row['time']->nome }})</span></span><strong>{{ $row['gols'] }} gol(s)</strong></div>
                    @empty
                        <p class="text-sm text-slate-500">Sem gols registrados.</p>
                    @endforelse
                </div>
            </section>
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Disciplina</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($disciplina as $row)
                        <div class="flex items-center justify-between gap-3 py-2 text-sm">
                            <span>{{ $row['participante']->nomeExibicao() }} <span class="text-slate-500">({{ $row['time']->nome }})</span></span>
                            <strong class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-1">{!! $cardIcon('amarelo') !!} {{ $row['amarelos'] }}</span>
                                <span class="inline-flex items-center gap-1">{!! $cardIcon('vermelho') !!} {{ $row['vermelhos'] }}</span>
                                <span class="inline-flex items-center gap-1">{!! $cardIcon('azul') !!} {{ $row['azuis'] ?? 0 }}</span>
                            </strong>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem cartões registrados.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div id="torneio-loading-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 px-6 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-lg border border-emerald-300/30 bg-white p-6 text-center shadow-2xl">
            <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-emerald-50 ring-8 ring-emerald-100">
                <img src="{{ asset('assets/img/logo/vai-ter-pelada-logo-transparente.png') }}" alt="Vai Ter Pelada" class="h-20 w-20 animate-[vtpSpin_1.4s_ease-in-out_infinite] object-contain">
            </div>
            <p id="torneio-loading-message" class="mt-5 text-lg font-black text-slate-950">Processando...</p>
            <p class="mt-1 text-sm text-slate-600">Aguarde um instante.</p>
            <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full w-1/2 animate-[vtpLoading_1.2s_ease-in-out_infinite] rounded-full bg-emerald-500"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes vtpSpin {
            0%, 100% { transform: rotate(-8deg) scale(1); }
            50% { transform: rotate(8deg) scale(1.08); }
        }

        @keyframes vtpLoading {
            0% { transform: translateX(-110%); }
            100% { transform: translateX(220%); }
        }
    </style>

    <script>
        document.querySelectorAll('[data-loading-submit]').forEach((form) => {
            form.addEventListener('submit', () => {
                const overlay = document.getElementById('torneio-loading-overlay');
                const message = document.getElementById('torneio-loading-message');

                if (message) {
                    message.textContent = form.dataset.loadingMessage || 'Processando...';
                }

                if (overlay) {
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                }

                form.querySelectorAll('button').forEach((button) => {
                    button.disabled = true;
                    button.classList.add('opacity-70', 'cursor-wait');
                });
            });
        });

        document.querySelectorAll('[data-toggle-target]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.toggleTarget);

                if (!target) {
                    return;
                }

                const isHidden = target.classList.toggle('hidden');
                button.setAttribute('aria-expanded', String(!isHidden));
                button.textContent = isHidden
                    ? (button.dataset.labelOpen || 'Ver')
                    : (button.dataset.labelClose || 'Ocultar');
            });
        });

        const makeElement = (tag, attributes = {}, text = '') => {
            const element = document.createElement(tag);

            Object.entries(attributes).forEach(([key, value]) => {
                if (key === 'className') {
                    element.className = value;
                } else if (key === 'dataset') {
                    Object.assign(element.dataset, value);
                } else {
                    element.setAttribute(key, value);
                }
            });

            if (text) {
                element.textContent = text;
            }

            return element;
        };

        const buildPlayerSelect = (name, players, selected) => {
            const select = makeElement('select', {
                name,
                className: 'w-full rounded-md border-slate-300 text-sm',
                required: 'required',
            });
            select.appendChild(makeElement('option', { value: '' }, 'Escolha o jogador'));

            players.forEach((player) => {
                const option = makeElement('option', { value: player.id }, player.name);
                option.selected = Number(player.id) === Number(selected);
                select.appendChild(option);
            });

            return select;
        };

        const buildGoals = (form, side) => {
            const input = form.querySelector(`[data-goals-input="${side}"]`);
            const list = form.querySelector(`[data-goals-list="${side}"]`);
            const players = JSON.parse(form.dataset[side === 'home' ? 'homePlayers' : 'awayPlayers'] || '[]');
            const selectedGoals = JSON.parse(form.dataset[side === 'home' ? 'homeGoals' : 'awayGoals'] || '[]');
            const teamId = form.dataset[side === 'home' ? 'homeTeamId' : 'awayTeamId'];
            const count = Math.max(0, Number(input.value || 0));

            list.innerHTML = '';

            if (!count) {
                list.appendChild(makeElement('p', { className: 'rounded-md bg-slate-100 px-3 py-2 text-sm text-slate-500' }, 'Sem gols para este time.'));
                return;
            }

            for (let index = 0; index < count; index++) {
                const globalIndex = `${side}_${index}`;
                const row = makeElement('div', { className: 'rounded-md border border-slate-200 bg-slate-50 p-3' });
                row.appendChild(makeElement('label', { className: 'mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500' }, `Gol ${index + 1}`));
                row.appendChild(buildPlayerSelect(`gols[${globalIndex}][participante_id]`, players, selectedGoals[index]));
                row.appendChild(makeElement('input', { type: 'hidden', name: `gols[${globalIndex}][time_id]`, value: teamId || '' }));
                row.appendChild(makeElement('input', { type: 'hidden', name: `gols[${globalIndex}][quantidade]`, value: '1' }));
                list.appendChild(row);
            }
        };

        const cardSelect = (name, selectedCards) => {
            const select = makeElement('select', {
                name,
                multiple: 'multiple',
                size: '3',
                className: 'w-full rounded-md border-slate-300 text-sm',
            });

            [
                ['amarelo', 'Amarelo'],
                ['vermelho', 'Vermelho'],
                ['azul', 'Azul'],
            ].forEach(([value, label]) => {
                const option = makeElement('option', { value }, label);
                option.selected = selectedCards.includes(value);
                select.appendChild(option);
            });

            return select;
        };

        const buildCardsGroup = (form, title, teamId, players) => {
            const wrapper = makeElement('div', { className: 'rounded-md bg-slate-50 p-3' });
            wrapper.appendChild(makeElement('p', { className: 'text-sm font-bold text-slate-800' }, title));
            const list = makeElement('div', { className: 'mt-2 divide-y divide-slate-200' });

            if (!players.length) {
                list.appendChild(makeElement('p', { className: 'py-3 text-sm text-slate-500' }, 'Sem jogadores neste time.'));
            }

            players.forEach((player) => {
                const index = `${form.id}_${player.id}`;
                const row = makeElement('div', { className: 'grid gap-2 py-3 sm:grid-cols-[minmax(0,1fr)_180px] sm:items-center' });
                const info = makeElement('div');
                info.appendChild(makeElement('p', { className: 'font-semibold text-slate-900' }, player.name));
                info.appendChild(makeElement('input', { type: 'hidden', name: `cartoes[${index}][participante_id]`, value: player.id }));
                info.appendChild(makeElement('input', { type: 'hidden', name: `cartoes[${index}][time_id]`, value: teamId || '' }));
                info.appendChild(makeElement('input', { type: 'hidden', name: `cartoes[${index}][quantidade]`, value: '1' }));
                row.appendChild(info);
                row.appendChild(cardSelect(`cartoes[${index}][tipos][]`, player.cards || []));
                list.appendChild(row);
            });

            wrapper.appendChild(list);
            return wrapper;
        };

        const upgradeSumulaForm = (form) => {
            const security = Array.from(form.querySelectorAll('input[name="_token"], input[name="_method"]')).map((node) => node.cloneNode());
            const golsA = form.querySelector('input[name="gols_a"]')?.value || '0';
            const golsB = form.querySelector('input[name="gols_b"]')?.value || '0';
            const vencedorOptions = form.querySelector('select[name="vencedor_id"]')?.innerHTML || '<option value="">Selecione se precisar</option>';
            const woOptions = form.querySelector('select[name="wo_vencedor_id"]')?.innerHTML || '<option value="">Selecione</option>';
            const penaltis = form.querySelector('input[name="decidido_penaltis"]')?.checked;
            const wo = form.querySelector('input[name="wo"]')?.checked;
            const observacao = form.querySelector('textarea[name="observacao"]')?.value || '';
            const homePlayers = JSON.parse(form.dataset.homePlayers || '[]');
            const awayPlayers = JSON.parse(form.dataset.awayPlayers || '[]');

            form.replaceChildren(...security);

            const top = makeElement('div', { className: 'grid gap-3 md:grid-cols-2 xl:grid-cols-6' });

            [
                ['home', form.dataset.homeTeamName || 'Casa', 'gols_a', golsA],
                ['away', form.dataset.awayTeamName || 'Visitante', 'gols_b', golsB],
            ].forEach(([side, label, name, value]) => {
                const field = makeElement('label', { className: 'text-sm font-semibold text-slate-700' }, `Gols ${label}`);
                field.appendChild(makeElement('input', {
                    type: 'number',
                    name,
                    min: '0',
                    value,
                    className: 'mt-1 w-full rounded-md border-slate-300',
                    dataset: { goalsInput: side },
                }));
                top.appendChild(field);
            });

            const winner = makeElement('label', { className: 'text-sm font-semibold text-slate-700 xl:col-span-2' }, 'Vencedor se empate');
            const winnerSelect = makeElement('select', { name: 'vencedor_id', className: 'mt-1 w-full rounded-md border-slate-300' });
            winnerSelect.innerHTML = vencedorOptions;
            winner.appendChild(winnerSelect);
            top.appendChild(winner);

            const checks = makeElement('div', { className: 'flex items-end gap-4' });
            checks.innerHTML = `
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="decidido_penaltis" value="1" ${penaltis ? 'checked' : ''}> Pênaltis</label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="wo" value="1" ${wo ? 'checked' : ''}> W.O.</label>
            `;
            top.appendChild(checks);

            const woWinner = makeElement('label', { className: 'text-sm font-semibold text-slate-700' }, 'Vencedor W.O.');
            const woSelect = makeElement('select', { name: 'wo_vencedor_id', className: 'mt-1 w-full rounded-md border-slate-300' });
            woSelect.innerHTML = woOptions;
            woWinner.appendChild(woSelect);
            top.appendChild(woWinner);
            form.appendChild(top);

            const middle = makeElement('div', { className: 'grid gap-4 xl:grid-cols-2' });
            const goalsCard = makeElement('div', { className: 'rounded-lg border border-slate-200 bg-white p-4' });
            goalsCard.appendChild(makeElement('h4', { className: 'font-bold text-slate-950' }, 'Gols da partida'));
            const goalsGrid = makeElement('div', { className: 'mt-4 grid gap-4 lg:grid-cols-2' });
            [
                ['home', form.dataset.homeTeamName || 'Time casa'],
                ['away', form.dataset.awayTeamName || 'Time visitante'],
            ].forEach(([side, label]) => {
                const column = makeElement('div');
                column.appendChild(makeElement('p', { className: 'text-sm font-bold text-slate-700' }, label));
                column.appendChild(makeElement('div', { className: 'mt-2 space-y-2', dataset: { goalsList: side } }));
                goalsGrid.appendChild(column);
            });
            goalsCard.appendChild(goalsGrid);
            middle.appendChild(goalsCard);

            const obs = makeElement('label', { className: 'rounded-lg border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700' }, 'Observações da partida');
            const textarea = makeElement('textarea', {
                name: 'observacao',
                rows: '8',
                className: 'mt-2 w-full rounded-md border-slate-300',
                placeholder: 'Lesões, decisões por pênaltis, motivo de W.O. ou observações gerais.',
            });
            textarea.value = observacao;
            obs.appendChild(textarea);
            middle.appendChild(obs);
            form.appendChild(middle);

            const cards = makeElement('div', { className: 'rounded-lg border border-slate-200 bg-white p-4' });
            cards.appendChild(makeElement('h4', { className: 'font-bold text-slate-950' }, 'Cartões'));
            cards.appendChild(makeElement('p', { className: 'mt-1 text-sm text-slate-600' }, 'Marque os cartões recebidos por cada jogador. Pode marcar mais de uma opção.'));
            const cardsGrid = makeElement('div', { className: 'mt-4 grid gap-4 lg:grid-cols-2' });
            cardsGrid.appendChild(buildCardsGroup(form, form.dataset.homeTeamName || 'Time casa', form.dataset.homeTeamId, homePlayers));
            cardsGrid.appendChild(buildCardsGroup(form, form.dataset.awayTeamName || 'Time visitante', form.dataset.awayTeamId, awayPlayers));
            cards.appendChild(cardsGrid);
            form.appendChild(cards);

            form.appendChild(makeElement('button', { className: 'w-full rounded-md bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700' }, 'Salvar súmula'));

            form.querySelectorAll('[data-goals-input]').forEach((input) => {
                input.addEventListener('input', () => buildGoals(form, input.dataset.goalsInput));
                buildGoals(form, input.dataset.goalsInput);
            });
        };

        document.querySelectorAll('[data-sumula-form]').forEach(upgradeSumulaForm);
    </script>
</x-app-layout>
