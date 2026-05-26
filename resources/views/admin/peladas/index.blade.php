<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-page-header
            eyebrow="Admin"
            title="Peladas"
            description="Acompanhe as peladas cadastradas, seus organizadores, categorias e status."
        />

        <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="min-w-[860px] w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="p-3">Nome</th>
                        <th class="p-3">Organizador</th>
                        <th class="p-3">Esporte</th>
                        <th class="p-3">Categoria</th>
                        <th class="p-3">Fundacao</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peladas as $pelada)
                        <tr>
                            <td class="p-3 font-semibold text-slate-900">{{ $pelada->nome }}</td>
                            <td class="p-3">{{ $pelada->organizador->name }}</td>
                            <td class="p-3">{{ $pelada->esporte->nome }}</td>
                            <td class="p-3">{{ $pelada->categoriaLabel() }}</td>
                            <td class="p-3">{{ $pelada->data_fundacao ? $pelada->data_fundacao->format('d/m/Y') : '-' }}</td>
                            <td class="p-3">
                                <x-status-badge :variant="$pelada->ativa ? 'ativo' : 'inativo'">
                                    {{ $pelada->ativa ? 'Ativa' : 'Inativa' }}
                                </x-status-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500">Nenhuma pelada cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $peladas->links() }}</div>
    </div>
</x-app-layout>
