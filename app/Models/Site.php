<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Site extends Model
{
    protected $fillable = [
        'nom',
        'domaine',
        'token_tracking',
        'actif',
        'ignorer_bots',
        'ignorer_dnt',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ignorer_bots' => 'boolean',
        'ignorer_dnt' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Site $site) {
            if (empty($site->token_tracking)) {
                $site->token_tracking = hash('sha256', Str::random(40));
            }
        });
    }

    public function visites(): HasMany
    {
        return $this->hasMany(Visite::class, 'site_id');
    }

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class, 'site_id');
    }

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function getScriptTracking(): string
    {
        $url = config('app.url');
        $token = $this->token_tracking;

        return <<<HTML
        <script defer src="{$url}/tracker.js" data-token="{$token}"></script>
        HTML;
    }
}
