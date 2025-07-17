<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'name' => 'Produits',
                'function_name' => 'stock',
                'blade' => 'stock',
            ],
            [
                'name' => 'Services',
                'function_name' => 'service',
                'blade' => 'service',
            ],
            [
                'name' => 'Points de vente',
                'function_name' => 'pos',
                'blade' => 'pos',
            ],
            [
                'name' => 'TVA',
                'function_name' => 'tva',
                'blade' => 'tva',
            ],
            [
                'name'=>'Recouverement',
                'function_name'=>'recouverement',
                'blade'=>'recouverement'
            ]
        ];
        foreach ($data as $dashboard){
            \DB::table('dashboards')->where('function_name',$dashboard['function_name'])->existsOr(function () use ($dashboard){
               \DB::table('dashboards')->insert($dashboard);
            });

        }
    }
}
