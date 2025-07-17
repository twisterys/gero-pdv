<?php

namespace Database\Seeders;

use App\Models\DocumentsParametre;
use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Template::where('blade','template1')->firstOr(function (){
            Template::create([
                'nom'=>'Classic',
                'blade' => 'template1',
                'logo'=>null,
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
            ]);
        });
        Template::where('blade','template2')->firstOr(function (){
            Template::create([
                'nom'=>'Service',
                'blade' => 'template2',
                'logo'=>null,
                'logo_hauteur' => 0,
                'logo_largeur' => 0,
                'image_arriere_plan' => null,
                'image' => 'images/documents-template2.png',
                'image_en_tete' => '',
                'image_en_tete_hauteur' => 130.0,
                'image_en_tete_largeur' => 794.0,
                'image_en_bas' => '',
                'image_en_bas_hauteur' => 130.0,
                'image_en_bas_largeur' => 794.0,
                'couleur' => '#23b67f',
                'cachet_hauteur'=>100,
                'cachet_largeur'=>100,
                'afficher_total_en_chiffre' => '0',
                'elements' => 'image_en_bas,image_en_tete'
            ]);
        });
        Template::where('blade','service2')->firstOr(function (){
            Template::create([
                'nom'=>'Service 2',
                'blade' => 'service2',
                'logo'=>null,
                'logo_hauteur' => 0,
                'logo_largeur' => 0,
                'image_arriere_plan' => null,
                'image' => '',
                'image_en_tete' => '',
                'image_en_tete_hauteur' => 130.0,
                'image_en_tete_largeur' => 794.0,
                'image_en_bas' => '',
                'image_en_bas_hauteur' => 130.0,
                'image_en_bas_largeur' => 794.0,
                'couleur' => '#cc0000',
                'afficher_total_en_chiffre' => '0',
                'elements' => 'image_en_bas,image_en_tete'
            ]);
        });
        Template::where('blade','marchandise')->firstOr(function (){
            Template::create([
                'nom'=>'Marchandise',
                'blade' => 'marchandise',
                'logo'=>null,
                'logo_hauteur' => 0,
                'logo_largeur' => 0,
                'image_arriere_plan' => null,
                'image' => '',
                'image_en_tete' => '',
                'image_en_tete_hauteur' => 0.0,
                'image_en_tete_largeur' => 0.0,
                'image_en_bas' => '',
                'image_en_bas_hauteur' => 130.0,
                'image_en_bas_largeur' => 794.0,
                'couleur' => '#cc0000',
                'afficher_total_en_chiffre' => '0',
                'elements' => 'image_en_bas'
            ]);
        });
        Template::where('blade','cachet')->firstOr(function (){
            Template::create([
                'nom'=>'Cachet',
                'blade' => 'cachet',
                'logo'=>null,
                'logo_hauteur' => 0,
                'logo_largeur' => 0,
                'image_arriere_plan' => null,
                'image' => '',
                'image_en_tete' => '',
                'image_en_tete_hauteur' => 130.0,
                'image_en_tete_largeur' => 794.0,
                'image_en_bas' => '',
                'image_en_bas_hauteur' => 130.0,
                'image_en_bas_largeur' => 794.0,
                'couleur' => '#23b67f',
                'cachet_hauteur'=>170,
                'cachet_largeur'=>170,
                'cachet'=>'',
                'afficher_total_en_chiffre' => '0',
                'elements' => 'image_en_bas,image_en_tete,cachet'
            ]);
        });

        DocumentsParametre::first()->update(['template_id' => 1]);
    }
}
