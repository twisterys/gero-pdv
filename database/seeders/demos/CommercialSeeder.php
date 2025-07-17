<?php

namespace Database\Seeders;

use App\Models\Commercial;
use App\Services\ReferenceService;
use Illuminate\Database\Seeder;

class CommercialSeeder extends Seeder
{
    public function run(): void
    {
        for ($i=1;$i<11 ; $i++){
            Commercial::factory(1)->create(['reference' => ReferenceService::generateReference('cms')]);
            ReferenceService::incrementCompteur('cms');
        }
    }
}
