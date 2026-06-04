<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Denuncias</h1>
                <p class="mt-2 text-slate-600">Analise denuncias enviadas por jogadores cadastrados.</p>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex gap-2">
                <select name="status" class="rounded-md border-slate-300 text-sm">
                    <option value="">Todos os status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
            </form>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse($reports as $report)
                    @php
                        $targetUrl = data_get($report->metadata, 'target_url');
                        $targetName = data_get($report->metadata, 'target_name', 'Alvo');
                        $type = data_get($report->metadata, 'type', class_basename($report->reportable_type));
                    @endphp
                    <article class="grid gap-4 p-5 lg:grid-cols-[minmax(0,1fr)_320px]">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-bold uppercase text-red-700">#{{ $report->id }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $statuses[$report->status] ?? $report->status }}</span>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($type) }}</span>
                            </div>
                            <h2 class="mt-3 text-lg font-bold text-slate-950">{{ $targetName }}</h2>
                            <p class="mt-1 text-sm text-slate-500">Denunciante: {{ $report->reporter?->name ?? 'Usuario removido' }} - {{ $report->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-3 text-sm font-semibold text-slate-700">Motivo: {{ $report->reason }}</p>
                            @if($report->description)
                                <p class="mt-2 whitespace-pre-line rounded-md bg-slate-50 p-3 text-sm leading-6 text-slate-700">{{ $report->description }}</p>
                            @endif
                            @if($targetUrl)
                                <a href="{{ $targetUrl }}" class="mt-3 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-800">Abrir alvo denunciado</a>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-3 rounded-lg bg-slate-50 p-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Status</label>
                                <select name="status" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected($report->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Resolucao</label>
                                <textarea name="resolution" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm" placeholder="Observacoes internas">{{ old('resolution', $report->resolution) }}</textarea>
                            </div>
                            <button class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Atualizar</button>
                        </form>
                    </article>
                @empty
                    <p class="p-6 text-sm text-slate-500">Nenhuma denúncia encontrada.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>
</x-app-layout>
