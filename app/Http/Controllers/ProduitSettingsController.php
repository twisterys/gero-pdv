<?php

namespace App\Http\Controllers;

use App\Models\ProduitSettings;
use Illuminate\Http\Request;

class ProduitSettingsController extends Controller
{
    public function modifier(){
        $produit_settings = ProduitSettings::all();
        return view('parametres.produitsSettings.modifier',compact('produit_settings'));
    }

    public function sauvegarder(Request $request){
        $request->validate([
            'image'=>'nullable|in:0,1',
            'marque'=>'nullable|in:0,1',
            'numero_serie'=>'nullable|in:0,1'
        ]);
        foreach (['image','marque','numero_serie'] as $key){
            $product_setting = ProduitSettings::where('key',$key)->firstOr(function () use($request,$key){
                ProduitSettings::create([
                    'key' => 'numero_serie',
                    'value' =>$request->get($key) ?? 0
                ]);
            });
            if ($product_setting){
                $product_setting->update(['value' => $request->get($key) ?? 0]);
            }
        }
        session('success','Paramètres de produit mise à jour !');
        return redirect()->route('produits-settings.modifier');
    }
}
