<?php

namespace Plugins\AiAnalytics;

use App\Core\Plugin\AbstractPlugin;

class AiAnalyticsPlugin extends AbstractPlugin
{
    public function getIdentifiant(): string
    {
        return "ai-analytics";
    }

    public function getNom(): string
    {
        return "Analyse IA";
    }

    public function getVersion(): string
    {
        return "1.0.0";
    }

    public function getOnglets(): array
    {
        return [
            [
                "id" => "ai",
                "label" => "Analyse IA",
                "icone" => "cpu",
            ],
        ];
    }

    public function getHooks(): array
    {
        return ["tab.ai"];
    }

    public function rendrePourHook(string $hook, array $donnees = []): string
    {
        return match ($hook) {
            "tab.ai" => view("ai-analytics::tab", $donnees)->render(),
            default => "",
        };
    }

    public function getReglages(): array
    {
        return [
            [
                "cle" => "fournisseur",
                "label" => "Fournisseur IA",
                "type" => "select",
                "options" => ["groq", "openai"],
                "obligatoire" => true,
                "aide" => "Choisissez votre fournisseur de modèle IA.",
            ],
            [
                "cle" => "cle_api",
                "label" => "Clé API",
                "type" => "password",
                "obligatoire" => true,
                "placeholder" => "sk-...",
                "aide" =>
                    "Votre clé API. Elle est stockée de manière sécurisée.",
            ],
            [
                "cle" => "modele",
                "label" => "Modèle",
                "type" => "text",
                "obligatoire" => false,
                "placeholder" => "llama-3.3-70b-versatile",
                "aide" =>
                    "Laisser vide pour utiliser le modèle par défaut du fournisseur.",
            ],
        ];
    }

    /**
     * Ajoute le lien dans la barre latérale du tableau de bord.
     */
    public function getNavigationItems(): array
    {
        return [
            [
                "label" => "Analyse IA",
                "icone" => "cpu",
                "sous_menus" => [
                    [
                        "label" => "Historique des rapports",
                        "route" => "plugins.ai-analytics.historique.index",
                    ],
                ],
            ],
        ];
    }

    public function enregistrer(): void
    {
        parent::enregistrer();
    }
}
