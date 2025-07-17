<?php

namespace App\Console\Commands;


use App\Models\Abonnement;
use App\Models\abonnementSettings;
use App\Models\Tenant;
use App\Services\LogService;
use App\Services\SmtpService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SendAbonnementEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abonnement:send-emails {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Envoyer les notifications d'abonnements par email ";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::all();
        $smtpService = new SmtpService();
        foreach ($tenants as $tenant) {
            try {
                //Get abonnement settings
                $abonnements_settings = AbonnementSettings::first();

                if (!$abonnements_settings) {
                    $this->info('No abonnement settings found.');
                    throw new \Exception('No abonnement settings found.');
                }
                // Extract settings
                $emails = explode(';', $abonnements_settings->emails); // Convert semicolon-separated emails to an array
                $content = $abonnements_settings->content;
                $subject = $abonnements_settings->subject;
                $notifierClient = $abonnements_settings->notifier_client;
                // Get Abonnements
                $abonnements = Abonnement::with(['client','article'])->get();
                foreach ($abonnements as $abonnement) {
                    $daysUntilExpiration = Carbon::parse($abonnement->date_expiration)->diffInDays(Carbon::now());
                    if ($daysUntilExpiration <= 15) {
                        $emailBody = str_replace(
                            [
                                '[CLIENT]',
                                '[TYPE]',
                                '[TITRE]',
                                '[PRIX]',
                                '[DATE_EXPIRATION]',
                                '[EXPIRE_DANS]'
                            ],
                            [
                                $abonnement->client->nom ,
                                $abonnement->article->designation ,
                                $abonnement->titre,
                                $abonnement->prix ?? null,
                                $abonnement->date_expiration,
                                $daysUntilExpiration
                            ],
                            $content
                        );

                        $smtpService->send($emails, $subject, $emailBody,$tenant->id);
                        if ($notifierClient == 1 && isset($abonnement->client->email)) {
                            $smtpService->send($abonnement->client->email, $subject, $emailBody ,$tenant->id);
                        }
                    }
                }
            } catch (\Exception $e) {
                LogService::logException($e);
            }

        }
        $this->info('Abonnement emails sent to queue.');

    }
}
