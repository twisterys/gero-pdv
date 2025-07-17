<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormeJuridique extends Model
{
    use HasFactory;


    protected $table = 'forme_juridique';

    protected $fillable = [
        'id',
        'nom',
        'nom_sur_facture',
        'active',
    ];


    protected $casts = [
        'active' => 'boolean',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'forme_juridique_id');
    }
}
