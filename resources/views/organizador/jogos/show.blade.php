<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')
        @php($rodadaBloqueada = $jogo->bloqueadoParaEdicao())

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('organizador.peladas.jogos.index', $jogo->pelada) }}" class="text-sm font-semibold text-emerald-700">Voltar para rodadas</a>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $jogo->titulo }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }} - {{ $confirmados->count() }} confirmado(s)</p>
            </div>
            <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">{{ $jogo->status }}</span>
        </div>

        @if($rodadaBloqueada)
            <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">
                Esta rodada foi finalizada. As opcoes de edicao, presenca, sorteio, gols e cartoes estao indisponiveis.
            </div>
        @endif

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="text-lg font-bold text-slate-950">Confirmar membro da pelada</h2>
                <form method="POST" action="{{ route('organizador.jogos.participantes.confirmar-membro', $jogo) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                    @csrf
                    <select name="pelada_membro_id" class="w-full rounded-md border-slate-300" @disabled($membrosDisponiveis->isEmpty() || $rodadaBloqueada)>
                        <option value="">Selecione mensalista ou diarista</option>
                        @foreach($membrosDisponiveis as $membro)
                            <option value="{{ $membro->id }}">{{ $membro->nomeExibicao() }} - {{ ucfirst($membro->tipo) }}</option>
                        @endforeach
                    </select>
                    <button class="shrink-0 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" @disabled($membrosDisponiveis->isEmpty() || $rodadaBloqueada)>Confirmar</button>
                </form>
                <x-input-error :messages="$errors->get('pelada_membro_id')" class="mt-2" />
            </section>

            <section class="rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                <h2 class="text-lg font-bold text-slate-950">Adicionar avulso</h2>
                <form method="POST" action="{{ route('organizador.jogos.sorteios.avulsos', $jogo) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                    @csrf
                    <input name="nome" required maxlength="120" placeholder="Nome do jogador avulso" class="w-full rounded-md border-slate-300" @disabled($rodadaBloqueada)>
                    <button class="shrink-0 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" @disabled($rodadaBloqueada)>Adicionar</button>
                </form>
            </section>
        </div>

        @php($listaInicialmenteOculta = (bool) $ultimoSorteio)

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Lista de chegada e sorteio</h2>
                    <p class="mt-1 text-sm text-slate-600">Marque quem entra no sorteio e ajuste a ordem de chegada.</p>
                </div>
                <button
                    type="button"
                    id="toggle-lista-sorteio"
                    class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    aria-controls="lista-sorteio-panel"
                    aria-expanded="{{ $listaInicialmenteOculta ? 'false' : 'true' }}"
                >
                    {{ $listaInicialmenteOculta ? 'Mostrar lista' : 'Ocultar lista' }}
                </button>
            </div>
        </section>

        <div id="lista-sorteio-panel" class="{{ $listaInicialmenteOculta ? 'hidden' : '' }}">
        <form id="form-presencas" method="POST" action="{{ route('organizador.jogos.sorteios.presencas', $jogo) }}" class="mt-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-950">Chegada dos jogadores</h3>
                    <p class="mt-1 text-sm text-slate-600">Use os botões para ordenar no mobile ou arraste no desktop.</p>
                </div>
                <button class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" @disabled($rodadaBloqueada)>Salvar lista</button>
            </div>

            @if($confirmados->isEmpty())
                <p class="mt-4 text-sm text-slate-500">Nenhum jogador confirmado ainda.</p>
            @else
                <ul id="lista-presenca" class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200">
                    @foreach($confirmados as $index => $participante)
                        <li class="flex cursor-grab items-center gap-3 bg-white p-3 active:cursor-grabbing" data-participante-id="{{ $participante->id }}" draggable="true">
                            <div class="flex shrink-0 items-center gap-1">
                                <button type="button" class="order-up inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-50" title="Subir na ordem" aria-label="Subir na ordem" @disabled($rodadaBloqueada)>
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                                </button>
                                <button type="button" class="order-down inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-50" title="Descer na ordem" aria-label="Descer na ordem" @disabled($rodadaBloqueada)>
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                            </div>
                            <span class="ordem-label text-xs font-bold text-slate-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <label class="flex flex-1 items-center gap-3">
                                <input type="checkbox" name="presentes[]" value="{{ $participante->id }}" class="presente-check rounded border-slate-300 text-emerald-600" @checked($participante->presente_local) @disabled($rodadaBloqueada)>
                                @if($participante->user)
                                    <x-user-avatar :user="$participante->user" size="xs" />
                                @else
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">A</span>
                                @endif
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-slate-900">{{ $participante->nomeExibicao() }}</span>
                                    <span class="block text-xs text-slate-500">{{ $participante->isAvulso() ? 'Avulso' : ucfirst($participante->tipo) }}</span>
                                </span>
                            </label>
                            <div class="flex items-center gap-1">
                                <button form="remover-participante-{{ $participante->id }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-red-200 text-red-700 hover:bg-red-50 disabled:opacity-50" title="Remover participante" aria-label="Remover participante" @disabled($rodadaBloqueada)>
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div id="ordem-hidden"></div>
            @endif
        </form>

        @foreach($confirmados as $participante)
            <form id="remover-participante-{{ $participante->id }}" method="POST" action="{{ route('organizador.jogos.participantes.remover', [$jogo, $participante]) }}">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <form id="form-sorteio" method="POST" action="{{ route('organizador.jogos.sorteios.sortear', $jogo) }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            <h2 class="text-lg font-bold text-slate-950">Sortear times</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <label class="text-sm font-medium text-slate-700">
                    Numero de times
                    <input id="quantidade_times" type="number" name="quantidade_times" min="1" max="20" value="{{ old('quantidade_times', 2) }}" required class="mt-1 w-full rounded-md border-slate-300" @disabled($rodadaBloqueada)>
                </label>
                <label class="text-sm font-medium text-slate-700">
                    Jogadores por time
                    <input id="jogadores_por_time" type="number" name="jogadores_por_time" min="1" max="30" value="{{ old('jogadores_por_time', 5) }}" required class="mt-1 w-full rounded-md border-slate-300" @disabled($rodadaBloqueada)>
                </label>
                <fieldset class="space-y-2">
                    <legend class="text-sm font-medium text-slate-700">Ordem</legend>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="modo_ordenacao" value="manual" class="text-emerald-600" checked @disabled($rodadaBloqueada)> Chegada</label>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="modo_ordenacao" value="prioridade" class="text-emerald-600" @disabled($rodadaBloqueada)> Mensalistas primeiro</label>
                </fieldset>
            </div>
            <div id="resumo-sorteio" class="mt-4 rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-700"></div>
            <button id="btn-sortear" class="mt-4 rounded-md bg-emerald-600 px-4 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50" @disabled($confirmados->isEmpty() || $rodadaBloqueada)>Sortear times</button>
        </form>
        </div>

        <div id="sorteio-loading" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 px-4 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-lg border border-emerald-300/30 bg-white p-6 text-center shadow-2xl">
                <img src="{{ asset('images/logo-vai-ter-pelada.png') }}" alt="Vai Ter Pelada" class="mx-auto h-24 w-auto animate-pulse">
                <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full w-1/2 rounded-full bg-emerald-500" style="animation: loadingBar 1.2s ease-in-out infinite;"></div>
                </div>
                <p class="mt-4 text-base font-bold text-slate-950">Sorteando os times</p>
                <p class="mt-1 text-sm text-slate-500">Organizando a pelada pela regra escolhida.</p>
            </div>
        </div>

        <style>
            @keyframes loadingBar {
                0% { transform: translateX(-100%); }
                50% { transform: translateX(50%); }
                100% { transform: translateX(200%); }
            }
        </style>

        @if($ultimoSorteio)
            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Times do ultimo sorteio</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $ultimoSorteio->created_at->format('d/m/Y H:i') }} - {{ $ultimoSorteio->jogadores_por_time }} por time</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('organizador.jogos.sorteios.times.update', [$jogo, $ultimoSorteio]) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($ultimoSorteio->times as $time)
                            @php($selecionados = $time->jogadores->pluck('pelada_jogo_participante_id')->filter()->all())
                            <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                                <h3 class="font-bold text-emerald-700">{{ $time->nome }}</h3>
                                <select name="times[{{ $time->id }}][]" multiple size="8" class="mt-3 w-full rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)>
                                    @foreach($confirmados as $participante)
                                        <option value="{{ $participante->id }}" @selected(in_array($participante->id, $selecionados, true))>{{ $participante->nomeExibicao() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <button class="mt-4 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" @disabled($rodadaBloqueada)>Salvar times</button>
                </form>
            </section>
        @endif

        @if($confirmados->isNotEmpty())
            <form method="POST" action="{{ route('organizador.jogos.estatisticas.store', $jogo) }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
                @csrf
                <h2 class="text-lg font-bold text-slate-950">Gols e cartoes da rodada</h2>
                <p class="mt-1 text-sm text-slate-600">Registre gols e cartoes aplicados pelo organizador. As avaliacoes ficam por conta dos jogadores presentes.</p>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                            <tr>
                                <th class="p-3">Jogador</th>
                                <th class="p-3">Gols</th>
                                <th class="p-3">Amarelos</th>
                                <th class="p-3">Vermelhos</th>
                                <th class="p-3">Azuis</th>
                                <th class="p-3">Observacao</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($confirmados as $participante)
                                @php($estatistica = $participante->estatistica)
                                <tr>
                                    <td class="p-3 font-semibold text-slate-900">{{ $participante->nomeExibicao() }}</td>
                                    <td class="p-3"><input type="number" name="participantes[{{ $participante->id }}][gols]" min="0" max="99" value="{{ old('participantes.'.$participante->id.'.gols', $estatistica?->gols ?? 0) }}" class="w-20 rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)></td>
                                    <td class="p-3"><input type="number" name="participantes[{{ $participante->id }}][cartoes_amarelos]" min="0" max="9" value="{{ old('participantes.'.$participante->id.'.cartoes_amarelos', $estatistica?->cartoes_amarelos ?? 0) }}" class="w-20 rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)></td>
                                    <td class="p-3"><input type="number" name="participantes[{{ $participante->id }}][cartoes_vermelhos]" min="0" max="9" value="{{ old('participantes.'.$participante->id.'.cartoes_vermelhos', $estatistica?->cartoes_vermelhos ?? 0) }}" class="w-20 rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)></td>
                                    <td class="p-3"><input type="number" name="participantes[{{ $participante->id }}][cartoes_azuis]" min="0" max="9" value="{{ old('participantes.'.$participante->id.'.cartoes_azuis', $estatistica?->cartoes_azuis ?? 0) }}" class="w-20 rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)></td>
                                    <td class="p-3"><input type="text" name="participantes[{{ $participante->id }}][observacao]" maxlength="500" value="{{ old('participantes.'.$participante->id.'.observacao', $estatistica?->observacao) }}" class="min-w-52 rounded-md border-slate-300 text-sm" @disabled($rodadaBloqueada)></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button class="mt-4 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" @disabled($rodadaBloqueada)>Salvar gols e cartoes</button>
            </form>
        @endif
    </div>

    <script>
        (function () {
            const lista = document.getElementById('lista-presenca');
            const ordemHidden = document.getElementById('ordem-hidden');
            const formSorteio = document.getElementById('form-sorteio');
            const formPresencas = document.getElementById('form-presencas');
            const inputPorTime = document.getElementById('jogadores_por_time');
            const inputTimes = document.getElementById('quantidade_times');
            const resumo = document.getElementById('resumo-sorteio');
            const btnSortear = document.getElementById('btn-sortear');
            const loading = document.getElementById('sorteio-loading');
            const toggleLista = document.getElementById('toggle-lista-sorteio');
            const listaPanel = document.getElementById('lista-sorteio-panel');

            if (!lista) return;

            let dragged = null;

            function atualizarOrdemHidden() {
                if (!ordemHidden) return;
                ordemHidden.innerHTML = '';
                lista.querySelectorAll('li[data-participante-id]').forEach((li, index) => {
                    const label = li.querySelector('.ordem-label');
                    if (label) {
                        label.textContent = String(index + 1).padStart(2, '0');
                    }
                    if (!li.querySelector('.presente-check')?.checked) return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ordem[]';
                    input.value = li.dataset.participanteId;
                    ordemHidden.appendChild(input);
                });
            }

            function copiarListaParaSorteio() {
                formSorteio.querySelectorAll('input[data-copied]').forEach((el) => el.remove());
                lista.querySelectorAll('li[data-participante-id]').forEach((li) => {
                    if (!li.querySelector('.presente-check')?.checked) return;
                    ['presentes[]', 'ordem[]'].forEach((name) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = li.dataset.participanteId;
                        input.dataset.copied = '1';
                        formSorteio.appendChild(input);
                    });
                });
            }

            function atualizarResumo() {
                const porTime = Math.max(1, Number(inputPorTime?.value) || 5);
                const times = Math.max(1, Number(inputTimes?.value) || 2);
                const presentes = lista.querySelectorAll('.presente-check:checked').length;
                const usados = Math.min(presentes, porTime * times);
                const sobras = Math.max(0, presentes - usados);
                const podeSortear = presentes >= porTime;
                const modo = formSorteio.querySelector('input[name="modo_ordenacao"]:checked')?.value || 'manual';
                const regra = modo === 'prioridade'
                    ? 'mensalistas confirmados primeiro, depois diaristas e avulsos'
                    : 'ordem de chegada marcada na lista';

                resumo.innerHTML = `Regra: <strong>${regra}</strong>. Presentes marcados: <strong>${presentes}</strong>. Sorteio: <strong>${times}</strong> time(s) com <strong>${porTime}</strong> jogador(es). Times A e B usam os <strong>${Math.min(usados, porTime * 2)}</strong> primeiro(s) pela regra selecionada. ${sobras ? `Fora deste sorteio: <strong>${sobras}</strong>.` : ''}`;
                btnSortear.disabled = !podeSortear;
            }

            lista.querySelectorAll('li[data-participante-id]').forEach((li) => {
                li.addEventListener('dragstart', () => {
                    dragged = li;
                    li.classList.add('opacity-50');
                });
                li.addEventListener('dragend', () => {
                    li.classList.remove('opacity-50');
                    dragged = null;
                    atualizarOrdemHidden();
                    atualizarResumo();
                });
                li.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    if (!dragged || dragged === li) return;
                    const rect = li.getBoundingClientRect();
                    lista.insertBefore(dragged, (event.clientY - rect.top) / rect.height > 0.5 ? li.nextSibling : li);
                });
            });

            lista.addEventListener('click', (event) => {
                const up = event.target.closest('.order-up');
                const down = event.target.closest('.order-down');

                if (!up && !down) return;

                const item = event.target.closest('li[data-participante-id]');
                if (!item) return;

                if (up && item.previousElementSibling) {
                    lista.insertBefore(item, item.previousElementSibling);
                }

                if (down && item.nextElementSibling) {
                    lista.insertBefore(item.nextElementSibling, item);
                }

                atualizarOrdemHidden();
                atualizarResumo();
            });

            lista.addEventListener('change', () => {
                atualizarOrdemHidden();
                atualizarResumo();
            });
            inputPorTime?.addEventListener('input', atualizarResumo);
            inputTimes?.addEventListener('input', atualizarResumo);
            formSorteio.querySelectorAll('input[name="modo_ordenacao"]').forEach((input) => {
                input.addEventListener('change', atualizarResumo);
            });
            formPresencas?.addEventListener('submit', atualizarOrdemHidden);
            formSorteio?.addEventListener('submit', (event) => {
                copiarListaParaSorteio();
                if (btnSortear.disabled) {
                    event.preventDefault();
                    return;
                }

                btnSortear.disabled = true;
                btnSortear.textContent = 'Sorteando...';
                loading?.classList.remove('hidden');
                loading?.classList.add('flex');
            });

            toggleLista?.addEventListener('click', () => {
                if (!listaPanel) return;

                const isHidden = listaPanel.classList.toggle('hidden');
                toggleLista.textContent = isHidden ? 'Mostrar lista' : 'Ocultar lista';
                toggleLista.setAttribute('aria-expanded', String(!isHidden));
            });

            atualizarOrdemHidden();
            atualizarResumo();
        })();
    </script>
</x-app-layout>
