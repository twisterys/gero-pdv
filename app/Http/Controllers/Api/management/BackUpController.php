<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Jobs\RunTenantBackupJob;
use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackUpController extends Controller
{
    public function backup(Request $request)
    {

        $tenantsToBackup = $request->input('tenants', []);

        $results = [];


        foreach ($tenantsToBackup as $tenant) {
            $tenantId = $tenant['tenant_id'];
            $backupId = $tenant['backup_id'];
            $storageConfig = $tenant['storage_config'] ?? null;
            try {
                $tenantModel = Tenant::find($tenantId);
                if (!$tenantModel) {
                    $results[] = [
                        'tenant_id' => $tenantId,
                        'backup_id' => $backupId,
                        'status' => 'error',
                        'message' => 'Tenant not found',
                    ];
                    continue;
                }
                $timestamp = now()->format('Y-m-d-H-i');
                $backupName = "{$tenantModel->tenancy_db_name}-{$timestamp}.zip";


                RunTenantBackupJob::dispatch($tenantModel->id, $backupName, $backupId,$storageConfig)->onQueue('backups');


                $results[] = [
                    'tenant_id' => $tenantModel->id,
                    'backup_id' => $backupId,
                    'status' => 'queued',
                ];

            } catch (\Exception $e) {
                LogService::logException($e);
                $results[] = [
                    'tenant_id' => $tenantId,
                    'backup_id' => $backupId,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }
        return response()->json([
            'results' => $results,
        ]);
    }


    public function download($filename)
    {
        $localBackupExists = Storage::disk('local_storage')->exists($filename);
        if ($localBackupExists) {
            return Storage::disk('local_storage')->download($filename);
        }
    }
}
