<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Ranking de presencas</h1>
        <div class="mt-6 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
            @foreach($jogadores as $jogador)
                <div class="flex items-center justify-between p-4">
                    <span class="font-medium">{{ $loop->iteration }}. {{ $jogador->name }}</span>
                    <span class="rounded bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">{{ $jogador->participacoes_count }} presencas</span>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
