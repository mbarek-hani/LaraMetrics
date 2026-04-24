<?php

namespace App\Providers;

use App\Core\Plugin\PluginDiscovery;
use App\Core\Plugin\PluginManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginDiscovery::class);

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager($app->make(PluginDiscovery::class));
        });
    }

    public function boot(): void
    {
        $manager = $this->app->make(PluginManager::class);
        $manager->initialiser();

        // Utilisation : @hook('dashboard.widgets')
        Blade::directive('hook', function (string $expression) {
            return "<?php echo implode('', app(\App\Core\Plugin\PluginManager::class)->executerHook({$expression})); ?>";
        });

        // Utilisation : @pluginActif('ai-analytics') ... @endpluginActif
        Blade::if('pluginActif', function (string $identifiant) {
            return app(PluginManager::class)->estActif($identifiant);
        });
    }
}
