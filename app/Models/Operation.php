<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{


    protected $fillable = [
        'nom',
        'action',
        'reference'
    ];


    public function paiements()
    {
        return $this->morphMany(Paiement::class,'payable');
    }
}
