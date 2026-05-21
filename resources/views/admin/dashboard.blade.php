<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Painel administrativo</h1>
        <p class="mt-2 text-slate-600">Visão geral do Vai Ter Pelada.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @foreach([
                'Usuários' => $usuariosCount,
                'Organizadores' => $organizadoresCount,
                'Peladas' => $peladasCount,
                'Modalidades' => $esportesCount,
                'Banners' => $bannersCount,
                'Patrocinadores' => $patrocinadoresCount,
            ] as $label => $value)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 font-semibold text-emerald-700">Usuários</a>
            <a href="{{ route('admin.esportes.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 font-semibold text-emerald-700">Esportes</a>
            <a href="{{ route('admin.peladas.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 font-semibold text-emerald-700">Peladas</a>
            <a href="{{ route('admin.banners.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 font-semibold text-emerald-700">Banners</a>
            <a href="{{ route('admin.patrocinadores.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 font-semibold text-emerald-700">Patrocinadores</a>
        </div>
    </div>
</x-app-layout>
