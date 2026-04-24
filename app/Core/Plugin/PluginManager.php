<?php

namespace App\Core\Plugin;

use App\Models\Plugin as PluginModele;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Gestionnaire central de tous les plugins.
 * Enregistré comme Singleton dans le conteneur Laravel.
 */
class PluginManager
{
    /**
     * Plugins découverts sur le disque.
     *
     * @var array<string, PluginInterface>
     */
    private array $pluginsDecouverts = [];

    /**
     * Plugins actuellement actifs (chargés en mémoire).
     *
     * @var array<string, PluginInterface>
     */
    private array $pluginsActifs = [];

    /**
     * Registry des hooks et leurs callbacks associés.
     *
     * @var array<string, callable[]>
     */
    private array $hooks = [];

    public function __construct(private PluginDiscovery $discovery) {}

    /**
     * Point d'entrée principal : découvre et charge les plugins actifs.
     * Appelé au démarrage de l'application.
     */
    public function initialiser(): void
    {
        $this->pluginsDecouverts = $this->discovery->decouvrir();

        // Récupérer les plugins actifs en BDD (avec cache 5 min)
        $pluginsActifsEnBdd = Cache::remember('plugins_actifs', 300, function () {
            return PluginModele::where('actif', true)->pluck('identifiant')->toArray();
        });

        // Enregistrer et activer uniquement les plugins actifs
        foreach ($this->pluginsDecouverts as $identifiant => $plugin) {
            if (in_array($identifiant, $pluginsActifsEnBdd)) {
                $this->chargerPlugin($plugin);
            }
        }

        Log::info('PluginManager initialisé. Plugins actifs : '.implode(', ', array_keys($this->pluginsActifs)));
    }

    /**
     * Enregistre un plugin dans Laravel et le marque comme actif.
     */
    private function chargerPlugin(PluginInterface $plugin): void
    {
        try {
            // Enregistre routes, vues, services...
            $plugin->enregistrer();

            // Enregistre les hooks déclarés par le plugin
            $this->enregistrerHooks($plugin);

            $this->pluginsActifs[$plugin->getIdentifiant()] = $plugin;
        } catch (\Throwable $e) {
            Log::error("Impossible de charger le plugin [{$plugin->getIdentifiant()}] : ".$e->getMessage());
        }
    }

    /**
     * Enregistre les hooks d'un plugin.
     */
    private function enregistrerHooks(PluginInterface $plugin): void
    {
        foreach ($plugin->getHooks() as $hook) {
            if (! isset($this->hooks[$hook])) {
                $this->hooks[$hook] = [];
            }
            $this->hooks[$hook][] = $plugin;
        }
    }

    /**
     * ACTIVE un plugin : mise à jour BDD + appel activer().
     */
    public function activer(string $identifiant): bool
    {
        $plugin = $this->pluginsDecouverts[$identifiant] ?? null;

        if (! $plugin) {
            Log::error("Impossible d'activer : plugin [{$identifiant}] non trouvé.");

            return false;
        }

        try {
            $plugin->activer(); // Lance les migrations, etc.

            PluginModele::updateOrCreate(
                ['identifiant' => $identifiant],
                [
                    'nom' => $plugin->getNom(),
                    'version' => $plugin->getVersion(),
                    'actif' => true,
                    'installe' => true,
                    'active_le' => now(),
                    'installe_le' => now(),
                    'metadonnees' => $plugin->getManifest(),
                ]
            );

            Cache::forget('plugins_actifs');
            $this->chargerPlugin($plugin);

            return true;
        } catch (\Throwable $e) {
            Log::error("Erreur activation plugin [{$identifiant}] : ".$e->getMessage());

            return false;
        }
    }

    /**
     * DÉSACTIVE un plugin : mise à jour BDD + appel desactiver().
     */
    public function desactiver(string $identifiant): bool
    {
        $plugin = $this->pluginsActifs[$identifiant] ?? null;

        if (! $plugin) {
            return false;
        }

        try {
            $plugin->desactiver();

            PluginModele::where('identifiant', $identifiant)
                ->update(['actif' => false]);

            unset($this->pluginsActifs[$identifiant]);
            Cache::forget('plugins_actifs');

            return true;
        } catch (\Throwable $e) {
            Log::error("Erreur désactivation plugin [{$identifiant}] : ".$e->getMessage());

            return false;
        }
    }

    /**
     * Exécute tous les plugins enregistrés sur un hook donné.
     * Retourne un tableau de vues/HTML générés.
     *
     * @return string[]
     */
    public function executerHook(string $hook, array $donnees = []): array
    {
        $resultats = [];

        if (! isset($this->hooks[$hook])) {
            return $resultats;
        }

        foreach ($this->hooks[$hook] as $plugin) {
            if (method_exists($plugin, 'rendrePourHook')) {
                $resultats[] = $plugin->rendrePourHook($hook, $donnees);
            }
        }

        return $resultats;
    }

    // Accesseurs

    /**
     * @return array<string, PuginInterface>
     */
    public function getPluginsDecouverts(): array
    {
        return $this->pluginsDecouverts;
    }

    /**
     * @return array<string, PuginInterface>
     */
    public function getPluginsActifs(): array
    {
        return $this->pluginsActifs;
    }

    public function estActif(string $identifiant): bool
    {
        return isset($this->pluginsActifs[$identifiant]);
    }
}
