<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Participantes - {{ $jogo->titulo }}</h1>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-900">Confirmados</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @foreach($jogo->participantes->where('status', 'confirmado') as $participante)
                        <p class="py-2 text-sm">{{ $participante->user->name }} - {{ $participante->tipo }}</p>
                    @endforeach
                </div>
            </section>
            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-900">Fila de espera</h2>
                <div class="mt-3 divide-y divide-slate-100">
                    @foreach($jogo->participantes->where('status', 'fila')->sortBy('posicao_fila') as $participante)
                        <p class="py-2 text-sm">#{{ $participante->posicao_fila }} {{ $participante->user->name }} - {{ $participante->tipo }}</p>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
