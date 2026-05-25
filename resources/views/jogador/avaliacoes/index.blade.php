<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Avaliações pendentes</h1>
                <p class="mt-2 text-slate-600">Avalie os jogadores que estiveram presentes nas suas partidas nos últimos 3 dias.</p>
            </div>
            <a href="{{ route('jogador.peladas.minhas') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Minhas peladas</a>
        </div>

        @if($pendingGames->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-600 shadow-sm">
                <p class="text-lg font-semibold text-slate-900">Nenhuma avaliação pendente no momento</p>
                <p class="mt-2">Partidas finalizadas nos últimos 3 dias aparecerão aqui após o registro de presença.</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($pendingGames as $item)
                    @php($jogo = $item->jogo)
                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ $jogo->pelada->esporte->nome }}</p>
                                    <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $jogo->pelada->nome }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">{{ $jogo->titulo }} • {{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">Avaliações abertas</span>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($item->avaliados as $participante)
                                <div class="px-6 py-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center gap-3">
                                            <x-user-avatar :user="$participante->user" size="sm" />
                                            <div>
                                                <p class="text-base font-semibold text-slate-900">{{ $participante->user->name }}</p>
                                                <p class="text-sm text-slate-500">Presente na partida e disponível para avaliação.</p>
                                            </div>
                                        </div>
                                        <details class="group w-full sm:w-auto">
                                            <summary class="inline-flex cursor-pointer items-center justify-between rounded-full border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 sm:px-5">
                                                Avaliar
                                                <span class="ml-2 rounded-full bg-emerald-500 px-2 py-0.5 text-xs font-semibold text-white">+</span>
                                            </summary>
                                            <div class="mt-4 rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                                <form method="POST" action="{{ route('jogador.avaliacoes.store') }}" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="pelada_jogo_id" value="{{ $jogo->id }}" />
                                                    <input type="hidden" name="avaliado_id" value="{{ $participante->user->id }}" />

                                                    <div>
                                                        <label class="block text-sm font-medium text-slate-700">Nota</label>
                                                        <div class="mt-2 flex items-center gap-2">
                                                            @foreach(range(1, 5) as $star)
                                                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                                                    <input type="radio" name="estrelas" value="{{ $star }}" class="hidden" @checked(old('estrelas') == $star) />
                                                                    <span>{{ $star }}★</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @error('estrelas')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="comentario" class="block text-sm font-medium text-slate-700">Comentário (opcional)</label>
                                                        <textarea id="comentario" name="comentario" rows="3" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm" placeholder="Conte o que você achou do companheiro de jogo.">{{ old('comentario') }}</textarea>
                                                    </div>

                                                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">Enviar avaliação</button>
                                                </form>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
