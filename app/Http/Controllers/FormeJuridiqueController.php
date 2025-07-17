<?php

namespace App\Http\Controllers;

use App\Models\FormeJuridique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class FormeJuridiqueController extends Controller
{
    public function liste(){
        $this->guard_custom(['parametres.forme_juridique']);
        $o_formes_juridique = FormeJuridique::all();
        return view("parametres.formesJuridique.liste", compact('o_formes_juridique'));
    }

    public function ajouter() {}

    public function sauvegarder(Request $request){
        $this->guard_custom(['parametres.forme_juridique']);
        $rules = [
            'nom' => ['required', Rule::unique('forme_juridique', 'nom')],
            'nom_sur_facture' => ['required', Rule::unique('forme_juridique', 'nom_sur_facture')],
            'active' => 'nullable|boolean',
        ];

        $attributes = [
            'nom' => 'forme juridique',
            'nom_sur_facture' => 'nom sur facture',
            'active' => 'active',
        ];

        $validation = Validator::make($request->all(), $rules, [], $attributes);

        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            return response($messaget, 400);
        }

        FormeJuridique::create([
            'nom' => $request->get('nom'),
            'nom_sur_facture' => $request->get('nom_sur_facture'),
            'active' => $request->get('active') ? '1' : '0',
        ]);

        return response('Forme juridique ajoutée', 200);

    }

    public function modifier($id){
        $this->guard_custom(['parametres.forme_juridique']);
        $o_forme_juridique = FormeJuridique::where('id',$id)->first();
       // dd($id);
        if (!$o_forme_juridique){
            return response("Taxe n'exist pas!",404);
        }
        return view('parametres.formesJuridique.modifier',compact('o_forme_juridique'));
    }

    public function mettre_a_jour(Request $request ,$id) {
        $this->guard_custom(['parametres.forme_juridique']);
        $o_forme_juridique = FormeJuridique::where('id', $id)->first();

        if (!$o_forme_juridique){
            return response("Forme juridique n'existe pas !", 404);
        }

        $rules = [
            'nom' => ['required', Rule::unique('forme_juridique', 'nom')->ignore($o_forme_juridique->nom, 'nom')],
            'nom_sur_facture' => ['required', Rule::unique('forme_juridique', 'nom_sur_facture')->ignore($o_forme_juridique->nom_sur_facture, 'nom_sur_facture')],
        ];

        $attributes = [
            'nom' => 'forme juridique',
            'nom_sur_facture' => 'nom sur facture',
        ];

        $validation = Validator::make($request->all(), $rules, [], $attributes);

        if ($validation->fails()){
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message){
                $messaget .= $message[0] . '<br>';
            }
            return response($messaget, 400);
        }
        try {
            $o_forme_juridique->update([
                'nom' => $request->get('nom'),
                'nom_sur_facture' => $request->get('nom_sur_facture'),
            ]);
            session()->flash('success', 'Forme juridique mise à jour');
            return  redirect()->route('formes_juridique.liste');
        }catch (Exception $e) {
            session()->flash('error', 'Une erreur est produit');
            return  redirect()->route('formes_juridique.liste');
        }

    }

    public function supprimer($id){
        $this->guard_custom(['parametres.forme_juridique']);
        if (\request()->ajax()){
            $o_forme_juridique = FormeJuridique::where('id',$id)->first();
            if ($o_forme_juridique){
                $o_forme_juridique->delete();
                return  response('Forme juridique supprimé',200);
            }else {
                return response('Erreur',404);
            }
        }
    }

    public function modifier_active(Request $request){
        $this->guard_custom(['parametres.forme_juridique']);
        $id = $request->input('id');
        $active = $request->input('active');

        // Mettre à jour la base de données
        DB::table('forme_juridique')->where('id', $id)->update(['active' => $active]);

        return response()->json(['success' => 'success']);
    }

}
