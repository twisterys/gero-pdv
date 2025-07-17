<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchatLigne extends Model
{
    use HasFactory;

    public const DECLENCHEUR = 'AchatLigne';

    protected $fillable = [
       'achat_id',
       'article_id',
       'unite_id',
       'nom_article',
       'description',
       'ht',
       'quantite',
       'taxe',
       'reduction',
       'total_ttc',
       'mode_reduction',
       'position',
       'magasin_id',
    ];
    public function achat()
    {
        return $this->belongsTo(Achat::class, 'achat_id');
    }
    public function unite()
    {
        return $this->belongsTo(Unite::class ,'unite_id');
    }

    public function article(){
        return $this->belongsTo(Article::class);
    }
}
