<?php

namespace App\Jobs;

use App\Models\InformationEntreprise;
use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;



    protected $tenant;
    protected $request;

    /**
     * Create a new job instance.
     *
     * @param Tenant $tenant
     * @param array $request
     */
    public function __construct(Tenant $tenant, array $request)
    {
        $this->tenant = $tenant;
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
            $this->migrate($this->tenant);
            $this->seed($this->tenant);
            if (!empty($this->request['entreprise'])) {
                $this->init_tenant_info($this->tenant, $this->request['entreprise']);
            }
            if (!empty($this->request['config'])) {
                $this->init_tenant_configuration($this->tenant, $this->request['config']);
            }
            $this->tenant->status = 'En production';
            $this->tenant->save();
            $this->call_webhook($this->tenant, 'Traitement terminé avec succès.');
        } catch (\Throwable $e) {
            LogService::logException($e, $this->tenant->id);
            $this->call_webhook($this->tenant, 'Erreur lors du traitement.');
            return;
        }
    }

    private function init_tenant_info($tenant, $clientData)
    {
        try {
            $tenant->run(function () use ($clientData) {
                InformationEntreprise::create($clientData);
            });
        } catch (\Exception $e) {
            $tenant->status = "Echec d'initialisation des informations du tenant";
            $tenant->save();
            LogService::logException($e);
            throw new \Exception("Echec d'initialisation des informations pour le tenant {$tenant->id}");
        }
    }

    private function migrate($tenant)
    {
        try{
            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id]
            ]);
        }catch (\Exception $e) {
            $tenant->status = "Echec de migration";
            $tenant->save();
            LogService::logException($e);
            throw new \Exception("Echec de migration pour le tenant {$tenant->id}");
        }
    }

    private function seed($tenant)
    {
        try{
            Artisan::call('tenants:seed', [
                '--tenants' => [$tenant->id],
                '--force' => true,
            ]);
        }catch (\Exception $e) {
            $tenant->status = "Echec de seed";
            $tenant->save();
            LogService::logException($e);
            throw new \Exception("Echec de seed pour le tenant {$tenant->id}");
        }
    }

    private function init_tenant_configuration($tenant, $configData)
    {
        try {
            $tenant->run(function () use ($configData) {
                // Traitement des modules
                $modules = json_decode($configData['modules']);
                foreach ($modules as $key => $module) {
                    \App\Models\Module::where('type', $key)->update([
                        'stock_action' => $module->stock,
                        'active' => $module->active,
                        'action_paiement' => $module->payable,
                    ]);
                }

                // Traitement des limites
                $limites = json_decode($configData['limites']);
                foreach ($limites as $key => $limite) {
                    \DB::table('limites')->where('key', $key)->update([
                        'value' => $limite
                    ]);
                }

                // Traitement du dashboard par défaut
                if (\DB::table('dashboards')->where('function_name', $configData['dashboard'])->exists()) {
                    \App\Models\GlobalSetting::first()?->update(['dashboard' => $configData['dashboard']]);
                }

                // Traitement des rapports
                $rapports = json_decode($configData['rapports']);
                if (is_array($rapports) && count($rapports)) {
                    foreach ($rapports as $rapport) {
                        \App\Models\Rapport::where('nom', $rapport->nom)->existsOr(function () use ($rapport) {
                            \App\Models\Rapport::create([
                                'nom' => $rapport->nom,
                                'type' => $rapport->type,
                                'description' => $rapport->description,
                                'route' => $rapport->route,
                            ]);
                        });
                    }
                }
            });
        } catch (\Exception $e) {
            $tenant->status = "Echec d'initialisation de la configuration du tenant";
            $tenant->save();
            LogService::logException($e);
            throw new \Exception("Echec d'initialisation de la configuration pour le tenant {$tenant->id}");
        }
    }

    private function call_webhook($tenant, $message = null)
    {
        $apiToken = env('INTERNAL_API_TOKEN');
        $base = rtrim((string) config('tenancy.callback_url'), '/');
        if (!$base) {
            \Log::warning('TENANCY_CALLBACK_URL manquante, webhook ignoré.');
            return;
        }
        $url = $base . '/' . $tenant->id;
        $cbHost = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if ($cbHost && $appHost && strcasecmp($cbHost, $appHost) === 0) {
            \Log::warning("Webhook ignoré (même hôte): {$url}");
            return;
        }

        try {
            $response = \Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiToken])
                ->post($url, ['status' => $tenant->status, 'message' => $message]);

            if ($response->failed()) {
                \Log::error("Webhook call failed for tenant {$tenant->id}: " . $response->body());
            }
        } catch (\Throwable $e) {
            \App\Services\LogService::logException($e, $tenant->id);
        }
    }
}
