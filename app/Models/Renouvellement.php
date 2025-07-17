<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renouvellement extends Model
{
    use HasFactory;

    protected $fillable = [
        'abonnement_id',
        'date_renouvellement',
        'date_expiration',
        'montant',
        'note',
        'document_reference',
    ];

    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class);
    }
}
