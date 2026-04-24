<?php

namespace Plugins\AiAnalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AiReportController extends Controller
{
    public function rapport(): JsonResponse
    {
        sleep(2);

        return response()->json([
            'rapport' => [
                'score' => 7,
                'resume' => 'Trafic stable avec une progression notable sur mobile.',
                'points_cles' => [
                    '1 243 visiteurs uniques cette semaine',
                    'Page /accueil la plus visitée (34%)',
                    'Trafic mobile en hausse de 12%',
                ],
                'recommandations' => [
                    'Optimiser les images pour améliorer le temps de chargement mobile',
                    'Ajouter du contenu sur les pages à fort taux de rebond',
                ],
                'genere_le' => now()->format('d/m/Y à H:i'),
            ],
        ]);
    }
}
