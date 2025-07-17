<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeTransfertLigne extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'quantite_demande',
        'quantite_livre',
        'demande_transfert_id'
    ];

    function article(){
        return $this->belongsTo(Article::class);
    }
    function demandeTransfert(){
        return $this->belongsTo(DemandeTransfert::class,'demande_transfert_id');
    }
}
