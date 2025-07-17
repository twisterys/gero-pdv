<?php

use App\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('templates', function () {
            Template::create([
                'nom' => 'Classic compact',
                'blade' => 'classic_compact',
                'logo' => null,
                'logo_hauteur' => 0,
                'logo_largeur' => 0,
                'image_arriere_plan' => null,
                'image' => 'images/documents-template1.png',
                'image_en_tete' => '',
                'image_en_tete_hauteur' => 130.0,
                'image_en_tete_largeur' => 794.0,
                'image_en_bas' => '',
                'image_en_bas_hauteur' => 130.0,
                'image_en_bas_largeur' => 794.0,
                'couleur' => '#3a5562',
                'afficher_total_en_chiffre' => '0',
                'elements' => 'image_en_bas,image_en_tete'
            ])->save();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function () {
            Template::where('blade', 'classic_compact')->delete();
        });
    }
};
