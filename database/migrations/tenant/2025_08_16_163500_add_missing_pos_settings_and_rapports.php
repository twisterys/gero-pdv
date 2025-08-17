<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_settings')) {
            $rows = [
                ['key' => 'autoTicketPrinting', 'label' => 'Impression automatique du ticket', 'value' => 0],
                ['key' => 'global_reduction', 'label' => 'Réduction globale', 'value' => 0],
                ['key' => 'cloture', 'label' => 'Clôture de caisse', 'value' => 0],
                ['key' => 'button_credit', 'label' => 'Bouton Crédit', 'value' => 1],
                ['key' => 'button_other', 'label' => 'Bouton Autre', 'value' => 1],
                ['key' => 'button_cash', 'label' => 'Bouton Espèces', 'value' => 1],
            ];
            foreach ($rows as $r) {
                if (!DB::table('pos_settings')->where('key', $r['key'])->exists()) {
                    DB::table('pos_settings')->insert([
                        'key' => $r['key'],
                        'label' => $r['label'],
                        'value' => $r['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if (Schema::hasTable('pos_rapports')) {
            if (!DB::table('pos_rapports')->where('cle', 'dl')->exists()) {
                DB::table('pos_rapports')->insert([
                    'nom' => 'Rapport Journalier',
                    'cle' => 'dl',
                    'actif' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_settings')) {
            DB::table('pos_settings')->whereIn('key', [
                'autoTicketPrinting',
                'global_reduction',
                'cloture',
                'button_credit',
                'button_other',
                'button_cash',
            ])->delete();
        }

        if (Schema::hasTable('pos_rapports')) {
            DB::table('pos_rapports')->where('cle', 'dl')->delete();
        }
    }
};
