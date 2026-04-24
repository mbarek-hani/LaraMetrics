<?php

namespace App\Core\Plugin;

class PluginAutoloader
{
    /**
     * @var array<string, string>
     */
    private array $prefixes = [];

    public function register(): void
    {
        spl_autoload_register([$this, 'autoload']);
    }

    public function addNamespace(string $prefix, string $baseDirectory): void
    {
        $prefix = trim($prefix, '\\').'\\';
        $baseDirectory = rtrim($baseDirectory, '/').'/';

        $this->prefixes[$prefix] = $baseDirectory;
    }

    public function autoload(string $class): void
    {
        foreach ($this->prefixes as $prefix => $baseDirectory) {
            if (! str_starts_with($class, $prefix)) {
                continue;
            }

            $relativeClass = substr($class, strlen($prefix));
            $file =
                $baseDirectory.
                str_replace('\\', '/', $relativeClass).
                '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}
