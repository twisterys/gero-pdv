<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
use App\Models\PosSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PosSessionContoller extends Controller
{
    public function ajouter()
    {
        $this->guard_custom(['pos.*']);
        $pos_ouverte = PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if ($pos_ouverte) {
            return redirect()->route('pos');
        }
        $magasins =  auth()->user()->magasins;
        return view('pos.ajouter', compact('magasins'));
    }

    public function commencer(Request $request)
    {
        $this->guard_custom(['pos.*']);
        $pos_ouverte = PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if ($pos_ouverte) {
            return redirect()->route('pos');
        }
        $attributes = [
            'magasin_id' => "magasin",
        ];
        if (!$request->user()->accessibleTo($request->input('magasin_id'))){
            session()->flash('warning',"Magasin n'est pas accessible");
            return redirect()->route('pos.ajouter');
        }
        Validator::make($request->all(), [
            'magasin_id' => 'required|exists:magasins,id'
        ], [], $attributes)->validate();

        $session = new PosSession();
        $session->magasin_id = $request->input('magasin_id');
        $session->user_id = auth()->user()->id;
        $session->ouverte = 1;
        $session->save();

        return redirect()->route('pos');
    }
}
