<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div>
            <a href="{{ route('organizador.peladas.torneios.index', $pelada) }}" class="text-sm font-semibold text-emerald-700">Voltar para torneios</a>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $torneio->exists ? 'Editar torneio' : 'Novo torneio' }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $pelada->nome }}</p>
        </div>

        @php
            $formatoAtual = old('formato', $torneio->formato ?: 'pontos_corridos');
            $tipoConfrontoAtual = old('tipo_confronto', $torneio->tipo_confronto ?: 'ida');
            $mataMataAtual = old('tipo_confronto_mata_mata', $torneio->tipo_confronto_mata_mata ?: 'unico');
            $finalAtual = old('tipo_confronto_final', $torneio->tipo_confronto_final ?: 'unico');
        @endphp

        <form
            method="POST"
            action="{{ $torneio->exists ? route('organizador.torneios.update', $torneio) : route('organizador.peladas.torneios.store', $pelada) }}"
            class="mt-6 space-y-5"
            data-tournament-wizard
        >
            @csrf
            @if($torneio->exists)
                @method('PATCH')
            @endif

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">1</span>
                    <div>
                        <h2 class="font-bold text-slate-950">Dados principais</h2>
                        <p class="text-sm text-slate-600">Defina nome, data e quantidade de times antes de escolher o formato.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700 md:col-span-2">
                        Nome do torneio
                        <input name="nome" value="{{ old('nome', $torneio->nome) }}" required class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Copa dos Mensalistas">
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </label>

                    <label class="text-sm font-medium text-slate-700">
                        Data
                        <input type="date" name="data_torneio" value="{{ old('data_torneio', optional($torneio->data_torneio)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border-slate-300">
                        <x-input-error :messages="$errors->get('data_torneio')" class="mt-2" />
                    </label>

                    <label class="text-sm font-medium text-slate-700">
                        Jogadores por time
                        <input type="number" name="jogadores_por_time" min="2" max="20" value="{{ old('jogadores_por_time', $torneio->jogadores_por_time) }}" required class="mt-1 w-full rounded-md border-slate-300">
                        <x-input-error :messages="$errors->get('jogadores_por_time')" class="mt-2" />
                    </label>

                    <label class="text-sm font-medium text-slate-700">
                        Quantidade de times
                        <input data-times-count type="number" name="quantidade_times" min="2" max="64" value="{{ old('quantidade_times', $torneio->quantidade_times) }}" required class="mt-1 w-full rounded-md border-slate-300">
                        <x-input-error :messages="$errors->get('quantidade_times')" class="mt-2" />
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">2</span>
                    <div>
                        <h2 class="font-bold text-slate-950">Formato principal</h2>
                        <p class="text-sm text-slate-600">As opcoes mudam automaticamente conforme a quantidade de times.</p>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('formato')" class="mt-4" />

                <div class="mt-5 grid gap-3 lg:grid-cols-3">
                    <label data-format-card="mata_mata" class="cursor-pointer rounded-lg border border-slate-200 p-4 transition hover:border-emerald-300">
                        <input data-format-radio type="radio" name="formato" value="mata_mata" class="sr-only" @checked($formatoAtual === 'mata_mata')>
                        <span class="block font-bold text-slate-950">Mata-mata direto</span>
                        <span class="mt-1 block text-sm text-slate-600">Eliminatoria simples sem folgas. Exige 4, 8, 16, 32 ou 64 times.</span>
                        <span data-format-warning class="mt-3 hidden rounded-md bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800">Indisponivel para esta quantidade de times.</span>
                    </label>

                    <label data-format-card="pontos_corridos" class="cursor-pointer rounded-lg border border-slate-200 p-4 transition hover:border-emerald-300">
                        <input data-format-radio type="radio" name="formato" value="pontos_corridos" class="sr-only" @checked($formatoAtual === 'pontos_corridos')>
                        <span class="block font-bold text-slate-950">Grupo unico</span>
                        <span class="mt-1 block text-sm text-slate-600">Todos contra todos e depois fase final com os melhores colocados.</span>
                    </label>

                    <label data-format-card="grupos_mata_mata" class="cursor-pointer rounded-lg border border-slate-200 p-4 transition hover:border-emerald-300">
                        <input data-format-radio type="radio" name="formato" value="grupos_mata_mata" class="sr-only" @checked($formatoAtual === 'grupos_mata_mata')>
                        <span class="block font-bold text-slate-950">Multi-grupos</span>
                        <span class="mt-1 block text-sm text-slate-600">Times divididos em grupos e classificados encaixando no mata-mata.</span>
                    </label>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label data-section="turnos" class="text-sm font-medium text-slate-700">
                        Turnos da fase inicial
                        <select name="tipo_confronto" class="mt-1 w-full rounded-md border-slate-300">
                            <option value="ida" @selected($tipoConfrontoAtual === 'ida')>Apenas ida</option>
                            <option value="ida_volta" @selected($tipoConfrontoAtual === 'ida_volta')>Ida e volta</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_confronto')" class="mt-2" />
                    </label>

                    <label data-section="classificados-total" class="text-sm font-medium text-slate-700">
                        Classificados para o mata-mata
                        <select data-classificados-total name="classificados_total" class="mt-1 w-full rounded-md border-slate-300"></select>
                        <x-input-error :messages="$errors->get('classificados_total')" class="mt-2" />
                    </label>

                    <label data-section="quantidade-grupos" class="text-sm font-medium text-slate-700">
                        Quantidade de grupos
                        <select data-grupos name="quantidade_grupos" class="mt-1 w-full rounded-md border-slate-300"></select>
                        <x-input-error :messages="$errors->get('quantidade_grupos')" class="mt-2" />
                    </label>

                    <label data-section="classificados-grupo" class="text-sm font-medium text-slate-700">
                        Classificados por grupo
                        <select data-classificados-grupo name="classificados_por_grupo" class="mt-1 w-full rounded-md border-slate-300"></select>
                        <x-input-error :messages="$errors->get('classificados_por_grupo')" class="mt-2" />
                    </label>

                    <label data-section="mata-direto" class="text-sm font-medium text-slate-700">
                        Confrontos do mata-mata direto
                        <select name="tipo_confronto_mata_mata" class="mt-1 w-full rounded-md border-slate-300">
                            <option value="unico" @selected($mataMataAtual === 'unico')>Jogo unico</option>
                            <option value="ida_volta" @selected($mataMataAtual === 'ida_volta')>Ida e volta</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_confronto_mata_mata')" class="mt-2" />
                    </label>
                </div>
            </section>

            <section data-section="fase-final" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">3</span>
                    <div>
                        <h2 class="font-bold text-slate-950">Mata-mata final</h2>
                        <p data-final-summary class="text-sm text-slate-600">A fase inicial sera calculada conforme os classificados.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">
                        Jogos eliminatorios
                        <select name="tipo_confronto_mata_mata" class="mt-1 w-full rounded-md border-slate-300" data-final-mata-select>
                            <option value="unico" @selected($mataMataAtual === 'unico')>Jogo unico</option>
                            <option value="ida_volta" @selected($mataMataAtual === 'ida_volta')>Ida e volta</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_confronto_mata_mata')" class="mt-2" />
                    </label>

                    <label class="text-sm font-medium text-slate-700">
                        Grande final
                        <select name="tipo_confronto_final" class="mt-1 w-full rounded-md border-slate-300">
                            <option value="unico" @selected($finalAtual === 'unico')>Jogo unico</option>
                            <option value="ida_volta" @selected($finalAtual === 'ida_volta')>Ida e volta</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_confronto_final')" class="mt-2" />
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">
                        Placar W.O. vencedor
                        <input type="number" name="wo_gols_vencedor" min="1" max="20" value="{{ old('wo_gols_vencedor', $torneio->wo_gols_vencedor) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>

                    <label class="text-sm font-medium text-slate-700">
                        Placar W.O. perdedor
                        <input type="number" name="wo_gols_perdedor" min="0" max="20" value="{{ old('wo_gols_perdedor', $torneio->wo_gols_perdedor) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>

                    <div class="space-y-2 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="terceiro_lugar" value="1" @checked(old('terceiro_lugar', $torneio->terceiro_lugar))>
                            Disputa de terceiro lugar
                        </label>
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="wo_conta_saldo" value="1" @checked(old('wo_conta_saldo', $torneio->wo_conta_saldo))>
                            W.O. conta para saldo de gols
                        </label>
                    </div>

                    <label class="text-sm font-medium text-slate-700 md:col-span-2">
                        Regras do torneio
                        <textarea name="regras" rows="4" class="mt-1 w-full rounded-md border-slate-300" placeholder="Criterios, tempo de jogo, penaltis, W.O. e observacoes gerais.">{{ old('regras', $torneio->regras) }}</textarea>
                    </label>
                </div>

                <div class="mt-5 flex justify-end">
                    <button class="rounded-md bg-emerald-600 px-5 py-2 font-semibold text-white hover:bg-emerald-700">Salvar</button>
                </div>
            </section>
        </form>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-tournament-wizard]');
            if (!root) return;

            const oldValues = {
                classificadosTotal: @json((int) old('classificados_total', $torneio->classificados_total ?: 2)),
                grupos: @json((int) old('quantidade_grupos', $torneio->quantidade_grupos ?: 2)),
                classificadosGrupo: @json((int) old('classificados_por_grupo', $torneio->classificados_por_grupo ?: 1)),
            };

            const powerOptions = [2, 4, 8, 16, 32, 64];
            const groupOptions = [2, 4, 8, 16];
            const countInput = root.querySelector('[data-times-count]');
            const totalSelect = root.querySelector('[data-classificados-total]');
            const groupSelect = root.querySelector('[data-grupos]');
            const perGroupSelect = root.querySelector('[data-classificados-grupo]');
            const finalSummary = root.querySelector('[data-final-summary]');

            const isPowerOfTwo = (number) => number > 0 && (number & (number - 1)) === 0;
            const currentFormat = () => root.querySelector('[data-format-radio]:checked')?.value || 'pontos_corridos';

            const fillSelect = (select, values, selected, emptyLabel = 'Sem opcoes validas') => {
                select.innerHTML = '';

                if (!values.length) {
                    const option = new Option(emptyLabel, '');
                    select.add(option);
                    select.disabled = true;
                    return;
                }

                select.disabled = false;
                values.forEach((value) => {
                    const option = new Option(String(value), String(value));
                    option.selected = Number(value) === Number(selected);
                    select.add(option);
                });
            };

            const setSection = (name, visible) => {
                root.querySelectorAll(`[data-section="${name}"]`).forEach((section) => {
                    section.classList.toggle('hidden', !visible);
                    section.querySelectorAll('input, select, textarea').forEach((field) => {
                        field.disabled = !visible;
                    });
                });
            };

            const updateCards = (times) => {
                root.querySelectorAll('[data-format-card]').forEach((card) => {
                    const radio = card.querySelector('[data-format-radio]');
                    const active = radio.checked;
                    const blocked = radio.value === 'mata_mata' && !isPowerOfTwo(times);

                    card.classList.toggle('border-emerald-500', active);
                    card.classList.toggle('bg-emerald-50', active);
                    card.classList.toggle('opacity-50', blocked);
                    card.classList.toggle('cursor-not-allowed', blocked);
                    card.querySelector('[data-format-warning]')?.classList.toggle('hidden', !blocked);
                    radio.disabled = blocked;

                    if (blocked && active) {
                        root.querySelector('[data-format-radio][value="pontos_corridos"]').checked = true;
                    }
                });
            };

            const update = () => {
                const times = Number(countInput.value || 0);
                updateCards(times);

                const format = currentFormat();
                const isDirect = format === 'mata_mata';
                const isSingleGroup = format === 'pontos_corridos';
                const isMultiGroup = format === 'grupos_mata_mata';

                setSection('turnos', !isDirect);
                setSection('classificados-total', isSingleGroup);
                setSection('quantidade-grupos', isMultiGroup);
                setSection('classificados-grupo', isMultiGroup);
                setSection('mata-direto', isDirect);
                setSection('fase-final', !isDirect);

                const totalAtual = Number(totalSelect.value || oldValues.classificadosTotal);
                const totalOptions = powerOptions.filter((value) => value < times);
                fillSelect(totalSelect, totalOptions, totalAtual);

                const gruposAtual = Number(groupSelect.value || oldValues.grupos);
                const validGroups = groupOptions.filter((value) => value < times && times % value === 0);
                fillSelect(groupSelect, validGroups, gruposAtual);

                const groups = Number(groupSelect.value || 0);
                const teamsPerGroup = groups ? times / groups : 0;
                const porGrupoAtual = Number(perGroupSelect.value || oldValues.classificadosGrupo);
                const perGroupOptions = Array.from({ length: Math.max(0, teamsPerGroup - 1) }, (_, index) => index + 1)
                    .filter((value) => isPowerOfTwo(value * groups));
                fillSelect(perGroupSelect, perGroupOptions, porGrupoAtual);

                const classified = isSingleGroup
                    ? Number(totalSelect.value || 0)
                    : Number(groupSelect.value || 0) * Number(perGroupSelect.value || 0);
                const phase = classified >= 8 ? 'Quartas de final' : classified === 4 ? 'Semifinal' : classified === 2 ? 'Final' : 'A definir';
                finalSummary.textContent = `Fase inicial calculada: ${phase}.`;
            };

            root.addEventListener('change', update);
            countInput.addEventListener('input', update);
            update();
        })();
    </script>
</x-app-layout>
