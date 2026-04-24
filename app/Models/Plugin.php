<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArray;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'identifiant',
        'nom',
        'version',
        'description',
        'auteur',
        'actif',
        'installe',
        'configuration',
        'metadonnees',
        'installe_le',
        'active_le',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'installe' => 'boolean',
        'configuration' => AsArray::class,
        'metadonnees' => AsArray::class,
        'installe_le' => 'datetime',
        'active_le' => 'datetime',
    ];

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function scopeInstalles($query)
    {
        return $query->where('installe', true);
    }
}
