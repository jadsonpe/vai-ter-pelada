<x-app-layout>
    @php
        $cardIcon = fn (string $color) => match ($color) {
            'amarelo' => '<span class="inline-block h-4 w-3 rounded-sm bg-yellow-400 align-middle ring-1 ring-yellow-500/40"></span>',
            'vermelho' => '<span class="inline-block h-4 w-3 rounded-sm bg-red-600 align-middle ring-1 ring-red-700/40"></span>',
            'azul' => '<span class="inline-block h-4 w-3 rounded-sm bg-blue-600 align-middle ring-1 ring-blue-700/40"></span>',
            default => '',
        };
        $muralUrls = $torneio->muralFotosUrls();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-slate-950 text-white shadow-sm">
            @if($torneio->imagemUrl())
                <img src="{{ $torneio->imagemUrl() }}" alt="Imagem do torneio {{ $torneio->nome }}" class="h-56 w-full object-cover md:h-80">
            @endif
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

        @if($muralUrls)
            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-950">Mural de fotos</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($muralUrls as $index => $fotoUrl)
                        <button type="button" class="group overflow-hidden rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" data-gallery-open="{{ $index }}">
                            <img src="{{ $fotoUrl }}" alt="Foto do mural do torneio" class="h-40 w-full object-cover transition duration-200 group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

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
                <h2 class="text-xl font-bold text-slate-950">Classificacao</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="p-2">Time</th><th>P</th><th>J</th><th>V</th><th>E</th><th>D</th><th>GP</th><th>GC</th><th>SG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($classificacao as $row)
                                <tr>
                                    <td class="p-2 font-semibold">{{ $row['time']->nome }}</td>
                                    <td>{{ $row['pontos'] }}</td>
                                    <td>{{ $row['jogos'] }}</td>
                                    <td>{{ $row['vitorias'] }}</td>
                                    <td>{{ $row['empates'] }}</td>
                                    <td>{{ $row['derrotas'] }}</td>
                                    <td>{{ $row['gols_pro'] }}</td>
                                    <td>{{ $row['gols_contra'] }}</td>
                                    <td>{{ $row['saldo'] }}</td>
                                </tr>
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
            <h2 class="text-xl font-bold text-slate-950">Jogos e sumulas</h2>
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

                        <button type="button" class="mt-3 w-full rounded-md border border-emerald-200 px-3 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-50" data-toggle-target="public-sumula-{{ $jogo->id }}" aria-controls="public-sumula-{{ $jogo->id }}" aria-expanded="false" data-label-open="Ver sumula" data-label-close="Ocultar sumula">
                            Ver sumula
                        </button>

                        <div id="public-sumula-{{ $jogo->id }}" class="mt-4 hidden space-y-4 rounded-lg bg-slate-50 p-4">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Placar</p>
                                    <p class="mt-1 text-lg font-black text-slate-950">{{ $jogo->gols_a ?? 0 }} x {{ $jogo->gols_b ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vencedor</p>
                                    <p class="mt-1 font-bold text-slate-950">{{ $jogo->vencedor?->nome ?: 'Nao informado' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Decisao</p>
                                    <p class="mt-1 font-bold text-slate-950">{{ $jogo->wo ? 'W.O.' : ($jogo->decidido_penaltis ? 'Penaltis' : 'Tempo normal') }}</p>
                                </div>
                            </div>

                            <div class="grid gap-3 lg:grid-cols-2">
                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <h3 class="font-bold text-slate-950">Gols</h3>
                                    <div class="mt-2 space-y-2 text-sm">
                                        @forelse($jogo->gols->groupBy('torneio_time_id') as $timeId => $golsDoTime)
                                            <div class="rounded-md bg-slate-50 p-3">
                                                <p class="font-bold text-slate-800">{{ (int) $timeId === (int) $jogo->time_a_id ? ($jogo->timeA?->nome ?: 'Time casa') : ($jogo->timeB?->nome ?: 'Time visitante') }}</p>
                                                <ul class="mt-1 space-y-1 text-slate-700">
                                                    @foreach($golsDoTime as $gol)
                                                        <li>{{ $gol->participante?->nomeExibicao() ?: 'Jogador' }} - {{ $gol->quantidade }} gol(s)</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @empty
                                            <p class="rounded-md bg-slate-100 p-3 text-slate-500">Nenhum gol registrado.</p>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <h3 class="font-bold text-slate-950">Cartoes</h3>
                                    <div class="mt-2 space-y-2 text-sm">
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
                                            <p class="rounded-md bg-slate-100 p-3 text-slate-500">Nenhum cartao registrado.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            @if($jogo->observacao)
                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <h3 class="font-bold text-slate-950">Observacoes</h3>
                                    <p class="mt-1 text-sm text-slate-700">{{ $jogo->observacao }}</p>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Tabela ainda nao gerada.</p>
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
                <h2 class="text-xl font-bold text-slate-950">Cartoes</h2>
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
                        <p class="text-sm text-slate-500">Sem cartoes registrados.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @if($muralUrls)
        <div id="gallery-modal" class="fixed inset-0 z-50 hidden bg-slate-950/95 px-4 py-6">
            <button type="button" class="absolute right-4 top-4 rounded-full bg-white/10 px-3 py-2 text-sm font-bold text-white hover:bg-white/20" data-gallery-close>Fechar</button>
            <button type="button" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 px-4 py-3 text-2xl font-black text-white hover:bg-white/20" data-gallery-prev aria-label="Foto anterior">&lt;</button>
            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 px-4 py-3 text-2xl font-black text-white hover:bg-white/20" data-gallery-next aria-label="Proxima foto">&gt;</button>
            <div class="flex h-full items-center justify-center">
                <img id="gallery-image" src="" alt="Foto ampliada do mural" class="max-h-[82vh] max-w-full rounded-lg object-contain shadow-2xl">
            </div>
            <p id="gallery-counter" class="absolute bottom-5 left-0 right-0 text-center text-sm font-bold text-white"></p>
        </div>
    @endif

    <script>
        document.querySelectorAll('[data-toggle-target]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.toggleTarget);
                if (!target) return;

                const isHidden = target.classList.toggle('hidden');
                button.setAttribute('aria-expanded', String(!isHidden));
                button.textContent = isHidden
                    ? (button.dataset.labelOpen || 'Ver')
                    : (button.dataset.labelClose || 'Ocultar');
            });
        });

        (() => {
            const images = @json($muralUrls);
            const modal = document.getElementById('gallery-modal');
            const image = document.getElementById('gallery-image');
            const counter = document.getElementById('gallery-counter');
            let current = 0;

            if (!modal || !image || !images.length) return;

            const render = () => {
                image.src = images[current];
                counter.textContent = `${current + 1} / ${images.length}`;
            };

            const open = (index) => {
                current = Number(index || 0);
                render();
                modal.classList.remove('hidden');
                modal.classList.add('block');
                document.body.classList.add('overflow-hidden');
            };

            const close = () => {
                modal.classList.add('hidden');
                modal.classList.remove('block');
                document.body.classList.remove('overflow-hidden');
            };

            const move = (direction) => {
                current = (current + direction + images.length) % images.length;
                render();
            };

            document.querySelectorAll('[data-gallery-open]').forEach((button) => {
                button.addEventListener('click', () => open(button.dataset.galleryOpen));
            });

            modal.querySelector('[data-gallery-close]')?.addEventListener('click', close);
            modal.querySelector('[data-gallery-prev]')?.addEventListener('click', () => move(-1));
            modal.querySelector('[data-gallery-next]')?.addEventListener('click', () => move(1));
            modal.addEventListener('click', (event) => {
                if (event.target === modal) close();
            });
            document.addEventListener('keydown', (event) => {
                if (modal.classList.contains('hidden')) return;
                if (event.key === 'Escape') close();
                if (event.key === 'ArrowLeft') move(-1);
                if (event.key === 'ArrowRight') move(1);
            });
        })();
    </script>
</x-app-layout>
