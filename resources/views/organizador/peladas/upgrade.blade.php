<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <section class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Atualizacao de plano</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Voce atingiu o limite do plano gratis</h1>
            <p class="mt-3 text-slate-600">
                Seu plano atual permite criar {{ $limite }} pelada. Voce ja criou {{ $peladasCriadas }}.
                Para organizar mais peladas, sera necessario atualizar seu plano.
            </p>

            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 p-5">
                    <h2 class="text-lg font-semibold text-slate-900">Gratis</h2>
                    <p class="mt-2 text-sm text-slate-600">Ideal para organizar uma pelada recorrente.</p>
                    <p class="mt-4 text-2xl font-bold text-slate-900">1 pelada</p>
                </div>

                <div class="rounded-lg border-2 border-emerald-500 p-5">
                    <h2 class="text-lg font-semibold text-slate-900">Organizador Plus</h2>
                    <p class="mt-2 text-sm text-slate-600">Para quem organiza varias peladas, turmas ou horarios.</p>
                    <p class="mt-4 text-2xl font-bold text-emerald-700">Em breve</p>
                    <a href="{{ route('home') }}" class="mt-5 inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">
                        Quero ser avisado
                    </a>
                </div>
            </div>

            <a href="{{ route('organizador.peladas.index') }}" class="mt-6 inline-flex text-sm font-semibold text-emerald-700">
                Voltar para minhas peladas
            </a>
        </section>
    </div>
</x-app-layout>
