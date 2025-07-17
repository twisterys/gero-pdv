<?php

namespace App\Http\Controllers;

use App\Http\Requests\AffaireStoreRequest;
use App\Http\Requests\AffaireUpdateRequest;
use App\Models\Achat;
use App\Models\Affaire;
use App\Models\Client;
use App\Models\Commercial;
use App\Models\Depense;
use App\Models\Jalon;
use App\Models\Magasin;
use App\Models\Tag;
use App\Models\Taxe;
use App\Models\Unite;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\GlobalService;
use App\Services\LimiteService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;
use function Laravel\Prompts\progress;

class AffaireController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['affaire.liste']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        // ------------------- ### Ajax logic ### -------------------
        if ($request->ajax()) {
            $o_affaires = Affaire::query()->whereYear('date_debut', session()->get('exercice'))->with('client');
            // -------------------### Filters ###-------------------
            if ($request->get('client_id')) {
                $o_affaires->where('client_id', $request->get('client_id'));
            }
            if ($request->get('statut')) {
                $o_affaires->where('statut', $request->get('statut'));
            }
            if ($request->get('reference')) {
                $search = '%' . $request->get('reference') . '%';
                $o_affaires->where('reference', 'LIKE', $search);
            }

            // ------------------- ### End of filters ### -------------------
            $o_affaires = $o_affaires->get();
            $table = DataTables::of($o_affaires);
            // ------------------- ### Columns formatting ### -------------------
            $table->editColumn('client_id', function ($vents) {
                return $vents->client->nom;
            });
            $table->addColumn('actions', function ($row) {
                $viewAction = '<form method="get" action="' . route("affaire.afficher", ['id' => $row->id]) . '" class="d-inline">
                     <button type="submit" class="btn btn-sm btn-soft-info mx-1"><i class="fa fa-eye"></i></button>
                   </form>';

                $editAction = '<a href="' . route('affaire.modifier', ['id' => $row->id]) . '" class="btn btn-sm btn-soft-warning"
                      data-bs-template=\'<div class="tooltip mb-1 rounded "role="tooltip"><div class="tooltip-inner bg-warning font-size-10"></div></div>\'
                      data-bs-toggle="tooltip" data-bs-custom-class="bg-warning" data-bs-placement="top" data-bs-original-title="Modifier">
                      <i class="fa fa-edit"></i>
                   </a>';

                $deleteAction = '<button data-url="' . route('affaire.supprimer', ['id' => $row->id]) . '"
        class="btn btn-sm btn-soft-danger sa-warning mx-1" data-bs-custom-class="danger-tooltip"
        data-bs-template=\'<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>\'
        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Supprimer">
        <i class="fa fa-trash-alt"></i>
    </button>';

                $action = $viewAction . $editAction . $deleteAction;
                return $action;
            });


            $table->editColumn('budget_estimatif', function ($row) {
                if (!$row->budget_estimatif) {
                    return $row->budget_estimatif;
                }
                return $row->budget_estimatif . ' MAD';
            });
            $table->editColumn('ca_global', function ($row) {
                if (!$row->ca_global) {
                    return $row->ca_global;
                } else {
                    return $row->ca_global . ' MAD';
                }
            });
            $table->addColumn(
                'selectable_td',
                function ($contact) {
                    $id = $contact->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            );
            $table->rawColumns(['actions', 'selectable_td']);
            // ------------------- ### End of columns formatting ### -------------------
            return $table->make();
        }
        // ------------------- ### End of ajax logic ### -------------------
        $o_client = Client::get(['id', 'nom as text']);
        return view("affaires.liste", compact('o_client'));
    }

    public function supprimer(int $id)
    {

        $this->guard_custom(['affaire.supprimer']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        if (\request()->ajax()) {
            $affaire = Affaire::find($id);
            if ($affaire) {
                $affaire->jalons()->delete();
                $affaire->delete();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => Affaire::class,
                        'subject_id' => $id,
                        'subject_reference' => $affaire->reference,
                    ])
                    ->log('Suppression d\'affaire ');

                return response('Affaire supprimée avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }

    public function mettre_a_jour(AffaireUpdateRequest $request, $id)
    {
        $this->guard_custom(['affaire.modifier']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $affaire = Affaire::find($id);
        if (!$affaire) {
            abort(404);
        }
        $data = $request->validated();

        $data['date_debut'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date_debut'])->format('Y-m-d');
        $data['date_fin'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date_fin'])->format('Y-m-d');

        // Mise à jour de l'affaire
        $affaire->update([
            'client_id' => $data['client_id'],
            'reference' => $data['reference'],
            'titre' => $data['titre'] ?? null,
            'cycle_duree' => $data['cycle_duree'] ?? null,
            'cycle_type' => $data['cycle_type'] ?? null,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'budget_estimatif' => $data['budget_estimatif'] ?? null,
            'ca_global' => $data['ca_global'] ?? null,
            'description' => $data['description'] ?? null,
            'statut' => 'Créé'
        ]);
        $exist_lignes = [];
        if (isset($data['lignes']) && !empty($data['lignes'])) {
            foreach ($data['lignes'] as $key => $ligne) {
                if ($ligne['id'] != null) {
                    $o_ligne = Jalon::find($ligne['id']);
                } else {
                    $o_ligne = new Jalon();
                }
                $o_ligne->nom = $ligne['nom'];
                $o_ligne->date = \Carbon\Carbon::createFromFormat('d/m/Y', $ligne['jalon_date'])->format('Y-m-d');
                $o_ligne->affaire_id = $affaire->id;
                $o_ligne->save();
                $exist_lignes[] = $o_ligne->id;
            }
        }

        Jalon::whereNotIn('id', $exist_lignes)->where('affaire_id', $affaire->id)->delete();

        activity()
            ->causedBy(Auth::user())
            ->event('Modification')
            ->withProperties([
                'subject_type' => Affaire::class,
                'subject_id' => $id,
                'subject_reference' => $affaire->reference,
            ])
            ->log('Modification d\'affaire ');

        return redirect()->route('affaire.afficher', $id)->with('success', 'Affaire modifiée avec succès.');
    }

    public function jalon_modal($id)
    {
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $jalons = Jalon::where('affaire_id', $id)->get();
        $affaire = Affaire::where('id', $id)->with('jalons')->get();
        if (!$affaire) {
            return response(" Affaire n'exist pas !", 404);
        }

        return view('affaires.partials.jalons_modal', compact('jalons'));
    }

    public function afficher($id)
    {
        $this->guard_custom(['affaire.liste']);

        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $totalAv = Vente::query()
            ->where('affaire_id', $id)
            ->sum('total_ttc');
        $types = array_diff(ModuleService::getActiveModules(), Achat::TYPES);
        $payables = ModuleService::getPayabaleTypes();
        $modules = ModuleService::getActiveModules();
        $affaire = Affaire::where('id', $id)->with('jalons', 'client', 'ventes')->first();


        $gantt = $affaire->jalons->map(function (Jalon $jalon) use ($affaire, &$previousEnd) {
            $start = $previousEnd ?? Carbon::createFromFormat('d/m/Y', $affaire->date_debut)->toDateString();

            $end = Carbon::parse($jalon->date)->toDateString();

            // Update the previous end date for the next iteration
            $previousEnd = $end;

            // Generate a calm random color
            $color = $this->generateCalmColor();

            // Calculate progress
            $currentDate = Carbon::now();
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

            $progress = 0; // Default progress
            if ($startDate->eq($endDate)) {
                // If start and end are the same, progress is 100% if current date >= start
                $progress = $currentDate->gte($startDate) ? 100 : 0;
            } else {
                $totalDuration = $startDate->diffInSeconds($endDate);
                $elapsedDuration = $startDate->diffInSeconds($currentDate);

                $progress = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
            }

            return [
                'id' => $jalon->id,
                'name' => $jalon->nom,
                'start' => $start,
                'end' => $end,
                'color' => $color, // Add the calm random color
                'progress' => round($progress, 2), // Progress as a percentage
            ];
        });


        return view('affaires.afficher', compact('totalAv', 'affaire', 'modules', 'types', 'payables', 'gantt'));
    }

    public function ajouter()
    {
        $this->guard_custom(['affaire.ajouter']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        return view('affaires.ajouter');
    }

    public function modifier($id)
    {
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $affaire = Affaire::where('id', $id)->with('jalons', 'client')->first();
        return view('affaires.modifier', compact('affaire'));
    }

    public function ajouter_vente($id, $type)
    {
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $affaire = Affaire::where('id', $id)->with('jalons', 'client')->first();

        $data = [
            'created_by' => auth()->id(),
            'affaire_id' => $id,
            'client_id' => $affaire->client->id,
            "statut" => "brouillon",
            'date_document' => now()->toDateString(),
            'date_expiration' => now()->toDateString(),
            'date_emission' => now()->toDateString(),
            'type_document' => $type,
            'statut_paiement' => 'non_paye',
            'reference' => null,
        ];
        if (Magasin::count() == 1) {
            $data['magasin_id'] = Magasin::first()->id;
        }
        $o_vente = Vente::create($data);

        $vente_ligne = new VenteLigne();
        $vente_ligne->nom_article = $affaire->titre;
        $vente_ligne->quantite = 1;
        $vente_ligne->ht = 0;
        $vente_ligne->vente_id = $o_vente->id;
        $vente_ligne->unit_id = Unite::first()->id;
        $vente_ligne->save();

        activity()
            ->causedBy(Auth::user())
            ->event('Création')
            ->withProperties([
                'subject_type' => Vente::class,
                'subject_id' => $o_vente->id,
                'subject_reference' => $o_vente->reference,
            ])
            ->log('Création de ' . __('ventes.' . $type) . ' ' . $o_vente->reference . ' depuis l\'affaire' . ' ' . $affaire->reference);

        $o_commercils = Commercial::get(['id', 'nom', "commission_par_defaut"]);
        $o_unites = Unite::get(['id', "nom"]);
        $o_taxes = Taxe::where('active', '1')->get(["valeur", "nom"]);
        $globals = GlobalService::get_all_globals();
        $modifier_reference = $o_vente->reference && $globals->modifier_reference;
        $prix_revient = $globals->prix_revient;
        $o_magasins = \request()->user()->magasins()->get(['magasin_id as id', 'nom as text']);
        $payabale_types = ModuleService::getPayabaleTypes();
        $globals = GlobalService::get_all_globals();
        $prix_revient = $globals->prix_revient;
        return view('ventes.afficher', compact('o_vente', 'type', 'payabale_types', 'prix_revient'));

    }

    public function sauvegarder(AffaireStoreRequest $request)
    {
        $this->guard_custom(['affaire.ajouter']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $data = $request->validated();
        // Conversion des dates au format Y-m-d
        $data['date_debut'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date_debut'])->format('Y-m-d');
        $data['date_fin'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date_fin'])->format('Y-m-d');

        // Création de l'affaire
        $affaire = Affaire::create([
            'client_id' => $data['client_id'],
            'reference' => $data['i_reference'],
            'titre' => $data['titre'],
            'cycle_duree' => $data['cycle_duree'],
            'cycle_type' => $data['cycle_type'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'budget_estimatif' => $data['budget_estimatif'],
            'ca_global' => $data['ca_global'],
            'description' => $data['description'],
            'statut' => 'Créé'
        ]);

        if (key_exists('lignes', $data)) {
            if ($data['lignes']) {
                foreach ($data['lignes'] as $ligne) {
                    $affaire->jalons()->create([
                        'nom' => $ligne['nom'],
                        'date' => \Carbon\Carbon::createFromFormat('d/m/Y', $ligne['jalon_date'])->format('Y-m-d'),
                    ]);
                }
            }
        }


        // Création des jalons

        activity()
            ->causedBy(Auth::user())
            ->event('Création')
            ->withProperties([
                'subject_type' => Affaire::class,
                'subject_id' => $affaire->id,
                'subject_reference' => $affaire->reference,
            ])
            ->log('Création d\'affaire ' . $affaire->reference);

        return redirect()->route('affaire.liste')->with('success', 'Affaire créé avec succès.');

    }

    public function attachement_modal_search(Request $request, $id)
    {
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $this->guard_custom(['affaire.ajouter']);
        $affaire = Affaire::find($id);
        if ($request->get('type') === 'vente') {
            $data = Vente::where('client_id', $affaire->client_id)->where('reference', 'LIKE', '%' . $request->get('search') . '%')->where('statut', 'validé')->whereNull('affaire_id')->get(['id as id', 'reference as text']);
            return response()->json($data);
        }
        if ($request->get('type') === 'depense') {
            $data = Depense::where('reference', 'LIKE', '%' . $request->get('search') . '%')->whereNull('affaire_id')->get(['id as id', 'reference as text']);
            return response()->json($data);
        }
        abort(404);
    }


    public function attacher(Request $request, $id)
    {
        $this->guard_custom(['affaire.ajouter']);
        if (!LimiteService::is_enabled('affaires')) {
            abort(404);
        }
        $affaire = Affaire::find($id);
        if (!$affaire) {
            abort(404);
        }
        if ($request->get('type') === 'vente') {
            $o_vente = Vente::find($request->vente_id);
            if (!$o_vente) {
                return response('Vente introuvable', 404);
            }
            $o_vente->update(['affaire_id' => $id]);
            activity()
                ->causedBy(Auth::user())
                ->event('Attachement')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Attachement de ' . __('ventes.' . $o_vente->type_document) . ' ' . $o_vente->reference . ' avec l\'affaire' . ' ' . $affaire->reference);
        }
        if ($request->get('type') === 'depense') {
            $o_depense = Depense::find($request->depense_id);
            if (!$o_depense) {
                return response('Depense introuvable', 404);
            }
            $o_depense->update(['affaire_id' => $id]);
            activity()
                ->causedBy(Auth::user())
                ->event('Attachement')
                ->withProperties([
                    'subject_type' => Depense::class,
                    'subject_id' => $o_depense->id,
                    'subject_reference' => $o_depense->reference,
                ])
                ->log('Attachement de depense ' . $o_depense->reference . ' avec l\'affaire' . ' ' . $affaire->reference);
        }
        return response('Document attché', 200);
    }

    function generateCalmColor()
    {
        $red = rand(150, 220);   // Higher range for lighter colors
        $green = rand(150, 220); // Higher range for lighter colors
        $blue = rand(150, 220);  // Higher range for lighter colors

        return sprintf("#%02x%02x%02x", $red, $green, $blue);
    }
}

