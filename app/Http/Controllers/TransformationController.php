<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use App\Models\TransformationLigne;
use App\Models\Unite;
use App\Models\Vente;
use App\Services\LimiteService;
use App\Services\LogService;
use App\Services\PermissionsService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransformationController extends Controller
{
    public function liste(Request $request)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.liste']);

        if ($request->ajax()) {
            $query = Transformation::with(['magasin', 'createdBy'])->get();

            $table = DataTables::of($query);

            $table->addColumn('actions', function ($row) use ($request) {
                $id = $row->id;

                $crudRoutePart = 'transformations';
                $delete = null;
                $show = null;

                if ($request->user()->can('transformation.supprimer')) {
                    $delete = 'supprimer';
                }

                if ($request->user()->can('transformation.afficher')) {
                    $show = 'afficher';
                }

                return view('partials.__datatable-action', compact('id', 'crudRoutePart', 'delete', 'show'));

            })->addColumn(
                'selectable_td',
                function ($contact) {
                    $id = $contact->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->editColumn('date', function ($row) {
                return Carbon::make($row->date)->format('d/m/Y');
            })->editColumn('magasin_id', function ($row) {
                return $row->magasin->reference ?? '--';
            })->rawColumns(['actions', 'selectable_td']);

            return $table->make(true);
        }
        return view('transformations.liste');
    }

    public function ajouter()
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.sauvegarder']);
        $o_magasins = \request()->user()->magasins()->get(['magasin_id as id', 'nom as text']);
        $o_unites = Unite::get(['id', "nom"]);

        return view('transformations.ajouter', compact('o_magasins', 'o_unites'));
    }

    public function sauvegarder(Request $request)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.sauvegarder']);
        \Validator::make($request->all(), [
            'date' => ['required', 'date_format:d/m/Y'],
            'objet' => ['nullable'],
            'i_note' => ['nullable'],
            'magasin_id' => ['nullable', 'exists:magasins,id'],
            'lignes_sortant' => 'required|array',
            'lignes_sortant.*.i_unite' => 'required|exists:unites,id',
            'lignes_sortant.*.i_quantite' => 'required|numeric',
            'lignes_sortant.*.i_article_id' => 'nullable|exists:articles,id',
            'lignes_sortant.*.i_article' => 'required|string|max:255',

            'lignes_entrant' => 'required|array',
            'lignes_entrant.*.i_unite' => 'required|exists:unites,id',
            'lignes_entrant.*.i_quantite' => 'required|numeric',
            'lignes_entrant.*.i_article_id' => 'nullable|exists:articles,id',
            'lignes_entrant.*.i_article' => 'required|string|max:255',
        ], [], [
            'date' => 'date',
            'lignes_sortant' => 'lignes sortant',
            'lignes_entrant' => 'lignes entrant',
            'objet' => 'objet',
            'i_note' => 'note',
            'magasin_id' => 'magasin',
            'lignes_sortant.*.i_unite' => 'unite',
            'lignes_sortant.*.i_quantite' => 'quantite',
            'lignes_sortant.*.i_article_id' => 'article',
            'lignes_sortant.*.i_article' => 'article',
            'lignes_entrant.*.i_unite' => 'unite',
            'lignes_entrant.*.i_quantite' => 'quantite',
            'lignes_entrant.*.i_article_id' => 'article',
            'lignes_entrant.*.i_article' => 'article',
        ])->validate();
        \DB::beginTransaction();

        try {
            // Create new transformation
            $o_transformation = Transformation::create([
                'reference' => 'TR-' . date('Ymdis'),
                'date' => $request->input('date'),
                'object' => $request->input('objet'),
                'note' => $request->input('i_note'),
                'magasin_id' => $request->input('magasin_id'),
                'created_by' => \request()->user()->id,
            ]);

            $eventName = 'Création';
            $successMessage = "Transformation ajoutée avec succès";

            // Add sortant lines
            foreach ($request->input('lignes_sortant') as $ligne_sortant) {
                $ligne = TransformationLigne::create([
                    'transformation_id' => $o_transformation->id,
                    'unite_id' => $ligne_sortant['i_unite'],
                    'quantite' => $ligne_sortant['i_quantite'],
                    'article_id' => $ligne_sortant['i_article_id'],
                    'nom_article' => $ligne_sortant['i_article'],
                    'type' => 'sortant'
                ]);
                StockService::stock_sortir($ligne->article_id, $ligne->quantite, Carbon::make($o_transformation->date)->format('Y-m-d'), Transformation::class, $o_transformation->id, $o_transformation->magasin_id);
            }

            // Add entrant lines
            foreach ($request->input('lignes_entrant') as $ligne_entrant) {
                $ligne = TransformationLigne::create([
                    'transformation_id' => $o_transformation->id,
                    'unite_id' => $ligne_entrant['i_unite'],
                    'quantite' => $ligne_entrant['i_quantite'],
                    'article_id' => $ligne_entrant['i_article_id'],
                    'nom_article' => $ligne_entrant['i_article'],
                    'type' => 'entrant'
                ]);
                StockService::stock_entre($ligne->article_id, $ligne->quantite, Carbon::make($o_transformation->date)->format('Y-m-d'), Transformation::class, $o_transformation->id, $o_transformation->magasin_id);

            }


            \DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event($eventName)
                ->withProperties([
                    'subject_type' => Transformation::class,
                    'subject_id' => $o_transformation->id,
                    'subject_reference' => $o_transformation->reference,
                ])
                ->log($eventName . ' de transformation ' . $o_transformation->reference ?? '-');

            return redirect()->route('transformations.liste')->with('success', $successMessage);
        } catch (\Exception $exception) {
            \DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('transformations.liste')->with('error', "Une erreur s'est produite lors de la demande");
        }
    }

    public function afficher(int $id)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.afficher']);;
        $o_transformation = Transformation::with(['lignes', 'magasin'])->findOrFail($id);
        return view('transformations.afficher', compact('o_transformation'));
    }

    public function modifier($id)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $o_transformation = Transformation::with(['lignes'])->findOrFail($id);
        $o_magasins = \request()->user()->magasins()->get(['magasin_id as id', 'nom as text']);
        $o_unites = Unite::get(['id', "nom"]);

        return view('transformations.modifer', compact('o_transformation', 'o_magasins', 'o_unites'));
    }

    public function annuler(int $id)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.annuler']);;
        $o_transformation = Transformation::findOrFail($id);
        if ($o_transformation->status === 'annulé') {
            return response('Transformation est déja annulé', 400);
        }
        StockService::stock_revert(Transformation::class, $o_transformation->id);
        $o_transformation->update([
            'status' => 'annulé'
        ]);
        activity()
            ->causedBy(Auth::user())
            ->event('Annulation')
            ->withProperties([
                'subject_type' => Transformation::class,
                'subject_id' => $o_transformation->id,
            ]);
        return response('Transformation annulé avec succès', 200);

    }

    public function supprimer(int $id)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        $this->guard_custom(['transformation.supprimer']);;

        if (\request()->ajax()) {
            $o_transformation = Transformation::find($id);
            if ($o_transformation) {
                $o_transformation->delete();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => Transformation::class,
                        'subject_id' => $o_transformation->id,
                        'subject_reference' => $o_transformation->reference,
                    ])
                    ->log('Suppression de transformation ' . $o_transformation->reference ?? '-');

                return response('Transformation supprimé  avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }

    public function mettre_a_jour(Request $request, $id)
    {
        if (!LimiteService::is_enabled('transformation')) {
            abort(404);
        }
        \Validator::make($request->all(), [
            'date' => ['required', 'date_format:d/m/Y'],
            'objet' => ['nullable'],
            'i_note' => ['nullable'],
            'magasin_id' => ['nullable', 'exists:magasins,id'],
            'lignes_sortant' => 'required|array',
            'lignes_sortant.*.i_unite' => 'required|exists:unites,id',
            'lignes_sortant.*.i_quantite' => 'required|numeric',
            'lignes_sortant.*.i_article_id' => 'nullable|exists:articles,id',
            'lignes_sortant.*.i_article' => 'required|string|max:255',

            'lignes_entrant' => 'required|array',
            'lignes_entrant.*.i_unite' => 'required|exists:unites,id',
            'lignes_entrant.*.i_quantite' => 'required|numeric',
            'lignes_entrant.*.i_article_id' => 'nullable|exists:articles,id',
            'lignes_entrant.*.i_article' => 'required|string|max:255',
        ], [], [
            'date' => 'date',
            'lignes_sortant' => 'lignes sortant',
            'lignes_entrant' => 'lignes entrant',
            'objet' => 'objet',
            'i_note' => 'note',
            'magasin_id' => 'magasin',
            'lignes_sortant.*.i_unite' => 'unite',
            'lignes_sortant.*.i_quantite' => 'quantite',
            'lignes_sortant.*.i_article_id' => 'article',
            'lignes_sortant.*.i_article' => 'article',
            'lignes_entrant.*.i_unite' => 'unite',
            'lignes_entrant.*.i_quantite' => 'quantite',
            'lignes_entrant.*.i_article_id' => 'article',
            'lignes_entrant.*.i_article' => 'article',
        ])->validate();
        \DB::beginTransaction();

        try {
            // Find the transformation to update
            $o_transformation = Transformation::findOrFail($id);

            // Update the transformation
            $o_transformation->update([
                'date' => $request->input('date'),
                'objet' => $request->input('objet'),
                'note' => $request->input('i_note'),
                'magasin_id' => $request->input('magasin_id'),
            ]);

            // Delete existing lines
            TransformationLigne::where('transformation_id', $o_transformation->id)->delete();

            // Add sortant lines
            foreach ($request->input('lignes_sortant') as $ligne_sortant) {
                TransformationLigne::create([
                    'transformation_id' => $o_transformation->id,
                    'unite_id' => $ligne_sortant['i_unite'],
                    'quantite' => $ligne_sortant['i_quantite'],
                    'article_id' => $ligne_sortant['i_article_id'],
                    'nom_article' => $ligne_sortant['i_article'],
                    'type' => 'sortant'
                ]);
            }

            // Add entrant lines
            foreach ($request->input('lignes_entrant') as $ligne_entrant) {
                TransformationLigne::create([
                    'transformation_id' => $o_transformation->id,
                    'unite_id' => $ligne_entrant['i_unite'],
                    'quantite' => $ligne_entrant['i_quantite'],
                    'article_id' => $ligne_entrant['i_article_id'],
                    'nom_article' => $ligne_entrant['i_article'],
                    'type' => 'entrant'
                ]);
            }

            \DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Modification')
                ->withProperties([
                    'subject_type' => Transformation::class,
                    'subject_id' => $o_transformation->id,
                    'subject_reference' => $o_transformation->reference,
                ])
                ->log('Modification de transformation ' . $o_transformation->reference ?? '-');

            return redirect()->route('transformations.liste')->with('success', "Transformation modifiée avec succès");
        } catch (\Exception $exception) {
            \DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('transformations.liste')->with('error', "Une erreur s'est produite lors de la demande");
        }
    }
}
