<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function stats(Request $request)
    {

        $tenants = $request->input('tenants', []);
        $day = $request->input('day', now()->toDateString());
        $results = [];

        foreach ($tenants as $tenantName) {
            try {
                $tenant = Tenant::where('id', $tenantName)->first();

                if (!$tenant) {
                    throw new \Exception("Tenant non trouvé");
                }

                $stats = [
                    'achats_count' => null,
                    'ventes_count' => null,
                    'depenses_count' => null,
                    'paiements_count' => null,
                ];

                //Passage par reference pour éviter les problèmes de closure
                $tenant->run(function () use (&$stats, $day) {
                    $stats['achats_count'] = \App\Models\Achat::whereDate('created_at', $day)->count();
                    $stats['ventes_count'] = \App\Models\Vente::whereDate('created_at', $day)->count();
                    $stats['depenses_count'] = \App\Models\Depense::whereDate('created_at', $day)->count();
                    $stats['paiements_count'] = \App\Models\Paiement::whereDate('created_at', $day)->count();
                });

                $results[] = array_merge(['tenant' => $tenantName], $stats);
            } catch (\Exception $e) {
                $results[] = [
                    'tenant' => $tenantName,
                    'achats_count' => null,
                    'ventes_count' => null,
                    'depenses_count' => null,
                    'paiements_count' => null,
                ];
            }
        }

        return response()->json($results);
    }
}
