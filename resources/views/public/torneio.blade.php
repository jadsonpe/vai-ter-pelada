<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-slate-950 text-white shadow-sm">
            <div class="p-6 lg:p-8">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-200">{{ $torneio->pelada->esporte->nome }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold">{{ $torneio->formatoLabel() }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold">{{ ucfirst($torneio->status) }}</span>
                </div>
                <h1 class="mt-4 text-3xl font-black sm:text-5xl">{{ $torneio->nome }}</h1>
                <p class="mt-3 max-w-3xl text-slate-300">{{ $torneio->pelada->nome }} - {{ $torneio->data_torneio->format('d/m/Y') }}</p>
                <a href="https://wa.me/?text={{ urlencode('Acompanhe o torneio '.$torneio->nome.': '.url()->current()) }}" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex rounded-md bg-emerald-500 px-5 py-3 text-sm font-bold text-slate-950">Compartilhar no WhatsApp</a>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Times</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($torneio->times->sortBy('ordem') as $time)
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <h3 class="font-bold text-slate-950">{{ $time->nome }}</h3>
                            <ul class="mt-2 space-y-1 text-sm text-slate-700">
                                @foreach($time->jogadores->sortBy('ordem') as $jogador)
                                    <li>{{ $jogador->participante->nomeExibicao() }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Classificação</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500"><tr><th class="p-2">Time</th><th>P</th><th>J</th><th>V</th><th>E</th><th>D</th><th>GP</th><th>GC</th><th>SG</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($classificacao as $row)
                                <tr><td class="p-2 font-semibold">{{ $row['time']->nome }}</td><td>{{ $row['pontos'] }}</td><td>{{ $row['jogos'] }}</td><td>{{ $row['vitorias'] }}</td><td>{{ $row['empates'] }}</td><td>{{ $row['derrotas'] }}</td><td>{{ $row['gols_pro'] }}</td><td>{{ $row['gols_contra'] }}</td><td>{{ $row['saldo'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
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

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold text-slate-950">Jogos</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @forelse($jogosOrdenados as $jogo)
                    <article class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $labelFase($jogo) }} - Jogo {{ $jogo->ordem }}</p>
                        <div class="mt-2 flex items-center justify-between gap-3">
                            <span class="font-semibold">{{ $jogo->timeA?->nome ?: 'A definir' }}</span>
                            <strong>{{ $jogo->gols_a ?? '-' }}</strong>
                        </div>
                        <div class="mt-1 flex items-center justify-between gap-3">
                            <span class="font-semibold">{{ $jogo->timeB?->nome ?: 'A definir' }}</span>
                            <strong>{{ $jogo->gols_b ?? '-' }}</strong>
                        </div>
                        @if($jogo->vencedor)
                            <p class="mt-2 text-xs font-semibold text-emerald-700">Vencedor: {{ $jogo->vencedor->nome }} @if($jogo->wo) por W.O. @endif</p>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Tabela ainda não gerada.</p>
                @endforelse
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Artilharia</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($artilharia as $row)
                        <div class="flex items-center justify-between py-2 text-sm"><span>{{ $row['participante']->nomeExibicao() }} <span class="text-slate-500">({{ $row['time']->nome }})</span></span><strong>{{ $row['gols'] }}</strong></div>
                    @empty
                        <p class="text-sm text-slate-500">Sem gols registrados.</p>
                    @endforelse
                </div>
            </section>
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Cartões</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($disciplina as $row)
                        <div class="flex items-center justify-between py-2 text-sm"><span>{{ $row['participante']->nomeExibicao() }} <span class="text-slate-500">({{ $row['time']->nome }})</span></span><strong>{{ $row['amarelos'] }} CA / {{ $row['vermelhos'] }} CV</strong></div>
                    @empty
                        <p class="text-sm text-slate-500">Sem cartões registrados.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
