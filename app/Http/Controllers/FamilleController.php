<?php

namespace App\Http\Controllers;

use App\Models\Famille;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FamilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function liste()
    {
        if (\request()->ajax()) {
            $query = Famille::select('*');
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            );
            $table->addColumn('actions', function ($row) {
                $crudRoutePart = 'familles';
                $delete = 'supprimer';
                $edit_modal = ['url' => route('familles.modifier', $row->id), 'modal_id' => 'edit-cat-modal'];
                $id = $row->id;
                return view('partials.__datatable-action', compact('id', 'crudRoutePart', 'edit_modal', 'delete'));
            });
            $table->editColumn('actif', function ($row) {
                if (+$row->actif === 1) {
                    return '<div class="badge bg-soft-success"">Oui</div>';
                }
                return '<div class="badge bg-soft-danger"">Non</div>';
            });
            $table->editColumn('couleur', function ($row) {
                return '<div class="badge" style="background-color:' . $row->couleur . '">' . $row->couleur . '</div>';
            });
            $table->rawColumns(['selectable_td', 'couleur', 'actif', 'actions']);
            return $table->make();
        }
        return view('familles.liste');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function sauvegarder(Request $request)
    {
        if ($request->ajax()){
            $i_famille = Famille::create([
                'nom' => $request->get('i_nom'),
                'couleur' => $request->get('i_couleur'),
                'actif' => $request->get('i_actif') ?? '0'
            ]);
            return $i_famille;
        }
        $i_famille = Famille::create([
            'nom' => $request->get('i_nom'),
            'couleur' => $request->get('i_couleur'),
            'actif' => $request->get('i_actif') ?? '0'
        ]);
        if ($i_famille) {
            session()->flash('success', 'Famille ajouté');
            return redirect()->route('familles.liste');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifier($id)
    {
        $i_famille = Famille::find($id);
        if ($i_famille) {
            $name = $i_famille->nom;
            $color = $i_famille->couleur;
            $actif = $i_famille->actif;
            return view('familles.partials.modifier_modal', compact('name', 'actif', 'color', 'id'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function mettre_a_jour(Request $request, $id)
    {
        $i_famille = Famille::find($id);
        if ($i_famille) {
            $i_famille->update([
                'nom' => $request->get('i_nom'),
                'couleur' => $request->get('i_couleur'),
                'actif' => $request->get('i_actif') ?? '0'
            ]);
            session()->flash('success', 'Famille mise à jour');
            return redirect()->route('familles.liste');
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer($id)
    {
        if (\request()->ajax()) {
            $i_famille = Famille::find($id);
            if ($i_famille) {
                $i_famille->delete();
                return  response('Famille supprimé', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    public function famille_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Famille::where('nom', 'LIKE', $search)->where('actif', '1')->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }
}
