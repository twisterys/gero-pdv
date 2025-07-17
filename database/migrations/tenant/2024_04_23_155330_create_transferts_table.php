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
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('reference');
            $table->unsignedBigInteger('magasin_entree');
            $table->unsignedBigInteger('magasin_sortie');
            $table->foreign('magasin_entree')->references('id')->on('magasins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('magasin_sortie')->references('id')->on('magasins')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferts');
    }
};
