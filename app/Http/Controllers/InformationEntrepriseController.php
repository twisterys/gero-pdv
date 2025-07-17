<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\FormeJuridique;
use App\Models\InformationEntreprise;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\Vente;
use App\Services\LogService;
use Exception;
use http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InformationEntrepriseController extends Controller
{
    public function modifier(){
        $o_client = InformationEntreprise::first();
        $form_juridique_types = FormeJuridique::where('active','1')->get();
        return view('parametres.informations.modifier',compact('o_client', 'form_juridique_types'));
    }
    public function mettre_a_jour(Request $request){

        DB::beginTransaction();
        try {
            $informations_entreprise = InformationEntreprise::first();

            $data = [
                'forme_juridique'=>$request->get('forme_juridique'),
                'raison_social'=>$request->get('raison_social'),
                'ice'=>$request->get('ice'),
                'email'=>$request->get('email'),
                'telephone'=>$request->get('telephone'),
                'note'=>$request->get('note'),
                'adresse'=>$request->get('adresse'),
                'RC'=>$request->get('RC'),
                'IF'=>$request->get('IF'),
                'ville'=>$request->get('ville'),
            ];
//            dd($data);
            if($informations_entreprise){
                $informations_entreprise->update($data);
            }else{
                $informations_entreprise = new InformationEntreprise();
                $informations_entreprise->create($data);
            }
            session()->put('nom_entreprise',$informations_entreprise->raison_social);
            DB::commit();
            session()->flash('success','Information entreprise mis à jour');
            return redirect()->route('informations.modifier');

//            return redirect()->route('informations.modifier')->with('success', 'Informations d\'entreprise modifiés avec succès');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('informations.modifier')->with('error', "Probleme de sauvegarde");
        }
    }



}
