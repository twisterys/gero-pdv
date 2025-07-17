<?php

namespace App\Http\Controllers;

use App\Models\CategorieDepense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategorieDepenseController extends Controller
{
    public function liste()
    {
        $o_categorie = CategorieDepense::all();
        return view("parametres.categorieDepense.liste", compact('o_categorie'));
    }

    public function sauvegarder(Request $request)
    {

        $request->validate([
            'i_nom' => 'required |string| unique:categorie_depense,nom',
            'i_active' => 'boolean',
        ]);

        $o_categorie = new CategorieDepense();
        $o_categorie->nom = $request->get('i_nom');
        $o_categorie->active = $request->get('i_active') ?? '0';
        $o_categorie->save();

        session()->flash('success', 'Catégorie ajouté');
        return  redirect()->route('categories.liste');
    }

    public function modifier($id)
    {
        $o_categorie = CategorieDepense::find($id);
        if ($o_categorie) {
            $nom = $o_categorie->nom;
            $active = $o_categorie->active;
            return view('parametres.categorieDepense.modifier', compact('nom', 'active', 'id'));
        }
        abort(404);
    }

    public function  mettre_a_jour(Request $request, $id)
    {
        $o_categorie = CategorieDepense::find($id);
        if ($o_categorie) {
            try {
                $o_categorie->update([
                    'nom' => $request->get('i_nom'),

                ]);

                session()->flash('success', 'Catégorie mise à jour');
                return  redirect()->route('categories.liste');

            } catch (\Exception $exception) {

                session()->flash('error', 'Une erreur est produit');
                return  redirect()->route('categories.liste');
            }
        }
    }

    public function supprimer($id){
        if (\request()->ajax()){
            $i_categorie = CategorieDepense::find($id);
            if ($i_categorie){
                $i_categorie->delete();
                return  response('Catégorie supprimé',200);
            }else {
                return response('Erreur',404);
            }
        }
    }

    public function afficher_ajax(Request $request , $id)
    {
        $o_categorie = CategorieDepense::find($id);
        if ($request->ajax()) {
            if (!$o_categorie) {
                return response()->json('', 404);
            }
            return response()->json($o_categorie, 200);
        }
        if (!$o_categorie) {
            return redirect()->back()->with('error', "Categorie n'existe pas");
        }

    }

    public function categorie_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = CategorieDepense::where('nom', 'LIKE', $search)
                ->where('active', '1')
                ->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }

    public function modifier_active (Request $request){
        $id = $request->input('id');
        $active = $request->input('active');

        // Mettre à jour la base de données
        DB::table('categorie_depense')->where('id', $id)->update(['active' => $active]);

        return response()->json(['success' => 'success']);
    }
}
