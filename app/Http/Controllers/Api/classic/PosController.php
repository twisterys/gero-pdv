<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use App\Services\LogService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function terminer()
    {
        try {
            PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->update(['ouverte' => 0, 'date_fin' => now()]);
            return response('Session est terminée.', 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response('Erreur lors de la fermeture de la session.', 500);
        }
    }

    public function cloture(){
        try {
            $pos_session =  PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
            $total_retour = $pos_session->ventes->where('type_document',\App\Services\PosService::getValue('type_retour'))->sum('total_ttc');
            $total_vente = $pos_session->ventes->where('type_document',\App\Services\PosService::getValue('type_vente'))->sum('total_ttc');
            $total_depense = $pos_session->depenses->sum('montant');
            $total = $total_vente - $total_retour - $total_depense;


            $count_retour = $pos_session->ventes->where('type_document',\App\Services\PosService::getValue('type_retour'))->count();
            $count_vente = $pos_session->ventes->where('type_document',\App\Services\PosService::getValue('type_vente'))->count();
            $count_depense = $pos_session->depenses->count();
            $count_total = $count_retour + $count_vente + $count_depense;

            return view('pos.cloture_ticket',compact('pos_session','total_retour','total_vente','total_depense','total','count_depense','count_vente','count_retour','count_total'));
        } catch (\Exception $e) {
            LogService::logException($e);
            return response('Erreur lors de la fermeture de la session.', 500);
        }
    }
}
