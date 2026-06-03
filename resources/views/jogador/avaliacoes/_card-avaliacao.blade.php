<div class="
    rounded-lg border p-3 transition-all
    {{ $needsReview ? 'border-slate-200 bg-white' : 'border-slate-300 bg-slate-100' }}
">
    @if(!$needsReview)
        <div class="mb-3 rounded-md bg-slate-200 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700">
            ✓ Jogador já avaliado nesta rodada
        </div>
    @endif

    <details>
        <summary class="flex cursor-pointer list-none flex-col gap-3 marker:hidden sm:flex-row sm:items-center sm:justify-between">
            <span class="flex items-center gap-3">
                <x-user-avatar :user="$participante->user" size="sm" />
                <span>
                    <span class="block font-semibold {{ $needsReview ? 'text-slate-950' : 'text-slate-500' }}">
                        {{ $participante->user->name }}
                    </span>
                    <a href="{{ route('peladeiros.show', $participante->user->publicProfile()) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                        Ver perfil publico
                    </a>
                </span>
            </span>

            <span class="
                inline-flex w-fit items-center justify-center rounded-md px-4 py-2 text-sm font-semibold
                {{ $needsReview ? 'bg-emerald-600 text-white' : 'bg-slate-500' }}
            ">
                {{ $needsReview ? 'Avaliar' : '✓ editar' }}
            </span>
        </summary>

        <form method="POST" action="{{ route('jogador.avaliacoes.store') }}" class="mt-4 space-y-5 border-t border-slate-200 pt-4">
            @csrf

            <input type="hidden" name="pelada_jogo_id" value="{{ $jogo->id }}">
            <input type="hidden" name="avaliado_id" value="{{ $participante->user->id }}">

            <div>
                <p class="text-sm font-semibold text-slate-900">Destaque da rodada</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($voteTypes as $type => $voteType)
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="vote_type"
                                value="{{ $type }}"
                                class="peer hidden"
                                @checked($selectedVote === $type)
                            >

                            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1.5 text-xs font-bold transition
                                {{ $selectedVote === $type
                                    ? 'border-emerald-700 bg-emerald-600 text-white ring-2 ring-emerald-300'
                                    : 'border-emerald-200 bg-emerald-100 text-emerald-900 hover:bg-emerald-200'
                                }}
                                peer-checked:border-emerald-700 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:ring-2 peer-checked:ring-emerald-300"
                            >
                                <span class="hidden peer-checked:inline">✓</span>
                                {{ $voteType['label'] }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <x-input-error :messages="$oldForThisPlayer ? $errors->get('vote_type') : []" class="mt-2" />
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700">Nota</label>
                <div class="mt-2 grid grid-cols-5 gap-2">
                    @foreach(range(1, 5) as $star)
                        <label class="group cursor-pointer">
                            <input
                                type="radio"
                                name="estrelas"
                                value="{{ $star }}"
                                class="peer sr-only"
                                @checked((string) $selectedStars === (string) $star)
                            >

                            <span class="block rounded-md border border-slate-300 bg-white px-2 py-2 text-center text-sm font-bold text-slate-700 transition group-hover:border-emerald-300 group-hover:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-600 peer-checked:text-white peer-focus:ring-2 peer-focus:ring-emerald-300">
                                {{ $star }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <x-input-error :messages="$oldForThisPlayer ? $errors->get('estrelas') : []" class="mt-2" />
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700">Comentario opcional</label>
                <textarea
                    name="comentario"
                    rows="3"
                    class="mt-1 w-full rounded-md border-slate-300 text-sm text-slate-900 placeholder:text-slate-400"
                    placeholder="Ex: jogou limpo, ajudou o time, chegou no horario..."
                >{{ old('comentario', $commentValue) }}</textarea>

                <x-input-error :messages="$oldForThisPlayer ? $errors->get('comentario') : []" class="mt-2" />
            </div>

            <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                {{ $needsReview ? 'Enviar avaliacao' : 'Salvar alteracoes' }}
            </button>
        </form>
    </details>
</div>