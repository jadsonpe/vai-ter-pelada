<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <main>
                <p class="text-sm font-semibold text-emerald-700">{{ $pelada->esporte->nome }}</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">{{ $pelada->nome }}</h1>
                <p class="mt-3 text-slate-600">{{ $pelada->descricao ?: 'Pelada recorrente aberta para confirmacao de jogadores.' }}</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Local</span><p class="font-semibold">{{ $pelada->local }}</p></div>
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Capacidade</span><p class="font-semibold">{{ $pelada->capacidade }}</p></div>
                    <div class="rounded-lg border border-slate-200 p-4"><span class="text-sm text-slate-500">Organizador</span><p class="font-semibold">{{ $pelada->organizador->name }}</p></div>
                </div>
            </main>
            <aside class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-900">Solicitar mensalista</h2>
                @auth
                    <form method="POST" action="{{ route('jogador.peladas.solicitar-mensalista', $pelada) }}" class="mt-4 space-y-3">
                        @csrf
                        <textarea name="mensagem" class="w-full rounded-md border-slate-300" rows="3" placeholder="Mensagem opcional"></textarea>
                        <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Enviar solicitacao</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="mt-4 block rounded-md bg-emerald-600 px-4 py-2 text-center font-semibold text-white">Entrar para participar</a>
                @endauth
            </aside>
        </div>

        <section class="mt-10">
            <h2 class="text-xl font-bold text-slate-900">Proximas rodadas</h2>
            <div class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @forelse($pelada->jogos as $jogo)
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $jogo->titulo }}</h3>
                            <p class="text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }} - {{ $jogo->participantes->where('status', 'confirmado')->count() }} confirmados</p>
                        </div>
                        @auth
                            <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}">
                                @csrf
                                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Confirmar presenca</button>
                            </form>
                        @endauth
                    </div>
                @empty
                    <p class="p-4 text-sm text-slate-600">Nenhuma rodada criada ainda.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
