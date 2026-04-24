<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visite extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'site_id',
        'session_id',
        'url',
        'chemin',
        'titre',
        'referent',
        'referent_domaine',
        'utm_source',
        'utm_medium',
        'utm_campagne',
        'navigateur',
        'version_navigateur',
        'systeme_exploitation',
        'appareil',
        'pays_code',
        'pays_nom',
        'duree_session',
        'est_rebond',
        'est_nouvelle_session',
        'cree_le',
    ];

    protected $casts = [
        'est_rebond' => 'boolean',
        'est_nouvelle_session' => 'boolean',
        'cree_le' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class, 'visite_id');
    }

    public function metadonnees(): HasMany
    {
        return $this->hasMany(PluginMetadonnee::class, 'visite_id');
    }

    public function scopePeriode($query, string $debut, string $fin)
    {
        return $query->whereBetween('cree_le', [$debut, $fin]);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('cree_le', today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('cree_le', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    public function scopeCeMois($query)
    {
        return $query
            ->whereMonth('cree_le', now()->month)
            ->whereYear('cree_le', now()->year);
    }
}
