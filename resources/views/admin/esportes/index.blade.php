<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <x-page-header
            eyebrow="Admin"
            title="Esportes"
            description="Gerencie modalidades, icones e visibilidade das categorias usadas nas peladas."
        />

        <form method="POST" action="{{ route('admin.esportes.store') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
            @csrf
            <label class="text-sm font-medium text-slate-700">Nome
                <input name="nome" class="mt-1 w-full rounded-md border-slate-300" placeholder="Futebol">
            </label>
            <label class="text-sm font-medium text-slate-700">Icone
                <input name="icone" class="mt-1 w-full rounded-md border-slate-300" placeholder="bola ou nome do icone">
            </label>
            <label class="flex h-10 items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="ativo" value="1" checked>
                Ativo
            </label>
            <button class="h-10 rounded-md bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Adicionar</button>
        </form>

        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white shadow-sm">
            @forelse($esportes as $esporte)
                <div class="grid gap-3 p-4 md:grid-cols-[1fr_auto] md:items-end">
                    <form method="POST" action="{{ route('admin.esportes.update', $esporte) }}" class="grid gap-3 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
                        @csrf
                        @method('PUT')
                        <label class="text-sm font-medium text-slate-700">Nome
                            <input name="nome" value="{{ $esporte->nome }}" class="mt-1 w-full rounded-md border-slate-300">
                        </label>
                        <label class="text-sm font-medium text-slate-700">Icone
                            <input name="icone" value="{{ $esporte->icone }}" class="mt-1 w-full rounded-md border-slate-300">
                        </label>
                        <label class="flex h-10 items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ativo" value="1" @checked($esporte->ativo)>
                            Ativo
                        </label>
                        <button class="h-10 rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salvar</button>
                    </form>

                    <form method="POST" action="{{ route('admin.esportes.destroy', $esporte) }}" onsubmit="return confirm('Remover este esporte?')">
                        @csrf
                        @method('DELETE')
                        <button class="h-10 w-full rounded-md border border-red-200 px-4 text-sm font-semibold text-red-700 hover:bg-red-50 md:w-auto">Remover</button>
                    </form>
                </div>
            @empty
                <p class="p-5 text-sm text-slate-500">Nenhum esporte cadastrado.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>


