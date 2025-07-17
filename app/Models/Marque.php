<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marque extends Model
{
    protected $fillable = [
        'nom',
    ];

    public function articles (){
        return $this->hasMany(Article::class,'marque_id');
    }
}
