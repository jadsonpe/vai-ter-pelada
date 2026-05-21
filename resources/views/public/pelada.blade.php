<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <main>
                <p class="text-sm font-semibold text-emerald-700">{{ $pelada->esporte->nome }}</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">{{ $pelada->nome }}</h1>
                <p class="mt-3 text-slate-600">{{ $pelada->descricao ?: 'Pelada recorrente aberta para confirmação de jogadores.' }}</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Local</span><p class="font-semibold">{{ $pelada->local_nome ?: $pelada->local }}</p></div>
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Capacidade</span><p class="font-semibold">{{ $pelada->vagas_totais ?: $pelada->capacidade }}</p></div>
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Organizador</span><p class="font-semibold">{{ $pelada->organizador->name }}</p></div>
                </div>
            </main>
            <aside class="rounded-lg border border-slate-200 bg-white p-5">
                @guest
                    <h2 class="font-semibold text-slate-900">Quero jogar</h2>
                    <p class="mt-2 text-sm text-slate-600">Entre na sua conta para pedir participação ou confirmar presença nas rodadas.</p>
                    <a href="{{ route('login') }}" class="mt-4 block rounded-md bg-emerald-600 px-4 py-2 text-center font-semibold text-white">Entrar para participar</a>
                @else
                    @if($isOwner)
                        <h2 class="font-semibold text-slate-900">Você organiza esta pelada</h2>
                        <p class="mt-2 text-sm text-slate-600">Como criador da pelada, você já entra como membro mensalista ativo.</p>
                        <a href="{{ route('organizador.peladas.jogos.index', $pelada) }}" class="mt-4 block rounded-md bg-emerald-600 px-4 py-2 text-center font-semibold text-white">Gerenciar rodadas</a>
                    @elseif($membro)
                        <h2 class="font-semibold text-slate-900">Você já participa</h2>
                        <p class="mt-2 text-sm text-slate-600">Seu status nesta pelada:</p>
                        <div class="mt-3 rounded-md bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                            {{ ucfirst($membro->tipo) }} - {{ ucfirst($membro->status) }}
                        </div>

                        @if($membro->tipo === 'diarista' && !$solicitacaoPendente)
                            <form method="POST" action="{{ route('jogador.peladas.solicitar-mensalista', $pelada) }}" class="mt-4 space-y-3">
                                @csrf
                                <textarea name="mensagem" class="w-full rounded-md border-slate-300" rows="3" placeholder="Mensagem opcional"></textarea>
                                <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Quero virar mensalista</button>
                            </form>
                        @elseif($solicitacaoPendente)
                            <p class="mt-4 rounded-md bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">Você já tem uma solicitação pendente.</p>
                        @endif
                    @elseif($solicitacaoPendente)
                        <h2 class="font-semibold text-slate-900">Solicitação em análise</h2>
                        <p class="mt-2 rounded-md bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">Seu pedido já foi enviado ao organizador.</p>
                    @else
                        <h2 class="font-semibold text-slate-900">Quero participar</h2>
                        <p class="mt-2 text-sm text-slate-600">Envie um pedido para o organizador aprovar sua entrada como diarista.</p>
                        <form method="POST" action="{{ route('jogador.peladas.solicitar-mensalista', $pelada) }}" class="mt-4 space-y-3">
                            @csrf
                            @if(blank(auth()->user()->phone))
                                <div>
                                    <label for="phone" class="text-sm font-medium text-slate-700">WhatsApp</label>
                                    <input id="phone" name="phone" value="{{ old('phone') }}" required class="mt-1 w-full rounded-md border-slate-300" placeholder="(81) 99999-9999">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            @else
                                <div class="rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                    WhatsApp cadastrado: <strong>{{ auth()->user()->phone }}</strong>
                                </div>
                            @endif
                            <textarea name="mensagem" class="w-full rounded-md border-slate-300" rows="3" placeholder="Mensagem opcional"></textarea>
                            <x-input-error :messages="$errors->get('mensagem')" class="mt-2" />
                            <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Pedir para participar</button>
                        </form>
                    @endif
                @endguest
            </aside>
        </div>

        <section class="mt-10">
            <h2 class="text-xl font-bold text-slate-900">Próximas rodadas</h2>
            <div class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @forelse($pelada->jogos as $jogo)
                    @php
                        $participacao = auth()->check()
                            ? $jogo->participantes->firstWhere('user_id', auth()->id())
                            : null;
                    @endphp
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $jogo->titulo }}</h3>
                            <p class="text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }} - {{ $jogo->participantes->where('status', 'confirmado')->count() }} confirmados</p>
                        </div>
                        @auth
                            @if($membro && $membro->status === 'ativo')
                                @if($participacao && in_array($participacao->status, ['confirmado', 'fila'], true))
                                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                                        <span class="rounded-md px-4 py-2 text-sm font-semibold {{ $participacao->status === 'confirmado' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                            {{ $participacao->status === 'confirmado' ? 'Presença confirmada' : 'Fila #' . $participacao->posicao_fila }}
                                        </span>
                                        <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto">Cancelar presença</button>
                                        </form>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}">
                                        @csrf
                                        <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white sm:w-auto">Confirmar presença</button>
                                    </form>
                                @endif
                            @else
                                <span class="rounded-md bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">
                                    Solicite participação para confirmar
                                </span>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white sm:w-auto">Entrar para confirmar</a>
                        @endauth
                    </div>
                @empty
                    <p class="p-4 text-sm text-slate-600">Nenhuma rodada criada ainda.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
