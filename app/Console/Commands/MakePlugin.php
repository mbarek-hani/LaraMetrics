<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePlugin extends Command
{
    protected $signature = 'make:plugin {name : The name of the plugin in PascalCase}';

    protected $description = 'Create a new LaraMetrics plugin structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly(
            $this->argument('name'),
        );
        $path = base_path("plugins/{$name}");

        if (File::exists($path)) {
            $this->error("The plugin {$name} already exists");

            return;
        }

        $direcories = [
            $path.'/src',
            $path.'/routes',
            $path.'/resources/views',
            $path.'/database/migrations',
        ];

        foreach ($direcories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        $identifiant = Str::kebab($name);
        File::put(
            $path.'/manifest.json',
            json_encode(
                [
                    'identifiant' => $identifiant,
                    'nom' => $name,
                    'version' => '1.0.0',
                    'classe' => "Plugins\\{$name}\\{$name}",
                    'description' => "Description pour {$name}",
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
            ),
        );

        $stub = $this->getPluginStub($name, $identifiant);
        File::put($path."/src/{$name}.php", $stub);

        File::put(
            $path.'/routes/web.php',
            "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Routes pour {$name}",
        );

        $this->info("Plugin {$name} created!");
    }

    protected function getPluginStub(string $name, string $id)
    {
        return "<?php\n\nnamespace Plugins\\{$name};\n\nuse App\Core\Plugin\AbstractPlugin;\n\nclass {$name} extends AbstractPlugin {\n    //\n}\n\n";
    }
}
