<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'article_id',
        'magasin_id',
        'declencheur',
        'qte_sortir',
        'qte_entree',
        'valeur_sortir',
        'valeur_entrer',
        'stockable_id',
        'stockable_type',
        'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function stockable()
    {
        return $this->morphTo();
    }
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
