<?php

namespace Plugins\AiAnalytics\Services;

use App\Models\Plugin;
use App\Models\Site;
use App\Services\StatistiqueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $fournisseur;
    private string $cleApi;
    private string $modele;
    private int    $periodeJours;

    //Modèles par défaut

    private const MODELES_DEFAUT = [
        'groq'   => 'llama-3.3-70b-versatile',
        'openai' => 'gpt-4o-mini',
    ];

    // Endpoints API

    private const ENDPOINTS = [
        'groq'   => 'https://api.groq.com/openai/v1/chat/completions',
        'openai' => 'https://api.openai.com/v1/chat/completions',
    ];

    public function __construct()
    {
        $config = Plugin::where('identifiant', 'ai-analytics')
            ->value('configuration') ?? [];

        $this->fournisseur  = $config['fournisseur']     ?? 'groq';
        $this->cleApi       = $config['cle_api']         ?? '';
        $this->modele       = $config['modele']          ?? self::MODELES_DEFAUT[$this->fournisseur] ?? 'llama-3.3-70b-versatile';
        $this->periodeJours = (int) ($config['periode_analyse'] ?? 7);

        if (empty($this->modele)) {
            $this->modele = self::MODELES_DEFAUT[$this->fournisseur] ?? 'llama-3.3-70b-versatile';
        }
    }

    // Point d'entrée principal

    /**
     * Génère un rapport d'analyse complet pour un site.
     *
     * @return array{
     *     score: int,
     *     resume: string,
     *     points_cles: string[],
     *     recommandations: string[],
     *     tendances: string[],
     *     genere_le: string,
     *     fournisseur: string,
     *     modele: string,
     * }
     *
     * @throws \Exception
     */
    public function genererRapport(Site $site): array
    {
        $this->validerConfiguration();

        $donnees = $this->extraireDonnees($site);

        $prompt = $this->construirePrompt($site, $donnees);

        $reponse = $this->appellerApi($prompt);

        return $this->parserReponse($reponse);
    }

    // Validation

    /**
     * @throws \Exception
     */
    private function validerConfiguration(): void
    {
        if (empty($this->cleApi)) {
            throw new \Exception(
                'Clé API manquante. Configurez-la dans l\'onglet Réglages.'
            );
        }

        if (!in_array($this->fournisseur, ['groq', 'openai'])) {
            throw new \Exception(
                "Fournisseur « {$this->fournisseur} » non supporté."
            );
        }
    }

    // Extraction des données

    /**
     * Extrait les statistiques du site pour la période configurée.
     *
     * @return array<string, mixed>
     */
    private function extraireDonnees(Site $site): array
    {
        $debut   = Carbon::now()->subDays($this->periodeJours);
        $fin     = Carbon::now();
        $service = new StatistiqueService($site);

        $resume     = $service->resume($debut, $fin);
        $topPages   = $service->topPages($debut, $fin, 10);
        $appareils  = $service->parAppareil($debut, $fin);
        $referents  = $service->topReferents($debut, $fin, 5);
        $topPays    = $service->topPays($debut, $fin, 5);
        $evolution  = $service->evolutionParJour($debut, $fin);

        return [
            'periode'   => [
                'debut' => $debut->format('d/m/Y'),
                'fin'   => $fin->format('d/m/Y'),
                'jours' => $this->periodeJours,
            ],
            'resume'    => $resume,
            'top_pages' => $topPages->map(fn($p) => [
                'chemin'    => $p->chemin,
                'vues'      => $p->vues,
                'visiteurs' => $p->visiteurs,
            ])->toArray(),
            'appareils' => $appareils->map(fn($a) => [
                'appareil'  => $a->appareil,
                'visiteurs' => $a->visiteurs,
            ])->toArray(),
            'referents' => $referents->map(fn($r) => [
                'domaine'   => $r->referent_domaine,
                'visiteurs' => $r->visiteurs,
            ])->toArray(),
            'pays'      => $topPays->map(fn($p) => [
                'pays'      => $p->pays_nom ?? $p->pays_code,
                'visiteurs' => $p->visiteurs,
            ])->toArray(),
            'evolution' => $evolution->map(fn($e) => [
                'date'       => $e->date,
                'visiteurs'  => $e->visiteurs,
                'pages_vues' => $e->pages_vues,
            ])->toArray(),
        ];
    }

    // Prompt

    /**
     * Construit le prompt envoyé à l'API.
     */
    private function construirePrompt(Site $site, array $donnees): string
    {
        $json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
        Tu es un expert en analyse de trafic web.
        Analyse les statistiques suivantes du site "{$site->nom}" ({$site->domaine})
        pour la période du {$donnees['periode']['debut']} au {$donnees['periode']['fin']}.

        DONNÉES :
        {$json}

        Génère une analyse complète et actionnable en français.

        Réponds UNIQUEMENT avec un objet JSON valide respectant exactement cette structure :
        {
            "score": <entier entre 0 et 10>,
            "resume": "<résumé de 2 phrases maximum>",
            "points_cles": [
                "<point clé 1>",
                "<point clé 2>",
                "<point clé 3>"
            ],
            "recommandations": [
                "<recommandation concrète 1>",
                "<recommandation concrète 2>",
                "<recommandation concrète 3>"
            ],
            "tendances": [
                "<tendance observée 1>",
                "<tendance observée 2>"
            ]
        }

        Règles :
        - Le score reflète la qualité globale du trafic (engagement, volume, diversité)
        - Les points clés sont des observations factuelles basées sur les données
        - Les recommandations sont actionnables et spécifiques
        - Les tendances décrivent l'évolution du trafic sur la période
        - Tout doit être en français, professionnel et concis
        - Ne génère RIEN d'autre que le JSON
        PROMPT;
    }

    // Appel API

    /**
     * Appelle l'API du fournisseur IA.
     *
     * @throws \Exception
     */
    private function appellerApi(string $prompt): string
    {
        $endpoint = self::ENDPOINTS[$this->fournisseur];

        try {
            $reponse = Http::withToken($this->cleApi)
                ->timeout(30)
                ->retry(2, 1000)
                ->post($endpoint, [
                    'model'       => $this->modele,
                    'messages'    => [
                        [
                            'role'    => 'system',
                            'content' => 'Tu es un expert en analyse de trafic web. '
                                       . 'Tu réponds uniquement en JSON valide, sans markdown, '
                                       . 'sans blocs de code, uniquement le JSON brut.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens'  => 1000,
                ]);

            if ($reponse->failed()) {
                $erreur = $reponse->json('error.message') ?? $reponse->body();
                Log::error("AiService API error [{$this->fournisseur}] : " . $erreur);

                throw new \Exception(
                    "Erreur API {$this->fournisseur} : " . $erreur
                );
            }

            $contenu = $reponse->json('choices.0.message.content');

            if (empty($contenu)) {
                throw new \Exception('Réponse vide de l\'API.');
            }

            return $contenu;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception(
                "Impossible de joindre l'API {$this->fournisseur}. Vérifiez votre connexion."
            );
        }
    }

    // Parser la réponse

    /**
     * Parse la réponse JSON de l'API.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    private function parserReponse(string $contenu): array
    {
        // Nettoyer la réponse (supprimer les blocs markdown si présents)
        $contenu = trim($contenu);
        $contenu = preg_replace('/^```json\s*/i', '', $contenu);
        $contenu = preg_replace('/^```\s*/i', '', $contenu);
        $contenu = preg_replace('/\s*```$/i', '', $contenu);
        $contenu = trim($contenu);

        $donnees = json_decode($contenu, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('AiService : JSON invalide reçu : ' . $contenu);
            throw new \Exception(
                'La réponse de l\'IA n\'est pas un JSON valide. Réessayez.'
            );
        }

        return [
            'score'           => max(0, min(10, (int) ($donnees['score'] ?? 5))),
            'resume'          => $donnees['resume'] ?? 'Analyse indisponible.',
            'points_cles'     => array_slice((array) ($donnees['points_cles'] ?? []), 0, 5),
            'recommandations' => array_slice((array) ($donnees['recommandations'] ?? []), 0, 5),
            'tendances'       => array_slice((array) ($donnees['tendances'] ?? []), 0, 3),
            'genere_le'       => now()->format('d/m/Y à H:i'),
            'fournisseur'     => $this->fournisseur,
            'modele'          => $this->modele,
        ];
    }
}
