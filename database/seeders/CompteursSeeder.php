<?php

namespace Database\Seeders;

use App\Models\Compteur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompteursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compteurs = [
            [
                'type' => 'clt',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'fr',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'cms',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'art',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'dv',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'fa',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'av',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'bl',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                'type' => 'bc',
                'annee' => now()->year,
                'compteur' => 1,
            ],

            [
                'type' => 'br',
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "dva",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "bca",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "bla",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "bra",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "faa",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "ava",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "fp",
                'annee' => now()->year,
                'compteur' => 1,
            ],
            [
                "type" => "dpa",
                'annee' => now()->year,
                'compteur' => 1,
            ]
        ];
        foreach ($compteurs as $compteur){
            Compteur::where('type',$compteur['type'])->where('annee',$compteur['annee'])->firstOr(function () use($compteur){
                Compteur::create($compteur);
            });
        }
    }
}





