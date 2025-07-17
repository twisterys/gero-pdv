<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Module;
use App\Models\Vente;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function modifier(){
        $this->guard_custom(['parametres.modules']);
        $modules = Module::all();
        return view('parametres.modules.modifier',compact('modules'));
    }
    public function mettre_a_jour(Request $request){
        $this->guard_custom(['parametres.modules']);
        foreach (array_merge(Achat::TYPES,Vente::TYPES) as $type){
            Module::where('type',$type)->update([
               'active' => $request->get($type)['active'] ?? false,
               'action_stock' => $request->get($type)['stock'] ?? null,
               'action_paiement' => $request->get($type)['payable'] ?? null,
            ]);
        }
        session()->flash('success','Gestion des documents mise à jour');
        return redirect()->route('modules.modifier');
    }
}
