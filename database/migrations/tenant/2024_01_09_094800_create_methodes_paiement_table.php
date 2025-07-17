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
        Schema::create('methodes_paiement', function (Blueprint $table) {
            $table->id();
            $table->index('key');
            $table->string('key');
            $table->string('nom');
            $table->boolean('actif')->default(false);
            $table->boolean('defaut')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methodes_paiement');
    }
};
