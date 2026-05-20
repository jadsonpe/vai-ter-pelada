<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Minhas peladas</h1>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach($membros as $membro)
                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-emerald-700">{{ $membro->tipo }} - {{ $membro->status }}</p>
                            <h2 class="mt-1 text-xl font-semibold">{{ $membro->pelada->nome }}</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $membro->pelada->local }}</p>
                        </div>
                        <a href="{{ route('peladas.show', $membro->pelada) }}" class="text-sm font-semibold text-emerald-700">Abrir</a>
                    </div>
                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach($membro->pelada->jogos as $jogo)
                            <div class="flex items-center justify-between py-3">
                                <span class="text-sm">{{ $jogo->titulo }} - {{ $jogo->data_hora->format('d/m/Y H:i') }}</span>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}">
                                        @csrf
                                        <button class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">Confirmar</button>
                                    </form>
                                    <form method="POST" action="{{ route('jogador.jogos.cancelar', $jogo) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold">Cancelar</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <section class="mt-8 rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-slate-900">Solicitacoes de mensalista</h2>
            <div class="mt-3 divide-y divide-slate-100">
                @forelse($solicitacoes as $solicitacao)
                    <div class="flex justify-between py-3 text-sm">
                        <span>{{ $solicitacao->pelada->nome }}</span>
                        <span class="font-semibold">{{ $solicitacao->status }}</span>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">Nenhuma solicitacao enviada.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
