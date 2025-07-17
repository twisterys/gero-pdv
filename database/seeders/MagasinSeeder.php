<?php

namespace Database\Seeders;

use App\Models\Magasin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MagasinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Magasin::where('reference','LC-1')->firstOr(function (){
            Magasin::create([
                'reference' => 'LC-1',
                'nom' => 'Magasin 1',
                'adresse' => 'Castilla',
                'type_local' => 1
            ]);
        });
    }
}
