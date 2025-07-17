<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Http\Request;

class LimiteController extends Controller
{
    public function liste($tenantId)
    {

        try {
            $tenant = Tenant::findOrFail($tenantId);

            $limites = $tenant->run(function () {
                return \DB::table('limites')
                    ->select('id', 'key', 'value')
                    ->get();
            });

            return response()->json(['limites' => $limites], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la récupération des limites'], 500);
        }
    }

    public function sauvegarder(string $tenantId, Request $request)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);

            $tenant->run(function () use ($request) {
                $limites = $request->input('limites', []);

                foreach ($limites as $key => $value) {
                    \DB::table('limites')
                        ->where('key', $key)
                        ->update([
                            'value' => $value,
                        ]);
                }
            });

            return response()->json(['message' => 'Limites mises à jour avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la mise à jour des limites'], 500);
        }
    }

}

