<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{

    use HasFactory;
    public const DECLENCHEUR = 'Transfert';

    protected $fillable = [
        'reference',
        'magasin_entree',
        'magasin_sortie',
        'is_controled',
        'controled_at',
        'controled_by'
    ];

    public function magasinEntree()
    {
        return $this->belongsTo(Magasin::class, 'magasin_entree');
    }

    public function magasinSortie()
    {
        return $this->belongsTo(Magasin::class, 'magasin_sortie');
    }

    public function lignes(){
        return $this->hasMany(TransfertLigne::class,'transfert_id');
    }
}
