<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\LogService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DuplicateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $original_tenant;
    protected $duplicated_tenant;
    protected $request;

    /**
     * Create a new job instance.
     *
     * @param Tenant $duplicated_tenant
     * @param array $request
     */
    public function __construct(Tenant $original_tenant,Tenant $duplicated_tenant, array $request)
    {
        $this->original_tenant = $original_tenant;
        $this->duplicated_tenant = $duplicated_tenant;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $backupFilename = $this->original_tenant->tenancy_db_name . '-backup.zip';
            tenancy()->find($this->original_tenant->id)->run(function() use ($backupFilename) {
                $output = Artisan::call('backup:run', [
                    '--filename' => $backupFilename,
                    '--only-db'  => true,
                    '--only-to-disk' => 'local_storage',
                ]);
                if (str_contains(Artisan::output(), 'Backup failed') || $output !== 0) {
                    throw new Exception('Local backup command reported failure: ' . Artisan::output());
                }
            });

            $zipPath = storage_path("app/backups/{$backupFilename}");
            $extractPath = storage_path("app/backups/tmp_{$this->duplicated_tenant->id}");

            $zip = new \ZipArchive;
            $zip->open($zipPath);
            $zip->extractTo($extractPath);
            $zip->close();

            $sqlPath = $extractPath . '/db-dumps/mysql-' . $this->original_tenant->tenancy_db_name . '.sql';

            if (!file_exists($sqlPath)) {
                throw new Exception("SQL file not found at {$sqlPath}");
            }

            $this->duplicated_tenant->run(function () use ($sqlPath) {
                DB::unprepared(file_get_contents($sqlPath));
            });

            // cleanup
            File::delete($zipPath);
            File::deleteDirectory($extractPath);

            $this->duplicated_tenant->status = 'En production';
            $this->duplicated_tenant->save();
            $this->call_webhook($this->duplicated_tenant, 'Traitement terminé avec succès.');
        } catch (\Exception $e) {
            LogService::logException($e, $this->duplicated_tenant->id);
            $this->call_webhook($this->duplicated_tenant, 'Erreur lors du traitement.');
        }
    }

    private function call_webhook($tenant, $message = null)
    {
        $apiToken = env('INTERNAL_API_TOKEN');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
            ])->post(config('tenancy.callback_url').$tenant->id, [
                'status' => $tenant->status,
                'message' => $message,
            ]);
            if ($response->failed()) {
                Log::error("Webhook call failed for tenant {$tenant->id}: " . $response->body());
            }
        } catch (\Exception $e) {
            LogService::logException($e, $tenant->id);
        }
    }
}
