<?php

namespace Plugins\AiAnalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiReport;
use App\Models\Plugin;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Plugins\AiAnalytics\Services\AiService;

class AiReportController extends Controller
{
    /**
     * Génère un nouveau rapport IA pour le site sélectionné.
     */
    public function generer(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        // Vérifier que la clé API est configurée
        $config = Plugin::where('identifiant', 'ai-analytics')->value('configuration') ?? [];

        if (empty($config['cle_api'])) {
            return response()->json([
                'erreur' => 'Clé API non configurée. Rendez-vous dans l\'onglet Réglages.',
            ], 422);
        }

        $site = Site::findOrFail($request->site_id);

        try {
            $service = new AiService();
            $rapport = $service->genererRapport($site);

            // Sauvegarder le rapport en base
            \DB::table('ai_analytics_reports')->insert([
                'site_id'         => $site->id,
                'score'           => $rapport['score'],
                'resume'          => $rapport['resume'],
                'points_cles'     => json_encode($rapport['points_cles']),
                'recommandations' => json_encode($rapport['recommandations']),
                'tendances'       => json_encode($rapport['tendances']),
                'fournisseur'     => $rapport['fournisseur'],
                'modele'          => $rapport['modele'],
                'cree_le'         => now(),
            ]);

            return response()->json([
                'succes'  => true,
                'rapport' => $rapport,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'erreur' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Retourne le dernier rapport généré pour un site.
     */
    public function dernier(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        $rapport = \DB::table('ai_analytics_reports')
            ->where('site_id', $request->site_id)
            ->latest('cree_le')
            ->first();

        if (!$rapport) {
            return response()->json([
                'rapport' => null,
            ]);
        }

        return response()->json([
            'rapport' => [
                'score'           => $rapport->score,
                'resume'          => $rapport->resume,
                'points_cles'     => json_decode($rapport->points_cles, true),
                'recommandations' => json_decode($rapport->recommandations, true),
                'tendances'       => json_decode($rapport->tendances, true),
                'fournisseur'     => $rapport->fournisseur,
                'modele'          => $rapport->modele,
                'genere_le'       => \Carbon\Carbon::parse($rapport->cree_le)
                                        ->format('d/m/Y à H:i'),
            ],
        ]);
    }

    /**
     * Retourne l'historique des rapports pour un site.
     */
    public function historique(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        $rapports = \DB::table('ai_analytics_reports')
            ->where('site_id', $request->site_id)
            ->orderByDesc('cree_le')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'score'       => $r->score,
                'resume'      => $r->resume,
                'fournisseur' => $r->fournisseur,
                'modele'      => $r->modele,
                'genere_le'   => \Carbon\Carbon::parse($r->cree_le)->format('d/m/Y à H:i'),
            ]);

        return response()->json(['rapports' => $rapports]);
    }
}
