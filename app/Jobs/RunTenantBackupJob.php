<?php

namespace App\Jobs;

use App\Services\BackupLink\BackupLinkGeneratorService;
use App\Services\LogService;
use App\Listeners\BackupEventSubscriber;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class RunTenantBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const SUPPORTED_DRIVERS = ['google', 'sftp'];
    private const DISK_NAMES = [
        'google' => 'google_storage',
        'sftp' => 'sftp_storage'
    ];


    public function __construct(
        private readonly string $tenantId,
        private readonly string $backupName,
        private readonly int $backupId,
        private readonly array $storageConfig
    ) {}

    /**
     * Execute the backup job.
     */
    public function handle(
        BackupLinkGeneratorService $linkGeneratorService,
        BackupEventSubscriber $backupEventSubscriber
    ): void {
        try {
            $diskName = $this->configureDisk();
            $this->setBackupContext();
            $this->runBackup($diskName);

            $link = $this->generateDownloadLink($linkGeneratorService);
            $this->sendSuccessCallback($backupEventSubscriber, $link);

        } catch (Exception $e) {
            $this->handleBackupFailure($e);
        }
    }

    /**
     * Configure the storage disk based on driver type.
     * @throws Exception
     */
    private function configureDisk(): string
    {
        $driver = $this->getDriver();
        $this->validateDriver($driver);

        return match ($driver) {
            'google' => $this->configureGoogleDisk(),
            'sftp' => $this->configureSftpDisk(),
        };
    }

    /**
     * Configure Google Cloud Storage disk.
     */
    private function configureGoogleDisk(): string
    {
        config(['filesystems.disks.google_storage' => $this->storageConfig]);
        return self::DISK_NAMES['google'];
    }

    /**
     * Configure SFTP storage disk.
     */
    private function configureSftpDisk(): string
    {
        $sftpConfig = array_merge($this->storageConfig, [
            'port' => (int) $this->storageConfig['port'],
            'visibility' => 'public',
            'root' => '/backups'
        ]);

        config(['filesystems.disks.sftp_storage' => $sftpConfig]);
        return self::DISK_NAMES['sftp'];
    }

    /**
     * Set backup context in application container and config.
     */
    private function setBackupContext(): void
    {
        app()->instance('current_backup_id', $this->backupId);
        app()->instance('current_backup_filename', $this->backupName);

        config([
            'current_backup_id' => $this->backupId,
            'current_backup_filename' => $this->backupName
        ]);
    }

    /**
     * Execute the backup command within tenant context.
     */
    private function runBackup(string $diskName): void
    {
        try {
            tenancy()->find($this->tenantId)->run(function () use ($diskName){
                $output = Artisan::call('backup:run', [
                    '--filename' => $this->backupName,
                    '--only-to-disk' => $diskName,
                ]);

                // Check if the backup command output indicates success
                if (str_contains(Artisan::output(), 'Backup failed') || $output !== 0) {
                    throw new Exception('Backup command reported failure: ' . Artisan::output());
                }
            });

            // Verify the backup file exists
            $backupExists = Storage::disk($diskName)->exists($this->backupName);
            if (!$backupExists) {
                throw new Exception("Backup file {$this->backupName} was not created on {$diskName}");
            }
        } catch (\Exception $e) {
            // Rethrow the exception to be caught by the main try-catch block
            throw new Exception("Backup operation failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate download link based on storage driver.
     */
    private function generateDownloadLink(BackupLinkGeneratorService $linkGeneratorService): string
    {
        $driver = $this->getDriver();

        return match ($driver) {
            'google' => $this->generateGoogleLink($linkGeneratorService),
            'sftp' => $this->generateSftpLink(),
        };
    }

    /**
     * Generate Google Cloud Storage download link.
     */
    private function generateGoogleLink(BackupLinkGeneratorService $linkGeneratorService): string
    {
        try {
            $linkGenerator = $linkGeneratorService->getGeneratorFor('google');
            return $linkGenerator->generate($this->backupName, $this->storageConfig);
        } catch (\Exception $e) {
            LogService::logException($e);
            return '';
        }
    }

    /**
     * Generate SFTP download link.
     */
    private function generateSftpLink(): string
    {
        $host = $this->storageConfig['host'];
        $root = $this->storageConfig['root'] ?? '/backups';

        return "http://{$host}" . DIRECTORY_SEPARATOR . ltrim($root, '/') . DIRECTORY_SEPARATOR . $this->backupName;
    }

    /**
     * Send success callback with backup details.
     */
    private function sendSuccessCallback(BackupEventSubscriber $subscriber, string $link): void
    {
        if (empty($link)) {
            $subscriber->sendCallback('error', [
                'filename' => $this->backupName,
                'status' => 'Failed to generate link',
            ]);
        } else {
            $subscriber->sendCallback('success', [
                'filename' => $this->backupName,
                'path' => $link,
                'status' => 'success',
            ]);
        }
    }

    /**
     * Handle backup failure by logging and sending error callback.
     * @throws Exception
     */
    private function handleBackupFailure(Exception $e): void
    {
        LogService::logException($e);

        $subscriber = new BackupEventSubscriber();
        $subscriber->sendCallback('error', [
            'backup_id' => $this->backupId,
            'message' => $e->getMessage(),
            'filename' => $this->backupName,
        ]);

        throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    /**
     * Get the storage driver from configuration.
     */
    private function getDriver(): string
    {
        return $this->storageConfig['driver'] ?? 'unknown';
    }

    /**
     * Validate that the driver is supported.
     * @throws Exception
     */
    private function validateDriver(string $driver): void
    {
        if (!in_array($driver, self::SUPPORTED_DRIVERS, true)) {
            throw new Exception("Unsupported storage driver: {$driver}");
        }
    }
}
