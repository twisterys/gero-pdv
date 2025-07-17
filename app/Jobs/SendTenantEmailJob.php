<?php

namespace App\Jobs;

use App\Services\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SmtpEmail;
use App\Models\SMTPSetting;

class SendTenantEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $destinations;
    protected $cc;
    protected $subject;
    protected $body;
    protected $tenant;
    protected $attachments;


    /**
     * Crée une nouvelle instance du job.
     *
     * @param  array  $destinations
     * @param  string  $subject
     * @param  string  $body
     * @return void
     */
    public function __construct($destinations, $subject, $body, $tenant, array $attachments = [],$cc = [])
    {
        $this->destinations = $destinations;
        $this->cc = $cc;
        $this->subject = $subject;
        $this->body = $body;
        $this->tenant = $tenant;
        $this->attachments = $attachments;

    }

    /**
     * Exécute le job d'envoi d'email.
     *
     * @return void
     */
    public function handle()
    {

        // Récupère les paramètres SMTP du tenant
        try {
            $smtpSettings = SmtpSetting::first();

            if (!$smtpSettings) {
                throw new \Exception("SMTP settings not found.");
            }

        } catch (\Exception $e) {
            LogService::logException($e); // Log general errors
        }


        // Update configuration directly using `config()` function
        config([
            'mail.mailers.smtp.host' => $smtpSettings->host,
            'mail.mailers.smtp.port' => $smtpSettings->port,
            'mail.mailers.smtp.username' => $smtpSettings->username,
            'mail.mailers.smtp.password' => $smtpSettings->password,
            'mail.mailers.smtp.encryption' => $smtpSettings->encryption ?? 'tls',
            'mail.from.address' => $smtpSettings->from_address ?? 'no-reply@gero.ma',
            'mail.from.name' => $smtpSettings->from_name ?? "gero"
        ]);
        Mail::to($this->destinations)
            ->cc($this->cc)
            ->send(new SmtpEmail($this->subject, $this->body, $this->attachments));
    }
}
