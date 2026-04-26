<?php

namespace App\Core\Plugin;

use Illuminate\Support\Facades\Log;

/**
 * Scanne le dossier /plugins et retourne toutes les
 * instances de plugins valides trouvées.
 */
class PluginDiscovery
{
    private string $dossierPlugins;

    public function __construct(private PluginAutoloader $autoloader)
    {
        $this->dossierPlugins = base_path('plugins');
    }

    /**
     * @return array<string, PluginInterface>
     */
    public function decouvrir(): array
    {
        $plugins = [];

        if (! is_dir($this->dossierPlugins)) {
            Log::warning(
                "Le dossier plugins n'existe pas : {$this->dossierPlugins}",
            );

            return $plugins;
        }

        $dossiers = glob($this->dossierPlugins.'/*', GLOB_ONLYDIR);
        if (! $dossiers) {
            Log::warning(
                "There was an error trying to match directories inside {$this->dossierPlugins}",
            );

            return $plugins;
        }

        foreach ($dossiers as $dossier) {
            $plugin = $this->chargerPlugin($dossier);

            if ($plugin !== null) {
                $plugins[$plugin->getIdentifiant()] = $plugin;
            }
        }

        return $plugins;
    }

    private function chargerPlugin(string $dossier): ?PluginInterface
    {
        $manifest = $this->lireManifest($dossier);

        if ($manifest === null) {
            return null;
        }

        $classePlugin = $manifest['classe'] ?? null;

        if (! $classePlugin) {
            Log::error("Clé 'classe' manquante dans {$dossier}/manifest.json");

            return null;
        }

        $namespaceRacine = $this->extraireNamespacePlugin($classePlugin);

        if ($namespaceRacine === null) {
            Log::error("Namespace plugin invalide : {$classePlugin}");

            return null;
        }

        $this->autoloader->addNamespace($namespaceRacine, $dossier.'/src');

        if (! class_exists($classePlugin)) {
            Log::error("Classe plugin introuvable : {$classePlugin}");

            return null;
        }

        if (! is_subclass_of($classePlugin, PluginInterface::class)) {
            Log::error(
                "La classe {$classePlugin} n'implémente pas PluginInterface",
            );

            return null;
        }

        return app($classePlugin);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lireManifest(string $dossier): ?array
    {
        $cheminManifest = $dossier.'/manifest.json';

        if (! file_exists($cheminManifest)) {
            return null;
        }

        $contenu = file_get_contents($cheminManifest);
        $manifest = json_decode($contenu, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error(
                "JSON invalide dans {$cheminManifest} : ".
                    json_last_error_msg(),
            );

            return null;
        }

        return $manifest;
    }

    private function extraireNamespacePlugin(string $classePlugin): ?string
    {
        $parties = explode('\\', trim($classePlugin, '\\'));

        if (count($parties) < 2 || $parties[0] !== 'Plugins') {
            return null;
        }

        return $parties[0].'\\'.$parties[1];
    }
}
