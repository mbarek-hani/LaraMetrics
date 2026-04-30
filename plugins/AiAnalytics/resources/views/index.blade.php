<x-app-layout>
    <x-slot name="titre">
        Historique des analyses IA
    </x-slot>
    <div x-data="aiHistory()" class="space-y-4 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- En-tête & Filtre --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Analyses IA</h2>
                <p class="text-sm text-gray-500">Consultez et gérez l'historique de vos rapports intelligents.</p>
            </div>

            <div class="flex items-center gap-2">
                <select
                    x-model="siteId"
                    @change="filter()"
                    class="rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Tous les sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Liste des rapports --}}
        <x-card>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Résumé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site / Modèle</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($rapports as $r)
                            <tr class="hover:bg-white transition-colors group">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm
                                        {{ $r->score >= 7 ? 'bg-green-100 text-green-700' : ($r->score >= 4 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $r->score }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 cursor-pointer" @click="window.location.href='{{ route('plugins.ai-analytics.historique.show', $r->id) }}'">
                                    <p class="text-sm text-gray-900 line-clamp-2">{{ $r->resume }}</p>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <div class="font-medium text-gray-700">{{ $r->site->nom }}</div>
                                    <div>{{ $r->fournisseur }} · {{ $r->modele }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $r->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <button
                                        @click="confirmerSuppression({{ $r->id }})"
                                        class="text-gray-400 hover:text-red-600 transition"
                                    >
                                        <x-custom-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    Aucun rapport trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $rapports->links() }}
            </div>
        </x-card>
    </div>

    <script>
        function aiHistory() {
            return {
                siteId: '{{ request('site_id') }}',
                filter() {
                    const url = new URL(window.location.href);
                    if (this.siteId) url.searchParams.set('site_id', this.siteId);
                    else url.searchParams.delete('site_id');
                    window.location.href = url.toString();
                },
                async confirmerSuppression(id) {
                    if (!confirm('Voulez-vous vraiment supprimer ce rapport ?')) return;

                    try {
                        const r = await fetch(`/plugins/ai-analytics/historique/rapport/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        if (r.ok) window.location.reload();
                    } catch (e) {
                        alert('Erreur lors de la suppression');
                    }
                }
            }
        }
    </script>
</x-app-layout>
