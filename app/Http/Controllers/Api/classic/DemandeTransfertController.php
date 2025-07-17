<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\parfums\DemandeExterneTransfertResource;
use App\Http\Resources\Api\parfums\DemandeTransfertResource;
use App\Http\Resources\Api\parfums\MaDemandeTransfertResource;
use App\Models\DemandeTransfert;
use App\Models\DemandeTransfertLigne;
use App\Models\PosSession;
use App\Services\StockService;
use Illuminate\Http\Request;

class DemandeTransfertController extends Controller
{
    public function liste(){
        $o_pos_session =  PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if (!$o_pos_session){
            return response('Session non ouverte',403);
        }
        $data = [
            'mes_demandes'=>DemandeTransfertResource::collection(DemandeTransfert::where('magasin_entree_id',$o_pos_session->magasin_id)->whereIn('statut',['Nouvelle','Livrée'])->get()),
            'demandes'=>DemandeTransfertResource::collection(DemandeTransfert::where('magasin_sortie_id',$o_pos_session->magasin_id)->whereIn('statut',['Nouvelle','Livrée'])->get())
        ];
        return response()->json($data);
    }
    public function mes_demandes(){
        $o_pos_session =  PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if (!$o_pos_session){
            return response('Session non ouverte',403);
        }
        $data =MaDemandeTransfertResource::collection(DemandeTransfert::where('magasin_entree_id',$o_pos_session->magasin_id)->whereIn('statut',['Nouvelle','Livrée'])->get());
        return response()->json($data);
    }
    public function demandes_externe(){
        $o_pos_session =  PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if (!$o_pos_session){
            return response('Session non ouverte',403);
        }
        $data =DemandeExterneTransfertResource::collection(DemandeTransfert::where('magasin_sortie_id',$o_pos_session->magasin_id)->whereIn('statut',['Nouvelle','Livrée'])->get());
        return response()->json($data);
    }
    public function sauvegarder(Request $request){
        \Validator::make($request->all(),[
            'lignes'=>'required|array',
            'lignes.*.id'=>'required|exists:articles,id',
            'lignes.*.quantity'=>'required|numeric|min:1',
            'magasin_sortie'=>'required',
            'magasin_sortie.value'=>'required|exists:magasins,id'
        ],[]);
        $o_pos_session =  PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->first();
        if (!$o_pos_session){
            return response('Session non ouverte',403);
        }
        $o_demande_transfert=  DemandeTransfert::create([
           'reference' => 'DT-'.now()->toDateTimeString(),
            'magasin_entree_id' => $o_pos_session->magasin_id,
            'magasin_sortie_id' => $request->get('magasin_sortie')['value'],
            'user_id' => $request->user()->id,
            'statut' => 'Nouvelle',
        ]);
        foreach ($request->lignes as $ligne) {
            DemandeTransfertLigne::create([
                'article_id' => $ligne['id'],
                'quantite_demande' => $ligne['quantity'],
                'demande_transfert_id' => $o_demande_transfert->id,
            ]);
        }
        return response('Demande envoyée !');
    }

    public  function afficher($id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
        return DemandeTransfertResource::make($o_demande_transfert);
    }

    public  function refuser($id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
        if ($o_demande_transfert->statut !== 'Nouvelle'){
            return response('Demamde est déja traiter');
        }
       $o_demande_transfert->update([
           'statut' => 'Refusée'
       ]);
    }

    public function livrer(Request $request,$id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
        if ($o_demande_transfert->statut !== 'Nouvelle'){
            return response('Demamde est déja traiter');
        }
        \Validator::make($request->all(),[
            'lignes'=>'required|array',
            'lignes.*.id'=>'required|exists:articles,id',
            'lignes.*.quantite_livre'=>'required|numeric|min:1',
            'magasin_sortie'=>'required',
            'magasin_sortie.value'=>'required|exists:magasins,id'
        ],[]);
        foreach ($request->lignes as $ligne){
            $o_demande_transfert_ligne = DemandeTransfertLigne::find($ligne['id']);
            $o_demande_transfert_ligne->update([
                'quantite_livre' => $ligne['quantite_livre']
            ]);
            StockService::stock_sortir($o_demande_transfert_ligne->article_id,$ligne['quantite_livre'],now()->toDateString(),DemandeTransfert::class,$o_demande_transfert->id,$o_demande_transfert->magasin_sortie_id);
        }
        $o_demande_transfert->update([
            'statut' => 'Livrée'
        ]);
        return response('Demande livrée !');
    }

    public function printDemande($id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
        $pos_session = PosSession::where('user_id',auth()->user()->id)->where('ouverte',1)->first();
        return  view('pos.demande_ticket',compact('o_demande_transfert','pos_session'));
    }

    public function accepter($id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
        if ($o_demande_transfert->statut !== 'Livrée'){
            return response('Demamde est déja traiter');
        }
        foreach ($o_demande_transfert->lignes as $ligne){
            $o_demande_transfert_ligne = DemandeTransfertLigne::find($ligne['id']);
            StockService::stock_entre($o_demande_transfert_ligne->article_id,$ligne['quantite_livre'],now()->toDateString(),DemandeTransfert::class,$o_demande_transfert->id,$o_demande_transfert->magasin_entree_id);
        }
        $o_demande_transfert->update([
            'statut' => 'Accepté'
        ]);
        return response('Demande acceptée');
    }

    public function annuler($id){
        $o_demande_transfert = DemandeTransfert::findOrFail($id);
            StockService::stock_revert(DemandeTransfert::class,$o_demande_transfert->id);
        $o_demande_transfert->update([
            'statut' => 'Annulée'
        ]);
        return response('Demande annulée');
    }
}
