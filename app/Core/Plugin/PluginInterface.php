<?php

namespace App\Core\Plugin;

interface PluginInterface
{
    //Identité
    public function getIdentifiant(): string;
    public function getNom(): string;
    public function getVersion(): string;

    //Cycle de vie
    public function activer(): void;
    public function desactiver(): void;
    public function desinstaller(): void;
    public function enregistrer(): void;

    //UI : Onglets dashboard

    /**
     * Onglets ajoutés au dashboard.
     * @return array<int, array{id: string, label: string, icone: string}>
     */
    public function getOnglets(): array;

    //UI : Réglages

    /**
     * Champs de configuration du plugin.
     * @return array<int, array{cle: string, label: string, type: string}>
     */
    public function getReglages(): array;

    //UI : Pages et navigation

    /**
     * Liens ajoutés à la navigation principale.
     * @return array<int, array{label: string, route: string, icone: string}>
     */
    public function getNavigationItems(): array;

    //Hooks système

    /** @return string[] */
    public function getHooks(): array;

    //Tracking : enrichissement des données

    /**
     * Traite les données de tracking AVANT enregistrement.
     * Permet au plugin d'ajouter/modifier des données.
     * Retourne null pour ne rien modifier.
     *
     * @param array $donnees   Données brutes du tracker
     * @param \Illuminate\Http\Request $request
     * @return array|null      Données enrichies ou null
     */
    public function enrichirTracking(array $donnees, $request): ?array;

    /**
     * Appelé APRÈS l'enregistrement d'une visite.
     * Permet au plugin de stocker ses propres métadonnées.
     *
     * @param \App\Models\Visite $visite
     * @param array $donnees
     * @param \Illuminate\Http\Request $request
     */
    public function apresVisite($visite, array $donnees, $request): void;

    /**
     * Appelé APRÈS l'enregistrement d'un événement.
     *
     * @param \App\Models\Evenement $evenement
     * @param array $donnees
     * @param \Illuminate\Http\Request $request
     */
    public function apresEvenement($evenement, array $donnees, $request): void;

    //Tracking : données JS supplémentaires

    /**
     * Retourne du JavaScript à injecter dans tracker.js.
     * Permet au plugin de collecter des données côté client.
     * Retourne une chaîne JS ou null.
     */
    public function getTrackerJavaScript(): ?string;

    //Assets

    /**
     * Retourne le chemin vers un fichier CSS précompilé.
     * Sera inclus automatiquement dans le layout.
     */
    public function getCssPath(): ?string;

    //Manifest

    /** @return array<string, mixed> */
    public function getManifest(): array;
}
