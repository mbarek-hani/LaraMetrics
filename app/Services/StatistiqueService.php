<?php

namespace App\Services;

use App\Models\Evenement;
use App\Models\Site;
use App\Models\Visite;
use Illuminate\Support\Facades\DB;

class StatistiqueService
{
    public function __construct(private Site $site) {}

    /**
     * Résumé global pour le dashboard.
     *
     * @return array<string, mixed>
     */
    public function resume(string $debut, string $fin): array
    {
        $base = Visite::where('site_id', $this->site->id)->whereBetween(
            'cree_le',
            [$debut, $fin],
        );

        $visiteurs = (clone $base)->distinct('session_id')->count('session_id');
        $pagesVues = (clone $base)->count();
        $rebonds = (clone $base)->where('est_rebond', true)->count();
        $dureeMoy = (clone $base)
            ->whereNotNull('duree_session')
            ->avg('duree_session');

        return [
            'visiteurs_uniques' => $visiteurs,
            'pages_vues' => $pagesVues,
            'nouvelles_sessions' => (clone $base)
                ->where('est_nouvelle_session', true)
                ->count(),
            'taux_rebond' => $visiteurs > 0 ? round(($rebonds / $visiteurs) * 100, 1) : 0,
            'duree_moyenne' => round($dureeMoy ?? 0),
        ];
    }

    /**
     * Top pages les plus visitées.
     */
    public function topPages(string $debut, string $fin, int $limite = 10)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->select(
                'chemin',
                DB::raw('COUNT(*) as vues'),
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy('chemin')
            ->orderByDesc('vues')
            ->limit($limite)
            ->get();
    }

    /**
     * Répartition par appareil.
     */
    public function parAppareil(string $debut, string $fin)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->select(
                'appareil',
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy('appareil')
            ->orderByDesc('visiteurs')
            ->get();
    }

    /**
     * Top pays.
     */
    public function topPays(string $debut, string $fin, int $limite = 10)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->whereNotNull('pays_code')
            ->select(
                'pays_code',
                'pays_nom',
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy('pays_code', 'pays_nom')
            ->orderByDesc('visiteurs')
            ->limit($limite)
            ->get();
    }

    /**
     * Évolution jour par jour.
     */
    public function evolutionParJour(string $debut, string $fin)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->select(
                DB::raw('DATE(cree_le) as date'),
                DB::raw('COUNT(*) as pages_vues'),
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy(DB::raw('DATE(cree_le)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Top référents.
     */
    public function topReferents(string $debut, string $fin, int $limite = 10)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->whereNotNull('referent_domaine')
            ->select(
                'referent_domaine',
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy('referent_domaine')
            ->orderByDesc('visiteurs')
            ->limit($limite)
            ->get();
    }

    /**
     * Liste des événements personnalisés regroupés par nom.
     */
    public function evenementsParNom(
        string $debut,
        string $fin,
        int $limite = 20,
    ) {
        return Evenement::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->select(
                'nom',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT session_id) as sessions'),
            )
            ->groupBy('nom')
            ->orderByDesc('total')
            ->limit($limite)
            ->get();
    }

    /**
     * Événements par jour pour le graphique.
     */
    public function evenementsParJour(string $debut, string $fin)
    {
        return Evenement::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->select(
                DB::raw('DATE(cree_le) as date'),
                DB::raw('COUNT(*) as total'),
            )
            ->groupBy(DB::raw('DATE(cree_le)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Derniers événements.
     */
    public function derniersEvenements(
        string $debut,
        string $fin,
        int $limite = 20,
    ) {
        return Evenement::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->orderByDesc('cree_le')
            ->limit($limite)
            ->get();
    }

    public function evenementsParPage($debut, $fin)
    {
        return $this->site
            ->evenements()
            ->whereBetween('cree_le', [$debut, $fin])
            ->selectRaw('chemin, count(*) as total')
            ->groupBy('chemin')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Top éléments pour une colonne donnée (ex: utm_source, navigateur).
     */
    public function topParam(string $debut, string $fin, string $column, int $limite = 10)
    {
        return Visite::where('site_id', $this->site->id)
            ->whereBetween('cree_le', [$debut, $fin])
            ->whereNotNull($column)
            ->select(
                $column,
                DB::raw('COUNT(DISTINCT session_id) as visiteurs'),
            )
            ->groupBy($column)
            ->orderByDesc('visiteurs')
            ->limit($limite)
            ->get();
    }
}
