<?php

namespace Database\Seeders;

use App\Models\ProduitSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduitSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProduitSettings::where('key','image')->firstOr(function (){
            ProduitSettings::create([
                'key' => 'image',
                'value' => 0
            ]);
        });
        ProduitSettings::where('key','marque')->firstOr(function (){
            ProduitSettings::create([
                'key' => 'marque',
                'value' => 0
            ]);
        });

    }
}
