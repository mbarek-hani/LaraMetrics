<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TrackingController extends Controller
{
    public function __construct(private TrackingService $trackingService) {}

    public function track(Request $request): Response|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'size:64'],
            'type' => ['nullable', 'string', 'in:pageview,duree,evenement'],
            'url' => ['nullable', 'string', 'max:2048'],
            'chemin' => ['nullable', 'string', 'max:1024'],
            'titre' => ['nullable', 'string', 'max:255'],
            'referent' => ['nullable', 'string', 'max:2048'],
            'referent_domaine' => ['nullable', 'string', 'max:255'],
            'appareil' => [
                'nullable',
                'string',
                'in:ordinateur,mobile,tablette,inconnu',
            ],
            'largeur_ecran' => ['nullable', 'integer'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campagne' => ['nullable', 'string', 'max:100'],
            'duree' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'nom' => ['nullable', 'string', 'max:255'],
            'donnees' => ['nullable', 'array'],
            'est_navigation' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->reponseVide();
        }

        $donnees = $validator->validated();

        $site = Site::where('token_tracking', $donnees['token'])
            ->where('actif', true)
            ->first();

        if (! $site) {
            return $this->reponseVide();
        }

        $type = $donnees['type'] ?? 'pageview';

        match ($type) {
            'duree' => $this->trackingService->mettreAJourDuree(
                $site,
                $donnees,
                $request,
            ),
            'evenement' => $this->trackingService->enregistrerEvenement(
                $site,
                $donnees,
                $request,
            ),
            default => $this->trackingService->enregistrerVisite(
                $site,
                $donnees,
                $request,
            ),
        };

        return $this->reponseVide();
    }

    private function reponseVide(): Response
    {
        return response()
            ->noContent(204)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header(
                'Access-Control-Allow-Headers',
                'Content-Type, Accept, X-Requested-With',
            );
    }
}
