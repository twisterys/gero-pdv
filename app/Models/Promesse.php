<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promesse extends Model
{
    use HasFactory;
    protected $fillable = [
      'montant',
        'type',
        'date',
      'vente_id',
        'statut'
    ];


    public function vente(){
        return $this->belongsTo(Vente::class,'vente_id');
    }
}
