<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Minhas peladas</h1>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach($membros as $membro)
                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-700">{{ $membro->tipo }} - {{ $membro->status }}</p>
                            <h2 class="mt-1 text-xl font-semibold">{{ $membro->pelada->nome }}</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $membro->pelada->local }}</p>
                        </div>
                        <a href="{{ route('peladas.show', $membro->pelada) }}" class="inline-flex w-full items-center justify-center rounded-md border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 sm:w-auto">Abrir</a>
                    </div>
                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach($membro->pelada->jogos as $jogo)
                            @php($participacao = $jogo->participantes->first())
                            <div class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $jogo->titulo }}</p>
                                    <p class="text-xs text-slate-500">{{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    @if($participacao && in_array($participacao->status, ['confirmado', 'fila'], true))
                                        <span class="inline-flex items-center justify-center rounded-md px-3 py-2 text-xs font-semibold {{ $participacao->status === 'confirmado' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                            {{ $participacao->status === 'confirmado' ? 'Confirmado' : 'Fila #'.$participacao->posicao_fila }}
                                        </span>
                                        <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}" class="w-full sm:w-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold sm:w-auto">Cancelar</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}" class="w-full sm:w-auto">
                                            @csrf
                                            <button class="w-full rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white sm:w-auto">Confirmar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <section class="mt-8 rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-slate-900">Convites e solicitacoes</h2>
            <div class="mt-3 divide-y divide-slate-100">
                @forelse($solicitacoes as $solicitacao)
                    @php($isConvite = str_starts_with($solicitacao->tipo_solicitacao ?? '', 'convite_'))
                    <div class="flex flex-col gap-3 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $solicitacao->pelada->nome }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase text-slate-500">
                                {{ $isConvite ? 'Convite para '.str_replace('convite_', '', $solicitacao->tipo_solicitacao) : str_replace('_', ' ', $solicitacao->tipo_solicitacao ?: $solicitacao->tipo) }}
                                - {{ $solicitacao->status }}
                            </p>
                            @if($solicitacao->mensagem)
                                <p class="mt-1 text-sm text-slate-600">{{ $solicitacao->mensagem }}</p>
                            @endif
                        </div>
                        @if($isConvite && $solicitacao->status === 'pendente')
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <form method="POST" action="{{ route('jogador.solicitacoes.aceitar-convite', $solicitacao) }}" class="w-full sm:w-auto">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white sm:w-auto">Aceitar</button>
                                </form>
                                <form method="POST" action="{{ route('jogador.solicitacoes.recusar-convite', $solicitacao) }}" class="w-full sm:w-auto">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 sm:w-auto">Recusar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">Nenhum convite ou solicitacao encontrada.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
