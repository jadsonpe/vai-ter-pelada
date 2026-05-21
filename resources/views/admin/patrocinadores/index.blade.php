<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Patrocinadores</h1>
        <form method="POST" action="{{ route('admin.patrocinadores.store') }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-4">@csrf <input name="nome" class="w-full rounded-md border-slate-300" placeholder="Nome"><input name="logo" class="w-full rounded-md border-slate-300" placeholder="Logo URL"><input name="link" class="w-full rounded-md border-slate-300" placeholder="Site"><input name="telefone" class="w-full rounded-md border-slate-300" placeholder="Telefone"><button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white md:col-span-4">Adicionar</button></form>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">@foreach($patrocinadores as $patrocinador)<div class="p-4"><p class="font-semibold">{{ $patrocinador->nome }}</p><p class="text-sm text-slate-500">{{ $patrocinador->site_url }}</p></div>@endforeach</div>
    </div>
</x-app-layout>
