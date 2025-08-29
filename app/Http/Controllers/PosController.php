<?php

namespace App\Http\Controllers;

use App\Models\CategorieDepense;
use App\Models\Client;
use App\Models\Compte;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\PosSettings;
use App\Services\LimiteService;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\FormeJuridique;
use App\Models\MethodesPaiement;
use App\Models\PosSession;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{

    public  function pos(Request $request){
        $this->guard_custom(['pos.*']);
        if (!LimiteService::is_enabled('pos')){
            abort(404);
        }
        $pos_ouverte = PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();

        if (!$pos_ouverte) {
            return redirect()->route('pos.ajouter');
        }
        $session = $pos_ouverte;
        $client = Client::first(['id as value', 'nom as label']) ?? null;
        $comptes = Compte::all(['id as value', 'nom as label']);
        $methodes = MethodesPaiement::where('actif', 1)->get(['key as value', 'nom as label']);
        $formes_juridique = FormeJuridique::all(['id as value', 'nom as label']);
        $pos_type = PosService::getValue('type_pos');
        $modifier_prix =PosService::getValue('modifier_prix');
        $magasins = Magasin::whereNot('id',$pos_ouverte->magasin_id)->get(['nom as label','id as value']);
        $ouverture = Carbon::make($pos_ouverte->created_at)->format('d/m/Y h:m:s');
        $depenses = CategorieDepense::get(['nom as label','id as value']);
        $is_code_barre = GlobalSetting::first()->code_barre != 0;
        $on_reduction = (PosSettings::where('key', 'reduction')->value('value') == 1);
        $is_price_editable = (PosSettings::where('key', 'modifier_prix')->value('value') == 1);
        $is_depenses= (PosSettings::where('key','depenses')->value('value') == 1);
        $is_historique= (PosSettings::where('key','historique')->value('value') == 1);
        $is_demandes= (PosSettings::where('key','demandes')->value('value') == 1);

        // Get report statuses
        $rapport_ac_enabled = DB::table('pos_rapports')->where('cle', 'ac')->value('actif') ?? false;
        $rapport_as_enabled = DB::table('pos_rapports')->where('cle', 'as')->value('actif') ?? false;
        $rapport_tr_enabled = DB::table('pos_rapports')->where('cle', 'tr')->value('actif') ?? false;
        $rapport_af_enabled = DB::table('pos_rapports')->where('cle', 'af')->value('actif') ?? false;
        $rapport_cr_enabled = DB::table('pos_rapports')->where('cle', 'cr')->value('actif') ?? false;

        auth()->user()->tokens()->delete();
        return view('pos.pos', compact(
            'depenses','client', 'comptes', 'methodes',
            'formes_juridique', 'session', 'pos_type', 'magasins','ouverture',
            'modifier_prix','is_code_barre','on_reduction','is_price_editable',
            'is_depenses','is_historique','is_demandes', 'rapport_ac_enabled',
            'rapport_as_enabled','rapport_af_enabled','rapport_tr_enabled','rapport_cr_enabled'
        ));
    }
    public  function posNew(Request $request)
    {
        $this->guard_custom(['pos.*']);
        if (!LimiteService::is_enabled('pos')){
            abort(404);
        }
        $pos_ouverte = PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();

        if (!$pos_ouverte) {
            return redirect()->route('pos.ajouter');
        }

        if (!$request->get('token') || !$request->get('session_id')) {
            auth()->user()->tokens()->delete();
            return redirect()->route('pos',['token'=>auth()->user()->createToken('auth-api')->plainTextToken,'session_id'=>$pos_ouverte->id]);
        }
        return view('pos.pos');
    }
    public  function demandes()
    {
        $this->guard_custom(['pos.*']);
        if (!LimiteService::is_enabled('pos')){
            abort(404);
        }

        $pos_ouverte = PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();

        if (!$pos_ouverte) {
            return redirect()->route('pos.ajouter');
        }
        $session = $pos_ouverte;
        $pos_type = PosService::getValue('type_pos');
        $magasins = Magasin::whereNot('id',$pos_ouverte->magasin_id)->get(['nom as label','id as value']);
        return view('pos.demandes', compact('session','pos_type','magasins'));
    }
}
