<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Peladas</h1>
        <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="min-w-[680px] w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="p-3">Nome</th><th class="p-3">Organizador</th><th class="p-3">Esporte</th><th class="p-3">Status</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($peladas as $pelada)<tr><td class="p-3">{{ $pelada->nome }}</td><td class="p-3">{{ $pelada->organizador->name }}</td><td class="p-3">{{ $pelada->esporte->nome }}</td><td class="p-3">{{ $pelada->ativa ? 'Ativa' : 'Inativa' }}</td></tr>@endforeach</tbody></table>
        </div>
        <div class="mt-4">{{ $peladas->links() }}</div>
    </div>
</x-app-layout>
