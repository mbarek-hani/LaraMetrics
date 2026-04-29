<?php

namespace Plugins\AiAnalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Plugin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\AiAnalytics\Services\AiService;

class AiReportController extends Controller
{
    /**
     * Génère un nouveau rapport avec dates personnalisées.
     */
    public function generer(Request $request): JsonResponse
    {
        $request->validate([
            'site_id'    => ['required', 'exists:sites,id'],
            'date_debut' => ['required', 'date', 'before_or_equal:today', 'before_or_equal:date_fin'],
            'date_fin'   => ['required', 'date', 'before_or_equal:today'],
        ]);

        $config = Plugin::where('identifiant', 'ai-analytics')->value('configuration') ?? [];

        if (empty($config['cle_api'])) {
            return response()->json([
                'erreur' => 'Clé API non configurée. Rendez-vous dans Réglages.',
            ], 422);
        }

        $site  = Site::findOrFail($request->site_id);
        $debut = Carbon::parse($request->date_debut)->startOfDay();
        $fin   = Carbon::parse($request->date_fin)->endOfDay();

        try {
            $service = new AiService();
            $rapport = $service->genererRapportPeriode($site, $debut, $fin);

            $id = DB::table('ai_analytics_reports')->insertGetId([
                'site_id'         => $site->id,
                'score'           => $rapport['score'],
                'resume'          => $rapport['resume'],
                'points_cles'     => json_encode($rapport['points_cles']),
                'recommandations' => json_encode($rapport['recommandations']),
                'tendances'       => json_encode($rapport['tendances']),
                'fournisseur'     => $rapport['fournisseur'],
                'modele'          => $rapport['modele'],
                'date_debut'      => $debut->toDateString(),
                'date_fin'        => $fin->toDateString(),
                'cree_le'         => now(),
            ]);

            return response()->json([
                'succes'  => true,
                'rapport' => array_merge($rapport, ['id' => $id]),
            ]);

        } catch (\Exception $e) {
            return response()->json(['erreur' => $e->getMessage()], 422);
        }
    }

    /**
     * Dernier rapport d'un site.
     */
    public function dernier(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        $rapport = DB::table('ai_analytics_reports')
            ->where('site_id', $request->site_id)
            ->latest('cree_le')
            ->first();

        return response()->json([
            'rapport' => $rapport ? $this->formaterRapport($rapport) : null,
        ]);
    }

    /**
     * Un rapport spécifique par ID.
     */
    public function show(int $id): JsonResponse
    {
        $rapport = DB::table('ai_analytics_reports')->find($id);

        if (!$rapport) {
            return response()->json(['rapport' => null], 404);
        }

        return response()->json([
            'rapport' => $this->formaterRapport($rapport),
        ]);
    }

    /**
     * Historique des rapports d'un site.
     */
    public function historique(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        $rapports = DB::table('ai_analytics_reports')
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
                'genere_le'   => Carbon::parse($r->cree_le)->format('d/m/Y à H:i'),
            ]);

        return response()->json(['rapports' => $rapports]);
    }

    /**
     * Formate un rapport complet.
     */
    private function formaterRapport(object $rapport): array
    {
        return [
            'id'              => $rapport->id,
            'score'           => $rapport->score,
            'resume'          => $rapport->resume,
            'points_cles'     => json_decode($rapport->points_cles, true) ?? [],
            'recommandations' => json_decode($rapport->recommandations, true) ?? [],
            'tendances'       => json_decode($rapport->tendances, true) ?? [],
            'fournisseur'     => $rapport->fournisseur,
            'modele'          => $rapport->modele,
            'genere_le'       => Carbon::parse($rapport->cree_le)->format('d/m/Y à H:i'),
        ];
    }
}
