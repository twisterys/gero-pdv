<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormeJuridiqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');


        DB::table('forme_juridique')->truncate();

        DB::table('forme_juridique')->insertOrIgnore([
            [
                'id' => 1,
                'nom' => 'Personne physique',
                'nom_sur_facture' => 'PP',
                'active' => '1'
            ],
            [
                'id' => 2,
                'nom' => 'Société Anonyme',
                'nom_sur_facture' => 'S.A',
                'active' => '1'
            ],
            [
                'id' => 3,
                'nom' => 'Société Anonyme Simplifiée',
                'nom_sur_facture' => 'SAS',
                'active' => '1'
            ],
            [
                'id' => 4,
                'nom' => 'Société à Responsabilité Limitée',
                'nom_sur_facture' => 'SARL',
                'active' => '1'
            ],
            [
                'id' => 5,
                'nom' => 'Auto Entrepreneur',
                'nom_sur_facture' => 'AE',
                'active' => '1'
            ],
            [
                'id' => 6,
                'nom' => 'groupement d’intérêt économique',
                'nom_sur_facture' => 'GIE',
                'active' => '1'
            ],
            [
                'id' => 7,
                'nom' => 'Société en nom collectif',
                'nom_sur_facture' => 'SNC',
                'active' => '1'
            ],
            [
                'id' => 8,
                'nom' => 'Société en Commandite par Actions',
                'nom_sur_facture' => 'SCA',
                'active' => '1'
            ],
            [
                'id' => 9,
                'nom' => 'Société en Commandite Simple',
                'nom_sur_facture' => 'SCS',
                'active' => '1'
            ],
            [
                'id' => 10,
                'nom' => 'Particulier',
                'nom_sur_facture' => 'P',
                'active' => '1'
            ]

        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $correspondance = [
            'sarl' => 4,
            'personne_physique' => 1,
            'auto_entrepreneur' => 5,
            'sa' => 2,
            'sos' => 3,
            'gie' => 6,
            'snc' => 7,
            'scs' => 9,
            'sca' => 8
        ];


//        foreach ($correspondance as $ancienneValeur => $nouveauId) {
//            DB::table('clients')
//                ->where('forme_juridique', $ancienneValeur)
//                ->update(['forme_juridique_id' => $nouveauId]);
//        }


//        foreach ($correspondance as $ancienneValeur => $nouveauId) {
//            DB::table('fournisseurs')
//                ->where('forme_juridique', $ancienneValeur)
//                ->update(['forme_juridique_id' => $nouveauId]);
//        }

    }
}
