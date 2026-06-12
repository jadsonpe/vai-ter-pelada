@php

    $tabs = [

        'resumo' => 'Resumo',

        'peladas' => 'Minhas peladas',

        'avaliacoes' => 'Avaliações',

        'mensagens' => 'Mensagens',

    ];



    if($peladasOrganizadas->isNotEmpty()) {

        $tabs['organizacao'] = 'Organização';

    }



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

@endphp



<div class="bg-slate-100">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @include('shared.status')



        @if(! auth()->user()->perfilCompleto())

            <section class="mb-6 rounded-lg border border-emerald-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div data-feed-heading>

                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Perfil incompleto</p>

                        <h2 class="mt-1 text-xl font-bold text-slate-950">Finalize seus dados de jogador</h2>

                        <p class="mt-1 text-sm text-slate-600">Faltam: {{ implode(', ', auth()->user()->camposPerfilPendentes()) }}.</p>

                    </div>

                    <a href="{{ route('perfil.edit') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

                        Completar perfil

                    </a>

                </div>

            </section>

        @endif



        <section class="overflow-hidden rounded-lg bg-slate-950 text-white shadow-sm">

            <div class="grid gap-3 px-4 py-3 sm:px-5 lg:grid-cols-[1fr_auto] lg:items-center">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Painel do jogador</p>

                    <h1 class="mt-1 text-xl font-bold sm:text-2xl">Oi, {{ auth()->user()->name }}</h1>

                    <div class="hidden sm:block">

                    <p class="mt-1 max-w-2xl text-sm text-slate-300">Confirme presença, responda convites e acompanhe sua rotina de peladas.</p>

                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">

                        <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-emerald-400">Encontrar peladas</a>

                        <a href="{{ route('organizador.peladas.create') }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-slate-100">Criar pelada</a>

                        @if($peladasOrganizadas->isNotEmpty())

                            <a href="{{ route('dashboard', ['aba' => 'organizacao']) }}" class="inline-flex items-center justify-center rounded-md border border-emerald-300/40 px-3 py-2 text-xs font-semibold text-emerald-100 hover:bg-white/10">Organizar</a>

                        @endif

                        {{-- <a href="{{ route('peladeiros.show', auth()->user()->publicProfile()) }}" class="inline-flex items-center justify-center rounded-md border border-white/20 px-3 py-2 text-xs font-semibold text-white hover:bg-white/10">Perfil</a> --}}

                    </div>

                </div>



                <div class="hidden rounded-md border border-white/10 bg-white/10 p-2 sm:block lg:w-60">

                    <div class="flex items-center gap-3">

                        <x-user-avatar :user="auth()->user()" size="sm" class="border-2 border-emerald-300" />

                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>

                            <p class="truncate text-xs text-slate-300">{{ auth()->user()->username }}</p>

                            <span class="mt-1 inline-flex rounded-full bg-emerald-400/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-200">{{ auth()->user()->role }}</span>

                        </div>

                    </div>

                </div>

            </div>

        </section>



        <nav class="mt-6 rounded-lg border border-slate-200 bg-white p-2 shadow-sm">

            <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">

                @foreach($tabs as $key => $label)

                    <a href="{{ route('dashboard', ['aba' => $key]) }}" class="flex min-w-0 items-center justify-center rounded-md px-3 py-2 text-center text-sm font-bold transition sm:px-4 {{ $aba === $key ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">

                        {{ $label }}

                        @if($key === 'mensagens' && $notificacoesNaoLidasPainel)

                            <span class="ml-1 rounded-full bg-red-600 px-2 py-0.5 text-[10px] text-white">{{ $notificacoesNaoLidasPainel }}</span>

                        @endif

                        @if($key === 'avaliacoes' && $pendingGames->isNotEmpty())

                            <span class="ml-1 rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] text-white">{{ $pendingGames->sum(fn ($item) => $item->avaliados->count()) }}</span>

                        @endif

                    </a>

                @endforeach

            </div>

        </nav>



        @if($aba === 'resumo')

            <section class="mt-6 grid gap-2 sm:gap-4" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr));">
                <div class="flex min-h-[92px] min-w-0 flex-col justify-between rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:min-h-0 sm:p-5">
                    <p class="min-h-8 text-[11px] font-medium leading-tight text-slate-500 sm:min-h-0 sm:text-sm">Peladas</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $membros->count() }}</p>
                </div>
                <div class="flex min-h-[92px] min-w-0 flex-col justify-between rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:min-h-0 sm:p-5">
                    <p class="min-h-8 text-[11px] font-medium leading-tight text-slate-500 sm:min-h-0 sm:text-sm">Rodadas próximas</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $totalJogosProximos }}</p>
                </div>
                <div class="flex min-h-[92px] min-w-0 flex-col justify-between rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:min-h-0 sm:p-5">
                    <p class="min-h-8 text-[11px] font-medium leading-tight text-slate-500 sm:min-h-0 sm:text-sm">Convites pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-700 sm:text-3xl">{{ $convitesPendentes }}</p>
                </div>
                <div class="flex min-h-[92px] min-w-0 flex-col justify-between rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:min-h-0 sm:p-5">
                    <p class="min-h-8 text-[11px] font-medium leading-tight text-slate-500 sm:min-h-0 sm:text-sm">Avaliações pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $pendingGames->sum(fn ($item) => $item->avaliados->count()) }}</p>
                </div>
            </section>


            @include('jogador.stories._bar', ['storyGroups' => $storyGroups])

            <section class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">

                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-900">Feed dos peladeiros</h2>

                        <p class="mt-1 text-sm text-slate-500">Suas publicações e as publicações recentes dos jogadores que você segue.</p>

                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('player-posts.index') }}" class="inline-flex w-fit items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                            Publicar
                        </a>
                        <a href="{{ route('jogadores.index') }}" class="inline-flex w-fit items-center justify-center rounded-md border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">
                            Encontrar jogadores
                        </a>
                    </div>

                </div>



                @if($feedPosts->isEmpty())

                    <div class="p-8 text-center">

                        <p class="font-semibold text-slate-900">Seu feed ainda está vazio</p>

                        <p class="mt-1 text-sm text-slate-500">Aínda não encontramos publicações para mostrar. Siga outros peladeiros para acompanhar as postagens deles por aqui.</p>

                        <a href="{{ route('jogadores.index') }}" class="mt-4 inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

                            Buscar peladeiros

                        </a>

                    </div>

                @else

                    <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">

                        @foreach($feedPosts as $post)

                            @php

                                $postAuthor = $post->user;

                                $postProfile = $postAuthor?->playerProfile ?: $postAuthor?->publicProfile();

                                $isLiked = in_array($post->id, $likedFeedPostIds, true);

                                $postUrl = $postProfile ? route('peladeiros.show', $postProfile).'#publicacao-'.$post->id : $post->mediaUrl();

                                $shareTitle = 'Publicação de '.($postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro');

                            @endphp



                            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">

                                <a href="{{ $postUrl }}" class="block">

                                    <img src="{{ $post->thumbnailUrl() }}" alt="Publicação de {{ $postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro' }}" class="aspect-square w-full object-cover">

                                </a>



                                <div class="space-y-4 p-4">

                                    <div class="flex items-center gap-3">

                                        @if($postAuthor)

                                            <x-user-avatar :user="$postAuthor" size="sm" />

                                        @endif

                                        <div class="min-w-0">

                                            <a href="{{ $postProfile ? route('peladeiros.show', $postProfile) : '#' }}" class="block truncate text-sm font-bold text-slate-950 hover:text-emerald-700">

                                                {{ $postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro' }}

                                            </a>

                                            <p class="truncate text-xs text-slate-500">{{ $postCategoryLabels[$post->categoria] ?? 'Momento' }} · {{ optional($post->publicado_em ?: $post->created_at)->format('d/m/Y') }}</p>

                                        </div>

                                    </div>



                                    @if($post->legenda)

                                        <p class="text-sm leading-6 text-slate-700">{{ $post->legenda }}</p>

                                    @endif



                                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">

                                        <div class="flex items-center gap-2">

                                            <form method="post" action="{{ route('player-posts.likes.toggle', $post) }}" data-like-form data-post-id="{{ $post->id }}">

                                                @csrf

                                                <button type="submit" data-like-button data-liked-classes="bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100" data-unliked-classes="text-slate-500 hover:bg-slate-100 hover:text-emerald-700" class="inline-flex h-9 w-9 items-center justify-center rounded-full transition {{ $isLiked ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-700' }}" aria-label="{{ $isLiked ? 'Remover curtida' : 'Curtir publicação' }}" aria-pressed="{{ $isLiked ? 'true' : 'false' }}">

                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                        <path d="M7 11v10H3V11h4z" />

                                                        <path d="M7 11l4.2-8a2.2 2.2 0 0 1 2.1 2.8L12 9h6.6a2 2 0 0 1 2 2.3l-1.2 7.4A2.7 2.7 0 0 1 16.7 21H7" />

                                                    </svg>

                                                </button>

                                            </form>



                                            {{-- <a href="{{ $postUrl }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" aria-label="Ver coment?rios">

                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />

                                                </svg>

                                            </a> --}}



                                            <button type="button" data-share-post data-share-url="{{ $postUrl }}" data-share-title="{{ $shareTitle }}" data-share-text="{{ $post->legenda ?: 'Olha esta publica??o no Vai Ter Pelada.' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" aria-label="Compartilhar publica??o">

                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                    <circle cx="18" cy="5" r="3" />

                                                    <circle cx="6" cy="12" r="3" />

                                                    <circle cx="18" cy="19" r="3" />

                                                    <path d="m8.6 13.5 6.8 4" />

                                                    <path d="m15.4 6.5-6.8 4" />

                                                </svg>

                                            </button>



                                            <a href="{{ $postUrl }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700" aria-label="Abrir publica??o">

                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                    <circle cx="12" cy="12" r="9" />

                                                    <path d="m12 3 3.4 5-3.4 3.2L8.6 8z" />

                                                    <path d="m3.5 10 5.1-2 3.4 3.2-1.3 4.2-5.4.2" />

                                                    <path d="m20.5 10-5.1-2-3.4 3.2 1.3 4.2 5.4.2" />

                                                    <path d="m7.5 20 3.2-4.6h2.6l3.2 4.6" />

                                                </svg>

                                            </a>

                                        </div>



                                        <span data-like-count="{{ $post->id }}" class="text-xs font-bold text-slate-500">{{ $post->likes_count }} curtida(s)</span>

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                @endif

            </section>


            @if($discoverPosts->isNotEmpty())

                <section class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">

                    <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <h2 class="text-base font-bold text-slate-900">Outros peladeiros</h2>

                            <p class="mt-1 text-sm text-slate-500">Publicações recentes de jogadores que você ainda não segue.</p>

                        </div>

                        <a href="{{ route('jogadores.index') }}" class="inline-flex w-fit items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                            Ver jogadores
                        </a>

                    </div>

                    <div class="grid grid-cols-2 gap-3 p-4 sm:gap-4 sm:p-5">

                        @foreach($discoverPosts as $post)

                            @php

                                $postAuthor = $post->user;

                                $postProfile = $postAuthor?->playerProfile ?: $postAuthor?->publicProfile();

                                $isLiked = in_array($post->id, $likedFeedPostIds, true);

                                $postUrl = $postProfile ? route('peladeiros.show', $postProfile).'#publicacao-'.$post->id : $post->mediaUrl();

                                $shareTitle = 'Publicação de '.($postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro');

                            @endphp


                            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">

                                <a href="{{ $postUrl }}" class="block">

                                    <img src="{{ $post->thumbnailUrl() }}" alt="Publicação de {{ $postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro' }}" class="aspect-[4/3] w-full object-cover">

                                </a>


                                <div class="space-y-3 p-3">

                                    <div class="flex min-w-0 items-center gap-2">

                                        @if($postAuthor)

                                            <x-user-avatar :user="$postAuthor" size="xs" />

                                        @endif

                                        <div class="min-w-0">

                                            <a href="{{ $postProfile ? route('peladeiros.show', $postProfile) : '#' }}" class="block truncate text-xs font-bold text-slate-950 hover:text-emerald-700">

                                                {{ $postAuthor?->apelido ?: $postAuthor?->name ?: 'Peladeiro' }}

                                            </a>

                                            <p class="truncate text-[11px] text-slate-500">{{ optional($post->publicado_em ?: $post->created_at)->format('d/m/Y') }}</p>

                                        </div>

                                    </div>


                                    @if($post->legenda)

                                        <p class="line-clamp-2 text-xs leading-5 text-slate-600">{{ $post->legenda }}</p>

                                    @endif


                                    <div class="flex items-center justify-between border-t border-slate-100 pt-2">

                                        <div class="flex items-center gap-1">

                                            <form method="post" action="{{ route('player-posts.likes.toggle', $post) }}" data-like-form data-post-id="{{ $post->id }}">

                                                @csrf

                                                <button type="submit" data-like-button data-liked-classes="bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100" data-unliked-classes="text-slate-500 hover:bg-slate-100 hover:text-emerald-700" class="inline-flex h-8 w-8 items-center justify-center rounded-full transition {{ $isLiked ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-700' }}" aria-label="{{ $isLiked ? 'Remover curtida' : 'Curtir publicação' }}" aria-pressed="{{ $isLiked ? 'true' : 'false' }}">

                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                        <path d="M7 11v10H3V11h4z" />

                                                        <path d="M7 11l4.2-8a2.2 2.2 0 0 1 2.1 2.8L12 9h6.6a2 2 0 0 1 2 2.3l-1.2 7.4A2.7 2.7 0 0 1 16.7 21H7" />

                                                    </svg>

                                                </button>

                                            </form>

                                            <button type="button" data-share-post data-share-url="{{ $postUrl }}" data-share-title="{{ $shareTitle }}" data-share-text="{{ $post->legenda ?: 'Olha esta publicação no Vai Ter Pelada.' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" aria-label="Compartilhar publicação">

                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

                                                    <circle cx="18" cy="5" r="3" />

                                                    <circle cx="6" cy="12" r="3" />

                                                    <circle cx="18" cy="19" r="3" />

                                                    <path d="m8.6 13.5 6.8 4" />

                                                    <path d="m15.4 6.5-6.8 4" />

                                                </svg>

                                            </button>

                                        </div>

                                        <span data-like-count="{{ $post->id }}" class="text-[11px] font-bold text-slate-500">{{ $post->likes_count }}</span>

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                </section>

            @endif



            <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">

                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="text-lg font-semibold text-slate-900">Próximas rodadas</h2>

                        <p class="mt-1 text-sm text-slate-500">Confirme presença ou acompanhe sua posição na fila.</p>

                    </div>

                    <div class="divide-y divide-slate-100">

                        @forelse($proximosJogos as $jogo)

                            @php

                                $participacao = $jogo->participantes->first();

                                $capacidade = $jogo->vagas_totais ?: $jogo->capacidade ?: $jogo->pelada->vagas_totais ?: $jogo->pelada->capacidade;

                                $vagas = max(0, $capacidade - $jogo->confirmados_count);

                            @endphp

                            <article class="grid gap-4 p-5 md:grid-cols-[1fr_auto] md:items-center">

                                <div>

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="font-semibold text-slate-900">{{ $jogo->pelada->nome }}</h3>

                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $jogo->pelada->esporte->nome }}</span>

                                        @if($participacao)

                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $participacao->status === 'confirmado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">

                                                {{ $participacao->status === 'confirmado' ? 'Confirmado' : 'Fila #' . $participacao->posicao_fila }}

                                            </span>

                                        @endif

                                    </div>

                                    <p class="mt-1 text-sm text-slate-600">{{ $jogo->titulo }} - {{ $jogo->data_hora->format('d/m/Y H:i') }}</p>

                                    <div class="mt-3 flex flex-wrap gap-3 text-sm text-slate-500">

                                        <span>{{ $jogo->confirmados_count }}/{{ $capacidade }} confirmados</span>

                                        <span>{{ $vagas }} vagas abertas</span>

                                        <span>{{ $jogo->fila_count }} na fila</span>

                                    </div>

                                </div>

                                <div class="flex flex-col gap-2 sm:flex-row md:justify-end">

                                    @if($participacao && in_array($participacao->status, ['confirmado', 'fila'], true))

                                        <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}" class="w-full sm:w-auto">

                                            @csrf

                                            @method('DELETE')

                                            <button class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto">Cancelar</button>

                                        </form>

                                    @else

                                        <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}" class="w-full sm:w-auto">

                                            @csrf

                                            <button class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 sm:w-auto">Confirmar</button>

                                        </form>

                                    @endif

                                </div>

                            </article>

                        @empty

                            <div class="p-8 text-center">

                                <h3 class="font-semibold text-slate-900">Nenhuma rodada futura encontrada</h3>

                                <p class="mt-2 text-sm text-slate-500">Entre em uma pelada, ou crie uma para organizar seu grupo.</p>

                            </div>

                        @endforelse

                    </div>

                </section>



                <aside class="space-y-6">

                    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="text-lg font-semibold text-slate-900">Mensagens recentes</h2>

                        <div class="mt-4 divide-y divide-slate-100">

                            @forelse($notificacoes as $notificacao)

                                @php $notificationActor = $notificationActors[$notificacao->id] ?? null; @endphp

                                <a href="{{ $notificacao->link ?: '#' }}" class="flex items-start gap-3 py-3 hover:bg-slate-50">

                                    @if($notificationActor)
                                        <x-user-avatar :user="$notificationActor" size="sm" />
                                    @endif

                                    <div class="min-w-0">

                                        <p class="text-sm font-semibold text-slate-900">{{ $notificacao->titulo }}</p>

                                        <p class="mt-1 text-xs text-slate-500">{{ $notificacao->mensagem }}</p>

                                    </div>

                                </a>

                            @empty

                                <p class="py-4 text-sm text-slate-500">Nenhuma mensagem nova.</p>

                            @endforelse

                        </div>

                    </section>

                    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="text-lg font-semibold text-slate-900">Minhas peladas</h2>

                        <div class="mt-4 space-y-3">

                            @forelse($membros->take(5) as $membro)

                                <a href="{{ route('peladas.show', $membro->pelada) }}" class="flex items-center gap-3 rounded-md border border-slate-200 p-4 hover:border-emerald-300">

                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-emerald-200 bg-emerald-100 text-sm font-bold text-emerald-900 shadow-sm">
                                        @if($membro->pelada->imagemUrl())
                                            <img src="{{ $membro->pelada->imagemUrl() }}" alt="{{ $membro->pelada->nome }}" class="h-full w-full object-cover">
                                        @else
                                            {{ Str::of($membro->pelada->nome ?: 'Pelada')->trim()->substr(0, 1)->upper() }}
                                        @endif
                                    </span>

                                    <span class="min-w-0">

                                        <span class="block truncate font-semibold text-slate-900">{{ $membro->pelada->nome }}</span>

                                        <span class="mt-1 block truncate text-sm text-slate-500">{{ $membro->pelada->local_nome ?: $membro->pelada->local }}</span>

                                    </span>

                                </a>

                            @empty

                                <div class="rounded-md border border-dashed border-slate-300 bg-slate-50 p-4">

                                    <p class="text-sm text-slate-500">Você ainda não participa de nenhuma pelada.</p>

                                    <a href="{{ route('peladas.index') }}" class="mt-3 inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                        Encontrar peladas
                                    </a>

                                </div>

                            @endforelse

                        </div>

                    </section>

                </aside>

            </div>

        @elseif($aba === 'peladas')

            <div class="mt-6 grid gap-6 lg:grid-cols-2">

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                    <h2 class="text-xl font-bold text-slate-950">Convites recebidos</h2>

                    <div class="mt-4 divide-y divide-slate-100">

                        @forelse($convites as $convite)

                            @php $tipoConvite = str_replace('convite_', '', $convite->tipo_solicitacao ?? ''); @endphp

                            <div class="py-4">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                    <div>

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$convite->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($convite->status) }}</span>

                                        <p class="mt-3 font-semibold text-slate-950">{{ $convite->pelada->nome }}</p>

                                        <p class="mt-1 text-xs text-slate-500">Convite para {{ $tipoLabels[$tipoConvite] ?? $tipoConvite }}</p>

                                    </div>

                                    @if($convite->status === 'pendente')

                                        <div class="flex shrink-0 gap-2">

                                            <form method="POST" action="{{ route('jogador.solicitacoes.aceitar-convite', $convite) }}">

                                                @csrf

                                                @method('PATCH')

                                                <button class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Aceitar</button>

                                            </form>

                                            <form method="POST" action="{{ route('jogador.solicitacoes.recusar-convite', $convite) }}">

                                                @csrf

                                                @method('PATCH')

                                                <button class="rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Recusar</button>

                                            </form>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @empty

                            <p class="py-8 text-center text-sm text-slate-500">Nenhum convite encontrado.</p>

                        @endforelse

                    </div>

                </section>



                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                    <h2 class="text-xl font-bold text-slate-950">Solicitações enviadas</h2>

                    <div class="mt-4 divide-y divide-slate-100">

                        @forelse($solicitacoes as $solicitacao)

                            @php $tipoSolicitacao = $solicitacao->tipo_solicitacao ?: $solicitacao->tipo; @endphp

                            <div class="py-4">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                    <div>

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$solicitacao->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($solicitacao->status) }}</span>

                                        <p class="mt-3 font-semibold text-slate-950">{{ $solicitacao->pelada->nome }}</p>

                                        <p class="mt-1 text-xs text-slate-500">{{ $tipoLabels[$tipoSolicitacao] ?? str_replace('_', ' ', ucfirst($tipoSolicitacao)) }}</p>

                                    </div>

                                    <a href="{{ route('peladas.show', $solicitacao->pelada) }}" class="inline-flex shrink-0 items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">

                                        Ver pelada

                                    </a>

                                </div>

                            </div>

                        @empty

                            <p class="py-8 text-center text-sm text-slate-500">Nenhuma solicitação encontrada.</p>

                        @endforelse

                    </div>

                </section>

            </div>



            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <h2 class="text-xl font-bold text-slate-950">Minhas participações</h2>

                <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">

                    @forelse($membros as $membro)

                        <article class="border-b border-slate-200 bg-white p-4 last:border-b-0">

                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                                <div class="flex min-w-0 flex-1 items-start gap-3">

                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-emerald-200 bg-emerald-100 text-sm font-bold text-emerald-900 shadow-sm">
                                        @if($membro->pelada->imagemUrl())
                                            <img src="{{ $membro->pelada->imagemUrl() }}" alt="{{ $membro->pelada->nome }}" class="h-full w-full object-cover">
                                        @else
                                            {{ Str::of($membro->pelada->nome ?: 'Pelada')->trim()->substr(0, 1)->upper() }}
                                        @endif
                                    </span>

                                    <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$membro->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($membro->status) }}</span>

                                        @php $tipoLabel = $tipoLabels[$membro->tipo] ?? ucfirst($membro->tipo); @endphp

                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">{{ $tipoLabel }}</span>

                                    </div>

                                    <h3 class="mt-3 text-lg font-bold text-slate-950">{{ $membro->pelada->nome }}</h3>

                                    <p class="mt-1 text-sm text-slate-600">{{ $membro->pelada->local_nome ?: $membro->pelada->local }}</p>

                                    </div>

                                </div>

                                <a href="{{ route('peladas.show', $membro->pelada) }}" class="inline-flex items-center justify-center rounded-md border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">

                                    Abrir pelada

                                </a>

                            </div>



                            @php $rodadas = $membro->pelada->jogos ?? collect(); @endphp

                            @php $rodadasConfirmadas = $rodadas->filter(fn ($jogo) => $jogo->participantes->first()?->status === 'confirmado')->values(); @endphp

                            @php $rodadasFila = $rodadas->filter(fn ($jogo) => $jogo->participantes->first()?->status === 'fila')->values(); @endphp

                            @php $rodadasParaConfirmar = $rodadas->filter(fn ($jogo) => ! in_array($jogo->participantes->first()?->status, ['confirmado', 'fila'], true))->values(); @endphp

                            @php $rodadasPendentes = $rodadasFila->merge($rodadasParaConfirmar); @endphp



                            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">

                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                                    <h4 class="font-semibold text-slate-900">Próximas rodadas</h4>

                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $rodadas->count() }} encontrada(s)</span>

                                </div>

                                <div class="mt-3 divide-y divide-slate-200">

                                    @forelse($rodadasPendentes as $jogo)

                                        @php $participacao = $jogo->participantes->first(); @endphp

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

                                                    <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}">

                                                        @csrf

                                                        @method('DELETE')

                                                        <button class="rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-white">Cancelar</button>

                                                    </form>

                                                @else

                                                    <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}">

                                                        @csrf

                                                        <button class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Confirmar</button>

                                                    </form>

                                                @endif

                                            </div>

                                        </div>

                                    @empty

                                        <p class="rounded-lg bg-white px-3 py-3 text-sm text-slate-500">Nenhuma rodada pendente de confirmação agora.</p>

                                    @endforelse

                                </div>

                            </div>

                        </article>

                    @empty

                        <div class="p-8 text-center">

                            <p class="font-semibold text-slate-900">Nenhuma pelada encontrada.</p>

                        </div>

                    @endforelse

                </div>

            </section>

        @elseif($aba === 'avaliacoes')

            <section class="mt-6 grid gap-4 md:grid-cols-4">

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-500">Média recebida</p>

                    <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($mediaRecebida, 2) }}</p>

                    <p class="mt-1 text-xs text-slate-500">de 5 estrelas</p>

                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-500">Recebidas</p>

                    <p class="mt-2 text-3xl font-bold text-slate-950">{{ $totalRecebidas }}</p>

                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-500">Feitas</p>

                    <p class="mt-2 text-3xl font-bold text-slate-950">{{ $totalFeitas }}</p>

                </div>

                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 shadow-sm">

                    <p class="text-sm font-medium text-emerald-800">Pendentes</p>

                    <p class="mt-2 text-3xl font-bold text-emerald-950">{{ $pendingGames->sum(fn ($item) => $item->avaliados->count()) }}</p>

                    <p class="mt-1 text-xs text-emerald-800">abertas por até 2 dias</p>

                </div>

            </section>



            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">

                <section id="avaliacoes-pendentes" class="scroll-mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="text-lg font-bold text-slate-950">Avaliações pendentes</h2>

                        <p class="mt-1 text-sm text-slate-600">Aparecem aqui partidas realizadas em que sua presença foi marcada no local.</p>

                    </div>

                    @if($pendingGames->isEmpty())

                        <div class="p-8 text-center">

                            <p class="font-semibold text-slate-900">Nenhuma avaliação pendente</p>

                            <p class="mt-1 text-sm text-slate-500">Depois de uma rodada com presença marcada, você poderá avaliar os jogadores presentes.</p>

                        </div>

                    @else

                        <div class="divide-y divide-slate-100">

                            @foreach($pendingGames as $item)

                                @php

                                    $jogo = $item->jogo;

                                    $pendentes = $item->votaveis->filter(fn ($p) => ! $p->avaliacao_atual);

                                    $avaliados = $item->votaveis->filter(fn ($p) => $p->avaliacao_atual);

                                @endphp

                                <article class="p-5">

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                                        <div>

                                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $jogo->pelada->esporte->nome }}</p>

                                            <h3 class="mt-1 font-bold text-slate-950">{{ $jogo->pelada->nome }}</h3>

                                            <p class="mt-1 text-sm text-slate-500">{{ $jogo->titulo }} - {{ $jogo->data_hora->format('d/m/Y H:i') }}</p>

                                        </div>

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $pendentes->count() }} pendente(s)</span>

                                    </div>

                                    <div class="mt-5 grid gap-3">

                                        @foreach($pendentes as $participante)

                                            @php

                                                $currentReview = $participante->avaliacao_atual;

                                                $needsReview = ! $currentReview;

                                                $oldForThisPlayer = (int) old('avaliado_id') === (int) $participante->user_id;

                                                $selectedVote = $oldForThisPlayer ? old('vote_type') : $participante->voto_atual;

                                                $selectedStars = $oldForThisPlayer ? old('estrelas') : optional($currentReview)->estrelas;

                                                $commentValue = $oldForThisPlayer ? old('comentario') : optional($currentReview)->comentario;

                                            @endphp

                                            @include('jogador.avaliacoes._card-avaliacao')

                                        @endforeach

                                    </div>

                                    @if($avaliados->isNotEmpty())

                                        <details class="mt-5 rounded-lg border border-slate-300 bg-slate-50 p-4">

                                            <summary class="cursor-pointer text-sm font-bold text-slate-700">Ver jogadores já avaliados ({{ $avaliados->count() }})</summary>

                                            <div class="mt-4 grid gap-3">

                                                @foreach($avaliados as $participante)

                                                    @php

                                                        $currentReview = $participante->avaliacao_atual;

                                                        $needsReview = ! $currentReview;

                                                        $oldForThisPlayer = (int) old('avaliado_id') === (int) $participante->user_id;

                                                        $selectedVote = $oldForThisPlayer ? old('vote_type') : $participante->voto_atual;

                                                        $selectedStars = $oldForThisPlayer ? old('estrelas') : optional($currentReview)->estrelas;

                                                        $commentValue = $oldForThisPlayer ? old('comentario') : optional($currentReview)->comentario;

                                                    @endphp

                                                    @include('jogador.avaliacoes._card-avaliacao')

                                                @endforeach

                                            </div>

                                        </details>

                                    @endif

                                </article>

                            @endforeach

                        </div>

                    @endif

                </section>



                <aside class="space-y-6">

                    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="text-lg font-bold text-slate-950">Recebidas recentes</h2>

                        <div class="mt-4 divide-y divide-slate-100">

                            @forelse($recebidas as $avaliacao)

                                <div class="py-3">

                                    <div class="flex items-center justify-between gap-3">

                                        <p class="font-semibold text-slate-900">{{ $avaliacao->estrelas }}/5</p>

                                        <p class="text-xs text-slate-500">{{ $avaliacao->created_at->format('d/m/Y') }}</p>

                                    </div>

                                    <p class="mt-1 text-xs text-slate-500">{{ $avaliacao->jogo->pelada->nome ?? 'Pelada' }} - por {{ $avaliacao->avaliador->name ?? 'Jogador' }}</p>

                                </div>

                            @empty

                                <p class="text-sm text-slate-500">Você ainda não recebeu avaliações.</p>

                            @endforelse

                        </div>

                    </section>

                    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="text-lg font-bold text-slate-950">Feitas recentes</h2>

                        <div class="mt-4 divide-y divide-slate-100">

                            @forelse($feitas as $avaliacao)

                                <div class="py-3">

                                    <div class="flex items-center justify-between gap-3">

                                        <p class="font-semibold text-slate-900">{{ $avaliacao->avaliado->name ?? 'Jogador' }}</p>

                                        <p class="text-xs text-slate-500">{{ $avaliacao->estrelas }}/5</p>

                                    </div>

                                    <p class="mt-1 text-xs text-slate-500">{{ $avaliacao->jogo->pelada->nome ?? 'Pelada' }} - {{ $avaliacao->created_at->format('d/m/Y') }}</p>

                                </div>

                            @empty

                                <p class="text-sm text-slate-500">Você ainda não avaliou jogadores.</p>

                            @endforelse

                        </div>

                    </section>

                </aside>

            </div>

        @elseif($aba === 'mensagens')

            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <h2 class="text-xl font-bold text-slate-950">Mensagens</h2>

                <div class="mt-4 divide-y divide-slate-100">

                    @forelse($notificacoes as $notificacao)

                        @php $notificationActor = $notificationActors[$notificacao->id] ?? null; @endphp

                        <a href="{{ $notificacao->link ?: '#' }}" class="flex items-start gap-3 py-4 transition hover:bg-slate-50">

                            @if($notificationActor)
                                <x-user-avatar :user="$notificationActor" size="sm" />
                            @endif

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-slate-900">{{ $notificacao->titulo }}</p>

                                <p class="mt-1 text-sm text-slate-600">{{ $notificacao->mensagem }}</p>

                                <p class="mt-1 text-xs text-slate-400">{{ $notificacao->created_at->format('d/m/Y H:i') }}</p>

                            </div>

                        </a>

                    @empty

                        <p class="py-8 text-center text-sm text-slate-500">Nenhuma mensagem nova.</p>

                    @endforelse

                </div>

            </section>

        @elseif($aba === 'organizacao' && $peladasOrganizadas->isNotEmpty())

            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-xl font-bold text-slate-950">Organização</h2>

                        <p class="mt-1 text-sm text-slate-600">Peladas que você criou ou onde atua como diretor.</p>

                    </div>

                    <a href="{{ route('organizador.peladas.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

                        Criar nova pelada

                    </a>

                </div>



                <div class="mt-5 grid gap-4 md:grid-cols-2">

                    @foreach($peladasOrganizadas as $pelada)

                        <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div class="flex min-w-0 flex-1 items-start gap-3">

                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-emerald-200 bg-emerald-100 text-sm font-bold text-emerald-900 shadow-sm">
                                        @if($pelada->imagemUrl())
                                            <img src="{{ $pelada->imagemUrl() }}" alt="{{ $pelada->nome }}" class="h-full w-full object-cover">
                                        @else
                                            {{ Str::of($pelada->nome ?: 'Pelada')->trim()->substr(0, 1)->upper() }}
                                        @endif
                                    </span>

                                    <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">{{ $pelada->esporte?->nome ?: 'Pelada' }}</span>

                                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">{{ ucfirst($pelada->status) }}</span>

                                        <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-bold text-white">{{ $pelada->isOwner(auth()->user()) ? 'Organizador' : 'Diretor' }}</span>

                                    </div>

                                    <h3 class="mt-3 text-lg font-black text-slate-950">{{ $pelada->nome }}</h3>

                                    <p class="mt-1 text-sm text-slate-600">{{ $pelada->local_nome ?: $pelada->local }}</p>

                                    <div class="mt-3 flex flex-wrap gap-3 text-xs font-semibold text-slate-500">

                                        <span>{{ $pelada->membros_count }} membro(s)</span>

                                        <span>{{ $pelada->jogos_count }} rodada(s)</span>

                                    </div>

                                    </div>

                                </div>

                                <div class="flex shrink-0 flex-col gap-2 sm:w-40">

                                    <a href="{{ route('organizador.peladas.jogos.index', $pelada) }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-3 py-2 text-xs font-bold text-white hover:bg-slate-800">Rodadas</a>

                                    <a href="{{ route('organizador.peladas.membros.index', $pelada) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Membros</a>

                                    @if($pelada->isOwner(auth()->user()) || auth()->user()->isAdmin())

                                        <a href="{{ route('organizador.peladas.edit', $pelada) }}" class="inline-flex items-center justify-center rounded-md border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">Editar</a>

                                    @endif

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            </section>

        @endif

    </div>

</div>
