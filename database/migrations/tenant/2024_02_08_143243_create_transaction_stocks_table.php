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
        Schema::create('transaction_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->references('id')->on('articles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('declencheur')->nullable();
            $table->float('qte_sortir',15,2)->default('0');
            $table->float('qte_entree',15,2)->default('0');
            $table->float('valeur_sortir',15,2)->nullable();
            $table->float('valeur_entrer',15,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_stocks');
    }
};
