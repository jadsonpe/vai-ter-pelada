<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <x-page-header
            eyebrow="Admin"
            title="Banners"
            description="Cadastre chamadas visuais para areas promocionais do site."
        />

        <form method="POST" action="{{ route('admin.banners.store') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            <label class="text-sm font-medium text-slate-700">Titulo
                <input name="titulo" class="mt-1 w-full rounded-md border-slate-300" placeholder="Titulo do banner">
            </label>
            <label class="text-sm font-medium text-slate-700">Imagem
                <input name="imagem" class="mt-1 w-full rounded-md border-slate-300" placeholder="URL da imagem">
            </label>
            <label class="text-sm font-medium text-slate-700">Link
                <input name="link" class="mt-1 w-full rounded-md border-slate-300" placeholder="https://...">
            </label>
            <label class="text-sm font-medium text-slate-700">Posicao
                <input name="posicao" value="home_topo" class="mt-1 w-full rounded-md border-slate-300">
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="ativo" value="1" checked>
                Ativo
            </label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700 md:col-span-2">Adicionar banner</button>
        </form>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($banners as $banner)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $banner->titulo }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $banner->posicao }}</p>
                        </div>
                        <x-status-badge :variant="$banner->ativo ? 'ativo' : 'inativo'">{{ $banner->ativo ? 'Ativo' : 'Inativo' }}</x-status-badge>
                    </div>

                    <form method="POST" action="{{ route('admin.banners.update', $banner) }}" class="mt-4 grid gap-3">
                        @csrf
                        @method('PUT')
                        <input name="titulo" value="{{ $banner->titulo }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="Titulo">
                        <input name="imagem" value="{{ $banner->imagem }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="URL da imagem">
                        <input name="link" value="{{ $banner->link }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="https://...">
                        <input name="posicao" value="{{ $banner->posicao }}" class="w-full rounded-md border-slate-300 text-sm" placeholder="Posicao">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="ativo" value="1" @checked($banner->ativo)>
                            Ativo
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salvar</button>
                            @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" class="inline-flex rounded-md border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">Abrir link</a>
                            @endif
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="mt-3" onsubmit="return confirm('Remover este banner?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remover</button>
                    </form>
                </article>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white p-5 text-sm text-slate-500 md:col-span-2">Nenhum banner cadastrado.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>


