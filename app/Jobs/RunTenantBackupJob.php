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
use App\Listeners\BackupEventSubscriber;
class RunTenantBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Tenant $tenant;
    protected string $backupName;
    protected int $backupId;
    protected array $storageConfig;
    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant, string $backupName, int $backupId, array $storageConfig)
    {
        $this->tenant = $tenant;
        $this->backupName = $backupName;
        $this->backupId = $backupId;
        $this->storageConfig = $storageConfig;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle()
    {

        try {
            config(['filesystems.disks.backup_storage' => $this->storageConfig]);

            app()->instance('current_backup_id', $this->backupId);
            config(['current_backup_id' => $this->backupId]);

            app()->instance('current_backup_filename', $this->backupName);
            config(['current_backup_filename' => $this->backupName]);

            $this->tenant->run(function () {
                Artisan::call("backup:run --filename={$this->backupName}");
            });

        } catch (\Exception $e) {
           LogService::logException($e);

            $subscriber = new BackupEventSubscriber();
            $subscriber->sendCallback('error', [
                'backup_id' => $this->backupId,
                'message' => $e->getMessage(),
                'filename' => $this->backupName,
            ]);

           throw new Exception($e->getMessage());
        }
    }
}
