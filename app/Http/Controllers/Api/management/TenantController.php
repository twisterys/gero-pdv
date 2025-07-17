<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Jobs\CreateTenantJob;
use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function creer(Request $request)
    {
        try {
            $tenant = Tenant::create([
                'id' => $request->input('instance'),
                'tenancy_db_name' => $request->input('database'),
                'date_expiration' => $request->input('date_expiration'),
            ]);

            $tenant->domains()->create(['domain' => $request->input('domain')]);
            $response = response()->json(['message' => 'En cours de création'], 200);

            CreateTenantJob::dispatch($tenant, $request->all())->onQueue('tenants');

            return $response;
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la création du tenant'], 500);
        }
    }


    public function supprimer($tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenant->delete();

            return response()->json(['message' => 'Tenant supprimé avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la suppression du tenant'], 500);
        }
    }

    public function modifier_sous_domaine(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenant->domains()->update(['domain' => $request->input("subdomain")]);

            return response()->json(['message' => 'Sous-domaine mis à jour avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la mise à jour du sous-domaine'], 500);
        }
    }

    public function modifier_date_expiration(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenant->update(['date_expiration' => $request->input('date_expiration')]);

            return response()->json(['message' => 'Date d\'expiration mise à jour avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la mise à jour de la date d\'expiration'], 500);
        }
    }
}
