<?php

namespace Plugins\AiAnalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Plugins\AiAnalytics\Services\AiService;
use Plugins\AiAnalytics\Models\AiAnalyticsReport;

class AiReportController extends Controller
{
    /**
     * Génère un nouveau rapport avec dates personnalisées.
     */
    public function generer(Request $request): JsonResponse
    {
        $request->validate([
            "site_id" => ["required", "exists:sites,id"],
            "date_debut" => [
                "required",
                "date",
                "before_or_equal:today",
                "before_or_equal:date_fin",
            ],
            "date_fin" => ["required", "date", "before_or_equal:today"],
        ]);

        $config =
            Plugin::where("identifiant", "ai-analytics")->value(
                "configuration",
            ) ?? [];

        if (empty($config["cle_api"])) {
            return response()->json(
                [
                    "erreur" =>
                        "Clé API non configurée. Rendez-vous dans Réglages.",
                ],
                422,
            );
        }

        $site = Site::findOrFail($request->site_id);
        $debut = Carbon::parse($request->date_debut)->startOfDay();
        $fin = Carbon::parse($request->date_fin)->endOfDay();

        try {
            $service = new AiService();
            $rapport = $service->genererRapportPeriode($site, $debut, $fin);

            $rapport_cree = AiAnalyticsReport::create([
                "site_id" => $site->id,
                "score" => $rapport["score"],
                "resume" => $rapport["resume"],
                "points_cles" => $rapport["points_cles"],
                "recommandations" => $rapport["recommandations"],
                "tendances" => $rapport["tendances"],
                "fournisseur" => $rapport["fournisseur"],
                "modele" => $rapport["modele"],
                "date_debut" => $debut->toDateString(),
                "date_fin" => $fin->toDateString(),
            ]);

            return response()->json([
                "succes" => true,
                "rapport" => array_merge($rapport, ["id" => $rapport_cree->id]),
            ]);
        } catch (\Exception $e) {
            return response()->json(["erreur" => $e->getMessage()], 422);
        }
    }

    /**
     * Dernier rapport d'un site.
     */
    public function dernier(Request $request): JsonResponse
    {
        $request->validate([
            "site_id" => ["required", "exists:sites,id"],
        ]);

        $rapport = AiAnalyticsReport::where("site_id", $request->site_id)
            ->latest()
            ->first();

        return response()->json([
            "rapport" => $rapport ? $this->formaterRapport($rapport) : null,
        ]);
    }

    /**
     * Formate un rapport complet.
     */
    private function formaterRapport(object $rapport): array
    {
        return [
            "id" => $rapport->id,
            "score" => $rapport->score,
            "resume" => $rapport->resume,
            "points_cles" => $rapport->points_cles ?? [],
            "recommandations" => $rapport->recommandations ?? [],
            "tendances" => $rapport->tendances ?? [],
            "fournisseur" => $rapport->fournisseur,
            "modele" => $rapport->modele,
            "genere_le" => Carbon::parse($rapport->created_at)->format(
                "d/m/Y à H:i",
            ),
        ];
    }

    public function index(Request $request)
    {
        $query = AiAnalyticsReport::with("site")->latest();

        if ($request->filled("site_id")) {
            $query->where("site_id", $request->site_id);
        }

        return view("ai-analytics::index", [
            "rapports" => $query->paginate(15),
            "sites" => Site::all(),
        ]);
    }

    public function show(AiAnalyticsReport $rapport)
    {
        return view("ai-analytics::show", compact("rapport"));
    }

    public function destroy(AiAnalyticsReport $rapport)
    {
        $rapport->delete();
        return response()->json(["success" => true]);
    }
}
