<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.jogos.index', $jogo->pelada) }}" class="text-sm font-semibold text-emerald-700">Voltar para rodadas</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Participantes - {{ $jogo->titulo }}</h1>
            </div>
            <a href="{{ route('organizador.peladas.jogos.index', $jogo->pelada) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Voltar</a>
        </div>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Confirmar mensalista</h2>
                    <p class="mt-1 text-sm text-slate-600">Adicione um mensalista ativo desta pelada diretamente como confirmado na rodada.</p>
                </div>
                <form method="POST" action="{{ route('organizador.jogos.participantes.confirmar-mensalista', $jogo) }}" class="flex w-full flex-col gap-2 sm:flex-row md:max-w-xl">
                    @csrf
                    <select name="pelada_membro_id" class="w-full rounded-md border-slate-300" @disabled($mensalistasDisponiveis->isEmpty())>
                        <option value="">Selecione um mensalista</option>
                        @foreach($mensalistasDisponiveis as $membro)
                            <option value="{{ $membro->id }}">{{ $membro->nomeExibicao() }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60" @disabled($mensalistasDisponiveis->isEmpty())>Confirmar</button>
                </form>
            </div>
            <x-input-error :messages="$errors->get('pelada_membro_id')" class="mt-2" />
            @if($mensalistasDisponiveis->isEmpty())
                <p class="mt-3 text-sm text-slate-500">Todos os mensalistas ativos já estão confirmados, na fila ou não há mensalistas ativos cadastrados nesta pelada.</p>
            @endif
        </section>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-900">Confirmados</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($jogo->participantes->where('status', 'confirmado')->sortBy('ordem_chegada')->values() as $index => $participante)
                        <div class="flex flex-col gap-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <x-user-avatar :user="$participante->user" size="xs" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $participante->membro?->nomeExibicao() ?: $participante->user->name }}</p>
                                    <p class="text-xs font-semibold uppercase text-slate-500">{{ $participante->tipo }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('organizador.jogos.participantes.remover', [$jogo, $participante]) }}" class="w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button class="w-full rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 sm:w-auto">Remover</button>
                            </form>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-slate-500">Nenhum participante confirmado ainda.</p>
                    @endforelse
                </div>
            </section>
            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-900">Fila de espera</h2>
                <p class="mt-1 text-sm text-slate-600">Quando a rodada está lotada, novas confirmações entram aqui. Ao remover um confirmado, o primeiro da fila é promovido automaticamente, priorizando mensalistas.</p>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($jogo->participantes->where('status', 'fila')->sortBy('posicao_fila') as $participante)
                        <p class="py-2 text-sm">#{{ $participante->posicao_fila }} {{ $participante->membro?->nomeExibicao() ?: $participante->user->name }} - {{ $participante->tipo }}</p>
                    @empty
                        <p class="py-2 text-sm text-slate-500">Nenhum jogador na fila de espera.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
