<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'nom',
        'couleur',
    ];


    public function ventes() {
        return $this->morphedByMany(Vente::class,'taggable')->withTimestamps();
    }

    public function achats(){
        return $this->morphedByMany(Achat::class,'taggable')->withTimestamps();
    }
}
