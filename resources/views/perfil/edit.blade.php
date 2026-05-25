<x-app-layout>
    <div class="bg-slate-50">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <x-page-header
                title="Perfil"
                description="Mantenha seus dados atualizados para participar das peladas e aparecer corretamente nas listas do site."
                eyebrow="Minha conta"
            />

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <x-user-avatar :user="auth()->user()" size="lg" />
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-950 p-5 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-300">Media geral</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format(auth()->user()->rating_average, 2) }} / 5</p>
                    <p class="mt-1 text-sm text-slate-300">Avaliacao recebida nas partidas.</p>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Avaliacoes recebidas</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950">{{ auth()->user()->rating_count }}</p>
                    <p class="mt-1 text-sm text-slate-500">Historico acumulado no sistema.</p>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                @include('perfil.partials.update-profile-information-form')
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                    @include('perfil.partials.update-password-form')
                </div>

                <div class="rounded-lg border border-red-100 bg-white p-5 shadow-sm sm:p-8">
                    @include('perfil.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
