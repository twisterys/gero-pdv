<?php

namespace Database\Seeders;

use App\Models\Exercice;
use App\Services\ReferenceService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exercice::where('annee',Carbon::now()->get('year'))->firstOr(function (){
            $exercise=  Exercice::create([
                'annee' => Carbon::now()->get('year'),
                'cloturee' => '0',
            ]);
            ReferenceService::generer_les_compteur($exercise->annee);
        });
    }
}
