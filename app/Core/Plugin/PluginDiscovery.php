<?php

namespace App\Core\Plugin;

use Illuminate\Support\Facades\Log;

/**
 * Scanne le dossier /plugins et retourne toutes les
 * instances de plugins valides trouvées.
 */
class PluginDiscovery
{
    /**
     * Chemin absolu vers le dossier des plugins.
     */
    private string $dossierPlugins;

    public function __construct()
    {
        $this->dossierPlugins = base_path("plugins");
    }

    /**
     * Scanne et retourne tous les plugins découverts.
     *
     * @return PluginInterface[]
     */
    public function decouvrir(): array
    {
        $plugins = [];

        if (!is_dir($this->dossierPlugins)) {
            Log::warning("Le dossier /plugins n'existe pas.");

            return $plugins;
        }

        $dossiers = glob($this->dossierPlugins . "/*", GLOB_ONLYDIR);

        foreach ($dossiers as $dossier) {
            $plugin = $this->chargerPlugin($dossier);

            if ($plugin !== null) {
                $plugins[$plugin->getIdentifiant()] = $plugin;
            }
        }

        return $plugins;
    }

    /**
     * Tente de charger un plugin depuis son dossier.
     * Retourne null si le plugin est invalide.
     */
    private function chargerPlugin(string $dossier): ?PluginInterface
    {
        $manifest = $this->lireManifest($dossier);
        if ($manifest === null) {
            return null;
        }

        $classePlugin = $manifest["classe"] ?? null;
        if (!$classePlugin) {
            Log::error(
                "Plugin dans [{$dossier}] : clé 'classe' manquante dans manifest.json",
            );

            return null;
        }

        $fichierClasse =
            $dossier . "/src/" . class_basename($classePlugin) . ".php";
        if (file_exists($fichierClasse)) {
            require_once $fichierClasse;
        }

        if (!class_exists($classePlugin)) {
            Log::error("Plugin : la classe [{$classePlugin}] est introuvable.");

            return null;
        }

        if (!is_subclass_of($classePlugin, PluginInterface::class)) {
            Log::error(
                "Plugin [{$classePlugin}] n'implémente pas PluginInterface.",
            );

            return null;
        }

        return app($classePlugin);
    }

    /**
     * Lit et décode le manifest.json d'un plugin.
     *
     * @return array<string, mixed>|null
     */
    private function lireManifest(string $dossier): ?array
    {
        $cheminManifest = $dossier . "/manifest.json";

        if (!file_exists($cheminManifest)) {
            return null;
        }

        $contenu = file_get_contents($cheminManifest);
        $manifest = json_decode($contenu, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error(
                "manifest.json invalide dans [{$dossier}] : " .
                    json_last_error_msg(),
            );

            return null;
        }

        return $manifest;
    }
}
