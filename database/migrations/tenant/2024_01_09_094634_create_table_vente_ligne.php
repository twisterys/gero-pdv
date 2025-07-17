<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vente_lignes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vente_id');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->unsignedBigInteger('unit_id');

            $table->string('nom_article')->nullable();
            $table->text('description')->nullable();
            $table->decimal('ht', 10, 2)->default(0);
            $table->decimal('quantite', 10, 2)->default(0);
            $table->decimal('taxe', 10, 2)->default(0);
            $table->decimal('reduction', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->enum('mode_reduction', ['fixe','pourcentage']);
            $table->string('position')->nullable();


            $table->foreign('vente_id')->references('id')->on('ventes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('article_id')->references('id')->on('articles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('unit_id')->references('id')->on('unites')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vent_lignes');

    }
};
