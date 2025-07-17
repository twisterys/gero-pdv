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
        Schema::create('documents_parametres', function (Blueprint $table) {
            $table->id();
            $table->string('template');
            $table->string('image_arriere_plan')->nullable();
            $table->string('image_en_tete')->nullable();
            $table->string('image_pied_page')->nullable();
            $table->tinyInteger('afficher_image_arriere_plan')->default(0);
            $table->tinyInteger('afficher_image_en_tete')->default(0);
            $table->tinyInteger('afficher_image_pied_page')->default(0);
            $table->tinyInteger('afficher_bloc_de_signature')->default(0);
            $table->tinyInteger('afficher_total_en_chiffre')->default(0);
            $table->float('espace_pied_page')->nullable();
            $table->float('espace_en_tete')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_parametres');
    }
};
