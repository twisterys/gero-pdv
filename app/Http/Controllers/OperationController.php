<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['paiement.operation_bancaire']);
        $operations = Operation::all();
        return view('parametres.operations.liste',compact('operations'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['paiement.operation_bancaire']);
        if ($request->ajax()){
            $rules = [
                'i_nom' => ['required'],
//                'i_reference' => ['required'],
                'i_action' =>  ['required','in:encaisser,decaisser'],
            ];
            $attrs = [
                'i_nom'=>'nom',
                'i_action'=>'action'
            ];
            \Validator::make($request->all(),$rules,[],$attrs)->validate();
            Operation::create([
                'nom'=>$request->i_nom,
                'reference'=>$request->i_nom,
                'action'=>$request->i_action,
            ]);
            return response('Opération ajouté !',200);
        }
        abort(404);
    }



    public function supprimer(Operation $operation)
    {
        $this->guard_custom(['paiement.operation_bancaire']);
        if (\request()->ajax()){
            $operation->paiements()->delete();
            $operation->delete();
            return response('Opération supprimée',200);
        }
        abort(404);
    }

}
