<x-app-layout>
    <div class="bg-slate-50">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <x-page-header
                title="Perfil"
                description="Mantenha seus dados atualizados para participar das peladas e aparecer corretamente nas listas do site."
                eyebrow="Minha conta"
            />

            @if(session('status') === 'profile-updated')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900 shadow-sm">
                    Perfil salvo com sucesso.
                </div>
            @elseif(session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-900 shadow-sm" role="alert">
                    <p class="font-semibold">Não foi possível salvar as alterações.</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                    @if($errors->has('username'))
                        <a href="#profile-field-username" class="mt-3 inline-flex text-sm font-semibold text-red-800 underline underline-offset-4">
                            Ir para o campo de username
                        </a>
                    @endif
                </div>
            @endif

            <div class="rounded-lg border border-emerald-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-slate-950">Perfil público do peladeiro</h2>
                        <p class="mt-1 text-sm text-slate-600">Veja como outros jogadores enxergam seu cartao esportivo, estatisticas e links sociais.</p>
                    </div>
                    <a href="{{ route('peladeiros.show', auth()->user()->publicProfile()) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Ver meu perfil público
                    </a>
                </div>
            </div>

            @if(session('status') === 'Complete o seu perfil para aproveitar ao máximo a plataforma!')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 text-emerald-950">
                    <h2 class="font-semibold">Complete seu perfil para jogar melhor</h2>
                    <p class="mt-1 text-sm text-emerald-800">Adicione foto, telefone, estado, cidade, bairro e dados de jogador. Isso ajuda organizadores a confirmar sua participação e deixa suas avaliações mais confiáveis.</p>
                </div>
            @endif

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
