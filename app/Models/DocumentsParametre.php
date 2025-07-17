<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsParametre extends Model
{
    use HasFactory;


    protected $fillable = [
        'image_arriere_plan',
        'image_en_tete',
        'image_pied_page',
        'afficher_total_en_chiffre',
        'template_id',
        'logo',
        'couleur',
        'cachet',
    ];

    public function template(){
        return $this->belongsTo(Template::class);
    }
}
