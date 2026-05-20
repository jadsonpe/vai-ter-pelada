<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Rodadas - {{ $pelada->nome }}</h1>
        <form method="POST" action="{{ route('organizador.peladas.jogos.store', $pelada) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-4">
            @csrf
            <input name="titulo" class="rounded-md border-slate-300" placeholder="Rodada #1">
            <input type="datetime-local" name="data_hora" class="rounded-md border-slate-300">
            <input type="number" name="capacidade" class="rounded-md border-slate-300" placeholder="Vagas">
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Criar rodada</button>
        </form>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @foreach($pelada->jogos as $jogo)
                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold">{{ $jogo->titulo }}</p>
                        <p class="text-sm text-slate-600">{{ $jogo->data_hora->format('d/m/Y H:i') }} - {{ $jogo->participantes->where('status', 'confirmado')->count() }} confirmados</p>
                    </div>
                    <div class="flex gap-3 text-sm font-semibold">
                        <a class="text-emerald-700" href="{{ route('organizador.jogos.participantes', $jogo) }}">Participantes</a>
                        <a class="text-emerald-700" href="{{ route('organizador.jogos.sorteios.show', $jogo) }}">Sortear</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
