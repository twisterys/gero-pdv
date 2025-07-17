<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importation extends Model
{
    use HasFactory;

    public const DECLENCHEUR = 'Ouverture';
//
    protected $fillable = [
        'type',
        'magasin_id',
        'date_effet',
        'fichier_path',
        'statut',
        'reference'
    ];

    public function stock_transactions(){
        return $this->morphMany(TransactionStock::class,'stockable');

    }
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

}
