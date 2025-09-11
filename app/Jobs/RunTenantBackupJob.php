<?php

namespace App\Jobs;

use App\Services\BackupLink\BackupLinkGeneratorService;
use App\Services\LogService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
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
        BackupLinkGeneratorService $linkGeneratorService
    ): void {
        try {
            // Run local and remote backup
            $this->runLocalBackup();
            $diskName = $this->configureDisk();
            $driver = $this->getDriver();
            $this->runRemoteBackup($diskName);

            // If both are successful, generate link and send success callback
            $link = $this->generateDownloadLink($linkGeneratorService);

            if($link){
                $backupExists = Storage::disk($diskName)->exists($this->backupName);
                if (!$backupExists) {
                    $localBackupExists = Storage::disk('local_storage')->exists($this->backupName);

                    if (!$localBackupExists) {
                        throw new Exception("No backup was found localy or remotely");
                    }

                    $this->sendCallback('success', [
                        'backup_id' => $this->backupId,
                        'filename' => $this->backupName,
                        'message' => 'La sauvegarde a bien été créée localement, mais le transfert vers le stockage distant a échoué.',
                        'path' => url('api/download/backup/' . $this->backupName),
                        'driver' => 'local'
                    ]);
                }

                // delete local backup after successful remote backup
                Storage::disk('local_storage')->delete($this->backupName);
                $this->sendCallback('success', [
                    'backup_id' => $this->backupId,
                    'filename' => $this->backupName,
                    'path' => $link,
                    'driver' => $driver
                ]);
            }

        } catch (Exception $e) {

            LogService::logException($e);

            // Check if local backup file exists
            $localBackupExists = Storage::disk('local_storage')->exists($this->backupName);
            if ($localBackupExists) {
                // If local backup exists, send success callback with local path
                $this->sendCallback('success', [
                    'backup_id' => $this->backupId,
                    'filename' => $this->backupName,
                    'message' => 'Sauvegarde créée localement mais l\'envoi vers le stockage distant a échoué : ' . $e->getMessage(),
                    'path' => url('api/download/backup/' . $this->backupName),
                    'driver' => 'local'
                ]);
                return;
            }
            $this->sendCallback('error', [
                'backup_id' => $this->backupId,
                'message' => $e->getMessage(),
                'filename' => $this->backupName,
            ]);

        }
    }

    /**
     * Run local backup command to create the backup file.
     * @throws Exception
     */
    private function runLocalBackup(): void
    {
        tenancy()->find($this->tenantId)->run(function () {
            $output = Artisan::call('backup:run', [
                '--filename' => $this->backupName,
                '--only-db' => true,
                '--only-to-disk' => 'local_storage',
            ]);
            // Check if the backup command output indicates success
            if (str_contains(Artisan::output(), 'Backup failed') || $output !== 0) {
                throw new Exception('Local backup command reported failure: ' . Artisan::output());
            }
        });
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
     * Execute the backup command within tenant context.
     */
    private function runRemoteBackup(string $diskName): void
    {
        tenancy()->find($this->tenantId)->run(function () use ($diskName){
            $output = Artisan::call('backup:run', [
                '--filename' => $this->backupName,
                '--only-to-disk' => $diskName,
            ]);

            // Check if the backup command output indicates success
            if (str_contains(Artisan::output(), 'Backup failed') || $output !== 0) {
                throw new Exception('Remote backup command reported failure: ' . Artisan::output());
            }
        });
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

    /**
     * Envoie le statut au serveur de contrôle.
     */
    private function sendCallback(string $status, array $data = [])
    {
        $backupId = $data['backup_id'] ;
        if (!$backupId) {
            LogService::logException(new \Exception("Unable to send backup callback: backup_id not found."));
            return false;
        }
        $filename = $status === "success" ? $data['filename'] : null;
        $path = $status === "success" ? $data['path'] : null;
        $message = $data['message'] ?? null;
        $driver = $data['driver'] ?? null;

        try {
            $apiToken = env('INTERNAL_API_TOKEN');
            $callbackUrl = config('backup.callback_url');

            if (empty($apiToken) || empty($callbackUrl)) {
                LogService::logException(new \Exception("Missing API token or callback URL for backup callback"));
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
            ])->post($callbackUrl, [
                'backup_id' => $backupId,
                'status' => $status,
                'filename' => $filename,
                'path' => $path,
                'message' => $message,
                'driver' => $driver,
            ]);

            if (!$response->successful()) {
                LogService::logException(new \Exception("Backup callback failed with status: " . $response->status()));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            LogService::logException($e, [
                'message' => 'Failed to send backup callback to Gero Control',
                'backup_id' => $backupId,
                'status' => $status,
                'filename' => $filename,
            ]);
            return false;
        }
    }
}
