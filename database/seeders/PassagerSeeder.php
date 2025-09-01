<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormeJuridique;
use App\Models\Client;
use App\Services\ReferenceService;

class PassagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer la forme juridique "Particulier"
        $formeJuridique = FormeJuridique::where('nom', 'Particulier')->first();

        if ($formeJuridique) {
            $reference = ReferenceService::generateReference('clt');

            $clientExists = Client::where('forme_juridique_id', $formeJuridique->id)
                ->where('reference', $reference)
                ->where('nom', 'Passager')
                ->exists();

            if (!$clientExists) {
                Client::create([
                    'forme_juridique_id' => $formeJuridique->id,
                    'reference' => $reference,
                    'nom' => 'Passager',
                ]);

                ReferenceService::incrementCompteur('clt');
            }
        }
    }
}
