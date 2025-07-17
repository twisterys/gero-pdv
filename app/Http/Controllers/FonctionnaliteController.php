<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Services\GlobalService;
use Illuminate\Http\Request;

class FonctionnaliteController extends Controller
{
    public function modifier(){
        $o_global_settings = GlobalSetting::first();
        return view('parametres.fonctionnalites.modifier',compact('o_global_settings'));
    }

    public function sauvegarder(Request $request){
        $o_global_settings = GlobalSetting::first();
        $o_global_settings->update([
            'modifier_reference'=> $request->has('i_modifier_reference'),
            'prix_revient'=> $request->has('i_prix_revient'),
            'template_par_document'=> $request->has('i_template_par_document'),
            'code_barre'=> $request->has('i_code_barre'),
            'controle'=>$request->has('i_controle'),
            'pieces_jointes' => $request->has('i_pieces_jointes'),
        ]);
        session()->flash('success','Fonctionnalités mise à jour !');
        return redirect()->route('fonctionnalites.sauvegarder');
    }
}
