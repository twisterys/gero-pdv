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
        Schema::table('documents_parametres', function (Blueprint $table) {
            $table->dropColumn('template');
            $table->dropColumn('afficher_image_arriere_plan');
            $table->dropColumn('afficher_image_en_tete');
            $table->dropColumn('afficher_image_pied_page');
            $table->dropColumn('afficher_bloc_de_signature');
            $table->dropColumn('espace_pied_page');
            $table->dropColumn('espace_en_tete');
            $table->foreignId('template_id')->nullable()->references('id')->on('templates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('logo')->nullable();
            $table->string('couleur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents_parametres', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
            $table->dropColumn('logo');
        });
    }
};
