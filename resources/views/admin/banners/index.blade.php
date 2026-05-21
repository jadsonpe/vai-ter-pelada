<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Banners</h1>
        <form method="POST" action="{{ route('admin.banners.store') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-3">@csrf <input name="titulo" class="w-full rounded-md border-slate-300" placeholder="Titulo"><input name="imagem" class="w-full rounded-md border-slate-300" placeholder="URL da imagem"><input name="link" class="w-full rounded-md border-slate-300" placeholder="Link"><input name="posicao" value="home_topo" class="w-full rounded-md border-slate-300"><label class="flex gap-2 text-sm"><input type="checkbox" name="ativo" value="1" checked> Ativo</label><button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white md:w-auto">Adicionar</button></form>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">@foreach($banners as $banner)<div class="p-4"><p class="font-semibold">{{ $banner->titulo }}</p><p class="text-sm text-slate-500">{{ $banner->posicao }} - {{ $banner->ativo ? 'ativo' : 'inativo' }}</p></div>@endforeach</div>
    </div>
</x-app-layout>
