<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenteLigne extends Model
{
    use HasFactory;
    protected $table = 'vente_lignes';
    protected $fillable = [
        'nom_article',
        'description',
        'ht_unitaire',
        'quantite',
        'taxe',
        'reduction_unitaire',
        'total_ht',
        'total_reduction',
        'total_tva',
        'total_ttc',
        'mode_reduction',
        'position',
        'unit_id',
        'vente_id',
        'article_id'
    ];
    public function vente()
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }
    public function unite()
    {
        return $this->belongsTo(Unite::class ,'unit_id');
    }

    public function article(){
        return $this->belongsTo(Article::class);
    }
}
