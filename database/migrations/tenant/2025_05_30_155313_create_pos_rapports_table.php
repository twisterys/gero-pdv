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
        Schema::create('pos_rapports', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('cle')->nullable();
            $table->boolean('actif')->default(false);
            $table->timestamps();
        });

        DB::table('pos_rapports')->insert([
            ['nom' => 'Rapport Article/Client', 'cle' => 'ac','actif' => true],
            ['nom' => 'Rapport Stock', 'cle' => 'as', 'actif' => true ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_settings');
    }
};
