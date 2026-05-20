<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    @include('shared.status')
    <h1 class="text-3xl font-bold text-slate-900">Dashboard do jogador</h1>
    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-slate-900">Minhas peladas</h2>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $membros->count() }}</p>
        </section>
        <section class="rounded-lg border border-slate-200 bg-white p-5 lg:col-span-2">
            <h2 class="font-semibold text-slate-900">Proximas rodadas</h2>
            <div class="mt-3 divide-y divide-slate-100">
                @forelse($proximosJogos as $jogo)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium">{{ $jogo->pelada->nome }} - {{ $jogo->titulo }}</p>
                            <p class="text-sm text-slate-500">{{ $jogo->data_hora->format('d/m/Y H:i') }}</p>
                        </div>
                        <form method="POST" action="{{ route('jogador.jogos.confirmar', $jogo) }}">
                            @csrf
                            <button class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white">Confirmar</button>
                        </form>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">Nenhuma rodada futura encontrada.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
