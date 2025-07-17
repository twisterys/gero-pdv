<?php

namespace App\Http\Controllers;

use App\Models\MethodesPaiement;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MethodesPaiementController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['parametres.methode_de_paiement']);
        $o_methodes_paiement = MethodesPaiement::all();
        return view("parametres.methodesPaiement.liste", compact('o_methodes_paiement'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.methode_de_paiement']);
        $validationRules = ['nom' => 'required|min:3', 'actif' => 'boolean',];

        $validated = $request->validate($validationRules);
        $validated['key'] = $validated['nom'];

        try {
            MethodesPaiement::create($validated);
            session()->flash('success', 'Méthode de paiement ajouté avec succès');
            return redirect()->route('methodes_paiement.liste');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function modifier($id)
    {
        $this->guard_custom(['parametres.methode_de_paiement']);
        $o_methodes_paiement = MethodesPaiement::find($id);
        if ($o_methodes_paiement) {
            $name = $o_methodes_paiement->nom;
            $actif = $o_methodes_paiement->actif;
            $defaut = $o_methodes_paiement->defaut;
            return view('parametres.methodesPaiement.modifier', compact('name', 'actif', 'defaut', 'id'));
        }
        abort(404);
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['parametres.methode_de_paiement']);
        $validationRules = ['nom' => 'min:3', 'actif' => 'boolean',];

        $validated = $request->validate($validationRules);

        if (!isset($validated['actif'])) {
            $validated['actif'] = 0;
        }

        $o_methodes_paiement = MethodesPaiement::findOrFail($id);
        $o_methodes_paiement->update($validated);
        session()->flash('success', 'Méthode de paiement mise à jour');
        return redirect()->route('methodes_paiement.liste');
    }

    public function supprimer($id)
    {
        $this->guard_custom(['parametres.methode_de_paiement']);
        if (\request()->ajax()) {
            $o_methodes_paiement = MethodesPaiement::find($id);
            if ($o_methodes_paiement) {
                $o_methodes_paiement->delete();
                session()->flash('success', 'Méthode de paiement supprimé');
                return redirect()->route('methodes_paiement.liste');
            } else {
                return response('Erreur', 404);
            }
        }
    }

    public function modifier_active(Request $request){
        $this->guard_custom(['parametres.methode_de_paiement']);
        $id = $request->get('id');
        $active = $request->get('active');
        DB::table('methodes_paiement')->where('id', $id)->update(['actif' => $active]);
        return response()->json(['success' => 'success']);
    }
}
