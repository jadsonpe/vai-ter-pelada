<x-app-layout>
    <div class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @include('shared.status')

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Jogador</p>
                    <h1 class="mt-1 text-3xl font-bold text-slate-950">Avaliacoes</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">Avalie quem jogou com voce e acompanhe sua reputacao nas partidas.</p>
                </div>
                <a href="{{ route('jogador.peladas.minhas') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Minhas peladas</a>
            </div>

            <section class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Media recebida</p>
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
                    <p class="mt-1 text-xs text-emerald-800">abertas por ate 3 dias</p>
                </div>
            </section>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="text-lg font-bold text-slate-950">Avaliacoes pendentes</h2>
                        <p class="mt-1 text-sm text-slate-600">Aparecem aqui partidas realizadas em que sua presenca foi marcada no local.</p>
                    </div>

                    @if($pendingGames->isEmpty())
                        <div class="p-8 text-center">
                            <p class="font-semibold text-slate-900">Nenhuma avaliacao pendente</p>
                            <p class="mt-1 text-sm text-slate-500">Depois de uma rodada com presenca marcada, voce podera avaliar os jogadores presentes.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($pendingGames as $item)
                                @php($jogo = $item->jogo)
                                <article class="p-5">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $jogo->pelada->esporte->nome }}</p>
                                            <h3 class="mt-1 font-bold text-slate-950">{{ $jogo->pelada->nome }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">{{ $jogo->titulo }} - {{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $item->avaliados->count() }} pendente(s)</span>
                                    </div>

                                    <div class="mt-5 rounded-lg border border-emerald-100 bg-emerald-50/70 p-4">
                                        <div>
                                            <h4 class="font-bold text-slate-950">Votos e avaliacoes da rodada</h4>
                                            <p class="text-sm text-slate-600">Vote nos destaques e avalie os jogadores presentes no mesmo card.</p>
                                        </div>

                                        <div class="mt-4 grid gap-3">
                                            @foreach($item->votaveis as $participante)
                                                @php($needsReview = $item->avaliados->contains('user_id', $participante->user_id))

                                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <x-user-avatar :user="$participante->user" size="sm" />
                                                            <div>
                                                                <p class="font-semibold text-slate-950">{{ $participante->user->name }}</p>
                                                                <a href="{{ route('peladeiros.show', $participante->user->publicProfile()) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Ver perfil publico</a>
                                                            </div>
                                                        </div>

                                                        <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $needsReview ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                                            {{ $needsReview ? 'Avaliacao pendente' : 'Ja avaliado' }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        @foreach($voteTypes as $type => $voteType)
                                                            @php($alreadyVoted = in_array($type, $participante->votos_feitos ?? [], true))
                                                            <form method="POST" action="{{ route('jogador.votos.store') }}">
                                                                @csrf
                                                                <input type="hidden" name="pelada_jogo_id" value="{{ $jogo->id }}">
                                                                <input type="hidden" name="voted_user_id" value="{{ $participante->user_id }}">
                                                                <input type="hidden" name="type" value="{{ $type }}">
                                                                <button
                                                                    @disabled($alreadyVoted)
                                                                    class="rounded-full border px-3 py-1.5 text-xs font-bold {{ $alreadyVoted ? 'border-slate-200 bg-slate-100 text-slate-400' : 'border-emerald-200 bg-emerald-100 text-emerald-900 hover:bg-emerald-200' }}"
                                                                >
                                                                    {{ $alreadyVoted ? 'Votado: ' : '' }}{{ $voteType['label'] }}
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>

                                                    @if($needsReview)
                                                        <details class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 marker:hidden">
                                                                <span class="text-sm font-bold text-slate-900">Nota do jogador</span>
                                                                <span class="rounded-md bg-slate-950 px-3 py-1.5 text-xs font-semibold text-white">Abrir</span>
                                                            </summary>

                                                            <form method="POST" action="{{ route('jogador.avaliacoes.store') }}" class="mt-4 space-y-4 border-t border-slate-200 pt-4">
                                                                @csrf
                                                                <input type="hidden" name="pelada_jogo_id" value="{{ $jogo->id }}">
                                                                <input type="hidden" name="avaliado_id" value="{{ $participante->user->id }}">

                                                                <div>
                                                                    <label class="text-sm font-medium text-slate-700">Nota</label>
                                                                    <div class="mt-2 grid grid-cols-5 gap-2">
                                                                        @foreach(range(1, 5) as $star)
                                                                            <label class="group cursor-pointer">
                                                                                <input type="radio" name="estrelas" value="{{ $star }}" class="peer sr-only" @checked(old('estrelas') == $star)>
                                                                                <span class="block rounded-md border border-slate-300 bg-white px-2 py-2 text-center text-sm font-bold text-slate-700 transition group-hover:border-emerald-300 group-hover:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-600 peer-checked:text-white peer-focus:ring-2 peer-focus:ring-emerald-300">
                                                                                    {{ $star }}
                                                                                </span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                    <x-input-error :messages="$errors->get('estrelas')" class="mt-2" />
                                                                </div>

                                                                <div>
                                                                    <label class="text-sm font-medium text-slate-700">Comentario opcional</label>
                                                                    <textarea name="comentario" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm text-slate-900 placeholder:text-slate-400" placeholder="Ex: jogou limpo, ajudou o time, chegou no horario...">{{ old('comentario') }}</textarea>
                                                                    <x-input-error :messages="$errors->get('comentario')" class="mt-2" />
                                                                </div>

                                                                <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Enviar avaliacao</button>
                                                            </form>
                                                        </details>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <aside class="space-y-6">
                    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-950">Como funciona</h2>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <p>1. O organizador marca a presenca no local.</p>
                            <p>2. A avaliacao fica aberta por 3 dias apos a rodada.</p>
                            <p>3. Voce avalia apenas jogadores presentes e cadastrados.</p>
                            <p>4. Notas recebidas impactam media, pontos, badges e ranking.</p>
                        </div>
                    </section>

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
                                    @if($avaliacao->comentario)
                                        <p class="mt-2 text-sm text-slate-600">{{ $avaliacao->comentario }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Voce ainda nao recebeu avaliacoes.</p>
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
                                <p class="text-sm text-slate-500">Voce ainda nao avaliou jogadores.</p>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
