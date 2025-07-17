<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $annees = DB::table('compteurs')->distinct()->pluck('annee');
        foreach ($annees as $annee) {
            $exists = DB::table('compteurs')->where('annee', $annee)->where('type','fpa')->exists();
            if (!$exists) {
                DB::table('compteurs')->insert([
                    'annee' => $annee,
                    'type' => 'fpa',
                    'compteur' => 1
                ]);
            }
        }

        $reference = DB::table('references')->where('type','fpa')->first();
        if (!$reference) {
            DB::table('references')->insert([
                'template' => 'FPA-[n]',
                'longueur_compteur' => 4,
                'nom' => __('achats.fpa',[],'fr'),
                'type' => 'fpa',
            ]);
        }


        $module = DB::table('modules')->where('type','fpa')->first();
        if (!$module) {
            DB::table('modules')->insert([
                'active' => 1,
                'type' => 'fpa',
                'action_paiement' => null,
                'action_stock' => null,
                'stock_action' => null,
            ]);
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
