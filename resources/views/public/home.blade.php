<x-app-layout>
    @section('title', 'Vai Ter Pelada | Organize, encontre e jogue peladas')

    @push('meta')
        <meta name="description" content="Sua pelada gerenciada como um campeonato profissional. Encontre jogos, confirme presença e construa seu perfil de peladeiro.">
        <meta property="og:title" content="Vai Ter Pelada">
        <meta property="og:description" content="Encontre peladas, confirme presença e organize rodadas com caixa, sorteio, torneios e avaliações.">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ asset('assets/img/logo/vai-ter-pelada-logo-1024.png') }}">
        <meta property="og:image:secure_url" content="{{ asset('assets/img/logo/vai-ter-pelada-logo-1024.png') }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="1024">
        <meta property="og:image:height" content="1024">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Vai Ter Pelada">
        <meta name="twitter:description" content="Sua pelada gerenciada como um campeonato profissional.">
        <meta name="twitter:image" content="{{ asset('assets/img/logo/vai-ter-pelada-logo-1024.png') }}">
    @endpush

    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        @include('shared.status')
    </div>

    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img src="{{ asset('assets/img/backgrounds/home-hero-pelada.jpg') }}" alt="" class="h-full w-full object-cover opacity-50">
            
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-transparent"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[minmax(0,1fr)_440px] lg:px-8 lg:py-20">
            <div class="flex flex-col justify-center">
                <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-300">Vai Ter Pelada</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">
                    Sua pelada gerenciada como um campeonato profissional.
                </h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-200">
                    O jogador encontra vaga, confirma presença e evolui no ranking. O organizador controla caixa, times, torneios e avaliações sem depender de planilha.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-400 px-6 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-950/30 transition hover:bg-emerald-300">
                        Criar conta grátis
                    </a>
                    <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-lg border border-white/15 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:border-emerald-300 hover:bg-white/15">
                        Explorar peladas
                    </a>
                </div>

                <div class="mt-10 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                        <p class="text-2xl font-black text-white">30s</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-300">para confirmar presença</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                        <p class="text-2xl font-black text-white">5.00</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-300">avaliação por rodada</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                        <p class="text-2xl font-black text-white">100%</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-300">gestão da pelada</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="rounded-lg border border-emerald-300/30 bg-slate-900/90 p-5 shadow-2xl shadow-slate-950/60">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/img/icons/icon-256x256.png') }}" alt="Vai Ter Pelada" class="h-16 w-16 rounded-lg object-contain">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-300">Rodada de hoje</p>
                            <p class="mt-1 text-xl font-black">Arena Central</p>
                            <p class="text-sm text-slate-400">20 confirmados, 4 na fila</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3">
                        <div class="rounded-lg bg-slate-800 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-white-400">Caixa da rodada</span>
                                <span class="text-lg font-black text-emerald-300">R$ 740</span>
                            </div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-700">
                                <div class="h-full w-4/5 bg-emerald-400"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-slate-800 p-4">
                                <p class="text-xs font-bold uppercase text-white-400">Times</p>
                                <p class="mt-2 text-2xl font-black">4</p>
                            </div>
                            <div class="rounded-lg bg-slate-800 p-4">
                                <p class="text-xs font-bold uppercase text-white-400">Jogos</p>
                                <p class="mt-2 text-2xl font-black">8</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-14" style="padding-top: 4rem; padding-bottom: 4.5rem;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-600">Como funciona</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">Do convite ao jogo em três passos.</h2>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['step' => '1', 'title' => 'Encontre', 'text' => 'Veja peladas ativas por bairro, esporte e valor da diária.'],
                    ['step' => '2', 'title' => 'Confirme', 'text' => 'Entre na lista, acompanhe sua vaga e receba o contexto da rodada.'],
                    ['step' => '3', 'title' => 'Jogue', 'text' => 'Compareça, seja avaliado e construa seu histórico de peladeiro.'],
                ] as $item)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-lg font-black text-white">{{ $item['step'] }}</div>
                        <h3 class="mt-5 text-xl font-black text-slate-950">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-16 text-white" style="padding-top: 5rem; padding-bottom: 5.5rem;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-300">Seu card de peladeiro</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Mostre que você não é só mais um na lista.</h2>
                <p class="mt-4 text-base leading-7 text-slate-300">
                    Cada jogador ganha um perfil público com estatísticas, avaliações, conquistas, reputação da rodada e histórico de participação.
                </p>
            </div>

            <div class="mx-auto mt-9 max-w-5xl overflow-hidden rounded-lg border border-white/10 bg-slate-900 shadow-2xl shadow-slate-950/60">
                <div class="relative min-h-44 p-5 sm:p-6">
                    <img src="{{ asset('images/player-covers/society3.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-45">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20"></div>
                    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border border-emerald-300/40 bg-emerald-400 shadow-lg shadow-slate-950/40">
                                <img src="{{ asset('assets/img/illustrations/jogador-h.png') }}" alt="Gustavo Canhota" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-2xl font-black tracking-tight">Gustavo “Canhota”</h3>
                                    <span class="rounded-full bg-yellow-300 px-3 py-1 text-xs font-black uppercase text-slate-950">Craque da rodada</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-slate-200">
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1">Society</span>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1">Meia-armador</span>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1">29 anos</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full rounded-lg border border-emerald-300/30 bg-slate-950/75 px-4 py-3 text-center sm:w-auto">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Avaliação</p>
                            <p class="mt-1 text-3xl font-black text-emerald-300">3.80/5</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <div class="rounded-lg border border-white/10 bg-slate-800/80 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Jogos</p>
                            <p class="mt-2 text-3xl font-black">42</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-slate-800/80 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Gols</p>
                            <p class="mt-2 text-3xl font-black">18</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-slate-800/80 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Cartões</p>
                            <p class="mt-2 text-3xl font-black">6</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-slate-800/80 p-4">
                            <p class="text-xs font-bold uppercase text-slate-400">Reputação</p>
                            <p class="mt-2 text-lg font-black text-emerald-300">Maestro</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div class="rounded-lg border border-white/10 bg-slate-800/70 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <p class="text-sm font-black uppercase tracking-wide text-slate-300">Desempenho nas peladas</p>
                                <div class="rounded-md border border-white/10 bg-slate-950/50 px-3 py-2">
                                    <p class="mb-2 text-right text-[10px] font-black uppercase tracking-wide text-slate-500">Cartões</p>
                                    <div class="flex items-center gap-3">
                                        {{-- <span class="inline-flex items-center gap-1.5 text-sm font-black leading-none">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.875rem; height: 1.25rem; background-color: #fde047;"></span>
                                            <span>3</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-sm font-black leading-none">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.875rem; height: 1.25rem; background-color: #ef4444;"></span>
                                            <span>1</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-sm font-black leading-none">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.875rem; height: 1.25rem; background-color: #38bdf8;"></span>
                                            <span>2</span>
                                        </span> --}}
                                        <div class="flex items-center justify-center gap-2 rounded-md border border-white/10 bg-slate-900/80 px-2 py-2">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.75rem; height: 1.05rem; background-color: #fde047;"></span>
                                            <span class="text-sm font-black leading-none">3</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-2 rounded-md border border-white/10 bg-slate-900/80 px-2 py-2">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.75rem; height: 1.05rem; background-color: #ef4444;"></span>
                                            <span class="text-sm font-black leading-none">3</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-2 rounded-md border border-white/10 bg-slate-900/80 px-2 py-2">
                                            <span class="rounded-[2px] shadow-sm" style="display: inline-block; width: 0.75rem; height: 1.05rem; background-color: #38bdf8;"></span>
                                            <span class="text-sm font-black leading-none">3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-2 sm:grid-cols-3">
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Rodadas</p>
                                    <p class="mt-1 text-xl font-black">28</p>
                                </div>
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Torneios</p>
                                    <p class="mt-1 text-xl font-black">4</p>
                                </div>
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Gols</p>
                                    <p class="mt-1 text-xl font-black">18</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-slate-800/70 p-4">
                            <p class="text-sm font-black uppercase tracking-wide text-slate-300">Votos recebidos</p>
                            <div class="mt-4 grid gap-2 sm:grid-cols-3">
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Craque</p>
                                    <p class="mt-1 text-xl font-black">7</p>
                                </div>
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Garçom</p>
                                    <p class="mt-1 text-xl font-black">5</p>
                                </div>
                                <div class="rounded-md bg-slate-950/60 p-3">
                                    <p class="text-xs font-bold uppercase text-slate-500">Maestro</p>
                                    <p class="mt-1 text-xl font-black">9</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-100">Fominha da rodada</span>
                        <span class="rounded-full bg-yellow-300/15 px-3 py-1 text-xs font-bold text-yellow-100">Artilheiro</span>
                        <span class="rounded-full bg-sky-400/15 px-3 py-1 text-xs font-bold text-sky-100">Maestro</span>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-slate-100">Resenha limpa</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-100 py-14" style="padding-top: 5rem; padding-bottom: 5rem;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700">Painel do organizador</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">A pelada vira uma operação simples de controlar.</h2>
                <p class="mt-3 text-slate-600">Ferramentas para quem cuida da lista, do dinheiro, dos times e dos campeonatos internos.</p>
            </div>

            <div class="mt-10 grid gap-4 lg:grid-cols-3">
                @foreach([
                    ['icon' => 'R$', 'title' => 'Controle de Caixa', 'text' => 'Relatório mensal de entradas de mensalistas e diaristas, saídas e histórico por rodada.'],
                    ['icon' => '2x', 'title' => 'Sorteio de Times', 'text' => 'Divisão de equipes, coletes e sobras de forma clara para todos os presentes.'],
                    ['icon' => 'T', 'title' => 'Torneios Internos', 'text' => 'Campeonatos com times, tabela, súmula completa, gols, cartões e classificação.'],
                ] as $feature)
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-950 text-sm font-black text-emerald-300">{{ $feature['icon'] }}</div>
                        <h3 class="mt-5 text-xl font-black text-slate-950">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $feature['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8" style="padding-top: 5rem; padding-bottom: 5rem;">
        <div class="mb-7 flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700">Peladas em destaque</p>
                <h2 class="mt-1 text-3xl font-black text-slate-950">Escolha onde vai jogar.</h2>
                <p class="mt-2 text-sm text-slate-500">Confira próximas partidas, grupos ativos e organizadores perto de você.</p>
            </div>
            <a class="text-sm font-bold text-emerald-700 hover:text-emerald-800" href="{{ route('peladas.index') }}">Ver todas</a>
        </div>

        @if($peladas->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($peladas as $pelada)
                    <div class="group overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                        <a href="{{ route('peladas.show', $pelada) }}">
                            <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" class="h-48 w-full object-cover transition-transform group-hover:scale-[1.02]" />
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">{{ $pelada->esporte->nome }}</span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">{{ $pelada->categoriaLabel() }}</span>
                                </div>
                                <h3 class="mt-3 text-lg font-black text-slate-950 group-hover:text-emerald-700">{{ $pelada->nome }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>
                                @if($pelada->data_fundacao)
                                    <p class="mt-2 text-xs font-semibold text-slate-500">Desde {{ $pelada->data_fundacao->format('d/m/Y') }}</p>
                                @endif
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                    <p class="truncate text-xs text-slate-500">Organizador: {{ $pelada->organizador->name }}</p>
                                    <span class="text-xs font-bold text-emerald-700">Ver detalhes</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-lg bg-slate-50 p-8 text-center">
                <p class="text-slate-500">Nenhuma pelada disponível no momento.</p>
                <p class="mt-2 text-sm text-slate-400">Volte em breve para conferir novidades.</p>
            </div>
        @endif

        <div class="mt-12 rounded-lg bg-slate-950 p-8 text-center text-white shadow-xl sm:p-10">
            <h3 class="text-2xl font-black sm:text-3xl">Entre para a próxima lista sem depender de grupo perdido no WhatsApp.</h3>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-300">Crie sua conta grátis, encontre uma pelada ativa e comece a montar seu histórico de jogador.</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-400 px-6 py-3 text-sm font-black text-slate-950 hover:bg-emerald-300">Criar conta grátis</a>
                <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-lg border border-white/15 px-6 py-3 text-sm font-bold text-white hover:bg-white/10">Ver peladas</a>
            </div>
        </div>

        @if($patrocinadores->isNotEmpty())
            <div class="mt-12 rounded-lg border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-center text-sm font-bold uppercase tracking-wide text-slate-500">Nossos patrocinadores</h2>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                    @foreach($patrocinadores as $patrocinador)
                        <a class="rounded-lg bg-white px-5 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:text-emerald-700"
                           href="{{ $patrocinador->link ?: $patrocinador->site_url ?: '#' }}"
                           target="_blank"
                           rel="noopener noreferrer">
                            {{ $patrocinador->nome }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-app-layout>
