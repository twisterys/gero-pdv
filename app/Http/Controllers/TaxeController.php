<?php

namespace App\Http\Controllers;

use App\Models\Taxe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaxeController extends Controller
{

    public function liste()
    {
        $this->guard_custom(['parametres.taxes']);
        $taxes = Taxe::all();
        return view('parametres.taxes.liste',compact('taxes'));
    }
    public function modifier($valeur)
    {
        $this->guard_custom(['parametres.taxes']);
        $o_taxe = Taxe::where('valeur',$valeur)->first();
        if (!$o_taxe){
            return response("Taxe n'exist pas !",404);
        }
        return view('parametres.taxes.partials.modifier_modal',compact('o_taxe'));
    }
    public function ajouter()
    {
        $this->guard_custom(['parametres.taxes']);

        return view('parametres.taxes.partials.ajouter_modal');
    }
    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.taxes']);

        $rules= [
            'i_nom'=>['required',Rule::unique('taxes','nom')],
            'i_valeur'=>['required',Rule::unique('taxes','valeur'),'numeric'],
            'i_par_defaut'=>'nullable'
        ];
        $attributes = [
            'i_nom'=>'nom',
            'i_valeur'=>'valuer',
            'i_par_defaut'=>'par défaut'
        ];
        $validation = Validator::make($request->all(),$rules,[],$attributes);
        if ($validation->fails()){
            $messaget='';
            foreach ($validation->messages()->getMessages() as $message){
                $messaget.=$message[0].'<br>';
            }
            return response($messaget,400);
        }
//        if ($request->get('i_par_defaut')){
//            Taxe::query()->update([
//                'active'=>'0',
//            ]);
//        }
        Taxe::create([
            'nom'=>$request->get('i_nom'),
            'valeur'=>$request->get('i_valeur'),
            'active'=>$request->get('i_par_defaut')?'1' : '0',
        ]);
        return response('Taxe ajouté',200);
    }
    public function mettre_a_jour(Request $request, $valeur)
    {
        $o_taxe = Taxe::where('valeur',$valeur)->first();
        if (!$o_taxe){
            return response("Taxe n'exist pas !",404);
        }
        $rules= [
            'i_nom'=>['required',Rule::unique('taxes','nom')->ignore(number_format($valeur,1,'.',''),'valeur')],
            'i_valeur'=>['required',Rule::unique('taxes','valeur')->ignore(number_format($valeur,1,'.',''),'valeur'),'numeric'],

        ];
        $attributes = [
            'i_nom'=>'nom',
            'i_valeur'=>'valuer',

        ];
        $validation = Validator::make($request->all(),$rules,[],$attributes);
        if ($validation->fails()){
            $messaget='';
            foreach ($validation->messages()->getMessages() as $message){
                $messaget.=$message[0].'<br>';
            }
            return response($messaget,400);
        }

        $o_taxe->update([
            'nom'=>$request->get('i_nom'),
            'valeur'=>$request->get('i_valeur'),
        ]);
        return response('Taxe modifié',200);
    }
    public function supprimer($valeur)
    {
        $this->guard_custom(['parametres.taxes']);

        if (\request()->ajax()){
            $o_taxe = Taxe::where('valeur',$valeur)->first();
            if ($o_taxe){
                $o_taxe->delete();
                return  response('Taxe supprimé',200);
            }else {
                return response('Erreur',404);
            }
        }
    }
    public function taxe_select(Request $request)
    {
        $search = '%'.$request->get('term').'%';
        $data = Taxe::where('nom','LIKE',$search)->get(['nom as text','valeur as id']);

        return response()->json($data,200);
    }

    public function modifier_active(Request $request){
        $this->guard_custom(['parametres.taxes']);

        $valeur = $request->input('valeur');
        $active = $request->input('active');

        // Mettre à jour la base de données
        DB::table('taxes')->where('valeur', $valeur)->update(['active' => $active]);

        return response()->json(['success' => 'success']);
    }
}
