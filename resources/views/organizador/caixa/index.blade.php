<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.index') }}" class="text-sm font-semibold text-emerald-700">Voltar para minhas peladas</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Caixa - {{ $pelada->nome }}</h1>
                <p class="mt-1 text-sm text-slate-600">Controle mensalidades, diarias e despesas da pelada.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('organizador.peladas.caixa.index', $pelada) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[180px_1fr_auto]">
            <div>
                <label for="mes" class="text-sm font-medium text-slate-700">Mes</label>
                <input id="mes" type="month" name="mes" value="{{ $competencia->format('Y-m') }}" class="mt-1 w-full rounded-md border-slate-300">
            </div>
            <div>
                <label for="jogo_id" class="text-sm font-medium text-slate-700">Rodada para diarias</label>
                <select id="jogo_id" name="jogo_id" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="">Rodada mais recente</option>
                    @foreach($jogos as $jogo)
                        <option value="{{ $jogo->id }}" @selected(optional($jogoSelecionado)->id === $jogo->id)>{{ $jogo->titulo }} - {{ $jogo->data_hora->format('d/m/Y H:i') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full rounded-md bg-slate-900 px-4 py-2 font-semibold text-white md:w-auto">Filtrar</button>
            </div>
        </form>

        <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Entradas no mes</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">R$ {{ number_format($entradas, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Saidas no mes</p>
                <p class="mt-2 text-2xl font-bold text-red-700">R$ {{ number_format($saidas, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Saldo do mes</p>
                <p class="mt-2 text-2xl font-bold {{ $saldo >= 0 ? 'text-slate-900' : 'text-red-700' }}">R$ {{ number_format($saldo, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Saldo geral</p>
                <p class="mt-2 text-2xl font-bold {{ $saldoGeral >= 0 ? 'text-slate-900' : 'text-red-700' }}">R$ {{ number_format($saldoGeral, 2, ',', '.') }}</p>
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px] 2xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="space-y-6">
                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-5">
                        <h2 class="font-semibold text-slate-900">Mensalidades de {{ $competencia->format('m/Y') }}</h2>
                        <p class="mt-1 text-sm text-slate-600">Mensalistas ativos que devem pagar a mensalidade do mes.</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($mensalistas as $membro)
                            @php($pagamento = $mensalidadesPagas->get($membro->id))
                            <div class="grid gap-3 p-4 lg:grid-cols-[minmax(0,1fr)_120px_110px_210px] lg:items-center">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">{{ $membro->nomeExibicao() }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $membro->user->email }}</p>
                                </div>
                                <span class="inline-flex w-full items-center justify-center rounded-md px-3 py-2 text-sm font-semibold lg:w-auto {{ $pagamento ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $pagamento ? 'Pago' : 'Pendente' }}
                                </span>
                                <p class="text-sm font-semibold text-slate-700 lg:text-right">
                                    R$ {{ number_format($pagamento?->valor ?? ($pelada->valor_mensalista ?: 0), 2, ',', '.') }}
                                </p>
                                @if(! $pagamento)
                                    <form method="POST" action="{{ route('organizador.peladas.caixa.mensalidades.store', [$pelada, $membro]) }}" class="grid gap-2 sm:grid-cols-[1fr_auto] lg:grid-cols-[90px_110px]">
                                        @csrf
                                        <input type="hidden" name="mes" value="{{ $competencia->format('Y-m') }}">
                                        <input name="valor" value="{{ old('valor', $pelada->valor_mensalista ?: '') }}" class="h-10 w-full rounded-md border-slate-300 text-sm" placeholder="Valor">
                                        <button class="h-10 rounded-md bg-emerald-600 px-3 text-sm font-semibold leading-tight text-white">Marcar pago</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('organizador.peladas.caixa.destroy', [$pelada, $pagamento]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Remover este pagamento do caixa?')" class="h-10 w-full rounded-md border border-red-200 px-3 text-sm font-semibold text-red-700 lg:w-[110px]">Estornar</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="p-5 text-sm text-slate-600">Nenhum mensalista ativo nesta pelada.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-5">
                        <h2 class="font-semibold text-slate-900">Diarias da rodada</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $jogoSelecionado ? $jogoSelecionado->titulo.' - '.$jogoSelecionado->data_hora->format('d/m/Y H:i') : 'Nenhuma rodada selecionada.' }}</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @php($participantesDiaristas = $jogoSelecionado ? $jogoSelecionado->participantes->where('status', 'confirmado')->where('tipo', 'diarista') : collect())
                        @forelse($participantesDiaristas as $participante)
                            @php($pagamento = $diariasPagas->get($participante->id))
                            <div class="grid gap-3 p-4 lg:grid-cols-[minmax(0,1fr)_120px_110px_210px] lg:items-center">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">{{ $participante->membro?->nomeExibicao() ?: $participante->user->name }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $participante->user->email }}</p>
                                </div>
                                <span class="inline-flex w-full items-center justify-center rounded-md px-3 py-2 text-sm font-semibold lg:w-auto {{ $pagamento ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $pagamento ? 'Pago' : 'Pendente' }}
                                </span>
                                <p class="text-sm font-semibold text-slate-700 lg:text-right">
                                    R$ {{ number_format($pagamento?->valor ?? ($pelada->valor_diarista ?: 0), 2, ',', '.') }}
                                </p>
                                @if(! $pagamento)
                                    <form method="POST" action="{{ route('organizador.peladas.caixa.diarias.store', [$pelada, $jogoSelecionado, $participante]) }}" class="grid gap-2 sm:grid-cols-[1fr_auto] lg:grid-cols-[90px_110px]">
                                        @csrf
                                        <input name="valor" value="{{ old('valor', $pelada->valor_diarista ?: '') }}" class="h-10 w-full rounded-md border-slate-300 text-sm" placeholder="Valor">
                                        <button class="h-10 rounded-md bg-emerald-600 px-3 text-sm font-semibold leading-tight text-white">Marcar pago</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('organizador.peladas.caixa.destroy', [$pelada, $pagamento]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Remover este pagamento do caixa?')" class="h-10 w-full rounded-md border border-red-200 px-3 text-sm font-semibold text-red-700 lg:w-[110px]">Estornar</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="p-5 text-sm text-slate-600">Nenhum diarista confirmado nesta rodada.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-900">Lancamento avulso</h2>
                    <p class="mt-1 text-sm text-slate-600">Use para aluguel de campo, compra de bola, coletes, patrocinios ou ajustes.</p>
                    <form method="POST" action="{{ route('organizador.peladas.caixa.store', $pelada) }}" class="mt-4 space-y-3">
                        @csrf
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="text-sm font-medium text-slate-700">Tipo
                                <select name="tipo" class="mt-1 w-full rounded-md border-slate-300">
                                    <option value="saida">Saida</option>
                                    <option value="entrada">Entrada</option>
                                </select>
                            </label>
                            <label class="text-sm font-medium text-slate-700">Categoria
                                <select name="categoria" class="mt-1 w-full rounded-md border-slate-300">
                                    <option value="aluguel">Aluguel</option>
                                    <option value="material">Material</option>
                                    <option value="patrocinio">Patrocinio</option>
                                    <option value="ajuste">Ajuste</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </label>
                        </div>
                        <label class="text-sm font-medium text-slate-700">Descricao
                            <input name="descricao" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Aluguel da quadra">
                        </label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="text-sm font-medium text-slate-700">Valor
                                <input name="valor" class="mt-1 w-full rounded-md border-slate-300" placeholder="150,00">
                            </label>
                            <label class="text-sm font-medium text-slate-700">Data
                                <input type="date" name="data_pagamento" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-md border-slate-300">
                            </label>
                        </div>
                        <label class="text-sm font-medium text-slate-700">Forma de pagamento
                            <input name="forma_pagamento" class="mt-1 w-full rounded-md border-slate-300" placeholder="Pix, dinheiro, cartão">
                        </label>
                        <button class="w-full rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Registrar lancamento</button>
                    </form>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-5">
                        <h2 class="font-semibold text-slate-900">Extrato do mes</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($movimentacoes as $movimentacao)
                            <div class="p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900">{{ $movimentacao->descricao }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ ucfirst($movimentacao->categoria) }} - {{ $movimentacao->data_pagamento->format('d/m/Y') }}</p>
                                    </div>
                                    <p class="shrink-0 font-bold sm:text-right {{ $movimentacao->tipo === 'entrada' ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $movimentacao->tipo === 'entrada' ? '+' : '-' }} R$ {{ number_format($movimentacao->valor, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="p-5 text-sm text-slate-600">Nenhum lancamento neste mes.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
