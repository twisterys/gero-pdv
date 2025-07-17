<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MethodesPaiement extends Model
{
    use HasFactory;

    protected $table = 'methodes_paiement';

    protected $fillable = [
        'key',
        'nom',
        'actif',
    ];
}
