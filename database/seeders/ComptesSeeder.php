<?php

namespace Database\Seeders;

use App\Models\Compte;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComptesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Compte::where('nom','Caisse')->firstOr(function (){
            Compte::create([
                'nom' => 'Caisse',
                'type' => 'caisse',
                'statut' => 1,
                'principal' => 1
            ]);
        });
    }
}
