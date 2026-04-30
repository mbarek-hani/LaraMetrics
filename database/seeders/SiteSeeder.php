<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Site::create([
            'nom' => 'Mon Blog',
            'domaine' => 'monblog.com',
            'token_tracking' => Str::random(64),
            'actif' => true,
            'ignorer_bots' => true,
            'ignorer_dnt' => false,
        ]);

        Site::create([
            'nom' => 'Ma Boutique',
            'domaine' => 'maboutique.com',
            'token_tracking' => Str::random(64),
            'actif' => true,
            'ignorer_bots' => true,
            'ignorer_dnt' => false,
        ]);

        Site::create([
            'nom' => 'Portfolio',
            'domaine' => 'portfolio.dev',
            'token_tracking' => Str::random(64),
            'actif' => false,
            'ignorer_bots' => false,
            'ignorer_dnt' => true,
        ]);
    }
}
