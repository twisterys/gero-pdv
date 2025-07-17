<?php

namespace App\Http\Controllers;

use App\Models\Compteur;
use Illuminate\Http\Request;

class CompteurController extends Controller
{
    public function modifier(){
        $compteurs =Compteur::where('annee',session()->get('exercice'))->orWhereIn('type',['clt','fr','cms','art'])->get();
        return view('parametres.compteurs.modifier',compact('compteurs'));
    }

    public function sauvegarder(Request $request){
        $compteurs =Compteur::where('annee',session()->get('exercice'))->get();
        foreach ($compteurs as $compteur){
            $compteur->update(
                ['compteur' => $request->get($compteur->type)]
            );
        }
        session()->flash('success','Compteurs mise à jour !');
        return redirect()->route('compteurs.modifier');
    }
}
