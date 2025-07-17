<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LogService
{
    /**
     * Log info about exception error
     *
     * @param Exception $exception
     * @return void
     */


    public static function logException(Exception $exception, $tenant = null): void
    {
        if ($tenant === null) {
            $tenant = session()->get('tenant');
        }
        Log::channel('daily')->emergency($tenant . ' ' . $exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
    }


    public static function logExceptionImports(Exception $exception, String $reference): void
    {
        $dateTime = Carbon::now();
        $logMessage = $dateTime->toDateTimeString() .' '.$reference . ' ' . $exception->getMessage() ;
        $logFileName = 'imports-' . Carbon::now()->format('Y-m-d') . '.log';
//        $logFileName = 'imports.log';
        $path = 'public'.DIRECTORY_SEPARATOR. 'logs'.DIRECTORY_SEPARATOR.$logFileName;
        Storage::disk('external_storage')->append($path, $logMessage);
    }


    public static function createLog($tenantId, $message, $etat)
    {
        // Create a new log instance
        $log = new \App\Models\Log([
            'tenant_id' => $tenantId,
            'message' => $message,
            'etat' => $etat
        ]);

        // Save the log to the database
        $log->save();

        return $log;
    }
}
