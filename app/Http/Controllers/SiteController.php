<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount("visites")->orderBy("nom")->get();
        return view("sites.index", compact("sites"));
    }

    public function create()
    {
        return view("sites.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "nom" => ["required", "string", "max:255"],
            "domaine" => [
                "required",
                "string",
                "max:255",
                "unique:sites,domaine",
            ],
        ]);

        Site::create($validated);

        return redirect()
            ->route("sites.index")
            ->with("succes", "Site ajouté avec succès.");
    }

    public function show(Site $site)
    {
        return view("sites.show", compact("site"));
    }

    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()
            ->route("sites.index")
            ->with("succes", "Site supprimé.");
    }
}
