<?php

namespace App\Http\Controllers;

use App\Core\Plugin\PluginManager;
use App\Models\Site;
use App\Services\StatistiqueService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $sites = Site::actifs()->orderBy("nom")->get();
        $siteCourant = Site::latest()->first();
        $manager = app(PluginManager::class);

        if (!$siteCourant) {
            return view("dashboard", [
                "aucunSite" => true,
            ]);
        }

        return view("dashboard", [
            "sites" => $sites,
            "siteCourant" => $siteCourant,
            "aucunSite" => false,
            "onglets" => $manager->getOnglets(),
            "reglages" => $manager->getTousLesReglages(),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $request->validate([
            "site_id" => ["required", "exists:sites,id"],
            "periode" => [
                "required",
                "in:aujourdhui,7j,30j,ce_mois,cette_annee",
            ],
        ]);

        $site = Site::findOrFail($request->site_id);
        [$debut, $fin] = $this->calculerPeriode($request->periode);

        $service = new StatistiqueService($site);

        return response()->json([
            "resume" => $service->resume($debut, $fin),
            "evolution" => $service->evolutionParJour($debut, $fin),
            "top_pages" => $service->topPages($debut, $fin),
            "top_referents" => $service->topReferents($debut, $fin),
            "top_pays" => $service->topPays($debut, $fin),
            "appareils" => $service->parAppareil($debut, $fin),
            "evenements_par_nom" => $service->evenementsParNom($debut, $fin),
            "evenements_par_jour" => $service->evenementsParJour($debut, $fin),
            "derniers_evenements" => $service->derniersEvenements($debut, $fin),
            "periode" => [
                "debut" => $debut->format("d/m/Y"),
                "fin" => $fin->format("d/m/Y"),
            ],
        ]);
    }

    private function calculerPeriode(string $periode): array
    {
        return match ($periode) {
            "aujourdhui" => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ],
            "7j" => [Carbon::now()->subDays(7), Carbon::now()],
            "30j" => [Carbon::now()->subDays(30), Carbon::now()],
            "ce_mois" => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            "cette_annee" => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::now()->subDays(7), Carbon::now()],
        };
    }
}
