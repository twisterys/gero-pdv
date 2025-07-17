<?php

namespace Database\Seeders;

use App\Models\MagasinUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MagasinUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MagasinUser::where('user_id','1')->firstOr(function (){
            MagasinUser::create([
                'user_id' => 1,
                'magasin_id' => 1,
            ]);
        });
    }

}
