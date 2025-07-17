<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\Validator;

class ReferenceController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['parametres.reference']);
        $o_reference = Reference::all();
        $generatedReferences = [];
        foreach ($o_reference as $reference) {
            $generatedRef = ReferenceService::generateReference($reference->type,now()->setYear(session()->get('exercice')));
            $generatedReferences[] = $generatedRef;
        }

        return view("parametres.references.liste",compact('o_reference','generatedReferences'));
    }

    public function modifier($id)
    {
        $this->guard_custom(['parametres.reference']);

        $o_reference = Reference::find($id);
        if ($o_reference){
            return view('parametres.references.modifier',compact('o_reference'));
        }
        abort(404);
    }


    public function modifier_global(Request $request)
    {
        $this->guard_custom(['parametres.reference']);

        $rules = [
            'format'=>'required|min:3',
            'longueur_compteur'=>'nullable|numeric|min:1|max:5',
            'type' => 'required'
        ];
        $attrs = [
            'format' => 'Format',
            'longueur_compteur'=>'Longueur du compteur',
            'type'=> 'Type de référence',
        ];
        Validator::make($request->all(),$rules,[],$attrs)->validate();
        $ventes  = [
            'Devis',
            'Bons de commande',
            'Bon de livraison',
            'Bons de retour',
            'Factures',
            'Factures proforma',
            'Avoirs',
        ];
        $achats = [
            'Devis d\'achat',
            'Bons de commande d\'achat',
            'Bon de reception d\'achat',
            'Bons de retour d\'achat',
            'Factures d\'achat',
            'Avoirs d\'achat',
            'Depenses '
        ];
        $achats_ventes = array_merge($achats, $ventes);


        if($request->get('type') == 1){
            $references = Reference::whereIn('nom', $ventes)->get();
        }elseif ($request->get('type') == 0){
            $references = Reference::whereIn('nom', $achats)->get();
        }else{
            $references = Reference::whereIn('nom', $achats_ventes)->get();
        }
        foreach ($references as $reference){
           $type = strtoupper($reference['type']);
           $template = $type . $request->get('format');
           $reference->update(['template' =>$template]);
           if($request->get('longueur_compteur')){
           $reference->update(['longueur_compteur' =>$request->get('longueur_compteur')]);
           }
        }
        session()->flash('success', 'Reference global mis à jour');
        return  redirect()->route('references.liste');

    }

    public function mettre_a_jour(Request $request,$id)
    {
        $this->guard_custom(['parametres.reference']);

        $rules = [
          'format'=>'required|min:3',
          'longueur_compteur'=>'required|numeric|min:1|max:5'
        ];
        $attrs = [
          'format' => 'Format',
          'longueur_compteur'=>'Longueur du compteur'
        ];
        Validator::make($request->all(),$rules,[],$attrs)->validate();
        $o_reference = Reference::findOrFail($id);

        $o_reference->update([
            'longueur_compteur' => $request->get('longueur_compteur'),
            'template' => $request->get('format')
        ]);
        session()->flash('success', 'Reference mise à jour');
        return  redirect()->route('references.liste');

    }
}
