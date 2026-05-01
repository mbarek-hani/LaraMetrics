<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RemovePlugin extends Command
{
    protected $signature = 'remove:plugin {name : Le nom du dossier du plugin}';

    protected $description = 'Supprime un plugin et ses données associées';

    public function handle()
    {
        $name = $this->argument('name');
        $path = base_path("plugins/{$name}");

        if (! File::exists($path)) {
            $this->error("Le plugin {$name} n'existe pas.");

            return;
        }

        if ($this->confirm("Voulez-vous vraiment supprimer le plugin {$name} et ses fichiers ?")) {

            $manifest = json_decode(File::get($path.'/manifest.json'), true);
            $id = $manifest['identifiant'] ?? null;

            if ($id) {
                DB::table('plugins')->where('identifiant', $id)->delete();
                $this->comment("Entrée supprimée de la table 'plugins'.");
            }

            File::deleteDirectory($path);

            $this->info("Plugin {$name} supprimé avec succès.");
        }
    }
}
