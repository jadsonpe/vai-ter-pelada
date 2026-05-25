<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <x-page-header
            eyebrow="Admin"
            title="Patrocinadores"
            description="Gerencie apoiadores exibidos nas areas publicas do site."
        />

        <form method="POST" action="{{ route('admin.patrocinadores.store') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            <label class="text-sm font-medium text-slate-700">Nome
                <input name="nome" class="mt-1 w-full rounded-md border-slate-300" placeholder="Nome do patrocinador">
            </label>
            <label class="text-sm font-medium text-slate-700">Logo
                <input name="logo" class="mt-1 w-full rounded-md border-slate-300" placeholder="URL da logo">
            </label>
            <label class="text-sm font-medium text-slate-700">Site
                <input name="link" class="mt-1 w-full rounded-md border-slate-300" placeholder="https://...">
            </label>
            <label class="text-sm font-medium text-slate-700">Telefone
                <input name="telefone" class="mt-1 w-full rounded-md border-slate-300" placeholder="(00) 00000-0000">
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="ativo" value="1" checked>
                Ativo
            </label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700 md:col-span-2">Adicionar patrocinador</button>
        </form>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($patrocinadores as $patrocinador)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        @if($patrocinador->logo)
                            <img src="{{ $patrocinador->logo }}" alt="{{ $patrocinador->nome }}" class="h-12 w-12 rounded-md object-cover">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-md bg-emerald-100 font-bold text-emerald-800">{{ str($patrocinador->nome)->substr(0, 1)->upper() }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-900">{{ $patrocinador->nome }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $patrocinador->telefone ?: 'Telefone nao informado' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.patrocinadores.update', $patrocinador) }}" class="mt-4 grid gap-3">
                        @csrf
                        @method('PUT')
                        <input name="nome" value="{{ $patrocinador->nome }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="Nome">
                        <input name="logo" value="{{ $patrocinador->logo }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="URL da logo">
                        <input name="link" value="{{ $patrocinador->link }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="https://...">
                        <input name="telefone" value="{{ $patrocinador->telefone }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="Telefone">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ativo" value="1" @checked($patrocinador->ativo)>
                            Ativo
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salvar</button>
                            @if($patrocinador->link)
                                <a href="{{ $patrocinador->link }}" target="_blank" class="inline-flex rounded-md border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">Abrir site</a>
                            @endif
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.patrocinadores.destroy', $patrocinador) }}" class="mt-3" onsubmit="return confirm('Remover este patrocinador?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remover</button>
                    </form>
                </article>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white p-5 text-sm text-slate-500 md:col-span-2">Nenhum patrocinador cadastrado.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>

