<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Client;
use App\Services\LimiteService;
use Database\Factories\ClientFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ExerciceSeeder::class);
        $this->call(TaxeSeeder::class);
        $this->call(CompteursSeeder::class);
        $this->call(ReferencesSeeder::class);
        $this->call(UniteSeeder::class);
        $this->call(MagasinSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MagasinUserSeeder::class);
        $this->call(DocumentsParametresSeeder::class);
        $this->call(MethodesPaiementSeeder::class);
        $this->call(GlobalSettingsSeeder::class);
        $this->call(ComptesSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(FormeJuridiqueSeeder::class);
        $this->call(PosSettingsSeeder::class);
        $this->call(ProduitSettingsSeeder::class);
        $this->call(LimitesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(BanquesSeeder::class);
        $this->call(DashboardSeeder::class);

    }
}
