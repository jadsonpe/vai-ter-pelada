<div class="bg-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <section class="overflow-hidden rounded-lg bg-slate-950 text-white shadow-sm">
            <div class="grid gap-6 px-6 py-8 lg:grid-cols-[1fr_320px] lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Painel do jogador</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Oi, {{ auth()->user()->name }}</h1>
                    <p class="mt-3 max-w-2xl text-slate-300">Encontre peladas para jogar ou crie a sua própria. Quando você cria uma pelada, passa a ser o organizador dela.</p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">Encontrar peladas</a>
                        <a href="{{ route('organizador.peladas.create') }}" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-slate-100">Criar pelada</a>
                        <a href="{{ route('jogador.peladas.minhas') }}" class="inline-flex items-center justify-center rounded-md border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Minhas peladas</a>
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <div class="flex items-center gap-4">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-16 w-16 rounded-full border-2 border-emerald-300 object-cover">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-2xl font-bold text-slate-950">
                                {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-slate-300">{{ auth()->user()->email }}</p>
                            <span class="mt-2 inline-flex rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-200">{{ auth()->user()->role }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Peladas</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $membros->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Mensalista</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $mensalistasCount }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Diarista</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $diaristasCount }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Confirmações recentes</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $confirmacoesCount }}</p>
            </div>
        </section>

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
                            <div class="mt-4 flex flex-col justify-center gap-3 sm:flex-row">
                                <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Ver peladas</a>
                                <a href="{{ route('organizador.peladas.create') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Criar pelada</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Mensagens</h2>
                    <div class="mt-4 divide-y divide-slate-100">
                        @forelse($notificacoes as $notificacao)
                            <a href="{{ $notificacao->link ?: '#' }}" class="block py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $notificacao->titulo }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $notificacao->mensagem }}</p>
                                    </div>
                                    @if(!$notificacao->lida_em)
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Nova</span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">Nenhuma mensagem nova.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Minhas peladas</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($membros as $membro)
                            <a href="{{ route('peladas.show', $membro->pelada) }}" class="block rounded-md border border-slate-200 p-4 hover:border-emerald-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $membro->pelada->nome }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $membro->pelada->local_nome ?: $membro->pelada->local }}</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $membro->tipo }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">Você ainda não participa de nenhuma pelada.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Histórico recente</h2>
                    <div class="mt-4 divide-y divide-slate-100">
                        @forelse($participacoes as $participacao)
                            <div class="py-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $participacao->jogo->pelada->nome }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $participacao->jogo->titulo }} - {{ $participacao->status }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Nenhuma participação registrada ainda.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
