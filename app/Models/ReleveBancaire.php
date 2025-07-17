<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleveBancaire extends Model
{
    use HasFactory;

    protected $table = 'releve_bancaires';

    protected $fillable = [
        'compte_id',
        'url',
        'year',
        'month',
    ];

    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }
}
