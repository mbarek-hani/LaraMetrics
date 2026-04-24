<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'site_id',
        'visite_id',
        'session_id',
        'type',
        'nom',
        'donnees',
        'chemin',
        'cree_le',
    ];

    protected $casts = [
        'donnees' => 'array',
        'cree_le' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function visite(): BelongsTo
    {
        return $this->belongsTo(Visite::class, 'visite_id');
    }
}
