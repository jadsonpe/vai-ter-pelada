<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Sua avaliação no sistema</h2>
                <p class="mt-2 text-sm text-slate-500">Média de todas as avaliações recebidas nas partidas.</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="rounded-3xl bg-slate-950 px-5 py-4 text-white shadow-sm">
                        <p class="text-xs uppercase tracking-[0.24em] text-emerald-300">Média geral</p>
                        <p class="mt-2 text-3xl font-semibold">{{ number_format(auth()->user()->rating_average, 2) }} / 5</p>
                    </div>
                    <div class="rounded-3xl bg-slate-950 px-5 py-4 text-white shadow-sm">
                        <p class="text-xs uppercase tracking-[0.24em] text-emerald-300">Avaliações recebidas</p>
                        <p class="mt-2 text-3xl font-semibold">{{ auth()->user()->rating_count }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('perfil.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('perfil.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('perfil.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
