<?php

namespace App\Http\Controllers;

use App\Models\Promesse;
use Illuminate\Http\Request;

class PromesseController extends Controller
{
    public function supprimer($id){
        $this->guard_custom(['promesse.supprimer']);

        $o_promesse = Promesse::find($id);
        if (!$o_promesse) {
            abort(404);
        }
        $o_promesse->delete();
        session()->flash('success', "Promesse supprimé avec succès");
        return redirect()->route('ventes.afficher', [$o_promesse->vente->type_document, $o_promesse->vente_id]);
    }

    public function respecter(int $id){
        $this->guard_custom(['promesse.respecter']);

        $o_promesse = Promesse::findOrFail($id);
        $o_promesse->update(['statut'=>'respecte']);
        return response('Promesse marquée comme respecté');
    }

    public function rompre(int $id){
        $this->guard_custom(['promesse.rompre']);

        $o_promesse = Promesse::findOrFail($id);
        $o_promesse->update(['statut'=>'rompre']);
        return response('Promesse marquée comme rompré');
    }
}
