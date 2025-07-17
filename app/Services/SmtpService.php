<?php
namespace App\Services;

use App\Jobs\SendTenantEmailJob;
use App\Models\Tenant;

class SmtpService
{
    /**
     * Envoie un email en utilisant la queue.
     *
     * @param mixed $destinations Un ou plusieurs destinataires
     * @param string $subject Sujet de l'email
     * @param string $body Corps de l'email en HTML
     * @return bool
     */
    public function send($destinations, $subject, $body, $tenant_id, array $attachments = [], $cc = [])
    {
        $destinations = is_array($destinations) ? $destinations : [$destinations];
        try {
            $tenant = Tenant::where('id', $tenant_id)->first();
            SendTenantEmailJob::dispatch($destinations, $subject, $body, $tenant, $attachments,$cc);
            return true;
        } catch (\Exception $e) {
            LogService::logException($e);
            return false;
        }
    }
}
