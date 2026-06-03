<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.index') }}" class="text-sm font-semibold text-emerald-700">Voltar para minhas peladas</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Rodadas - {{ $pelada->nome }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $pelada->jogos->count() }} rodada(s) cadastrada(s) desde a criacao da pelada.</p>
            </div>
            <a href="{{ route('organizador.peladas.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Voltar</a>
        </div>

        @php($editingJogoId = old('editing_jogo_id'))

        <form method="POST" action="{{ route('organizador.peladas.jogos.store', $pelada) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-2">
            @csrf
            <div class="rounded-md bg-emerald-50 px-4 py-3 md:col-span-3">
                <p class="text-sm font-semibold text-emerald-900">Proxima rodada: Rodada {{ $pelada->jogos->count() + 1 }}</p>
                <p class="mt-1 text-xs text-emerald-800">O titulo e gerado automaticamente para manter o historico da pelada em sequencia.</p>
            </div>

            <div>
                <label for="data_hora" class="text-sm font-medium text-slate-700">Data e horario</label>
                <input id="data_hora" type="datetime-local" name="data_hora" value="{{ $editingJogoId ? '' : old('data_hora') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required class="mt-1 w-full rounded-md border-slate-300">
                @unless($editingJogoId)
                    <x-input-error :messages="$errors->get('data_hora')" class="mt-2" />
                @endunless
            </div>

            <div>
                <label for="vagas_totais" class="text-sm font-medium text-slate-700">Vagas totais</label>
                <input id="vagas_totais" type="number" name="vagas_totais" min="2" value="{{ $editingJogoId ? ($pelada->vagas_totais ?: $pelada->capacidade) : old('vagas_totais', $pelada->vagas_totais ?: $pelada->capacidade) }}" class="mt-1 w-full rounded-md border-slate-300" placeholder="Vagas totais">
                @unless($editingJogoId)
                    <x-input-error :messages="$errors->get('vagas_totais')" class="mt-2" />
                @endunless
            </div>

            <div class="md:col-span-2">
                <label for="observacao" class="text-sm font-medium text-slate-700">Observacao</label>
                <textarea id="observacao" name="observacao" rows="2" class="mt-1 w-full rounded-md border-slate-300" placeholder="Recado opcional para esta rodada">{{ $editingJogoId ? '' : old('observacao') }}</textarea>
                @unless($editingJogoId)
                    <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
                @endunless
            </div>

            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white md:col-span-2">Criar rodada</button>
        </form>

        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @forelse($pelada->jogos as $jogo)
                <div class="p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold">{{ $jogo->titulo }}</p>
                            <p class="text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }} - {{ $jogo->participantes->where('status', 'confirmado')->count() }} confirmados</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $jogo->status }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm font-semibold">
                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-emerald-700 hover:bg-emerald-50" href="{{ route('organizador.jogos.show', $jogo) }}" title="Ver rodada" aria-label="Ver rodada">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button
                                type="button"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-emerald-700 hover:bg-emerald-50"
                                data-edit-toggle="editar-rodada-{{ $jogo->id }}"
                                aria-controls="editar-rodada-{{ $jogo->id }}"
                                aria-expanded="{{ (string) $editingJogoId === (string) $jogo->id ? 'true' : 'false' }}"
                                title="Editar rodada"
                                aria-label="Editar rodada"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div id="editar-rodada-{{ $jogo->id }}" class="{{ (string) $editingJogoId === (string) $jogo->id ? '' : 'hidden' }} mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <form method="POST" action="{{ route('organizador.jogos.update', $jogo) }}" class="mt-4 grid gap-3 md:grid-cols-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="editing_jogo_id" value="{{ $jogo->id }}">

                            <div class="rounded-md bg-white px-4 py-3 md:col-span-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $jogo->titulo }}</p>
                                <p class="mt-1 text-xs text-slate-500">O numero da rodada e fixo para preservar o historico da pelada.</p>
                            </div>

                            <div>
                                <label for="data_hora_{{ $jogo->id }}" class="text-sm font-medium text-slate-700">Data e horario</label>
                                <input id="data_hora_{{ $jogo->id }}" type="datetime-local" name="data_hora" value="{{ (string) $editingJogoId === (string) $jogo->id ? old('data_hora', $jogo->data_hora->format('Y-m-d\TH:i')) : $jogo->data_hora->format('Y-m-d\TH:i') }}" required class="mt-1 w-full rounded-md border-slate-300">
                                @if((string) $editingJogoId === (string) $jogo->id)
                                    <x-input-error :messages="$errors->get('data_hora')" class="mt-2" />
                                @endif
                            </div>

                            <div>
                                <label for="vagas_totais_{{ $jogo->id }}" class="text-sm font-medium text-slate-700">Vagas totais</label>
                                <input id="vagas_totais_{{ $jogo->id }}" type="number" name="vagas_totais" min="2" value="{{ (string) $editingJogoId === (string) $jogo->id ? old('vagas_totais', $jogo->vagas_totais ?: $jogo->capacidade) : ($jogo->vagas_totais ?: $jogo->capacidade) }}" class="mt-1 w-full rounded-md border-slate-300" placeholder="Vagas totais">
                                @if((string) $editingJogoId === (string) $jogo->id)
                                    <x-input-error :messages="$errors->get('vagas_totais')" class="mt-2" />
                                @endif
                            </div>

                            <div>
                                <label for="status_{{ $jogo->id }}" class="text-sm font-medium text-slate-700">Status</label>
                                <select id="status_{{ $jogo->id }}" name="status" required class="mt-1 w-full rounded-md border-slate-300">
                                    @foreach(['aberto', 'fechado', 'finalizado', 'realizado', 'cancelado'] as $status)
                                        <option value="{{ $status }}" @selected(((string) $editingJogoId === (string) $jogo->id ? old('status', $jogo->status) : $jogo->status) === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @if((string) $editingJogoId === (string) $jogo->id)
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                @endif
                            </div>

                            <div class="md:col-span-3">
                                <label for="observacao_{{ $jogo->id }}" class="text-sm font-medium text-slate-700">Observacao</label>
                                <textarea id="observacao_{{ $jogo->id }}" name="observacao" rows="2" class="mt-1 w-full rounded-md border-slate-300" placeholder="Recado opcional para esta rodada">{{ (string) $editingJogoId === (string) $jogo->id ? old('observacao', $jogo->observacao) : $jogo->observacao }}</textarea>
                                @if((string) $editingJogoId === (string) $jogo->id)
                                    <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
                                @endif
                            </div>

                            <div class="flex justify-end md:col-span-3">
                                <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar alteracoes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <p class="p-5 text-sm text-slate-600">Nenhuma rodada criada ainda.</p>
            @endforelse
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.editToggle);

                if (!target) {
                    return;
                }

                const isHidden = target.classList.toggle('hidden');
                button.setAttribute('aria-expanded', String(!isHidden));
            });
        });
    </script>
</x-app-layout>
