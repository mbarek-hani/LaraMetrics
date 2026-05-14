<x-app-layout>
    <x-slot name="titre">
        Historique des analyses IA
    </x-slot>
    <div class="p-page">
        <div x-data="aiHistory()" class="p-ai p-container p-container--lg">
            {{-- En-tête & Filtre --}}
            <div class="p-ai__header u-mb-3">
                <div>
                    <h2 class="p-page__title u-mb-0">Analyses IA</h2>
                    <p class="p-text">Consultez et gérez l'historique de vos rapports intelligents.</p>
                </div>

                <div class="p-row">
                    <select
                        x-model="siteId"
                        @change="filter()"
                        class="c-input"
                        style="width: auto;"
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
                <div style="overflow-x: auto;">
                    <table class="p-ai__table">
                        <thead>
                            <tr>
                                <th>Score</th>
                                <th>Résumé</th>
                                <th>Site / Modèle</th>
                                <th>Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rapports as $r)
                                <tr @click="window.location.href='{{ route('plugins.ai-analytics.historique.show', $r->id) }}'" style="cursor: pointer;">
                                    <td>
                                        <span class="p-ai__score-circle {{ $r->score >= 7 ? 'p-ai__score--good' : ($r->score >= 4 ? 'p-ai__score--warning' : 'p-ai__score--danger') }}">
                                            {{ $r->score }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="p-text" style="color: var(--gray-900); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $r->resume }}</p>
                                    </td>
                                    <td>
                                        <div class="p-text--bold" style="font-size: 0.75rem;">{{ $r->site->nom }}</div>
                                        <div class="p-text--xs">{{ $r->fournisseur }} · {{ $r->modele }}</div>
                                    </td>
                                    <td>
                                        <div class="p-text--xs">
                                            {{ $r->created_at?->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button
                                            @click.stop="confirmerSuppression({{ $r->id }})"
                                            style="color: var(--gray-400); background: none; border: none; cursor: pointer; transition: color 0.2s;"
                                            onmouseover="this.style.color='var(--error-accent)'"
                                            onmouseout="this.style.color='var(--gray-400)'"
                                            title="Supprimer"
                                        >
                                            <x-custom-icon name="trash" class="c-icon--sm" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--gray-500);">
                                        Aucun rapport trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-mt-2">
                    {{ $rapports->links() }}
                </div>
            </x-card>
        </div>
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
