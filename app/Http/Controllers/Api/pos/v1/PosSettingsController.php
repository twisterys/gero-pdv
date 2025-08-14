<?php

namespace App\Http\Controllers\Api\pos\v1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class PosSettingsController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'up']);
    }

    public function init(){
        $settings = \App\Models\PosSettings::first();

        $rapports = DB::table('pos_rapports')->get();

        $client = Client::first(['id as value', 'nom as label']) ?? null;

        $data = [
            "features" => [
                "ticketPrinting"=>$settings->where('key','ticket')->first()?->value,
                "autoTicketPrinting"=>$settings->where('key','autoTicketPrinting')->first()?->value,
                "priceEditing"=>$settings->where('key','modifier_prix')->first()?->value,
                "reductionEnabled"=>$settings->where('key','reduction')->first()?->value,
                "globalReductionEnabled"=>$settings->where('key','global_reduction')->first()?->value,
                "demandes"=>$settings->where('key','demandes')->first()?->value,
                "depense"=>$settings->where('key','depenses')->first()?->value,
                "history"=>$settings->where('key','historique')->first()?->value,
            ],
            "posType"=>$settings->where('key','type_pos')->first()?->value,
            "rapports"=> [
                "stock"=> $rapports->where('cle','as')->first()->actif,
                "saleByProductAndCLient"=> $rapports->where('cle','ac')->first()->actif,
                "productBySupplier"=>$rapports->where('cle','af')->first()->actif,
                "paymentsAndCredit"=>$rapports->where('cle','cr')->first()->actif,
                "treasury"=>$rapports->where('cle','tr')->first()->actif,
            ],
            "default_client" => $client,
            "url"=>URL::to('/'),
            "url_api"=>URL::to('/api/pos/v1'),
        ];

        return response()->json($data);
    }
}
