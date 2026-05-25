<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div>
            <a href="{{ route('organizador.peladas.jogos.index', $jogo->pelada) }}" class="text-sm font-semibold text-emerald-700">Voltar para rodadas</a>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Sorteio presencial — {{ $jogo->titulo }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
        </div>

        {{-- Inclusão rápida --}}
        <section class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-5">
            <h2 class="font-semibold text-slate-900">Adicionar jogador(avulso)</h2>
            <p class="mt-1 text-sm text-slate-600">Adicione quem chegou e não estava na lista de confirmados do app.</p>
            <form method="POST" action="{{ route('organizador.jogos.sorteios.avulsos', $jogo) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                @csrf
                <input name="nome" required maxlength="120" placeholder="Nome do jogador avulso" class="w-full rounded-md border-slate-300">
                <button class="shrink-0 rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Adicionar presente</button>
            </form>
        </section>

        {{-- Presença no local --}}
        <form id="form-presencas" method="POST" action="{{ route('organizador.jogos.sorteios.presencas', $jogo) }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Presença no local</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Lista de quem confirmou pelo app. Marque quem <strong>realmente chegou</strong> e arraste para definir a ordem de chegada.
                    </p>
                </div>
                <button type="submit" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Salvar presenças
                </button>
            </div>

            @if($confirmados->isEmpty())
                <p class="mt-4 text-sm text-slate-500">Nenhum jogador confirmou presença nesta rodada pelo aplicativo.</p>
            @else
                <ul id="lista-presenca" class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200">
                    @foreach($confirmados as $participante)
                        <li
                            class="flex cursor-grab items-center gap-3 bg-white p-3 active:cursor-grabbing"
                            data-participante-id="{{ $participante->id }}"
                            draggable="true"
                        >
                            <span class="handle text-slate-400" title="Arrastar">☰</span>
                            <label class="flex flex-1 items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="presentes[]"
                                    value="{{ $participante->id }}"
                                    class="presente-check rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                    @checked($participante->presente_local)
                                >
                                @if($participante->user)
                                    <x-user-avatar :user="$participante->user" size="xs" />
                                @else
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">A</span>
                                @endif
                                <span class="min-w-0 flex-1">
                                    <span class="font-medium text-slate-900">{{ $participante->nomeExibicao() }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">
                                        {{ $participante->isAvulso() ? 'Avulso' : ucfirst($participante->tipo) }}
                                        @if($participante->user?->email && ! $participante->isAvulso())
                                            · {{ $participante->user->email }}
                                        @endif
                                    </span>
                                </span>
                            </label>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Confirmado app</span>
                        </li>
                    @endforeach
                </ul>
                <div id="ordem-hidden"></div>
            @endif
        </form>

        {{-- Sorteio --}}
        <form id="form-sorteio" method="POST" action="{{ route('organizador.jogos.sorteios.sortear', $jogo) }}" class="mt-6 space-y-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            <h2 class="font-semibold text-slate-900">Montar times (2 em quadra)</h2>
            <p class="mt-1 text-sm text-slate-600">
                Os primeiros <strong id="vagas-iniciais-label">10</strong> presentes (na ordem definida) entram no 1º jogo entre <strong>Time A</strong> e <strong>Time B</strong>, sorteados aleatoriamente.
                O restante forma os próximos times (C, D, E…), com o último incompleto se faltar jogador.
            </p>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium text-slate-700">
                    Jogadores por time (hoje)
                    <input
                        type="number"
                        name="jogadores_por_time"
                        id="jogadores_por_time"
                        min="1"
                        max="30"
                        value="{{ old('jogadores_por_time', 5) }}"
                        required
                        class="mt-1 w-full rounded-md border-slate-300"
                    >
                </label>
                <fieldset class="space-y-2 sm:col-span-2">
                    <legend class="text-sm font-medium text-slate-700">Ordem para o sorteio</legend>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="modo_ordenacao" value="manual" class="text-emerald-600" checked>
                        Usar a ordem da lista (arraste para organizar a chegada)
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="modo_ordenacao" value="prioridade" class="text-emerald-600">
                        Prioridade: mensalistas presentes primeiro, depois diaristas/avulsos
                    </label>
                </fieldset>
            </div>

            <div id="resumo-sorteio" class="rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-700"></div>

            <button type="submit" id="btn-sortear" class="rounded-md bg-emerald-600 px-4 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50" @disabled($confirmados->isEmpty())>
                Sortear times
            </button>
            <p id="aviso-minimo-time" class="hidden text-sm text-amber-700"></p>
        </form>

        @if($sorteios->isNotEmpty())
            <section class="mt-10">
                <h2 class="text-xl font-bold text-slate-900">Sorteios realizados</h2>
                <div class="mt-4 space-y-5">
                    @foreach($sorteios as $sorteio)
                        <article class="rounded-lg border border-slate-200 bg-white p-5">
                            <p class="text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $sorteio->created_at->format('d/m/Y H:i') }}</span>
                                · {{ $sorteio->jogadores_por_time }} por time
                                · {{ $sorteio->usar_ordem_manual ? 'Ordem manual' : 'Prioridade mensalista/diarista' }}
                            </p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($sorteio->times as $time)
                                    <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                                        <h3 class="font-semibold text-emerald-700">
                                            {{ $time->nome }}
                                            <span class="text-xs font-normal text-slate-500">({{ $time->jogadores->count() }}/{{ $sorteio->jogadores_por_time }})</span>
                                        </h3>
                                        <ul class="mt-2 space-y-1">
                                            @foreach($time->jogadores->sortBy('ordem') as $jogador)
                                                @php
                                                    $nome = $jogador->participante?->nomeExibicao()
                                                        ?: $jogador->user?->name
                                                        ?: 'Jogador';
                                                @endphp
                                                <li class="flex items-center gap-2 text-sm text-slate-700">
                                                    @if($jogador->user)
                                                        <x-user-avatar :user="$jogador->user" size="xs" />
                                                    @else
                                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">A</span>
                                                    @endif
                                                    <span>{{ $jogador->ordem }}. {{ $nome }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        (function () {
            const lista = document.getElementById('lista-presenca');
            const ordemHidden = document.getElementById('ordem-hidden');
            const formSorteio = document.getElementById('form-sorteio');
            const formPresencas = document.getElementById('form-presencas');
            const inputPorTime = document.getElementById('jogadores_por_time');
            const resumo = document.getElementById('resumo-sorteio');
            const vagasLabel = document.getElementById('vagas-iniciais-label');
            const btnSortear = document.getElementById('btn-sortear');
            const avisoMinimo = document.getElementById('aviso-minimo-time');

            if (!lista) return;

            let dragged = null;

            function atualizarOrdemHidden() {
                if (!ordemHidden) return;
                ordemHidden.innerHTML = '';
                lista.querySelectorAll('li[data-participante-id]').forEach((li) => {
                    const id = li.dataset.participanteId;
                    const presente = li.querySelector('.presente-check')?.checked;
                    if (!presente) return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ordem[]';
                    input.value = id;
                    input.className = 'ordem-input';
                    ordemHidden.appendChild(input);
                });
            }

            function copiarPresencasParaSorteio() {
                if (!formSorteio) return;
                formSorteio.querySelectorAll('input[data-copied-presente]').forEach((el) => el.remove());
                formSorteio.querySelectorAll('input[data-copied-ordem]').forEach((el) => el.remove());

                lista.querySelectorAll('li[data-participante-id]').forEach((li) => {
                    const id = li.dataset.participanteId;
                    const presente = li.querySelector('.presente-check')?.checked;
                    if (!presente) return;

                    const p = document.createElement('input');
                    p.type = 'hidden';
                    p.name = 'presentes[]';
                    p.value = id;
                    p.dataset.copiedPresente = '1';
                    formSorteio.appendChild(p);
                });

                lista.querySelectorAll('li[data-participante-id]').forEach((li) => {
                    const id = li.dataset.participanteId;
                    const presente = li.querySelector('.presente-check')?.checked;
                    if (!presente) return;
                    const o = document.createElement('input');
                    o.type = 'hidden';
                    o.name = 'ordem[]';
                    o.value = id;
                    o.dataset.copiedOrdem = '1';
                    formSorteio.appendChild(o);
                });
            }

            function atualizarResumo() {
                const porTime = Math.max(1, Number(inputPorTime?.value) || 5);
                const vagasIniciais = porTime * 2;
                const presentes = lista.querySelectorAll('.presente-check:checked').length;
                const proximos = Math.max(0, presentes - vagasIniciais);
                const timesExtras = proximos > 0 ? Math.ceil(proximos / porTime) : 0;
                const podeSortear = presentes >= porTime;

                if (vagasLabel) vagasLabel.textContent = String(vagasIniciais);
                if (resumo) {
                    resumo.innerHTML = `
                        Mínimo para sortear: <strong>${porTime}</strong> presente(s) (1 time completo).
                        Atualmente: <strong>${presentes}</strong> presente(s) no local.
                        ${podeSortear ? '' : '<span class="text-amber-800"> Ainda não é possível sortear.</span>'}
                        <br>
                        1º jogo: <strong>${Math.min(presentes, vagasIniciais)}</strong> jogadores entre Time A e B (${vagasIniciais} vagas).
                        ${proximos > 0 ? `Fila: <strong>${proximos}</strong> jogador(es) em até <strong>${timesExtras}</strong> time(s) seguinte(s).` : ''}
                    `;
                    resumo.classList.toggle('border-amber-300', !podeSortear);
                    resumo.classList.toggle('bg-amber-50', !podeSortear);
                    resumo.classList.toggle('bg-slate-50', podeSortear);
                }
                if (btnSortear) {
                    btnSortear.disabled = !podeSortear;
                }
                if (avisoMinimo) {
                    avisoMinimo.textContent = podeSortear
                        ? ''
                        : `Marque pelo menos ${porTime} jogador(es) presentes para completar um time antes de sortear.`;
                    avisoMinimo.classList.toggle('hidden', podeSortear);
                }
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
                li.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (!dragged || dragged === li) return;
                    const rect = li.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / rect.height > 0.5;
                    lista.insertBefore(dragged, next ? li.nextSibling : li);
                });
            });

            lista.addEventListener('change', () => {
                atualizarOrdemHidden();
                atualizarResumo();
            });

            inputPorTime?.addEventListener('input', atualizarResumo);

            formPresencas?.addEventListener('submit', () => {
                atualizarOrdemHidden();
            });

            formSorteio?.addEventListener('submit', (e) => {
                copiarPresencasParaSorteio();
                const porTime = Math.max(1, Number(inputPorTime?.value) || 5);
                const presentes = lista.querySelectorAll('.presente-check:checked').length;
                if (presentes < porTime) {
                    e.preventDefault();
                    alert(`É necessário pelo menos ${porTime} jogador(es) presentes para completar um time.`);
                }
            });

            atualizarOrdemHidden();
            atualizarResumo();
        })();
    </script>
</x-app-layout>
