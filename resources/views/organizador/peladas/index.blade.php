<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-slate-900">Minhas peladas</h1>
            <a href="{{ route('organizador.peladas.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Nova pelada</a>
        </div>
        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Nome</th><th class="p-3">Esporte</th><th class="p-3">Vagas</th><th class="p-3"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($peladas as $pelada)
                        <tr>
                            <td class="p-3 font-medium">{{ $pelada->nome }}</td>
                            <td class="p-3">{{ $pelada->esporte->nome }}</td>
                            <td class="p-3">{{ $pelada->capacidade }}</td>
                            <td class="p-3 text-right">
                                <a class="text-emerald-700" href="{{ route('organizador.peladas.edit', $pelada) }}">Editar</a>
                                <a class="ms-3 text-emerald-700" href="{{ route('organizador.peladas.membros.index', $pelada) }}">Membros</a>
                                <a class="ms-3 text-emerald-700" href="{{ route('organizador.peladas.jogos.index', $pelada) }}">Rodadas</a>
                                <a class="ms-3 text-emerald-700" href="{{ route('organizador.peladas.solicitacoes.index', $pelada) }}">Solicitacoes</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
