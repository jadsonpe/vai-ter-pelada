<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Esportes</h1>
        <form method="POST" action="{{ route('admin.esportes.store') }}" class="mt-6 flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-5 sm:flex-row">@csrf <input name="nome" class="w-full rounded-md border-slate-300" placeholder="Futebol"><input name="icone" class="w-full rounded-md border-slate-300" placeholder="Ícone"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="ativo" value="1" checked> Ativo</label><button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white sm:w-auto">Adicionar</button></form>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @foreach($esportes as $esporte)
                <form method="POST" action="{{ route('admin.esportes.update', $esporte) }}" class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">@csrf @method('PUT')<input name="nome" value="{{ $esporte->nome }}" class="w-full rounded-md border-slate-300"><input name="icone" value="{{ $esporte->icone }}" class="w-full rounded-md border-slate-300"><label class="flex gap-2 text-sm"><input type="checkbox" name="ativo" value="1" @checked($esporte->ativo)> Ativo</label><button class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold sm:w-auto">Salvar</button></form>
            @endforeach
        </div>
    </div>
</x-app-layout>
