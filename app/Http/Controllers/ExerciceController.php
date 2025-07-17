<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    function changer()
    {
        $exercices = Exercice::where('cloturee','0')->get();
        return view('exercices.partials.changer-modal',compact('exercices'));
    }

    function mettre_en_place(Request $request)
    {
        $request->validate(['i_exercice'=>'exists:exercices,annee|required']);
        session()->put('exercice',$request->get('i_exercice'));
        session()->flash('success','Exercice a été mis en place');
        return back();
    }

    function ajouter(Request $request)
    {
        return view('exercices.partials.ajouter-modal');
    }

    function sauvegarder(Request $request)
    {
        $request->validate(['i_year'=>'unique:exercices,annee|numeric|min:4|required']);
        $o_exercice = new Exercice();
        $o_exercice->annee = $request->get('i_year');
        $o_exercice->save();
        ReferenceService::generer_les_compteur($o_exercice->annee);
        return response()->json($o_exercice,200);
    }
}
