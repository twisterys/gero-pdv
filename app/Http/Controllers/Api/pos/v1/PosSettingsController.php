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

    public function init()
    {
        // Load all settings as a collection (some code elsewhere relies on collection methods)
        $settings = \App\Models\PosSettings::all();

        // Load rapports once
        $rapports = DB::table('pos_rapports')->get();

        // Default client (nullable)
        $client = Client::first(['id as value', 'nom as label']) ?? null;

        // Helper to fetch boolean setting by key safely
        $bool = function (string $key, bool $default = false) use ($settings): bool {
            $val = $settings->firstWhere('key', $key)?->value ?? $default;
            return (bool) $val;
        };

        // Helper to fetch rapport actif by cle safely
        $rapport = function (string $cle, bool $default = false) use ($rapports): bool {
            $row = $rapports->firstWhere('cle', $cle);
            return (bool) ($row?->actif ?? $default);
        };

        $data = [
            'features' => [
                'ticketPrinting' => $bool('ticket', false),
                'autoTicketPrinting' => $bool('autoTicketPrinting', false),
                'priceEditing' => $bool('modifier_prix', false),
                'reductionEnabled' => $bool('reduction', false),
                'globalReductionEnabled' => $bool('global_reduction', false),
                'demandes' => $bool('demandes', false),
                'history' => $bool('historique', false),
                'depense' => $bool('depenses', false),
                'cloture' => $bool('cloture', false),
                'rebut' => $bool('rebut', false),
            ],
            'rapports' => [
                'stock' => $rapport('as', false),
                'saleByProductAndCLient' => $rapport('ac', false),
                'productBySupplier' => $rapport('af', false),
                'paymentsAndCredit' => $rapport('cr', false),
                'treasury' => $rapport('tr', false),
                'daily' => $rapport('dl', false),
            ],
            'buttons' => [
                'credit' => $bool('button_credit', false),
                'other' => $bool('button_other', false),
                'cash' => $bool('button_cash', false),
            ],
            'defaultClient' => $client,
            'posType' => $settings->firstWhere('key', 'type_pos')?->value ?? 'caisse',
            'url' => URL::to('/'),
            'apiUrl' => URL::to('/api/pos/v1'),
        ];

        return response()->json($data);
    }
}
