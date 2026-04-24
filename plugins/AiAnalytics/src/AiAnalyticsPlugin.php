<?php

namespace Plugins\AiAnalytics;

use App\Core\Plugin\AbstractPlugin;

class AiAnalyticsPlugin extends AbstractPlugin
{
    public function getIdentifiant(): string
    {
        return 'ai-analytics';
    }

    public function getNom(): string
    {
        return 'Analyse par Intelligence Artificielle';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Déclare les hooks auxquels ce plugin s'abonne.
     */
    public function getHooks(): array
    {
        return ['dashboard.widgets'];
    }

    /**
     * Méthode appelée par le PluginManager lors de l'exécution d'un hook.
     * Retourne le HTML à injecter dans le dashboard.
     */
    public function rendrePourHook(string $hook, array $donnees = []): string
    {
        return match ($hook) {
            'dashboard.widgets' => view(
                'ai-analytics::widget',
                $donnees,
            )->render(),
            default => '',
        };
    }

    /**
     * Surcharge : enregistre aussi les composants Livewire du plugin.
     */
    public function enregistrer(): void
    {
        parent::enregistrer(); // Routes + vues
    }
}
