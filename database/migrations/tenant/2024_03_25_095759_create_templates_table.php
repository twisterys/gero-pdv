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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('blade');
            $table->string('logo')->nullable();
            $table->decimal('logo_largeur')->default(0);
            $table->decimal('logo_hauteur')->default(0);
            $table->string('image_arriere_plan')->nullable();
            $table->string('image_en_tete')->nullable();
            $table->decimal('image_en_tete_hauteur')->default(0);
            $table->decimal('image_en_tete_largeur')->default(0);
            $table->string('image_en_bas')->nullable();
            $table->decimal('image_en_bas_hauteur')->default(0);
            $table->decimal('image_en_bas_largeur')->default(0);
            $table->string('image')->nullable();
            $table->boolean('afficher_total_en_chiffre')->default(0);
            $table->string('elements')->nullable();
            $table->string('couleur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
