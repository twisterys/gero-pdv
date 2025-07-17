<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GlobalSetting::firstOr(function (){
            GlobalSetting::create([
                'modifier_reference' => false,
                'prix_revient' => 0,
                "dashboard_date"=> "month",
                "template_par_document"=> 0,
                "code_barre"=> 0

            ]);
        });
    }
}
