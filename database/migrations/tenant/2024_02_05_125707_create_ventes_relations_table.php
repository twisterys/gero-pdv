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
        Schema::create('ventes_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->references('id')->on('ventes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('en_relation_id')->references('id')->on('ventes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes_relations');
    }
};
