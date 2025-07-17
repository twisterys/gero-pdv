<?php

namespace Database\Seeders;

use App\Models\Unite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unites = [
            [
                'nom'=>'U',
                'defaut'=>1
            ]
        ];
        foreach ($unites as $unite){
            Unite::where('nom',$unite['nom'])->firstOr(function () use ($unite){
                Unite::create($unite);
            });
        }

    }
}
