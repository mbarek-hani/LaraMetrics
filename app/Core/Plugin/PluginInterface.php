<?php

namespace App\Core\Plugin;

/**
 * Contrat que TOUS les plugins doivent respecter.
 */
interface PluginInterface
{
    /**
     * Retourne l'identifiant unique du plugin (slug).
     * Exemple : "ai-analytics"
     */
    public function getIdentifiant(): string;

    /**
     * Retourne le nom lisible du plugin.
     * Exemple : "Analyse par Intelligence Artificielle"
     */
    public function getNom(): string;

    /**
     * Retourne la version du plugin.
     * Exemple : "1.2.0"
     */
    public function getVersion(): string;

    /**
     * Méthode appelée lors de l'ACTIVATION du plugin.
     * C'est ici qu'on lance les migrations, seeds, etc.
     */
    public function activer(): void;

    /**
     * Méthode appelée lors de la DÉSACTIVATION du plugin.
     * Nettoyage des listeners, caches, etc.
     */
    public function desactiver(): void;

    /**
     * Méthode appelée lors de la DÉSINSTALLATION.
     * Suppression des tables, fichiers, etc.
     */
    public function desinstaller(): void;

    /**
     * Enregistre les services, routes, vues du plugin
     * dans le conteneur Laravel.
     */
    public function enregistrer(): void;

    /**
     * Retourne la liste des hooks que ce plugin utilise.
     * Exemple : ['dashboard.widgets', 'nav.menu']
     *
     * @return string[]
     */
    public function getHooks(): array;
}
