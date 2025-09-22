<?php

namespace App\Http\Controllers;

use App\Models\CategorieDepense;
use App\Models\Compte;
use App\Models\Magasin;
use App\Services\LimiteService;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Illuminate\Http\Request;


class MagasinController extends Controller
{

    public function liste()
    {
        $this->guard_custom(['parametres.magasins']);
        $reference = 'LC-' . Magasin::count()+1;
        $magasins = Magasin::with('compte')->get();
        $comptes = Compte::all();
        return view("parametres.magasins.liste", compact('magasins', 'reference', 'comptes'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.magasins']);
        if (LimiteService::get_value('magasin_extra') < Magasin::count()){
            session()->flash('warning',"Vous avez atteint le nombre maximum des magasins");
            return redirect()->route('magasin.liste');
        }
        $validationRules = ['reference' => 'required|max:20|unique:magasins,reference',
            'nom' => 'required',
            'adresse' => 'required',
            'type_local' => 'required',
            'compte_id' => 'required|exists:comptes,id'
        ];

        $validated = $request->validate($validationRules);
        try {
            Magasin::create($validated);
            session()->flash('success', 'Magasin ajouté avec succès');
            return redirect()->route('magasin.liste');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function supprimer($id)
    {
        $this->guard_custom(['parametres.magasins']);
        if (\request()->ajax()){
            $o_magasin = Magasin::find($id);
            if ($o_magasin){
                $o_magasin->delete();
                return  response('Magasin supprimé avec succès',200);
            }else {
                return response('Erreur',404);
            }
        }
    }
    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['parametres.magasins']);
        $validationRules = ['reference' => 'required|max:20|unique:magasins,reference,'.$id,
            'nom' => 'required',
            'adresse' => 'required',
            'type_local' => 'required',
            'compte_id' => 'required|exists:comptes,id',
        ];
        $validated = $request->validate($validationRules);
        $o_magasin = Magasin::findOrFail($id);
        $o_magasin->update($validated);
        session()->flash('success', 'Magasin mise à jour');
        return redirect()->route('magasin.liste');
    }


    public function modifier($id)
    {
        $this->guard_custom(['parametres.magasins']);

        $o_magasin = Magasin::where('id', $id)->first();
        $comptes = Compte::all();
        if ($o_magasin) {
            $nom = $o_magasin->nom;
            $adresse = $o_magasin->adresse;
            $reference = $o_magasin->reference;
            $type_local = $o_magasin->type_local;
            $compte_id = $o_magasin->compte_id;
            return view('parametres.magasins.modifier', compact('comptes','nom', 'reference', 'adresse', 'id', 'type_local', 'compte_id'));
        }
        abort(404);
    }

    public function magasin_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Magasin::where('nom', 'LIKE', $search)
                ->where('active', '1')
                ->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }

    public function modifier_active(Request $request){

        $this->guard_custom(['parametres.magasins']);

        $id = $request->get('id');
        $active=$request->get('active');

        DB::table('magasins')->where('id', $id)->update(['active' => $active]);
        return response()->json(['success' => 'success']);
    }

}
