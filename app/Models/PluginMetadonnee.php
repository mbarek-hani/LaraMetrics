<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginMetadonnee extends Model
{
    public $timestamps = false;

    protected $fillable = ['visite_id', 'plugin', 'cle', 'valeur', 'cree_le'];

    protected $casts = [
        'cree_le' => 'datetime',
    ];

    public function visite(): BelongsTo
    {
        return $this->belongsTo(Visite::class, 'visite_id');
    }

    public static function enregistrer(
        int $visiteId,
        string $plugin,
        string $cle,
        mixed $valeur,
    ): self {
        return static::updateOrCreate(
            [
                'visite_id' => $visiteId,
                'plugin' => $plugin,
                'cle' => $cle,
            ],
            ['valeur' => is_array($valeur) ? json_encode($valeur) : $valeur],
        );
    }
}
