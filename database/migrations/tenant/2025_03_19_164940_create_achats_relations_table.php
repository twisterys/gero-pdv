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
        Schema::create('achats_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->references('id')->on('achats')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('en_relation_id')->references('id')->on('achats')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achats_relations');
    }
};
