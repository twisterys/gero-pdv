<?php

namespace App\Http\Controllers;

use App\Models\PosSettings;
use App\Services\LogService;
use DB;
use Exception;
use Illuminate\Http\Request;

class PosSettingsController extends Controller
{
    public function modifier()
    {
        $this->guard_custom(['parametres.pos']);
        $pos_settings = PosSettings::all();
        $rapports = DB::table('pos_rapports')->get();
        return view('parametres.posSettings.modifier', compact('pos_settings','rapports'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.pos']);
        $pos_settings = PosSettings::all();
        try {
            DB::beginTransaction();

            // Save general settings
            foreach ($pos_settings as $option) {
                $option->value = $request->get($option->key) ?? null;
                $option->save();
            }

            // Save rapport settings
            $rapports = DB::table('pos_rapports')->get();
            foreach ($rapports as $rapport) {
                $isActive = $request->has('rapport_' . $rapport->cle) ? true : false;
                DB::table('pos_rapports')
                    ->where('id', $rapport->id)
                    ->update(['actif' => $isActive]);
            }

            DB::commit();
            session()->flash('success', 'Paramètres mise à jour !');
            return redirect()->route('pos-settings.modifier');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Erreur');
            return redirect()->route('pos-settings.modifier');
        }
    }
}
