<?php

namespace App\Listeners;

use App\Services\LogService;
use Illuminate\Support\Facades\Http;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\BackupWasSuccessful;

class BackupEventSubscriber
{
    /**
     * Create the event listener.
     */
    public function handleBackupSuccess(BackupWasSuccessful $event)
    {
    }

    /**
     * Gère l'événement d'échec de la sauvegarde.
     */
    public function handleBackupFailure(BackupHasFailed $event)
    {
        // Get the filename from the application container or config
        $filename = app('current_backup_filename') ?? config('current_backup_filename');

        $this->sendCallback('error', [
            'message' => "Sauvegarde échouée : Paramètres de stockage incorrects ou vides" ,
            'filename' => $filename,
        ]);
    }

    /**
     * Envoie le statut au serveur de contrôle.
     */
    public function sendCallback(string $status, array $data = [])
    {
        // Try to get backup ID from the application container first
        $backupId = app('current_backup_id') ?? config('current_backup_id');

        if (!$backupId) {
            LogService::logException(new \Exception("Unable to send backup callback: current_backup_id not found."));
            return false;
        }

        // Extract filename from data if present
        $filename = $status === "success" ? $data['filename'] : null;
        $path = $status === "success" ? $data['path'] : null;
        $message = $data['message'] ?? null;

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

    /**
     * Enregistre les écouteurs pour l'abonné.
     */
    public function subscribe($events)
    {
        $events->listen(
            BackupWasSuccessful::class,
            [BackupEventSubscriber::class, 'handleBackupSuccess']
        );

        $events->listen(
            BackupHasFailed::class,
            [BackupEventSubscriber::class, 'handleBackupFailure']
        );
    }
}
