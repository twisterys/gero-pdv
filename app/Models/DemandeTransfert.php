<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeTransfert extends Model
{
    use HasFactory;

    public const DECLENCHEUR = 'Demande-Transfert';

    protected $fillable = [
        'magasin_entree_id', 'magasin_sortie_id', 'user_id', 'reference', 'statut'
    ];

    function user(){
        return $this->belongsTo(User::class);
    }
    function magasin_entree(){
        return $this->belongsTo(Magasin::class,'magasin_entree_id');
    }
    function magasin_sortie(){
        return $this->belongsTo(Magasin::class,'magasin_sortie_id');
    }
    function lignes(){
        return $this->hasMany(DemandeTransfertLigne::class,'demande_transfert_id');
    }
    public function stock_transaction()
    {
        return $this->morphMany(TransactionStock::class, 'stockable');
    }
}
