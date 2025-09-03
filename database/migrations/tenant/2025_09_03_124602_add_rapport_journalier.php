<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('rapports')->insert([
            'nom' => 'Rapport journalier',
            'route' => 'journalier',
            'description' => 'Suivi quotidien des activités commerciales et financières.',
            'type' => 'pos',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('rapports')
            ->where('nom', 'Rapport journalier')
            ->where('route', 'journalier')
            ->delete();
    }
};
