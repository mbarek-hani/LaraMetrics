<?php

namespace Plugins\AiAnalytics\Models;

use App\Models\Site;
use Illuminate\Database\Eloquent\Model;

class AiAnalyticsReport extends Model
{
    protected $fillable = [
        'site_id',
        'score',
        'resume',
        'points_cles',
        'recommandations',
        'tendances',
        'fournisseur',
        'modele',
        'date_debut',
        'date_fin',
    ];

    /**
     * Cast des colonnes JSON en array
     */
    protected $casts = [
        'points_cles' => 'array',
        'recommandations' => 'array',
        'tendances' => 'array',
    ];

    /**
     * La relation manquante
     */
    public function site()
    {
        // On lie le rapport au modèle Site principal de l'application
        return $this->belongsTo(Site::class);
    }
}
