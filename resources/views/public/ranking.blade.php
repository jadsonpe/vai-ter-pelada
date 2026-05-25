<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Ranking de jogadores</h1>
                <p class="mt-2 text-slate-600">Os melhores jogadores pelo desempenho nas partidas e pela média de avaliações recebidas.</p>

                <div class="mt-6 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                    @foreach($jogadores as $jogador)
                        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <x-user-avatar :user="$jogador" size="sm" />
                                <div>
                                    <span class="font-semibold text-slate-900">{{ ($jogadores->currentPage() - 1) * 15 + $loop->iteration }}. {{ $jogador->name }}</span>
                                    <p class="mt-1 text-sm text-slate-500">{{ $jogador->avaliacoes_recebidas_count }} avaliações • Média {{ number_format($jogador->avaliacoes_recebidas_avg ?? 0, 2) }}/5</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">{{ $jogador->participacoes_count }} presenças</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $jogadores->links() }}
                </div>
            </div>

            <aside class="space-y-6">
                <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Leaderboard semanal</h2>
                    <p class="mt-1 text-sm text-slate-500">Quem acumula mais pontos nos últimos 7 dias.</p>
                    <div class="mt-5 space-y-3">
                        @foreach($weeklyLeaderboard as $jogador)
                            <div class="flex items-center justify-between rounded-3xl border border-slate-200 px-4 py-3">
                                <span class="flex items-center gap-2 font-medium text-slate-900"><x-user-avatar :user="$jogador" size="xs" /> {{ $loop->iteration }}. {{ $jogador->name }}</span>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">{{ $jogador->weekly_points ?? 0 }} pts</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Leaderboard mensal</h2>
                    <p class="mt-1 text-sm text-slate-500">Quem mais pontuou no último mês.</p>
                    <div class="mt-5 space-y-3">
                        @foreach($monthlyLeaderboard as $jogador)
                            <div class="flex items-center justify-between rounded-3xl border border-slate-200 px-4 py-3">
                                <span class="flex items-center gap-2 font-medium text-slate-900"><x-user-avatar :user="$jogador" size="xs" /> {{ $loop->iteration }}. {{ $jogador->name }}</span>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">{{ $jogador->monthly_points ?? 0 }} pts</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
