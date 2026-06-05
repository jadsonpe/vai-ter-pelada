<x-app-layout>
    @php
        $enderecoCompleto = collect([
            $pelada->local_nome ?: $pelada->local,
            $pelada->endereco,
            $pelada->bairro,
            $pelada->cidade,
        ])->filter()->implode(', ');
    @endphp
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="grid gap-0 lg:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)]">
                <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" class="aspect-[16/9] h-full min-h-[260px] w-full overflow-hidden bg-slate-100 lg:aspect-auto" />

                <div class="p-6 lg:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                            {{ $pelada->esporte->nome }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ ucfirst($pelada->status) }}
                        </span>
                        <span class="rounded-full bg-slate-950 px-3 py-1 text-xs font-semibold text-white">
                            {{ $pelada->categoriaLabel() }}
                        </span>
                    </div>

                    <h1 class="mt-4 text-3xl font-bold text-slate-950 sm:text-4xl">{{ $pelada->nome }}</h1>
                    <p class="mt-3 leading-7 text-slate-600">{{ $pelada->descricao ?: 'Pelada recorrente aberta para confirmação de jogadores.' }}</p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Mensalista</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">
                                {{ $pelada->valor_mensalista ? 'R$ '.number_format($pelada->valor_mensalista, 2, ',', '.') : 'A combinar' }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diarista</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">
                                {{ $pelada->valor_diarista ? 'R$ '.number_format($pelada->valor_diarista, 2, ',', '.') : 'A combinar' }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Vagas totais</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">{{ $pelada->vagas_totais ?: $pelada->capacidade }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Vagas diaristas</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">{{ $pelada->vagas_diaristas ?: 0 }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Categoria</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">{{ $pelada->categoriaLabel() }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fundação</span>
                            <p class="mt-1 text-xl font-bold text-slate-950">{{ $pelada->data_fundacao ? $pelada->data_fundacao->format('d/m/Y') : 'Não informada' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-8">
            <main class="space-y-8">
                <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-950">Informações da pelada</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Local</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->local_nome ?: $pelada->local }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Organizador</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->organizador->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Cidade</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->cidade ?: 'Não informada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Bairro</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->bairro ?: 'Não informado' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-sm font-semibold text-slate-500">Endereço</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $enderecoCompleto ?: 'Não informado' }}</p>
                            @if($pelada->mapsUrl())
                                <a href="{{ $pelada->mapsUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                    Abrir no Google Maps
                                </a>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Aceita diarista</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->aceita_diarista ? 'Sim' : 'Não' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Aprovação obrigatória</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $pelada->requer_aprovacao ? 'Sim' : 'Não' }}</p>
                        </div>
                    </div>

                    @if($pelada->regras)
                        <details class="group mt-6 rounded-lg border border-slate-200 bg-slate-50">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3">
                                <span>
                                    <span class="block text-sm font-bold text-slate-900">Regras da pelada</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">Clique para ver as regras definidas pelo organizador.</span>
                                </span>
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white text-slate-600 ring-1 ring-slate-200 transition group-open:rotate-180">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="border-t border-slate-200 px-4 py-4">
                                <p class="whitespace-pre-line leading-7 text-slate-700">{{ $pelada->regras }}</p>
                            </div>
                        </details>
                    @endif
                </section>

                @if(! $isOwner)
                    @include('partials.report-panel', [
                        'title' => 'Denunciar pelada',
                        'description' => 'Informe problemas como pelada falsa, dados incorretos, cobrança suspeita ou conduta abusiva.',
                        'action' => route('denuncias.peladas.store', $pelada),
                        'reasons' => $reportReasons,
                        'loginRedirect' => route('peladas.show', $pelada),
                    ])
                @endif

                <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-950">Jogadores da pelada</h2>
                            <p class="mt-1 text-sm text-slate-600">Clique em um jogador para ver informações básicas do perfil.</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ $membrosAtivosCount }} ativo(s)</span>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($membrosPreview as $membroPelada)
                            <a href="{{ route('peladeiros.show', $membroPelada->user->publicProfile()) }}" class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 hover:border-emerald-300 hover:bg-emerald-50/40">
                                <x-user-avatar :user="$membroPelada->user" size="sm" />
                                <span class="min-w-0">
                                    <span class="block truncate font-semibold text-slate-900">{{ $membroPelada->nomeExibicao() }}</span>
                                    <span class="block text-xs font-medium uppercase text-slate-500">{{ $membroPelada->tipo }}</span>
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500 sm:col-span-2 lg:col-span-3">Nenhum jogador ativo listado ainda.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-emerald-200 bg-white p-6 shadow-sm">
                    @guest
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-slate-950">Quero participar</h2>
                                <p class="mt-2 text-sm text-slate-600">Crie sua conta ou entre para pedir participação e confirmar presença nas rodadas.</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <a href="{{ route('register', ['redirect' => url()->current()]) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                    Cadastrar para participar
                                </a>
                                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                    Entrar
                                </a>
                            </div>
                        </div>
                    @else
                        @if($isOwner)
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-950">Você organiza esta pelada</h2>
                                    <p class="mt-2 text-sm text-slate-600">Como criador da pelada, você já entra como membro mensalista ativo.</p>
                                </div>
                                <a href="{{ route('organizador.peladas.jogos.index', $pelada) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                    Gerenciar rodadas
                                </a>
                            </div>
                        @elseif($membro)
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-950">Você já participa</h2>
                                    <p class="mt-2 text-sm text-slate-600">Seu status nesta pelada:</p>
                                    <div class="mt-3 inline-flex rounded-md bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                                        {{ ucfirst($membro->tipo) }} - {{ ucfirst($membro->status) }}
                                    </div>
                                    @if($solicitacaoPendente)
                                        <p class="mt-4 rounded-md bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">Você já tem uma solicitação pendente.</p>
                                    @endif
                                </div>

                                @if($membro->tipo === 'diarista' && !$solicitacaoPendente)
                                    <form method="POST" action="{{ route('jogador.peladas.solicitar-mensalista', $pelada) }}" class="w-full space-y-3 lg:max-w-md">
                                        @csrf
                                        <textarea name="mensagem" class="w-full rounded-md border-slate-300" rows="3" placeholder="Mensagem opcional"></textarea>
                                        <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700">Quero virar mensalista</button>
                                    </form>
                                @endif
                            </div>
                        @elseif($solicitacaoPendente)
                            <h2 class="text-xl font-bold text-slate-950">Solicitacao em analise</h2>
                            <p class="mt-3 rounded-md bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">Seu pedido já foi enviado ao organizador.</p>
                        @else
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-950">Quero participar</h2>
                                    <p class="mt-2 text-sm text-slate-600">Envie um pedido para o organizador aprovar sua entrada como diarista.</p>
                                </div>
                                <form method="POST" action="{{ route('jogador.peladas.solicitar-mensalista', $pelada) }}" class="w-full space-y-3 lg:max-w-md">
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
                                    <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700">Pedir para participar</button>
                                </form>
                            </div>
                        @endif
                    @endguest
                </section>

                <section>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-950">Próximas rodadas</h2>
                            <p class="mt-1 text-sm text-slate-600">Confira as rodadas abertas e confirme sua presença se você já participa da pelada.</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ $rodadas->count() }} próxima(s)</span>
                    </div>

                    <div class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white shadow-sm">
                        @forelse($rodadas as $jogo)
                            @php
                                $participacao = auth()->check()
                                    ? $jogo->participantes->firstWhere('user_id', auth()->id())
                                    : null;
                                $confirmados = $jogo->participantes->where('status', 'confirmado')->count();
                                $capacidade = $jogo->vagas_totais ?: $jogo->capacidade ?: $pelada->vagas_totais ?: $pelada->capacidade;
                            @endphp
                            <article class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-slate-950">{{ $jogo->titulo }}</h3>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($jogo->status) }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $confirmados }}/{{ $capacidade }} confirmados</p>
                                    @if($jogo->observacao)
                                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $jogo->observacao }}</p>
                                    @endif
                                </div>

                                @auth
                                    @if($membro && $membro->status === 'ativo')
                                        @if($participacao && in_array($participacao->status, ['confirmado', 'fila'], true))
                                            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                                                <span class="rounded-md px-4 py-2 text-sm font-semibold {{ $participacao->status === 'confirmado' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                                    {{ $participacao->status === 'confirmado' ? 'Presença confirmada' : 'Fila #'.$participacao->posicao_fila }}
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
                                                <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 sm:w-auto">Confirmar presença</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="rounded-md bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">
                                            Solicite participação para confirmar
                                        </span>
                                    @endif
                                @else
                                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                                        <a href="{{ route('register', ['redirect' => url()->current()]) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Cadastrar</a>
                                        <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Entrar</a>
                                    </div>
                                @endauth
                            </article>
                        @empty
                            <p class="p-5 text-sm text-slate-600">Nenhuma rodada aberta no momento.</p>
                        @endforelse
                    </div>

                </section>
            </main>
        </div>
    </div>
</x-app-layout>
