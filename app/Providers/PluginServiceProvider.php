<?php

namespace App\Providers;

use App\Core\Plugin\PluginDiscovery;
use App\Core\Plugin\PluginManager;
use App\Core\Plugin\PluginAutoloader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginAutoloader::class, function () {
            $autoloader = new PluginAutoloader();
            $autoloader->register();

            return $autoloader;
        });

        $this->app->singleton(PluginDiscovery::class);

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager($app->make(PluginDiscovery::class));
        });
    }

    public function boot(): void
    {
        if ($this->doitIgnorer()) {
            $this->enregistrerDirectivesBlade();
            return;
        }

        $manager = $this->app->make(PluginManager::class);
        $manager->initialiser();

        $this->enregistrerDirectivesBlade();
    }

    /**
     * Enregistre les directives Blade communes (toujours actives).
     */
    private function enregistrerDirectivesBlade(): void
    {
        // @hook('dashboard.widgets')
        Blade::directive("hook", function (string $expression) {
            return "<?php echo implode('', app(\App\Core\Plugin\PluginManager::class)->executerHook({$expression})); ?>";
        });

        // @pluginActif('ai-analytics') ... @endpluginActif
        Blade::if("pluginActif", function (string $identifiant) {
            return app(PluginManager::class)->estActif($identifiant);
        });
    }

    /**
     * Détermine si l'initialisation des plugins doit être ignorée.
     * Cas ignorés : migrate, migrate:fresh, migrate:rollback, tests, etc.
     */
    private function doitIgnorer(): bool
    {
        if (!$this->app->runningInConsole()) {
            return false;
        }

        // Commandes artisan qui ne doivent PAS déclencher l'init des plugins
        $commandesIgnorees = [
            "migrate",
            "migrate:fresh",
            "migrate:rollback",
            "migrate:reset",
            "migrate:refresh",
            "migrate:status",
            "db:seed",
            "db:wipe",
            "schema:dump",
        ];

        // Récupère la commande artisan en cours d'exécution
        $argv = $_SERVER["argv"] ?? [];
        $commandeEnCours = $argv[1] ?? "";

        foreach ($commandesIgnorees as $commande) {
            if (str_starts_with($commandeEnCours, $commande)) {
                return true;
            }
        }

        return false;
    }
}
