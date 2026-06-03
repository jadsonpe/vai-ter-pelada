<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Peladas que organizo</h1>
                <p class="mt-1 text-sm text-slate-600">Crie uma pelada para se tornar o organizador dela e gerenciar membros, rodadas e sorteios.</p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Plano {{ match(auth()->user()->plano ?? 'gratis') { 'gratis' => 'Grátis', 'plus' => 'Plus', 'ilimitado' => 'Ilimitado', default => auth()->user()->plano } }}: {{ $peladas->count() }}/{{ auth()->user()->limite_peladas ?: 1 }} {{ $peladas->count() === 1 ? 'pelada criada' : 'peladas criadas' }}
                </p>
            </div>
            <a href="{{ route('organizador.peladas.create') }}" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white sm:w-auto">
                {{ auth()->user()->podeCriarPelada() ? 'Nova pelada' : 'Atualizar plano' }}
            </a>
        </div>
        <div class="mt-6 space-y-4 md:hidden">
            @forelse($peladas as $pelada)
                <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="flex gap-3 p-4">
                        <x-pelada-imagem variant="thumb" :src="$pelada->imagemUrl()" :alt="$pelada->nome" empty="Sem foto" />
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-semibold text-slate-900">{{ $pelada->nome }}</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $pelada->esporte?->nome ?: 'Esporte nao informado' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $pelada->capacidade }} vagas - {{ $pelada->categoriaLabel() }}</p>
                            @if($pelada->data_fundacao)
                                <p class="mt-1 text-xs text-slate-500">Desde {{ $pelada->data_fundacao->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-t border-slate-100 p-4 text-sm font-semibold">
                        <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.edit', $pelada) }}">Editar</a>
                        <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.membros.index', $pelada) }}">Membros</a>
                        <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.jogos.index', $pelada) }}">Rodadas</a>
                        @if($pelada->esporte && in_array($pelada->esporte->slug, ['futebol', 'society', 'futsal'], true) && \Illuminate\Support\Facades\Route::has('organizador.peladas.torneios.index'))
                            <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.torneios.index', $pelada) }}">Torneio</a>
                        @endif
                        <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.caixa.index', $pelada) }}">Caixa</a>
                        <a class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-emerald-700" href="{{ route('organizador.peladas.solicitacoes.index', $pelada) }}">Solicitações</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-slate-200 bg-white p-5 text-sm text-slate-600">
                    Você ainda não criou nenhuma pelada.
                </div>
            @endforelse
        </div>

        <div class="mt-6 hidden overflow-hidden rounded-lg border border-slate-200 bg-white md:block">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Pelada</th><th class="p-3">Esporte</th><th class="p-3">Categoria</th><th class="p-3">Fundacao</th><th class="p-3">Vagas</th><th class="p-3"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peladas as $pelada)
                        <tr>
                            <td class="p-3">
                                <div class="flex items-center gap-3">
                                    <x-pelada-imagem variant="thumb" :src="$pelada->imagemUrl()" :alt="$pelada->nome" empty="Sem foto" />
                                    <span class="font-medium">{{ $pelada->nome }}</span>
                                </div>
                            </td>
                            <td class="p-3">{{ $pelada->esporte?->nome ?: 'Esporte nao informado' }}</td>
                            <td class="p-3">{{ $pelada->categoriaLabel() }}</td>
                            <td class="p-3">{{ $pelada->data_fundacao ? $pelada->data_fundacao->format('d/m/Y') : '-' }}</td>
                            <td class="p-3">{{ $pelada->capacidade }}</td>
                            <td class="p-3">
                                <div class="flex flex-wrap justify-end gap-3 font-semibold">
                                    <a class="text-emerald-700" href="{{ route('organizador.peladas.edit', $pelada) }}">Editar</a>
                                    <a class="text-emerald-700" href="{{ route('organizador.peladas.membros.index', $pelada) }}">Membros</a>
                                    <a class="text-emerald-700" href="{{ route('organizador.peladas.jogos.index', $pelada) }}">Rodadas</a>
                                    @if($pelada->esporte && in_array($pelada->esporte->slug, ['futebol', 'society', 'futsal'], true) && \Illuminate\Support\Facades\Route::has('organizador.peladas.torneios.index'))
                                        <a class="text-emerald-700" href="{{ route('organizador.peladas.torneios.index', $pelada) }}">Torneio</a>
                                    @endif
                                    <a class="text-emerald-700" href="{{ route('organizador.peladas.caixa.index', $pelada) }}">Caixa</a>
                                    <a class="text-emerald-700" href="{{ route('organizador.peladas.solicitacoes.index', $pelada) }}">Solicitações</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-sm text-slate-600">Você ainda não criou nenhuma pelada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
