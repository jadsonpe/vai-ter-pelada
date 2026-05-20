<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Peladas</h1>
        <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($peladas as $pelada)
                <a href="{{ route('peladas.show', $pelada) }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-emerald-300">
                    <p class="text-sm font-medium text-emerald-700">{{ $pelada->esporte->nome }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $pelada->nome }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $pelada->local }}</p>
                    <p class="mt-4 text-sm text-slate-500">{{ $pelada->capacidade }} vagas por rodada</p>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $peladas->links() }}</div>
    </div>
</x-app-layout>
