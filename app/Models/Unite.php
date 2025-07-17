<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unite extends Model
{
    use HasFactory;
    protected $casts = [
        'defaut' => 'boolean',
    ];
    protected $fillable = ['nom', 'defaut', 'actif'];
}
