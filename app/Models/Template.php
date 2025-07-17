<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['nom',
        'blade',
        'logo',
        'logo_largeur',
        'logo_hauteur',
        'image_arriere_plan',
        'image_en_tete',
        'image_en_tete_hauteur',
        'image_en_tete_largeur',
        'image_en_bas',
        'image_en_bas_hauteur',
        'image_en_bas_largeur',
        'image',
        'afficher_total_en_chiffre',
        'elements',
        'couleur',
        'cachet',
        'cahet_hauteur'
        , 'cachet_largeur',
    ];

    public function document_parametre()
    {
        return $this->hasOne(DocumentsParametre::class, 'template_id');
    }

    public function ventes()
    {
        return $this->hasOne(Vente::class, 'template_id');
    }

    public function achats()
    {
        return $this->hasOne(Vente::class, 'template_id');
    }


}
