<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Sorteio - {{ $jogo->titulo }}</h1>
        <form method="POST" action="{{ route('organizador.jogos.sorteios.sortear', $jogo) }}" class="mt-6 flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-5 sm:flex-row">
            @csrf
            <input type="number" min="2" name="quantidade_times" value="2" class="rounded-md border-slate-300">
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Sortear times</button>
        </form>
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            @foreach($sorteios as $sorteio)
                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <h2 class="font-semibold">Sorteio {{ $sorteio->created_at->format('d/m/Y H:i') }}</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach($sorteio->times as $time)
                            <div class="rounded-md bg-slate-50 p-4">
                                <h3 class="font-semibold text-emerald-700">{{ $time->nome }}</h3>
                                @foreach($time->jogadores as $jogador)
                                    <p class="mt-2 text-sm">{{ $jogador->user->name }}</p>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>
