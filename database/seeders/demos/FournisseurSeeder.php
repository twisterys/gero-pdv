<?php

namespace Database\Seeders;

use App\Models\Fournisseur;
use App\Services\ReferenceService;
use Illuminate\Database\Seeder;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        for ($i=1;$i<11 ; $i++){
            Fournisseur::factory(1)->create(['reference' => ReferenceService::generateReference('fr')]);
            ReferenceService::incrementCompteur('fr');
        }
    }
}
