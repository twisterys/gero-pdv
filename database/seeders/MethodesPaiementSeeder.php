<?php

namespace Database\Seeders;

use App\Models\MethodesPaiement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MethodesPaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methodes = [
            ['key' => 'especes','nom' => 'Espèces', 'actif' => true, 'defaut' => true],
            ['key' => 'cheque','nom' => 'Chèque', 'actif' => true, 'defaut' => false],
            ['key' => 'carte','nom' => 'Carte bancaire', 'actif' => true, 'defaut' => false],
            ['key' => 'tpe','nom' => 'TPE', 'actif' => true, 'defaut' => false],
            ['key' => 'virement','nom' => 'Virement bancaire', 'actif' => true, 'defaut' => false],
            ['key' => 'lcn','nom' => 'LCN', 'actif' => true, 'defaut' => false],
        ];

        foreach ($methodes as $methode){
            MethodesPaiement::where('key',$methode['key'])->firstOr(function () use ($methode){
                MethodesPaiement::create($methode);
            });
        }
    }
}
