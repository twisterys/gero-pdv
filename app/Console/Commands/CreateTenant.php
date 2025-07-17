<?php

namespace App\Console\Commands;
use App\Http\Controllers\InformationEntrepriseController;
use App\Models\GlobalSetting;
use App\Models\InformationEntreprise;
use App\Models\Module;
use App\Models\Rapport;
use App\Models\Status;
use App\Services\LimiteService;
use App\Services\LogService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Files\Disk;
use Stancl\Tenancy\Facades\Tenancy;


class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:tenant {param}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creation et migration du tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $instance = $this->argument('param');
        if ($instance) {
            $tenant = Tenant::where('id', $instance)->first();
            if ($tenant) {
                try {
                    $tenant->status = 'En production';
                    $tenant->save();
                    $this->migrate($tenant);
                    $this->seed($tenant);
                    $this->make_directory($tenant);
                    $this->init_tenant_info($tenant);
                    $this->init_tenant_configuration($tenant);

                } catch (\Exception $e) {
                    $tenant->status = 'Échec de migration ou seeding';
                    $tenant->save();
                    LogService::logException($e, $instance);
                }
            }
        }
    }

    public function make_directory($tenant)
    {
        try {
            $path = config('filesystems.disks.external_storage.root').DIRECTORY_SEPARATOR.$tenant->id.DIRECTORY_SEPARATOR.'public';
            mkdir($path.DIRECTORY_SEPARATOR.'articles', 0775, true);
            mkdir($path.DIRECTORY_SEPARATOR.'importations', 0775, true);
            mkdir($path.DIRECTORY_SEPARATOR.'documents', 0775, true);
            mkdir($path.DIRECTORY_SEPARATOR.'logs', 0775, true);
            mkdir($path.DIRECTORY_SEPARATOR.'db_backups', 0775, true);
        } catch (\Exception $e) {
            $tenant->status = "Echec de creation des dossiers";
            $tenant->save();
            LogService::logException($e,$this->argument('param'));
            }

    }

    public function migrate($tenant)
    {
        try{
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id]
        ]);
        }catch (\Exception $e) {
            $tenant->status = "Echec de migration";
            $tenant->save();
            LogService::logException($e, $this->argument('param'));
        }
    }

    public function seed($tenant)
    {

        try{
            Artisan::call('tenants:seed', [
                '--tenants' => [$tenant->id],
                '--force' => true,
            ]);
        }catch (\Exception $e) {
            $tenant->status = "Echec de seed";
            $tenant->save();
            LogService::logException($e, $this->argument('param'));
        }

    }

    public function init_tenant_info($tenant)
    {
        try {
            $client = DB::table('clients')->where('id', $tenant->client_id)->first();
            if ($client) {
                $tenant->run(function () use ($client) { InformationEntreprise::create(
                    (array)$client
                ); });
            }
        } catch (\Exception $e) {
            LogService::logException($e, $this->argument('param'));
        }
    }

    public function init_tenant_configuration($tenant){
        try {
            $config = DB::table('configs')->find($tenant->config_id);
            $rapports = json_decode($config->rapports);
            $o_rapports = null;
            if (count($rapports)){
                $o_rapports = Rapport::whereIn('id',$rapports)->get();
            }
            if ($config) {
                $tenant->run(function () use ($config,$o_rapports) {
                    $modules = json_decode($config->modules);
                    foreach ($modules as $key => $module){
                        Module::where('type',$key)->update([
                            'stock_action' => $module->stock,
                            'active' => $module->active,
                            'action_paiement' => $module->payable,
                        ]);
                    }
                    $limites = json_decode($config->limites);
                    foreach ($limites as $key => $limite){
                        DB::table('limites')->where('key',$key)->update([
                            'value'=>$limite
                        ]);
                    }
                    if (DB::table('dashboards')->where('function_name',$config->dashboard)->exists()){
                        GlobalSetting::first()?->update(['dashboard' =>$config->dashboard ]);
                    }
                    if ($o_rapports){
                        foreach ($o_rapports as $rapport){
                            Rapport::where('nom',$rapport->nom)->existsOr(function () use($rapport){
                                Rapport::create([
                                   'nom'=>$rapport->nom,
                                   'type'=>$rapport->type,
                                   'description'=>$rapport->description,
                                   'route'=>$rapport->route,
                                ]);
                            });
                        }
                    }
                });
            }
        } catch (\Exception $e) {
            LogService::logException($e, $this->argument('param'));
        }
    }
}
