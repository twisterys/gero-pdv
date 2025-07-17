<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LimitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limites = ['stock','magasin_extra','users','pos','commerciaux','methode_livraison','affaires','activites','abonnement' ];
        foreach ($limites as $limite){
            if (!\DB::table('limites')->where('key',$limite)->first()){
                \DB::table('limites')->insert(['key'=>$limite,'value'=>0]);
            }
        }

    }
}
