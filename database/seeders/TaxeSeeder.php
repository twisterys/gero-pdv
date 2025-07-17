<?php

namespace Database\Seeders;

use App\Models\Taxe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = ['0','7','10','16','20'];
        foreach ($taxes as $tax){
            Taxe::where('valeur',$tax)->firstOr(function () use ($tax){
                Taxe::create([
                    'nom' => $tax.'%',
                    'valeur' => $tax,
                    'active' => '1'
                ]);
            });
        }
    }
}
