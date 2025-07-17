<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'prefixe',
        'format_date',
        'longueur_compteur',
        'separateur',
        'emplacement_separateur',
        'format_number',
        'template',
    ];

}
