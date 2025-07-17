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
        Schema::create('transfert_lignes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfert_id');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->float('qte',15,2)->default('0');
            $table->foreign('transfert_id')->references('id')->on('transferts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('article_id')->references('id')->on('articles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_lignes');
    }
};
