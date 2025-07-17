<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jalon extends Model
{
    use HasFactory;

    protected $fillable = [
        'affaire_id',
        'nom',
        'date',
    ];

    /**
     * Get the affaire that owns the jalon.
     */
    public function affaire()
    {
        return $this->belongsTo(Affaire::class);
    }
}
