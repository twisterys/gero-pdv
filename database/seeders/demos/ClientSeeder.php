<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Services\ReferenceService;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        for ($i=1;$i<11 ; $i++){
            Client::factory(1)->create(['reference' => ReferenceService::generateReference('clt')]);
            ReferenceService::incrementCompteur('clt');
        }
    }
}
