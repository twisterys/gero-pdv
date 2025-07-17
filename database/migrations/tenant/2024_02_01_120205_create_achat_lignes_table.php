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
        Schema::create('achat_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->references('id')->on('achats')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('article_id')->nullable()->references('id')->on('articles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('unite_id')->references('id')->on('unites')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nom_article');
            $table->text('description')->nullable();
            $table->decimal('ht',10,2);
            $table->decimal('quantite',10,2)->nullable();
            $table->decimal('taxe',10,2)->nullable();
            $table->decimal('reduction',10,2)->nullable();
            $table->decimal('total_ttc',10,2)->nullable();
            $table->enum('mode_reduction',['fixe','pourcentage']);
            $table->integer('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_lignes');
    }
};
